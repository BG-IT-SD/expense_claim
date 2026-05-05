<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\GroupSpecial;
use App\Models\Plant;
use App\Models\Valldataemp;
use App\Models\Vbooking;
use Illuminate\Http\Request;
use App\Models\ApproveGroup;
use App\Models\DistanceRate;
use App\Models\Groupplant;
use App\Models\Heademp;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Mail;
use App\Mail\ApproveNotification;
use App\Mail\TestMail;
use App\Models\Approve;
use App\Models\Expense;
use App\Models\ExpenseFile;
use App\Models\ExpenseFood;
use App\Models\Fuelprice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use App\Helpers\MailHelper;
use App\Models\Approvespecial;
use App\Models\FuelPrice91;
use App\Models\MessageAlert;
use App\Models\Objectdata;
use App\Models\Vbookingall;

class TechClaimController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $groupEmpIds = GroupSpecial::where('typeid', 3)->pluck('fullname', 'empid')->toArray();

        $exid = $request->filled('exid')
            ? ltrim($request->exid, 'EX')
            : null;

        $bookings = Vbooking::where(function ($q) use ($groupEmpIds) {
            $q->whereIn('booking_emp_id', array_keys($groupEmpIds))
                ->orWhereIn('passenger_empid', array_keys($groupEmpIds));
        })
            ->when(
                $request->filled('bookid'),
                fn($q) =>
                $q->where('id', $request->bookid)
            )
            ->when(
                $request->filled('exdate'),
                fn($q) =>
                $q->whereDate('departure_date', '>=', $request->exdate)
            )
            ->when(
                $request->filled('end_exdate'),
                fn($q) =>
                $q->whereDate('departure_date', '<=', $request->end_exdate)
            )
            ->get();

        // ผูก expense ทีละรายการด้วย passenger_empid
        foreach ($bookings as $booking) {
            $booking->expense = Expense::where('bookid', $booking->id)
                ->where('empid', $booking->passenger_empid)
                ->with(['latestApprove', 'user'])
                ->orderBy('id', 'desc')
                ->first();
        }

        if ($exid) {
            $bookings = $bookings->filter(function ($b) use ($exid) {
                return optional($b->expense)->id == $exid;
            })->values(); // reset index
        }

        return view('front.techclaim.list', compact('bookings', 'groupEmpIds'));
    }

    public function history(Request $request)
    {

        $startDate = $request->filled('exdate')
            ? $request->exdate
            : now()->startOfMonth()->toDateString();

        $endDate = $request->filled('end_exdate')
            ? $request->end_exdate
            : now()->endOfMonth()->toDateString();

        $exid = $request->filled('exid')
            ? ltrim($request->exid, 'EX')
            : null;

        $expenses = Expense::with(['latestApprove', 'vbooking', 'tech'])
            ->when(
                $request->filled('exid'),
                fn($q) => $q->where('id', $exid)
            )
            ->when(
                $request->filled('bookid'),
                fn($q) => $q->where('bookid', $request->bookid)
            )
            ->where('extype', 3)
            ->whereHas('latestApprove', function ($query) {
                $query->whereIn('typeapprove', [1, 2, 3, 4, 5, 6]);
            })
            ->get();


        $expenses = $expenses->filter(function ($exp) use ($startDate, $endDate) {
            $departure = optional($exp->vbooking)->departure_date;

            return $departure &&
                $departure >= $startDate &&
                $departure <= $endDate;
        })->values();

        return view('front.techclaim.history', compact('expenses'));
    }

    // public function history(Request $request)
    // {

    // $startDate = $request->filled('exdate')
    //     ? $request->exdate
    //     : now()->startOfMonth()->toDateString(); // วันแรกของเดือนนี้

    // $endDate = $request->filled('end_exdate')
    //     ? $request->end_exdate
    //     : now()->endOfMonth()->toDateString(); // วันสุดท้ายของเดือนนี้


    //     $exid = $request->filled('exid')
    //         ? ltrim($request->exid, 'EX')
    //         : null;

    //     $expenses = Expense::with(['latestApprove', 'vbooking', 'tech'])
    //         ->when(
    //             $request->filled('exid'),
    //             fn($q) =>
    //             $q->where('id', $exid)
    //         )
    //         ->when(
    //             $request->filled('bookid'),
    //             fn($q) =>
    //             $q->where('bookid', $request->bookid)
    //         )
    //         ->where('extype', 3)
    //         ->whereHas('latestApprove', function ($query) {
    //             $query->whereIn('typeapprove', [1, 2, 3, 4, 5, 6]);
    //         })
    //         ->get();

    //     if ($request->filled('exdate') && $request->filled('end_exdate')) {
    //         $expenses = $expenses->filter(function ($exp) use ($request,$startDate,$endDate) {
    //             $departure = optional($exp->vbooking)->departure_date;

    //             return $departure &&
    //                 $departure >= $startDate &&
    //                 $departure <= $endDate;
    //         })->values();
    //     }else{
    //         $expenses = $expenses->filter(function ($exp) use ($request,$startDate,$endDate) {
    //             $departure = optional($exp->vbooking)->departure_date;

    //             return $departure &&
    //                 $departure >= $startDate &&
    //                 $departure <= $endDate;
    //         })->values();
    //     }

    //     return view('front.techclaim.history', compact('expenses'));
    // }

    /**
     * Show the form for creating a new resource.
     */
    // public function create($id, $empid)
    // {
    //     // $booking = Vbooking::find($id);

    //     $booking = Vbookingall::find($id);
    //     $person = Valldataemp::where('CODEMPID', $empid)->where('STAEMP', '!=', '9')->first();
    //     $empemail = $person->EMAIL;
    //     $empfullname = $person->NAMFIRSTT . ' ' . $person->NAMLASTT;

    //     $typegroup = 1;
    //     $totalDistance = 0;
    //     $startplant = "";
    //     $endplant = "";
    //     $PlantStart = $booking->bu;
    //     // $bu = $person->alias_name;
    //     $bu = BuEmp($empid);
    //     $level = 1;
    //     $empLevel = LevelEmp($empid);

    //     if ($empLevel <= 7) {
    //         $level = 1;
    //     } else {
    //         $level = 2;
    //     }


    //     // เช็คประเภทuser
    //     $groupspecial = GroupSpecial::where('empid', $empid)->first();
    //     // dd($groupspecial);
    //     if ($groupspecial) {
    //         if ($groupspecial->typeid == 1 || $groupspecial->typeid == 2) {
    //             $typegroup = 2;
    //         } else {
    //             $typegroup = 3;
    //         }
    //     }

    //     // Plant
    //     $plants = Plant::where('status', 1)->where('deleted', 0)
    //         // ->whereIn('id', [2, 4, 7, 9, 10, 11])
    //         ->get();

    //     // Databooking
    //     $departure_date = $booking->departure_date ? Carbon::parse("{$booking->departure_date} {$booking->departure_time}")->format('d/m/Y H:i') : null;
    //     $return_date = $booking->return_date ? Carbon::parse("{$booking->return_date} {$booking->return_time}")->format('d/m/Y H:i') : null;
    //     $reasons = ['อบรม', 'สัมมนา', 'ฝึกงาน', 'ติดตั้งเครื่องจักร', 'ลูกค้าร้องเรียน', 'พบลูกค้า', 'อื่นๆ'];

    //     if ($booking->locationid != 12) {
    //         $startplant = $PlantStart;
    //         $endplant = $booking->locationbu;
    //         $totalDistance = $this->getDistance($startplant, $endplant);
    //         // dd($totalDistance);
    //     }

    //     // Food
    //     $groupplant = Groupplant::with([
    //         'plant',
    //         'meal.group',
    //         'meal',
    //     ])
    //         ->where('deleted', 0)
    //         ->whereHas('plant', function ($query) use ($bu) {
    //             $query->where('plantname', $bu);
    //         })
    //         ->whereHas('meal.group', function ($query) use ($level) {
    //             $query->where('levelid', $level);
    //         })
    //         ->first();

    //     //  dd($groupplant->mealid);

    //     $startDate = Carbon::parse($booking->departure_date);
    //     $endDate = Carbon::parse($booking->return_date);
    //     $startTime = Carbon::parse($booking->departure_time);
    //     $endTime = Carbon::parse($booking->return_time);

    //     // Loop by day (you can change the interval to '1 week', '1 month', etc.)
    //     $Alldayfood = CarbonPeriod::create($startDate, '1 day', $endDate);

    //     // สายอนุมัติตาม group

    //     $headempid = "";
    //     $headlevel = "";
    //     $heademail = "";
    //     $headname = "";
    //     $groupapprove = GroupSpecial::where('empid',$empid)->where('deleted',0)->first();
    //     $level = LevelEmp($empid);
    //     $groupData = $groupapprove->groupapprove ?? 1;
    //     if($level > 7){
    //         $nextStep = 2;
    //     }else{
    //         $nextStep = 1;
    //     }
    //     $nextStepApprove = Approvestep($bu,3,$nextStep,$groupData);
    //     // dd($nextStepApprove);
    //     $heademail = $nextStepApprove["email"];
    //     $headname = $nextStepApprove["fullname"];
    //     $headempid = $nextStepApprove["empid"];

    //     // $heademail = 'Kamolwan.b@bgiglass.com';
    //     // $headname = 'กมลวรรณ บรรชา';
    //     // $headempid = '66000510';


    //     $approve_g = 0;
    //     $pageTech = 1;
    //     // dd(11);
    //     // ราคาน้ำมัน
    //     $ratefuels = Fuelprice::where("status", 1)->where("deleted", 0)->orderByDesc('startrate')->get();

    //     return view('front.techclaim.create', compact(['booking', 'empid', 'empemail', 'empfullname', 'typegroup', 'plants', 'ratefuels', 'departure_date', 'return_date', 'reasons', 'totalDistance', 'groupplant', 'Alldayfood', 'startDate', 'startTime', 'endDate', 'endTime', 'empLevel', 'headempid', 'headlevel', 'heademail', 'headname', 'approve_g','pageTech']));
    // }
    public function create($id, $empid)
    {
        // 1) Booking ต้องมี
        $booking = Vbookingall::find($id);
        if (!$booking) {
            abort(404, 'Booking not found');
        }

        // 2) คนต้องมี
        $person = Valldataemp::where('CODEMPID', $empid)
            ->where('STAEMP', '!=', '9')
            ->first();

        if (!$person) {
            abort(404, 'Employee not found');
        }

        $empemail    = $person->EMAIL ?? '';
        $empfullname = trim(($person->NAMFIRSTT ?? '') . ' ' . ($person->NAMLASTT ?? ''));

        $typegroup   = 1;
        $totalDistance = 0;
        $startplant = "";
        $endplant = "";
        $passengertype = 0;
        $rate_id = "";
        $bath_per_km = "";
        $oilid = "";
        $data_oil_price = "";
        if (($booking->type_reserve == 4)) {

            // ตรวจสอบ ผู้ร่วมเดินทาง
            if ($empid == $booking->passenger_empid) {
                if ($booking->passenger_empid == $booking->booking_emp_id) {
                    $passengertype = 0;
                } else {
                    $passengertype = 1;
                }
            }

            $travelDate = Carbon::parse($booking->departure_date)->startOfDay();

            // หาราคาที่น้อยกว่าหรือเท่ากับวันเดินทาง
            $fuelBefore = FuelPrice91::where('status', 1)
                ->where('deleted', 0)
                ->whereDate('dateprice', '<=', $travelDate)
                ->orderByDesc('dateprice')
                ->first();

            if (!$fuelBefore) {
                return response()->json(['message' => 'ไม่พบราคาก่อนวันเดินทาง']);
            }

            // หาราคาที่มากกว่าวันที่เจอ (คือ row ถัดไป)
            $fuelAfter = FuelPrice91::where('status', 1)
                ->where('deleted', 0)
                ->whereDate('dateprice', '>', $fuelBefore->dateprice)
                ->orderBy('dateprice')
                ->first();

            // ตรวจสอบว่า travelDate อยู่ระหว่าง fuelBefore กับ fuelAfter หรือไม่มี fuelAfter
            if (!$fuelAfter || $travelDate < Carbon::parse($fuelAfter->dateprice)) {
                $oilPrice = $fuelBefore->price;
                $oilPriceID = $fuelBefore->id;
            } else {
                return response()->json(['message' => 'วันที่เดินทางอยู่นอกช่วงราคาน้ำมันที่มีข้อมูล']);
            }

            // หาช่วงราคาที่ oilPrice ตกอยู่ในนั้น
            $rate = Fuelprice::where('status', 1)
                ->where('deleted', 0)
                ->where('startrate', '<=', $oilPrice)
                ->where('endrate', '>=', $oilPrice)
                ->first();

            if (!$rate) {

                $data_oil_price = $oilPrice;
                $data_message = 'ไม่พบช่วงราคาครอบคลุม';
            }

            $travel_date = $travelDate->format('d/m/Y');
            $data_oil_price = $oilPrice;
            $price_used_date = Carbon::parse($fuelBefore->dateprice)->format('d/m/Y');
            $rate_id = $rate->id;
            $bath_per_km = $rate->bathperkm;
            $oilid = $oilPriceID;
        }

        $PlantStart = $booking->bu;
        $bu        = BuEmp($empid); // อาจคืนค่า null ได้ แต่ไม่ทำให้พัง

        // ระดับพนักงาน
        $empLevel = (int) (LevelEmp($empid) ?? 0);
        $level    = $empLevel <= 7 ? 1 : 2;

        // สิทธิพิเศษ (กันพังด้วย ?->)
        $groupspecial = GroupSpecial::where('empid', $empid)->where('deleted', 0)->first();
        if ($groupspecial) {
            $typegroup = in_array($groupspecial->typeid, [1, 2], true) ? 2 : 3;
        }

        // Plant
        $plants = Plant::where('status', 1)->where('deleted', 0)->get();

        // วันเวลาเดินทาง (กัน null)
        $departure_date = $booking->departure_date
            ? Carbon::parse("{$booking->departure_date} {$booking->departure_time}")->format('d/m/Y H:i')
            : null;
        $return_date = $booking->return_date
            ? Carbon::parse("{$booking->return_date} {$booking->return_time}")->format('d/m/Y H:i')
            : null;

        $reasons = ['อบรม', 'สัมมนา', 'ฝึกงาน', 'ติดตั้งเครื่องจักร', 'ลูกค้าร้องเรียน', 'พบลูกค้า', 'อื่นๆ'];

        if ((int)$booking->locationid !== 12) {
            $startplant = $PlantStart;
            $endplant   = $booking->locationbu;
            $totalDistance = $this->getDistance($startplant, $endplant);
        }

        // กลุ่มมื้ออาหาร (ถ้า $bu หรือ level ไม่แมตช์ ผลจะเป็น null ได้)
        $groupplant = Groupplant::with(['plant', 'meal.group', 'meal'])
            ->where('deleted', 0)
            ->whereHas('plant', fn($q) => $q->where('plantname', $bu))
            ->whereHas('meal.group', fn($q) => $q->where('levelid', $level))
            ->first();

        $startDate = Carbon::parse($booking->departure_date);
        $endDate   = Carbon::parse($booking->return_date);
        $startTime = Carbon::parse($booking->departure_time);
        $endTime   = Carbon::parse($booking->return_time);
        $Alldayfood = CarbonPeriod::create($startDate, '1 day', $endDate);

        // สายอนุมัติ (กันพังด้วย null-safe)
        $groupData = $groupspecial?->groupapprove ?? 1;   // <-- จุดที่พังบ่อย
        $nextStep  = $empLevel > 7 ? 2 : 1;
        $nextStepApprove = Approvestep($bu, 3, $nextStep, $groupData) ?? [];

        $heademail = $nextStepApprove['email']    ?? '';
        $headname  = $nextStepApprove['fullname'] ?? '';
        $headempid = $nextStepApprove['empid']    ?? '';
        $headlevel = $nextStepApprove['level']    ?? '';

        $approve_g = 0;
        $pageTech  = 1;

        $ratefuels = Fuelprice::where('status', 1)
            ->where('deleted', 0)
            ->orderByDesc('startrate')
            ->get();

        $messageAlert = MessageAlert::where('status', 1)->where('deleted', 0)->first();
        $message_data = $messageAlert->message ?? 'test';

        //ตรวจว่าเป็น Base64 จริงไหม
        $isBase64 = false;
        if ($message_data) {
            $decoded = base64_decode($message_data, true); // true = strict mode
            if ($decoded !== false && base64_encode($decoded) === $message_data) {
                $isBase64 = true;
            }
        }

        // ถอดรหัสถ้าใช่ Base64
        $message_decode = $isBase64 ? base64_decode($message_data) : $message_data;

        //  $specialApprovers = "";
        $specialApprovers = Approvespecial::where('status', 1)
            ->where('deleted', 0)
            ->orderBy('id')
            ->get();

        // objectdata
        $objectdata = Objectdata::where('status', 1)
            ->where('deleted', 0)
            ->orderBy('id')
            ->get();

        return view('front.techclaim.create', compact(
            'booking',
            'objectdata',
            'empid',
            'empemail',
            'empfullname',
            'typegroup',
            'plants',
            'ratefuels',
            'departure_date',
            'return_date',
            'reasons',
            'totalDistance',
            'groupplant',
            'Alldayfood',
            'startDate',
            'startTime',
            'endDate',
            'endTime',
            'empLevel',
            'headempid',
            'headlevel',
            'heademail',
            'headname',
            'approve_g',
            'pageTech',
            'passengertype',
            'rate_id',
            'data_oil_price',
            'oilid',
            'bath_per_km',
            'messageAlert',
            'message_decode',
            'specialApprovers'
        ));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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

    private function getDistance($startName, $endName)
    {
        return DistanceRate::where(function ($q) use ($startName, $endName) {
            $q->whereHas('Startplant', function ($q2) use ($startName) {
                $q2->where('plantname', $startName);
            })->whereHas('Endplant', function ($q3) use ($endName) {
                $q3->where('plantname', $endName);
            });
        })->orWhere(function ($q) use ($startName, $endName) {
            $q->whereHas('Startplant', function ($q2) use ($endName) {
                $q2->where('plantname', $endName);
            })->whereHas('Endplant', function ($q3) use ($startName) {
                $q3->where('plantname', $startName);
            });
        })->where('deleted', 0)->value('kilometer') ?? 0;
    }
}
