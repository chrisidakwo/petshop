<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Password;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use OpenApi\Annotations as OA;

class ForgotPasswordController extends Controller
{
    /**
     * Creates a token to reset a user password
     *
     * @OA\Post(
     *     path="/api/v1/user/forgot-password",
     *     tags={"User"},
     *     summary="Creates a token to reset a user password",
     *     operationId="user/forgot-password",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 required={"email"},
     *                 @OA\Property(
     *                     property="email",
     *                     description="User email",
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
