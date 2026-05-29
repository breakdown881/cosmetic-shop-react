<?php

namespace App\Services\Customer;

use App\Models\Category;
use App\Models\Discount;
use App\Models\Product;
use App\Repositories\Customer\CustomerHomeRepository;
use App\Repositories\Customer\CustomerPromotionRepository;

class CustomerHomeService
{
    public function __construct(
        private readonly CustomerHomeRepository $homeRepository,
        private readonly CustomerNavigationService $navigationService,
        private readonly CustomerPromotionRepository $promotionRepository,
    ) {}

    public function props(): array
    {
        $categories = $this->homeRepository->categories();
        $products = $this->homeRepository->featuredProducts();
        $mediaUrls = $this->homeRepository->mediaUrls(
            $products->pluck('media_id')->filter()->unique()->values()->all()
        );

        $formattedProducts = $products
            ->map(fn (Product $product) => $this->formatProduct($product, $mediaUrls))
            ->groupBy('category_id');

        return [
            'slides' => $this->slides(),
            'navItems' => $this->navigationService->navItems(),
            'promotions' => $this->promotionRepository->activeDiscounts()
                ->take(3)
                ->map(fn (Discount $discount) => $this->formatPromotion($discount))
                ->values(),
            'categories' => $categories
                ->map(fn (Category $category) => $this->formatCategory($category, $formattedProducts))
                ->values(),
            'categorySections' => $categories
                ->map(fn (Category $category) => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'url' => '/categories/' . $category->id,
                    'products' => $formattedProducts->get($category->id, collect())->take(6)->values(),
                ])
                ->values(),
        ];
    }

    private function slides(): array
    {
        return [
            [
                'title' => 'Mỹ phẩm chính hãng cho làn da Việt',
                'description' => 'Ưu đãi chăm sóc da, trang điểm và dưỡng thể được chọn lọc mỗi tuần.',
                'imageUrl' => asset('adm/images/slider1.jpg'),
                'ctaLabel' => 'Xem tất cả sản phẩm',
                'ctaUrl' => '/products',
            ],
            [
                'title' => 'Combo dưỡng da tiết kiệm',
                'description' => 'Khám phá sản phẩm nổi bật theo từng danh mục chỉ trong một trang.',
                'imageUrl' => asset('adm/images/slider_2.jpg'),
                'ctaLabel' => 'Xem danh mục',
                'ctaUrl' => '#categories',
            ],
            [
                'title' => 'Tỏa sáng cùng Goda Shop',
                'description' => 'Sản phẩm bán chạy, giá tốt và giao hàng nhanh cho khách hàng thân thiết.',
                'imageUrl' => asset('adm/images/slider_3.jpg'),
                'ctaLabel' => 'Khám phá ngay',
                'ctaUrl' => '#featured-products',
            ],
        ];
    }

    private function formatCategory(Category $category, $formattedProducts): array
    {
        return [
            'id' => $category->id,
            'name' => $category->name,
            'parentId' => $category->parent_id,
            'url' => '/categories/' . $category->id,
            'productsCount' => $formattedProducts->get($category->id, collect())->count(),
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
            'category_id' => $product->category_id,
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

    private function formatPromotion(Discount $discount): array
    {
        return [
            'code' => $discount->code,
            'description' => $discount->description,
            'label' => (int) $discount->is_fixed === 1
                ? number_format((int) $discount->discount_amount, 0, ',', '.') . ' VND'
                : ((int) $discount->discount_amount) . '%',
            'expires_at' => optional($discount->expires_at)->toDateTimeString(),
        ];
    }
}
