<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CustomerProductListingTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        config(['scout.driver' => 'database']);
    }

    public function test_customer_routes_use_customer_react_shell_not_admin_shell(): void
    {
        foreach (['/products', '/cart', '/checkout', '/orders', '/account'] as $path) {
            $this->get($path)
                ->assertOk()
                ->assertSee('class="customer-site antialiased"', false)
                ->assertDontSee('react-admin-shell', false)
                ->assertDontSee('AdminSpaApp', false);
        }
    }

    public function test_product_listing_delegates_database_work_to_service_and_repository(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/Customer/ProductController.php'));
        $service = file_get_contents(app_path('Services/Customer/CustomerProductService.php'));
        $repository = file_get_contents(app_path('Repositories/Customer/CustomerProductRepository.php'));

        $this->assertStringContainsString('CustomerProductService', $controller);
        $this->assertStringNotContainsString('use App\\Models\\', $controller);
        $this->assertStringNotContainsString('Product::', $controller);
        $this->assertStringNotContainsString('Category::', $controller);
        $this->assertStringNotContainsString('Brand::', $controller);

        $this->assertStringContainsString('CustomerProductRepository', $service);
        $this->assertStringNotContainsString('Product::', $service);
        $this->assertStringNotContainsString('Category::', $service);
        $this->assertStringNotContainsString('Brand::', $service);
        $this->assertStringNotContainsString('DB::', $service);
        $this->assertStringNotContainsString('Storage::', $service);

        $this->assertStringContainsString('Product::query()', $repository);
        $this->assertStringContainsString('Category::query()', $repository);
        $this->assertStringContainsString('Brand::query()', $repository);
    }

    public function test_products_page_renders_filtered_sorted_products_with_filter_options(): void
    {
        $targetBrand = Brand::create(['name' => 'Acme Beauty', 'status' => 1, 'created_by' => 1]);
        $otherBrand = Brand::create(['name' => 'Other Beauty', 'status' => 1, 'created_by' => 1]);
        $targetCategory = Category::create(['name' => 'Skin Care', 'status' => 1, 'created_by' => 1]);
        $otherCategory = Category::create(['name' => 'Makeup', 'status' => 1, 'created_by' => 1]);

        Product::create($this->productData([
            'name' => 'Ruby Serum',
            'brand_id' => $targetBrand->id,
            'category_id' => $targetCategory->id,
            'price' => 300000,
            'discount_percentage' => 10,
            'featured' => 1,
        ]));
        Product::create($this->productData([
            'name' => 'Ruby Toner',
            'brand_id' => $otherBrand->id,
            'category_id' => $otherCategory->id,
            'price' => 120000,
        ]));
        Product::create($this->productData([
            'name' => 'Hidden Ruby Cream',
            'brand_id' => $targetBrand->id,
            'category_id' => $targetCategory->id,
            'status' => 0,
        ]));

        $this->get("/products?q=Ruby&brand_id={$targetBrand->id}&category_id={$targetCategory->id}&featured=1&sort=price_desc")
            ->assertOk()
            ->assertSee('data-react-component="CustomerProductIndex"', false)
            ->assertSee('Ruby Serum')
            ->assertSee('Acme Beauty')
            ->assertSee('Skin Care')
            ->assertSee('&quot;sale_price&quot;:270000', false)
            ->assertDontSee('Ruby Toner')
            ->assertDontSee('Hidden Ruby Cream');
    }

    public function test_category_and_brand_routes_preselect_listing_filters(): void
    {
        $brand = Brand::create(['name' => 'Route Brand', 'status' => 1, 'created_by' => 1]);
        $category = Category::create(['name' => 'Route Category', 'status' => 1, 'created_by' => 1]);
        Product::create($this->productData([
            'name' => 'Route Serum',
            'brand_id' => $brand->id,
            'category_id' => $category->id,
        ]));

        $this->get("/categories/{$category->id}")
            ->assertOk()
            ->assertSee('data-react-component="CustomerProductIndex"', false)
            ->assertSee('Route Category')
            ->assertSee('&quot;category_id&quot;:' . $category->id, false);

        $this->get("/brands/{$brand->id}")
            ->assertOk()
            ->assertSee('data-react-component="CustomerProductIndex"', false)
            ->assertSee('Route Brand')
            ->assertSee('&quot;brand_id&quot;:' . $brand->id, false);
    }

    private function productData(array $overrides = []): array
    {
        return array_merge([
            'code' => 'CUS-' . uniqid(),
            'name' => 'Default Customer Product',
            'brand_id' => 1,
            'category_id' => 1,
            'price' => 100000,
            'discount_percentage' => 0,
            'discount_from_date' => now()->toDateString(),
            'discount_to_date' => now()->addDay()->toDateString(),
            'media_id' => 1,
            'inventory_qty' => 10,
            'description' => 'Customer product description',
            'star' => 4.5,
            'featured' => 0,
            'created_by' => 1,
            'status' => 1,
        ], $overrides);
    }
}
