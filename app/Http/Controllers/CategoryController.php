<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\Category\CategoryResourceCollection;
use App\Http\Services\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(private CategoryService $categoryService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $page = (int) $request->get('page', 1);
        $limit = (int) $request->get('limit', 10);
        $sortBy = $request->get('sortBy');
        $desc = $request->boolean('desc');

        $categories = $this->categoryService->list(
            page: $page,
            limit: $limit,
            sortColumn: $sortBy,
            sortDesc: $desc,
        );

        return CategoryResourceCollection::make($categories)->toResponse($request);
    }
}
