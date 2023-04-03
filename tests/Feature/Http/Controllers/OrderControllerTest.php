<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testItCreatesANewOrder(): void
    {
        $user = $this->createPredictableRegularUser();

        $this->actingAs($user);

        $products = Product::factory(3)->create();

        $requestData = [
            'order_status_uuid' => OrderStatus::factory()->create()->uuid,
            'payment_uuid' => Payment::factory()->create()->uuid,
            'products' => [
                [
                    'product_id' => $products[2]->uuid,
                    'quantity' => 3,
                ],
                [
                    'product_id' => $products[0]->uuid,
                    'quantity' => 1,
                ],
                [
                    'product_id' => $products[1]->uuid,
                    'quantity' => 7,
                ],
            ],
            'billing' => $this->faker->streetAddress(),
            'shipping' => $this->faker->streetAddress(),
        ];

        $response = $this->postJson(route('api.order.store'), $requestData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'uuid',
                    'products' => [
                        [
                            'uuid',
                            'price',
                            'product',
                            'quantity',
                        ],
                    ],
                    'address' => [
                        'billing',
                        'shipping',
                    ],
                    'delivery_fee',
                    'amount',
                    'order_status' => [
                        'uuid',
                        'title',
                    ],
                    'user' => [
                        'uuid',
                        'first_name',
                        'last_name',
                    ],
                    'payment' => [
                        'uuid',
                        'type',
                        'details',
                    ],
                ],
            ]);
    }

    public function testItUpdatesAnOrderRecord(): void
    {
        $user = $this->createPredictableRegularUser();

        $this->actingAs($user);

        OrderStatus::factory(3)->create();
        $order = Order::factory()->create([
            'products' => Product::factory(3)
                ->create()
                ->map(function ($product) {
                    return [
                        'product_id' => $product->uuid,
                        'quantity' => $this->faker->randomNumber(1, 8),
                    ];
                })
                ->toArray(),
        ]);

        $this->assertCount(3, $order->products);

        $requestData = [
            'order_status_uuid' => $order->order_status_uuid,
            'payment_uuid' => Payment::factory()->create([
                'type' => Payment::TYPE_CREDIT_CARD,
            ])->uuid,
            'products' => array_merge($order->getOriginalProducts(), [
                [
                    'product_id' => Product::factory()->create()->uuid,
                    'quantity' => 6,
                ],
                [
                    'product_id' => Product::factory()->create()->uuid,
                    'quantity' => 3,
                ],
            ]),
            'billing' => $order->address['billing'],
            'shipping' => $order->address['shipping'],
        ];

        $response = $this->putJson(route('api.order.update', $order->uuid), $requestData);

        // products list got updated
        $this->assertCount(5, $response->json('data.products'));
        $this->assertGreaterThan($order->amount, $response->json('data.amount'));

        $response->assertStatus(200);
    }

    public function testItDeletesOrder(): void
    {
        $user = $this->createPredictableRegularUser();

        $this->actingAs($user);

        OrderStatus::factory()->create();
        $order = Order::factory()->create();

        $this->assertDatabaseCount('payments', 1);

        $response = $this->deleteJson(route('api.order.destroy', $order->uuid));

        $response->assertStatus(200);

        $this->assertDatabaseCount('payments', 0);
        $this->assertDatabaseCount('orders', 0);
    }
}
