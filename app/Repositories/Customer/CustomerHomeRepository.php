<?php

namespace App\Repositories\Customer;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CustomerHomeRepository
{
    public function categories(): Collection
    {
        return Cache::remember('customer:home:categories', $this->ttl(), fn () => Category::query()
            ->select(['id', 'name', 'parent_id'])
            ->orderBy('parent_id')
            ->orderBy('name')
            ->get());
    }

    public function featuredProducts(): Collection
    {
        return Cache::remember('customer:home:featured-products', $this->ttl(), fn () => Product::query()
            ->select([
                'id',
                'name',
                'category_id',
                'price',
                'discount_percentage',
                'media_id',
                'inventory_qty',
                'star',
                'featured',
                'created_at',
            ])
            ->orderByDesc('featured')
            ->latest()
            ->get());
    }

    public function mediaUrls(array $mediaIds): array
    {
        if ($mediaIds === []) {
            return [];
        }

        return Cache::remember($this->mediaCacheKey('home', $mediaIds), $this->ttl(), fn () => DB::table('media')
            ->whereIn('id', $mediaIds)
            ->get(['id', 'disk', 'file_name'])
            ->mapWithKeys(fn (object $media) => [
                $media->id => Storage::disk($media->disk ?: 'public')->url($media->file_name),
            ])
            ->all());
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
