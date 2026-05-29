<?php

namespace App\Repositories\Customer;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CustomerChatbotRepository
{
    public function activeProducts(): Collection
    {
        return Product::query()
            ->where('status', 1)
            ->where('inventory_qty', '>', 0)
            ->latest()
            ->limit(50)
            ->get();
    }

    public function logMessage(?int $userId, string $question, string $answer, string $intent, array $suggestions): void
    {
        DB::table('chatbot_messages')->insert([
            'user_id' => $userId,
            'question' => $question,
            'answer' => $answer,
            'intent' => $intent,
            'suggestions' => json_encode($suggestions),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
