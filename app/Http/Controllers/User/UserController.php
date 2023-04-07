<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Auth\Jwt;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Exceptions\JwtException;
use Illuminate\Http\JsonResponse;
use App\Http\Services\UserService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\DeleteUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\User\UserResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="User",
 *     description="User API endpoint"
 * )
 */
class UserController extends Controller
{
    public function __construct(
        private UserService $userService,
        private Jwt $jwt,
    ) {
    }

    /**
     * Create a user account
     *
     * @OA\Post(
     *     path="/api/v1/user/create",
     *     tags={"User"},
     *     summary="Create a user account",
     *     operationId="user/create",
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
     *         response=422,
     *         description="Unprocessable Entity"
     *     ),
     * )
     *
     * @throws JwtException
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = $this->userService->create($request->validated());

        $user->token = $this->jwt->generateTokenFromUser($user);

        return $this->response(UserResource::make($user)->toArray($request), 201);
    }

    /**
     * View a user account
     *
     * @OA\Get(
     *     path="/api/v1/user",
     *     tags={"User"},
     *     summary="View a user account",
     *     operationId="user/show",
     *     security={{"bearerAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *     ),
     * )
     */
    public function show(Request $request): JsonResponse
    {
        $user = UserResource::make($request->user())->toArray($request);

        return $this->response(Arr::except($user, ['token']));
    }

    /**
     * Update a user account
     *
     * @OA\Put(
     *     path="/api/v1/user/edit",
     *     tags={"User"},
     *     summary="Update a user account",
     *     operationId="user/update",
     *     security={{"bearerAuth": {} }},
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
    public function update(UpdateUserRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $user = $this->userService->update($user, $request->validated());

        if (Auth::id() === $user->uuid) {
            $user->token = $this->jwt->generateTokenFromUser($user);
        }

        return $this->response(UserResource::make($user)->toArray($request));
    }

    /**
     * Delete a user account
     *
     * @OA\Delete(
     *     path="/api/v1/user",
     *     tags={"User"},
     *     summary="Delete a user account",
     *     operationId="user/delete",
     *     security={{"bearerAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *     ),
     * )
     */
    public function delete(DeleteUserRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $result = $this->userService->delete($user);

        if ($result === false) {
            return $this->error('Could not delete user');
        }

        return $this->response();
    }
}
