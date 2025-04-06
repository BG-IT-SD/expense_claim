<?php

namespace App\Http\Controllers;

use App\Models\Approve;
use App\Models\User;
use App\Models\Valldataemp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ApproveLoginController extends Controller
{

    public function loginWithToken(Request $request)
    {
        $token = $request->query('token');

        $approve = Approve::where('login_token', $token)
            ->where('token_expires_at', '>=', now())
            ->first();

        if (!$approve) {
            return view('approve.approve_expired');
        }

        // ล้าง token เพื่อป้องกัน reuse
        // $approve->login_token = null;
        // $approve->save();

        // ค้นหา user ตาม empid
        $user = User::where('empid', $approve->empid)->first();

        if (!$user) {
            // ✅ สร้าง user ใหม่อัตโนมัติ
            $DetailEmp = Valldataemp::where('CODEMPID', $approve->empid)
                ->where('STAEMP', '!=', '9')
                ->first();

                if (!$DetailEmp) {
                    return response()->view('approve.approve_expired', [
                        'message' => 'ไม่พบข้อมูลพนักงานในระบบภายนอก'
                    ]);
                }
            $user = User::create([
                'empid'    => $approve->empid,
                'fullname' => $approve->approvename ?? 'Auto-generated',
                'email'    => $DetailEmp->EMAIL ?? null,
                'bu' => $DetailEmp->alias_name ?? null,
                'dept' =>  $DetailEmp->DEPT ?? null,
                'status'   => 1,
                'deleted'  => 0,
                'password' => bcrypt(Str::random(16)), // รหัสผ่านสุ่ม (ไม่ได้ใช้จริง)
                'created_by' => 'system-auto'
            ]);
        }

        // ล็อกอิน user (ทั้งที่มีอยู่แล้วหรือเพิ่งสร้าง)
        Auth::login($user);
        session()->regenerate();

        return redirect()->route('approve.page', ['id' => $approve->id]);
    }
}
