<?php

namespace App\Http\Controllers\Admin;

use App\Auth\Jwt;
use App\Exceptions\JwtException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdminUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\Admin\UserResource;
use App\Http\Services\UserService;
use App\Models\User;
use Illuminate\Http\JsonResponse;

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
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $user = $this->userService->update($user, $request->validated());

        return UserResource::make($user)->toResponse($request);
    }
}
