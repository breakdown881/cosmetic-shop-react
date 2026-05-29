<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CustomerProductDetailTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        config(['scout.driver' => 'database']);
    }

    public function test_product_detail_delegates_database_work_to_service_and_repository(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/Customer/ProductController.php'));
        $service = file_get_contents(app_path('Services/Customer/CustomerProductDetailService.php'));
        $repository = file_get_contents(app_path('Repositories/Customer/CustomerProductDetailRepository.php'));

        $this->assertStringContainsString('CustomerProductDetailService', $controller);
        $this->assertStringNotContainsString('use App\\Models\\', $controller);
        $this->assertStringNotContainsString('Product::', $controller);

        $this->assertStringContainsString('CustomerProductDetailRepository', $service);
        $this->assertStringNotContainsString('Product::', $service);
        $this->assertStringNotContainsString('DB::', $service);
        $this->assertStringNotContainsString('Storage::', $service);

        $this->assertStringContainsString('Product::query()', $repository);
    }

    public function test_product_detail_page_renders_product_and_related_products(): void
    {
        $brand = Brand::create(['name' => 'Detail Brand', 'status' => 1, 'created_by' => 1]);
        $category = Category::create(['name' => 'Detail Category', 'status' => 1, 'created_by' => 1]);
        $product = Product::create($this->productData([
            'name' => 'Detail Serum',
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'price' => 500000,
            'discount_percentage' => 20,
            'inventory_qty' => 8,
            'description' => 'A brightening serum for daily skincare.',
            'star' => 4.7,
        ]));
        Product::create($this->productData([
            'name' => 'Related Toner',
            'brand_id' => $brand->id,
            'category_id' => $category->id,
        ]));

        $this->get("/products/{$product->id}")
            ->assertOk()
            ->assertSee('data-react-component="CustomerProductShow"', false)
            ->assertSee('Detail Serum')
            ->assertSee('Detail Brand')
            ->assertSee('Detail Category')
            ->assertSee('A brightening serum for daily skincare.')
            ->assertSee('Related Toner')
            ->assertSee('&quot;sale_price&quot;:400000', false);
    }

    public function test_inactive_product_detail_returns_not_found(): void
    {
        $brand = Brand::create(['name' => 'Hidden Detail Brand', 'status' => 1, 'created_by' => 1]);
        $category = Category::create(['name' => 'Hidden Detail Category', 'status' => 1, 'created_by' => 1]);
        $product = Product::create($this->productData([
            'name' => 'Hidden Detail Serum',
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'status' => 0,
        ]));

        $this->get("/products/{$product->id}")->assertNotFound();
    }

    private function productData(array $overrides = []): array
    {
        return array_merge([
            'code' => 'DET-' . uniqid(),
            'name' => 'Default Detail Product',
            'brand_id' => 1,
            'category_id' => 1,
            'price' => 100000,
            'discount_percentage' => 0,
            'discount_from_date' => now()->toDateString(),
            'discount_to_date' => now()->addDay()->toDateString(),
            'media_id' => 1,
            'inventory_qty' => 10,
            'description' => 'Detail product description',
            'star' => 4.5,
            'featured' => 0,
            'created_by' => 1,
            'status' => 1,
        ], $overrides);
    }
}
