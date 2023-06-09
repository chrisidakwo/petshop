<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Models\User;
use App\Http\Services\JwtTokenService;
use App\Events\Auth\JwtGeneratedForUser;

class PersistJwtTokenToDB
{
    public function __construct(private JwtTokenService $jwtTokenService)
    {
    }

    /**
     * Handle the event.
     */
    public function handle(JwtGeneratedForUser $event): void
    {
        /** @var User|null $user */
        $user = User::query()->whereUuid($event->getSubject()->getSubjectIdentifier())->first();

        if ($user !== null) {
            $this->jwtTokenService->expireUserToken($user->id);

            $this->jwtTokenService->create($event->getToken(), $event->getSubject());
        }
    }
}
