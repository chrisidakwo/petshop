<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Services\PromotionService;
use App\Http\Resources\Promotion\PromotionResourceCollection;
use Fouladgar\EloquentBuilder\Exceptions\NotFoundFilterException;
use OpenApi\Annotations as OA;

class PromotionController extends Controller
{
    public function __construct(
        private PromotionService $promotionService,
    ) {
    }

    /**
     * List all promotions
     *
     * @OA\Get(
     *     path="/api/v1/main/promotions",
     *     tags={"MainPage"},
     *     summary="List all promotions",
     *     operationId="main/promotions/index",
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
