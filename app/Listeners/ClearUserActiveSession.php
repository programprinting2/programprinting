<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;

class ClearUserActiveSession
{
    public function handle(Logout $event): void
    {
        $user = $event->user;
        if (! $user) {
            return;
        }

        $user->forceFill([
            'current_session_id' => null,
        ])->save();
    }
}