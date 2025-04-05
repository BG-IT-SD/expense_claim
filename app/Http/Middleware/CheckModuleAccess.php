<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\UserRole;
use App\Models\Module;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;

class CheckModuleAccess
{
    /**
     * Handle access check by module name and allowed roles (multiple)
     */
    public function handle(Request $request, Closure $next, string $moduleName, string $roleNames): Response
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, 'กรุณาเข้าสู่ระบบ');
        }

        // ✅ แยกชื่อ role เป็น array
        $roleList = explode('|', $roleNames);
        $roleIds = Role::whereIn('rolename', $roleList)->pluck('id');
        // dd($roleIds);
       // ไม่เช็ค roleid ในกรณี AllSystems
        $hasAllSystem = UserRole::where('userid', $user->id)
        ->whereHas('module', fn($q) => $q->where('modulename', 'AllSystems'))
        ->where('status', 1)
        ->where('deleted', 0)
        ->exists();

        if ($hasAllSystem) {
        return $next($request); // ✅ ผ่านเลย
        }


        // ✅ ตรวจสอบสิทธิ์เฉพาะโมดูลที่ระบุ
        $module = Module::where('modulename', $moduleName)->first();

        if (!$module) {
            abort(403, 'โมดูลไม่ถูกต้อง');
        }

        $hasAccess = UserRole::where('userid', $user->id)
            ->where('moduleid', $module->id)
            ->whereIn('roleid', $roleIds)
            ->where('status', 1)
            ->where('deleted', 0)
            ->exists();

        if (!$hasAccess) {
            abort(403, 'คุณไม่มีสิทธิ์เข้าถึงโมดูลนี้');
        }

        return $next($request);
    }


}

