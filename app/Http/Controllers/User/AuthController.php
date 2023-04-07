<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Controllers\BaseAuthController;
use Illuminate\Http\JsonResponse;

class AuthController extends BaseAuthController
{
    /**
     * Login a user account
     */
    public function login(LoginRequest $request): JsonResponse
    {
        return parent::login($request);
    }

    /**
     * Logout a user account
     */
    public function logout(): JsonResponse
    {
        return parent::logout();
    }

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
