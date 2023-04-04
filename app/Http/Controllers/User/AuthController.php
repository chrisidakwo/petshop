<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Controllers\BaseAuthController;

class AuthController extends BaseAuthController
{
    /**
     * @return array<string, mixed>
     */
    protected function getCredentials(LoginRequest $request): array
    {
        return [
            'email' => $request->validated('email'),
            'password' => $request->validated('password'),
            'is_admin' => 0,
        ];
    }
}
