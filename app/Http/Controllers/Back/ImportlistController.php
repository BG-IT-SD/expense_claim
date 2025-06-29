<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Imports\GroupSpecialImport;
use App\Models\GroupSpecial;
use App\Models\Typegroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class ImportlistController extends Controller
{
    public function index()
    {
        $typegroups = Typegroup::where([
            ['deleted', '=', '0'],
            ['status', '=', '1']
        ])->get();
        $groupspecials = GroupSpecial::with('Typegroup')->where('deleted', '0')->get();
        return view('back.importlist.index',compact('typegroups','groupspecials'));
    }

    public function importuser()
    {
        $typegroups = Typegroup::where([
            ['deleted', '=', '0'],
            ['status', '=', '1']
        ])->get();
        return view('back.importlist.import',compact('typegroups'));
    }

    public function importexcel(Request $request)
    {
            $request->validate([
                'file' => 'required|mimes:xlsx,csv|max:2048',
            ]);

            $import = new GroupSpecialImport();

            try {
                Excel::import($import, $request->file('file'));
            } catch (ValidationException $e) {
                foreach ($e->failures() as $failure) {
                    $import->importResults[] = [
                        'row'     => $failure->values(),
                        'status'  => 'error',
                        'message' => 'Row ' . $failure->row() . ': ' . implode(', ', $failure->errors())
                    ];
                }
            }

            return back()->with('importResults', $import->importResults)->with('success','Import Success');

    }

    public function delimport($id){
        $users = GroupSpecial::findOrFail($id);
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
