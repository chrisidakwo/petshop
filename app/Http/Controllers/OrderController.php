<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\Order\OrderResource;
use App\Http\Resources\Order\OrderResourceCollection;
use App\Http\Services\OrderService;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(private OrderService $orderService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $page = (int) $request->get('page', 1);
        $limit = (int) $request->get('limit', 10);
        $sortBy = $request->get('sortBy');
        $desc = $request->boolean('desc');

        $orders = $this->orderService->list(
            page: $page,
            limit: $limit,
            sortColumn: $sortBy,
            sortDesc: $desc,
        );

        return OrderResourceCollection::make($orders)->toResponse($request);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        $order = $this->orderService->create($request->validated())
            ->load(['orderStatus', 'payment', 'user']);

        return $this->response(
            OrderResource::make($order)->toArray($request),
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Order $order): JsonResponse
    {
        return $this->response(
            OrderResource::make($order)->toArray($request),
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderRequest $request, Order $order): JsonResponse
    {
        $order =  $this->orderService->update($order, $request->validated())
            ->load(['orderStatus', 'payment', 'user']);

        return $this->response(
            OrderResource::make($order)->toArray($request),
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order): JsonResponse
    {
        $result = $this->orderService->delete($order);

        if (! $result) {
            return $this->error('Could not delete order');
        }

        return $this->response();
    }
}
