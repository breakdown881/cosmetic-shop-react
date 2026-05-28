<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use DatabaseTransactions;

    public function test_dashboard_shell_does_not_embed_page_data(): void
    {
        $admin = Admin::create([
            'name' => 'Dashboard Shell Manager',
            'email' => 'dashboard-shell-manager-' . uniqid('', true) . '@example.test',
            'password' => 'password',
            'role' => 'MANAGER',
            'is_active' => true,
        ]);

        $this->actingAs($admin, 'admin')
            ->get('/admin')
            ->assertOk()
            ->assertSee('data-react-component="AdminSpaApp"', false)
            ->assertDontSee('"metrics":', false)
            ->assertDontSee('"orders":', false);
    }

    public function test_dashboard_api_returns_real_order_metrics_and_recent_orders(): void
    {
        $admin = Admin::create([
            'name' => 'Dashboard Manager',
            'email' => 'dashboard-manager-' . uniqid('', true) . '@example.test',
            'password' => 'password',
            'role' => 'MANAGER',
            'is_active' => true,
        ]);
        $customer = User::factory()->create(['name' => 'Nguyen Dashboard']);

        Order::create($this->orderData($admin, $customer, [
            'shipping_mobile' => '0900111222',
            'payment_total' => 350000,
            'status' => 'COMPLETE',
        ]));
        Order::create($this->orderData($admin, $customer, [
            'shipping_mobile' => '0900333444',
            'payment_total' => 120000,
            'status' => 'CANCELLED',
        ]));

        $this->actingAs($admin, 'admin')
            ->getJson('/admin/api/dashboard')
            ->assertOk()
            ->assertJsonPath('metrics.0.key', 'orders')
            ->assertJsonPath('metrics.0.value', 2)
            ->assertJsonPath('metrics.1.key', 'revenue')
            ->assertJsonPath('metrics.1.value', 350000)
            ->assertJsonPath('metrics.2.key', 'cancelledOrders')
            ->assertJsonPath('metrics.2.value', 1)
            ->assertJsonFragment(['customerName' => 'Nguyen Dashboard', 'customerPhone' => '0900111222'])
            ->assertJsonFragment(['customerName' => 'Nguyen Dashboard', 'customerPhone' => '0900333444']);
    }

    private function orderData(Admin $admin, User $customer, array $overrides = []): array
    {
        return array_merge([
            'staff_id' => $admin->id,
            'customer_id' => $customer->id,
            'shipping_fullname' => $customer->name,
            'shipping_mobile' => '0900000000',
            'payment_method' => 0,
            'shipping_ward_id' => '1',
            'shipping_housenumber_street' => '123 Dashboard Street',
            'shipping_fee' => 0,
            'delivered_date' => now()->addDay()->toDateString(),
            'price_total' => 100000,
            'discount_code' => '',
            'discount_amount' => 0,
            'sub_total' => 100000,
            'tax' => 0,
            'price_inc_tax_total' => 100000,
            'voucher_code' => '',
            'voucher_amount' => 0,
            'payment_total' => 100000,
            'status' => 'PENDING',
            'note' => null,
        ], $overrides);
    }
}
