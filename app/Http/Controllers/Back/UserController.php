<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Role;
use App\Models\Sigfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::where('deleted', '0')->get();
        $modules = Module::where("deleted", "0")->where("status", "1")->get();
        $roles = Role::where("deleted", "0")->where("status", "1")->get();
        return view('back.user.index', compact('users', 'modules', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('back.user.create');
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
        $users = User::with('sigfile')->findOrFail($id);
        // dd($users);
        return view('back.user.edit', compact('users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'status' => 'required'
        ], [
            'status.required' => 'กรุณาเลือก Status'
        ]);

        $users = User::findOrFail($id);

        try {
            $users->update($validatedData);
            return redirect()->route('User.index')->with([
                'message' => 'แก้ไขข้อมูลสำเร็จ',
                'class' => 'success',
                'typegroup' => $users,
            ]);
        } catch (\Throwable $th) {
            return redirect()->route('User.index')->with([
                'message' => 'แก้ไขข้อมูลไม่สำเร็จ',
                'class' => 'error',
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $users = User::findOrFail($id);
        $users->status = 0;
        $users->deleted = 1;
        $users->modified_by = Auth::id();
        try {
            $users->save();
            return response()->json(['message' => 'ลบข้อมูลเรียบร้อย', 'class' => 'success'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => 'ลบข้อมูลไม่สำเร็จ', 'class' => 'error'], 200);
        }
    }

    public function ResetPassword(Request $request, $id)
    {
        $empid = $request->empid ?? "";
        if ($empid != "") {
            $reset = User::findOrFail($id);
            $reset->password = Hash::make($empid);
            $reset->modified_by = Auth::id();

            try {
                $reset->save();
                return response()->json(['message' => 'แก้ไขข้อมูลเรียบร้อย', 'class' => 'success'], 200);
            } catch (\Throwable $th) {
                //throw $th;
                return response()->json(['message' => 'แก้ไขข้อมูลไม่สำเร็จ', 'class' => 'error'], 200);
            }
        }else{
            return response()->json(['message' => 'แก้ไขข้อมูลไม่สำเร็จ', 'class' => 'error'], 200);
        }
    }

    public function uploadSignature(Request $request)
    {
        $request->validate([
            'empid' => 'required',
            'sigfile' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $file = $request->file('sigfile');
        $filename = uniqid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('images/signatures', $filename, 'public');

        Sigfile::updateOrCreate(
            ['empid' => $request->empid], // ค้นหา record นี้
            [
                'path' => $path,
                'etc' => $file->getClientOriginalName(),
            ]
        );

        return response()->json(['message' => 'อัปโหลดลายเซ็นเรียบร้อย']);
    }



}
