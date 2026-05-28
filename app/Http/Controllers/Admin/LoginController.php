<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index()
    {
        $success = request()->session()->pull('success');
        $error = request()->session()->pull('error');

        return \App\Support\AdminReactShell::login([
            'action' => route('admin.login'),
            'csrfToken' => csrf_token(),
            'logoUrl' => asset('adm/images/logo.jpg'),
            'alerts' => [
                'message' => $error ?: $success,
                'type' => $error ? 'error' : 'success',
                'errors' => [],
            ],
            'labels' => [
                'email' => __('translate.email'),
                'password' => __('translate.password'),
                'rememberMe' => __('translate.rememberMe'),
                'login' => __('translate.login'),
                'logoAlt' => __('translate.brand'),
            ],
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->only(['email', 'password']);

        if (! Auth::guard('admin')->attempt($credentials)) {
            $request->session()->put('error', 'Your credentials are wrong. Please try again!');
            return redirect()->route('admin.login.form');
        }

        if (! Auth::guard('admin')->user()->is_active) {
            Auth::guard('admin')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            $request->session()->put('error', 'Account is unactive');

            return redirect()->route('admin.login.form');
        }

        $request->session()->regenerate();

        return redirect('/admin');
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login.form');
    }
}
