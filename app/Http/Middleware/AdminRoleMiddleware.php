<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminRoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $admin = Auth::guard('admin')->user() ?: $request->user();

        if (! $admin || ! property_exists($admin, 'role') && ! isset($admin->role)) {
            return $this->deny($request);
        }

        if (property_exists($admin, 'is_active') || isset($admin->is_active)) {
            if (! $admin->is_active) {
                return $this->deny($request);
            }
        }

        if (! in_array($admin->role, $roles, true)) {
            return $this->deny($request);
        }

        return $next($request);
    }

    private function deny(Request $request): Response
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        abort(403);
    }
}
