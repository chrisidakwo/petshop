<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Password;
use App\Http\Requests\Auth\ForgotPasswordRequest;

class ForgotPasswordController extends Controller
{
    /**
     * Creates a token to reset a user password
     */
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
