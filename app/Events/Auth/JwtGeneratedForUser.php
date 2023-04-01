<?php

declare(strict_types=1);

namespace App\Events\Auth;

use App\Auth\Contracts\JwtSubject;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class JwtGeneratedForUser
{
    use Dispatchable, SerializesModels;

    public string $token;
    public JwtSubject $subject;

    /**
     * Create a new event instance.
     */
    public function __construct(string $token, JwtSubject $subject)
    {
        $this->token = $token;
        $this->subject = $subject;
    }
}
