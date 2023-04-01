<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\BaseAuthController;
use App\Http\Requests\Auth\LoginRequest;

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
