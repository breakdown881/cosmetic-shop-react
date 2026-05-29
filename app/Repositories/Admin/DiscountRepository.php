<?php

namespace App\Repositories\Admin;

use App\Models\Discount;
use Illuminate\Support\Collection;

class DiscountRepository
{
    public function all(): Collection
    {
        return Discount::latest()->get();
    }

    public function find(int|string $id): Discount
    {
        return Discount::findOrFail($id);
    }

    public function findByCode(string $code): ?Discount
    {
        return Discount::where('code', $code)->first();
    }

    public function create(array $data): Discount
    {
        return Discount::create($data);
    }

    public function update(Discount $discount, array $data): Discount
    {
        $discount->update($data);

        return $discount->refresh();
    }

    public function delete(Discount $discount): void
    {
        $discount->delete();
    }
}
