<?php

namespace App\Services\Customer;

use App\Models\Product;
use App\Repositories\Customer\CustomerCartRepository;
use Illuminate\Session\Store;

class CustomerCartService
{
    private const SESSION_KEY = 'customer_cart';

    public function __construct(private readonly CustomerCartRepository $cartRepository) {}

    public function add(Store $session, int $productId, int $quantity): array
    {
        $product = $this->cartRepository->findActiveProduct($productId);
        $cart = $this->rawCart($session);
        $currentQuantity = (int) ($cart[$product->id] ?? 0);
        $newQuantity = min($currentQuantity + $quantity, (int) $product->inventory_qty);

        $cart[$product->id] = $newQuantity;
        $this->saveRawCart($session, $cart);

        return $this->payload($session);
    }

    public function update(Store $session, int $productId, int $quantity): array
    {
        $product = $this->cartRepository->findActiveProduct($productId);
        $cart = $this->rawCart($session);
        $cart[$product->id] = min($quantity, (int) $product->inventory_qty);
        $this->saveRawCart($session, $cart);

        return $this->payload($session);
    }

    public function remove(Store $session, int $productId): array
    {
        $cart = $this->rawCart($session);
        unset($cart[$productId]);
        $this->saveRawCart($session, $cart);

        return $this->payload($session);
    }

    public function clear(Store $session): void
    {
        $session->put(self::SESSION_KEY, []);
    }

    public function props(Store $session, array $navItems): array
    {
        return [
            'title' => 'Giỏ hàng',
            'navItems' => $navItems,
            'cart' => $this->payload($session),
        ];
    }

    public function payload(Store $session): array
    {
        $cart = $this->rawCart($session);
        $products = $this->cartRepository->activeProductsByIds(array_keys($cart));
        $mediaUrls = $this->cartRepository->mediaUrls(
            $products->pluck('media_id')->filter()->unique()->values()->all()
        );
        $items = $products
            ->map(fn (Product $product) => $this->formatItem($product, (int) $cart[$product->id], $mediaUrls))
            ->values();

        return [
            'items' => $items,
            'total' => $items->sum('subtotal'),
        ];
    }

    private function rawCart(Store $session): array
    {
        return $session->get(self::SESSION_KEY, []);
    }

    private function saveRawCart(Store $session, array $cart): void
    {
        $session->put(self::SESSION_KEY, array_filter($cart, fn ($quantity) => (int) $quantity > 0));
    }

    private function formatItem(Product $product, int $quantity, array $mediaUrls): array
    {
        $price = (int) $product->price;
        $discountPercentage = max(0, min(100, (int) $product->discount_percentage));
        $salePrice = $discountPercentage > 0
            ? (int) round($price * (100 - $discountPercentage) / 100)
            : $price;

        return [
            'product_id' => $product->id,
            'name' => $product->name,
            'quantity' => $quantity,
            'price' => $price,
            'sale_price' => $salePrice,
            'subtotal' => $salePrice * $quantity,
            'inventory_qty' => (int) $product->inventory_qty,
            'image' => $mediaUrls[$product->media_id] ?? asset('adm/images/godakeben450x170.jpg'),
            'url' => '/products/' . $product->id,
        ];
    }
}
