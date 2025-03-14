<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Groupprice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class GrouppriceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data =[];
        $result=[];

        // $groupprices = Groupprice::where('groupprices.deleted', '0')
        // ->leftJoin('levels', 'groupprices.levelid', '=', 'levels.id')
        // ->select('groupprices.*', 'levels.levelname')
        // ->get(); #Query Builder

        $groupprices = Groupprice::with('level')->where('deleted', '0')->get();  #Eloquent
        foreach ($groupprices as $groupprice) {
            $data = [
                'id' => $groupprice->id,
                'groupname' => $groupprice->groupname,
                'levelname' => $groupprice->level->levelname,
                'status' => $groupprice->status == 1 ? '<span class="badge rounded-pill bg-success">Active</span>' : '<span class="badge rounded-pill bg-danger">Inactive</span>',
                'action' => '<button class="btn btn-warning btn-sm btngroupedit"  data-id="' . $groupprice->id . '"><i class="mdi mdi-pencil-circle-outline"></i> edit</button> <button type="button" class="btn btn-danger btn-sm deletegroup"  data-id="' . $groupprice->id . '"><i class="mdi mdi-trash-can"></i></button>',
            ];
            $result[] = $data;
        }
        return response()->json(['data' => $result]);
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
            'groups' => [
                'required',
                Rule::unique('groupprices', 'groupname')->where(function ($query) use ($request) {
                    return $query->where('levelid', '=', $request->level)->where('deleted', 0);
                }),
            ],
            'level' => 'required',
        ], [
            'groups.required' => 'กรุณากรอกชื่อกลุ่ม',
            'groups.unique' => 'มีชื่อกลุ่มนี้อยู่แล้วกรุณาตรวจสอบข้อมูล',
            'level.required' => 'กรุณาเลือกระดับ',
        ]);

        // dd($request->validate);

        $groupprice = new Groupprice();
        $groupprice->groupname = $request->groups;
        $groupprice->status = $request->status;
        $groupprice->levelid = $request->level;
        $groupprice->created_by = Auth::id();

        try {
            $groupprice->save();
            return response()->json(['message' => 'เพิ่มข้อมูลเรียบร้อย', 'class' => 'success'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => 'เพิ่มข้อมูลไม่สำเร็จ', 'class' => 'error'], 200);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Groupprice $groupprice)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data = Groupprice::find($id);
        return response()->json(['data' => $data]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'groups' => [
                'required',
                Rule::unique('groupprices', 'groupname')->ignore($id)->where(function ($query) use ($request) {
                    return $query->where('levelid', '=', $request->level)->where('deleted', 0);
                }),
            ],
            'level' => 'required',
        ], [
            'groups.required' => 'กรุณากรอกชื่อกลุ่ม',
            'groups.unique' => 'มีชื่อกลุ่มนี้อยู่แล้วกรุณาตรวจสอบข้อมูล',
            'level.required' => 'กรุณาเลือกระดับ',
        ]);

        $update = Groupprice::find($id);
        $update->groupname = $request->groups;
        $update->status = $request->status;
        $update->levelid = $request->level;
        $update->modified_by = '1';
        // dd($request->id, $update);

        try {
            $update->save();
            return response()->json(['message' => 'แก้ไขข้อมูลเรียบร้อย', 'class' => 'success'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => 'แก้ไขข้อมูลไม่สำเร็จ', 'class' => 'error'], 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $delete = Groupprice::findOrFail($id);
        $delete->status = 0;
        $delete->deleted = 1;
        $delete->modified_by = Auth::id();
        try {
            $delete->save();
            return response()->json(['message' => 'ลบข้อมูลเรียบร้อย', 'class' => 'success'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => 'ลบข้อมูลไม่สำเร็จ', 'class' => 'error'], 200);
        }
    }
}
