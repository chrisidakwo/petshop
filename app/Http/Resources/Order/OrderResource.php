<?php

declare(strict_types=1);

namespace App\Http\Resources\Order;

use App\Http\Resources\OrderStatus\OrderStatusResource;
use App\Http\Resources\Payment\PaymentResource;
use App\Http\Resources\User\UserResource;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Order
 */
class OrderResource extends JsonResource
{
    public static $wrap = null;
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'products' => $this->products,
            'address' => $this->address,
            'delivery_fee' => $this->delivery_fee,
            'amount' => $this->amount,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'shipped_at' => $this->shipped_at,

            'order_status' => new OrderStatusResource($this->whenLoaded('orderStatus')),
            'user' => new UserResource($this->whenLoaded('user')),
            'payment' => new PaymentResource($this->whenLoaded('payment')),
        ];
    }
}
