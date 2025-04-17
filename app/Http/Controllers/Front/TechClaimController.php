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

class TechClaimController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $groupEmpIds = GroupSpecial::where('typeid', 3)->pluck('fullname', 'empid')->toArray();

        $bookings = Vbooking::where(function ($q) use ($groupEmpIds) {
                $q->whereIn('booking_emp_id', array_keys($groupEmpIds))
                  ->orWhereIn('passenger_empid', array_keys($groupEmpIds));
            })
            ->get();

        // ผูก expense ทีละรายการด้วย passenger_empid
        foreach ($bookings as $booking) {
            $booking->expense = Expense::where('bookid', $booking->id)
                ->where('empid', $booking->passenger_empid)
                ->with(['latestApprove', 'user'])
                ->first();
        }

        return view('front.techclaim.list', compact('bookings', 'groupEmpIds'));
    }

    public function history(){

        $expenses = Expense::with(['latestApprove', 'vbooking', 'tech'])
        ->where('extype', 3)
        ->whereHas('latestApprove', function ($query) {
            $query->whereIn('typeapprove', [1, 2, 3, 4, 5]);
        })
        ->get();

    return view('front.techclaim.history', compact('expenses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id, $empid)
    {
        $booking = Vbooking::find($id);
        $person = Valldataemp::where('CODEMPID', $empid)->where('STAEMP', '!=', '9')->first();
        $empemail = $person->EMAIL;
        $empfullname = $person->NAMFIRSTT . ' ' . $person->NAMLASTT;

        $typegroup = 1;
        $totalDistance = 0;
        $startplant = "";
        $endplant = "";
        $PlantStart = $booking->bu;
        $bu = $person->alias_name;
        $level = 1;
        $empLevel = LevelEmp($empid);

        if ($empLevel <= 7) {
            $level = 1;
        } else {
            $level = 2;
        }


        // เช็คประเภทuser
        $groupspecial = GroupSpecial::where('empid', $empid)->first();
        // dd($groupspecial);
        if ($groupspecial) {
            if ($groupspecial->typeid == 1 || $groupspecial->typeid == 2) {
                $typegroup = 2;
            } else {
                $typegroup = 3;
            }
        }

        // Plant
        $plants = Plant::where('status', 1)->where('deleted', 0)
            // ->whereIn('id', [2, 4, 7, 9, 10, 11])
            ->get();

        // Databooking
        $departure_date = $booking->departure_date ? Carbon::parse("{$booking->departure_date} {$booking->departure_time}")->format('d/m/Y H:i') : null;
        $return_date = $booking->return_date ? Carbon::parse("{$booking->return_date} {$booking->return_time}")->format('d/m/Y H:i') : null;
        $reasons = ['อบรม', 'สัมมนา', 'ฝึกงาน', 'ติดตั้งเครื่องจักร', 'ลูกค้าร้องเรียน', 'พบลูกค้า', 'อื่นๆ'];

        if ($booking->locationid != 12) {
            $startplant = $PlantStart;
            $endplant = $booking->locationbu;
            $totalDistance = $this->getDistance($startplant, $endplant);
            // dd($totalDistance);
        }

        // Food
        $groupplant = Groupplant::with([
            'plant',
            'meal.group',
            'meal',
        ])
            ->where('deleted', 0)
            ->whereHas('plant', function ($query) use ($bu) {
                $query->where('plantname', $bu);
            })
            ->whereHas('meal.group', function ($query) use ($level) {
                $query->where('levelid', $level);
            })
            ->first();

        // dd($groupplant->mealid);

        $startDate = Carbon::parse($booking->departure_date);
        $endDate = Carbon::parse($booking->return_date);
        $startTime = Carbon::parse($booking->departure_time);
        $endTime = Carbon::parse($booking->return_time);

        // Loop by day (you can change the interval to '1 week', '1 month', etc.)
        $Alldayfood = CarbonPeriod::create($startDate, '1 day', $endDate);

        // สายอนุมัติตาม group

        $headempid = "";
        $headlevel = "";
        $heademail = "";
        $headname = "";
        $groupapprove = GroupSpecial::where('empid',$empid)->where('deleted',0)->first();
        $level = LevelEmp($empid);
        $groupData = $groupapprove->groupapprove ?? 1;
        if($level > 7){
            $nextStep = 2;
        }else{
            $nextStep = 1;
        }
        $nextStepApprove = Approvestep($bu,3,$nextStep,$groupData);
        // dd($nextStepApprove);
        // $heademail = $nextStepApprove["email"];
        // $headname = $nextStepApprove["fullname"];
        // $headempid = $nextStepApprove["empid"];

        $heademail = 'Kamolwan.b@bgiglass.com';
        $headname = 'กมลวรรณ บรรชา';
        $headempid = '66000510';


        $approve_g = 0;
        // ราคาน้ำมัน
        $ratefuels = Fuelprice::where("status", 1)->where("deleted", 0)->orderByDesc('startrate')->get();

        return view('front.techclaim.create', compact(['booking', 'empid', 'empemail', 'empfullname', 'typegroup', 'plants', 'ratefuels', 'departure_date', 'return_date', 'reasons', 'totalDistance', 'groupplant', 'Alldayfood', 'startDate', 'startTime', 'endDate', 'endTime', 'empLevel', 'headempid', 'headlevel', 'heademail', 'headname', 'approve_g']));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

    }

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
