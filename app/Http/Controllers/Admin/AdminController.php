<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Auth\Jwt;
use App\Models\User;
use App\Exceptions\JwtException;
use Illuminate\Http\JsonResponse;
use App\Http\Services\UserService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\DeleteUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\User\UserResource;
use App\Http\Requests\StoreAdminUserRequest;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Admin",
 *     description="Admin API endpoint"
 * )
 */
class AdminController extends Controller
{
    public function __construct(
        private UserService $userService,
        private Jwt $jwt,
    ) {
    }

    /**
     * Create an admin account
     *
     * @OA\Post(
     *     path="/api/v1/admin/create",
     *     tags={"Admin"},
     *     summary="Create an admin account",
     *     operationId="admin/create",
     *     security={{"bearerAuth": {} }},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 required={"first_name",  "last_name",  "email", "password", "password_confirmation", "avatar", "address", "phone_number"},
     *                 @OA\Property(
     *                     property="first_name",
     *                     description="User first name",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="last_name",
     *                     description="User last name",
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
     *                 @OA\Property(
     *                     property="avatar",
     *                     description="Avatar image UUID",
     *                     type="string",
     *                     format="uuid"
     *                 ),
     *                 @OA\Property(
     *                     property="address",
     *                     description="User main address",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="phone_number",
     *                     description="User main phone number",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="marketing",
     *                     description="User marketing preference",
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
     *         response=401,
     *         description="Unauthorized",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity"
     *     ),
     * )
     *
     * @throws JwtException
     */
    public function store(StoreAdminUserRequest $request): JsonResponse
    {
        $user = $this->userService->create(
            array_merge(
                $request->validated(),
                [
                    'is_admin' => 1,
                ],
            ),
        );

        $user->token = $this->jwt->generateTokenFromUser($user);

        return response()->json([
            'data' => UserResource::make($user)->toArray($request),
        ], 201);
    }

    /**
     * Edit a user account
     *
     * @OA\Put(
     *     path="/api/v1/admin/user-edit/{uuid}",
     *     tags={"Admin"},
     *     summary="Edit a user account",
     *     operationId="admin/update",
     *     security={{"bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="uuid"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 required={"first_name",  "last_name",  "email", "password", "password_confirmation", "address", "phone_number"},
     *                 @OA\Property(
     *                     property="first_name",
     *                     description="User first name",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="last_name",
     *                     description="User last name",
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
     *                 @OA\Property(
     *                     property="avatar",
     *                     description="Avatar image UUID",
     *                     type="string",
     *                     format="uuid"
     *                 ),
     *                 @OA\Property(
     *                     property="address",
     *                     description="User main address",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="phone_number",
     *                     description="User main phone number",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="marketing",
     *                     description="User marketing preference",
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
     *         response=401,
     *         description="Unauthorized",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity"
     *     ),
     * )
     *
     * @throws JwtException
     */
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $user = $this->userService->update($user, $request->validated());

        if (Auth::id() !== $user->id) {
            $user->token = $this->jwt->generateTokenFromUser($user);
        }

        return UserResource::make($user)->toResponse($request);
    }

    /**
     * Delete a user account
     *
     * @OA\Delete(
     *     path="/api/v1/admin/user-edit/{uuid}",
     *     tags={"Admin"},
     *     summary="Delete a user account",
     *     operationId="admin/delete",
     *     security={{"bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="uuid"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *     )
     * )
     *
     */
    public function delete(DeleteUserRequest $request, User $user): JsonResponse
    {
        $result = $this->userService->delete($user);

        if ($result === false) {
            return $this->error('Could not delete user');
        }

        return $this->response();
    }
}
