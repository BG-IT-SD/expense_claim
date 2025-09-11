<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\ApproveStaff;
use Illuminate\Http\Request;

class ApporveDriverController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

     $staffapproves = ApproveStaff::whereIn('step', [1, 2])
        ->where('extype', 2)
        ->where('deleted', 0)
        ->orderBy('step')
        ->get();

        return view('back.driverapprove.index' ,compact('staffapproves'));

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
        $approveStaff = ApproveStaff::findorFail($id);
        return view('back.driverapprove.edit', compact('approveStaff', 'id'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //   $id = $request->input('id', '');
        $empid = $request->input('head_id', '');
        $empname = $request->input('head_name', '');
        $email = $request->input('head_email', '');
        $groupID = $request->input('groupid', '');
        $step = $request->input('step', '');

        if ($id != "") {

            $update = ApproveStaff::findOrFail($id);

            if (in_array($step, [1, 2])) {
                //ถ้าเป็น ผู้จัดการส่วน หรือ ผู้จัดการฝ่าย ให้ insert คนใหม่เข้าไปและ update คนเก่าเป็น Inactive
                if ($empid != "") {
                    $create = new ApproveStaff();
                    $create->empid = $empid;
                    $create->email = $email;
                    $create->fullname = $empname;
                    $create->extype = $update->extype;
                    $create->step = $step;
                    $create->group = $groupID;

                    $update->status = 0;
                    $update->deleted = 1;

                    $create->save();
                }else{
                    $update->status = 1;
                }


            }

            try {
                $update->save();

                return redirect()->route('DriverApprove.index')->with(['message' => 'บันทึกสำเร็จ', 'class' => 'success']);
            } catch (\Throwable $th) {
                return redirect()->route('DriverApprove.index')->with(['message' => 'บันทึกไม่สำเร็จ' . $th, 'class' => 'error']);
                // $th->getMessage()
            }
        }
        return redirect()->route('DriverApprove.index')->with(['message' => 'ไม่พบข้อมูลสำหรับอัปเดต', 'class' => 'error']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
