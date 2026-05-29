<?php

namespace App\Services\Customer;

use App\Models\User;
use App\Repositories\Customer\CustomerAuthRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CustomerSocialAuthService
{
    private const PROVIDERS = ['google', 'facebook'];

    public function __construct(
        private readonly CustomerAuthRepository $customers,
        private readonly CustomerSocialiteGateway $socialite,
    ) {}

    public function redirectUrl(string $provider): string
    {
        $this->ensureSupportedProvider($provider);

        return $this->socialite->redirectUrl($provider);
    }

    public function handleCallback(Request $request, string $provider): User
    {
        $this->ensureSupportedProvider($provider);

        $socialUser = $this->socialite->user($provider);
        $providerId = (string) ($socialUser['id'] ?? '');
        $email = $socialUser['email'] ?? null;

        if ($providerId === '' || ! $email) {
            throw ValidationException::withMessages([
                'email' => 'Unable to retrieve an email address from the social provider.',
            ]);
        }

        $user = $this->customers->findBySocialProvider($provider, $providerId)
            ?: $this->customers->findByEmail($email);

        if (! $user) {
            $user = $this->customers->create([
                'name' => $socialUser['name'] ?: 'Customer',
                'email' => $email,
                'password' => Str::password(32),
                'provider_name' => $provider,
                'provider_id' => $providerId,
                'avatar_url' => $socialUser['avatar'] ?? null,
            ]);
        } elseif ($user->provider_name !== $provider || $user->provider_id !== $providerId) {
            $user = $this->customers->linkSocialProvider($user, $provider, $providerId, $socialUser['avatar'] ?? null);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return $user;
    }

    private function ensureSupportedProvider(string $provider): void
    {
        abort_unless(in_array($provider, self::PROVIDERS, true), 404);
    }
}
