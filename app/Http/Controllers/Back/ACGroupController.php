<?php


namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Accountplant;
use App\Models\Accountsetplanthead;
use App\Models\Accountstep;
use App\Models\ApproveStaff;
use App\Models\Plant;
use App\Models\Plantsettingdetail;
use App\Models\Plantsettinghead;
use App\Models\Valldataemp;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ACGroupController extends Controller
{
    public function index()
    {
        $hrgroups = Accountsetplanthead::where('deleted', 0)->get();
        return view('back.acgroup.index', compact('hrgroups'));
    }

    public function edit($id)
    {

        $hrgroupId = $id;
        $hrplants = Accountplant::with('plant')->where('headid', $id)->where('deleted', 0)->get();
        $staffapproves = Accountstep::where('headid', $id)->whereIn('step', [1, 2, 9])->where('deleted', 0)->get();
        return view('back.acgroup.edit', compact('hrplants', 'hrgroupId', 'staffapproves'));
    }

    public function addList($id)
    {

        return view('back.acgroup.addlist', compact('id'));
    }


    public function editList($id)
    {
        $approveStaff = Accountstep::findorFail($id);
        // dd($approveStaff);
        return view('back.acgroup.editlist', compact('approveStaff', 'id'));
    }


    public function addPlant($id)
    {

        // ดึง id เช็ค
        $plantedIds = Accountplant::where('deleted', 0)->pluck('plantid')->toArray();

        // เอาเฉพาะ plant ที่ยังไม่อยู่ใน รายการแต่ละกลุ่ม
        $plants = Plant::where('deleted', 0)
            ->whereNotIn('id', $plantedIds)
            ->get();
        $action = 'Add';

        return view('back.acgroup.addplant', compact('id', 'plants','action'));
    }

    public function editPlant($id){
        // ดึง id เช็ค
        $plantedIds = Accountplant::where('deleted', 0)->pluck('plantid')->toArray();

        // เอาเฉพาะ plant ที่ยังไม่อยู่ใน รายการแต่ละกลุ่ม
        $plants = Plant::where('deleted', 0)
            ->whereNotIn('id', $plantedIds)
            ->get();

        $plantToGroup = Accountplant::findorfail($id);
        // dd($plantToGroup);

        $action = 'Edit';
        return view('back.acgroup.addplant', compact('id', 'plants','action','plantToGroup'));
    }

    public function SavePlant(Request $request)
    {
      $request->validate([
            'plant' => 'required',
            'groupid' => 'required',
            'action' => 'required|in:Add,Edit',
        ]);

        $groupID = $request->input('groupid');
        $action = $request->input('action');
        $pageID = $groupID; // default

        try {
            if ($action === 'Add') {
                $create = new Accountplant;
                $create->headid = $groupID;
                $create->plantid = $request->input('plant');
                $create->save();
            } elseif ($action === 'Edit') {
                $update = Accountplant::findOrFail($groupID);
                $update->plantid = $request->input('plant');
                $update->save();
                $pageID = $update->headid;
            }

            return redirect()
                ->route('ACgroup.edit', $pageID)
                ->with(['message' => 'บันทึกสำเร็จ', 'class' => 'success']);
        } catch (\Throwable $th) {
            return redirect()
                ->route('ACgroup.edit', $pageID)
                ->with(['message' => 'บันทึกไม่สำเร็จ', 'class' => 'error']);
        }
    }


    public function SaveList(Request $request)
    {
        $request->validate([
            'emp_data' => 'required',
            'name_data' => 'required',
            'groupid' => 'required',
        ]);

        $groupID = isset($request->groupid) ? $request->groupid : null;

        if ($groupID != null) {
            try {
                $create = new Accountstep();
                $create->step = 1;
                $create->headid = $groupID;
                $create->empid = $request->emp_data;
                $create->email = $request->email_data;
                $create->fullname = $request->name_data;
                $create->save();

                return redirect()->route('ACgroup.edit', $groupID)->with(['message' => 'บันทึกสำเร็จ', 'class' => 'success']);
            } catch (\Throwable $th) {
                //throw $th;
                return redirect()->route('ACgroup.edit', $groupID)->with(['message' => 'บันทึกไม่สำเร็จ' . $th, 'class' => 'error']);
            }
        }
    }

    public function UpdateList(Request $request)
    {
        $id = $request->input('id', '');
        $empid = $request->input('head_id', '');
        $empname = $request->input('head_name', '');
        $email = $request->input('head_email', '');
        $status = $request->input('status', '');
        $groupID = $request->input('groupid', '');
        $step = $request->input('step', '');

        if ($id != "") {

            $update = Accountstep::findOrFail($id);

                if ($empid != "") {

                    $update->empid = $empid;
                    $update->email = $email;
                    $update->fullname = $empname; // แก้จาก fuulname เป็น fullname
                }

                if ($status != "") {
                    $update->status = $status;
                }



            try {
                $update->save();

                return redirect()->route('ACgroup.edit', $groupID)->with(['message' => 'บันทึกสำเร็จ', 'class' => 'success']);
            } catch (\Throwable $th) {
                return redirect()->route('ACgroup.edit', $groupID)->with(['message' => 'บันทึกไม่สำเร็จ' . $th, 'class' => 'error']);
                // $th->getMessage()
            }
        }
        return redirect()->route('ACgroup.edit', $groupID)->with(['message' => 'ไม่พบข้อมูลสำหรับอัปเดต', 'class' => 'error']);
    }

    public function delPlant($id){

        if($id != ""){

            $delete = Accountplant::findorFail($id);
            $delete->deleted = 1;
            $delete->status = 0;

            try {
                $delete->save();
                return response()->json(['message' => 'ลบข้อมูลเรียบร้อย', 'class' => 'success'], 200);
            } catch (\Throwable $th) {
                //throw $th;
                return response()->json(['message' => 'ลบข้อมูลไม่สำเร็จ', 'class' => 'error'], 200);
            }
        }
    }


    public function CheckEmpID(Request $request)
    {
        $request->validate([
            'empid' => [
                'required',
                Rule::unique('accountstep')->where(function ($query) {
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

    public function ListEmpHrms(Request $request)
    {

        $group = $request->input('group');
        $step = $request->input('step');

        $approveStaff = Accountstep::where('deleted', 0)->where('headid', $group)->where('step', $step)->get();
        $approveStaffData = $approveStaff?->pluck('empid')
            ->filter()
            ->unique()
            ->values();
        // dd($approveStaffData);
        $keyword = $request->input('sKeyword');
        $page = $request->input('page', 1);
        $limit = 5;

        $query = Valldataemp::query();

        if ($keyword) {
            $query->where('EMAIL', 'like', "%$keyword%");
        }

        $query->where('status', 1)
            ->where('deleted', 0)
            ->whereNotIn('CODEMPID', $approveStaffData)
            // ->whereNotIn('CODEMPID', ['1234', '41000014', '23000033', ])
            ->where('STAEMP', '!=', 9);
        // ->where('numlvl', '>=', 7);

        $total = $query->count();

        $results = $query->skip(($page - 1) * $limit)
            ->take($limit)
            ->get(['CODEMPID', 'EMAIL', 'NAMFIRSTT', 'NAMLASTT']) // ดึงหลาย field
            ->map(function ($item) {
                return [
                    'id' => $item->CODEMPID,
                    'text' => "{$item->EMAIL} | {$item->NAMFIRSTT} {$item->NAMLASTT}"
                ];
            });

        return response()->json([
            'data' => $results,
            'total_count' => $total,
        ]);
    }

    public function getEmpData(Request $request)
    {
        $empid = $request->query('emid'); // รับ emid จาก query string

        $data = Valldataemp::where('CODEMPID', $empid)
            ->where('status', 1)
            ->where('deleted', 0)
            ->whereNotIn('CODEMPID', ['1234', '41000014', '23000033',])
            ->where('STAEMP', '!=', 9)
            ->first();

        // ถ้าไม่เจอข้อมูลเลย
        if (!$data) {
            return response()->json([
                'message' => 'ไม่พบข้อมูล',
            ], 404);
        }

        return response()->json([
            'Idemp' => $data->CODEMPID,
            'Emailemp' => $data->EMAIL,
            'Nameemp' => $data->NAMFIRSTT . ' ' . $data->NAMLASTT,
        ]);
    }
}

