<?php

namespace App\Services\Api;

use App\Repositories\Api\ProductRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ProductService
{
    public function __construct(private readonly ProductRepository $products) {}

    public function all(Request $request): Collection
    {
        return $this->products->all($request);
    }

    public function create(array $data, ?int $createdBy)
    {
        $data['created_by'] = $createdBy ?? 1;

        return $this->products->loadForAdmin($this->products->create($data));
    }

    public function find(int|string $id)
    {
        return $this->products->loadForAdmin($this->products->find($id));
    }

    public function update(int|string $id, array $data)
    {
        return $this->products->loadForAdmin(
            $this->products->update($this->products->find($id), $data)
        );
    }

    public function delete(int|string $id): void
    {
        $this->products->delete($this->products->find($id));
    }

    public function updateStatus(int|string $id, int $status)
    {
        return $this->products->loadForAdmin(
            $this->products->update($this->products->find($id), ['status' => $status])
        );
    }
}
