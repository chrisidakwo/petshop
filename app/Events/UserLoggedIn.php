<?php

declare(strict_types=1);

namespace App\Events;

use App\Auth\Contracts\JwtSubject;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserLoggedIn
{
    use Dispatchable, SerializesModels;

    protected Authenticatable|JwtSubject $user;

    /**
     * Create a new event instance.
     */
    public function __construct(Authenticatable|JwtSubject $user)
    {
        $this->user = $user;
    }

    public function getUser(): JwtSubject|Authenticatable
    {
        return $this->user;
    }
}
