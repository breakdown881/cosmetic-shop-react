<?php

namespace App\Services\Customer;

use App\Models\User;
use App\Repositories\Customer\CustomerAuthRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CustomerAuthService
{
    public function __construct(
        private readonly CustomerAuthRepository $customers,
        private readonly CustomerNavigationService $navigation,
    ) {}

    public function loginProps(): array
    {
        return [
            'title' => 'Login',
            'csrfToken' => csrf_token(),
            'navItems' => $this->navigation->navItems(),
        ];
    }

    public function registerProps(): array
    {
        return [
            'title' => 'Create account',
            'csrfToken' => csrf_token(),
            'navItems' => $this->navigation->navItems(),
        ];
    }

    public function login(Request $request, array $credentials): void
    {
        if (! Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => 'The provided credentials are incorrect.',
            ]);
        }

        $request->session()->regenerate();
    }

    public function register(Request $request, array $data): User
    {
        $user = $this->customers->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return $user;
    }

    public function logout(Request $request): void
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
