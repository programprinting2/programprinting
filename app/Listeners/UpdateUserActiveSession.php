<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Carbon;

class UpdateUserActiveSession
{
    public function handle(Login $event): void
    {
        $user = $event->user;
        if (! $user) {
            return;
        }

        $user->forceFill([
            'current_session_id' => session()->getId(),
            'last_login_at' => Carbon::now(),
            'last_login_ip' => request()->ip(),
            'last_login_user_agent' => substr((string) request()->userAgent(), 0, 512),
        ])->save();
    }
}