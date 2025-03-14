<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Groupplant;
use App\Models\Groupprice;
use App\Models\Level;
use App\Models\Plant;
use App\Models\Pricepermeal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PricepermealController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $levels = Level::where('deleted', '0')->get();
        // $groupprices = Groupprice::where('groupprices.deleted', '0')
        // ->where('groupprices.status','1')
        // ->leftJoin('levels', 'groupprices.levelid', '=', 'levels.id')
        // ->select('groupprices.*', 'levels.levelname')
        // ->orderBy('groupprices.id', 'asc')
        // ->get(); Query Builder
        // $groupprices = Groupprice::with('level')->where('deleted', '0')->where('status', '1')->get();  #Eloquent
        return view('back.pricepermeal.index', compact('levels'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $plants = Plant::where('deleted', '0')->get();
        $groupprices = Groupprice::with('level')->where('deleted', '0')->where('status', '1')
        ->whereDoesntHave('pricepermeal', function ($query) {
            $query->where('deleted', '0');
        })
        ->get(); // Exclude records that exist in Pricepermeal
        return view('back.pricepermeal.create', compact('plants', 'groupprices'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $levelID = $request->levelid;

        $request->validate([
            'groupprice' => 'required',
            'meal1' => 'required|numeric|min:0',
            'meal2' => 'required|numeric|min:0',
            'meal3' => 'required|numeric|min:0',
            'meal4' => 'nullable|numeric|min:0',
            'plants' => 'required|array|min:1', // ตรวจสอบว่าเป็น Array และมีอย่างน้อย 1 ค่า
        ], [
            'groupprice.required' => 'กรุณาเลือกกลุ่ม',
            'meal1.required' => 'กรุณากรอกมื้อเช้า',
            'meal2.required' => 'กรุณากรอกมื้อกลางวัน',
            'meal3.required' => 'กรุณากรอกมื้อเย็น',
            'meal4.required' => 'กรุณากรอกมื้อดึก',
            'plants.required' => 'กรุณาเลือก Plant 1 รายการ',
            // 'plants.array' => 'ข้อมูลต้องอยู่ในรูปแบบ Array',
            'plants.*.required' => 'ค่าแต่ละรายการต้องไม่ว่าง',

        ]);

        $meal4 = isset($request->meal4) ? (float) $request->meal4 : 0;
        DB::beginTransaction(); // เริ่ม Transaction เพื่อป้องกันข้อมูลไม่ครบ

        try {
            // ✅ 1. บันทึก pricepermeal
            $pricepermeal = new Pricepermeal();
            $pricepermeal->groupid = $request->groupprice;
            $pricepermeal->meal1 = $request->meal1;
            $pricepermeal->meal2 = $request->meal2;
            $pricepermeal->meal3 = $request->meal3;
            $pricepermeal->meal4 = $request->meal4;
            $pricepermeal->created_by = Auth::id();
            $pricepermeal->save();

            if (!$pricepermeal->id) {
                // return response()->json(['message' => 'บันทึก pricepermeal ไม่สำเร็จ', 'class' => 'error'], 500);
                return redirect()->route('Pricepermeal.index')->with([
                    'message' => 'บันทึก pricepermeal ไม่สำเร็จ',
                    'class' => 'error',
                ]);
            }

            // ✅ 2. วนลูปบันทึก $plants
            if (!empty($request->plants) && is_array($request->plants)) {
                $plantData = [];
                foreach ($request->plants as $plant) {
                    $plantData[] = [
                        'mealid' => (int)$pricepermeal->id, // เชื่อมกับตาราง pricepermeal
                        'plantid' => (int)$plant,
                        'created_by' => Auth::id(),
                    ];
                }

                Groupplant::insert($plantData); // ใช้ batch insert เพื่อลด Query
            }

            DB::commit(); // ✅ ยืนยันการบันทึกทั้งหมด
            // return response()->json(['message' => 'เพิ่มข้อมูลเรียบร้อย', 'class' => 'success'], 200);
            return redirect()->route('Pricepermeal.index')->with([
                'message' => 'เพิ่มข้อมูลเรียบร้อย',
                'class' => 'success',
            ]);
        } catch (\Exception $e) {
            DB::rollBack(); //  ยกเลิกการบันทึกทั้งหมดถ้ามี Error
            // return response()->json(['message' => 'เพิ่มข้อมูลไม่สำเร็จ' . $e->getMessage(), 'class' => 'error'], 200);
            return redirect()->route('Pricepermeal.index')->with([
                'message' => 'เพิ่มข้อมูลไม่สำเร็จ' . $e->getMessage(),
                'class' => 'error',
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Pricepermeal $pricepermeal)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pricepermeal $pricepermeal)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pricepermeal $pricepermeal)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction(); // เริ่ม Transaction เพื่อป้องกันข้อมูลไม่ครบ

        try {
            // ✅ 1. อัปเดตข้อมูลใน Pricepermeal
            $pricepermeal = Pricepermeal::findOrFail($id);
            $pricepermeal->status = 0;
            $pricepermeal->deleted = 1;
            $pricepermeal->modified_by = Auth::id();
            $pricepermeal->save();

            // ✅ 2. อัปเดตข้อมูลใน Plant ที่เกี่ยวข้อง
            Groupplant::where('mealid', $id)->update([
                'deleted' => 1,
                'status' => 0,
                'modified_by' => Auth::id(),
            ]);

            DB::commit(); // ✅ ยืนยันการอัปเดต
            return response()->json(['message' => 'ลบข้อมูลสำเร็จ', 'class' => 'success'], 200);
        } catch (\Exception $e) {
            DB::rollBack(); // ยกเลิกการอัปเดตทั้งหมดถ้ามี Error
            return response()->json([
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage(),
                'class' => 'error'
            ], 500);
        }
    }

    public function list()
    {
        $data = [];
        $result = [];
        $pricepermeals = Pricepermeal::with('group')->where('deleted', '0')->get();  #Eloquent
        foreach ($pricepermeals as $pricepermeal) {
            $data = [
                'id' => $pricepermeal->id,
                'groupname' => $pricepermeal->group ? $pricepermeal->group->groupname . ' ' . $pricepermeal->group->level->levelname : 'N/A',
                'status' => $pricepermeal->status == 1 ? '<span class="badge rounded-pill bg-success">Active</span>' : '<span class="badge rounded-pill bg-danger">Inactive</span>',
                'action' => '<button class="btn btn-warning btn-sm" onclick="window.location.href=\'' . route('Pricepermeal.edit', $pricepermeal->id) . '\'"><i class="mdi mdi-pencil-circle-outline"></i> edit</button>
                <button type="button" class="btn btn-danger btn-sm deletemeal" data-id="' . $pricepermeal->id . '"><i class="mdi mdi-trash-can"></i></button>',
            ];
            $result[] = $data;
        }

        return response()->json(['data' => $result]);
    }
}
