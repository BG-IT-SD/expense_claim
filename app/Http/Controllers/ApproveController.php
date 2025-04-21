<?php

namespace App\Http\Controllers;

use App\Helpers\MailHelper;
use App\Models\Approve;
use App\Models\GroupSpecial;
use App\Models\User;
use App\Models\Valldataemp;
use App\Models\Vbooking;
use App\Models\Vbookingall;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        $groupapprove = GroupSpecial::where('empid',$expense->empid)->where('deleted',0)->first();
        $groupData = $groupapprove->groupapprove ?? 1;
        $extype = $expense->extype ?? 1;

        if($extype == 3){
            if ($approve->typeapprove == 2) {

                $nextStepApprove = Approvestep($bu, $extype , 2,$groupData);
                $nextempid = $nextStepApprove['empid'];
                $nextemail = $nextStepApprove['email'];
                $nextfullname = $nextStepApprove['fullname'];

            }
        }else{
            // dd($extype);
            if ($approve->typeapprove == 4) {
                if($extype == 2){
                    $nextStepApprove = Approvestep($bu, 1 , 2);
                }else{
                    $nextStepApprove = Approvestep($bu, $extype , 2);
                }

                $nextempid = $nextStepApprove['empid'];
                $nextemail = $nextStepApprove['email'];
                $nextfullname = $nextStepApprove['fullname'];

            }
        }



        $exMail = '';
        $exName = '';
        if($extype == 2 || $extype == 3){
            $vAllemp = Valldataemp::where('CODEMPID', $expense->empid)->where('STAEMP', '!=', '9')->first();
            $exMail = $vAllemp->EMAIL;
            $exName = $vAllemp->NAMFIRSTT . ' ' . $vAllemp->NAMLASTT;
        }


        return view('approve.approve', compact('approve', 'expense', 'user', 'booking', 'departure_date', 'return_date', 'nextempid', 'nextemail', 'nextfullname','exName','exMail'));
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
                if($approve->typeapprove == 2){
                    $nextType = 1;
                }else{
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

        return back()->with([
            'message' => 'บันทึกผลอนุมัติเรียบร้อย',
            'class' => 'success'
        ]);
    }


    // public function confirmNextStep(Request $request, $id)
    // {
    //     $approve = Approve::findOrFail($id);

    //     if ($approve->statusapprove !== 0) {
    //         return back()->with('error', 'คุณได้ดำเนินการไปแล้ว');
    //     }

    //     if (now()->greaterThan($approve->token_expires_at)) {
    //         return back()->with('error', 'ลิงก์หมดอายุแล้ว');
    //     }

    //     $approve->statusapprove = $request->action === 'approve' ? 1 : 2;
    //     $approve->save();

    //     // ✅ หากอนุมัติสำเร็จ → สร้าง approve ถัดไป
    //     if ($approve->statusapprove === 1) {
    //         // ตั้งค่าข้อมูลผู้อนุมัติถัดไป (บัญชี)
    //         $nextType = $approve->typeapprove + 1;

    //         $nextEmpId = '66000111';
    //         $nextEmail = 'accounting@company.com';
    //         $nextName = 'บัญชีตรวจสอบ';

    //         $token = Str::random(64);
    //         $nextApprove = Approve::create([
    //             'exid' => $approve->exid,
    //             'typeapprove' => $nextType,
    //             'empid' => $nextEmpId,
    //             'email' => $nextEmail,
    //             'approvename' => $nextName,
    //             'emailstatus' => 1,
    //             'statusapprove' => 0,
    //             'login_token' => $token,
    //             'token_expires_at' => now()->addDays(10),
    //         ]);

    //         // ✅ ส่งอีเมลลิงก์อนุมัติรอบถัดไป
    //         $link = route('approve.magic.login', ['token' => $token]);

    //         $data = [
    //             'type' => $nextType,
    //             'title' => 'แจ้งเตือนการอนุมัติการเบิกเบี้ยเลี้ยง (ขั้นตอนถัดไป)',
    //             'name' => $nextName,
    //             'full_name' => $approve->approvename,
    //             'departuredate' => $approve->expense?->departuredate ?? '',
    //             'link' => $link,
    //         ];

    //         MailHelper::sendExternalMail(
    //             $nextEmail,
    //             'อนุมัติการเบิกเบี้ยเลี้ยง (ขั้นตอนถัดไป)',
    //             'mails.exapprove',
    //             $data,
    //             'Expense Claim System'
    //         );
    //     }

    //     return back()->with('success', 'บันทึกผลอนุมัติเรียบร้อย');
    // }
}
