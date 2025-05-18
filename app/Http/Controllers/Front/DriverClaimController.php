<?php

namespace App\Http\Controllers\Front;

use App\Helpers\MailHelper;
use App\Http\Controllers\Controller;
use App\Models\Approve;
use App\Models\Expense;
use App\Models\ExpenseFood;
use App\Models\ExpenseLog;
use App\Models\GroupSpecial;
use App\Models\Vbookingall;
use App\Models\Vbookmanage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DriverClaimController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $drivers = GroupSpecial::whereIn('typeid', [1, 2])->where("deleted", 0)->where("status", 1)->get();

        return view('front.driver.list', compact('drivers'));
    }

    public function history(){
        $expenses = Expense::with(['latestApprove', 'vbooking','tech'])
        ->whereHas('latestApprove', function ($query) {
            $query->whereIn('typeapprove', [1,3,4,5,6]);
            // ->where('statusapprove', 1);
        })
        ->whereIn('extype', [2])
        ->get();
        $page = 'DriverClaim.show';
        return view('back.hr.historydv', compact('expenses','page'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $bookingIds = $request->input('booking_ids', []);
        $bookings = Vbookmanage::whereIn('id', $bookingIds)->get();
        $driver_empid = $bookings[0]->driver_empid ?? "";
        $driver_name = $bookings[0]->driver_name ?? "";

        // dd($driver_empid);

        // เตรียมเวลาของแต่ละ booking แยกตามวันที่
        $groupedTimeRanges = [];

        foreach ($bookings as $booking) {
            $start = Carbon::parse($booking->departure_date . ' ' . $booking->departure_time);
            $end = Carbon::parse($booking->return_date . ' ' . $booking->return_time);

            for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                $dayKey = $date->toDateString();

                // เวลาเริ่มในวันนั้น
                $startTime = $date->isSameDay($start) ? $start->copy() : $date->copy()->setTime(6, 0);
                $endTime = $date->isSameDay($end) ? $end->copy() : $date->copy()->setTime(23, 59);

                if (!isset($groupedTimeRanges[$dayKey])) {
                    $groupedTimeRanges[$dayKey] = [
                        'start' => $startTime,
                        'end' => $endTime,
                        'details' => [[
                            'id' => $booking->id,
                            'location_name' => $booking->location_name,
                            'start' => $start,
                            'end' => $end,
                        ]],
                    ];
                } else {
                    $groupedTimeRanges[$dayKey]['start'] = $startTime->lt($groupedTimeRanges[$dayKey]['start']) ? $startTime : $groupedTimeRanges[$dayKey]['start'];
                    $groupedTimeRanges[$dayKey]['end'] = $endTime->gt($groupedTimeRanges[$dayKey]['end']) ? $endTime : $groupedTimeRanges[$dayKey]['end'];
                    $groupedTimeRanges[$dayKey]['details'][] = [
                        'id' => $booking->id,
                        'location_name' => $booking->location_name,
                    ];
                }
            }
        }

        // ✅ ดึงวันที่ที่มี booking จริง
        $Alldayfood = collect();
        if ($bookings->count()) {
            $minDate = $bookings->min('departure_date');
            $maxDate = $bookings->max('return_date');

            for ($d = Carbon::parse($minDate); $d->lte(Carbon::parse($maxDate)); $d->addDay()) {
                $Alldayfood->push($d->copy());
            }
        }

        // ✅ ราคาต่อมื้อ
        $prices = [1 => 50, 2 => 60, 3 => 60, 4 => 50];

        // คนอนุมัติ
        $bu = BuEmp($driver_empid);
        $nextStepApprove = Approvestep($bu, 2, 1, 1);
        // $finalHEmailNext = $nextStepApprove["email"];
        // $finalHNameNext = $nextStepApprove["fullname"];
        // $finalIdNext = $nextStepApprove["empid"];
        $finalHEmailNext = 'Kamolwan.b@bgiglass.com';
        $finalHNameNext = 'กมลวรรณ บรรชา';
        $finalIdNext = '66000510';

        return view('front.driver.create', compact('bookings', 'driver_empid', 'driver_name', 'Alldayfood', 'groupedTimeRanges', 'prices', 'finalHEmailNext', 'finalHNameNext', 'finalIdNext'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        DB::beginTransaction();

        try {
            $days = $request->input('days', []);
            $costoffood = $request->input('costoffood', 0);
            $groupedTimeRangesInput = $request->input('groupedTimeRanges', []);
            $empid = $request->empid;
            $groupedTimeRanges = [];

            foreach ($groupedTimeRangesInput as $dayKey => $detailGroup) {
                $groupedTimeRanges[$dayKey] = [
                    'details' => array_values($detailGroup['details']), // คืน array ต่อวันให้เป็น array ปกติ
                ];

                // ถ้าอยากคำนวณเวลา start/end ใน controller แทน
                $groupedTimeRanges[$dayKey]['start'] = Carbon::createFromTimeString('06:00');
                $groupedTimeRanges[$dayKey]['end'] = Carbon::createFromTimeString('23:59');
            }

            // หาวันแรกสำหรับอ้างอิง booking หลัก
            $firstDate = array_key_first($days);
            $firstDay = $days[$firstDate]['date'] ?? null;

            $mainBookingId = null;
            if (isset($groupedTimeRanges[$firstDay]['details']) && count($groupedTimeRanges[$firstDay]['details']) > 0) {
                $sortedMain = collect($groupedTimeRanges[$firstDay]['details'])->sortBy('id')->values();
                $mainBookingId = $sortedMain->first()['id'];
            }

            // สร้างรหัสอ้างอิง
            $todayPrefix = Carbon::now()->format('Ymd');
            $latestId = Expense::where('id', 'like', $todayPrefix . '%')->orderByDesc('id')->first();
            $nextNumber = $latestId ? ((int)substr($latestId->id, 8)) + 1 : 1;
            $newId = $todayPrefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            // dd($groupedTimeRanges);
            // insert ตารางหลัก
            $expense = new Expense();
            $expense->id = $newId;
            $expense->prefix = "EX";
            $expense->bookid = $mainBookingId;
            $expense->costoffood = $costoffood;
            $expense->extype = 2;
            $expense->empid = $empid;
            $expense->departurefrom = 1;
            $expense->departureplant = 2;
            $expense->returnfrom = 1;
            $expense->returnplant = 2;
            $expense->returnfromtext = 2;
            $expense->returntime = '00:00:00';
            $expense->totaldistance = 0;
            $expense->travelexpenses = 0;
            $expense->gasolinecost = 0;
            $expense->totalprice = $costoffood;
            $expense->save();

            // loop วัน
            foreach ($days as $index => $day) {
                $usedDate = $day['date'];

                $meal1 = isset($day['meal1']) ? array_sum($day['meal1']) : 0;
                $meal2 = isset($day['meal2']) ? array_sum($day['meal2']) : 0;
                $meal3 = isset($day['meal3']) ? array_sum($day['meal3']) : 0;
                $meal4 = isset($day['meal4']) ? array_sum($day['meal4']) : 0;

                $total = $meal1 + $meal2 + $meal3 + $meal4;

                //ต้องเรียงใหม่ก่อนใช้
                $bookingsThatDay = $groupedTimeRanges[$usedDate]['details'] ?? [];

                $sortedBookingIds = collect($bookingsThatDay)->sortBy('id')->values();
                $firstBookingId = $sortedBookingIds->first()['id'] ?? $mainBookingId;

                $otherIds = $sortedBookingIds->pluck('id')->slice(1)->all();
                $remark = count($otherIds) > 0
                    ? 'ร่วมกับ booking ID: ' . implode(', ', $otherIds)
                    : null;


                //บันทึก expense_food
                $expenseFood = ExpenseFood::create([
                    'exid' => $expense->id,
                    'mealid' => 2,
                    'meal1' => $meal1,
                    'meal2' => $meal2,
                    'meal3' => $meal3,
                    'meal4' => $meal4,
                    'used_date' => $usedDate,
                    'bookid' => $firstBookingId,
                    'remark' => $remark,
                    'totalpricebf' => $total,
                    'totalprice' => $total,
                ]);
                //บันทึก log
                foreach ($sortedBookingIds as $booking) {
                    ExpenseLog::create([
                        'exid' => $expense->id,
                        'bookid' => $booking['id'],
                        'foodid' => $expenseFood->id,
                        'empid' => $empid,
                        'type' => 2,
                        'remark' => 'เบิกค่าอาหารวันที่ ' . $usedDate,
                    ]);
                }
            }

            $token = Str::random(64);
             Approve::create([
                'exid' => $expense->id,
                'typeapprove' => 1, //ต้นสังกัด พขร อนุมัติ
                'empid' => $request->head_id,
                'email' => $request->head_email ?? '',
                'approvename' => $request->head_name ?? '',
                'emailstatus' => 1,
                'statusapprove' => 0,
                'login_token' => $token,
                'token_expires_at' => now()->addDays(10),
            ]);


            DB::commit();
             // Sent Mail
             $allDates = collect($days)->pluck('date')->sort()->values();
             $startDate = $allDates->first();
             $endDate = $allDates->last();

             $startDateFormatted = Carbon::parse($startDate)->format('d/m/Y');
             $endDateFormatted = Carbon::parse($endDate)->format('d/m/Y');


             $link = route('approve.magic.login', ['token' => $token]);
             $data = [
                 'type' => 2,
                 'title' => 'แจ้งเตือนการอนุมัติการเบิกเบี้ยเลี้ยง',
                 'name' => $request->head_name,
                 'full_name' => $request->driver_name,
                 'admintext' => 'Admin สำหรับเบิกเบี้ยเลี้ยงพนักงานขับรถบริษัท',
                 'departuredate' => $startDateFormatted . ' - ' . $endDateFormatted,
                 'link' => $link,
             ];

             MailHelper::sendExternalMail(
                 $request->head_email,
                 'อนุมัติการเบิกเบี้ยเลี้ยง',
                 'mails.diverapprove', // ชื่อ blade view
                 $data,
                 'Expense Claim System EX' . $expense->id,
             );
             //End Sent Mail

            return redirect()->route('DriverClaim.index')->with('success', 'บันทึกข้อมูลเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('DriverClaim.index')->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }


    /**
     * Display the specified resource.
     */
    public function show($id,$type = null)
    {
        $expense = Expense::with(['foods', 'logs', 'bookings'])->findOrFail($id);

        $driver_empid = $expense->empid;
        $driver_name = optional($expense->employee)->fullname ?? '-'; // สมมติว่ามี relation employee

        // เตรียมข้อมูลวันที่ที่เบิก
        $Alldayfood = $expense->foods->pluck('used_date')->unique()->sort()->map(function ($date) {
            return \Carbon\Carbon::parse($date);
        });

        $groupedTimeRanges = [];

        foreach ($expense->foods as $food) {
            $dayKey = $food->used_date;

            if (!isset($groupedTimeRanges[$dayKey])) {
                $groupedTimeRanges[$dayKey] = [
                    'start' => Carbon::createFromTimeString('06:00'),
                    'end' => Carbon::createFromTimeString('23:59'),
                    'details' => [],
                ];
            }

            $groupedTimeRanges[$dayKey]['details'][] = [
                'id' => $food->bookid,
                'location_name' => optional($food->booking)->location_name ?? '-',
            ];
        }

        // ราคา
        $prices = [1 => 50, 2 => 60, 3 => 60, 4 => 50];

        // คนอนุมัติ
        $finalHEmailNext = 'Kamolwan.b@bgiglass.com';
        $finalHNameNext = 'กมลวรรณ บรรชา';
        $finalIdNext = '66000510';

        return view('front.driver.show', compact(
            'expense',
            'driver_empid',
            'driver_name',
            'Alldayfood',
            'groupedTimeRanges',
            'prices',
            'finalHEmailNext',
            'finalHNameNext',
            'finalIdNext'
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

    public function searchBooking(Request $request)
    {
        $empid = $request->empid;

        $usedBookIds = ExpenseLog::where('type', 2)->pluck('bookid');

        $bookings = Vbookmanage::where('driver_empid', $empid)
            ->whereNotIn('id', $usedBookIds)
            ->orderByDesc('departure_date')
            ->get();

        return view('front.driver.booking-list', compact('bookings'));
    }
}
