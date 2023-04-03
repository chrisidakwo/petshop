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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('category_uuid')
                ->references('uuid')
                ->on('categories');
            $table->uuid()->unique();
            $table->string('title');
            $table->double('price', 12, 2);
            $table->text('description');
            $table->json('metadata');

            $brandUuid = \Illuminate\Support\Facades\DB::connection()
                ->getQueryGrammar()
                ->wrap('metadata->brand');

            $table->uuid('brand_uuid')->storedAs($brandUuid);

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
