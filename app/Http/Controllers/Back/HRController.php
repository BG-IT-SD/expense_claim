<?php

namespace App\Http\Controllers\Back;

use App\Helpers\MailHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Expense;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Mail;
use App\Mail\ApproveNotification;
use App\Mail\TestMail;
use App\Models\Approve;
use App\Models\ApproveStaff;
use App\Models\ExpenseFile;
use App\Models\ExpenseFood;
use App\Models\Fuelprice;
use App\Models\Groupplant;
use App\Models\Passenger;
use App\Models\Plant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HRController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $expenses = Expense::with(['latestApprove', 'vbooking', 'user','tech'])
            ->whereHas('latestApprove', function ($query) {
                $query->whereIn('typeapprove', [1,3]);
                // ->where('statusapprove', 1);
            })
            ->get();

        // dd($expenses);

        return view('back.hr.list', compact('expenses'));
    }

    public function history()
    {
        $expenses = Expense::with(['latestApprove', 'vbooking', 'user','tech'])
            ->whereHas('latestApprove', function ($query) {
                $query->whereIn('typeapprove', [4, 5]);
                // ->where('statusapprove', 1);
            })
            ->get();

        return view('back.hr.approved', compact('expenses'));
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
    public function edit($id, $type = null)
    {
        // ส่งตัวแปรบอกว่าเป็นหน้า edit
        $isView = 1;
        if ($type != null) {
            $isView = 0;
        } else {
            $isView = 1;
        }

        $finalHEmail = '';
        $finalHName = '';
        $finalId = '';

        $finalHEmailNext = '';
        $finalHNameNext = '';
        $finalIdNext = '';

        $expense = Expense::with(['vbooking', 'user'])->findOrFail($id);
        // Plant
        $plants = Plant::where('status', 1)->where('deleted', 0)
            ->get();
        $departure_date = $expense->vbooking->departure_date
            ? Carbon::parse("{$expense->vbooking->departure_date} {$expense->vbooking->departure_time}")->format('d/m/Y H:i')
            : null;

        $return_date = $expense->vbooking->return_date
            ? Carbon::parse("{$expense->vbooking->return_date} {$expense->vbooking->return_time}")->format('d/m/Y H:i')
            : null;

        $empid = $expense->empid;
        $bu = BuEmp($empid);
        $level = 1;
        $empLevel = LevelEmp($empid);
        // dd($empLevel);
        if ($empLevel <= 7) {
            $level = 1;
        } else {
            $level = 2;
        }

        $startDate = Carbon::parse($expense->vbooking->departure_date);
        $endDate = Carbon::parse($expense->vbooking->return_date);
        $startTime = Carbon::parse($expense->vbooking->departure_time);
        $endTime = Carbon::parse($expense->vbooking->return_time);

        $Alldayfood = CarbonPeriod::create($startDate, '1 day', $endDate);
        $expenseFoods = ExpenseFood::where('exid', $expense->id)->get()->keyBy('used_date');
        $approvals = Approve::where('exid', $expense->id)
            ->where('deleted', 0)
            ->where('status', 1)
            ->orderBy('id')
            ->get();

        $files = ExpenseFile::where('exid', $expense->id)
            ->where('deleted', 0)
            ->where('status', 1)
            ->get();

        // Food
        $groupplant = Groupplant::with([
            'plant',
            'meal.group',
            'meal',
        ])
            ->where('deleted', 0)
            ->whereHas('plant', function ($query) use ($bu) {
                $query->where('plantname', $bu);
            })
            ->whereHas('meal.group', function ($query) use ($level) {
                $query->where('levelid', $level);
            })
            ->first();

        $reasons = ['อบรม', 'สัมมนา', 'ฝึกงาน', 'ติดตั้งเครื่องจักร', 'ลูกค้าร้องเรียน', 'พบลูกค้า', 'อื่นๆ'];
        // ราคาน้ำมัน
        $ratefuels = Fuelprice::where("status", 1)->where("deleted", 0)->orderByDesc('startrate')->get();

        $extype = $expense->extype;

        // คนตรวจสอบ
        $finalHEmail = Auth::user()->email;
        $finalHName = Auth::user()->fullname;
        $finalId = Auth::user()->empid;
        // ลำดับถัดไป
        $nextStepApprove = Approvestep($bu, 1, 1);
        // dd($nextStepApprove);
        // $finalHEmailNext = $nextStepApprove["email"];
        // $finalHNameNext = $nextStepApprove["fullname"];
        // $finalIdNext = $nextStepApprove["empid"];

        $finalHEmailNext = 'Kamolwan.b@bgiglass.com';
        $finalHNameNext = 'กมลวรรณ บรรชา';
        $finalIdNext = '66000510';

        // $finalHEmailNext = 'Saowapha.K@bgiglass.com';
        // $finalHNameNext = 'เสาวภา เข็มเหลือง';
        // $finalIdNext = '63000455';

        return view('back.hr.frmapprovegrp', compact(['expense', 'empid', 'reasons', 'departure_date', 'return_date', 'plants', 'ratefuels', 'Alldayfood', 'expenseFoods', 'groupplant', 'approvals', 'files', 'isView', 'startDate', 'endDate', 'startTime', 'endTime', 'bu', 'finalHEmail', 'finalHName', 'finalId', 'finalHEmailNext', 'finalHNameNext', 'finalIdNext']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([

            'costoffood' => 'required',
            'travelexpenses' => 'required',
            'gasolinecost' => 'required',
            'totalExpense' => 'required',
            // 'files.*' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,pdf',

        ]);

        try {
            DB::beginTransaction();

            $update = Expense::find($id);
            if ($update) {
                $update->update([
                    'returntime' => $request->returntime,
                    'publictransportfare' => $request->publictransportfare ?? 0,
                    'expresswaytoll' => $request->expresswaytoll ?? 0,
                    'otherexpenses' => $request->otherexpenses ?? 0,
                    'costoffood' => $request->costoffood,
                    'travelexpenses' => $request->travelexpenses,
                    'gasolinecost' => $request->gasolinecost,
                    'totalprice' => $request->totalExpense,
                ]);
            }

            // มื้ออาหาร
            foreach ($request->days as $day) {
                $expenseFood = ExpenseFood::where('exid', $id)
                    ->where('used_date', $day['date'])
                    ->first();

                if ($expenseFood) {
                    $expenseFood->meal1 = floatval($day['meal1'][0] ?? 0);
                    $expenseFood->meal2 = floatval($day['meal2'][0] ?? 0);
                    $expenseFood->meal3 = floatval($day['meal3'][0] ?? 0);
                    $expenseFood->meal4 = floatval($day['meal4'][0] ?? 0);

                    $expenseFood->meal1reject = in_array("1", $day['mealx1'] ?? []) ? 1 : 0;
                    $expenseFood->meal2reject = in_array("1", $day['mealx2'] ?? []) ? 1 : 0;
                    $expenseFood->meal3reject = in_array("1", $day['mealx3'] ?? []) ? 1 : 0;
                    $expenseFood->meal4reject = in_array("1", $day['mealx4'] ?? []) ? 1 : 0;

                    $expenseFood->totalprice = floatval($day['totalprice'] ?? 0);
                    $expenseFood->totalpricebf = floatval($day['totalpricebf'] ?? 0);
                    $expenseFood->totalreject = intval($day['totalreject'] ?? 0);

                    $expenseFood->save();
                }
            }

            //  End มื้ออาหาร
            // บันทึก Approve
            $token = Str::random(64);
            $token2 = Str::random(64);
            $approve = Approve::create([
                'exid' => $id,
                'typeapprove' => 3, //ประเภทที่ 3 Hr ตรวจสอบ
                'empid' => $request->head_id,
                'email' => $request->head_email ?? '',
                'approvename' => $request->head_name ?? '',
                'emailstatus' => 1,
                'statusapprove' => 1,
                'login_token' => $token,
                'token_expires_at' => now()->addDays(10),
            ]);

            $approve_nextstep = Approve::create([
                'exid' => $id,
                'typeapprove' => 4, //ประเภทที่ 4 Hr อนุมัติจากผู้จัดการส่วนHR
                'empid' => $request->nexthead_id,
                'email' => $request->nexthead_email ?? '',
                'approvename' => $request->nexthead_name ?? '',
                'emailstatus' => 1,
                'statusapprove' => 0,
                'login_token' => $token2,
                'token_expires_at' => now()->addDays(10),
            ]);

            // End บันทึก Approve
            DB::commit();
            // Sent Mail
            $link = route('approve.magic.login', ['token' => $token2]);
            $data = [
                'type' => 1,
                'title' => 'แจ้งเตือนการอนุมัติการเบิกเบี้ยเลี้ยง',
                'name' => $request->nexthead_name,
                'full_name' => $request->empfullname,
                'check_hr' => $request->head_name,
                'departuredate' => $request->departuredatemail ?? '',
                'link' => $link,
            ];

            MailHelper::sendExternalMail(
                $request->nexthead_email,
                'อนุมัติการเบิกเบี้ยเลี้ยง',
                'mails.hrapprove', // ชื่อ blade view
                $data,
                'Expense Claim System EX' . $id,
            );
            //End Sent Mail

            return response()->json([
                'status' => 200,
                'message' => 'ตรวจสอบข้อมูลเรียบร้อยแล้ว',
                'expense_id' => $id,
                'class' => 'success'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 500,
                'message' => 'เกิดข้อผิดพลาดระหว่างการบันทึกข้อมูล',
                'error' => $e->getMessage(),
                'class' => 'error'
            ], 500);
        }
    }

    public function reject(Request $request)
    { {
            $request->validate([
                'rejectremark' => 'required',
                'rejectidexpense' => 'required'
            ]);

            $id = $request->rejectidexpense ?? "";
            $empfullname = $request->empfullname ?? "";
            $departuredaterj = $request->departuredaterj ?? "";
            $empemailrj = $request->empemailrj ?? "";

            if ($id != "") {
                $approve = Approve::create([
                    'exid' => $id,
                    'typeapprove' => 3,
                    'empid' => $request->head_idrj,
                    'email' => $request->head_emailrj ?? '',
                    'approvename' => $request->head_namerj ?? '',
                    'emailstatus' => 1,
                    'statusapprove' => 2,
                    'remark' => $request->rejectremark,
                ]);

                if ($approve) {
                    // ส่งเมลเฉพาะกรณี reject
                    $data = [
                        'headname' => $request->head_namerj ?? '', // คนที่ reject
                        'name' => $empfullname, // user
                        'expenseid' => $approve->exid, //exid
                        'departuredate' => $departuredaterj,
                        'remark' => $request->rejectremark,
                    ];

                    MailHelper::sendExternalMail(
                        $empemailrj, // ผู้รับ คือ ผู้ขอเบิก
                        'แจ้งผลการไม่อนุมัติการเบิกเบี้ยเลี้ยง',
                        'mails.reject', // ชื่อ blade view mail
                        $data,
                        'Expense Claim System EX' . $id,
                    );

                    return response()->json([
                        'status' => 200,
                        'message' => 'ยกเลิกข้อมูลเรียบร้อยแล้ว',
                        'expense_id' => $id,
                        'class' => 'success'
                    ]);
                } else {
                    return response()->json([
                        'status' => 500,
                        'message' => 'เกิดข้อผิดพลาดระหว่างการบันทึกข้อมูล',
                        'class' => 'error'
                    ], 500);
                }
            } else {
                return response()->json([
                    'status' => 400,
                    'message' => 'ไม่พบรหัสอ้างอิงรายการ',
                    'class' => 'warning'
                ], 400);
            }
        }
    }
    /**
     * Remove the specified resource from storage.
     */

    public function destroy(string $id)
    {
        //
    }


    public function showPassengerList($bookid)
    {
        try {
            $passengers = Passenger::where('booking_id', $bookid)->get();

            foreach ($passengers as $p) {
                // ดึงข้อมูล expense โดยเชื่อมด้วย bookid และ empid
                $expense = Expense::where('bookid', $p->booking_id)
                    ->where('empid', $p->passenger_empid)
                    ->first();

                $p->expense = $expense;

                // ถ้ามี expense แล้ว ค่อยดึง approve
                if ($expense) {
                    $lastApprove = Approve::where('exid', $expense->id)
                        ->orderByDesc('id')
                        ->first();

                    if ($lastApprove) {
                        // เพิ่ม text หลังจากกำหนด lastapprove แล้ว
                        $lastApprove->status_text = status_approve_badge(
                            $lastApprove->statusapprove,
                            $lastApprove->typeapprove
                        );

                        $lastApprove->type_text = type_approve_text(
                            $lastApprove->typeapprove
                        );

                        $p->expense->lastapprove = $lastApprove;
                    }
                }

            }


            return response()->json($passengers);
        } catch (\Exception $e) {
            // \Log::error('Error fetching passenger list: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
