<?php

namespace App\Services\Admin;

use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\Auth;

class ProductService
{
    protected $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function getAll()
    {
        return $this->productRepository->getAll();
    }

    public function getByCategory($id)
    {
        return $this->productRepository->getByCategory($id);
    }

    public function getByBrand()
    {
        return $this->productRepository->getByBrand();
    }

    public function get($id)
    {
        return $this->productRepository->get($id);
    }

    public function store($data)
    {
        $data['created_by'] = Auth::guard('admin')->user()->id;
        $category = $this->productRepository->create($data);

        return $category;
    }

    public function update($category, $data)
    {
        $this->productRepository->update($category, $data);

        return $category;
    }

    public function destroy($category)
    {
        return $this->productRepository->delete($category);
    }

    public function changeStatus($category, $status)
    {
        $this->productRepository->update($category, ['status' => $status]);
        return $category;
    }
}
