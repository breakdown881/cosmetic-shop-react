<?php

namespace App\Services\Customer;

use App\Models\Product;
use App\Repositories\Customer\CustomerProductDetailRepository;

class CustomerProductDetailService
{
    public function __construct(
        private readonly CustomerProductDetailRepository $detailRepository,
        private readonly CustomerNavigationService $navigationService,
    ) {}

    public function showProps(int|string $productId): array
    {
        $product = $this->detailRepository->findActive($productId);
        $relatedProducts = $this->detailRepository->related($product);
        $mediaUrls = $this->detailRepository->mediaUrls(
            collect([$product])
                ->merge($relatedProducts)
                ->pluck('media_id')
                ->filter()
                ->unique()
                ->values()
                ->all()
        );

        return [
            'title' => $product->name,
            'navItems' => $this->navigationService->navItems(),
            'product' => $this->formatProduct($product, $mediaUrls),
            'relatedProducts' => $relatedProducts
                ->map(fn (Product $relatedProduct) => $this->formatProduct($relatedProduct, $mediaUrls))
                ->values(),
        ];
    }

    private function formatProduct(Product $product, array $mediaUrls): array
    {
        $price = (int) $product->price;
        $discountPercentage = max(0, min(100, (int) $product->discount_percentage));
        $salePrice = $discountPercentage > 0
            ? (int) round($price * (100 - $discountPercentage) / 100)
            : $price;

        return [
            'id' => $product->id,
            'name' => $product->name,
            'brand_id' => $product->brand_id,
            'brand_name' => $product->brand?->name,
            'category_id' => $product->category_id,
            'category_name' => $product->category?->name,
            'price' => $price,
            'sale_price' => $salePrice,
            'discount_percentage' => $discountPercentage,
            'inventory_qty' => (int) $product->inventory_qty,
            'description' => $product->description,
            'star' => $product->star,
            'featured' => (bool) $product->featured,
            'featured_image' => $mediaUrls[$product->media_id] ?? asset('adm/images/godakeben450x170.jpg'),
            'url' => '/products/' . $product->id,
        ];
    }
}
