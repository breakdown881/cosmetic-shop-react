<?php

namespace App\Repositories\Admin;

use App\Models\Role;
use Illuminate\Support\Collection;

class RoleRepository
{
    public function all(): Collection
    {
        return Role::latest()->get();
    }

    public function find(int|string $id): Role
    {
        return Role::findOrFail($id);
    }

    public function create(array $data): Role
    {
        return Role::create($data);
    }

    public function update(Role $role, array $data): Role
    {
        $role->update($data);

        return $role;
    }

    public function delete(Role $role): void
    {
        $role->delete();
    }
}
