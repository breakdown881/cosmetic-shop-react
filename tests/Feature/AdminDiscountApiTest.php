<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Discount;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminDiscountApiTest extends TestCase
{
    use DatabaseTransactions;

    public function test_all_admin_roles_can_view_discounts(): void
    {
        $discount = Discount::create($this->discountData([
            'code' => 'WELCOME10',
            'discount_amount' => 10,
        ]));

        foreach (['MANAGER', 'ADMIN', 'STAFF'] as $role) {
            Sanctum::actingAs($this->admin($role));

            $this->getJson('/api/discounts')
                ->assertOk()
                ->assertJsonFragment(['code' => 'WELCOME10']);

            $this->getJson("/api/discounts/{$discount->id}")
                ->assertOk()
                ->assertJsonPath('data.id', $discount->id)
                ->assertJsonPath('data.discount_amount', 10);
        }
    }

    public function test_manager_and_admin_can_create_update_and_delete_discounts(): void
    {
        foreach (['MANAGER', 'ADMIN'] as $role) {
            Sanctum::actingAs($this->admin($role));

            $createResponse = $this->postJson('/api/discounts', $this->discountData([
                'code' => $role . '-SALE',
                'discount_amount' => 20000,
            ]))->assertCreated()
                ->assertJsonPath('data.code', $role . '-SALE')
                ->assertJsonPath('data.discount_amount', 20000);

            $discountId = $createResponse->json('data.id');

            $this->patchJson("/api/discounts/{$discountId}", $this->discountData([
                'code' => $role . '-UPDATED',
                'is_fixed' => 0,
                'discount_amount' => 15,
            ]))->assertOk()
                ->assertJsonPath('data.code', $role . '-UPDATED')
                ->assertJsonPath('data.is_fixed', 0)
                ->assertJsonPath('data.discount_amount', 15);

            $this->deleteJson("/api/discounts/{$discountId}")
                ->assertNoContent();
            $this->assertSoftDeleted('discounts', ['id' => $discountId]);
        }
    }

    public function test_staff_cannot_create_update_or_delete_discounts(): void
    {
        Sanctum::actingAs($this->admin('STAFF'));
        $discount = Discount::create($this->discountData(['code' => 'STAFF-VIEW']));

        $this->postJson('/api/discounts', $this->discountData(['code' => 'STAFF-CREATE']))
            ->assertForbidden();

        $this->patchJson("/api/discounts/{$discount->id}", $this->discountData(['code' => 'STAFF-UPDATE']))
            ->assertForbidden();

        $this->deleteJson("/api/discounts/{$discount->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('discounts', [
            'id' => $discount->id,
            'deleted_at' => null,
        ]);
    }

    public function test_admin_discount_page_permissions_match_role_rules(): void
    {
        foreach (['MANAGER', 'ADMIN'] as $role) {
            $this->actingAs($this->admin($role), 'admin')
                ->get('/admin/discounts')
                ->assertOk()
                ->assertSee('data-react-component="AdminSpaApp"', false)
                ->assertDontSee('"canCreate":true', false);

            $this->actingAs($this->admin($role), 'admin')
                ->get('/admin/discounts/create')
                ->assertOk()
                ->assertSee('data-react-component="AdminSpaApp"', false);
        }

        $this->actingAs($this->admin('STAFF'), 'admin')
            ->get('/admin/discounts')
            ->assertOk()
            ->assertSee('data-react-component="AdminSpaApp"', false)
            ->assertDontSee('"canCreate":false', false);

        $this->actingAs($this->admin('STAFF'), 'admin')
            ->get('/admin/discounts/create')
            ->assertForbidden();
    }

    public function test_discount_code_must_be_unique(): void
    {
        Sanctum::actingAs($this->admin('MANAGER'));
        Discount::create($this->discountData(['code' => 'UNIQUE']));

        $this->postJson('/api/discounts', $this->discountData(['code' => 'UNIQUE']))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['code']);
    }

    private function admin(string $role): Admin
    {
        return Admin::create([
            'name' => $role . ' Discount Admin',
            'email' => 'discount-admin-' . strtolower($role) . uniqid('', true) . '@example.test',
            'password' => 'password',
            'role' => $role,
            'is_active' => true,
        ]);
    }

    private function discountData(array $overrides = []): array
    {
        return array_merge([
            'code' => 'DISC-' . uniqid(),
            'description' => 'Discount description',
            'is_fixed' => 1,
            'discount_amount' => 10000,
            'starts_at' => now()->format('Y-m-d H:i:s'),
            'expires_at' => now()->addDays(7)->format('Y-m-d H:i:s'),
        ], $overrides);
    }
}
