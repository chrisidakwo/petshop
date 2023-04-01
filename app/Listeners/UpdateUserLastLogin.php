<?php

namespace App\Listeners;

use App\Events\UserLoggedIn;
use App\Models\User;

class UpdateUserLastLogin
{
    /**
     * Handle the event.
     */
    public function handle(UserLoggedIn $event): void
    {
        User::query()->whereUuid($event->user->getAuthIdentifier())->update([
            'last_login_at' => now()->getTimestamp(),
        ]);
    }
}
