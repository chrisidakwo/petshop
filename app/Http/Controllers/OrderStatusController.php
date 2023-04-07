<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Services\OrderStatusService;
use App\Http\Resources\OrderStatus\OrderStatusResourceCollection;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Order Statuses",
 *     description="Order Statuses API endpoint"
 * )
 */
class OrderStatusController extends Controller
{
    public function __construct(private OrderStatusService $orderStatusService)
    {
    }

    /**
     * List all order statuses
     *
     * @OA\Get(
     *     path="/api/v1/order-statuses",
     *     tags={"Order Statuses"},
     *     summary="List all order statuses",
     *     operationId="order-status/index",
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
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $page = (int) $request->get('page', 1);
        $limit = (int) $request->get('limit', 10);
        $sortBy = $request->get('sortBy');
        $desc = $request->boolean('desc');

        $orderStatuses = $this->orderStatusService->list($page, $limit, $sortBy, $desc);

        return OrderStatusResourceCollection::make($orderStatuses)->toResponse($request);
    }
}
