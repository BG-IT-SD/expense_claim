<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::where('deleted', '0')->get();
        $modules = Module::where('deleted', '0')->get();
        return view('back.role.index', compact('roles', 'modules'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($type)
    {
        return view('back.role.create', compact('type'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $type = $request->type;
        if ($type == '1') {
            $request->validate([
                'modulename' => 'required',
            ], [
                'modulename.required' => 'กรุณากรอกชื่อหน้าจอการทำงาน',
            ]);

            $create = new Module();
            $create->modulename = $request->modulename;
            $create->status = $request->status;
            $create->created_by = Auth::id();
        } else {
            $request->validate([
                'rolename' => 'required',
            ], [
                'rolename.required' => 'กรุณากรอกชื่อสิทธิ',
            ]);

            $create = new Role();
            $create->rolename = $request->rolename;
            $create->status = $request->status;
            $create->created_by = Auth::id();
        }

        try {
            $create->save();
            return redirect()->route('Role.index')->with([
                'message' => 'บันทึกข้อมูลสำเร็จ',
                'class' => 'success',
            ]);
        } catch (\Throwable $th) {
            return redirect()->route('Role.index')->with([
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
    public function edit($Role, $type = null)
    {
        if ($type == 1) {
            $roles = Module::find($Role);
        } else {
            $roles = Role::find($Role);
        }

        // dd($roles);

        return view('back.role.create', compact('roles', 'type'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $type = $request->type;
        if ($type == '1') {
            $request->validate([
                'modulename' => 'required',
            ], [
                'modulename.required' => 'กรุณากรอกชื่อหน้าจอการทำงาน',
            ]);
            $update = Module::find($id);
            $update->modulename = $request->modulename;
            $update->status = $request->status;
            $update->modified_by = Auth::id();
        } else {
            $request->validate([
                'rolename' => 'required',
            ], [
                'rolename.required' => 'กรุณากรอกชื่อสิทธิ',
            ]);

            $update = Role::find($id);
            $update->rolename = $request->rolename;
            $update->status = $request->status;
            $update->modified_by = Auth::id();
        }

        try {
            $update->save();
            return redirect()->route('Role.index')->with([
                'message' => 'แก้ไขข้อมูลสำเร็จ',
                'class' => 'success',
            ]);
        } catch (\Throwable $th) {
            return redirect()->route('Role.index')->with([
                'message' => 'บันทึกข้อมูลไม่สำเร็จ',
                'class' => 'error',
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, $type)
    {
        // dd($type);
        if ($type == 1) {
            $delete = Module::findOrFail($id);
        } else {
            $delete = Role::findOrFail($id);
        }
        $delete->status = 0;
        $delete->deleted = 1;
        $delete->modified_by = Auth::id();



        try {
            $delete->save();
            return response()->json(['message' => 'ลบข้อมูลเรียบร้อยแล้ว', 'class' => 'success'], 200);
        } catch (\Throwable $th) {
            // throw $th;
            return response()->json(['message' => 'ลบข้อมูลไม่สำเร็จ', 'class' => 'error'], 200);
        }
    }
}
