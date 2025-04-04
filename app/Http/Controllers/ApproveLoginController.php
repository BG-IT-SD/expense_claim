<?php

namespace App\Http\Controllers;

use App\Models\Approve;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApproveLoginController extends Controller
{
    public function loginWithToken(Request $request)
    {
        $token = $request->query('token');

        $approve = Approve::where('login_token', $token)
            ->where('token_expires_at', '>=', now())
            ->first();

        if (!$approve) {
            return response()->view('approve_expired'); // แสดงหน้าหมดอายุ
        }

        // OPTIONAL: ล็อกอิน user ตาม empid ถ้ามี
        $user =User::where('empid', $approve->empid)->first();
        if ($user) {
            Auth::login($user);
        }

        return redirect()->route('approve.page', ['id' => $approve->id]);
    }

}
