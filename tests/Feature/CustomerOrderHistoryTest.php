<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CustomerOrderHistoryTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        config(['scout.driver' => 'database']);
    }

    public function test_order_history_delegates_database_work_to_service_and_repository(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/Customer/OrderController.php'));
        $service = file_get_contents(app_path('Services/Customer/CustomerOrderService.php'));
        $repository = file_get_contents(app_path('Repositories/Customer/CustomerOrderRepository.php'));

        $this->assertStringContainsString('CustomerOrderService', $controller);
        $this->assertStringNotContainsString('use App\\Models\\', $controller);
        $this->assertStringNotContainsString('Order::', $controller);

        $this->assertStringContainsString('CustomerOrderRepository', $service);
        $this->assertStringNotContainsString('Order::', $service);
        $this->assertStringNotContainsString('Product::', $service);

        $this->assertStringContainsString('Order::query()', $repository);
    }

    public function test_customer_order_history_shows_only_authenticated_users_orders(): void
    {
        $customer = User::factory()->create(['name' => 'Order Owner']);
        $otherCustomer = User::factory()->create(['name' => 'Other Owner']);
        $ownedProduct = $this->product('Owned Serum');
        $otherProduct = $this->product('Other Serum');

        $ownedOrder = $this->order($customer, 'Owned Order Name', 180000);
        OrderItem::create([
            'order_id' => $ownedOrder->id,
            'product_id' => $ownedProduct->id,
            'qty' => 2,
            'unit_price' => 90000,
            'total_price' => 180000,
        ]);

        $otherOrder = $this->order($otherCustomer, 'Other Order Name', 250000);
        OrderItem::create([
            'order_id' => $otherOrder->id,
            'product_id' => $otherProduct->id,
            'qty' => 1,
            'unit_price' => 250000,
            'total_price' => 250000,
        ]);

        $this->actingAs($customer)
            ->get('/orders')
            ->assertOk()
            ->assertSee('data-react-component="CustomerOrderHistoryPage"', false)
            ->assertSee('Owned Order Name')
            ->assertSee('Owned Serum')
            ->assertSee('&quot;payment_total&quot;:180000', false)
            ->assertDontSee('Other Order Name')
            ->assertDontSee('Other Serum');
    }

    public function test_guest_order_history_page_does_not_expose_orders(): void
    {
        $customer = User::factory()->create();
        $order = $this->order($customer, 'Private Guest Order', 99000);

        $this->get('/orders')
            ->assertOk()
            ->assertSee('data-react-component="CustomerOrderHistoryPage"', false)
            ->assertSee('&quot;requiresAuth&quot;:true', false)
            ->assertDontSee('Private Guest Order')
            ->assertDontSee((string) $order->id);
    }

    private function order(User $customer, string $shippingName, int $paymentTotal): Order
    {
        return Order::create([
            'staff_id' => 1,
            'customer_id' => $customer->id,
            'shipping_fullname' => $shippingName,
            'shipping_mobile' => '0900111222',
            'payment_method' => 0,
            'shipping_ward_id' => '001',
            'shipping_housenumber_street' => '123 Order Street',
            'shipping_fee' => 0,
            'feeship_id' => null,
            'delivered_date' => now()->toDateString(),
            'price_total' => $paymentTotal,
            'discount_code' => '',
            'discount_amount' => 0,
            'sub_total' => $paymentTotal,
            'tax' => 0,
            'price_inc_tax_total' => $paymentTotal,
            'voucher_code' => '',
            'voucher_amount' => 0,
            'payment_total' => $paymentTotal,
            'status' => 'PENDING',
            'note' => null,
        ]);
    }

    private function product(string $name): Product
    {
        $brand = Brand::create(['name' => 'Order Brand ' . uniqid(), 'status' => 1, 'created_by' => 1]);
        $category = Category::create(['name' => 'Order Category ' . uniqid(), 'status' => 1, 'created_by' => 1]);

        return Product::create([
            'code' => 'ORD-' . uniqid(),
            'name' => $name,
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'price' => 100000,
            'discount_percentage' => 0,
            'discount_from_date' => now()->toDateString(),
            'discount_to_date' => now()->addDay()->toDateString(),
            'media_id' => 1,
            'inventory_qty' => 10,
            'description' => 'Order product description',
            'star' => 4.5,
            'featured' => 0,
            'created_by' => 1,
            'status' => 1,
        ]);
    }
}
