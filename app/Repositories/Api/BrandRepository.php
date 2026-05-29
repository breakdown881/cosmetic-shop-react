<?php

namespace App\Repositories\Api;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class BrandRepository
{
    public function all(Request $request): Collection
    {
        if (! $request->filled('q')) {
            $query = Brand::query()->latest();

            if ($request->filled('status')) {
                $query->where('status', $request->integer('status'));
            }

            return $query->get();
        }

        $search = Brand::search((string) $request->input('q'));

        if ($request->filled('status')) {
            $search->where('status', $request->integer('status'));
        }

        return $search->query(function ($query) use ($request) {
            $query->latest()
                ->where('name', 'like', '%' . $request->input('q') . '%');

            if ($request->filled('status')) {
                $query->where('status', $request->integer('status'));
            }
        })->get();
    }

    public function find(int|string $id): Brand
    {
        return Brand::findOrFail($id);
    }

    public function create(array $data): Brand
    {
        return Brand::create($data);
    }

    public function update(Brand $brand, array $data): Brand
    {
        $brand->update($data);

        return $brand;
    }

    public function delete(Brand $brand): void
    {
        $brand->delete();
    }
}
