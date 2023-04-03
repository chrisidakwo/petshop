<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->foreignUuid('order_status_uuid')
                ->references('uuid')
                ->on('order_statuses')
                ->cascadeOnDelete();

            $table->foreignUuid('payment_uuid')
                ->nullable()
                ->references('uuid')
                ->on('payments')
                ->cascadeOnDelete();

            $table->uuid()->unique();
            $table->json('products');
            $table->json('address');
            $table->double('delivery_fee', 8, 2)->nullable();
            $table->double('amount', 12, 2);
            $table->timestamp('shipped_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
