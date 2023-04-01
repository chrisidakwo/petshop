<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Auth\Jwt;
use App\Exceptions\JwtException;
use App\Http\Controllers\Controller;
use App\Http\Requests\DeleteUserRequest;
use App\Http\Requests\StoreAdminUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\Admin\UserResource;
use App\Http\Services\UserService;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function __construct(
        private UserService $userService,
        private Jwt $jwt,
    ){ }

    /**
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

    public function delete(DeleteUserRequest $request, User $user): JsonResponse
    {
        $result = $this->userService->delete($user);

        if ($result === false) {
            return $this->error('Could not delete user');
        }

        return $this->response();
    }
}
