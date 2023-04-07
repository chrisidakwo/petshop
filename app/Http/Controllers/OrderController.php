<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\Order\OrderResource;
use App\Http\Resources\Order\OrderResourceCollection;
use App\Http\Services\OrderService;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OrderController extends Controller
{
    public function __construct(private OrderService $orderService)
    {
    }

    /**
     * List all orders
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
     * Create a new order
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        $order = $this->orderService->create($request->validated())
            ->load(['orderStatus', 'payment', 'user']);

        return $this->response(
            OrderResource::make($order)->toArray($request),
            201
        );
    }

    /**
     * Fetch an order
     */
    public function show(Request $request, Order $order): JsonResponse
    {
        return $this->response(
            OrderResource::make($order)->toArray($request),
        );
    }

    /**
     * Download an order
     */
    public function download(Order $order): Response
    {
        $order = $order->load(['orderStatus', 'payment', 'user']);

        $orderSubTotal = $order->subTotalAmount();

        /** @var \Barryvdh\DomPDF\PDF $pdf */
        $pdf = Pdf::loadView('orders.receipt', compact('order', 'orderSubTotal'))
            ->setPaper('A4');

        return $pdf->download("{$order->uuid}.pdf");
    }

    /**
     * Update an existing order
     */
    public function update(UpdateOrderRequest $request, Order $order): JsonResponse
    {
        $order = $this->orderService->update($order, $request->validated())
            ->load(['orderStatus', 'payment', 'user']);

        return $this->response(
            OrderResource::make($order)->toArray($request),
        );
    }

    /**
     * Delete an existing order
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
