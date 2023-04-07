<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Controllers\BaseAuthController;
use Illuminate\Http\JsonResponse;

class AuthController extends BaseAuthController
{
    /**
     * Login an admin account
     */
    public function login(LoginRequest $request): JsonResponse
    {
        return parent::login($request);
    }

    /**
     * Logout an admin account
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
            'is_admin' => 1,
        ];
    }
}
