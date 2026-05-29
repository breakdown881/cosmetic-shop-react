<?php

namespace App\Repositories\Customer;

use App\Models\User;

class CustomerAuthRepository
{
    public function create(array $data): User
    {
        return User::query()->create($data);
    }

    public function findByEmail(string $email): ?User
    {
        return User::query()
            ->where('email', $email)
            ->first();
    }

    public function findBySocialProvider(string $provider, string $providerId): ?User
    {
        return User::query()
            ->where('provider_name', $provider)
            ->where('provider_id', $providerId)
            ->first();
    }

    public function linkSocialProvider(User $user, string $provider, string $providerId, ?string $avatarUrl): User
    {
        $user->forceFill([
            'provider_name' => $provider,
            'provider_id' => $providerId,
            'avatar_url' => $avatarUrl,
        ])->save();

        return $user->refresh();
    }
}
