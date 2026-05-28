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
            $table->bigInteger('staff_id')->index('STAFF_ID');
            $table->bigInteger('customer_id')->nullable(false)->index('CUSTOMER_ID');
            $table->string('shipping_fullname', 100)->nullable(false);
            $table->string('shipping_mobile', 15)->nullable(false);
            $table->integer('payment_method')->nullable(false)->comment('0:COD, 1: bank');
            $table->string('shipping_ward_id', 10)->nullable(false);
            $table->string('shipping_housenumber_street', 255)->nullable(false);
            $table->integer('shipping_fee')->nullable(false)->default(0);
            $table->date('delivered_date');
            $table->bigInteger('price_total')->nullable(false);
            $table->string('discount_code', 255);
            $table->bigInteger('discount_amount');
            $table->bigInteger('sub_total')->nullable(false);
            $table->bigInteger('tax')->nullable(false);
            $table->bigInteger('price_inc_tax_total')->nullable(false);
            $table->string('voucher_code', 255);
            $table->bigInteger('voucher_amount');
            $table->bigInteger('payment_total')->nullable(false);
            $table->string('status', 50)->nullable(false)->index('STATUS');
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
