<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResourceCollection;
use App\Http\Services\UserService;
use Fouladgar\EloquentBuilder\Exceptions\NotFoundFilterException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @throws NotFoundFilterException
     */
    public function index(Request $request): JsonResponse
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 10);
        $sortBy = $request->get('sortBy');
        $desc = $request->boolean('desc');

        $searchFields = $request->except(['page', 'limit', 'sortBy', 'desc']);

        return UserResourceCollection::make(
            $this->userService->list($searchFields, (int) $page, (int) $limit, $sortBy, $desc)
        )->toResponse($request);
    }
}
