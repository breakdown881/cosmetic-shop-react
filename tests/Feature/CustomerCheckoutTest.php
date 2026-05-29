<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Discount;
use App\Models\Order;
use App\Models\Product;
use App\Models\Province;
use App\Models\Transport;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CustomerCheckoutTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        config(['scout.driver' => 'database']);
    }

    public function test_checkout_delegates_database_work_to_service_and_repository(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/Customer/CheckoutController.php'));
        $service = file_get_contents(app_path('Services/Customer/CustomerCheckoutService.php'));
        $repository = file_get_contents(app_path('Repositories/Customer/CustomerCheckoutRepository.php'));

        $this->assertStringContainsString('CustomerCheckoutService', $controller);
        $this->assertStringNotContainsString('use App\\Models\\', $controller);
        $this->assertStringNotContainsString('Order::', $controller);

        $this->assertStringContainsString('CustomerCheckoutRepository', $service);
        $this->assertStringNotContainsString('Order::', $service);
        $this->assertStringNotContainsString('Product::', $service);

        $this->assertStringContainsString('Order::create', $repository);
        $this->assertStringContainsString('DB::transaction', $repository);
    }

    public function test_checkout_page_renders_customer_checkout_component_with_cart_summary(): void
    {
        $product = $this->product('Checkout Serum', 300000, 5, 10);
        $feeShip = $this->feeShip(25000);

        $this->actingAs(User::factory()->create())
            ->withSession(['customer_cart' => [$product->id => 2]])
            ->get('/checkout')
            ->assertOk()
            ->assertSee('data-react-component="CustomerCheckoutPage"', false)
            ->assertSee('Checkout Serum')
            ->assertSee((string) $feeShip->price);
    }

    public function test_customer_can_checkout_cart_and_order_is_created_transactionally(): void
    {
        $customer = User::factory()->create();
        $product = $this->product('Paid Serum', 300000, 5, 10);
        $feeShip = $this->feeShip(25000);
        $discount = Discount::create([
            'code' => 'WELCOME50',
            'description' => 'Welcome discount',
            'is_fixed' => 1,
            'discount_amount' => 50000,
            'starts_at' => now()->subDay(),
            'expires_at' => now()->addDay(),
        ]);

        $response = $this
            ->actingAs($customer)
            ->withSession(['customer_cart' => [$product->id => 2]])
            ->postJson('/checkout', [
                'shipping_fullname' => 'Nguyen Van A',
                'shipping_mobile' => '0900111222',
                'shipping_ward_id' => '001',
                'shipping_housenumber_street' => '123 Beauty Street',
                'payment_method' => 0,
                'feeship_id' => $feeShip->id,
                'discount_code' => $discount->code,
                'note' => 'Leave at reception',
            ])
            ->assertAccepted()
            ->assertJsonPath('data.status', 'COMPLETED')
            ->assertJsonPath('data.order.status', 'PENDING')
            ->assertJsonPath('data.order.sub_total', 540000)
            ->assertJsonPath('data.order.discount_amount', 50000)
            ->assertJsonPath('data.order.shipping_fee', 25000)
            ->assertJsonPath('data.order.payment_total', 515000)
            ->assertJsonPath('data.order.items.0.unit_price', 270000)
            ->assertJsonPath('data.order.items.0.qty', 2);

        $orderId = $response->json('data.order.id');
        $this->assertDatabaseHas('customer_checkout_requests', [
            'id' => $response->json('data.id'),
            'customer_id' => $customer->id,
            'order_id' => $orderId,
            'status' => 'COMPLETED',
        ]);
        $this->assertDatabaseHas('orders', [
            'id' => $orderId,
            'customer_id' => $customer->id,
            'status' => 'PENDING',
            'payment_total' => 515000,
        ]);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $orderId,
            'product_id' => $product->id,
            'qty' => 2,
            'unit_price' => 270000,
            'total_price' => 540000,
        ]);
        $this->assertSame([], session('customer_cart'));
    }

    public function test_checkout_rejects_empty_cart_and_expired_discount(): void
    {
        $customer = User::factory()->create();
        $expiredDiscount = Discount::create([
            'code' => 'EXPIRED',
            'description' => 'Expired discount',
            'is_fixed' => 1,
            'discount_amount' => 10000,
            'starts_at' => now()->subDays(2),
            'expires_at' => now()->subDay(),
        ]);

        $this->actingAs($customer)
            ->postJson('/checkout', $this->checkoutPayload(['discount_code' => $expiredDiscount->code]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['cart']);
    }

    private function checkoutPayload(array $overrides = []): array
    {
        return array_merge([
            'shipping_fullname' => 'Nguyen Van A',
            'shipping_mobile' => '0900111222',
            'shipping_ward_id' => '001',
            'shipping_housenumber_street' => '123 Beauty Street',
            'payment_method' => 0,
        ], $overrides);
    }

    private function product(string $name, int $price, int $inventoryQty, int $discountPercentage = 0): Product
    {
        $brand = Brand::create(['name' => 'Checkout Brand ' . uniqid(), 'status' => 1, 'created_by' => 1]);
        $category = Category::create(['name' => 'Checkout Category ' . uniqid(), 'status' => 1, 'created_by' => 1]);

        return Product::create([
            'code' => 'CHK-' . uniqid(),
            'name' => $name,
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'price' => $price,
            'discount_percentage' => $discountPercentage,
            'discount_from_date' => now()->toDateString(),
            'discount_to_date' => now()->addDay()->toDateString(),
            'media_id' => 1,
            'inventory_qty' => $inventoryQty,
            'description' => 'Checkout product description',
            'star' => 4.5,
            'featured' => 0,
            'created_by' => 1,
            'status' => 1,
        ]);
    }

    private function feeShip(int $price): Transport
    {
        $province = Province::create(['name' => 'Checkout Province ' . uniqid(), 'type' => 'City']);

        return Transport::create([
            'province_id' => $province->id,
            'price' => $price,
        ]);
    }
}
