<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Mockery;
use Tests\TestCase;

class CustomerSocialAuthTest extends TestCase
{
    use DatabaseTransactions;

    public function test_social_auth_uses_controller_service_repository_and_gateway_pattern(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/Customer/SocialAuthController.php'));
        $service = file_get_contents(app_path('Services/Customer/CustomerSocialAuthService.php'));
        $repository = file_get_contents(app_path('Repositories/Customer/CustomerAuthRepository.php'));

        $this->assertStringContainsString('CustomerSocialAuthService', $controller);
        $this->assertStringNotContainsString('use App\\Models\\', $controller);
        $this->assertStringNotContainsString('User::', $controller);
        $this->assertStringNotContainsString('Socialite::', $controller);

        $this->assertStringContainsString('CustomerAuthRepository', $service);
        $this->assertStringContainsString('CustomerSocialiteGateway', $service);
        $this->assertStringNotContainsString('User::', $service);

        $this->assertStringContainsString('User::query()', $repository);
        $this->assertStringContainsString('findBySocialProvider', $repository);
        $this->assertStringContainsString('findByEmail', $repository);
    }

    public function test_google_and_facebook_redirects_delegate_to_socialite_gateway(): void
    {
        $gateway = Mockery::mock(\App\Services\Customer\CustomerSocialiteGateway::class);
        $gateway->shouldReceive('redirectUrl')->once()->with('google')->andReturn('https://accounts.google.test/oauth');
        $gateway->shouldReceive('redirectUrl')->once()->with('facebook')->andReturn('https://facebook.test/oauth');
        $this->app->instance(\App\Services\Customer\CustomerSocialiteGateway::class, $gateway);

        $this->get('/auth/google/redirect')
            ->assertRedirect('https://accounts.google.test/oauth');

        $this->get('/auth/facebook/redirect')
            ->assertRedirect('https://facebook.test/oauth');
    }

    public function test_google_callback_creates_customer_and_logs_them_in(): void
    {
        $gateway = Mockery::mock(\App\Services\Customer\CustomerSocialiteGateway::class);
        $gateway->shouldReceive('user')->once()->with('google')->andReturn([
            'id' => 'google-123',
            'name' => 'Google Customer',
            'email' => 'google-customer@example.test',
            'avatar' => 'https://cdn.example.test/google-avatar.jpg',
        ]);
        $this->app->instance(\App\Services\Customer\CustomerSocialiteGateway::class, $gateway);

        $this->get('/auth/google/callback')->assertRedirect('/account');

        $user = User::query()->where('email', 'google-customer@example.test')->first();

        $this->assertNotNull($user);
        $this->assertSame('google', $user->provider_name);
        $this->assertSame('google-123', $user->provider_id);
        $this->assertSame('https://cdn.example.test/google-avatar.jpg', $user->avatar_url);
        $this->assertAuthenticatedAs($user);
    }

    public function test_social_callback_links_existing_customer_by_email(): void
    {
        $existing = User::factory()->create([
            'name' => 'Existing Customer',
            'email' => 'existing-social@example.test',
        ]);

        $gateway = Mockery::mock(\App\Services\Customer\CustomerSocialiteGateway::class);
        $gateway->shouldReceive('user')->once()->with('facebook')->andReturn([
            'id' => 'facebook-456',
            'name' => 'Facebook Name',
            'email' => 'existing-social@example.test',
            'avatar' => null,
        ]);
        $this->app->instance(\App\Services\Customer\CustomerSocialiteGateway::class, $gateway);

        $this->get('/auth/facebook/callback')->assertRedirect('/account');

        $existing->refresh();

        $this->assertSame('Existing Customer', $existing->name);
        $this->assertSame('facebook', $existing->provider_name);
        $this->assertSame('facebook-456', $existing->provider_id);
        $this->assertAuthenticatedAs($existing);
    }
}
