<?php

namespace App\Http\Controllers\Front;

use App\Helpers\MailHelper;
use App\Http\Controllers\Controller;
use App\Models\Approve;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HeadApprovedController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $currentEmpid = Auth::user()->empid;

        $expenses = Expense::with(['latestApprove', 'vbooking', 'user'])
            ->whereHas('latestApprove', function ($query) use ($currentEmpid) {
                $query->whereIn('typeapprove', [1, 2])
                    ->where('empid', $currentEmpid)
                    ->where('statusapprove', 0);
            })
            ->where('deleted', 0)
            ->get();
        $page =  'HeadApprove.show';

        return view('front.headapprove.list', compact('expenses', 'page'));
    }

    public function confirm(Request $request)
    {
        $request->validate([
            'approve_id' => 'required|array',
            'action' => 'required|in:approve,reject',
            'typeapprove' => 'required|array',
            'nextempid' => 'array',
            'nextemail' => 'array',
        ]);

        $empid = Auth::user()->empid;
        $now = now();
        $ids = $request->approve_id;
        $types = $request->typeapprove;
        $nextEmpIds = $request->nextempid ?? [];
        $nextEmails = $request->nextemail ?? [];
        $nextFullnames = $request->nextfullname ?? [];
        $empfullname = $request->empfullname ?? [];
        $action = $request->action;
        $approvename = Auth::user()->fullname;
        $approveemail = Auth::user()->email;
        $approveempid = Auth::user()->empid;
        $departuredate = $request->departuredate ?? [];

        DB::beginTransaction();
        try {
            foreach ($ids as $index => $approveId) {
                $approve = Approve::find($approveId);
                if (!$approve || $approve->statusapprove != 0) {
                    continue;
                }

                $approve->statusapprove = $action === 'approve' ? 1 : 2;
                // $approve->approvename = $approvename;
                // $approve->empid = $approveempid;
                // $approve->email = $approveemail;
                $approve->emailstatus = 1;
                $approve->save();

                // ถ้าอนุมัติ และเป็น type 2 สร้าง step ถัดไป type 1
                if ($action === 'approve' && isset($types[$index]) && $types[$index] == 2) {

                    $nextempid = $nextEmpIds[$index] ?? null;
                    // $nextemail = $nextEmails[$index] ?? null;
                    $nextemail = 'Kamolwan.b@bgiglass.com';
                    $nextfullname = $nextFullnames[$index] ?? null;
                    $token = Str::random(64);
                    $nextApprove = Approve::create([
                        'exid' => $approve->exid,
                        'typeapprove' => 1,
                        'empid' => $nextempid,
                        'email' => $nextemail,
                        'approvename' => $nextfullname,
                        'emailstatus' => 1,
                        'statusapprove' => 0,
                        'login_token' => $token,
                        'token_expires_at' => now()->addDays(10),
                    ]);

                    // ✅ ส่งอีเมลลิงก์อนุมัติรอบถัดไป
                    $link = route('approve.magic.login', ['token' => $token]);

                    $data = [
                        'type' => 1,
                        'title' => 'แจ้งเตือนการอนุมัติการเบิกเบี้ยเลี้ยง',
                        'name' => $nextfullname,
                        'full_name' => $empfullname[$index] ?? '-',
                        'departuredate' => $departuredate[$index] ?? '',
                        'check_hr' => $approvename,
                        'link' => $link,
                    ];

                    MailHelper::sendExternalMail(
                        $nextemail,
                        'อนุมัติการเบิกเบี้ยเลี้ยง',
                        'mails.hrheadapprove',
                        $data,
                        'Expense Claim System EX' . $approve->exid,
                    );
                }

                // ส่งเมลถ้า reject
                if ($action === 'reject') {
                }
            }

            DB::commit();
            return back()->with(['message' => 'บันทึกผลอนุมัติเรียบร้อย', 'class' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with(['message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage(), 'class' => 'danger']);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id, $type = null)
    {
        $expense = Expense::with(['foods', 'logs.booking'])->findOrFail($id);

        $driver_empid = $expense->empid;
        $driver_name = optional($expense->tech)->fullname ?? '-';

        // ดึงวันที่ที่เบิกอาหารทั้งหมด
        $Alldayfood = $expense->foods->pluck('used_date')->unique()->sort()->map(function ($date) {
            return \Carbon\Carbon::parse($date);
        });

        $groupedTimeRanges = [];
        $start = null;
        $end = null;

        foreach ($expense->foods as $food) {
            $dayKey = $food->used_date;

            // หา logs ที่เกี่ยวข้องกับ foodid นี้
            $relatedLogs = $expense->logs->where('foodid', $food->id);

            $details = [];
            $start = null;
            $end = null;

            foreach ($relatedLogs as $log) {
                $booking = $log->booking;
                if ($booking) {
                    $bookingStart = Carbon::parse($booking->departure_date . ' ' . $booking->departure_time);
                    $bookingEnd = Carbon::parse($booking->return_date . ' ' . $booking->return_time);

                    $start = is_null($start) || $bookingStart->lt($start) ? $bookingStart : $start;
                    $end = is_null($end) || $bookingEnd->gt($end) ? $bookingEnd : $end;

                    $details[] = [
                        'id' => $booking->id,
                        'location_name' => $booking->location_name,
                        'start' => $bookingStart,
                        'end' => $bookingEnd,
                    ];
                }
            }

            // fallback ถ้าไม่มี log
            $groupedTimeRanges[$dayKey] = [
                'start' => $start ?? Carbon::createFromTimeString('06:00'),
                'end' => $end ?? Carbon::createFromTimeString('23:59'),
                'details' => $details,
            ];
        }

        $approvals = Approve::where('exid', $expense->id)
            ->where('deleted', 0)
            ->where('status', 1)
            ->orderBy('id')
            ->get();

        // dd($details);

        // ราคาต่อมื้อ
        $prices = [1 => 50, 2 => 60, 3 => 60, 4 => 50];

        // คนอนุมัติปัจจุบัน
        $headempid = Auth::user()->empid ?? '';
        $heademail = Auth::user()->email ?? '';
        $headname = Auth::user()->fullname ?? '';

        // คนอนุมัติขั้นถัดไป
        $bu = BuEmp($driver_empid);
        $nextStepApprove = Approvestep($bu, 1, 1);

        // $finalHEmailNext = $nextStepApprove['email'] ?? 'Kamolwan.b@bgiglass.com';
        $finalHNameNext = $nextStepApprove['fullname'] ?? '';
        // $finalIdNext = $nextStepApprove['empid'] ?? '66000510';
        $finalHEmailNext = 'Kamolwan.b@bgiglass.com';
        // $finalHNameNext = 'กมลวรรณ บรรชา';
        $finalIdNext = '66000510';

        $startDate = $Alldayfood->first();
        $endDate = $Alldayfood->last();


        return view('front.driver.show', compact(
            'expense',
            'driver_empid',
            'driver_name',
            'Alldayfood',
            'groupedTimeRanges',
            'prices',
            'finalHEmailNext',
            'finalHNameNext',
            'finalIdNext',
            'type',
            'headempid',
            'heademail',
            'headname',
            'approvals',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
