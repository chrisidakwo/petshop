<?php

namespace Tests\Models;

use App\Auth\Contracts\JwtSubject;
use Illuminate\Foundation\Auth\User;

class TestUser2 extends User implements JwtSubject
{
    public function getSubjectIdentifier(): string
    {
        return '98cf56be-0792-415e-a037-3334ebdafa52';
    }
}
