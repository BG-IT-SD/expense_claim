<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\ApproveStaff;
use App\Models\Plant;
use App\Models\Plantsettingdetail;
use App\Models\Plantsettinghead;
use App\Models\Valldataemp;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class HRgroupController extends Controller
{
    public function index(){
        $hrgroups = Plantsettinghead::where('deleted',0)->get();
        return view('back.hrgroup.index',compact('hrgroups'));
    }

    public function edit($id){

        $hrgroupId= $id;
        $hrplants = Plantsettingdetail::with('plant')->where('headid',$id)->where('deleted',0)->get();
        $staffapproves = ApproveStaff::where('group',$id)->where('step', 9)->where('deleted', 0)->get();
        return view('back.hrgroup.edit',compact('hrplants','hrgroupId','staffapproves'));
    }

    public function addList($id){

        return view('back.hrgroup.addlist',compact('id'));
    }


    public function addPlant($id){

       // ดึง id เช็ค
        $plantedIds = Plantsettingdetail::where('deleted', 0)->pluck('plantid')->toArray();

        // เอาเฉพาะ plant ที่ยังไม่อยู่ใน รายการแต่ละกลุ่ม
        $plants = Plant::where('deleted', 0)
            ->whereNotIn('id', $plantedIds)
            ->get();

        return view('back.hrgroup.addplant',compact('id','plants'));
    }

    public function SavePlant(Request $request){
        $request->validate([
            'plant' => 'required',
        ]);
        $groupID = isset($request->groupid) ? $request->groupid : null;
        if($groupID != null){
            try {
                $create = new Plantsettingdetail;
                $create->headid = $groupID;
                $create->plantid = $request->plant;
                $create->save();

                return redirect()->route('HRgroup.edit',$groupID)->with(['message' => 'บันทึกสำเร็จ', 'class' => 'success']);
            } catch (\Throwable $th) {
                //throw $th;
                return redirect()->route('HRgroup.edit',$groupID)->with(['message' => 'บันทึกไม่สำเร็จ', 'class' => 'error']);
            }



        }



    }

    public function CheckEmpID(Request $request)
    {
        $request->validate([
            'empid' => [
                'required',
                Rule::unique('approvestaff')->where(function ($query) {
                    return $query->where('deleted', 0);
                }),
            ],
        ], [
            'empid.unique' => 'มีกลุ่มแล้วกรุณาตรวจสอบข้อมูล',
        ]);



        $empid = $request->empid;

        $employees = Valldataemp::where('CODEMPID', $empid)
            ->where('STAEMP', '!=', '9')
            ->first();

        if ($employees) {
            return response()->json(['status' => 200, 'message' => 'พบข้อมูลพนักงาน', 'employees' => $employees], 200);
        } else {
            return response()->json(['status' => 404, 'message' => 'ไม่พบข้อมูล'], 200);
        }
    }
}
