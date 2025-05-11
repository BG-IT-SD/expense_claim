<?php

namespace App\Http\Controllers;

use App\Helpers\MailHelper;
use App\Models\Approve;
use App\Models\ApproveStaff;
use App\Models\Exgroup;
use App\Models\Expense;
use App\Models\GroupSpecial;
use App\Models\User;
use App\Models\Valldataemp;
use App\Models\Vbooking;
use App\Models\Vbookingall;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ApproveController extends Controller
{
    public function show($id)
    {
        $nextempid = '';
        $nextemail = '';
        $nextfullname = '';
        $nextStepApprove = '';
        $nextempid = '';
        $nextemail = '';
        $nextfullname = '';

        $approve = Approve::with('expense')->findOrFail($id);
        // if (!Auth::check() && session('approve_access') != $id) {
        //     abort(403, 'Unauthorized');
        // }


        $expense = $approve->expense;
        $user = User::where('empid', $expense->empid)->first(); // กรณีมี user
        $booking = Vbookingall::where('id', $expense->bookid)->first();
        // dd($expense);
        $departure_date = $booking->departure_date ? Carbon::parse("{$booking->departure_date} {$booking->departure_time}")->format('d/m/Y H:i') : null;
        $return_date = $booking->return_date ? Carbon::parse("{$booking->return_date} {$booking->return_time}")->format('d/m/Y H:i') : null;

        $bu = BuEmp($expense->empid);
        // เช็คกลุ่ม

        $groupapprove = GroupSpecial::where('empid', $expense->empid)->where('deleted', 0)->first();
        $groupData = $groupapprove->groupapprove ?? 1;
        $extype = $expense->extype ?? 1;

        if ($extype == 3) {
            if ($approve->typeapprove == 2) {

                $nextStepApprove = Approvestep($bu, $extype, 2, $groupData);
                $nextempid = $nextStepApprove['empid'];
                $nextemail = $nextStepApprove['email'];
                $nextfullname = $nextStepApprove['fullname'];
            }
        } else {
            // dd($extype);
            if ($approve->typeapprove == 4) {
                if ($extype == 2) {
                    $nextStepApprove = Approvestep($bu, 1, 2);
                } else {
                    $nextStepApprove = Approvestep($bu, $extype, 2);
                }

                $nextempid = $nextStepApprove['empid'];
                $nextemail = $nextStepApprove['email'];
                $nextfullname = $nextStepApprove['fullname'];
            }
        }



        $exMail = '';
        $exName = '';
        if ($extype == 2 || $extype == 3) {
            $vAllemp = Valldataemp::where('CODEMPID', $expense->empid)->where('STAEMP', '!=', '9')->first();
            $exMail = $vAllemp->EMAIL;
            $exName = $vAllemp->NAMFIRSTT . ' ' . $vAllemp->NAMLASTT;
        }


        return view('approve.approve', compact('approve', 'expense', 'user', 'booking', 'departure_date', 'return_date', 'nextempid', 'nextemail', 'nextfullname', 'exName', 'exMail'));
    }


    public function confirm(Request $request, $id)
    {
        $approve = Approve::findOrFail($id);
        $action = $request->input('action');
        $reason = $request->input('reason');
        $typeapprove = $request->input('typeapprove');
        $expenseempid = $request->input('expenseempid');
        $departuredate = $request->input('departuredate');
        $approvename = $request->input('approvename');
        $expenseid = $request->input('expenseid');
        $empemail = $request->input('empemail');
        $empfullname = $request->input('empfullname');

        if ($approve->statusapprove !== 0) {
            return back()->with([
                'message' => 'คุณได้ดำเนินการไปแล้ว',
                'class' => 'error'
            ]);
        }

        if (now()->greaterThan($approve->token_expires_at)) {
            return back()->with([
                'message' => 'ลิงก์หมดอายุแล้ว',
                'class' => 'error'
            ]);
        }

        if ($action === 'reject' && empty($reason)) {
            return back()->with([
                'message' => 'กรุณากรอกเหตุผลที่ไม่อนุมัติ',
                'class' => 'error'
            ]);
        }

        $approve->statusapprove = $request->action === 'approve' ? 1 : 2;
        $approve->remark = $reason; // หรือ reject_reason
        $approve->save();

        // ✅ ส่งเมลเฉพาะกรณี reject
        if ($request->action === 'reject') {
            $data = [
                'headname' => $approvename, // คนที่ reject
                'name' => $empfullname, // user
                'expenseid' => $approve->exid, //exid
                'departuredate' => $departuredate,
                'remark' => $reason,
            ];

            MailHelper::sendExternalMail(
                $empemail, // ผู้รับ คือ ผู้ขอเบิก
                'แจ้งผลการไม่อนุมัติการเบิกเบี้ยเลี้ยง',
                'mails.reject', // ชื่อ blade view mail
                $data,
                'Expense Claim System EX' . $approve->exid,
            );
        }

        $nextempid = '';
        $nextemail = '';
        $nextfullname = '';

        if ($approve->typeapprove == 4 || $approve->typeapprove == 2) {
            // ✅ หากอนุมัติสำเร็จ สร้าง approve ถัดไป
            if ($approve->statusapprove === 1) {

                // $nextempid = $request->input('nextempid');
                // $nextemail = $request->input('nextemail');
                // $nextfullname = $request->input('nextfullname');
                $nextempid = '66000510';
                $nextemail = 'kamolwan.b@bgiglass.com';
                $nextfullname = 'กมลวรรณ บรรชา';
                // ตั้งค่าข้อมูลผู้อนุมัติถัดไป (HR ผุู้จัดการฝ่าย)
                if ($approve->typeapprove == 2) {
                    $nextType = 1;
                } else {
                    $nextType = $approve->typeapprove + 1;
                }


                $token = Str::random(64);
                $nextApprove = Approve::create([
                    'exid' => $approve->exid,
                    'typeapprove' => $nextType,
                    'empid' => $nextempid,
                    'email' => $nextemail,
                    'approvename' => $nextfullname,
                    'emailstatus' => 1,
                    'statusapprove' => 0,
                    'login_token' => $token,
                    'token_expires_at' => now()->addDays(10),
                ]);

                // ✅ ส่งอีเมลลิงก์อนุมัติรอบถัดไป
                $link = route('approve.magic.login', ['token' => $token]);

                $data = [
                    'type' => $nextType,
                    'title' => 'แจ้งเตือนการอนุมัติการเบิกเบี้ยเลี้ยง',
                    'name' => $nextfullname,
                    'full_name' => $empfullname,
                    'departuredate' => $departuredate ?? '',
                    'check_hr' => $approvename,
                    'link' => $link,
                ];

                MailHelper::sendExternalMail(
                    $nextemail,
                    'อนุมัติการเบิกเบี้ยเลี้ยง',
                    'mails.hrheadapprove',
                    $data,
                    'Expense Claim System EX' . $approve->exid,
                );
            }
        }

        // บัญชีอนุมัติเรียบร้อย
        if ($approve->typeapprove == 6 && $approve->statusapprove === 1) {
            // อนุมัติขั้นตอนสุดท้ายเสร็จ
            // $linksuccess = route('approve.magic.login', ['token' => $token]);

            $data = [
                'type' => 5,
                'title' => 'แจ้งเตือนการอนุมัติการเบิกเบี้ยเลี้ยง',
                'name' => $empfullname,
                'full_name' => $empfullname,
                'departuredate' => $departuredate ?? '',
            ];

            MailHelper::sendExternalMail(
                $empemail,
                'อนุมัติการเบิกเบี้ยเลี้ยง',
                'mails.success',
                $data,
                'Expense Claim System EX' . $approve->exid,
            );
        }
        // บัญชีอนุมัติเรียบร้อย

        return back()->with([
            'message' => 'บันทึกผลอนุมัติเรียบร้อย',
            'class' => 'success'
        ]);
    }

    public function showgroup($id,$type)
    {
        //ดึงรายการ approve ทั้งหมดในกลุ่ม
        $approve = Approve::where('exgroup', $id)
        ->where('typeapprove',$type)
        ->orderBy('typeapprove', 'desc')
        ->first();

        //ดึงรายการเบิกทั้งหมดในกลุ่ม พร้อม relations
        $expenses = Expense::with(['vbooking', 'user', 'tech', 'userhr'])
            ->where('exgroup', $id)
            ->get();
        $makeuserempid = $approve->empid;
        //ดึงข้อมูลกลุ่ม
        $exgroup = Exgroup::findOrFail($id);

        $nextstaffgroup = ApproveStaff::where('group', function ($query) use ($makeuserempid) {
            $query->select('group')
                ->from('approvestaff')
                ->where('empid', $makeuserempid)
                ->where('status', 1)
                ->where('deleted', 0)
                ->limit(1);
        })
            ->where('step', 2)
            ->where('status', 1)
            ->where('deleted', 0)
            ->first();
        // dd($nextstaffgroup);
        if($type == 5){
            $nextempid = '99999999';
            $nextfullname = 'หน่วยงานบัญชี';
            $nextemail = 'account_auto@bgiglass.com';
        }else{
            $nextempid = $nextstaffgroup->empid ?? '';
            $nextfullname = $nextstaffgroup->fullname ?? '';
            // $nextemail = $nextstaffgroup->email ?? '';
            $nextemail = 'Kamolwan.b@bgiglass.com';
        }

        return view('approve.approve_group', compact('approve', 'expenses', 'exgroup', 'nextstaffgroup', 'nextempid', 'nextfullname', 'nextemail'));
    }

    public function confirmgroup(Request $request, $id)
    {

        $approve = Exgroup::findOrFail($id);
        $approveex = Approve::where('exgroup', $id)->first();

        $action = $request->input('action');
        $reason = $request->input('reason');
        $typeapprove = $request->input('typeapprove');
        $approvename = $request->input('approvename');
        $nextempid = $request->input('nextempid');
        $nextfullname = $request->input('nextfullname');
        $nextemail = $request->input('nextemail');
        $expenseidgroup = $request->input('expenseidgroup');
        $groupdate = $request->filled('groupdate')
            ? Carbon::parse($request->input('groupdate'))->format('d/m/Y')
            : null;
        $expensegroupdata = Approve::where('exgroup', $id)
        ->where('deleted',0)
        ->where('status',1)
        ->where('typeapprove',$typeapprove)
        ->get();



        if ($approve->statusapprove !== 0) {
            return back()->with([
                'message' => 'คุณได้ดำเนินการไปแล้ว',
                'class' => 'error'
            ]);
        }


        if (now()->greaterThan($approveex->token_expires_at)) {
            return back()->with([
                'message' => 'ลิงก์หมดอายุแล้ว',
                'class' => 'error'
            ]);
        }

        if ($action === 'reject' && empty($reason)) {
            return back()->with([
                'message' => 'กรุณากรอกเหตุผลที่ไม่อนุมัติ',
                'class' => 'error'
            ]);
        }

        try {
            DB::beginTransaction();

            // 2. อัปเดต Approve ทั้งหมดในกลุ่มนี้
            Approve::where('exgroup', $id)->where('typeapprove', $typeapprove)->update([
                'statusapprove' => ($action === 'approve') ? 1 : 2,
                'remark' => $reason ?? null,
            ]);
            $nexttypeapprove = $typeapprove+1;
            // 3. ถ้า approve สร้าง approve ใหม่ (ขั้นถัดไป)
            $token = Str::random(64);
            if ($action === 'approve') {
                foreach ($expensegroupdata as $exid) {
                    Approve::create([
                        'exid' => $exid->exid,
                        'typeapprove' => $nexttypeapprove,
                        'empid' => $nextempid,
                        'email' => $nextemail,
                        'approvename' => $nextfullname,
                        'emailstatus' => 1,
                        'statusapprove' => 0,
                        'login_token' => $token,
                        'token_expires_at' => now()->addDays(10),
                        'exgroup' => $id,
                    ]);
                }

                    //อัปเดต exgroup
                    $approve->typeapprove = $nexttypeapprove;
                    $approve->statusapprove = 0;
                    if($typeapprove == 4){
                    $approve->finalempid = $nextempid;
                    $approve->finalemail = $nextemail;
                    }
                    $approve->save();

            }



            DB::commit();

            if($typeapprove == 4 || $typeapprove == 5){
                $link = route('approve.magic.login', ['token' => $token]);
                // ส่งเมลแจ้งขั้นถัดไปหลัง commit
                MailHelper::sendExternalMail(
                    $nextemail,
                    'แจ้งเตือนการอนุมัติรายการกลุ่ม',
                    'mails.groupapprove',
                    [
                        'name' => $nextfullname,
                        'groupid' => $id,
                        'count' => $expensegroupdata->count(),
                        'groupdate' => $groupdate,
                        'checkname' => $approvename,
                        'link' => $link
                    ],
                    'รายการขออนุมัติกลุ่ม EXGROUP-' . $id . 'วันที่ ' . $groupdate
                );
            }


            return redirect()->route('approve.page.group', ['id' => $id,'type'=>$typeapprove])
                ->with(['message' => 'บันทึกผลอนุมัติแล้ว', 'class' => 'success']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
        }
    }
}
