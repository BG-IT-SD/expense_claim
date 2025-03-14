<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Auth\Events\Failed;
use App\Models\LoginLog;
use Illuminate\Support\Facades\Request;

class LogFailedLogin
{
    public function handle(Failed $event)
    {
        LoginLog::create([
            'user_id' => null, // User ไม่ได้เข้าสู่ระบบ
            'empid' => $event->credentials['empid'] ?? 'unknown',
            'ip_address' => Request::ip(),
            'user_agent' => Request::header('User-Agent'),
            'status' => 'failed',
        ]);
    }
}
