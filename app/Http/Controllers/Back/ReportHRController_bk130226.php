<?php

namespace App\Http\Controllers\Back;

use App\Exports\BookingUnclaimedExport;
use App\Exports\ReportHRExport;
use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\GroupSpecial;
use App\Models\Plant;
use App\Models\User;
use App\Models\Vbookingall;
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
                // 'vbookingreport:id,bu,title,car_status,passengers,person_type,driver_name,departure_time,return_time,location_name,locationid,locationbu',
                'vbookingreport:id,passenger_empid,booked_by,person_type,passengers,title,car_status,driver_name,departure_time,return_time,location_name,locationid,locationbu,bu',
                'logs:id,exid,remark,bookid',
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
                // extype == 2 ใช้ vbookingdrv, อื่นๆ ใช้ vbooking
                $booking = $e->extype == 2 ? $e->vbookingdrv : $e->vbooking;

                $startDate = optional($booking)->departure_date;
                $endDate = null;

                if ($e->extype == 2) {
                    $logs = $e->logs ?? collect();

                    // ถ้า logs มี bookid และอยากให้ตรง booking จริง ๆ ให้ใช้บรรทัดนี้
                    // $logs = $logs->where('bookid', optional($booking)->id);

                    $lastLog = $logs->sortByDesc('id')->first();

                    if ($lastLog && preg_match('/\d{4}-\d{2}-\d{2}/', $lastLog->remark ?? '', $m)) {
                        $endDate = $m[0];
                    } else {
                        $endDate = optional($booking)->return_date; // fallback
                    }
                } else {
                    $endDate = optional($booking)->return_date;
                }

                $days = ($startDate && $endDate)
                    ? Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate), true) + 1
                    : 0;

                // $carTitle  = optional($e->vbookingreport)->title;
                // $carStatus = optional($e->vbookingreport)->car_status;
                $reports = $e->vbookingreport ?? collect();

                // ✅ เลือก record ให้ตรง empid ของ expense
                // 1) ถ้า empid เป็น passenger ให้จับ passenger_empid ก่อน
                // 2) ถ้าไม่เจอ ค่อยจับ booked_by (กรณีคนขอ)
                // 3) ถ้ายังไม่เจอ ใช้อันแรกกันพัง
                $reportRow =
                    $reports->firstWhere('passenger_empid', (string)$e->empid)
                    ?? $reports->firstWhere('booked_by', (string)$e->empid)
                    ?? $reports->first();

                $carTitle  = optional($reportRow)->title;
                $carStatus = optional($reportRow)->car_status;

                $passengers  = optional($reportRow)->passengers ?? 0;
                $personType  = optional($reportRow)->person_type ?? '-';

                $driver = optional($reportRow)->driver_name ?? $fullname;

                $from = ($e->departurefrom == 2)
                    ? ($e->map_a_name ?? '-')
                    : (optional($reportRow)->bu ?? '-');

                $to = optional($reportRow)->display_location
                    ?? (optional($reportRow)->location_name ?? '-');


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

                    'cartype'     => $carTitle ? $carTitle . (filled($carStatus) ? " ({$carStatus})" : '') : '-',
                    'passengers'  => $passengers,
                    'person_type' => $personType,
                    'driver'      => $driver,
                    'from'        => $from,
                    'to'          => $to,

                    'departuretime' => $e->departuretime ?? ($depTime ?? '-'),
                    'returntime'    => $e->returntime ?? ($retTime ?? '-'),

                    'distance1'      => $e->totaldistance ?? 0,
                    'distance2'      => $e->distancemore ?? 0,
                    'distance_total' => number_format((float) ($totle_dis ?? 0), 2),

                    // ($e->totaldistance ?? 0) + ($e->distancemore ?? 0),

                    'exid'           => 'EX' . $e->id,

                    //  วันตาม booking ที่ถูกกับ extype
                    // 'startdate'      => optional($booking)->departure_date ?? '-',
                    // 'enddate'        => optional($booking)->return_date ?? '-',
                    'startdate' => $startDate ? Carbon::parse($startDate)->format('Y-m-d') : '-',
                    'enddate'   => $endDate ? Carbon::parse($endDate)->format('Y-m-d') : '-',
                    'days'      => $days ?: '-',

                    //  เวลาออก/ถึง
                    // 'departuretime'  => $e->departuretime ?? (optional($e->vbookingreport)->departure_time ?? '-'),
                    // 'returntime'     => $e->returntime ?? (optional($e->vbookingreport)->return_time ?? '-'),

                    // 'days'           => '',

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

    /**
     * สร้างช่วงวันที่สำหรับ query:
     * - default start = วันนี้ - 7 วัน
     * - default end   = วันนี้
     * - clamp start ไม่ให้เกิน 30 วันย้อนหลัง
     */
    private function resolveDateRange(Request $request): array
    {
        $today = Carbon::today();

        // ✅ end = วันนี้ - 7 (เส้นตาย) ถ้า user ไม่ส่งมา
        $end = $request->filled('end_exdate')
            ? Carbon::parse($request->input('end_exdate'))
            : $today->copy()->subDays(7);

        // ✅ start = end - 30 ถ้า user ไม่ส่งมา
        $start = $request->filled('exdate')
            ? Carbon::parse($request->input('exdate'))
            : $end->copy()->subDays(30);

        // ✅ clamp: start ต้องไม่เก่ากว่า end-30
        $min = $end->copy()->subDays(30);
        if ($start->lt($min)) {
            $start = $min;
        }

        // กัน user ใส่ end < start
        if ($end->lt($start)) {
            $end = $start->copy();
        }

        return [$start->startOfDay(), $end->endOfDay()];
    }


    /**
     * ดึง “รายการที่ยังเบิกไม่ครบ” (1 แถวต่อ 1 คนที่ยังไม่เบิก)
     * สำคัญ: ห้าม join เพราะคนละ MySQL server
     */
    // private function buildUnclaimedRows(Request $request)
    // {
    //     [$startDate, $endDate] = $this->resolveDateRange($request);

    //     // 1) booking ช่วงวันที่ (เลือก field ที่คุณใช้จริง)
    //     // จากรูปคุณมี return_date (datetime)
    //     $bookings = Vbookingall::query()
    //         ->whereBetween('return_date', [$startDate, $endDate])
    //         ->select([
    //             'id',
    //             'passenger_empid',
    //             'booked_by',
    //             'booking_name',
    //             'booking_department',
    //             'return_date',
    //             // ถ้ามี field อื่น เช่น booking_emp_id / booking_emp_name ก็เติมได้
    //         ])
    //         ->orderByDesc('return_date')
    //         ->get();

    //     if ($bookings->isEmpty()) {
    //         return collect();
    //     }

    //     // 2) สร้างคู่ (bookid, empid) ที่ “ควรมีการเบิก”
    //     $pairs = collect();
    //     foreach ($bookings as $b) {
    //         if (!empty($b->passenger_empid)) {
    //             $pairs->push(['bookid' => $b->id, 'empid' => (string)$b->passenger_empid, 'role' => 'passenger']);
    //         }
    //         if (!empty($b->booked_by)) {
    //             $pairs->push(['bookid' => $b->id, 'empid' => (string)$b->booked_by, 'role' => 'booked_by']);
    //         }
    //     }
    //     $pairs = $pairs->unique(fn($x) => $x['bookid'] . '|' . $x['empid'])->values();

    //     // 3) ดึง expense เฉพาะที่เกี่ยวข้อง (เร็วกว่า OR เยอะ ๆ)
    //     $bookIds = $bookings->pluck('id')->unique()->values();
    //     $empIds  = $pairs->pluck('empid')->unique()->values();

    //     $claimedSet = Expense::query()
    //         ->select(['bookid', 'empid'])
    //         ->whereIn('bookid', $bookIds)
    //         ->whereIn('empid', $empIds)
    //         ->get()
    //         ->map(fn($e) => $e->bookid . '|' . $e->empid)
    //         ->flip(); // set

    //     // 4) ปั้นผลลัพธ์เป็น "แถว" ต่อคนที่ยังไม่เบิก
    //     //    เงื่อนไข: ถ้า passenger + booked_by เบิกครบแล้ว => ไม่มีแถว
    //     $rows = collect();

    //     foreach ($bookings as $b) {
    //         $needPassenger = !empty($b->passenger_empid);
    //         $needBookedBy  = !empty($b->booked_by);

    //         $passClaimed = $needPassenger
    //             ? $claimedSet->has($b->id . '|' . $b->passenger_empid)
    //             : true;

    //         $bookClaimed = $needBookedBy
    //             ? $claimedSet->has($b->id . '|' . $b->booked_by)
    //             : true;

    //         // ถ้าเบิกครบทั้งคู่ → ข้าม
    //         if ($passClaimed && $bookClaimed) {
    //             continue;
    //         }

    //         // 🔥 กรณี empid เดียวกัน (passenger = booked_by)
    //         if ($needPassenger && $needBookedBy && $b->passenger_empid == $b->booked_by) {

    //             // ถ้ายังไม่เบิก → แสดงแค่ booked_by
    //             if (!$bookClaimed) {
    //                 $rows->push([
    //                     'booking_id'    => $b->id,
    //                     'datetime_book' => Carbon::parse($b->return_date)->format('Y-m-d H:i:s'),
    //                     'empid'         => (string)$b->booked_by,
    //                     'role'          => 'booked_by',
    //                     'fullname' => !empty($b->booked_by) ? (FullnameEmp($b->booked_by) ?? '-') : '-',
    //                     'booking_name'  => $b->booking_name,
    //                     'booking_dept'  => $b->booking_department,
    //                 ]);
    //             }

    //             // สำคัญ: ข้าม passenger ไปเลย
    //             continue;
    //         }

    //         // --------- กรณีปกติ (empid ไม่ซ้ำ) ---------

    //         if ($needPassenger && !$passClaimed) {
    //             $rows->push([
    //                 'booking_id'    => $b->id,
    //                 'datetime_book' => Carbon::parse($b->return_date)->format('Y-m-d H:i:s'),
    //                 'empid'         => (string)$b->passenger_empid,
    //                 'role'          => 'passenger',
    //                 'fullname' => !empty($b->passenger_empid) ? (FullnameEmp($b->passenger_empid) ?? '-') : '-',
    //                 'booking_name'  => $b->booking_name,
    //                 'booking_dept'  => $b->booking_department,
    //             ]);
    //         }

    //         if ($needBookedBy && !$bookClaimed) {
    //             $rows->push([
    //                 'booking_id'    => $b->id,
    //                 'datetime_book' => Carbon::parse($b->return_date)->format('Y-m-d H:i:s'),
    //                 'empid'         => (string)$b->booked_by,
    //                 'role'          => 'booked_by',
    //                 'fullname'      => '-',
    //                 'booking_name'  => $b->booking_name,
    //                 'booking_dept'  => $b->booking_department,
    //             ]);
    //         }
    //     }


    //     // filter เพิ่มตาม status (ถ้าคุณมี statusList ที่หน้า)
    //     // ตัวอย่าง: status=passenger หรือ booked_by
    //     if ($request->filled('status')) {
    //         $status = $request->input('status');
    //         $rows = $rows->filter(fn($r) => $r['role'] === $status)->values();
    //     }

    //     return $rows->values();
    // }

    private function buildUnclaimedRows(Request $request)
    {
        [$startDate, $endDate] = $this->resolveDateRange($request);

        $bookings = Vbookingall::query()
            ->whereBetween('return_date', [$startDate, $endDate])
            ->select([
                'id',
                'passenger_empid',
                'booked_by',
                'booking_name',
                'booking_department',
                'return_date',
            ])
            ->orderByDesc('return_date')
            ->get();

        if ($bookings->isEmpty()) {
            return collect();
        }

        // 2) สร้างคู่ (bookid, empid)
        $pairs = collect();
        foreach ($bookings as $b) {
            if (!empty($b->passenger_empid)) {
                $pairs->push(['bookid' => $b->id, 'empid' => (string)$b->passenger_empid, 'role' => 'passenger']);
            }
            if (!empty($b->booked_by)) {
                $pairs->push(['bookid' => $b->id, 'empid' => (string)$b->booked_by, 'role' => 'booked_by']);
            }
        }
        $pairs = $pairs->unique(fn($x) => $x['bookid'] . '|' . $x['empid'])->values();

        $bookIds = $bookings->pluck('id')->unique()->values();
        $empIds  = $pairs->pluck('empid')->unique()->values();

        // 3) ดึง expense ที่เกี่ยวข้อง + เอาชื่อเต็มจาก user/tech ตาม extype
        $expenses = Expense::with(['user', 'tech'])
            ->select(['id', 'bookid', 'empid', 'extype']) // เลือกเท่าที่ใช้
            ->whereIn('bookid', $bookIds)
            ->whereIn('empid', $empIds)
            ->get();

        // set: ใช้เช็คว่าเบิกแล้วไหม
        $claimedSet = $expenses
            ->map(fn($e) => $e->bookid . '|' . $e->empid)
            ->flip();

        $userNames = User::query()
            ->whereIn('empid', $empIds)
            ->get(['empid', 'fullname'])
            ->mapWithKeys(fn($u) => [(string)$u->empid => $u->fullname]);

        $techNames = GroupSpecial::query()
            ->whereIn('empid', $empIds)
            ->get(['empid', 'fullname'])
            ->mapWithKeys(fn($t) => [(string)$t->empid => $t->fullname]);

        // รวม map (user มาก่อน tech)
        $fullnameMap = $userNames->union($techNames);
        // dd($fullnameMap->take(5));

        // 4) สร้าง rows
        $rows = collect();

        foreach ($bookings as $b) {
            $needPassenger = !empty($b->passenger_empid);
            $needBookedBy  = !empty($b->booked_by);

            $passKey = $b->id . '|' . (string)$b->passenger_empid;
            $bookKey = $b->id . '|' . (string)$b->booked_by;

            $passClaimed = $needPassenger ? $claimedSet->has($passKey) : true;
            $bookClaimed = $needBookedBy  ? $claimedSet->has($bookKey) : true;

            if ($passClaimed && $bookClaimed) {
                continue;
            }

            // passenger == booked_by => แสดงแค่ booked_by
            if ($needPassenger && $needBookedBy && $b->passenger_empid == $b->booked_by) {
                if (!$bookClaimed) {
                    $rows->push([
                        'booking_id'    => $b->id,
                        'datetime_book' => Carbon::parse($b->return_date)->format('Y-m-d H:i:s'),
                        'empid'         => (string)$b->booked_by,
                        'role'          => 'booked_by',
                        'fullname' => $fullnameMap->get((string)$b->booked_by, 'ยังไม่ได้ลงทะเบียนในระบบ'),

                        'booking_name'  => $b->booking_name,
                        'booking_dept'  => $b->booking_department,
                    ]);
                }
                continue;
            }

            if ($needPassenger && !$passClaimed) {
                $rows->push([
                    'booking_id'    => $b->id,
                    'datetime_book' => Carbon::parse($b->return_date)->format('Y-m-d H:i:s'),
                    'empid'         => (string)$b->passenger_empid,
                    'role'          => 'passenger',
                    'fullname' => $fullnameMap->get((string)$b->passenger_empid, 'ยังไม่ได้ลงทะเบียนในระบบ'),

                    'booking_name'  => $b->booking_name,
                    'booking_dept'  => $b->booking_department,
                ]);
            }

            if ($needBookedBy && !$bookClaimed) {
                $rows->push([
                    'booking_id'    => $b->id,
                    'datetime_book' => Carbon::parse($b->return_date)->format('Y-m-d H:i:s'),
                    'empid'         => (string)$b->booked_by,
                    'role'          => 'booked_by',
                    'fullname' => $fullnameMap->get((string)$b->booked_by, 'ยังไม่ได้ลงทะเบียนในระบบ'),
                    'booking_name'  => $b->booking_name,
                    'booking_dept'  => $b->booking_department,
                ]);
            }
        }

        if ($request->filled('status')) {
            $status = $request->input('status');
            $rows = $rows->filter(fn($r) => $r['role'] === $status)->values();
        }

        return $rows->values();
    }


    public function reportover(Request $request)
    {
        // statusList ตัวอย่าง (ปรับได้)
        $statusList = [
            'passenger' => 'Passenger ยังไม่เบิก',
            'booked_by' => 'Booked by ยังไม่เบิก',
        ];

        return view('back.hr.report.reporthrover', compact('statusList'));
    }

    /**
     * DataTables serverSide (จาก Collection)
     */
    public function overdata(Request $request)
    {
        $draw   = (int) $request->input('draw', 1);
        $start  = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);

        $rows = $this->buildUnclaimedRows($request);

        $recordsTotal = $rows->count();
        $recordsFiltered = $recordsTotal;

        // paginate แบบ collection
        $page = $rows->slice($start, $length)->values();

        // format ให้ตรง columns
        $data = [];
        foreach ($page as $i => $r) {
            $data[] = [
                'DT_RowIndex'    => $start + $i + 1,
                'booking_id'     => $r['booking_id'],
                'datetime_book'  => $r['datetime_book'],
                'empid'          => $r['empid'],
                'fullname'       => $r['fullname'],
                'role'           => $r['role'],
            ];
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    public function overexport(Request $request)
    {
        $rows = $this->buildUnclaimedRows($request);

        $fileName = 'HR_Unclaimed_Booking_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(
            new BookingUnclaimedExport($rows, $request->all()),
            $fileName
        );
    }
}
