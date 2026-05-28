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
        Schema::create('order_items', function (Blueprint $table) {
            $table->bigInteger('product_id')->nullable(false)->index('PRODUCT_ID');
            $table->bigInteger('order_id')->nullable(false)->index('ORDER_ID');
            $table->integer('qty')->nullable(false);
            $table->integer('unit_price')->nullable(false);
            $table->integer('total_price')->nullable(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
