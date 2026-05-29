<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\Customer\CustomerSocialAuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SocialAuthController extends Controller
{
    public function __construct(private readonly CustomerSocialAuthService $auth) {}

    public function redirect(string $provider): RedirectResponse
    {
        return redirect()->away($this->auth->redirectUrl($provider));
    }

    public function callback(Request $request, string $provider): RedirectResponse
    {
        try {
            $this->auth->handleCallback($request, $provider);
        } catch (ValidationException $exception) {
            return redirect('/login')->withErrors($exception->errors());
        }

        return redirect()->intended('/account');
    }
}
