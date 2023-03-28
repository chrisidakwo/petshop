<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    /**
     * @throws Exception
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->create()->id,
            'order_status_id' => OrderStatus::factory()->create()->id,
            'payment_id' => Payment::factory()->create()->id,

            'products' => Product::factory(random_int(1, 5))
                ->create()
                ->map(function ($product) {
                    return [
                        'product_id' => $product->uuid,
                        'quantity' => $this->faker->randomNumber(1, 8),
                    ];
                })
                ->toArray(),
            'address' => [
                'shipping' => $this->faker->streetAddress(),
                'billing' => $this->faker->streetAddress(),
            ],
            'delivery_fee' => $this->faker->randomFloat(2, 0, 80),
            'amount' => $this->faker->randomFloat(2, 250, 800),
        ];
    }

    public function shipped(): static
    {
        return $this->state(fn () => [
            'shipped_at' => now(),
        ]);
    }

    public function orderStatus(int $id): static
    {
        return $this->state(function () use ($id) {
            return [
                'order_status_id' => $id,
            ];
        });
    }
}
