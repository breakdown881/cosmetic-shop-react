<?php

namespace App\Repositories\Customer;

use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CustomerWishlistRepository
{
    public function add(User $user, int $productId): void
    {
        DB::table('customer_wishlist')->updateOrInsert(
            [
                'user_id' => $user->id,
                'product_id' => $productId,
            ],
            [
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }

    public function remove(User $user, int $productId): void
    {
        DB::table('customer_wishlist')
            ->where('user_id', $user->id)
            ->where('product_id', $productId)
            ->delete();
    }

    public function productIds(User $user): array
    {
        return DB::table('customer_wishlist')
            ->where('user_id', $user->id)
            ->pluck('product_id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    public function has(User $user, int $productId): bool
    {
        return DB::table('customer_wishlist')
            ->where('user_id', $user->id)
            ->where('product_id', $productId)
            ->exists();
    }

    public function products(User $user): Collection
    {
        $productIds = $this->productIds($user);

        if ($productIds === []) {
            return collect();
        }

        return Product::query()
            ->whereIn('id', $productIds)
            ->where('status', 1)
            ->latest()
            ->get();
    }
}
