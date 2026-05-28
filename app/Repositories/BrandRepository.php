<?php

namespace App\Repositories;

use App\Models\Brand;
use Illuminate\Support\Facades\DB;

class BrandRepository extends AbstractRepository implements BrandRepositoryInterface
{
    public function getModel()
    {
        return Brand::class;
    }

    public function get($id)
    {
        $brand = Brand::all()->firstWhere('id', $id);
        if ($brand) {
            return $brand;
        }
        return null;
    }

    public function getAll()
    {
        $brands = Brand::all();
        if ($brands->isNotEmpty()) {
            return $brands;
        }
        return null;
    }

    public function create(array $data)
    {
        try {
            $existBrand = Brand::where('name', '=', $data["name"])->get();
            $brandObject = new Brand();
            $brandObject->fill($data);

            if ($existBrand->count() == 0) {
                if ($brandObject->save()) {
                    return $brandObject;
                }
            }
            return false;
        } catch (\Exception $exception) {
            return false;
        }
    }

    public function update($brand, array $data)
    {
        try {
            if ($brand) {
                $brand->fill($data);
                if ($brand->save()) {
                    return $brand;
                }
                return false;
            }
        } catch (\Exception $exception) {
            return false;
        }
    }

    public function delete($brand)
    {
        try {
            if ($brand) {
                $brand->clearMediaCollection();
                return $brand->delete();
            }

            return false;
        } catch (\Exception $exception) {
            return false;
        }
    }
}
