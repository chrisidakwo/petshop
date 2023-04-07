<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Services\PromotionService;
use App\Http\Resources\Promotion\PromotionResourceCollection;
use Fouladgar\EloquentBuilder\Exceptions\NotFoundFilterException;

class PromotionController extends Controller
{
    public function __construct(
        private PromotionService $promotionService,
    ) {
    }

    /**
     * List all promotions
     *
     * @throws NotFoundFilterException
     */
    public function __invoke(Request $request): JsonResponse
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
