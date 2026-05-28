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
            $table->string('code', 255)->nullable(false);
            $table->string('name', 255)->nullable(false)->index('NAME');
            $table->bigInteger('brand_id')->nullable(false);
            $table->bigInteger('category_id')->nullable(false);
            $table->bigInteger('price')->nullable(false);
            $table->bigInteger('discount_percentage')->nullable(false);
            $table->date('discount_from_date')->nullable(false);
            $table->date('discount_to_date')->nullable(false);
            $table->bigInteger('media_id')->nullable(false);
            $table->bigInteger('inventory_qty')->nullable(false);
            $table->string('description', 255)->nullable(false);
            $table->float('star');
            $table->tinyInteger('featured')->default(0)->comment('1: nổi bật');
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
