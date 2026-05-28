<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Province;
use App\Models\Transport;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminFeeShipApiTest extends TestCase
{
    use DatabaseTransactions;

    public function test_all_admin_roles_can_view_fee_ships(): void
    {
        $province = $this->province('Ho Chi Minh');
        $feeShip = Transport::create([
            'province_id' => $province->id,
            'price' => 25000,
        ]);

        foreach (['MANAGER', 'ADMIN', 'STAFF'] as $role) {
            Sanctum::actingAs($this->admin($role));

            $this->getJson('/api/feeships')
                ->assertOk()
                ->assertJsonFragment(['province_name' => 'Ho Chi Minh']);

            $this->getJson("/api/feeships/{$feeShip->id}")
                ->assertOk()
                ->assertJsonPath('data.id', $feeShip->id)
                ->assertJsonPath('data.price', 25000);
        }
    }

    public function test_manager_and_admin_can_create_update_and_delete_fee_ships(): void
    {
        foreach (['MANAGER', 'ADMIN'] as $role) {
            Sanctum::actingAs($this->admin($role));

            $createResponse = $this->postJson('/api/feeships', [
                'province_name' => $role . ' Province',
                'province_type' => 'city',
                'price' => 30000,
            ])->assertCreated()
                ->assertJsonPath('data.province_name', $role . ' Province')
                ->assertJsonPath('data.province_type', 'city')
                ->assertJsonPath('data.price', 30000);

            $feeShipId = $createResponse->json('data.id');
            $provinceId = $createResponse->json('data.province_id');

            $this->assertDatabaseHas('provinces', [
                'id' => $provinceId,
                'name' => $role . ' Province',
                'type' => 'city',
            ]);

            $this->patchJson("/api/feeships/{$feeShipId}", [
                'province_name' => $role . ' Updated Province',
                'province_type' => 'province',
                'price' => 35000,
            ])->assertOk()
                ->assertJsonPath('data.province_id', $provinceId)
                ->assertJsonPath('data.province_name', $role . ' Updated Province')
                ->assertJsonPath('data.province_type', 'province')
                ->assertJsonPath('data.price', 35000);

            $this->deleteJson("/api/feeships/{$feeShipId}")
                ->assertNoContent();
            $this->assertDatabaseMissing('transports', ['id' => $feeShipId]);
            $this->assertDatabaseMissing('provinces', ['id' => $provinceId]);
        }
    }

    public function test_staff_cannot_create_update_or_delete_fee_ships(): void
    {
        Sanctum::actingAs($this->admin('STAFF'));
        $province = $this->province('Staff Province');
        $feeShip = Transport::create([
            'province_id' => $province->id,
            'price' => 25000,
        ]);

        $this->postJson('/api/feeships', [
            'province_name' => 'Staff New Province',
            'province_type' => 'city',
            'price' => 30000,
        ])->assertForbidden();

        $this->patchJson("/api/feeships/{$feeShip->id}", [
            'province_name' => 'Staff Updated Province',
            'province_type' => 'province',
            'price' => 40000,
        ])->assertForbidden();

        $this->deleteJson("/api/feeships/{$feeShip->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('transports', ['id' => $feeShip->id]);
    }

    public function test_admin_fee_ship_page_permissions_match_role_rules(): void
    {
        foreach (['MANAGER', 'ADMIN'] as $role) {
            $this->actingAs($this->admin($role), 'admin')
                ->get('/admin/feeships')
                ->assertOk()
                ->assertSee('data-react-component="AdminSpaApp"', false)
                ->assertDontSee('"canCreate":true', false);

            $this->actingAs($this->admin($role), 'admin')
                ->get('/admin/feeships/create')
                ->assertOk()
                ->assertSee('data-react-component="AdminSpaApp"', false);
        }

        $this->actingAs($this->admin('STAFF'), 'admin')
            ->get('/admin/feeships')
            ->assertOk()
            ->assertSee('data-react-component="AdminSpaApp"', false)
            ->assertDontSee('"canCreate":false', false);

        $this->actingAs($this->admin('STAFF'), 'admin')
            ->get('/admin/feeships/create')
            ->assertForbidden();
    }

    public function test_staff_cannot_write_fee_ships_through_admin_api(): void
    {
        $province = $this->province('Admin API Province');
        $feeShip = Transport::create([
            'province_id' => $province->id,
            'price' => 25000,
        ]);

        $this->actingAs($this->admin('STAFF'), 'admin')
            ->postJson('/admin/api/feeships', [
                'province_name' => 'Admin API New Province',
                'province_type' => 'city',
                'price' => 30000,
            ])->assertForbidden();

        $this->actingAs($this->admin('STAFF'), 'admin')
            ->patchJson("/admin/api/feeships/{$feeShip->id}", [
                'province_name' => 'Admin API Updated Province',
                'province_type' => 'province',
                'price' => 35000,
            ])->assertForbidden();

        $this->actingAs($this->admin('STAFF'), 'admin')
            ->deleteJson("/admin/api/feeships/{$feeShip->id}")
            ->assertForbidden();
    }

    private function admin(string $role): Admin
    {
        return Admin::create([
            'name' => $role . ' FeeShip Admin',
            'email' => 'feeship-admin-' . strtolower($role) . uniqid('', true) . '@example.test',
            'password' => 'password',
            'role' => $role,
            'is_active' => true,
        ]);
    }

    private function province(string $name): Province
    {
        return Province::create([
            'name' => $name,
            'type' => 'city',
        ]);
    }
}
