<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CustomerRedisCacheTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'cache.default' => 'array',
            'scout.driver' => 'database',
        ]);
        Cache::flush();
    }

    public function test_customer_homepage_payload_is_cached(): void
    {
        $brand = Brand::create(['name' => 'Redis Home Brand', 'status' => 1, 'created_by' => 1]);
        $category = Category::create(['name' => 'Redis Home Category', 'status' => 1, 'created_by' => 1]);
        $product = Product::create($this->productData([
            'name' => 'Redis Cached Serum',
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'featured' => 1,
        ]));

        $this->get('/')
            ->assertOk()
            ->assertSee('Redis Cached Serum');

        Product::query()->whereKey($product->id)->update(['name' => 'Redis Changed Serum']);

        $this->get('/')
            ->assertOk()
            ->assertSee('Redis Cached Serum')
            ->assertDontSee('Redis Changed Serum');
    }

    public function test_product_listing_payload_is_cached_by_filters(): void
    {
        $brand = Brand::create(['name' => 'Redis Listing Brand', 'status' => 1, 'created_by' => 1]);
        $category = Category::create(['name' => 'Redis Listing Category', 'status' => 1, 'created_by' => 1]);
        $product = Product::create($this->productData([
            'name' => 'Redis Listing Serum',
            'brand_id' => $brand->id,
            'category_id' => $category->id,
        ]));

        $this->get('/products?q=Redis&sort=newest')
            ->assertOk()
            ->assertSee('Redis Listing Serum');

        Product::query()->whereKey($product->id)->update(['name' => 'Redis Listing Changed']);

        $this->get('/products?q=Redis&sort=newest')
            ->assertOk()
            ->assertSee('Redis Listing Serum')
            ->assertDontSee('Redis Listing Changed');
    }

    public function test_category_listing_cache_key_is_scoped_per_category(): void
    {
        $brand = Brand::create(['name' => 'Redis Category Brand', 'status' => 1, 'created_by' => 1]);
        $firstCategory = Category::create(['name' => 'Redis First Category', 'status' => 1, 'created_by' => 1]);
        $secondCategory = Category::create(['name' => 'Redis Second Category', 'status' => 1, 'created_by' => 1]);
        Product::create($this->productData([
            'name' => 'Redis First Category Serum',
            'brand_id' => $brand->id,
            'category_id' => $firstCategory->id,
        ]));
        Product::create($this->productData([
            'name' => 'Redis Second Category Toner',
            'brand_id' => $brand->id,
            'category_id' => $secondCategory->id,
        ]));

        $this->get('/categories/' . $firstCategory->id)
            ->assertOk()
            ->assertSee('Redis First Category Serum')
            ->assertDontSee('Redis Second Category Toner');

        $this->get('/categories/' . $secondCategory->id)
            ->assertOk()
            ->assertSee('Redis Second Category Toner')
            ->assertDontSee('Redis First Category Serum');
    }

    public function test_product_detail_catalog_payload_is_cached(): void
    {
        $brand = Brand::create(['name' => 'Redis Detail Brand', 'status' => 1, 'created_by' => 1]);
        $category = Category::create(['name' => 'Redis Detail Category', 'status' => 1, 'created_by' => 1]);
        $product = Product::create($this->productData([
            'name' => 'Redis Detail Serum',
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'price' => 240000,
            'discount_percentage' => 10,
        ]));

        $this->get('/products/' . $product->id)
            ->assertOk()
            ->assertSee('Redis Detail Serum')
            ->assertSee('&quot;sale_price&quot;:216000', false);

        Product::query()->whereKey($product->id)->update([
            'name' => 'Redis Detail Changed',
            'price' => 500000,
        ]);

        $this->get('/products/' . $product->id)
            ->assertOk()
            ->assertSee('Redis Detail Serum')
            ->assertSee('&quot;sale_price&quot;:216000', false)
            ->assertDontSee('Redis Detail Changed')
            ->assertDontSee('&quot;sale_price&quot;:450000', false);
    }

    private function productData(array $overrides = []): array
    {
        return array_merge([
            'code' => 'REDIS-' . uniqid(),
            'name' => 'Default Redis Product',
            'brand_id' => 1,
            'category_id' => 1,
            'price' => 100000,
            'discount_percentage' => 0,
            'discount_from_date' => now()->toDateString(),
            'discount_to_date' => now()->addDay()->toDateString(),
            'media_id' => 1,
            'inventory_qty' => 10,
            'description' => 'Redis cached product description',
            'star' => 4.5,
            'featured' => 0,
            'created_by' => 1,
            'status' => 1,
        ], $overrides);
    }
}
