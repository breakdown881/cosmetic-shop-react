<?php

namespace App\Services\Api;

use App\Repositories\Api\CategoryRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class CategoryService
{
    public function __construct(private readonly CategoryRepository $categories) {}

    public function all(Request $request): Collection
    {
        return $this->categories->all($request);
    }

    public function create(array $data, ?int $createdBy)
    {
        $data['created_by'] = $createdBy ?? 1;

        return $this->categories->create($data);
    }

    public function find(int|string $id)
    {
        return $this->categories->find($id);
    }

    public function update(int|string $id, array $data)
    {
        return $this->categories->update($this->categories->find($id), $data);
    }

    public function delete(int|string $id): void
    {
        $this->categories->delete($this->categories->find($id));
    }

    public function updateStatus(int|string $id, int $status)
    {
        return $this->categories->update(
            $this->categories->find($id),
            ['status' => $status]
        );
    }
}
