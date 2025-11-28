<?php

namespace App\Http\Controllers\Back;

use App\Helpers\MailHelper;
use App\Http\Controllers\Controller;
use App\Models\Accountstep;
use App\Models\Approve;
use App\Models\ApproveStaff;
use App\Models\Exgroup;
use App\Models\Expense;
use App\Models\Plant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $plantNames = '';
        $empid = Auth::user()->empid;
        // พนักงานที่ login และโรงงานที่เขาดูแล
        $staff = Accountstep::with(['plantSettingDetails.plant'])
            ->where('empid', $empid)
            ->where('deleted', 0)
            ->where('step', 1)
            ->first();

        $plantNames = $staff?->plantSettingDetails
            ->pluck('plant.plantname', 'plant.id')
            ->filter()
            ->map(function ($name, $id) {
                return ['id' => $id, 'name' => $name];
            })
            ->values();

        // dd($plantNames);

        // $plantIds = $plantNames->pluck('id')->toArray();
        $plantIds = ($plantNames ?? collect())->pluck('id')->toArray();

        $exgroups = Exgroup::where('deleted', 0)
            ->where('typeapprove', 6)
            ->where('statusapprove', 0)
            ->whereIn('plantid', $plantIds)
            ->when(
                $request->filled('exdate'),
                fn($q) =>
                $q->whereDate('groupdate', '>=', $request->exdate)
            )
            ->when(
                $request->filled('end_exdate'),
                fn($q) =>
                $q->whereDate('groupdate', '<=', $request->end_exdate)
            )
            // ถ้าไม่กรอกวันที่เลย ให้ default ย้อนหลัง 1 เดือน
            ->when(
                !$request->filled('exdate') && !$request->filled('end_exdate'),
                fn($q) =>
                $q->whereDate('groupdate', '>=', now()->subMonth()->startOfDay())
            )
            ->orderByDesc('id')
            ->get();

        $plants = Plant::where('status', 1)->where('deleted', 0)->get();

        return view('back.account.index', compact('exgroups', 'plants', 'plantNames'));
    }

    public function manage($id)
    {
        $expenses = Expense::with(['vbooking', 'user', 'tech', 'userhr'])
            ->where('exgroup', $id)
            ->get();
        // dd($expenses);
        $exgroup = Exgroup::findOrFail($id);
        $approvename = Auth::user()->fullname;
        $approveempid = Auth::user()->empid;
        $approveemail = Auth::user()->email;

        return view('back.account.manage', compact('expenses', 'exgroup', 'approvename', 'approveempid', 'approveemail'));
    }

    public function view($id)
    {
        $expenses = Expense::with(['vbooking', 'user', 'tech', 'userhr'])
            ->where('exgroup', $id)
            ->get();
        $exgroup = Exgroup::findOrFail($id);

        return view('back.account.view', compact('expenses', 'exgroup'));
    }



    // public function saveExgroupApproval(Request $request)
    // {

    //     dd($request->all());

    //     $exgroupId = intval($request->input('exgroup_id'));

    //     $exgroup = Exgroup::where('id', $exgroupId)
    //         ->where('statusapprove', '!=', 1)
    //         ->first();

    //     if (!$exgroup) {
    //         return redirect()->route('Account.index')->with([
    //             'message' => 'รายการนี้ถูกอนุมัติแล้ว',
    //             'class' => 'warning'
    //         ]);
    //     }

    //     DB::beginTransaction();
    //     try {
    //         // loop อัปเดต statusapprove และ reason
    //         $expenseIds = $request->input('expense_id', []);
    //         $statuses = $request->input('statsapprove', []);
    //         $reasons = $request->input('txtreason', []);
    //         $fullnames = $request->input('fullname', []);
    //         $accountempid = $request->input('accountempid');
    //         $accountemail = $request->input('accountemail');
    //         $paymentdate = $request->input('paymentdate');

    //         foreach ($expenseIds as $index => $exid) {
    //             $status = $statuses[$index];
    //             $reason = $reasons[$index] ?? null;
    //             $fullname = $fullnames[$index] ?? '';

    //             $approve = Approve::where('exid', $exid)->where('typeapprove', 6)->first();
    //             if ($approve) {
    //                 $expense_data = $approve->expense;
    //                 $totalprice =  $approve->expense->totalprice;
    //                 $exdate =  $expense_data->vbooking->departure_date;
    //                 $pricedate = $exgroup->groupdate;
    //                 $Tomail = EmailEmp($approve->expense->empid) ?? '';
    //                 $approve->statusapprove = $status;
    //                 $approve->remark = $reason;
    //                 $approve->save();
    //                 if ($Tomail != '') {
    //                     if ($status == 1) {
    //                         // ส่ง mail ว่า success
    //                         $data = [
    //                             'name' => $fullname, // user
    //                             'price' => $totalprice,
    //                             'pricedate' => $paymentdate,
    //                             'expenseid' => $approve->exid, //exid
    //                         ];

    //                         MailHelper::sendExternalMail(
    //                             $Tomail, // ผู้รับ
    //                             'แจ้งยอดการเบิกเบี้ยเลี้ยงปฎิบัติงานนอกสถานที่',
    //                             'mails.accountapporve', // ชื่อ blade view mail
    //                             $data,
    //                             'Expense Claim System EX' . $exid,
    //                         );
    //                     } elseif ($status == 2 || $status == 9) {
    //                         $textreject = $status == 2 ? 'ไม่ผ่านการอนุมัติ' : 'ติดสถานะHold';
    //                         // ส่ง mail ว่า reject หรือ Hold
    //                         $data = [
    //                             'name' => $fullname, // user
    //                             'text' => $textreject,
    //                             'expenseid' => $approve->exid, //exid
    //                             'exdate' => $exdate,
    //                             'remark' => $reason,
    //                         ];
    //                         // $Tomail
    //                         MailHelper::sendExternalMail(
    //                             $Tomail, // ผู้รับ
    //                             'แจ้งผลการเบิกเบี้ยเลี้ยงปฎิบัติงานนอกสถานที่จากบัญชี',
    //                             'mails.accounthold', // ชื่อ blade view mail
    //                             $data,
    //                             'Expense Claim System EX' . $exid,
    //                         );
    //                     }
    //                 }
    //             }
    //         }
    //         // อัปเดตยอดสุทธิใน exgroup
    //         $exgroup->nettotalfood = $this->cleanNumber($request->input('nettotalfood'));
    //         $exgroup->nettotalfuel = $this->cleanNumber($request->input('nettotalfuel'));
    //         $exgroup->netexpresswaytoll = $this->cleanNumber($request->input('netexpresswaytoll'));
    //         $exgroup->netpublictransportfare = $this->cleanNumber($request->input('netpublictransportfare'));
    //         $exgroup->netotherexpenses = $this->cleanNumber($request->input('netotherexpenses'));
    //         $exgroup->nettotalother = $this->cleanNumber($request->input('nettotalother'));
    //         $exgroup->nettotal = $this->cleanNumber($request->input('nettotal'));
    //         $exgroup->accountempid = $accountempid; // บัญชีคนอนุมัติ
    //         $exgroup->accountemail = $accountemail; // emailบัญชีคนอนุมัติ
    //         $exgroup->statusapprove = 1; // set เป็นอนุมัติ
    //         $exgroup->paymentdate = $paymentdate; //วันที่จ่าย
    //         $exgroup->save();

    //         DB::commit();

    //         $logData = [
    //             'exgroup'      => $exgroup->toArray(),
    //             'expense_ids'  => $expenseIds,
    //             'approves'     => Approve::whereIn('exid', $expenseIds)->where('typeapprove', 6)->get()->toArray(),
    //             'statuses'     => $statuses,
    //             'reasons'      => $reasons,
    //             'accountempid' => $accountempid,
    //             'accountemail' => $accountemail,
    //             'paymentdate'  => $paymentdate,
    //         ];
    //         logAction(
    //             'update',
    //             'Exgroup',
    //             'บันทึกผลอนุมัติกลุ่ม EXGROUP-' . $exgroup->id,
    //             json_encode($logData)
    //         );

    //         return redirect()->route('Account.index')->with(['message' => 'บันทึกผลอนุมัติเรียบร้อย', 'class' => 'success']);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return redirect()->route('Account.index')->with(['message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage(), 'class' => 'danger']);
    //     }
    // }

    public function saveExgroupApproval(Request $request)
    {
        // ถ้าจะใช้ validate ด้วย เอา rules กลับมาแบบนี้
        $request->validate([
            'expense_id'              => 'required|array',
            'statsapprove'            => 'required|array',
            'txtreason'               => 'nullable|array',
            'nettotalfood'            => 'required',
            'nettotalfuel'            => 'required',
            'netexpresswaytoll'       => 'required',
            'netpublictransportfare'  => 'required',
            'netotherexpenses'        => 'required',
            'nettotalother'           => 'required',
            'nettotal'                => 'required',
        ]);

        // dd($request->all());

        $exgroupId = (int) $request->input('exgroup_id');

        $exgroup = Exgroup::where('id', $exgroupId)
            ->where('statusapprove', '!=', 1)
            ->first();

        if (!$exgroup) {
            return redirect()->route('Account.index')->with([
                'message' => 'รายการนี้ถูกอนุมัติแล้ว',
                'class'   => 'warning',
            ]);
        }

        DB::beginTransaction();

        try {
            // ดึงค่าจากฟอร์ม
            $expenseIds     = $request->input('expense_id', []);
            $statuses       = $request->input('statsapprove', []);
            $reasons        = $request->input('txtreason', []);
            $accountempid   = $request->input('accountempid');
            $accountemail   = $request->input('accountemail');
            $paymentdate    = $request->input('paymentdate');

            // preload approve + expense + user/tech ทีเดียว ลด N+1 และลดเวลา
            $approves = Approve::with(['expense.user', 'expense.tech', 'expense.vbooking'])
                ->where('typeapprove', 6)
                ->whereIn('exid', $expenseIds)
                ->get()
                ->keyBy('exid'); // key เป็น exid ไว้หาเร็ว ๆ

            foreach ($expenseIds as $index => $exid) {
                $status = $statuses[$index] ?? null;
                $reason = $reasons[$index] ?? null;

                /** @var \App\Models\Approve|null $approve */
                $approve = $approves->get($exid);

                if (!$approve) {
                    // \Log::warning('Approve not found for exid when saving exgroup approval', [
                    //     'exid'   => $exid,
                    //     'index'  => $index,
                    // ]);
                    continue;
                }

                $expense = $approve->expense;

                // หา fullname จากฐานข้อมูล ไม่ใช้ input form แล้ว
                $fullname = '';

                if ($expense) {
                    if (in_array($expense->extype, [2, 3])) {
                        // technician
                        $fullname = optional($expense->tech)->fullname;
                    } else {
                        // employee
                        $fullname = optional($expense->user)->fullname;
                    }
                }

                $fullname = $fullname ?? '';

                // ข้อมูลที่ใช้ในเมล
                $totalprice = $expense->totalprice ?? 0;
                $exdate     = optional(optional($expense)->vbooking)->departure_date;
                $Tomail     = $expense ? (EmailEmp($expense->empid) ?? '') : '';

                // อัปเดต approve
                $approve->statusapprove = $status;
                $approve->remark        = $reason;
                $approve->save();

                // ถ้าไม่มีเมล ก็ไม่ต้องส่ง
                if ($Tomail === '' || !$status) {
                    continue;
                }

                if ((int)$status === 1) {
                    //สถานะอนุมัติ — ส่งเมลแจ้งยอดจ่าย
                    $data = [
                        'name'      => $fullname,
                        'price'     => $totalprice,
                        'pricedate' => $paymentdate,
                        'expenseid' => $approve->exid,
                    ];

                    MailHelper::sendExternalMail(
                        $Tomail,
                        'แจ้งยอดการเบิกเบี้ยเลี้ยงปฎิบัติงานนอกสถานที่',
                        'mails.accountapporve',
                        $data,
                        'Expense Claim System EX' . $exid
                    );
                } elseif ((int)$status === 2 || (int)$status === 9) {
                    // สถานะ Reject หรือ Hold — ส่งเมลแจ้งผล
                    $textreject = (int)$status === 2 ? 'ไม่ผ่านการอนุมัติ' : 'ติดสถานะHold';

                    $data = [
                        'name'      => $fullname,
                        'text'      => $textreject,
                        'expenseid' => $approve->exid,
                        'exdate'    => $exdate,
                        'remark'    => $reason,
                    ];

                    MailHelper::sendExternalMail(
                        $Tomail,
                        'แจ้งผลการเบิกเบี้ยเลี้ยงปฎิบัติงานนอกสถานที่จากบัญชี',
                        'mails.accounthold',
                        $data,
                        'Expense Claim System EX' . $exid
                    );
                }
            }

            //อัปเดตยอดสุทธิใน exgroup
            $exgroup->nettotalfood           = $this->cleanNumber($request->input('nettotalfood'));
            $exgroup->nettotalfuel           = $this->cleanNumber($request->input('nettotalfuel'));
            $exgroup->netexpresswaytoll      = $this->cleanNumber($request->input('netexpresswaytoll'));
            $exgroup->netpublictransportfare = $this->cleanNumber($request->input('netpublictransportfare'));
            $exgroup->netotherexpenses       = $this->cleanNumber($request->input('netotherexpenses'));
            $exgroup->nettotalother          = $this->cleanNumber($request->input('nettotalother'));
            $exgroup->nettotal               = $this->cleanNumber($request->input('nettotal'));
            $exgroup->accountempid           = $accountempid;
            $exgroup->accountemail           = $accountemail;
            $exgroup->statusapprove          = 1;
            $exgroup->paymentdate            = $paymentdate;
            $exgroup->save();

            DB::commit();

            // log ไว้เหมือนเดิม
            $logData = [
                'exgroup'      => $exgroup->toArray(),
                'expense_ids'  => $expenseIds,
                'approves'     => Approve::whereIn('exid', $expenseIds)->where('typeapprove', 6)->get()->toArray(),
                'statuses'     => $statuses,
                'reasons'      => $reasons,
                'accountempid' => $accountempid,
                'accountemail' => $accountemail,
                'paymentdate'  => $paymentdate,
            ];

            logAction(
                'update',
                'Exgroup',
                'บันทึกผลอนุมัติกลุ่ม EXGROUP-' . $exgroup->id,
                json_encode($logData)
            );

            return redirect()->route('Account.index')->with([
                'message' => 'บันทึกผลอนุมัติเรียบร้อย',
                'class'   => 'success',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            // \Log::error('saveExgroupApproval error', [
            //     'message' => $e->getMessage(),
            //     'trace'   => $e->getTraceAsString(),
            // ]);

            return redirect()->route('Account.index')->with([
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage(),
                'class'   => 'danger',
            ]);
        }
    }


    private function cleanNumber($value)
    {
        return floatval(str_replace(',', '', $value));
    }


    public function ListHold()
    {

        $expenses = Expense::with(['latestApprove', 'vbooking', 'user', 'tech'])
            ->whereHas('latestApprove', function ($query) {
                $query->where('typeapprove', 6)
                    ->where('statusapprove', 9);
            })
            ->whereIn('extype', [1, 2, 3])
            ->get();

        $page = 'HeadApprove.show';

        return view('back.account.listhold', compact('expenses', 'page'));
    }

    public function ListApproved(Request $request)
    {

        $exgroups = Exgroup::where('deleted', 0)
            ->where('typeapprove', 6)
            // ->where('statusapprove',0)
            ->whereIn('statusapprove', [1, 2])
            ->when(
                $request->filled('exdate'),
                fn($q) =>
                $q->whereDate('paymentdate', '>=', $request->exdate)
            )
            ->when(
                $request->filled('end_exdate'),
                fn($q) =>
                $q->whereDate('paymentdate', '<=', $request->end_exdate)
            )
            // ถ้าไม่กรอกวันที่เลย ให้ default ย้อนหลัง 1 เดือน
            ->when(
                !$request->filled('exdate') && !$request->filled('end_exdate'),
                fn($q) =>
                $q->whereDate('paymentdate', '>=', now()->subMonth()->startOfDay())
            )
            ->orderByDesc('id')
            ->get();
        return view('back.account.listapprove', compact('exgroups'));
    }



    public function confirmHold(Request $request)
    {
        $request->validate([
            'expense_ids' => 'required|array',
            'costoffood' => 'required|array',
            'gasolinecost' => 'required|array',
            'expresswaytoll' => 'required|array',
            'publictransportfare' => 'required|array',
            'otherexpenses' => 'required|array',
            'totalprice' => 'required|array',
        ]);

        DB::beginTransaction();

        try {
            $paymentdate = $request->paymentdate;
            foreach ($request->expense_ids as $index => $expenseId) {
                $expense = Expense::find($expenseId);
                if (!$expense || $expense->latestApprove->statusapprove != 9) continue; // 9 = Hold

                //อัปเดตสถานะ approve
                $expense->latestApprove->statusapprove = 1; // approved
                $expense->latestApprove->save();

                //เพิ่มยอด Net เข้า exgroups
                $exgroup = Exgroup::find($expense->exgroup);
                if ($exgroup) {
                    $exgroup->nettotalfood += $request->costoffood[$index];
                    $exgroup->nettotalfuel += $request->gasolinecost[$index];
                    $exgroup->netexpresswaytoll += $request->expresswaytoll[$index];
                    $exgroup->netpublictransportfare += $request->publictransportfare[$index];
                    $exgroup->netotherexpenses += $request->otherexpenses[$index];
                    $exgroup->nettotal += $request->totalprice[$index];
                    $exgroup->save();
                }
                $email = $request->empemail[$index] ?? "";
                if ($email != "") {
                    // ส่ง mail ว่า success
                    $data = [
                        'name' => $request->fullname[$index] ?? "", // user
                        'price' => $request->totalprice[$index],
                        'pricedate' => $paymentdate,
                        'expenseid' => $expenseId, //exid
                    ];

                    MailHelper::sendExternalMail(
                        $email, // ผู้รับ
                        'แจ้งยอดการเบิกเบี้ยเลี้ยงปฎิบัติงานนอกสถานที่',
                        'mails.accountapporve', // ชื่อ blade view mail
                        $data,
                        'Expense Claim System EX' . $expenseId,
                    );
                }
            }

            DB::commit();
            return back()->with(['message' => 'อนุมัติรายการที่ Hold สำเร็จแล้ว', 'class' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with(['message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage(), 'class' => 'danger']);
        }
    }
}
