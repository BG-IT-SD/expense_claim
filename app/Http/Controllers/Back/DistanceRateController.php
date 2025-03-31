<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\DistanceRate;
use App\Models\Plant;
use Illuminate\Http\Request;

class DistanceRateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rates = DistanceRate::with(['Startplant', 'Endplant'])->where('deleted', 0)->get();
        $plantIds = collect($rates)->pluck('startplant')->merge($rates->pluck('endplant'))->unique()->sort();
        $plants = Plant::whereIn('id', $plantIds)->orderBy('id')->get();
        // dd($plants);

        $groupedPlants = collect($plants)->map(function ($plant) {
            // รวม AGI/AY1/AY2 เข้า group เดียว
            if (in_array($plant->id, [4, 10, 11])) {
                return (object)[
                    'id' => 'agi_group',
                    'plantname' => 'AGI/AY1/AY2',
                ];
            }
            return $plant;
        })->unique('id')->values(); // ลบซ้ำหลังรวม
        $matrixGrouped = [];

        foreach ($rates as $row) {
            $start = in_array($row->startplant, [4, 10, 11]) ? 'agi_group' : $row->startplant;
            $end   = in_array($row->endplant, [4, 10, 11]) ? 'agi_group' : $row->endplant;

            // ถ้าต้นทาง ≠ ปลายทาง
            if ($start !== $end) {
                $matrixGrouped[$start][$end] = $row->kilometer;
            }
        }



        return view('back.DistanceRate.index', compact('rates', 'plants', 'matrixGrouped', 'groupedPlants'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $plants = Plant::where('status', 1)->where('deleted', 0)->get();

        return view('back.DistanceRate.create', compact('plants'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'startplant' => 'required|integer|exists:plants,id',
            'endplant'   => 'required|integer|exists:plants,id|different:startplant',
            'kilometer'  => 'required|numeric|min:0.01',
        ], [
            'startplant.required' => 'กรุณาเลือก Plant เริ่มต้น',
            'startplant.integer' => 'ค่าที่เลือกไม่ถูกต้อง',
            'startplant.exists' => 'Plant เริ่มต้นไม่พบในระบบ',

            'endplant.required' => 'กรุณาเลือก Plant ปลายทาง',
            'endplant.integer' => 'ค่าที่เลือกไม่ถูกต้อง',
            'endplant.exists' => 'Plant ปลายทางไม่พบในระบบ',
            'endplant.different' => 'Plant เริ่มต้นและปลายทางต้องไม่ซ้ำกัน',

            'kilometer.required' => 'กรุณากรอกระยะทาง',
            'kilometer.numeric' => 'ระยะทางต้องเป็นตัวเลข',
            'kilometer.min' => 'ระยะทางต้องมากกว่า 0',
        ]);

        $exists = DistanceRate::where('startplant', $validated['startplant'])
            ->where('endplant', $validated['endplant'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['startplant' => 'มีระยะทางนี้ในระบบแล้ว']);
        }


        // เพิ่ม ข้อมูลจาก validate
        $data = $validated;
        try {

            // บันทึกด้วย create()
            DistanceRate::create($data);
            return redirect()->route('DistanceRate.index')->with([
                'message' => 'บันทึกข้อมูลสำเร็จ',
                'class' => 'success',
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            return redirect()->route('DistanceRate.index')->with([
                'message' => 'บันทึกข้อมูลไม่สำเร็จ',
                'class' => 'error',
            ]);
        }
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
}
