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
     * Update a user account
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
