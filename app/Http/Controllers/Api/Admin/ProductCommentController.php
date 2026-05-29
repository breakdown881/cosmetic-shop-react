<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateCommentActiveRequest;
use App\Services\Admin\ProductCommentService;
use Illuminate\Http\JsonResponse;

class ProductCommentController extends Controller
{
    public function __construct(private readonly ProductCommentService $comments) {}

    public function all(): JsonResponse
    {
        return response()->json([
            'data' => $this->comments->all(),
        ]);
    }

    public function index($product): JsonResponse
    {
        return response()->json([
            'data' => $this->comments->allForProduct($product),
        ]);
    }

    public function update(UpdateCommentActiveRequest $request, $product, $comment): JsonResponse
    {
        return response()->json([
            'message' => __('translate.updateSuccess'),
            'data' => $this->comments->updateForProduct($product, $comment, $request->validated()),
        ]);
    }

    public function updateAny(UpdateCommentActiveRequest $request, $comment): JsonResponse
    {
        return response()->json([
            'message' => __('translate.updateSuccess'),
            'data' => $this->comments->update($comment, $request->validated()),
        ]);
    }
}
