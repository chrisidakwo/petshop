<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->only('logout');
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $this->getCredentials($request);

        $token = Auth::attempt($credentials);

        if ($token === false) {
            return $this->error('Failed to authenticate user', [], 422);
        }

        return $this->response(
            [
                'token' => $token,
            ]
        );
    }

    public function logout(): JsonResponse
    {
        Auth::logout();

        return $this->response();
    }

    /**
     * @return array<string, string | int>
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
