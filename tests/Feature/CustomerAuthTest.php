<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CustomerAuthTest extends TestCase
{
    use DatabaseTransactions;

    public function test_customer_auth_uses_controller_service_repository_pattern(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/Customer/AuthController.php'));
        $service = file_get_contents(app_path('Services/Customer/CustomerAuthService.php'));
        $repository = file_get_contents(app_path('Repositories/Customer/CustomerAuthRepository.php'));

        $this->assertStringContainsString('CustomerAuthService', $controller);
        $this->assertStringNotContainsString('use App\\Models\\', $controller);
        $this->assertStringNotContainsString('User::', $controller);
        $this->assertStringNotContainsString('Hash::', $controller);

        $this->assertStringContainsString('CustomerAuthRepository', $service);
        $this->assertStringNotContainsString('User::', $service);

        $this->assertStringContainsString('User::query()', $repository);
    }

    public function test_login_and_register_pages_render_customer_shell_not_admin_shell(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('customer-site', false)
            ->assertSee('data-react-component="CustomerLoginPage"', false)
            ->assertSee('&quot;csrfToken&quot;', false)
            ->assertDontSee('admin-site', false);

        $this->get('/register')
            ->assertOk()
            ->assertSee('customer-site', false)
            ->assertSee('data-react-component="CustomerRegisterPage"', false)
            ->assertSee('&quot;csrfToken&quot;', false)
            ->assertDontSee('admin-site', false);
    }

    public function test_customer_can_register_and_is_logged_in(): void
    {
        $this->post('/register', [
            'name' => 'New Customer',
            'email' => 'new-auth-customer@example.test',
            'password' => 'secret-password',
            'password_confirmation' => 'secret-password',
        ])->assertRedirect('/account');

        $user = User::query()->where('email', 'new-auth-customer@example.test')->first();

        $this->assertNotNull($user);
        $this->assertTrue(Hash::check('secret-password', $user->password));
        $this->assertAuthenticatedAs($user);
    }

    public function test_customer_can_login_and_logout(): void
    {
        $user = User::factory()->create([
            'email' => 'login-customer@example.test',
            'password' => 'secret-password',
        ]);

        $this->post('/login', [
            'email' => 'login-customer@example.test',
            'password' => 'secret-password',
        ])->assertRedirect('/account');

        $this->assertAuthenticatedAs($user);

        $this->post('/logout')->assertRedirect('/');

        $this->assertGuest();
    }

    public function test_invalid_login_validation_does_not_leak_sensitive_details(): void
    {
        User::factory()->create([
            'email' => 'known-customer@example.test',
            'password' => 'correct-password',
        ]);

        $this->from('/login')->post('/login', [
            'email' => 'known-customer@example.test',
            'password' => 'wrong-password',
        ])
            ->assertRedirect('/login')
            ->assertSessionHasErrors(['email'])
            ->assertSessionDoesntHaveErrors(['password']);

        $this->assertGuest();
    }
}
