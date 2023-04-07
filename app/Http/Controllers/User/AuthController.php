<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Controllers\BaseAuthController;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class AuthController extends BaseAuthController
{
    /**
     * Login a user account
     *
     * @OA\Post(
     *     path="/api/v1/user/login",
     *     tags={"User"},
     *     summary="Login a user account",
     *     operationId="user/login",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 required={"email", "password"},
     *                 @OA\Property(
     *                     property="email",
     *                     description="User email address",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     description="User password",
     *                     type="string"
     *                 ),
     *                 example={"email": "john.doe@email.com", "password": "password"}
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
     *     )
     * )
     */
    public function login(LoginRequest $request): JsonResponse
    {
        return parent::login($request);
    }

    /**
     * Logout a user account
     *
     * @OA\Get(
     *     path="/api/v1/user/logout",
     *     tags={"User"},
     *     summary="Logout a user account",
     *     operationId="user/logout",
     *     security={{"bearerAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *     )
     * )
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
