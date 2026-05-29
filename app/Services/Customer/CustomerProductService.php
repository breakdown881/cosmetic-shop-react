<?php

namespace App\Services\Customer;

use App\Repositories\Customer\CustomerProductRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CustomerProductService
{
    public function __construct(
        private readonly CustomerProductRepository $productRepository,
        private readonly CustomerNavigationService $navigationService,
    ) {}

    public function indexProps(array $filters = [], ?string $title = null): array
    {
        $products = $this->productRepository->products($this->normalizeFilters($filters));
        $mediaUrls = $this->productRepository->mediaUrls(
            $products->getCollection()->pluck('media_id')->filter()->unique()->values()->all()
        );

        return [
            'title' => $title ?? 'Tất cả sản phẩm',
            'navItems' => $this->navigationService->navItems(),
            'filters' => $this->normalizeFilters($filters),
            'filterOptions' => [
                'categories' => $this->productRepository->categories()->map(fn ($category) => [
                    'id' => $category->id,
                    'name' => $category->name,
                ])->values(),
                'brands' => $this->productRepository->brands()->map(fn ($brand) => [
                    'id' => $brand->id,
                    'name' => $brand->name,
                ])->values(),
            ],
            'products' => $this->formatPaginator($products, $mediaUrls),
        ];
    }

    public function categoryProps(int|string $categoryId, array $filters = []): array
    {
        $category = $this->productRepository->findCategory($categoryId);
        $filters = array_merge($filters, ['category_id' => $category->id]);

        return $this->indexProps($filters, $category->name);
    }

    public function brandProps(int|string $brandId, array $filters = []): array
    {
        $brand = $this->productRepository->findBrand($brandId);
        $filters = array_merge($filters, ['brand_id' => $brand->id]);

        return $this->indexProps($filters, $brand->name);
    }

    private function normalizeFilters(array $filters): array
    {
        return array_filter([
            'q' => $filters['q'] ?? '',
            'category_id' => isset($filters['category_id']) ? (int) $filters['category_id'] : '',
            'brand_id' => isset($filters['brand_id']) ? (int) $filters['brand_id'] : '',
            'price_min' => isset($filters['price_min']) ? (int) $filters['price_min'] : '',
            'price_max' => isset($filters['price_max']) ? (int) $filters['price_max'] : '',
            'rating_min' => isset($filters['rating_min']) ? (float) $filters['rating_min'] : '',
            'featured' => $filters['featured'] ?? '',
            'in_stock' => $filters['in_stock'] ?? '',
            'sort' => $filters['sort'] ?? 'newest',
            'page' => isset($filters['page']) ? (int) $filters['page'] : '',
        ], fn ($value) => $value !== null && $value !== '');
    }

    private function formatPaginator(LengthAwarePaginator $products, array $mediaUrls): array
    {
        return [
            'data' => $products->getCollection()
                ->map(fn ($product) => $this->formatProduct($product, $mediaUrls))
                ->values(),
            'meta' => [
                'currentPage' => $products->currentPage(),
                'lastPage' => $products->lastPage(),
                'perPage' => $products->perPage(),
                'total' => $products->total(),
            ],
            'links' => [
                'next' => $products->nextPageUrl(),
                'prev' => $products->previousPageUrl(),
            ],
        ];
    }

    private function formatProduct($product, array $mediaUrls): array
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
            'inventory_qty' => $product->inventory_qty,
            'star' => $product->star,
            'featured' => (bool) $product->featured,
            'featured_image' => $mediaUrls[$product->media_id] ?? asset('adm/images/godakeben450x170.jpg'),
            'url' => '/products/' . $product->id,
        ];
    }
}
