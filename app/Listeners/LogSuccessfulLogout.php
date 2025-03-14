<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Auth\Events\Logout;
use App\Models\LoginLog;
use Illuminate\Support\Facades\Request;

class LogSuccessfulLogout
{
    public function handle(Logout $event)
    {
        $user = $event->user;
        LoginLog::create([
            'user_id' => $user->id ?? null,
            'empid' => $user->empid ?? null,
            'ip_address' => Request::ip(),
            'user_agent' => Request::header('User-Agent'),
            'status' => 'logout',
        ]);
    }
}
