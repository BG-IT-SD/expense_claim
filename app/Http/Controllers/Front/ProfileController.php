<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Valldataemp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        $id = Auth::user()->id;
        $users = User::find($id);
        $empid = $users->empid ?? "";
        if ($empid != "") {
            $account = Valldataemp::where('CODEMPID', $empid)
                ->where('STAEMP', '!=', '9')
                ->first('NUMBANK');
            return view('front.profile.index', compact('users', 'account'));
        }
    }

    public function resetPassword()
    {
        return view('front.profile.resetpassword');
    }

    public function updatePassword(Request $request, $id)
    {
        // Validate request
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ], [
            'current_password.required' => 'กรุณากรอกรหัสผ่านปัจจุบัน',
            'new_password.required' => 'กรุณากรอกรหัสผ่านใหม่',
            'new_password.min' => 'รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร',
            'new_password.confirmed' => 'รหัสผ่านใหม่ไม่ตรงกัน',
        ]);

        $user = Auth::user(); // Get the logged-in user
        // Ensure the correct user is updating the password
        if ($user->id != $id) {
            return back()->withErrors(['error' => 'คุณไม่ได้รับอนุญาตให้อัปเดตรหัสผ่านนี้']);
        }

        // Check if current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'รหัสผ่านปัจจุบันไม่ถูกต้อง']);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        // Log out user
        Auth::logout();

        // Clear session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Clear all cookies (including user authentication cookies)
        Cookie::queue(Cookie::forget('user_login'));
        Cookie::queue(Cookie::forget('remember_token'));
        // Redirect to login page
        return redirect('/login')->with('success', 'รหัสผ่านถูกเปลี่ยนแล้ว กรุณาเข้าสู่ระบบใหม่');
    }
}
