<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Valldataemp;

class ForgotByEmployeeController extends Controller
{

    public function index(){
       return view('auth.forgot-by-employee');
    }


    public function verify(Request $req)
    {
        $req->validate([
            'empid' => ['required', 'digits_between:4,12'],
            'cid'   => ['required', 'digits:13', function ($attr, $val, $fail) {
                $sum = 0;
                for ($i = 0; $i < 12; $i++) $sum += intval($val[$i]) * (13 - $i);
                if (((11 - ($sum % 11)) % 10) !== intval($val[12])) $fail('เลขบัตรประชาชนไม่ถูกต้อง');
            }],
        ]);

        $empid = trim($req->empid);
        $cid   = preg_replace('/\D/', '', $req->cid);

        $user = User::where('empid', $empid)->first();
        if (!$user) {
            return back()->withInput()
                ->with('no_user', true)
                ->with('message', 'ไม่พบข้อมูลผู้ใช้ในระบบ กรุณาสร้างผู้ใช้ก่อน');
        }

        $empRow = Valldataemp::where('CODEMPID', $empid)->first();
        if (!$empRow) {
            return back()->withErrors(['empid' => 'ข้อมูลไม่ถูกต้อง'])->withInput();
        }

        $cidDb = preg_replace('/\D/', '', (string)$empRow->NUMOFFID);
        if ($cidDb === '' || !hash_equals($cidDb, $cid)) {
            return back()->withErrors(['empid' => 'ข้อมูลไม่ถูกต้อง'])->withInput();
        }

        //ออก token และเก็บ session
        $token = Str::random(40);
        session()->put("pw_reset.$token", $user->empid);

        return view('auth.reset-by-employee', [
            'empid' => $user->empid,
            'empname' => $user->fullname,
            'token' => $token,
        ]);
    }

    public function reset(Request $req)
    {
        $req->validate([
            'empid'    => ['required'],
            'password' => ['required', 'confirmed', 'min:8', 'regex:/[A-Z]/', 'regex:/[0-9]/'],
            'token'    => ['required'],
        ]);

        // ตรวจ token จาก session
        $empFromSession = session("pw_reset.{$req->token}");
        if (!$empFromSession || $empFromSession !== $req->empid) {
            return redirect()->route('password.forgot-by-employee')
                ->withErrors(['empid' => 'เซสชันหมดอายุหรือไม่ถูกต้อง โปรดยืนยันตัวตนอีกครั้ง']);
        }
        session()->forget("pw_reset.{$req->token}");

        $user = User::where('empid', $req->empid)->firstOrFail();
        $user->password = Hash::make($req->password);
        $user->save();

        return redirect()->route('login')->with('success', 'ตั้งรหัสผ่านใหม่เรียบร้อย');
    }
}
