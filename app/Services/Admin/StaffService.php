<?php

namespace App\Services\Admin;

use App\Models\Admin;
use App\Repositories\Admin\StaffRepository;
use Illuminate\Support\Collection;

class StaffService
{
    public function __construct(private readonly StaffRepository $staff) {}

    public function all(): Collection
    {
        return $this->staff->all();
    }

    public function find(int|string $id): Admin
    {
        return $this->staff->find($id);
    }

    public function create(array $data): Admin
    {
        return $this->staff->create($data);
    }

    public function update(int|string $id, array $data): Admin
    {
        if (empty($data['password'])) {
            unset($data['password']);
        }

        return $this->staff->update($this->staff->find($id), $data);
    }

    public function delete(int|string $id): void
    {
        $this->staff->delete($this->staff->find($id));
    }

    public function roles(): array
    {
        return Admin::ROLE;
    }
}
