<?php

namespace App\Http\Controllers;

use App\Http\Resources\PromotionResourceCollection;
use App\Http\Services\PromotionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MainController extends Controller
{
    public function __construct(
        private PromotionService $promotionService
    ) {
    }

    public function promotions(Request $request): JsonResponse
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 10);
        $sortBy = $request->get('sortBy');
        $desc = $request->boolean('desc');
        $valid = $request->boolean('valid');

        $promotions = $this->promotionService->list(
            fields: [ 'valid' => $valid ],
            page: $page,
            limit: $limit,
            sortColumn: $sortBy,
            sortDesc: $desc,
        );

        return PromotionResourceCollection::make($promotions)->toResponse($request);
    }
}