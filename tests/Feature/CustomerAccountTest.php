<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CustomerAccountTest extends TestCase
{
    use DatabaseTransactions;

    public function test_account_profile_delegates_database_work_to_service_and_repository(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/Customer/AccountController.php'));
        $service = file_get_contents(app_path('Services/Customer/CustomerAccountService.php'));
        $repository = file_get_contents(app_path('Repositories/Customer/CustomerAccountRepository.php'));

        $this->assertStringContainsString('CustomerAccountService', $controller);
        $this->assertStringNotContainsString('use App\\Models\\', $controller);
        $this->assertStringNotContainsString('User::', $controller);

        $this->assertStringContainsString('CustomerAccountRepository', $service);
        $this->assertStringNotContainsString('User::', $service);

        $this->assertStringContainsString('User::query()', $repository);
    }

    public function test_guest_account_page_does_not_expose_profile(): void
    {
        $user = User::factory()->create(['email' => 'private@example.test']);

        $this->get('/account')
            ->assertOk()
            ->assertSee('data-react-component="CustomerAccountPage"', false)
            ->assertSee('&quot;requiresAuth&quot;:true', false)
            ->assertDontSee($user->email);
    }

    public function test_authenticated_customer_can_view_and_update_own_profile(): void
    {
        $user = User::factory()->create([
            'name' => 'Old Customer',
            'email' => 'old-customer@example.test',
        ]);

        $this->actingAs($user)
            ->get('/account')
            ->assertOk()
            ->assertSee('data-react-component="CustomerAccountPage"', false)
            ->assertSee('Old Customer')
            ->assertSee('old-customer@example.test');

        $this->actingAs($user)
            ->patchJson('/account', [
                'name' => 'New Customer',
                'email' => 'new-customer@example.test',
            ])
            ->assertOk()
            ->assertJsonPath('data.name', 'New Customer')
            ->assertJsonPath('data.email', 'new-customer@example.test');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Customer',
            'email' => 'new-customer@example.test',
        ]);
    }

    public function test_account_update_validates_unique_email(): void
    {
        $user = User::factory()->create(['email' => 'owner@example.test']);
        $otherUser = User::factory()->create(['email' => 'taken@example.test']);

        $this->actingAs($user)
            ->patchJson('/account', [
                'name' => 'Owner',
                'email' => $otherUser->email,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }
}
