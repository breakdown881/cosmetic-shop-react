<?php

namespace App\Repositories\Customer;

use App\Models\User;

class CustomerAccountRepository
{
    public function update(User $user, array $data): User
    {
        $user = User::query()->findOrFail($user->id);
        $user->update($data);

        return $user->refresh();
    }
}
