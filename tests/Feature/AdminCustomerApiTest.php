<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminCustomerApiTest extends TestCase
{
    use DatabaseTransactions;

    public function test_all_admin_roles_can_view_update_and_delete_customers_through_api(): void
    {
        foreach (['MANAGER', 'ADMIN', 'STAFF'] as $role) {
            Sanctum::actingAs($this->admin($role));
            $customer = User::factory()->create();

            $this->getJson('/api/customers')
                ->assertOk()
                ->assertJsonFragment(['email' => $customer->email]);

            $this->getJson("/api/customers/{$customer->id}")
                ->assertOk()
                ->assertJsonPath('data.id', $customer->id);

            $this->patchJson("/api/customers/{$customer->id}", [
                'name' => "{$role} Updated Customer",
                'email' => "customer-{$role}-" . uniqid() . '@example.test',
            ])->assertOk()
                ->assertJsonPath('data.name', "{$role} Updated Customer");

            $this->deleteJson("/api/customers/{$customer->id}")
                ->assertNoContent();
            $this->assertDatabaseMissing('users', ['id' => $customer->id]);
        }
    }

    public function test_all_admin_roles_can_open_customer_admin_page_and_use_admin_customer_api(): void
    {
        foreach (['MANAGER', 'ADMIN', 'STAFF'] as $role) {
            $admin = $this->admin($role);
            $customer = User::factory()->create();

            $this->actingAs($admin, 'admin')
                ->get('/admin/customers')
                ->assertOk();

            $this->actingAs($admin, 'admin')
                ->patchJson("/admin/api/customers/{$customer->id}", [
                    'name' => "{$role} Web Customer",
                    'email' => "web-customer-{$role}-" . uniqid() . '@example.test',
                ])->assertOk()
                ->assertJsonPath('data.name', "{$role} Web Customer");
        }
    }

    public function test_customer_update_validates_unique_email(): void
    {
        Sanctum::actingAs($this->admin('MANAGER'));
        $firstCustomer = User::factory()->create();
        $secondCustomer = User::factory()->create();

        $this->patchJson("/api/customers/{$secondCustomer->id}", [
            'name' => 'Duplicate Email Customer',
            'email' => $firstCustomer->email,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_customer_password_cannot_be_updated_from_admin(): void
    {
        Sanctum::actingAs($this->admin('MANAGER'));
        $customer = User::factory()->create();
        $originalPassword = $customer->password;

        $this->patchJson("/api/customers/{$customer->id}", [
            'name' => 'Password Update Attempt',
            'email' => $customer->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['password', 'password_confirmation']);

        $this->assertSame($originalPassword, $customer->refresh()->password);
    }

    private function admin(string $role): Admin
    {
        return Admin::create([
            'name' => $role . ' Customer Admin',
            'email' => 'customer-admin-' . strtolower($role) . uniqid('', true) . '@example.test',
            'password' => 'password',
            'role' => $role,
            'is_active' => true,
        ]);
    }
}
