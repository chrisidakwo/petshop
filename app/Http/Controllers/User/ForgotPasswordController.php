<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function index(ForgotPasswordRequest $request): JsonResponse
    {
        $email = $request->validated('email');

        $status = Password::sendResetLink([
            'email' => $email,
        ]);

        if (in_array($status, [Password::INVALID_USER, Password::RESET_THROTTLED])) {
            return $this->error('An error occurred', [], 422);
        }

        return $this->response([
            'reset_token' => $status,
        ]);
    }
}
