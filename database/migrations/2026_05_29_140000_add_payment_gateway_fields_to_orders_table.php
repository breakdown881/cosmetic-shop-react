<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'payment_gateway')) {
                $table->string('payment_gateway', 30)->nullable()->after('payment_method');
            }

            if (! Schema::hasColumn('orders', 'payment_status')) {
                $table->string('payment_status', 30)->default('UNPAID')->after('payment_gateway');
            }

            if (! Schema::hasColumn('orders', 'payment_reference')) {
                $table->string('payment_reference', 100)->nullable()->after('payment_status');
            }

            if (! Schema::hasColumn('orders', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('payment_reference');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $columns = array_values(array_filter([
                Schema::hasColumn('orders', 'payment_gateway') ? 'payment_gateway' : null,
                Schema::hasColumn('orders', 'payment_status') ? 'payment_status' : null,
                Schema::hasColumn('orders', 'payment_reference') ? 'payment_reference' : null,
                Schema::hasColumn('orders', 'paid_at') ? 'paid_at' : null,
            ]));

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
