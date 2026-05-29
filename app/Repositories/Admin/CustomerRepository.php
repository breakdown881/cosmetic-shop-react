<?php

namespace App\Repositories\Admin;

use App\Models\User;
use Illuminate\Support\Collection;

class CustomerRepository
{
    public function all(): Collection
    {
        return User::latest()->get();
    }

    public function find(int|string $id): User
    {
        return User::findOrFail($id);
    }

    public function update(User $customer, array $data): User
    {
        $customer->update($data);

        return $customer->refresh();
    }

    public function delete(User $customer): void
    {
        $customer->delete();
    }
}
