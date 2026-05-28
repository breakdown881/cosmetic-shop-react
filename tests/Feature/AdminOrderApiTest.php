<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Discount;
use App\Models\Order;
use App\Models\Product;
use App\Models\Province;
use App\Models\Transport;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminOrderApiTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        config(['scout.driver' => 'database']);
    }

    public function test_all_admin_roles_can_create_view_update_and_delete_orders(): void
    {
        foreach (['MANAGER', 'ADMIN', 'STAFF'] as $role) {
            Sanctum::actingAs($this->admin($role));

            $customer = User::factory()->create();
            $firstProduct = $this->product($role . ' Serum', 100000);
            $secondProduct = $this->product($role . ' Toner', 50000);
            $discount = Discount::create($this->discountData([
                'code' => $role . '-FIXED',
                'is_fixed' => 1,
                'discount_amount' => 30000,
            ]));
            $feeShip = $this->feeShip(15000);

            $createResponse = $this->postJson('/api/orders', $this->orderData($customer, [
                'discount_code' => $discount->code,
                'feeship_id' => $feeShip->id,
                'note' => $role . ' first note',
                'items' => [
                    ['product_id' => $firstProduct->id, 'qty' => 2],
                    ['product_id' => $secondProduct->id, 'qty' => 1],
                ],
            ]))->assertCreated()
                ->assertJsonPath('data.status', 'PENDING')
                ->assertJsonPath('data.payment_method', 0)
                ->assertJsonPath('data.sub_total', 250000)
                ->assertJsonPath('data.discount_amount', 30000)
                ->assertJsonPath('data.shipping_fee', 15000)
                ->assertJsonPath('data.payment_total', 235000)
                ->assertJsonPath('data.note', $role . ' first note')
                ->assertJsonCount(2, 'data.items');

            $orderId = $createResponse->json('data.id');

            $this->getJson("/api/orders/{$orderId}")
                ->assertOk()
                ->assertJsonPath('data.id', $orderId)
                ->assertJsonPath('data.customer.id', $customer->id);

            $percentDiscount = Discount::create($this->discountData([
                'code' => $role . '-PERCENT',
                'is_fixed' => 0,
                'discount_amount' => 10,
            ]));

            $this->patchJson("/api/orders/{$orderId}", $this->orderData($customer, [
                'payment_method' => 1,
                'status' => 'COMPLETE',
                'discount_code' => $percentDiscount->code,
                'feeship_id' => null,
                'note' => $role . ' updated note',
                'items' => [
                    ['product_id' => $firstProduct->id, 'qty' => 1],
                ],
            ]))->assertOk()
                ->assertJsonPath('data.status', 'COMPLETE')
                ->assertJsonPath('data.payment_method', 1)
                ->assertJsonPath('data.sub_total', 100000)
                ->assertJsonPath('data.discount_amount', 10000)
                ->assertJsonPath('data.shipping_fee', 0)
                ->assertJsonPath('data.payment_total', 90000)
                ->assertJsonPath('data.note', $role . ' updated note')
                ->assertJsonCount(1, 'data.items');

            $this->deleteJson("/api/orders/{$orderId}")
                ->assertNoContent();
            $this->assertDatabaseMissing('orders', ['id' => $orderId]);
            $this->assertDatabaseMissing('order_items', ['order_id' => $orderId]);
        }
    }

    public function test_order_validates_status_payment_method_and_items(): void
    {
        Sanctum::actingAs($this->admin('MANAGER'));
        $customer = User::factory()->create();

        $this->postJson('/api/orders', $this->orderData($customer, [
            'payment_method' => 9,
            'status' => 'UNKNOWN',
            'items' => [],
        ]))->assertUnprocessable()
            ->assertJsonValidationErrors(['payment_method', 'status', 'items']);
    }

    public function test_order_accepts_operational_statuses(): void
    {
        Sanctum::actingAs($this->admin('MANAGER'));
        $customer = User::factory()->create();
        $product = $this->product('Status Serum', 100000);

        foreach (['SHIPPING', 'CANCELLED', 'REFUNDED', 'FAILED'] as $status) {
            $this->postJson('/api/orders', $this->orderData($customer, [
                'status' => $status,
                'items' => [
                    ['product_id' => $product->id, 'qty' => 1],
                ],
            ]))->assertCreated()
                ->assertJsonPath('data.status', $status);
        }
    }

    public function test_all_admin_roles_can_open_order_admin_pages(): void
    {
        foreach (['MANAGER', 'ADMIN', 'STAFF'] as $role) {
            $this->actingAs($this->admin($role), 'admin')
                ->get('/admin/orders')
                ->assertOk()
                ->assertSee('"canCreate":true', false)
                ->assertSee('"canEdit":true', false)
                ->assertSee('"canDelete":true', false);

            $this->actingAs($this->admin($role), 'admin')
                ->get('/admin/orders/create')
                ->assertOk();
        }
    }

    private function admin(string $role): Admin
    {
        return Admin::create([
            'name' => $role . ' Order Admin',
            'email' => 'order-admin-' . strtolower($role) . uniqid('', true) . '@example.test',
            'password' => 'password',
            'role' => $role,
            'is_active' => true,
        ]);
    }

    private function product(string $name, int $price): Product
    {
        $brand = Brand::create(['name' => 'Brand ' . uniqid(), 'status' => 1, 'created_by' => 1]);
        $category = Category::create(['name' => 'Category ' . uniqid(), 'parent_id' => 0, 'status' => 1, 'created_by' => 1]);

        return Product::create([
            'code' => 'PRD-' . uniqid(),
            'name' => $name,
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'price' => $price,
            'discount_percentage' => 0,
            'discount_from_date' => now()->toDateString(),
            'discount_to_date' => now()->addDay()->toDateString(),
            'media_id' => 1,
            'inventory_qty' => 10,
            'description' => 'Product description',
            'star' => 4.5,
            'featured' => 0,
            'created_by' => 1,
            'status' => 1,
        ]);
    }

    private function feeShip(int $price): Transport
    {
        $province = Province::create(['name' => 'Province ' . uniqid(), 'type' => 'city']);

        return Transport::create([
            'province_id' => $province->id,
            'price' => $price,
        ]);
    }

    private function discountData(array $overrides = []): array
    {
        return array_merge([
            'code' => 'DISC-' . uniqid(),
            'description' => 'Discount description',
            'is_fixed' => 1,
            'discount_amount' => 10000,
            'starts_at' => now()->subDay()->format('Y-m-d H:i:s'),
            'expires_at' => now()->addDays(7)->format('Y-m-d H:i:s'),
        ], $overrides);
    }

    private function orderData(User $customer, array $overrides = []): array
    {
        return array_merge([
            'customer_id' => $customer->id,
            'shipping_fullname' => $customer->name,
            'shipping_mobile' => '0900000000',
            'payment_method' => 0,
            'shipping_ward_id' => '1',
            'shipping_housenumber_street' => '123 Test Street',
            'delivered_date' => now()->addDays(3)->toDateString(),
            'discount_code' => null,
            'feeship_id' => null,
            'status' => 'PENDING',
            'note' => 'Order note',
            'items' => [],
        ], $overrides);
    }
}
