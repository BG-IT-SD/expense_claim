<?php

namespace App\Http\Controllers\Back;

use App\Exports\ReportHRExport;
use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Plant;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;


class ReportHRController extends Controller
{
    public function index(Request $request)
    {
        $plants = Plant::where('status', 1)->where('deleted', 0)->get();
        $statusList = searchStatus();

        return view('back.hr.report.index', compact('plants', 'statusList'));
    }

    public function data(Request $request)
    {
        // กันเคสยังไม่เลือกวัน (DataTables จะได้ไม่ 500)
        if (!$request->filled('exdate') || !$request->filled('end_exdate')) {
            return response()->json([
                'draw' => (int) $request->draw,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
            ]);
        }

        $start = Carbon::parse($request->exdate)->startOfDay();
        $end   = Carbon::parse($request->end_exdate)->endOfDay();

        $query = Expense::query()
            ->select([
                'id',
                'extype',
                'empid',
                'bookid',
                'created_at',
                'departuretime',
                'returntime',
                'totaldistance',
                'distancemore',
                'costoffood',
                'gasolinecost',
                'expresswaytoll',
                'publictransportfare',
                'otherexpenses',
                'totalprice',
                'departurefrom',
                'map_a_name',
                'exgroup'
            ])
            ->with([
                // ใช้ approves ชุดเดียวหา latest + สายอนุมัติ
                'approves:id,exid,typeapprove,statusapprove,approvename,remark',

                'user:empid,fullname,bu',
                'tech:empid,fullname,bu',
                'userhr:CODEMPID,DEPT,JOBGRADE_TITLE,NUMBANK',
                'vbooking:id,departure_date,return_date',
                'vbookingdrv:id,departure_date,return_date',
                'vbookingreport:id,bu,title,car_status,passengers,person_type,driver_name,departure_time,return_time,location_name,locationid,locationbu',
                'exgroupData:id,paymentdate'
            ])
            ->whereIn('extype', [1, 2, 3])
            ->where('deleted', 0)
            ->where('status', 1)
            ->whereBetween('created_at', [$start, $end]);

        // Filter BU (จากฟอร์ม)
        if ($request->filled('bu')) {
            $bu = $request->bu;
            $query->where(function ($q) use ($bu) {
                $q->whereHas('user', fn($qq) => $qq->where('bu', $bu))
                    ->orWhereHas('tech', fn($qq) => $qq->where('bu', $bu));
            });
        }

        if ($request->filled('status')) {
            $status = (int) $request->status;

            $query->whereHas('approves', function ($q) use ($status) {
                $q->where('statusapprove', $status)
                    ->whereRaw('approve.id = (
                    SELECT MAX(a2.id)
                    FROM approve a2
                    WHERE a2.exid = approve.exid
                      AND a2.deleted = 0
                )');
            });
        }

        $total = $query->count();

        $items = $query
            ->orderByDesc('id')
            ->skip((int) $request->start)
            ->take((int) $request->length)
            ->get();

        // คำนวณ latest approve + สายอนุมัติ
        $items->each(function ($expense) {
            $approves = $expense->approves ?? collect();

            // latest = record ล่าสุด (ตาม id มากสุด)
            $latest = $approves->sortByDesc('id')->first();

            $expense->latestApproveObj = $latest;

            $line = resolveApproveLineFromApprove($approves, (int) $expense->extype);
            $expense->approve_cur  = $line['approve_cur'] ?? '-';
            $expense->approve_next = $line['approve_next'] ?? '-';
        });

        return response()->json([
            'draw' => (int) $request->draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $items->map(function ($e) {

                $fullname = in_array($e->extype, [2, 3])
                    ? (optional($e->tech)->fullname ?? '-')
                    : (optional($e->user)->fullname ?? '-');

                // extype == 2 ใช้ vbookingdrv, อื่นๆ ใช้ vbooking
                $booking = $e->extype == 2 ? $e->vbookingdrv : $e->vbooking;

                $carTitle  = optional($e->vbookingreport)->title;
                $carStatus = optional($e->vbookingreport)->car_status;

                // latest approve จาก approves (ไม่ใช้ latestApprove relation)
                $latest = $e->latestApproveObj ?? null;

                $type   = optional($latest)->typeapprove;
                $status = optional($latest)->statusapprove;

                $totle_dis = ($e->totaldistance ?? 0) + ($e->distancemore ?? 0);


                return [
                    'company'        => in_array($e->extype, [2, 3]) ? (optional($e->tech)->bu ?? '-') : (optional($e->user)->bu ?? '-'),
                    'empid'          => $e->empid ?? '-',
                    'fullname'       => $fullname,
                    'dept'           => optional($e->userhr)->DEPT ?? '-',
                    'grade'          => optional($e->userhr)->JOBGRADE_TITLE ?? '-',
                    'bank'           => optional($e->userhr)->NUMBANK ?? '-',

                    'bookid'         => $e->bookid ?? '-',

                    //  ประเภทรถ + ใส่วงเล็บปิด
                    'cartype'        => $carTitle
                        ? $carTitle . (filled($carStatus) ? ' (' . $carStatus . ')' : '')
                        : '-',

                    'passengers'     => optional($e->vbookingreport)->passengers ?? 0,
                    'person_type'    => optional($e->vbookingreport)->person_type ?? '-',

                    //  ถ้ามี driver_name ใช้ driver_name ถ้าไม่มีใช้ fullname
                    'driver'         => optional($e->vbookingreport)->driver_name ?? $fullname,

                    //  departurefrom == 2 ใช้ map_a_name ไม่งั้นใช้ bu จาก report
                    'from'           => ($e->departurefrom == 2)
                        ? ($e->map_a_name ?? '-')
                        : (optional($e->vbookingreport)->bu ?? '-'),

                    'to' => optional($e->vbookingreport)->display_location
                        ?? (optional($e->vbookingreport)->location_name ?? '-'),

                    'distance1'      => $e->totaldistance ?? 0,
                    'distance2'      => $e->distancemore ?? 0,
                    'distance_total' => number_format((float) ($totle_dis ?? 0), 2),

                    // ($e->totaldistance ?? 0) + ($e->distancemore ?? 0),

                    'exid'           => 'EX' . $e->id,

                    //  วันตาม booking ที่ถูกกับ extype
                    'startdate'      => optional($booking)->departure_date ?? '-',
                    'enddate'        => optional($booking)->return_date ?? '-',

                    //  เวลาออก/ถึง
                    'departuretime'  => $e->departuretime ?? (optional($e->vbookingreport)->departure_time ?? '-'),
                    'returntime'     => $e->returntime ?? (optional($e->vbookingreport)->return_time ?? '-'),

                    'days'           => '',

                    'food'    => number_format((float) ($e->costoffood ?? 0), 2),
                    'gas'     => number_format((float) ($e->gasolinecost ?? 0), 2),
                    'express' => number_format((float) ($e->expresswaytoll ?? 0), 2),
                    'public'  => number_format((float) ($e->publictransportfare ?? 0), 2),
                    'other'   => number_format((float) ($e->otherexpenses ?? 0), 2),
                    'total' => number_format(round((float) ($e->totalprice ?? 0)), 2),




                    //  ใช้ latest จาก approves
                    'approve_type'        => $type ?? '-',
                    'approve_status'      => $status ?? '-',

                    //  เพิ่ม text-only (ใช้ไปโชว์ในตาราง/Excel ได้เลย)
                    'approve_type_text'   => $type !== null ? hr_type_approve_text_only($type, $status) : '-',
                    'approve_status_text' => $status !== null ? hr_status_approve_text_only($status, $type) : '-',

                    'approve_cur'    => $e->approve_cur ?? '-',
                    'approve_next'   => $e->approve_next ?? '-',

                    'remark'         => optional($latest)->remark ?? '',
                    'paymentdate'    => optional($e->exgroupData)->paymentdate ?? '-',
                ];
            })->values(),
        ]);
    }

    public function export(Request $request)
    {
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '1024M');

        // กันเคสยังไม่เลือกวัน
        if (!$request->filled('exdate') || !$request->filled('end_exdate')) {
            return back()->with('error', 'กรุณาเลือกช่วงวันที่ก่อน Export');
        }

        $filename = 'HR_Report_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(
            new ReportHRExport($request),
            $filename
        );
    }

    public function reportover(Request $request){

        $plants = Plant::where('status', 1)->where('deleted', 0)->get();
        $statusList = searchStatus();

        return view('back.hr.report.index_sevenday',compact('plants','statusList'));
    }
}
