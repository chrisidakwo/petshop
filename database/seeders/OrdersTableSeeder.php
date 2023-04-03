<?php

namespace Database\Seeders;

use App\Models\Order;
use Illuminate\Database\Seeder;

class OrdersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orders = Order::factory(5)->create();

        $orders->each(function (Order $order) {
            $orderProducts = $order->products;
            $orderAmount = 0;

            foreach ($orderProducts as $orderProduct) {
                $orderAmount += $orderProduct['price'] * $orderProduct['quantity'];
            }

            $order->amount = $orderAmount;
            $order->save();
        });
    }
}
