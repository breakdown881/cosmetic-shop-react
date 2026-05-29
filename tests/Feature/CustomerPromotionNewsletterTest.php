<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Discount;
use App\Models\Product;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CustomerPromotionNewsletterTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        config(['scout.driver' => 'database']);
    }

    public function test_promotion_and_newsletter_use_service_repository_pattern(): void
    {
        $promotionController = file_get_contents(app_path('Http/Controllers/Customer/PromotionController.php'));
        $promotionService = file_get_contents(app_path('Services/Customer/CustomerPromotionService.php'));
        $promotionRepository = file_get_contents(app_path('Repositories/Customer/CustomerPromotionRepository.php'));
        $newsletterController = file_get_contents(app_path('Http/Controllers/Customer/NewsletterController.php'));
        $newsletterService = file_get_contents(app_path('Services/Customer/CustomerNewsletterService.php'));
        $newsletterRepository = file_get_contents(app_path('Repositories/Customer/CustomerNewsletterRepository.php'));

        $this->assertStringContainsString('CustomerPromotionService', $promotionController);
        $this->assertStringNotContainsString('use App\\Models\\', $promotionController);
        $this->assertStringContainsString('CustomerPromotionRepository', $promotionService);
        $this->assertStringContainsString('Discount::query()', $promotionRepository);

        $this->assertStringContainsString('CustomerNewsletterService', $newsletterController);
        $this->assertStringNotContainsString('use App\\Models\\', $newsletterController);
        $this->assertStringContainsString('CustomerNewsletterRepository', $newsletterService);
        $this->assertStringContainsString('NewsLetter::query()', $newsletterRepository);
    }

    public function test_promotions_page_shows_only_active_vouchers(): void
    {
        $active = $this->discount('ACTIVE50', 1, 50000);
        $expired = $this->discount('EXPIRED10', 0, 10, now()->subDays(5), now()->subDay());

        $this->get('/promotions')
            ->assertOk()
            ->assertSee('data-react-component="CustomerPromotionPage"', false)
            ->assertSee($active->code)
            ->assertSee('50.000', false)
            ->assertDontSee($expired->code);
    }

    public function test_customer_can_validate_fixed_and_percentage_vouchers_from_cart(): void
    {
        $product = $this->product(300000);
        $fixed = $this->discount('SAVE50', 1, 50000);
        $percent = $this->discount('SAVE10', 0, 10);

        $this->withSession(['customer_cart' => [$product->id => 1]])
            ->postJson('/cart/vouchers/validate', ['discount_code' => $fixed->code])
            ->assertOk()
            ->assertJsonPath('data.code', 'SAVE50')
            ->assertJsonPath('data.discount_amount', 50000)
            ->assertJsonPath('data.payment_total', 250000);

        $this->withSession(['customer_cart' => [$product->id => 1]])
            ->postJson('/cart/vouchers/validate', ['discount_code' => $percent->code])
            ->assertOk()
            ->assertJsonPath('data.code', 'SAVE10')
            ->assertJsonPath('data.discount_amount', 30000)
            ->assertJsonPath('data.payment_total', 270000);
    }

    public function test_voucher_validation_rejects_expired_codes_and_never_makes_negative_total(): void
    {
        $product = $this->product(300000);
        $expired = $this->discount('OLD10', 0, 10, now()->subDays(5), now()->subDay());
        $largeFixed = $this->discount('FREEBIG', 1, 999999);

        $this->withSession(['customer_cart' => [$product->id => 1]])
            ->postJson('/cart/vouchers/validate', ['discount_code' => $expired->code])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['discount_code']);

        $this->withSession(['customer_cart' => [$product->id => 1]])
            ->postJson('/cart/vouchers/validate', ['discount_code' => $largeFixed->code])
            ->assertOk()
            ->assertJsonPath('data.discount_amount', 300000)
            ->assertJsonPath('data.payment_total', 0);
    }

    public function test_customer_can_subscribe_newsletter_once(): void
    {
        $payload = ['email' => 'beauty-fan@example.test'];

        $this->postJson('/newsletter/subscribe', $payload)
            ->assertCreated()
            ->assertJsonPath('data.email', 'beauty-fan@example.test');

        $this->postJson('/newsletter/subscribe', $payload)
            ->assertOk()
            ->assertJsonPath('data.email', 'beauty-fan@example.test');

        $this->assertDatabaseHas('news_letters', [
            'email' => 'beauty-fan@example.test',
        ]);
        $this->assertSame(1, \DB::table('news_letters')->where('email', 'beauty-fan@example.test')->count());
    }

    private function discount(string $code, int $isFixed, int $amount, $startsAt = null, $expiresAt = null): Discount
    {
        return Discount::create([
            'code' => $code,
            'description' => $code . ' promotion',
            'is_fixed' => $isFixed,
            'discount_amount' => $amount,
            'starts_at' => $startsAt ?? now()->subDay(),
            'expires_at' => $expiresAt ?? now()->addDay(),
        ]);
    }

    private function product(int $price): Product
    {
        $brand = Brand::create(['name' => 'Promotion Brand ' . uniqid(), 'status' => 1, 'created_by' => 1]);
        $category = Category::create(['name' => 'Promotion Category ' . uniqid(), 'status' => 1, 'created_by' => 1]);

        return Product::create([
            'code' => 'PROMO-' . uniqid(),
            'name' => 'Promotion Serum',
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'price' => $price,
            'discount_percentage' => 0,
            'discount_from_date' => now()->toDateString(),
            'discount_to_date' => now()->addDay()->toDateString(),
            'media_id' => 1,
            'inventory_qty' => 10,
            'description' => 'Promotion product',
            'star' => 4.5,
            'featured' => 0,
            'created_by' => 1,
            'status' => 1,
        ]);
    }
}
