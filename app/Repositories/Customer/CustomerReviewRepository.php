<?php

namespace App\Repositories\Customer;

use App\Models\Comment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CustomerReviewRepository
{
    public function findActiveProduct(int|string $productId): Product
    {
        return Product::query()
            ->where('status', 1)
            ->findOrFail($productId);
    }

    public function approvedReviews(int $productId): Collection
    {
        return Comment::query()
            ->where('product_id', $productId)
            ->where('active', 1)
            ->latest()
            ->get();
    }

    public function hasReviewed(User $user, Product $product): bool
    {
        return Comment::query()
            ->where('product_id', $product->id)
            ->where('customer_id', $user->id)
            ->exists();
    }

    public function userPurchasedProduct(User $user, Product $product): bool
    {
        return DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.customer_id', $user->id)
            ->where('orders.status', 'COMPLETE')
            ->where('order_items.product_id', $product->id)
            ->exists();
    }

    public function createPending(User $user, Product $product, array $data): Comment
    {
        return Comment::query()->create([
            'product_id' => $product->id,
            'customer_id' => $user->id,
            'email' => $user->email,
            'fullname' => $user->name,
            'star' => (int) $data['star'],
            'description' => $data['description'],
            'active' => 0,
        ]);
    }
}
