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

class AdminProductCommentApiTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        config(['scout.driver' => 'database']);
    }

    public function test_manager_and_admin_can_view_product_comments_and_update_active_status(): void
    {
        foreach (['MANAGER', 'ADMIN'] as $role) {
            Sanctum::actingAs($this->admin($role));
            $product = $this->product($role . ' Comment Product');
            $otherProduct = $this->product($role . ' Other Product');
            $comment = Comment::create($this->commentData($product, [
                'email' => strtolower($role) . '-customer@example.test',
                'active' => 0,
            ]));
            Comment::create($this->commentData($otherProduct, [
                'email' => strtolower($role) . '-other@example.test',
                'active' => 1,
            ]));

            $this->getJson("/api/products/{$product->id}/comments")
                ->assertOk()
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.id', $comment->id)
                ->assertJsonPath('data.0.product_id', $product->id)
                ->assertJsonPath('data.0.product_name', $product->name);

            $this->patchJson("/api/products/{$product->id}/comments/{$comment->id}", [
                'active' => 1,
            ])->assertOk()
                ->assertJsonPath('data.active', 1);

            $this->assertDatabaseHas('comments', [
                'id' => $comment->id,
                'active' => 1,
            ]);
        }
    }

    public function test_staff_cannot_view_or_update_product_comments(): void
    {
        Sanctum::actingAs($this->admin('STAFF'));
        $product = $this->product('Staff Comment Product');
        $comment = Comment::create($this->commentData($product));

        $this->getJson("/api/products/{$product->id}/comments")
            ->assertForbidden();

        $this->patchJson("/api/products/{$product->id}/comments/{$comment->id}", [
            'active' => 1,
        ])->assertForbidden();
    }

    public function test_admin_comment_page_is_product_scoped(): void
    {
        $product = $this->product('Scoped Product');
        $comment = Comment::create($this->commentData($product));

        $this->actingAs($this->admin('MANAGER'), 'admin')
            ->get("/admin/products/{$product->id}/comments")
            ->assertOk()
            ->assertSee('data-react-component="AdminSpaApp"', false)
            ->assertDontSee($product->name)
            ->assertDontSee('"canCreate":false', false);

        $this->actingAs($this->admin('MANAGER'), 'admin')
            ->getJson("/admin/api/products/{$product->id}/comments")
            ->assertOk()
            ->assertJsonPath('data.0.product_name', $product->name);

        $this->actingAs($this->admin('MANAGER'), 'admin')
            ->patchJson("/admin/api/products/{$product->id}/comments/{$comment->id}", [
                'active' => 1,
            ])->assertOk()
            ->assertJsonPath('data.active', 1);
    }

    public function test_comment_must_belong_to_the_product_when_updating_active(): void
    {
        Sanctum::actingAs($this->admin('MANAGER'));
        $product = $this->product('Owner Product');
        $otherProduct = $this->product('Other Owner Product');
        $comment = Comment::create($this->commentData($otherProduct));

        $this->patchJson("/api/products/{$product->id}/comments/{$comment->id}", [
            'active' => 1,
        ])->assertNotFound();
    }

    private function admin(string $role): Admin
    {
        return Admin::create([
            'name' => $role . ' Comment Admin',
            'email' => 'comment-admin-' . strtolower($role) . uniqid('', true) . '@example.test',
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
