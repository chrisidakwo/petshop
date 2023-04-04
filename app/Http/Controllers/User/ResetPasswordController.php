<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use App\Http\Requests\Auth\ResetPasswordRequest;

class ResetPasswordController extends Controller
{
    public function showResetForm(): string
    {
        return 'Reset Password Form';
    }

    public function reset(ResetPasswordRequest $request): JsonResponse
    {
        $response = Password::reset($request->validated(), function (mixed $user, string $password): void {
            $user->password = bcrypt($password);
            $user->remember_token = Str::random(60);
            $user->save();

            event(new PasswordReset($user));

            Auth::guard()->login($user);
        });

        if ($response !== Password::PASSWORD_RESET) {
            return $this->error(trans($response));
        }

        return $this->response([
            'message' => trans($response),
        ]);
    }
}
