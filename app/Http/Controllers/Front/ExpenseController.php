<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\DistanceRate;
use App\Models\Groupplant;
use App\Models\GroupSpecial;
use App\Models\Plant;
use App\Models\Valldataemp;
use App\Models\Vbooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;


class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // ข้อมูลจองรถตาม CODEMPID 7 วันย้อนหลัง
        // create view ที่รวมเป็นผู้โดยสารด้วย
        $empid = Auth::user()->empid;
        $booking = Vbooking::where('booking_emp_id', "$empid")
            ->orWhere('passenger_empid', "$empid")
            ->get()
            ->unique('id')
            ->values();
            // ->unique('booking_id');
        // dd($booking);
        return view('front.expenses.list', compact('booking'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
    {
        $booking = Vbooking::find($id);
        $empid = Auth::user()->empid;
        // $booking->booking_emp_id ?? "";
        $typegroup = 1;
        $totalDistance = 0;
        $startplant = "";
        $endplant = "";
        $PlantStart = $booking->bu;
        $bu = Auth::user()->bu;
        $level = 1;

        if (session('level') <= 7) {
            $level = 1;
        } else {
            $level = 2;
        }
        // เช็คประเภทuser
        $groupspecial = GroupSpecial::where('empid', $empid)->get();
        if ($groupspecial->isNotEmpty()) {
            if ($groupspecial->id == 1 || $groupspecial->id == 2) {
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

        $startDate = Carbon::parse($booking->departure_date);
        $endDate = Carbon::parse($booking->return_date);
        $startTime = Carbon::parse($booking->departure_time);
        $endTime = Carbon::parse($booking->return_time);

        // Loop by day (you can change the interval to '1 week', '1 month', etc.)
        $Alldayfood = CarbonPeriod::create($startDate, '1 day', $endDate);





        // dd($booking);
        if ($empid != "") {
            if ($typegroup == 1) {
                return view('front.expenses.index', compact('booking', 'typegroup', 'plants', 'departure_date', 'return_date', 'reasons', 'totalDistance', 'groupplant', 'Alldayfood', 'startDate', 'startTime', 'endDate', 'endTime'));
            } else {
                echo 'ไม่ใช้ประเภทคนทั่วไป [พขร ช่าง admin ทำการเบิก]';
            }
        }
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
