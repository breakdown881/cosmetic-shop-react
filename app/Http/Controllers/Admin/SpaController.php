<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\AdminReactShell;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SpaController extends Controller
{
    public function index(Request $request, string $path = '')
    {
        $this->authorizePath(trim($path, '/'));

        return AdminReactShell::spa();
    }

    private function authorizePath(string $path): void
    {
        $role = Auth::guard('admin')->user()?->role;

        if ($this->matches($path, ['roles', 'staffs'])) {
            abort_unless($role === 'MANAGER', 403);
        }

        if ($this->matches($path, ['brands', 'categories', 'products', 'comments', 'images', 'newsletters'])) {
            abort_unless(in_array($role, ['MANAGER', 'ADMIN'], true), 403);
        }

        if ($this->matches($path, ['discounts/create', 'discounts/edit', 'feeships/create', 'feeships/edit'])) {
            abort_unless(in_array($role, ['MANAGER', 'ADMIN'], true), 403);
        }
    }

    private function matches(string $path, array $prefixes): bool
    {
        foreach ($prefixes as $prefix) {
            if ($path === $prefix || str_starts_with($path, $prefix . '/')) {
                return true;
            }
        }

        return false;
    }
}
