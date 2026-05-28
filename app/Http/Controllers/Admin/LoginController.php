<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index()
    {
        $data = [];
        return view('admin.login.index', $data);
    }

    public function login(Request $request)
    {

        $credentials = $request->only(['email', 'password']);
        if (!Auth::guard('admin')->attempt($credentials)) {
            $request->session()->put('error', 'Your credentials are wrong. Please try again!');
            return redirect()->route('admin.login.form');
        }
        if (Auth::guard('admin')->user()->is_active == false) {
            Auth::guard('admin')->logout();
            $request->session()->put('error', 'Account is unactive');
        }
        return redirect()->route('admin.dashboard');
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login.form');
    }
}
