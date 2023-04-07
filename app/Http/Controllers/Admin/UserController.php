<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Services\UserService;
use App\Http\Controllers\Controller;
use App\Http\Resources\User\UserResourceCollection;
use Fouladgar\EloquentBuilder\Exceptions\NotFoundFilterException;
use OpenApi\Annotations as OA;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * List all users
     *
     * @OA\Get(
     *     path="/api/v1/admin/user-listing",
     *     tags={"Admin"},
     *     summary="List all users",
     *     operationId="admin/user-listing",
     *     security={{"bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sortBy",
     *         in="query",
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="desc",
     *         in="query",
     *         @OA\Schema(
     *             type="boolean",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="first_name",
     *         in="query",
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="phone",
     *         in="query",
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="address",
     *         in="query",
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="created_at",
     *         in="query",
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="marketing",
     *         in="query",
     *         @OA\Schema(
     *             type="boolean",
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
