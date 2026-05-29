<?php

namespace App\Services\Customer;

use App\Models\Product;
use App\Models\User;
use App\Repositories\Customer\CustomerWishlistRepository;

class CustomerWishlistService
{
    public function __construct(
        private readonly CustomerWishlistRepository $wishlist,
        private readonly CustomerNavigationService $navigation,
    ) {}

    public function props(User $user): array
    {
        return [
            'title' => 'Wishlist',
            'csrfToken' => csrf_token(),
            'navItems' => $this->navigation->navItems(),
            'items' => $this->wishlist->products($user)
                ->map(fn (Product $product) => $this->formatProduct($product))
                ->values(),
        ];
    }

    public function add(User $user, int $productId): void
    {
        $this->wishlist->add($user, $productId);
    }

    public function remove(User $user, int $productId): void
    {
        $this->wishlist->remove($user, $productId);
    }

    public function state(?User $user, Product $product): array
    {
        return [
            'isWishlisted' => $user ? $this->wishlist->has($user, $product->id) : false,
            'storeUrl' => '/wishlist/items',
            'removeUrl' => '/wishlist/items/' . $product->id,
        ];
    }

    private function formatProduct(Product $product): array
    {
        $price = (int) $product->price;
        $discountPercentage = max(0, min(100, (int) $product->discount_percentage));
        $salePrice = $discountPercentage > 0
            ? (int) round($price * (100 - $discountPercentage) / 100)
            : $price;

        return [
            'id' => $product->id,
            'name' => $product->name,
            'price' => $price,
            'sale_price' => $salePrice,
            'featured_image' => asset('adm/images/godakeben450x170.jpg'),
            'url' => '/products/' . $product->id,
            'removeUrl' => '/wishlist/items/' . $product->id,
        ];
    }
}
