<?php

namespace App\Http\Controllers;

use App\Helpers\MailHelper;
use App\Models\Approve;
use App\Models\User;
use App\Models\Vbooking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ApproveController extends Controller
{
    public function show($id)
    {
        $approve = Approve::with('expense')->findOrFail($id);
        // if (!Auth::check() && session('approve_access') != $id) {
        //     abort(403, 'Unauthorized');
        // }

        $expense = $approve->expense;
        $user = User::where('empid', $expense->empid)->first(); // กรณีมี user
        $booking = Vbooking::where('id', $expense->bookid)->first();
        $departure_date = $booking->departure_date ? Carbon::parse("{$booking->departure_date} {$booking->departure_time}")->format('d/m/Y H:i') : null;
        $return_date = $booking->return_date ? Carbon::parse("{$booking->return_date} {$booking->return_time}")->format('d/m/Y H:i') : null;
        // dd($booking);

        return view('approve.approve', compact('approve', 'expense', 'user', 'booking', 'departure_date', 'return_date'));
    }


    public function confirm(Request $request, $id)
    {
        $approve = Approve::findOrFail($id);
        $action = $request->input('action');
        $reason = $request->input('reason');

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
        return back()->with([
            'message' => 'บันทึกผลอนุมัติเรียบร้อย',
            'class' => 'success'
        ]);
    }


    public function confirmNextStep(Request $request, $id)
    {
        $approve = Approve::findOrFail($id);

        if ($approve->statusapprove !== 0) {
            return back()->with('error', 'คุณได้ดำเนินการไปแล้ว');
        }

        if (now()->greaterThan($approve->token_expires_at)) {
            return back()->with('error', 'ลิงก์หมดอายุแล้ว');
        }

        $approve->statusapprove = $request->action === 'approve' ? 1 : 2;
        $approve->save();

        // ✅ หากอนุมัติสำเร็จ → สร้าง approve ถัดไป
        if ($approve->statusapprove === 1) {
            // ตั้งค่าข้อมูลผู้อนุมัติถัดไป (บัญชี)
            $nextType = $approve->typeapprove + 1;

            $nextEmpId = '66000111';
            $nextEmail = 'accounting@company.com';
            $nextName = 'บัญชีตรวจสอบ';

            $token = Str::random(64);
            $nextApprove = Approve::create([
                'exid' => $approve->exid,
                'typeapprove' => $nextType,
                'empid' => $nextEmpId,
                'email' => $nextEmail,
                'approvename' => $nextName,
                'emailstatus' => 1,
                'statusapprove' => 0,
                'login_token' => $token,
                'token_expires_at' => now()->addDays(10),
            ]);

            // ✅ ส่งอีเมลลิงก์อนุมัติรอบถัดไป
            $link = route('approve.magic.login', ['token' => $token]);

            $data = [
                'type' => $nextType,
                'title' => 'แจ้งเตือนการอนุมัติการเบิกเบี้ยเลี้ยง (ขั้นตอนถัดไป)',
                'name' => $nextName,
                'full_name' => $approve->approvename,
                'departuredate' => $approve->expense?->departuredate ?? '',
                'link' => $link,
            ];

            MailHelper::sendExternalMail(
                $nextEmail,
                'อนุมัติการเบิกเบี้ยเลี้ยง (ขั้นตอนถัดไป)',
                'mails.exapprove',
                $data,
                'Expense Claim System'
            );
        }

        return back()->with('success', 'บันทึกผลอนุมัติเรียบร้อย');
    }
}
