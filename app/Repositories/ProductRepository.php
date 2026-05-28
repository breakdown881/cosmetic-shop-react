<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductRepository extends AbstractRepository implements ProductRepositoryInterface
{
    public function getModel()
    {
        return Product::class;
    }

    public function get($id)
    {
        $product = Product::all()->firstWhere('id', $id);
        if ($product) {
            return $product;
        }
        return null;
    }

    public function getAll()
    {
        $products = Product::all();
        if ($products->isNotEmpty()) {
            return $products;
        }
        return null;
    }

    public function getByCategory($id)
    {
        $products = Product::all()->where('category_id', $id);
        if ($products) {
            return $products;
        }
        return null;
    }

    public function getByBrand()
    {
        $products = Product::all()->where('brand_id', NULL);
        if ($products) {
            return $products;
        }
        return null;
    }

    public function create(array $data)
    {
        try {
            $existProduct = Product::where('name', '=', $data["name"])->get();
            $productObject = new Product();
            $productObject->fill($data);

            if ($existProduct->count() == 0) {
                if ($productObject->save()) {
                    return $productObject;
                }
            }
            return false;
        } catch (\Exception $exception) {
            return false;
        }
    }

    public function update($product, array $data)
    {
        try {
            if ($product) {
                $product->fill($data);
                if ($product->save()) {
                    return $product;
                }
                return false;
            }
        } catch (\Exception $exception) {
            return false;
        }
    }

    public function delete($product)
    {
        try {
            if ($product) {
                return $product->delete();
            }

            return false;
        } catch (\Exception $exception) {
            return false;
        }
    }
}
