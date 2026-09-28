<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\ApproveStaff;
use App\Models\GroupSpecial;
use App\Models\Valldataemp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TechApprovalGroupController extends Controller
{
    public function index()
    {
        $approvalRows = ApproveStaff::query()
            ->where('extype', 3)
            ->whereIn('step', [1, 2])
            ->where('status', 1)
            ->where('deleted', 0)
            ->orderBy('group')
            ->orderBy('step')
            ->get();

        $groups = $approvalRows
            ->groupBy('group')
            ->map(function ($rows, $group) {
                return [
                    'group' => (int) $group,
                    'step1' => $rows->firstWhere('step', 1),
                    'step2' => $rows->firstWhere('step', 2),
                ];
            })
            ->sortKeys()
            ->values();

        $technicians = GroupSpecial::query()
            ->where('typeid', 3)
            ->where('status', 1)
            ->where('deleted', 0)
            ->orderByRaw('CASE WHEN groupapprove IS NULL OR groupapprove = 0 THEN 0 ELSE 1 END')
            ->orderBy('fullname')
            ->get();

        $assignedCounts = $technicians
            ->filter(fn (GroupSpecial $technician) => !empty($technician->groupapprove))
            ->countBy(fn (GroupSpecial $technician) => (int) $technician->groupapprove);
        $completeGroups = $groups->filter(fn (array $group) => $group['step1'] && $group['step2'])->values();
        $unassignedCount = $technicians->filter(fn (GroupSpecial $technician) => empty($technician->groupapprove))->count();

        return view('back.techapprove.index', compact(
            'groups',
            'completeGroups',
            'technicians',
            'assignedCounts',
            'unassignedCount'
        ));
    }

    public function saveApprovers(Request $request)
    {
        $validated = $request->validate([
            'group' => [
                'nullable',
                'integer',
                'min:1',
                Rule::exists('approvestaff', 'group')->where(fn ($query) => $query
                    ->where('extype', 3)
                    ->where('status', 1)
                    ->where('deleted', 0)),
            ],
            'step1_empid' => ['required', 'string', 'max:20'],
            'step2_empid' => ['required', 'string', 'max:20', 'different:step1_empid'],
        ], [
            'step1_empid.required' => 'กรุณาเลือกผู้อนุมัติ Step 1',
            'step2_empid.required' => 'กรุณาเลือกผู้อนุมัติ Step 2',
            'step2_empid.different' => 'ผู้อนุมัติ Step 1 และ Step 2 ต้องเป็นคนละคน',
        ]);

        $employees = Valldataemp::query()
            ->whereIn('CODEMPID', [$validated['step1_empid'], $validated['step2_empid']])
            ->where('STAEMP', '!=', 9)
            ->where('status', 1)
            ->where('deleted', 0)
            ->get()
            ->keyBy(fn ($employee) => (string) $employee->CODEMPID);

        if ($employees->count() !== 2) {
            return back()->withInput()->with('error', 'ไม่พบข้อมูลผู้อนุมัติใน HRMS หรือพนักงานพ้นสภาพแล้ว');
        }

        foreach ($employees as $employee) {
            if (empty($employee->EMAIL)) {
                return back()->withInput()->with('error', 'ผู้อนุมัติที่เลือกมีข้อมูลอีเมลไม่ครบ');
            }
        }

        $group = $validated['group'] ?? null;
        if (!$group) {
            $group = ((int) ApproveStaff::where('extype', 3)->max('group')) + 1;
        }

        DB::transaction(function () use ($employees, $validated, $group) {
            ApproveStaff::query()
                ->where('extype', 3)
                ->where('group', $group)
                ->whereIn('step', [1, 2])
                ->where('deleted', 0)
                ->update([
                    'status' => 0,
                    'deleted' => 1,
                    'modified_by' => Auth::id(),
                ]);

            foreach ([1 => 'step1_empid', 2 => 'step2_empid'] as $step => $field) {
                $employee = $employees->get((string) $validated[$field]);

                ApproveStaff::create([
                    'extype' => 3,
                    'step' => $step,
                    'group' => $group,
                    'empid' => $employee->CODEMPID,
                    'email' => $employee->EMAIL,
                    'fullname' => trim($employee->NAMFIRSTT.' '.$employee->NAMLASTT),
                    'status' => 1,
                    'deleted' => 0,
                ]);
            }
        });

        logAction(
            'save',
            'ApproveStaff',
            'บันทึกสายอนุมัติช่าง กลุ่ม '.$group,
            json_encode(['group' => $group, 'extype' => 3])
        );

        return redirect()->route('TechApprove.index')->with('success', 'บันทึกสายอนุมัติช่าง กลุ่ม '.$group.' สำเร็จ');
    }

    public function assignTechnicians(Request $request)
    {
        $validated = $request->validate([
            'group' => [
                'required',
                'integer',
                Rule::exists('approvestaff', 'group')->where(fn ($query) => $query
                    ->where('extype', 3)
                    ->where('status', 1)
                    ->where('deleted', 0)),
            ],
            'technician_ids' => ['required', 'array', 'min:1'],
            'technician_ids.*' => [
                'integer',
                Rule::exists('group_specials', 'id')->where(fn ($query) => $query
                    ->where('typeid', 3)
                    ->where('status', 1)
                    ->where('deleted', 0)),
            ],
        ], [
            'group.required' => 'กรุณาเลือกกลุ่มอนุมัติ',
            'technician_ids.required' => 'กรุณาเลือกพนักงานช่างอย่างน้อย 1 คน',
        ]);

        $steps = ApproveStaff::query()
            ->where('extype', 3)
            ->where('group', $validated['group'])
            ->whereIn('step', [1, 2])
            ->where('status', 1)
            ->where('deleted', 0)
            ->pluck('step')
            ->unique();

        if (!$steps->contains(1) || !$steps->contains(2)) {
            return back()->withInput()->with('error', 'กลุ่มที่เลือกต้องมีผู้อนุมัติ Step 1 และ Step 2 ครบ');
        }

        $updated = GroupSpecial::query()
            ->whereIn('id', $validated['technician_ids'])
            ->where('typeid', 3)
            ->where('status', 1)
            ->where('deleted', 0)
            ->update([
                'groupapprove' => $validated['group'],
                'modified_by' => Auth::id(),
            ]);

        logAction(
            'update',
            'GroupSpecial',
            'กำหนดกลุ่มอนุมัติช่าง กลุ่ม '.$validated['group'].' จำนวน '.$updated.' คน',
            json_encode([
                'group' => (int) $validated['group'],
                'technician_ids' => $validated['technician_ids'],
            ])
        );

        return redirect()->route('TechApprove.index')->with('success', 'กำหนดกลุ่มให้พนักงานช่าง '.$updated.' คนสำเร็จ');
    }

    public function searchEmployees(Request $request)
    {
        $keyword = trim((string) $request->input('q', ''));
        $page = max((int) $request->input('page', 1), 1);
        $limit = 10;

        $query = Valldataemp::query()
            ->where('STAEMP', '!=', 9)
            ->where('status', 1)
            ->where('deleted', 0)
            ->whereNotNull('EMAIL')
            ->where('EMAIL', '!=', '');

        if ($keyword !== '') {
            $query->where(function ($subQuery) use ($keyword) {
                $subQuery->where('CODEMPID', 'like', '%'.$keyword.'%')
                    ->orWhere('EMAIL', 'like', '%'.$keyword.'%')
                    ->orWhere('NAMFIRSTT', 'like', '%'.$keyword.'%')
                    ->orWhere('NAMLASTT', 'like', '%'.$keyword.'%');
            });
        }

        $total = (clone $query)->count();
        $results = $query
            ->orderBy('CODEMPID')
            ->skip(($page - 1) * $limit)
            ->take($limit)
            ->get(['CODEMPID', 'EMAIL', 'NAMFIRSTT', 'NAMLASTT'])
            ->map(fn ($employee) => [
                'id' => (string) $employee->CODEMPID,
                'text' => $employee->CODEMPID.' | '.trim($employee->NAMFIRSTT.' '.$employee->NAMLASTT).' | '.$employee->EMAIL,
            ]);

        return response()->json([
            'results' => $results,
            'pagination' => ['more' => ($page * $limit) < $total],
        ]);
    }
}
