<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::where('deleted','0')->get();
        $modules = Module::where("deleted","0")->where("status", "1")->get();
        $roles = Role::where("deleted","0")->where("status", "1")->get();
        return view('back.user.index',compact('users','modules','roles'));
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //$request->except('password') password ไม่ควรถูกอัปเดตโดยไม่ได้ตั้งใจ
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
}
