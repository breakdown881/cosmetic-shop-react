<?php

namespace App\Services\Customer;

use App\Models\Discount;
use App\Repositories\Customer\CustomerPromotionRepository;
use Illuminate\Session\Store;
use Illuminate\Validation\ValidationException;

class CustomerPromotionService
{
    public function __construct(
        private readonly CustomerPromotionRepository $promotions,
        private readonly CustomerCartService $cart,
        private readonly CustomerNavigationService $navigation,
    ) {}

    public function props(): array
    {
        return [
            'title' => 'Khuyen mai',
            'navItems' => $this->navigation->navItems(),
            'promotions' => $this->activePromotions(),
        ];
    }

    public function activePromotions(): array
    {
        return $this->promotions->activeDiscounts()
            ->map(fn (Discount $discount) => $this->formatDiscount($discount))
            ->values()
            ->all();
    }

    public function validateVoucher(Store $session, string $code): array
    {
        $cart = $this->cart->payload($session);
        abort_if(($cart['items'] ?? []) === [], 422, 'Your cart is empty.');

        $discount = $this->promotions->activeDiscountByCode($code);
        if (! $discount) {
            throw ValidationException::withMessages([
                'discount_code' => 'Discount code is not active.',
            ]);
        }

        $subTotal = (int) $cart['total'];
        $discountAmount = $this->discountAmount($discount, $subTotal);

        return [
            ...$this->formatDiscount($discount),
            'cart_total' => $subTotal,
            'discount_amount' => $discountAmount,
            'payment_total' => max(0, $subTotal - $discountAmount),
        ];
    }

    private function formatDiscount(Discount $discount): array
    {
        return [
            'id' => $discount->id,
            'code' => $discount->code,
            'description' => $discount->description,
            'is_fixed' => (int) $discount->is_fixed,
            'discount_amount' => (int) $discount->discount_amount,
            'label' => (int) $discount->is_fixed === 1
                ? number_format((int) $discount->discount_amount, 0, ',', '.') . ' VND'
                : ((int) $discount->discount_amount) . '%',
            'starts_at' => optional($discount->starts_at)->toDateTimeString(),
            'expires_at' => optional($discount->expires_at)->toDateTimeString(),
        ];
    }

    private function discountAmount(Discount $discount, int $subTotal): int
    {
        if ((int) $discount->is_fixed === 1) {
            return min($subTotal, (int) $discount->discount_amount);
        }

        return min($subTotal, (int) floor($subTotal * ((int) $discount->discount_amount / 100)));
    }
}
