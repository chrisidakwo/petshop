<?php

declare(strict_types=1);

namespace App\Events\Auth;

use App\Auth\Contracts\JwtSubject;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class JwtGeneratedForUser
{
    use Dispatchable, SerializesModels;

    protected string $token;
    protected JwtSubject $subject;

    /**
     * Create a new event instance.
     */
    public function __construct(string $token, JwtSubject $subject)
    {
        $this->token = $token;
        $this->subject = $subject;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getSubject(): JwtSubject
    {
        return $this->subject;
    }
}
