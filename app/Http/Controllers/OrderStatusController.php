<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Services\OrderStatusService;
use App\Http\Resources\OrderStatus\OrderStatusResourceCollection;

class OrderStatusController extends Controller
{
    public function __construct(private OrderStatusService $orderStatusService)
    {
    }

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
