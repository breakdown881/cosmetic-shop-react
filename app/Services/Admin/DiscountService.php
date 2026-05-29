<?php

namespace App\Services\Admin;

use App\Models\Discount;
use App\Repositories\Admin\DiscountRepository;
use Illuminate\Support\Collection;

class DiscountService
{
    public function __construct(private readonly DiscountRepository $discounts) {}

    public function all(): Collection
    {
        return $this->discounts->all()
            ->map(fn (Discount $discount) => $this->format($discount));
    }

    public function find(int|string $id): array
    {
        return $this->format($this->discounts->find($id));
    }

    public function create(array $data): array
    {
        return $this->format($this->discounts->create($data));
    }

    public function update(int|string $id, array $data): array
    {
        return $this->format($this->discounts->update($this->discounts->find($id), $data));
    }

    public function delete(int|string $id): void
    {
        $this->discounts->delete($this->discounts->find($id));
    }

    private function format(Discount $discount): array
    {
        return [
            'id' => $discount->id,
            'code' => $discount->code,
            'description' => $discount->description,
            'is_fixed' => (int) $discount->is_fixed,
            'discount_amount' => (int) $discount->discount_amount,
            'starts_at' => optional($discount->starts_at)->format('Y-m-d H:i:s'),
            'expires_at' => optional($discount->expires_at)->format('Y-m-d H:i:s'),
            'created_at' => optional($discount->created_at)->toDateTimeString(),
            'updated_at' => optional($discount->updated_at)->toDateTimeString(),
        ];
    }
}
