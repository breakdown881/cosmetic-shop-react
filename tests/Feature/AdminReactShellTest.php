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

        foreach (['/admin', '/admin/brands', '/admin/orders', '/admin/images', '/admin/newsletters'] as $path) {
            $this->actingAs($admin, 'admin')
                ->get($path)
                ->assertOk()
                ->assertSee('id="react-admin-shell"', false)
                ->assertSee('data-react-component="AdminAppShell"', false);
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
