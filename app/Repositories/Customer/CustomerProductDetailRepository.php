<?php

namespace App\Repositories\Customer;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CustomerProductDetailRepository
{
    public function findActive(int|string $id): Product
    {
        return Product::query()
            ->with(['brand:id,name,status', 'category:id,name,parent_id,status'])
            ->where('status', 1)
            ->findOrFail($id);
    }

    public function related(Product $product, int $limit = 4): Collection
    {
        return Product::query()
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
