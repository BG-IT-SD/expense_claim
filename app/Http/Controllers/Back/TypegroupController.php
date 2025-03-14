<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGroupTypeRequest;
use App\Models\Typegroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use function Ramsey\Uuid\v1;

class TypegroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $typegroups = Typegroup::where('deleted', '0')->get();
        return view('back.grouptypes.index', compact('typegroups'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        return view('back.grouptypes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGroupTypeRequest $request)
    {
        try {
            $typegroups = Typegroup::create($request->validated());
            return redirect()->route('Typegroup.index')->with([
                'message' => 'บันทึกข้อมูลสำเร็จ',
                'class' => 'success',
                'typegroup' => $typegroups,
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            return redirect()->route('Typegroup.index')->with([
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
    public function edit(string $id)
    {
        $typegroups = Typegroup::find($id);
        return view('back.grouptypes.create', compact('typegroups'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreGroupTypeRequest $request, string $id)
    {
        $typegroups = Typegroup::findOrFail($id);
        try {
            $typegroups->update($request->validated());
            return redirect()->route('Typegroup.index')->with([
                'message' => 'แก้ไขข้อมูลสำเร็จ',
                'class' => 'success',
                'typegroup' => $typegroups,
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            return redirect()->route('Typegroup.index')->with([
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
        $typegroups = Typegroup::findOrFail($id);
        $typegroups->status = 0;
        $typegroups->deleted = 1;
        $typegroups->modified_by = Auth::id();
        try {
            $typegroups->save();
            return response()->json(['message' => 'ลบข้อมูลเรียบร้อย', 'class' => 'success'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => 'ลบข้อมูลไม่สำเร็จ', 'class' => 'error'], 200);
        }
    }
}
