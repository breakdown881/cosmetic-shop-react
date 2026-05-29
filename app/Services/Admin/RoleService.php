<?php

namespace App\Services\Admin;

use App\Models\Role;
use App\Repositories\Admin\RoleRepository;
use Illuminate\Support\Collection;

class RoleService
{
    public function __construct(private readonly RoleRepository $roles) {}

    public function all(): Collection
    {
        return $this->roles->all();
    }

    public function find(int|string $id): Role
    {
        return $this->roles->find($id);
    }

    public function create(array $data): Role
    {
        return $this->roles->create($data);
    }

    public function update(int|string $id, array $data): Role
    {
        return $this->roles->update($this->roles->find($id), $data);
    }

    public function delete(int|string $id): void
    {
        $this->roles->delete($this->roles->find($id));
    }

    public function allowedRoles(): array
    {
        return Role::ALLOWED_ROLES;
    }
}
