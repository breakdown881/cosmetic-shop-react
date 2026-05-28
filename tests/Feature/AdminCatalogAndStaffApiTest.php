<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminCatalogAndStaffApiTest extends TestCase
{
    use DatabaseTransactions;

    public function test_role_matrix_protects_catalog_and_staff_apis(): void
    {
        Sanctum::actingAs($this->admin('STAFF'));
        $this->getJson('/api/products')->assertForbidden();

        Sanctum::actingAs($this->admin('ADMIN'));
        $this->getJson('/api/staffs')->assertForbidden();
        $this->getJson('/api/products')->assertOk();

        Sanctum::actingAs($this->admin('MANAGER'));
        $this->getJson('/api/staffs')->assertOk();
    }

    public function test_web_admin_routes_follow_the_same_role_matrix(): void
    {
        $this->actingAs($this->admin('STAFF'), 'admin')
            ->get('/admin/products')
            ->assertForbidden();

        $this->actingAs($this->admin('ADMIN'), 'admin')
            ->get('/admin/staffs')
            ->assertForbidden();
    }

    public function test_catalog_admin_pages_are_react_shells_backed_by_admin_api(): void
    {
        config(['scout.driver' => 'database']);
        $admin = $this->admin('MANAGER');
        $brand = Brand::create(['name' => 'Shell Brand', 'status' => 1, 'created_by' => $admin->id]);
        $category = Category::create(['name' => 'Shell Category', 'status' => 1, 'created_by' => $admin->id]);
        $product = Product::create($this->productData([
            'name' => 'Shell Product',
            'brand_id' => $brand->id,
            'category_id' => $category->id,
        ]));

        foreach (['brands', 'categories', 'products'] as $resource) {
            $this->actingAs($admin, 'admin')
                ->get("/admin/{$resource}")
                ->assertOk()
                ->assertSee("admin\/api\/{$resource}", false)
                ->assertSee('AdminApiResourceManager');
        }

        $this->actingAs($admin, 'admin')
            ->postJson('/admin/api/brands', [
                'name' => 'React API Brand',
                'status' => 1,
            ])->assertCreated()
            ->assertJsonPath('data.name', 'React API Brand');

        $this->actingAs($admin, 'admin')
            ->postJson('/admin/api/categories', [
                'name' => 'React API Category',
                'status' => 1,
            ])->assertCreated()
            ->assertJsonPath('data.name', 'React API Category');

        $this->actingAs($admin, 'admin')
            ->postJson('/admin/api/products', $this->productData([
                'code' => 'REACT-API-001',
                'name' => 'React API Product',
                'brand_id' => $brand->id,
                'category_id' => $category->id,
            ]))->assertCreated()
            ->assertJsonPath('data.name', 'React API Product');

        $this->actingAs($admin, 'admin')->post('/admin/brands/store', [])->assertNotFound();
        $this->actingAs($admin, 'admin')->delete("/admin/brands/delete/{$brand->id}")->assertNotFound();
        $this->actingAs($admin, 'admin')->post("/admin/brands/changeStatus/{$brand->id}", ['status' => 0])->assertNotFound();

        $this->actingAs($admin, 'admin')->post('/admin/categories/store', [])->assertNotFound();
        $this->actingAs($admin, 'admin')->delete("/admin/categories/delete/{$category->id}")->assertNotFound();
        $this->actingAs($admin, 'admin')->post("/admin/categories/changeStatus/{$category->id}", ['status' => 0])->assertNotFound();

        $this->actingAs($admin, 'admin')->post('/admin/products/store', [])->assertNotFound();
        $this->actingAs($admin, 'admin')->delete("/admin/products/delete/{$product->id}")->assertNotFound();
        $this->actingAs($admin, 'admin')->post("/admin/products/changeStatus/{$product->id}", ['status' => 0])->assertNotFound();
    }

    public function test_products_can_be_searched_by_name_brand_and_category(): void
    {
        config(['scout.driver' => 'database']);
        Sanctum::actingAs($this->admin('MANAGER'));

        $targetBrand = Brand::create(['name' => 'Acme Beauty', 'status' => 1, 'created_by' => 1]);
        $otherBrand = Brand::create(['name' => 'Other Beauty', 'status' => 1, 'created_by' => 1]);
        $targetCategory = Category::create(['name' => 'Lip Care', 'parent_id' => 0, 'status' => 1, 'created_by' => 1]);
        $otherCategory = Category::create(['name' => 'Skin Care', 'parent_id' => 0, 'status' => 1, 'created_by' => 1]);

        Product::create($this->productData([
            'code' => 'LIP-001',
            'name' => 'Ruby Lipstick',
            'brand_id' => $targetBrand->id,
            'category_id' => $targetCategory->id,
        ]));

        Product::create($this->productData([
            'code' => 'SKIN-001',
            'name' => 'Ruby Toner',
            'brand_id' => $otherBrand->id,
            'category_id' => $otherCategory->id,
        ]));

        $this->getJson("/api/products/search?q=Ruby&brand_id={$targetBrand->id}&category_id={$targetCategory->id}")
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Ruby Lipstick')
            ->assertJsonPath('data.0.brand.id', $targetBrand->id)
            ->assertJsonPath('data.0.category.id', $targetCategory->id);
    }

    public function test_brands_can_be_searched_by_name(): void
    {
        config(['scout.driver' => 'database']);
        Sanctum::actingAs($this->admin('MANAGER'));

        Brand::create(['name' => 'Acme Beauty', 'status' => 1, 'created_by' => 1]);
        Brand::create(['name' => 'Other Beauty', 'status' => 1, 'created_by' => 1]);

        $this->getJson('/api/brands?q=Acme')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Acme Beauty');
    }

    public function test_root_categories_can_be_created_and_searched_by_name(): void
    {
        config(['scout.driver' => 'database']);
        Sanctum::actingAs($this->admin('MANAGER'));

        $this->postJson('/api/categories', [
            'name' => 'Root Skin Care',
            'status' => 1,
        ])->assertCreated()
            ->assertJsonPath('data.name', 'Root Skin Care')
            ->assertJsonPath('data.parent_id', null);

        $this->getJson('/api/categories?q=Root Skin')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Root Skin Care');
    }

    private function admin(string $role): Admin
    {
        return Admin::create([
            'name' => $role . ' User',
            'email' => strtolower($role) . uniqid('', true) . '@example.test',
            'password' => 'password',
            'role' => $role,
            'is_active' => true,
        ]);
    }

    private function productData(array $overrides = []): array
    {
        return array_merge([
            'code' => 'PRD-' . uniqid(),
            'name' => 'Default Product',
            'brand_id' => 1,
            'category_id' => 1,
            'price' => 100000,
            'discount_percentage' => 0,
            'discount_from_date' => now()->toDateString(),
            'discount_to_date' => now()->addDay()->toDateString(),
            'media_id' => 1,
            'inventory_qty' => 10,
            'description' => 'Default product description',
            'star' => 4.5,
            'featured' => 0,
            'created_by' => 1,
            'status' => 1,
        ], $overrides);
    }
}
