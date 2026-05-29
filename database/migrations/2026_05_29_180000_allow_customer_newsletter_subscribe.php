<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement('ALTER TABLE news_letters MODIFY created_by INT UNSIGNED NULL');
        $this->removeDuplicateEmails();

        if (! $this->hasUniqueIndex()) {
            DB::statement('ALTER TABLE news_letters ADD UNIQUE customer_news_letters_email_unique (email)');
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        if ($this->hasUniqueIndex()) {
            DB::statement('ALTER TABLE news_letters DROP INDEX customer_news_letters_email_unique');
        }

        DB::table('news_letters')->whereNull('created_by')->update(['created_by' => 0]);
        DB::statement('ALTER TABLE news_letters MODIFY created_by INT UNSIGNED NOT NULL');
    }

    private function hasUniqueIndex(): bool
    {
        return collect(Schema::getIndexes('news_letters'))
            ->contains(fn (array $index) => $index['name'] === 'customer_news_letters_email_unique');
    }

    private function removeDuplicateEmails(): void
    {
        $duplicateEmails = DB::table('news_letters')
            ->select('email')
            ->groupBy('email')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('email');

        foreach ($duplicateEmails as $email) {
            $ids = DB::table('news_letters')
                ->where('email', $email)
                ->orderBy('id')
                ->pluck('id')
                ->all();

            DB::table('news_letters')
                ->whereIn('id', array_slice($ids, 1))
                ->delete();
        }
    }
};
