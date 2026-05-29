<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateProductRequest;
use App\Services\Api\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(private readonly ProductService $products) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $this->products->all($request),
        ]);
    }

    public function store(CreateProductRequest $request): JsonResponse
    {
        return response()->json([
            'message' => __('translate.createSuccess'),
            'data' => $this->products->create(
                $request->validated(),
                $request->user('admin')?->id ?? $request->user()?->id
            ),
        ], 201);
    }

    public function show($product): JsonResponse
    {
        return response()->json([
            'data' => $this->products->find($product),
        ]);
    }

    public function update(CreateProductRequest $request, $product): JsonResponse
    {
        return response()->json([
            'message' => __('translate.updateSuccess'),
            'data' => $this->products->update($product, $request->validated()),
        ]);
    }

    public function destroy($product): JsonResponse
    {
        $this->products->delete($product);

        return response()->json(null, 204);
    }

    public function updateStatus(Request $request, $product): JsonResponse
    {
        $data = $request->validate([
            'status' => ['required', 'integer', 'in:0,1'],
        ]);

        $product->update(['status' => $data['status']]);

        return response()->json([
            'message' => __('translate.changeStatusSuccess'),
            'data' => $this->products->updateStatus($product, (int) $data['status']),
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $this->products->all($request),
        ]);
    }
}
