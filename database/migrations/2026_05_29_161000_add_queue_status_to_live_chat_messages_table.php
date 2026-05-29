<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('live_chat_messages', function (Blueprint $table) {
            if (! Schema::hasColumn('live_chat_messages', 'status')) {
                $table->string('status', 30)->default('PROCESSED')->after('message')->index();
            }

            if (! Schema::hasColumn('live_chat_messages', 'processed_at')) {
                $table->timestamp('processed_at')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('live_chat_messages', function (Blueprint $table) {
            $columns = array_values(array_filter([
                Schema::hasColumn('live_chat_messages', 'status') ? 'status' : null,
                Schema::hasColumn('live_chat_messages', 'processed_at') ? 'processed_at' : null,
            ]));

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
