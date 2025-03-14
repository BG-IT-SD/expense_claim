<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\LoginLog;
use Illuminate\Support\Facades\Request;

class LogSuccessfulLogin
{
    public function handle(Login $event)
    {
        $user = $event->user;
        // dd($user->empid);
        LoginLog::create([
            'user_id' => $user->id,
            'empid' => $user->empid,
            'ip_address' => Request::ip(),
            'user_agent' => Request::header('User-Agent'),
            'status' => 'success',
        ]);
    }
}
