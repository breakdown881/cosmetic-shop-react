<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CustomerReviewTest extends TestCase
{
    use DatabaseTransactions;

    public function test_review_flow_uses_controller_service_repository_pattern(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/Customer/ReviewController.php'));
        $service = file_get_contents(app_path('Services/Customer/CustomerReviewService.php'));
        $repository = file_get_contents(app_path('Repositories/Customer/CustomerReviewRepository.php'));

        $this->assertStringContainsString('CustomerReviewService', $controller);
        $this->assertStringNotContainsString('use App\\Models\\', $controller);
        $this->assertStringNotContainsString('Comment::', $controller);

        $this->assertStringContainsString('CustomerReviewRepository', $service);
        $this->assertStringNotContainsString('Comment::', $service);

        $this->assertStringContainsString('Comment::query()', $repository);
        $this->assertStringContainsString('userPurchasedProduct', $repository);
    }

    public function test_guest_must_login_before_reviewing_product(): void
    {
        $product = $this->product();

        $this->post("/products/{$product->id}/reviews", [
            'star' => 5,
            'description' => 'Great serum.',
        ])->assertRedirect('/login');
    }

    public function test_customer_must_have_completed_order_before_reviewing_product(): void
    {
        $user = User::factory()->create();
        $product = $this->product();

        $this->actingAs($user)
            ->post("/products/{$product->id}/reviews", [
                'star' => 5,
                'description' => 'Great serum.',
            ])
            ->assertForbidden();
    }

    public function test_customer_can_review_purchased_product_and_review_waits_for_approval(): void
    {
        $user = User::factory()->create([
            'name' => 'Review Customer',
            'email' => 'review-customer@example.test',
        ]);
        $product = $this->product();
        $this->completedOrderFor($user, $product);

        $this->actingAs($user)
            ->post("/products/{$product->id}/reviews", [
                'star' => 4,
                'description' => 'Hydrating and gentle for my skin.',
            ])
            ->assertRedirect("/products/{$product->id}");

        $this->assertDatabaseHas('comments', [
            'product_id' => $product->id,
            'customer_id' => $user->id,
            'email' => 'review-customer@example.test',
            'fullname' => 'Review Customer',
            'star' => 4,
            'description' => 'Hydrating and gentle for my skin.',
            'active' => 0,
        ]);
    }

    public function test_customer_cannot_review_same_product_twice(): void
    {
        $user = User::factory()->create();
        $product = $this->product();
        $this->completedOrderFor($user, $product);

        Comment::query()->create([
            'product_id' => $product->id,
            'customer_id' => $user->id,
            'email' => $user->email,
            'fullname' => $user->name,
            'star' => 5,
            'description' => 'Already reviewed.',
            'active' => 1,
        ]);

        $this->actingAs($user)
            ->post("/products/{$product->id}/reviews", [
                'star' => 3,
                'description' => 'Trying again.',
            ])
            ->assertUnprocessable();
    }

    public function test_product_detail_shows_only_approved_reviews(): void
    {
        $product = $this->product();

        Comment::query()->create([
            'product_id' => $product->id,
            'email' => 'approved@example.test',
            'fullname' => 'Approved Reviewer',
            'star' => 5,
            'description' => 'Visible approved review.',
            'active' => 1,
        ]);
        Comment::query()->create([
            'product_id' => $product->id,
            'email' => 'pending@example.test',
            'fullname' => 'Pending Reviewer',
            'star' => 3,
            'description' => 'Hidden pending review.',
            'active' => 0,
        ]);

        $this->get("/products/{$product->id}")
            ->assertOk()
            ->assertSee('Visible approved review.')
            ->assertDontSee('Hidden pending review.');
    }

    private function product(array $overrides = []): Product
    {
        return Product::withoutSyncingToSearch(fn () => Product::query()->create(array_merge([
            'code' => 'REV-' . uniqid(),
            'name' => 'Review Serum',
            'brand_id' => 1,
            'category_id' => 1,
            'price' => 300000,
            'discount_percentage' => 0,
            'discount_from_date' => now()->subDay()->toDateString(),
            'discount_to_date' => now()->addDay()->toDateString(),
            'media_id' => 0,
            'inventory_qty' => 10,
            'description' => 'Reviewable product.',
            'star' => 0,
            'featured' => 0,
            'created_by' => 1,
            'status' => 1,
        ], $overrides)));
    }

    private function completedOrderFor(User $user, Product $product): void
    {
        $order = Order::query()->create([
            'staff_id' => 1,
            'customer_id' => $user->id,
            'shipping_fullname' => $user->name,
            'shipping_mobile' => '0900000000',
            'payment_method' => 0,
            'shipping_ward_id' => '1',
            'shipping_housenumber_street' => '1 Beauty Street',
            'shipping_fee' => 0,
            'delivered_date' => now()->toDateString(),
            'price_total' => 300000,
            'discount_code' => '',
            'discount_amount' => 0,
            'sub_total' => 300000,
            'tax' => 0,
            'price_inc_tax_total' => 300000,
            'voucher_code' => '',
            'voucher_amount' => 0,
            'payment_total' => 300000,
            'status' => 'COMPLETE',
        ]);

        OrderItem::query()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'qty' => 1,
            'unit_price' => 300000,
            'total_price' => 300000,
        ]);
    }
}
