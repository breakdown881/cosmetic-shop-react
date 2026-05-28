<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'created_by')) {
                $table->unsignedInteger('created_by')->default(1)->index('CREATED_BY')->after('featured');
            }

            if (! Schema::hasColumn('products', 'status')) {
                $table->smallInteger('status')->default(0)->after('created_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'created_by')) {
                $table->dropIndex('CREATED_BY');
            }

            $columns = array_values(array_filter([
                Schema::hasColumn('products', 'created_by') ? 'created_by' : null,
                Schema::hasColumn('products', 'status') ? 'status' : null,
            ]));

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};



