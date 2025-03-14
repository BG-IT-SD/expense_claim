<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\FuelPrice91;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class FuelPrice91Controller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        // ข้อมูลเริ่มต้นเป็น data ของเดือนปัจจุบัน
        $month = date('m');
        $year = date('Y');
        $startdate = isset($request->startdate) ? $request->startdate : "";
        $enddate = isset($request->enddate) ? $request->enddate : "";
        $oilLists="";

        // เรียก API ราคาน้ำมันจากบางจาก
        // $response = Http::get('https://oil-price.bangchak.co.th/ApiOilPrice2/th');
        // if ($response->successful()) {
        //     $data = $response->json();

        //     // แปลง OilList จาก JSON String เป็น Array
        //     $oilLists = json_decode($data[0]['OilList'], true);
        //     // $data[0]['OilList']
        // }


        $fuelprice91s = FuelPrice91::where('deleted', '0')
        ->when($startdate == "" && $enddate == "", function($query) use ($month, $year) {
            return $query->whereMonth('dateprice', $month)
                         ->whereYear('dateprice', $year);
        })
        ->when($startdate != "" && $enddate != "", function($query) use ($startdate, $enddate) {
            return $query->whereBetween('dateprice', [$startdate, $enddate]);
        })
        ->get();
        return view('back.fuelprice91.index', compact('fuelprice91s','oilLists'));
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
        $request->validate([
            'dateprice' => 'required',
            'price' => 'required|numeric|min:0',
        ], [
            'dateprice.required' => 'กรุณากรอกวันที่',
            'price.required' => 'กรุณากรอกราคา',
        ]);

        // FuelPrice91::create([
        //     'dateprice' => $request->dateprice,
        //     'price' => $request->price,
        //     'created_by' => '1',
        // ]);

        // FuelPrice::create($request->all());

        $create = new FuelPrice91();
        $create->dateprice = $request->dateprice;
        $create->price = $request->price;
        $create->created_by = Auth::id();

        try {
            $create->save();
            // return redirect()->back()->with('success','เพิ่มข้อมูลเรียบร้อย');
            return response()->json(['message' => 'เพิ่มข้อมูลเรียบร้อย', 'class' => 'success'], 200);
        } catch (\Throwable $th) {
            // throw $th;
            return response()->json(['message' => 'เพิ่มข้อมูลไม่สำเร็จ', 'class' => 'error'], 200);
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
        $edit = FuelPrice91::find($id);
        return response()->json($edit);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'dateprice' => 'required',
            'price' => 'required|numeric|min:0',
        ], [
            'dateprice.required' => 'กรุณากรอกวันที่',
            'price.required' => 'กรุณากรอกราคา',
        ]);

        $update = FuelPrice91::find($id);
        $update->dateprice = $request->data->dateprice;
        $update->price = $request->data->price;
        $update->modified_by = Auth::id();

        try {
            $update->save();
            return response()->json(['message' => 'แก้ไขข้อมูลเรียบร้อย', 'class' => 'success'], 200);
        } catch (\Throwable $th) {
            // throw $th;
            return response()->json(['message' => 'แก้ไขข้อมูลไม่สำเร็จ', 'class' => 'error'], 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        $Delete = FuelPrice91::findOrFail($id);
        $Delete->status = 0;
        $Delete->deleted = 1;
        $Delete->modified_by = Auth::id();

        try {
            $Delete->save();
            return response()->json(['message' => 'ลบข้อมูลเรียบร้อยแล้ว', 'class' => 'success'], 200);
        } catch (\Throwable $th) {
            // throw $th;
            return response()->json(['message' => 'ลบข้อมูลไม่สำเร็จ', 'class' => 'error'], 200);
        }
    }

    // public function getMonthFromDate($dateString) {
    //     $date = new DateTime($dateString);
    //     return $date->format('m');
    // }
}
