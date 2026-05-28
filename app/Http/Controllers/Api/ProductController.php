<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateProductRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Product::query()
            ->with(['brand:id,name,status', 'category:id,name,parent_id,status'])
            ->latest();

        $this->applyFilters($query, $request);

        return response()->json([
            'data' => $query->get(),
        ]);
    }

    public function store(CreateProductRequest $request): JsonResponse
    {
        $product = Product::create($request->validated());

        return response()->json([
            'message' => __('translate.createSuccess'),
            'data' => $product->load(['brand:id,name,status', 'category:id,name,parent_id,status']),
        ], 201);
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json([
            'data' => $product->load(['brand:id,name,status', 'category:id,name,parent_id,status']),
        ]);
    }

    public function update(CreateProductRequest $request, Product $product): JsonResponse
    {
        $product->update($request->validated());

        return response()->json([
            'message' => __('translate.updateSuccess'),
            'data' => $product->load(['brand:id,name,status', 'category:id,name,parent_id,status']),
        ]);
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return response()->json(null, 204);
    }

    public function search(Request $request): JsonResponse
    {
        $query = Product::query()
            ->with(['brand:id,name,status', 'category:id,name,parent_id,status'])
            ->latest();

        $this->applyFilters($query, $request);

        return response()->json([
            'data' => $query->get(),
        ]);
    }

    private function applyFilters($query, Request $request): void
    {
        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->input('q') . '%');
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->integer('category_id'));
        }

        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->integer('brand_id'));
        }

        if ($request->filled('featured')) {
            $query->where('featured', $request->boolean('featured'));
        }
    }
}
