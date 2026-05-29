<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CustomerWishlistTest extends TestCase
{
    use DatabaseTransactions;

    public function test_wishlist_uses_controller_service_repository_pattern(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/Customer/WishlistController.php'));
        $service = file_get_contents(app_path('Services/Customer/CustomerWishlistService.php'));
        $repository = file_get_contents(app_path('Repositories/Customer/CustomerWishlistRepository.php'));

        $this->assertStringContainsString('CustomerWishlistService', $controller);
        $this->assertStringNotContainsString('use App\\Models\\', $controller);
        $this->assertStringNotContainsString('DB::', $controller);

        $this->assertStringContainsString('CustomerWishlistRepository', $service);
        $this->assertStringNotContainsString('DB::table', $service);

        $this->assertStringContainsString('DB::table', $repository);
    }

    public function test_guest_must_login_to_open_or_mutate_wishlist(): void
    {
        $product = $this->product();

        $this->get('/wishlist')->assertRedirect('/login');
        $this->post('/wishlist/items', ['product_id' => $product->id])->assertRedirect('/login');
        $this->delete("/wishlist/items/{$product->id}")->assertRedirect('/login');
    }

    public function test_customer_can_add_remove_and_view_wishlist_items(): void
    {
        $user = User::factory()->create();
        $product = $this->product(['name' => 'Wishlist Serum']);
        $otherProduct = $this->product(['name' => 'Other Wishlist Serum']);
        $otherUser = User::factory()->create();

        $this->actingAs($otherUser)->post('/wishlist/items', ['product_id' => $otherProduct->id]);

        $this->actingAs($user)
            ->post('/wishlist/items', ['product_id' => $product->id])
            ->assertRedirect('/wishlist');

        $this->actingAs($user)
            ->post('/wishlist/items', ['product_id' => $product->id])
            ->assertRedirect('/wishlist');

        $this->assertDatabaseCount('customer_wishlist', 2);
        $this->assertDatabaseHas('customer_wishlist', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $this->actingAs($user)
            ->get('/wishlist')
            ->assertOk()
            ->assertSee('data-react-component="CustomerWishlistPage"', false)
            ->assertSee('Wishlist Serum')
            ->assertDontSee('Other Wishlist Serum');

        $this->actingAs($user)
            ->delete("/wishlist/items/{$product->id}")
            ->assertRedirect('/wishlist');

        $this->assertDatabaseMissing('customer_wishlist', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);
    }

    private function product(array $overrides = []): Product
    {
        return Product::withoutSyncingToSearch(fn () => Product::query()->create(array_merge([
            'code' => 'WISH-' . uniqid(),
            'name' => 'Wishlist Product',
            'brand_id' => 1,
            'category_id' => 1,
            'price' => 250000,
            'discount_percentage' => 0,
            'discount_from_date' => now()->subDay()->toDateString(),
            'discount_to_date' => now()->addDay()->toDateString(),
            'media_id' => 0,
            'inventory_qty' => 10,
            'description' => 'Wishlist product.',
            'star' => 0,
            'featured' => 0,
            'created_by' => 1,
            'status' => 1,
        ], $overrides)));
    }
}
