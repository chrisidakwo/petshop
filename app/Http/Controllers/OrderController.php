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
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Orders",
 *     description="Orders API endpoint"
 * )
 */
class OrderController extends Controller
{
    public function __construct(private OrderService $orderService)
    {
    }

    /**
     * List all orders
     *
     * @OA\Get(
     *     path="/api/v1/orders",
     *     tags={"Orders"},
     *     summary="List all orders",
     *     operationId="orders/index",
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
     *
     * @OA\Post(
     *     path="/api/v1/order/create",
     *     tags={"Orders"},
     *     summary="Create a new order",
     *     operationId="order/create",
     *     security={{"bearerAuth": {} }},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 required={"order_status_uuid", "payment_uuid", "products", "address"},
     *                 @OA\Property(
     *                     property="order_status_uuid",
     *                     description="Order status UUID",
     *                     type="string",
     *                     format="uuid",
     *                 ),
     *                 @OA\Property(
     *                     property="payment_uuid",
     *                     description="Payment UUID",
     *                     type="string",
     *                     format="uuid",
     *                 ),
     *                 @OA\Property(
     *                     property="products",
     *                     description="Array of objects with product uuid and quantity",
     *                     type="array",
     *                     @OA\Items(
     *                         minItems=1,
     *                         @OA\Property(
     *                             property="uuid",
     *                             description="Product UUID",
     *                             type="string",
     *                             format="uuid",
     *                         ),
     *                         @OA\Property(
     *                             property="quantity",
     *                             description="Order quantity for product",
     *                             type="integer",
     *                         ),
     *                     ),
     *                 ),
     *                 @OA\Property(
     *                     property="address",
     *                     description="Billing and Shipping address",
     *                     type="object",
     *                     @OA\Property(
     *                             property="billing",
     *                             description="Order billing address",
     *                             type="string",
     *                         ),
     *                         @OA\Property(
     *                             property="shipping",
     *                             description="Order shipping address",
     *                             type="string",
     *                         ),
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *     )
     * )
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
     *
     * @OA\Get(
     *     path="/api/v1/order/{uuid}",
     *     tags={"Orders"},
     *     summary="Fetch an order",
     *     operationId="order/show",
     *     security={{"bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="uuid"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *     ),
     * )
     */
    public function show(Request $request, Order $order): JsonResponse
    {
        return $this->response(
            OrderResource::make($order)->toArray($request),
        );
    }

    /**
     * Download an order
     *
     * @OA\Get(
     *     path="/api/v1/order/{uuid}/download",
     *     tags={"Orders"},
     *     summary="Download an order",
     *     operationId="order/download",
     *     security={{"bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="uuid"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *     ),
     * )
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
     *
     * @OA\Put(
     *     path="/api/v1/order/{uuid}",
     *     tags={"Orders"},
     *     summary="Update an existing order",
     *     operationId="order/update",
     *     security={{"bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="uuid"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 required={"order_status_uuid", "payment_uuid", "products", "address"},
     *                 @OA\Property(
     *                     property="order_status_uuid",
     *                     description="Order status UUID",
     *                     type="string",
     *                     format="uuid",
     *                 ),
     *                 @OA\Property(
     *                     property="payment_uuid",
     *                     description="Payment UUID",
     *                     type="string",
     *                     format="uuid",
     *                 ),
     *                 @OA\Property(
     *                     property="products",
     *                     description="Array of objects with product uuid and quantity",
     *                     type="array",
     *                     @OA\Items(
     *                         minItems=1,
     *                         @OA\Property(
     *                             property="uuid",
     *                             description="Product UUID",
     *                             type="string",
     *                             format="uuid",
     *                         ),
     *                         @OA\Property(
     *                             property="quantity",
     *                             description="Order quantity for product",
     *                             type="integer",
     *                         ),
     *                     ),
     *                 ),
     *                 @OA\Property(
     *                     property="address",
     *                     description="Billing and Shipping address",
     *                     type="object",
     *                     @OA\Property(
     *                             property="billing",
     *                             description="Order billing address",
     *                             type="string",
     *                         ),
     *                         @OA\Property(
     *                             property="shipping",
     *                             description="Order shipping address",
     *                             type="string",
     *                         ),
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity"
     *     ),
     * )
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
     *
     * @OA\Delete(
     *     path="/api/v1/order/{uuid}",
     *     tags={"Orders"},
     *     summary="Delete an existing order",
     *     operationId="order/delete",
     *     security={{"bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="uuid"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity"
     *     ),
     * )
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
