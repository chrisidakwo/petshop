<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Services\BlogService;
use App\Http\Resources\Blog\PostResource;
use App\Http\Resources\Blog\PostResourceCollection;

class PostController extends Controller
{
    public function __construct(private BlogService $blogService)
    {
    }

    /**
     * List all posts
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
     */
    public function viewPost(Request $request, Post $post): JsonResponse
    {
        return $this->response(
            PostResource::make($post)->toArray($request)
        );
    }
}
