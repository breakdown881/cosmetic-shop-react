<?php

namespace App\Repositories\Customer;

use App\Models\Discount;
use Illuminate\Support\Collection;

class CustomerPromotionRepository
{
    public function activeDiscounts(): Collection
    {
        return Discount::query()
            ->where(function ($query) {
                $query->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>=', now());
            })
            ->latest()
            ->get();
    }

    public function activeDiscountByCode(string $code): ?Discount
    {
        return $this->activeDiscounts()
            ->first(fn (Discount $discount) => strcasecmp($discount->code, $code) === 0);
    }
}
