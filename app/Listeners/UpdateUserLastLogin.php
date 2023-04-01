<?php

declare(strict_types=1);

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
        /** @var User $user */
        $user = $event->getUser();

        User::query()->whereUuid($user->getAuthIdentifier())->update([
            'last_login_at' => now(),
        ]);
    }
}
