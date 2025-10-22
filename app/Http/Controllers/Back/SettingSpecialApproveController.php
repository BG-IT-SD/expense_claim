<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Approvespecial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingSpecialApproveController extends Controller
{
    public function index()
    {
        $data = Approvespecial::where('deleted', 0)->orderBy('id', 'desc')->get();
        return view('back.specialapprove.index', compact('data'));
    }

    public function create()
    {
        return view('back.specialapprove.create');
    }

    public function edit($id)
    {
        $item = Approvespecial::findOrFail($id);
        return view('back.specialapprove.create', compact('item'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'empid' => 'required|string|max:20|unique:approvespecial,empid,0,id,deleted,0',
            'email' => 'required|email|max:255',
            'fullname' => 'required|string|max:255',
        ]);

        Approvespecial::create([
            'empid' => $request->empid,
            'email' => $request->email,
            'fullname' => $request->fullname,
            'status' => 1,
            'deleted' => 0,
            'created_by' => Auth::id() ?? 0,
        ]);

        return redirect()->route('SpecialApprove.index')
            ->with('message', 'เพิ่มรายชื่ออนุมัติพิเศษสำเร็จ!')
            ->with('class', 'success');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'empid' => 'required|string|max:20|unique:approvespecial,empid,' . $id . ',id,deleted,0',
            'email' => 'required|email|max:255',
            'fullname' => 'required|string|max:255',
        ]);

        $item = Approvespecial::findOrFail($id);
        $item->update([
            'empid' => $request->empid,
            'email' => $request->email,
            'fullname' => $request->fullname,
            'status' => $request->status ?? 1,
            'modified_by' => Auth::id() ?? 0,
        ]);

        return redirect()->route('SpecialApprove.index')
            ->with('message', 'อัปเดตรายชื่อสำเร็จ!')
            ->with('class', 'success');
    }


    public function destroy($id)
    {
        $item = Approvespecial::findOrFail($id);
        $item->update(['deleted' => 1]);
        return redirect()->route('SpecialApprove.index')->with('success', 'ลบข้อมูลสำเร็จ');
    }
}
