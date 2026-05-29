<?php

namespace App\Repositories\Customer;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CustomerProductDetailRepository
{
    public function findActive(int|string $id): Product
    {
        return Cache::remember('customer:products:detail:' . $id, $this->ttl(), fn () => Product::query()
            ->with(['brand:id,name,status', 'category:id,name,parent_id,status'])
            ->where('status', 1)
            ->findOrFail($id));
    }

    public function related(Product $product, int $limit = 4): Collection
    {
        return Cache::remember($this->relatedCacheKey($product, $limit), $this->ttl(), fn () => Product::query()
            ->with(['brand:id,name,status', 'category:id,name,parent_id,status'])
            ->where('status', 1)
            ->where('id', '!=', $product->id)
            ->where(function ($query) use ($product) {
                $query
                    ->where('category_id', $product->category_id)
                    ->orWhere('brand_id', $product->brand_id);
            })
            ->orderByDesc('featured')
            ->latest()
            ->limit($limit)
            ->get());
    }

    public function mediaUrls(array $mediaIds): array
    {
        if ($mediaIds === []) {
            return [];
        }

        return Cache::remember($this->mediaCacheKey('detail', $mediaIds), $this->ttl(), fn () => DB::table('media')
            ->whereIn('id', $mediaIds)
            ->get(['id', 'disk', 'file_name'])
            ->mapWithKeys(fn (object $media) => [
                $media->id => Storage::disk($media->disk ?: 'public')->url($media->file_name),
            ])
            ->all());
    }

    private function relatedCacheKey(Product $product, int $limit): string
    {
        return 'customer:products:related:' . md5(json_encode([
            'product_id' => $product->id,
            'brand_id' => $product->brand_id,
            'category_id' => $product->category_id,
            'limit' => $limit,
        ]));
    }

    private function ttl(): int
    {
        return (int) config('cache.customer_catalog_ttl', 600);
    }

    private function mediaCacheKey(string $scope, array $mediaIds): string
    {
        sort($mediaIds);

        return 'customer:' . $scope . ':media:' . md5(implode(',', $mediaIds));
    }
}
