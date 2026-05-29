<?php

namespace App\Services\Api;

use App\Models\Brand;
use App\Repositories\Api\BrandRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class BrandService
{
    public function __construct(private readonly BrandRepository $brands) {}

    public function all(Request $request): Collection
    {
        return $this->brands->all($request)
            ->map(fn (Brand $brand) => $this->format($brand));
    }

    public function create(array $data, ?int $createdBy, Request $request): array
    {
        $data['created_by'] = $createdBy ?? 1;
        $brand = $this->brands->create($data);

        if ($request->hasFile('image')) {
            $brand->addMediaFromRequest('image')->toMediaCollection('brands');
            $brand->refresh();
        }

        return $this->format($brand);
    }

    public function find(int|string $id): array
    {
        return $this->format($this->brands->find($id));
    }

    public function update(int|string $id, array $data, Request $request): array
    {
        $brand = $this->brands->update($this->brands->find($id), $data);

        if ($request->hasFile('image')) {
            $brand->clearMediaCollection('brands');
            $brand->addMediaFromRequest('image')->toMediaCollection('brands');
            $brand->refresh();
        }

        return $this->format($brand);
    }

    public function delete(int|string $id): void
    {
        $brand = $this->brands->find($id);
        $brand->clearMediaCollection('brands');
        $this->brands->delete($brand);
    }

    public function updateStatus(int|string $id, int $status): array
    {
        return $this->format($this->brands->update(
            $this->brands->find($id),
            ['status' => $status]
        ));
    }

    private function format(Brand $brand): array
    {
        return [
            'id' => $brand->id,
            'name' => $brand->name,
            'status' => (int) $brand->status,
            'created_by' => $brand->created_by,
            'image_url' => $brand->getFirstMediaUrl('brands') ?: null,
            'created_at' => optional($brand->created_at)->toDateTimeString(),
            'updated_at' => optional($brand->updated_at)->toDateTimeString(),
        ];
    }
}
