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
        return response()->json([
            'data' => $this->products($request),
        ]);
    }

    public function store(CreateProductRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['created_by'] = $request->user('admin')?->id ?? $request->user()?->id ?? 1;

        $product = Product::create($data);

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

    public function updateStatus(Request $request, Product $product): JsonResponse
    {
        $data = $request->validate([
            'status' => ['required', 'integer', 'in:0,1'],
        ]);

        $product->update(['status' => $data['status']]);

        return response()->json([
            'message' => __('translate.changeStatusSuccess'),
            'data' => $product->load(['brand:id,name,status', 'category:id,name,parent_id,status']),
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $this->products($request),
        ]);
    }

    private function products(Request $request)
    {
        if (! $this->hasFilters($request)) {
            return Product::query()
                ->with(['brand:id,name,status', 'category:id,name,parent_id,status'])
                ->latest()
                ->get();
        }

        $search = Product::search((string) $request->input('q', ''));

        if ($request->filled('category_id')) {
            $search->where('category_id', $request->integer('category_id'));
        }

        if ($request->filled('brand_id')) {
            $search->where('brand_id', $request->integer('brand_id'));
        }

        if ($request->filled('featured')) {
            $search->where('featured', $request->boolean('featured'));
        }

        if ($request->filled('status')) {
            $search->where('status', $request->integer('status'));
        }

        return $search->query(function ($query) use ($request) {
            $query->with(['brand:id,name,status', 'category:id,name,parent_id,status'])
                ->latest();

            $this->applyDatabaseFilters($query, $request);
        })->get();
    }

    private function hasFilters(Request $request): bool
    {
        return $request->filled('q')
            || $request->filled('category_id')
            || $request->filled('brand_id')
            || $request->filled('featured')
            || $request->filled('status');
    }

    private function applyDatabaseFilters($query, Request $request): void
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

        if ($request->filled('status')) {
            $query->where('status', $request->integer('status'));
        }
    }
}
