<?php

namespace App\Repositories\Admin;

use App\Models\Admin;
use Illuminate\Support\Collection;

class StaffRepository
{
    public function all(): Collection
    {
        return Admin::latest()->get();
    }

    public function find(int|string $id): Admin
    {
        return Admin::findOrFail($id);
    }

    public function create(array $data): Admin
    {
        return Admin::create($data);
    }

    public function update(Admin $staff, array $data): Admin
    {
        $staff->update($data);

        return $staff;
    }

    public function delete(Admin $staff): void
    {
        $staff->delete();
    }
}
