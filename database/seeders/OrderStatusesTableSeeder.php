<?php

namespace Database\Seeders;

use App\Models\OrderStatus;
use Illuminate\Database\Seeder;

class OrderStatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = ['Open', 'Pending Payment', 'Paid', 'Shipped', 'Cancelled'];

        foreach ($statuses as $status) {
            OrderStatus::query()->create([
                'title' => $status,
            ]);
        }
    }
}
