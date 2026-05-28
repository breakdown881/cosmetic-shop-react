<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateCommentActiveRequest;
use App\Models\Comment;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class ProductCommentController extends Controller
{
    public function index(Product $product): JsonResponse
    {
        return response()->json([
            'data' => $product->comments()
                ->with('product:id,name')
                ->latest()
                ->get()
                ->map(fn (Comment $comment) => $this->formatComment($comment)),
        ]);
    }

    public function update(UpdateCommentActiveRequest $request, Product $product, Comment $comment): JsonResponse
    {
        abort_if((int) $comment->product_id !== (int) $product->id, 404);

        $comment->update($request->validated());

        return response()->json([
            'message' => __('translate.updateSuccess'),
            'data' => $this->formatComment($comment->refresh()->load('product:id,name')),
        ]);
    }

    private function formatComment(Comment $comment): array
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
