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

class UserController extends Controller
{
    public function __construct(
        private UserService $userService,
        private Jwt $jwt,
    ) {
    }

    /**
     * @throws JwtException
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = $this->userService->create($request->validated());

        $user->token = $this->jwt->generateTokenFromUser($user);

        return $this->response(UserResource::make($user)->toArray($request), 201);
    }

    public function show(Request $request): JsonResponse
    {
        $user = UserResource::make($request->user())->toArray($request);

        return $this->response(Arr::except($user, ['token']));
    }

    /**
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
