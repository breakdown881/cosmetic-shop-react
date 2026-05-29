<?php

namespace App\Repositories\Admin;

use App\Models\Province;
use App\Models\Transport;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class FeeShipRepository
{
    public function all(): Collection
    {
        return Transport::query()
            ->with('province:id,name,type')
            ->latest()
            ->get();
    }

    public function find(int|string $id): Transport
    {
        return Transport::findOrFail($id);
    }

    public function createWithProvince(array $data): Transport
    {
        return DB::transaction(function () use ($data) {
            $province = Province::create([
                'name' => $data['province_name'],
                'type' => $data['province_type'],
            ]);

            return Transport::create([
                'province_id' => $province->id,
                'price' => $data['price'],
            ])->load('province:id,name,type');
        });
    }

    public function updateWithProvince(Transport $feeShip, array $data): Transport
    {
        DB::transaction(function () use ($data, $feeShip) {
            $feeShip->province()->update([
                'name' => $data['province_name'],
                'type' => $data['province_type'],
            ]);

            $feeShip->update([
                'price' => $data['price'],
            ]);
        });

        return $feeShip->refresh()->load('province:id,name,type');
    }

    public function deleteWithProvince(Transport $feeShip): void
    {
        DB::transaction(function () use ($feeShip) {
            $province = $feeShip->province;
            $feeShip->delete();
            $province?->delete();
        });
    }
}
