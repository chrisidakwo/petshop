<?php

declare(strict_types=1);

namespace App\Events;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserLoggedIn
{
    use Dispatchable, SerializesModels;

    public Authenticatable $user;

    /**
     * Create a new event instance.
     */
    public function __construct(Authenticatable $user)
    {
        $this->user = $user;
    }
}
