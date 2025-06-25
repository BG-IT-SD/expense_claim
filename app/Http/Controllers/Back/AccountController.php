<?php

namespace App\Http\Controllers\Back;

use App\Helpers\MailHelper;
use App\Http\Controllers\Controller;
use App\Models\Approve;
use App\Models\Exgroup;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
    public function index()
    {
        $exgroups = Exgroup::where('deleted', 0)
            ->where('typeapprove', 6)
            ->where('statusapprove',0)
            ->orderByDesc('id')
            ->get();
        return view('back.account.index', compact('exgroups'));
    }

    public function manage($id)
    {
        $expenses = Expense::with(['vbooking', 'user', 'tech', 'userhr'])
            ->where('exgroup', $id)
            ->get();
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



    public function saveExgroupApproval(Request $request)
    {

        $request->validate([
            'expense_id' => 'required|array',
            'statsapprove' => 'required|array',
            'txtreason' => 'nullable|array',
            'nettotalfood' => 'required',
            'nettotalfuel' => 'required',
            'netexpresswaytoll' => 'required',
            'netpublictransportfare' => 'required',
            'netotherexpenses' => 'required',
            'nettotalother' => 'required',
            'nettotal' => 'required',
        ]);

        $exgroupId = intval($request->input('exgroup_id'));

        $exgroup = Exgroup::where('id', $exgroupId)
            ->where('statusapprove', '!=', 1)
            ->first();
        // dd($exgroup);
        if (!$exgroup) {
            return redirect()->route('Account.index')->with([
                'message' => 'รายการนี้ถูกอนุมัติแล้ว',
                'class' => 'warning'
            ]);
        }

        DB::beginTransaction();
        try {
            // loop อัปเดต statusapprove และ reason
            $expenseIds = $request->input('expense_id', []);
            $statuses = $request->input('statsapprove', []);
            $reasons = $request->input('txtreason', []);
            $fullnames = $request->input('fullname', []);
            $accountempid = $request->input('accountempid');
            $accountemail = $request->input('accountemail');
            $paymentdate = $request->input('paymentdate');

            foreach ($expenseIds as $index => $exid) {
                $status = $statuses[$index];
                $reason = $reasons[$index] ?? null;
                $fullname = $fullnames[$index] ?? '';

                $approve = Approve::where('exid', $exid)->where('typeapprove', 6)->first();
                if ($approve) {
                    $expense_data = $approve->expense;
                    $totalprice =  $approve->expense->totalprice;
                    $exdate =  $expense_data->vbooking->departure_date;
                    $pricedate = $exgroup->groupdate;
                    $Tomail = EmailEmp($approve->expense->empid) ?? '';
                    $approve->statusapprove = $status;
                    $approve->remark = $reason;
                    $approve->save();
                    if ($Tomail != '') {
                        if ($status == 1) {
                            // ส่ง mail ว่า success
                            $data = [
                                'name' => $fullname, // user
                                'price' => $totalprice,
                                'pricedate' => $paymentdate,
                                'expenseid' => $approve->exid, //exid
                            ];

                            MailHelper::sendExternalMail(
                                $Tomail, // ผู้รับ
                                'แจ้งยอดการเบิกเบี้ยเลี้ยงปฎิบัติงานนอกสถานที่',
                                'mails.accountapporve', // ชื่อ blade view mail
                                $data,
                                'Expense Claim System EX' . $exid,
                            );
                        } elseif ($status == 2 || $status == 9) {
                            $textreject = $status == 2 ? 'ไม่ผ่านการอนุมัติ' : 'ติดสถานะHold';
                            // ส่ง mail ว่า reject หรือ Hold
                            $data = [
                                'name' => $fullname, // user
                                'text' => $textreject,
                                'expenseid' => $approve->exid, //exid
                                'exdate' => $exdate,
                                'remark' => $reason,
                            ];
                            // $Tomail
                            MailHelper::sendExternalMail(
                                'Kamolwan.b@bgiglass.com', // ผู้รับ
                                'แจ้งผลการเบิกเบี้ยเลี้ยงปฎิบัติงานนอกสถานที่จากบัญชี',
                                'mails.accounthold', // ชื่อ blade view mail
                                $data,
                                'Expense Claim System EX' . $exid,
                            );
                        }
                    }
                }
            }
            // อัปเดตยอดสุทธิใน exgroup
            $exgroup->nettotalfood = $this->cleanNumber($request->input('nettotalfood'));
            $exgroup->nettotalfuel = $this->cleanNumber($request->input('nettotalfuel'));
            $exgroup->netexpresswaytoll = $this->cleanNumber($request->input('netexpresswaytoll'));
            $exgroup->netpublictransportfare = $this->cleanNumber($request->input('netpublictransportfare'));
            $exgroup->netotherexpenses = $this->cleanNumber($request->input('netotherexpenses'));
            $exgroup->nettotalother = $this->cleanNumber($request->input('nettotalother'));
            $exgroup->nettotal = $this->cleanNumber($request->input('nettotal'));
            $exgroup->accountempid = $accountempid; // บัญชีคนอนุมัติ
            $exgroup->accountemail = $accountemail; // emailบัญชีคนอนุมัติ
            $exgroup->statusapprove = 1; // set เป็นอนุมัติ
            $exgroup->paymentdate = $paymentdate; //วันที่จ่าย
            $exgroup->save();

            DB::commit();

            return redirect()->route('Account.index')->with(['message' => 'บันทึกผลอนุมัติเรียบร้อย', 'class' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('Account.index')->with(['message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage(), 'class' => 'danger']);
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

    public function ListApproved()
    {

        $exgroups = Exgroup::where('deleted', 0)
        ->where('typeapprove', 6)
        // ->where('statusapprove',0)
        ->whereIn('statusapprove', [1, 2])
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
