<?php

namespace App\Repositories\Api;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ProductRepository
{
    public function all(Request $request): Collection
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

    public function find(int|string $id): Product
    {
        return Product::findOrFail($id);
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(Product $product, array $data): Product
    {
        $product->update($data);

        return $product;
    }

    public function delete(Product $product): void
    {
        $product->delete();
    }

    public function loadForAdmin(Product $product): Product
    {
        return $product->load(['brand:id,name,status', 'category:id,name,parent_id,status']);
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
