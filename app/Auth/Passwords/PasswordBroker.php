<?php

declare(strict_types=1);

namespace App\Auth\Passwords;

use Closure;
use Illuminate\Auth\Passwords\PasswordBroker as BasePasswordBroker;
use Illuminate\Contracts\Auth\PasswordBroker as PasswordBrokerContract;

class PasswordBroker extends BasePasswordBroker implements PasswordBrokerContract
{
    /**
     * {@inheritdoc}
     */
    public function sendResetLink(array $credentials, Closure $callback = null): string
    {
        // First we will check to see if we found a user at the given credentials and
        // if we did not we will redirect back to this current URI with a piece of
        // "flash" data in the session to indicate to the developers the errors.
        $user = $this->getUser($credentials);

        if (is_null($user)) {
            return static::INVALID_USER;
        }

        if ($this->tokens->recentlyCreatedToken($user)) {
            return static::RESET_THROTTLED;
        }

        $token = $this->tokens->create($user);

        if ($callback) {
            $callback($user, $token);
        } else {
            // Once we have the reset token, we are ready to send the message out to this
            // user with a link to reset their password. We will then redirect back to
            // the current URI having nothing set in the session to indicate errors.
            $user->sendPasswordResetNotification($token);
        }

        return $token;
    }
}
