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
use OpenApi\Annotations as OA;

class ResetPasswordController extends Controller
{
    public function showResetForm(): string
    {
        return 'Reset Password Form';
    }

    /**
     * Reset a user password with a token
     *
     * @OA\Post(
     *     path="/api/v1/user/reset-password-token",
     *     tags={"User"},
     *     summary="Reset a user password with a token",
     *     operationId="user/reset-password",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 required={"token", "email", "password", "password_confirmation"},
     *                 @OA\Property(
     *                     property="token",
     *                     description="User reset token",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     description="User email",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     description="User password",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="password_confirmation",
     *                     description="User password",
     *                     type="string"
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity"
     *     ),
     * )
     */
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
