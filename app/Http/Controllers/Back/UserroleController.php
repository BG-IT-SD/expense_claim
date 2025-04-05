<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserroleRequest;
use App\Http\Requests\UpdateUserroleRequest;
use App\Models\Module;
use App\Models\Role;
use App\Models\Userrole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserroleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $userId = $request->userid;
        $data = [];
        $result = [];
        $userroles = Userrole::with('module', 'role')
        ->where('deleted', 0)
        ->where('userid', $userId)
        ->get();
        foreach ($userroles as $userrole) {
            $data = [
                'id' => $userrole->id,
                'module' => $userrole->module->modulename,
                'role' => $userrole->role->rolename,
                'status' => $userrole->status == 1 ? '<span class="badge rounded-pill bg-success">Active</span>' : '<span class="badge rounded-pill bg-danger">Inactive</span>',
                'action' => '<button class="btn btn-warning btn-sm btnroleedit"  data-id="' . $userrole->id . '"><i class="mdi mdi-pencil-circle-outline"></i> edit</button> <button type="button" class="btn btn-danger btn-sm deleterole"  data-id="' . $userrole->id . '"><i class="mdi mdi-trash-can"></i></button>',
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

        return view('back.userrole.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserroleRequest $request)
    {
        try {
            $userroles = Userrole::create($request->validated());
            return response()->json(['message' => 'เพิ่มข้อมูลเรียบร้อย', 'class' => 'success', 'role' => $userroles], 200);
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
        $data = Userrole::find($id);
        return response()->json(['data' => $data]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserroleRequest $request, string $id)
    {

        $userrole = Userrole::findOrFail($id);
        try {
            $userrole->update($request->validated());
            return response()->json(['message' => 'แก้ไขข้อมูลเรียบร้อย', 'class' => 'success', 'role' => $userrole], 200);
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
        {
            $delete = Userrole::findOrFail($id);
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
}
