<?php

declare(strict_types=1);

namespace App\Http\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class OrderService
{
    public function list(int $page, int $limit, string|null $sortColumn, bool $sortDesc): LengthAwarePaginator
    {
        $sortDirection = $sortDesc ? 'desc' : 'asc';

        return Order::query()
            ->orderBy($sortColumn ? $sortColumn : 'created_at', $sortDirection)
            ->with(['orderStatus', 'user', 'payment'])
            ->paginate(
                perPage: $limit,
                page: $page,
            );
    }

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): Order
    {
        $orderAmount = $this->getOrderAmount($data);

        /** @var User $user */
        $user = Auth::user();

        return Order::query()->create([
            'user_id' => $user->id,
            'order_status_uuid' => $data['order_status_uuid'],
            'payment_uuid' => $data['payment_uuid'],
            'products' => $data['products'],
            'delivery_fee' => $data['delivery_fee'] ?? 0.00,
            'amount' => $orderAmount,
            'address' => [
                'billing' => $data['billing'],
                'shipping' => $data['shipping'],
            ],
        ])->refresh();
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(Order $order, array $data): Order
    {
        $orderAmount = $this->getOrderAmount($data);

        $order->fill([
            'order_status_uuid' => $data['order_status_uuid'],
            'payment_uuid' => $data['payment_uuid'],
            'products' => $data['products'],
            'amount' => $orderAmount,
            'address' => [
                'billing' => $data['billing'],
                'shipping' => $data['shipping'],
            ],
        ])->save();

        return $order->refresh();
    }

    public function delete(Order $order): ?bool
    {
        // Remove payment
        Payment::query()->whereUuid($order->payment_uuid)->delete();

        // Remove order
        return $order->delete();
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function getOrderAmount(array $data): float|int
    {
        $productsUuids = Arr::pluck($data['products'], 'product_id');

        /** @var float|int $orderAmount */
        return Product::query()->whereIn('uuid', $productsUuids)
            ->get()
            ->reduce(function (int|float $sum, Product $product, int $index) use ($data): float {
                $productOrderAmount = $product->price * $data['products'][$index]['quantity'];

                return (float) $sum + $productOrderAmount;
            }, 0);
    }
}
