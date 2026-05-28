<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Product;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminCommentManagementTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        config(['scout.driver' => 'database']);
    }

    public function test_manager_can_open_all_comments_page_and_update_comment_status(): void
    {
        $admin = $this->admin('MANAGER');
        $product = $this->product('All Comments Serum');
        $comment = Comment::create($this->commentData($product, [
            'fullname' => 'All Comments Customer',
            'active' => 0,
        ]));

        $this->actingAs($admin, 'admin')
            ->get('/admin/comments')
            ->assertOk()
            ->assertSee('data-react-component="AdminSpaApp"', false)
            ->assertDontSee('admin\/api\/comments', false)
            ->assertDontSee('"canCreate":false', false)
            ->assertDontSee('"canEdit":true', false)
            ->assertDontSee('"canDelete":false', false);

        $this->actingAs($admin, 'admin')
            ->getJson('/admin/api/comments')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.fullname', 'All Comments Customer')
            ->assertJsonPath('data.0.product_name', 'All Comments Serum');

        $this->actingAs($admin, 'admin')
            ->patchJson("/admin/api/comments/{$comment->id}", ['active' => 1])
            ->assertOk()
            ->assertJsonPath('data.active', 1);
    }

    public function test_staff_cannot_manage_all_comments(): void
    {
        Sanctum::actingAs($this->admin('STAFF'));

        $this->getJson('/api/comments')->assertForbidden();
    }

    private function admin(string $role): Admin
    {
        return Admin::create([
            'name' => $role . ' Comment Manager',
            'email' => 'all-comment-admin-' . strtolower($role) . uniqid('', true) . '@example.test',
            'password' => 'password',
            'role' => $role,
            'is_active' => true,
        ]);
    }

    private function product(string $name): Product
    {
        $brand = Brand::create([
            'name' => 'Brand ' . uniqid(),
            'status' => 1,
            'created_by' => 1,
        ]);
        $category = Category::create([
            'name' => 'Category ' . uniqid(),
            'parent_id' => 0,
            'status' => 1,
            'created_by' => 1,
        ]);

        return Product::create([
            'code' => 'PRD-' . uniqid(),
            'name' => $name,
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'price' => 100000,
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

    private function commentData(Product $product, array $overrides = []): array
    {
        return array_merge([
            'product_id' => $product->id,
            'email' => 'customer-' . uniqid() . '@example.test',
            'fullname' => 'Customer Name',
            'star' => 5,
            'description' => 'Customer comment',
            'active' => 0,
        ], $overrides);
    }
}
