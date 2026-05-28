<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminReactShellTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_pages_are_served_by_react_shell_without_blade_views(): void
    {
        $admin = Admin::create([
            'name' => 'React Shell Admin',
            'email' => 'react-shell-' . uniqid('', true) . '@example.test',
            'password' => 'password',
            'role' => 'MANAGER',
            'is_active' => true,
        ]);

        foreach ([
            '/admin',
            '/admin/brands',
            '/admin/brands/create',
            '/admin/orders',
            '/admin/orders/create',
            '/admin/images',
            '/admin/newsletters',
            '/admin/products/123/comments',
        ] as $path) {
            $this->actingAs($admin, 'admin')
                ->get($path)
                ->assertOk()
                ->assertSee('id="react-admin-shell"', false)
                ->assertSee('data-react-component="AdminSpaApp"', false)
                ->assertDontSee('AdminApiResourceManager')
                ->assertDontSee('"page":', false);
        }
    }

    public function test_legacy_admin_page_routes_are_replaced_by_a_single_spa_route(): void
    {
        $routeNames = collect(app('router')->getRoutes())
            ->map(fn ($route) => $route->getName())
            ->filter()
            ->values();

        $this->assertTrue($routeNames->contains('admin.spa'));

        foreach ([
            'admin.dashboard',
            'admin.brand.index',
            'admin.category.index',
            'admin.product.index',
            'admin.order.index',
            'admin.customer.index',
            'admin.discount.index',
            'admin.feeship.index',
            'admin.role.index',
            'admin.staff.index',
            'admin.media.index',
            'admin.newsletter.index',
        ] as $legacyRouteName) {
            $this->assertFalse($routeNames->contains($legacyRouteName), "{$legacyRouteName} should be owned by React routing.");
        }
    }

    public function test_admin_controllers_do_not_render_blade_views(): void
    {
        $adminControllerFiles = glob(app_path('Http/Controllers/Admin/*.php'));

        foreach ($adminControllerFiles as $file) {
            $contents = file_get_contents($file);

            $this->assertStringNotContainsString('return view(', $contents, basename($file) . ' still renders a Blade view.');
        }
    }
}
