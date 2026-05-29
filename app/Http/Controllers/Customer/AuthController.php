<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\CustomerLoginRequest;
use App\Http\Requests\Customer\CustomerRegisterRequest;
use App\Services\Customer\CustomerAuthService;
use App\Support\PublicReactShell;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    public function __construct(
        private readonly CustomerAuthService $auth,
        private readonly PublicReactShell $shell,
    ) {}

    public function loginForm(): Response
    {
        $props = $this->auth->loginProps();

        return $this->shell->render('CustomerLoginPage', $props, $props['title']);
    }

    public function login(CustomerLoginRequest $request): RedirectResponse
    {
        $this->auth->login($request, $request->validated());

        return redirect()->intended('/account');
    }

    public function registerForm(): Response
    {
        $props = $this->auth->registerProps();

        return $this->shell->render('CustomerRegisterPage', $props, $props['title']);
    }

    public function register(CustomerRegisterRequest $request): RedirectResponse
    {
        $this->auth->register($request, $request->validated());

        return redirect('/account');
    }

    public function logout(Request $request): RedirectResponse
    {
        $this->auth->logout($request);

        return redirect('/');
    }
}
