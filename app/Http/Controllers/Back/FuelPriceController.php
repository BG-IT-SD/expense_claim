<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Fuelprice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FuelPriceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $fuelprices = Fuelprice::where('deleted', '0')->get();
        return view('back.FuelPrice.index', compact('fuelprices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('back.FuelPrice.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate([
            'startrate' => 'required|min:0',
            'endrate' => 'required|min:0',
            'bathperkm' => 'required|min:0',

        ], [
            'startrate.required' => 'กรุณาราคาเริ่มต้น',
            'endrate.required' => 'กรุณาราคาสิ้นสุด',
            'bathperkm.required' => 'กรุณากรอกบาท / กิโลกรัม',
        ]);

        $data = $request->all();
        $data['created_by'] = Auth::id(); // ✅ เพิ่มข้อมูลผู้ใช้
        // dd($data);
        try {

            Fuelprice::create($data);
            return redirect()->route('FuelPrice.index')->with([
                'message' => 'บันทึกข้อมูลสำเร็จ',
                'class' => 'success',
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            return redirect()->route('FuelPrice.index')->with([
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
        // ดึงข้อมูล FuelPrice จากฐานข้อมูล
        $fuelPrice = FuelPrice::find($id);
        return view('back.FuelPrice.create', compact('fuelPrice'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'startrate' => 'required|min:0',
            'endrate' => 'required|min:0',
            'bathperkm' => 'required|min:0',

        ], [
            'startrate.required' => 'กรุณาราคาเริ่มต้น',
            'endrate.required' => 'กรุณาราคาสิ้นสุด',
            'bathperkm.required' => 'กรุณากรอกบาท / กิโลกรัม',
        ]);

        $fuelPrice = FuelPrice::find($id);
        try {
            $request->merge(['modified_by' => Auth::id()]);
            // อัปเดตข้อมูลทั้งหมดจาก request
            $fuelPrice->update($request->all());
            return redirect()->route('FuelPrice.index')->with([
                'message' => 'บันทึกข้อมูลสำเร็จ',
                'class' => 'success',
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            return redirect()->route('FuelPrice.index')->with([
                'message' => 'บันทึกข้อมูลไม่สำเร็จ',
                'class' => 'error',
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $Delete = Fuelprice::findOrFail($id);
        $Delete->status = 0;
        $Delete->deleted = 1;
        $Delete->modified_by = Auth::id();

        try {
            $Delete->save();
            return response()->json(['message' => 'ลบข้อมูลสำเร็จ', 'class' => 'success'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => 'ลบข้อมูลไม่สำเร็จ', 'class' => 'error'], 200);
        }
    }
}
