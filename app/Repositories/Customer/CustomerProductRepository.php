<?php

namespace App\Repositories\Customer;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CustomerProductRepository
{
    public function categories(): Collection
    {
        return Category::query()
            ->select(['id', 'name', 'parent_id'])
            ->where('status', 1)
            ->orderBy('parent_id')
            ->orderBy('name')
            ->get();
    }

    public function brands(): Collection
    {
        return Brand::query()
            ->select(['id', 'name'])
            ->where('status', 1)
            ->orderBy('name')
            ->get();
    }

    public function findCategory(int|string $id): Category
    {
        return Category::query()
            ->select(['id', 'name', 'parent_id'])
            ->where('status', 1)
            ->findOrFail($id);
    }

    public function findBrand(int|string $id): Brand
    {
        return Brand::query()
            ->select(['id', 'name'])
            ->where('status', 1)
            ->findOrFail($id);
    }

    public function products(array $filters, int $perPage = 12): LengthAwarePaginator
    {
        $query = Product::query()
            ->with(['brand:id,name,status', 'category:id,name,parent_id,status'])
            ->select([
                'id',
                'name',
                'brand_id',
                'category_id',
                'price',
                'discount_percentage',
                'media_id',
                'inventory_qty',
                'description',
                'star',
                'featured',
                'status',
                'created_at',
            ])
            ->where('status', 1);

        if (! empty($filters['q'])) {
            $query->where('name', 'like', '%' . $filters['q'] . '%');
        }

        if (! empty($filters['category_id'])) {
            $query->where('category_id', (int) $filters['category_id']);
        }

        if (! empty($filters['brand_id'])) {
            $query->where('brand_id', (int) $filters['brand_id']);
        }

        if (isset($filters['price_min'])) {
            $query->where('price', '>=', (int) $filters['price_min']);
        }

        if (isset($filters['price_max'])) {
            $query->where('price', '<=', (int) $filters['price_max']);
        }

        if (isset($filters['rating_min'])) {
            $query->where('star', '>=', (float) $filters['rating_min']);
        }

        if (array_key_exists('featured', $filters)) {
            $query->where('featured', filter_var($filters['featured'], FILTER_VALIDATE_BOOLEAN));
        }

        if (array_key_exists('in_stock', $filters)) {
            $inStock = filter_var($filters['in_stock'], FILTER_VALIDATE_BOOLEAN);
            $inStock
                ? $query->where('inventory_qty', '>', 0)
                : $query->where('inventory_qty', '<=', 0);
        }

        match ($filters['sort'] ?? 'newest') {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'featured' => $query->orderByDesc('featured')->latest(),
            default => $query->latest(),
        };

        return $query->paginate($perPage)->appends($filters);
    }

    public function mediaUrls(array $mediaIds): array
    {
        if ($mediaIds === []) {
            return [];
        }

        return DB::table('media')
            ->whereIn('id', $mediaIds)
            ->get(['id', 'disk', 'file_name'])
            ->mapWithKeys(fn (object $media) => [
                $media->id => Storage::disk($media->disk ?: 'public')->url($media->file_name),
            ])
            ->all();
    }
}
