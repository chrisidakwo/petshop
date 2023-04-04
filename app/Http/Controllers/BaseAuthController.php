<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Auth\LoginRequest;

abstract class BaseAuthController extends Controller
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
     * @return array<string, mixed>
     */
    abstract protected function getCredentials(LoginRequest $request): array;
}
