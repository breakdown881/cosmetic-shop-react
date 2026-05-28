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
        Schema::create('shopping_cart', function (Blueprint $table) {
            $table->id();
            $table->string('identifier', 255)->nullable(false)->index('INDENTIFIER');
            $table->string('instance', 255)->nullable(false);
            $table->longText('content')->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shopping_cart');
    }
};
