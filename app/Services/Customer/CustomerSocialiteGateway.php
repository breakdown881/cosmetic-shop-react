<?php

namespace App\Services\Customer;

use Laravel\Socialite\Facades\Socialite;

class CustomerSocialiteGateway
{
    public function redirectUrl(string $provider): string
    {
        return Socialite::driver($provider)->redirect()->getTargetUrl();
    }

    public function user(string $provider): array
    {
        $socialiteUser = Socialite::driver($provider)->user();

        return [
            'id' => (string) $socialiteUser->getId(),
            'name' => $socialiteUser->getName() ?: $socialiteUser->getNickname() ?: 'Customer',
            'email' => $socialiteUser->getEmail(),
            'avatar' => $socialiteUser->getAvatar(),
        ];
    }
}
