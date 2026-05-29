<?php

namespace App\Services\Customer;

use App\Models\Category;
use App\Models\Product;
use App\Repositories\Customer\CustomerHomeRepository;

class CustomerHomeService
{
    public function __construct(private readonly CustomerHomeRepository $homeRepository) {}

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
            'navItems' => $this->navItems(),
            'categories' => $categories
                ->map(fn (Category $category) => $this->formatCategory($category, $formattedProducts))
                ->values(),
            'categorySections' => $categories
                ->map(fn (Category $category) => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'url' => '#category-' . $category->id,
                    'products' => $formattedProducts->get($category->id, collect())->take(6)->values(),
                ])
                ->values(),
        ];
    }

    private function navItems(): array
    {
        return [
            ['label' => 'Trang chủ', 'href' => '#home'],
            ['label' => 'Khuyến mãi', 'href' => '#promotions'],
            ['label' => 'Danh mục', 'href' => '#categories'],
            ['label' => 'Sản phẩm nổi bật', 'href' => '#featured-products'],
        ];
    }

    private function slides(): array
    {
        return [
            [
                'title' => 'Mỹ phẩm chính hãng cho làn da Việt',
                'description' => 'Ưu đãi chăm sóc da, trang điểm và dưỡng thể được chọn lọc mỗi tuần.',
                'imageUrl' => asset('adm/images/slider1.jpg'),
                'ctaLabel' => 'Mua ngay',
                'ctaUrl' => '#categories',
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
            'url' => '#category-' . $category->id,
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
            'url' => '#product-' . $product->id,
        ];
    }
}
