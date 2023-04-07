<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Services\BlogService;
use App\Http\Resources\Blog\PostResource;
use App\Http\Resources\Blog\PostResourceCollection;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="MainPage",
 *     description="MainPage API endpoint"
 * )
 */
class PostController extends Controller
{
    public function __construct(private BlogService $blogService)
    {
    }

    /**
     * List all posts
     *
     * @OA\Get(
     *     path="/api/v1/main/blog",
     *     tags={"MainPage"},
     *     summary="List all posts",
     *     operationId="main/blog",
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
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *     )
     * )
     */
    public function listPosts(Request $request): JsonResponse
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 10);
        $sortBy = $request->get('sortBy');
        $desc = $request->boolean('desc');

        $posts = $this->blogService->listPosts(
            page: $page,
            limit: $limit,
            sortColumn: $sortBy,
            sortDesc: $desc,
        );

        return PostResourceCollection::make($posts)->toResponse($request);
    }

    /**
     * Fetch a post
     *
     * @OA\Get(
     *     path="/api/v1/main/blog/{uuid}",
     *     tags={"MainPage"},
     *     summary="Fetch a post",
     *     operationId="main/blog/show",
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
     *     ),
     * )
     */
    public function viewPost(Request $request, Post $post): JsonResponse
    {
        return $this->response(
            PostResource::make($post)->toArray($request)
        );
    }
}
