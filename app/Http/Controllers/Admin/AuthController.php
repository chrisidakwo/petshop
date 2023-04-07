<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Controllers\BaseAuthController;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class AuthController extends BaseAuthController
{
    /**
     * Login an admin account
     *
     * @OA\Post(
     *     path="/api/v1/admin/login",
     *     tags={"Admin"},
     *     summary="Login an admin account",
     *     operationId="admin/login",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 required={"email", "password"},
     *                 @OA\Property(
     *                     property="email",
     *                     description="Admin email address",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     description="Admin password",
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
     * Logout an admin account
     *
     * @OA\Get(
     *     path="/api/v1/admin/logout",
     *     tags={"Admin"},
     *     summary="Logout an admin account",
     *     operationId="admin/logout",
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
            'is_admin' => 1,
        ];
    }
}
