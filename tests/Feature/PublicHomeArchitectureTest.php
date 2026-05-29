<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PublicHomeArchitectureTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        config(['scout.driver' => 'database']);
    }

    public function test_public_home_shell_delegates_payload_building_to_service(): void
    {
        $contents = file_get_contents(app_path('Support/PublicReactShell.php'));

        $this->assertStringContainsString('CustomerHomeService', $contents);
        $this->assertStringNotContainsString('use App\\Models\\', $contents);
        $this->assertStringNotContainsString('use Illuminate\\Support\\Facades\\DB', $contents);
        $this->assertStringNotContainsString('use Illuminate\\Support\\Facades\\Storage', $contents);
        $this->assertStringNotContainsString('private static function homeProps', $contents);
    }

    public function test_public_home_service_uses_repository_for_database_access(): void
    {
        $service = file_get_contents(app_path('Services/Customer/CustomerHomeService.php'));
        $repository = file_get_contents(app_path('Repositories/Customer/CustomerHomeRepository.php'));

        $this->assertStringContainsString('CustomerHomeRepository', $service);
        $this->assertStringNotContainsString('Category::', $service);
        $this->assertStringNotContainsString('Product::', $service);
        $this->assertStringNotContainsString('DB::', $service);
        $this->assertStringNotContainsString('Storage::', $service);

        $this->assertStringContainsString('Category::query()', $repository);
        $this->assertStringContainsString('Product::query()', $repository);
    }

    public function test_customer_homepage_renders_payload_from_service_repository(): void
    {
        $brand = Brand::create(['name' => 'Customer Home Brand', 'status' => 1, 'created_by' => 1]);
        $category = Category::create(['name' => 'Customer Home Category', 'status' => 1, 'created_by' => 1]);

        $product = Product::create([
            'code' => 'HOME-' . uniqid(),
            'name' => 'Customer Home Serum',
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'price' => 200000,
            'discount_percentage' => 10,
            'discount_from_date' => now()->toDateString(),
            'discount_to_date' => now()->addDay()->toDateString(),
            'media_id' => 1,
            'inventory_qty' => 12,
            'description' => 'Homepage product',
            'star' => 4.8,
            'featured' => 1,
            'created_by' => 1,
            'status' => 1,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('data-react-component="Home"', false)
            ->assertSee('Customer Home Category')
            ->assertSee('Customer Home Serum')
            ->assertSee('&quot;sale_price&quot;:180000', false)
            ->assertSee('&quot;url&quot;:&quot;\/products\/' . $product->id . '&quot;', false);
    }
}
