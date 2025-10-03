<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceMode
{
    public function handle(Request $request, Closure $next): Response
    {
        // dd($request->ip(), $request->header('x-forwarded-for'));
        // กำหนด IP ที่อนุญาตเข้าระบบ
        $allowedIp = '10.255.4.2520'; // IP เครื่องที่จะใช้

        // Route name ที่อนุญาตให้เข้าได้แม้จะ maintenance อยู่
        $allowedRoutes = [
            'updatefuel.index',
        ];

        // ถ้าเป็น route ที่ whitelist  ผ่านเลย
        if ($request->route() && in_array($request->route()->getName(), $allowedRoutes)) {
            return $next($request);
        }

        // ถ้า IP ไม่ใช่ที่อนุญาต → แสดงหน้า maintenance
        if ($request->ip() !== $allowedIp) {
            return response()->view('maintenance');
        }

        return $next($request);
    }
}
