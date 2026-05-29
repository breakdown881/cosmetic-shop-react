<?php

namespace App\Services\Admin;

use App\Models\Comment;
use App\Repositories\Admin\ProductCommentRepository;
use Illuminate\Support\Collection;

class ProductCommentService
{
    public function __construct(private readonly ProductCommentRepository $comments) {}

    public function all(): Collection
    {
        return $this->comments->all()
            ->map(fn (Comment $comment) => $this->format($comment));
    }

    public function allForProduct(int|string $productId): Collection
    {
        return $this->comments->allForProduct($productId)
            ->map(fn (Comment $comment) => $this->format($comment));
    }

    public function updateForProduct(int|string $productId, int|string $commentId, array $data): array
    {
        $comment = $this->comments->find($commentId);

        abort_if((int) $comment->product_id !== (int) $productId, 404);

        return $this->format($this->comments->update($comment, $data));
    }

    public function update(int|string $commentId, array $data): array
    {
        return $this->format($this->comments->update($this->comments->find($commentId), $data));
    }

    private function format(Comment $comment): array
    {
        return [
            'id' => $comment->id,
            'product_id' => $comment->product_id,
            'product_name' => $comment->product?->name,
            'email' => $comment->email,
            'fullname' => $comment->fullname,
            'star' => $comment->star,
            'description' => $comment->description,
            'active' => (int) $comment->active,
            'created_at' => optional($comment->created_at)->toDateTimeString(),
            'updated_at' => optional($comment->updated_at)->toDateTimeString(),
        ];
    }
}
