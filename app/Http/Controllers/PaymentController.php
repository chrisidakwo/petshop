<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Http\Services\PaymentService;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Resources\Payment\PaymentResource;

class PaymentController extends Controller
{
    public function __construct(private PaymentService $paymentService)
    {
    }

    /**
     * Create a new payment
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
