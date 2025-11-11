<?php

namespace App\Http\Controllers\Back;

use App\Helpers\MailHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ResendMailController extends Controller
{

    public function showForm()
    {
        return view('back.tools.resend_mail_form');
    }



    public function sendMail(Request $request)
    {
        // 2.1 ตรวจสอบข้อมูล (Validation)
        $validator = Validator::make($request->all(), [
            'head_email' => 'required|email',
            'head_name'  => 'required',
            'full_name'  => 'required',
            'link'       => 'required|url',
            'expense_id' => 'required',
            'departuredatemail' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // 2.2 เตรียม $data ที่จะส่งเข้า Blade
        $data = [
            'type'        => 1,
            'title'       => 'แจ้งเตือนการอนุมัติการเบิกเบี้ยเลี้ยง',
            'name'        => $request->head_name,
            'full_name'   => $request->full_name,
            'departuredate' => $request->departuredatemail ?? '',
            'link'        => $request->link,
        ];

        try {

            MailHelper::sendExternalMail(
                $request->head_email,
                'อนุมัติการเบิกเบี้ยเลี้ยง',
                'mails.exapprove', // ชื่อ blade view
                $data,
                'Expense Claim System EX ' . $request->expense_id,
            );
        } catch (\Exception $e) {
            // 2.4 กรณียิง Mail ไม่ผ่าน (เช่น SMTP config ผิด)
            return redirect()->back()
                ->withInput()
                ->with('error', 'ส่งอีเมลไม่สำเร็จ: ' . $e->getMessage());
        }

        // 2.5 ส่งสำเร็จ
        return redirect()->back()->with('success', 'ส่งอีเมลไปยัง ' . $request->head_email . ' สำเร็จแล้ว!');
    }
}
