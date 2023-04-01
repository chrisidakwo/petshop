<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Auth\Jwt;
use App\Exceptions\JwtException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\Admin\UserResource;
use App\Http\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class UserController extends Controller
{
    public function __construct(
        private UserService $userService,
        private Jwt $jwt,
    ){ }

    /**
     * @throws JwtException
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = $this->userService->create($request->validated());

        $user->token = $this->jwt->generateTokenFromUser($user);

        return response()->json([
            'data' => UserResource::make($user)->toArray($request),
        ]);
    }

    public function show(Request $request): JsonResponse
    {
        $user = UserResource::make($request->user())->toArray($request);

        return $this->response(Arr::except($user, ['token']));
    }
}
