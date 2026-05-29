<?php

namespace App\Repositories\Customer;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CustomerCartRepository
{
    public function findActiveProduct(int|string $productId): Product
    {
        return Product::query()
            ->select([
                'id',
                'name',
                'price',
                'discount_percentage',
                'media_id',
                'inventory_qty',
                'status',
            ])
            ->where('status', 1)
            ->findOrFail($productId);
    }

    public function activeProductsByIds(array $productIds): Collection
    {
        if ($productIds === []) {
            return collect();
        }

        return Product::query()
            ->select([
                'id',
                'name',
                'price',
                'discount_percentage',
                'media_id',
                'inventory_qty',
                'status',
            ])
            ->where('status', 1)
            ->whereIn('id', $productIds)
            ->get();
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
