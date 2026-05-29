<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CustomerCartTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        config(['scout.driver' => 'database']);
    }

    public function test_cart_flow_delegates_database_work_to_service_and_repository(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/Customer/CartController.php'));
        $service = file_get_contents(app_path('Services/Customer/CustomerCartService.php'));
        $repository = file_get_contents(app_path('Repositories/Customer/CustomerCartRepository.php'));

        $this->assertStringContainsString('CustomerCartService', $controller);
        $this->assertStringNotContainsString('use App\\Models\\', $controller);
        $this->assertStringNotContainsString('Product::', $controller);

        $this->assertStringContainsString('CustomerCartRepository', $service);
        $this->assertStringNotContainsString('Product::', $service);
        $this->assertStringNotContainsString('DB::', $service);
        $this->assertStringNotContainsString('Storage::', $service);

        $this->assertStringContainsString('Product::query()', $repository);
    }

    public function test_customer_can_add_update_and_remove_products_in_session_cart(): void
    {
        $product = $this->product('Cart Serum', 250000, 5, 20);

        $this->postJson('/cart/items', [
            'product_id' => $product->id,
            'quantity' => 2,
        ])->assertCreated()
            ->assertJsonPath('data.items.0.product_id', $product->id)
            ->assertJsonPath('data.items.0.quantity', 2)
            ->assertJsonPath('data.items.0.sale_price', 200000)
            ->assertJsonPath('data.total', 400000);

        $this->patchJson("/cart/items/{$product->id}", [
            'quantity' => 3,
        ])->assertOk()
            ->assertJsonPath('data.items.0.quantity', 3)
            ->assertJsonPath('data.total', 600000);

        $this->deleteJson("/cart/items/{$product->id}")
            ->assertOk()
            ->assertJsonPath('data.items', [])
            ->assertJsonPath('data.total', 0);
    }

    public function test_cart_rejects_quantity_greater_than_inventory(): void
    {
        $product = $this->product('Low Stock Serum', 250000, 1);

        $this->postJson('/cart/items', [
            'product_id' => $product->id,
            'quantity' => 2,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['quantity']);
    }

    public function test_cart_page_renders_customer_cart_component_with_session_items(): void
    {
        $product = $this->product('Rendered Cart Serum', 300000, 4, 10);

        $this->withSession([
            'customer_cart' => [
                $product->id => 2,
            ],
        ])->get('/cart')
            ->assertOk()
            ->assertSee('data-react-component="CustomerCartPage"', false)
            ->assertSee('Rendered Cart Serum')
            ->assertSee('&quot;total&quot;:540000', false);
    }

    private function product(string $name, int $price, int $inventoryQty, int $discountPercentage = 0): Product
    {
        $brand = Brand::create(['name' => 'Cart Brand ' . uniqid(), 'status' => 1, 'created_by' => 1]);
        $category = Category::create(['name' => 'Cart Category ' . uniqid(), 'status' => 1, 'created_by' => 1]);

        return Product::create([
            'code' => 'CART-' . uniqid(),
            'name' => $name,
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'price' => $price,
            'discount_percentage' => $discountPercentage,
            'discount_from_date' => now()->toDateString(),
            'discount_to_date' => now()->addDay()->toDateString(),
            'media_id' => 1,
            'inventory_qty' => $inventoryQty,
            'description' => 'Cart product description',
            'star' => 4.5,
            'featured' => 0,
            'created_by' => 1,
            'status' => 1,
        ]);
    }
}
