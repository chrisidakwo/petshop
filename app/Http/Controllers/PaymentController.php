<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Http\Services\PaymentService;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Resources\Payment\PaymentResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Payments",
 *     description="Payments API endpoint"
 * )
 */
class PaymentController extends Controller
{
    public function __construct(private PaymentService $paymentService)
    {
    }

    /**
     * Create a new payment
     *
     * @OA\Post(
     *     path="/api/v1/payment/create",
     *     tags={"Payments"},
     *     summary="Create a new payment",
     *     operationId="payment/create",
     *     security={{"bearerAuth": {} }},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 required={"type", "details"},
     *                 @OA\Property(
     *                     property="type",
     *                     description="Payment type",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="details",
     *                     description="Payment details for select type",
     *                     type="object",
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
    public function store(StorePaymentRequest $request): JsonResponse
    {
        $payment = $this->paymentService->create($request->validated());

        return $this->response(
            PaymentResource::make($payment)->toArray($request),
            201,
        );
    }
}
