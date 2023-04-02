<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\Blog\PostResource;
use App\Http\Resources\Blog\PostResourceCollection;
use App\Http\Services\BlogService;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function __construct(private BlogService $blogService)
    {
    }

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

    public function viewPost(Request $request, Post $post): JsonResponse
    {
        return $this->response(
            PostResource::make($post)->toArray($request)
        );
    }
}
