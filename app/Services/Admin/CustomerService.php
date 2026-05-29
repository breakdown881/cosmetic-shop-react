<?php

namespace App\Services\Admin;

use App\Models\User;
use App\Repositories\Admin\CustomerRepository;
use Illuminate\Support\Collection;

class CustomerService
{
    public function __construct(private readonly CustomerRepository $customers) {}

    public function all(): Collection
    {
        return $this->customers->all()
            ->map(fn (User $customer) => $this->format($customer));
    }

    public function find(int|string $id): array
    {
        return $this->format($this->customers->find($id));
    }

    public function update(int|string $id, array $data): array
    {
        return $this->format($this->customers->update($this->customers->find($id), $data));
    }

    public function delete(int|string $id): void
    {
        $this->customers->delete($this->customers->find($id));
    }

    private function format(User $customer): array
    {
        return [
            'id' => $customer->id,
            'name' => $customer->name,
            'email' => $customer->email,
            'email_verified_at' => optional($customer->email_verified_at)->toDateTimeString(),
            'created_at' => optional($customer->created_at)->toDateTimeString(),
            'updated_at' => optional($customer->updated_at)->toDateTimeString(),
        ];
    }
}
