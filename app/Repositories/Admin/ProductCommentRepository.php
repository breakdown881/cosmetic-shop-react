<?php

namespace App\Repositories\Admin;

use App\Models\Comment;
use App\Models\Product;
use Illuminate\Support\Collection;

class ProductCommentRepository
{
    public function all(): Collection
    {
        return Comment::query()
            ->with('product:id,name')
            ->latest()
            ->get();
    }

    public function allForProduct(int|string $productId): Collection
    {
        return $this->product($productId)
            ->comments()
            ->with('product:id,name')
            ->latest()
            ->get();
    }

    public function find(int|string $id): Comment
    {
        return Comment::findOrFail($id);
    }

    public function update(Comment $comment, array $data): Comment
    {
        $comment->update($data);

        return $comment->refresh()->load('product:id,name');
    }

    private function product(int|string $id): Product
    {
        return Product::findOrFail($id);
    }
}
