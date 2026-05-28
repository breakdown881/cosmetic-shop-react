<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'feeship_id')) {
                $table->unsignedBigInteger('feeship_id')->nullable()->after('shipping_fee');
            }

            if (! Schema::hasColumn('orders', 'note')) {
                $table->text('note')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $columns = array_values(array_filter([
                Schema::hasColumn('orders', 'feeship_id') ? 'feeship_id' : null,
                Schema::hasColumn('orders', 'note') ? 'note' : null,
            ]));

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
