<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement('ALTER TABLE orders MODIFY customer_id BIGINT NULL');
        DB::statement('ALTER TABLE customer_checkout_requests MODIFY customer_id BIGINT UNSIGNED NULL');
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::table('orders')->whereNull('customer_id')->update(['customer_id' => 0]);
        DB::table('customer_checkout_requests')->whereNull('customer_id')->update(['customer_id' => 0]);

        DB::statement('ALTER TABLE orders MODIFY customer_id BIGINT NOT NULL');
        DB::statement('ALTER TABLE customer_checkout_requests MODIFY customer_id BIGINT UNSIGNED NOT NULL');
    }
};
