<?php

namespace App\Services\Admin;

use App\Models\Transport;
use App\Repositories\Admin\FeeShipRepository;
use Illuminate\Support\Collection;

class FeeShipService
{
    public function __construct(private readonly FeeShipRepository $feeShips) {}

    public function all(): Collection
    {
        return $this->feeShips->all()
            ->map(fn (Transport $feeShip) => $this->format($feeShip));
    }

    public function find(int|string $id): array
    {
        return $this->format($this->feeShips->find($id)->load('province:id,name,type'));
    }

    public function create(array $data): array
    {
        return $this->format($this->feeShips->createWithProvince($data));
    }

    public function update(int|string $id, array $data): array
    {
        return $this->format($this->feeShips->updateWithProvince($this->feeShips->find($id), $data));
    }

    public function delete(int|string $id): void
    {
        $this->feeShips->deleteWithProvince($this->feeShips->find($id));
    }

    private function format(Transport $feeShip): array
    {
        return [
            'id' => $feeShip->id,
            'province_id' => $feeShip->province_id,
            'province_name' => $feeShip->province?->name,
            'province_type' => $feeShip->province?->type,
            'price' => $feeShip->price,
            'created_at' => optional($feeShip->created_at)->toDateTimeString(),
            'updated_at' => optional($feeShip->updated_at)->toDateTimeString(),
        ];
    }
}
