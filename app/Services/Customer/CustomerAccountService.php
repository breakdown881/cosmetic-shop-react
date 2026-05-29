<?php

namespace App\Services\Customer;

use App\Models\User;
use App\Repositories\Customer\CustomerAccountRepository;

class CustomerAccountService
{
    public function __construct(
        private readonly CustomerAccountRepository $accountRepository,
        private readonly CustomerNavigationService $navigationService,
    ) {}

    public function props(?User $user): array
    {
        return [
            'title' => 'Tài khoản',
            'navItems' => $this->navigationService->navItems(),
            'requiresAuth' => $user === null,
            'profile' => $user ? $this->format($user) : null,
        ];
    }

    public function update(User $user, array $data): array
    {
        return $this->format($this->accountRepository->update($user, [
            'name' => $data['name'],
            'email' => $data['email'],
        ]));
    }

    private function format(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ];
    }
}
