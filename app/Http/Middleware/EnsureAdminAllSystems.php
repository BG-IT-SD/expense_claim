<?php

namespace App\Http\Middleware;

use App\Models\UserRole;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminAllSystems
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        $hasAccess = $user && UserRole::query()
            ->where('userid', $user->id)
            ->where('status', 1)
            ->where('deleted', 0)
            ->whereHas('module', fn ($query) => $query->where('modulename', 'AllSystems'))
            ->whereHas('role', fn ($query) => $query->where('rolename', 'Admin'))
            ->exists();

        abort_unless($hasAccess, 403, 'คุณไม่มีสิทธิ์เข้าใช้งานเมนูนี้');

        return $next($request);
    }
}
