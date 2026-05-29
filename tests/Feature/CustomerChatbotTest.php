<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CustomerChatbotTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        config(['scout.driver' => 'database']);
    }

    public function test_chatbot_uses_controller_request_service_repository_pattern(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/Customer/ChatbotController.php'));
        $service = file_get_contents(app_path('Services/Customer/CustomerChatbotService.php'));
        $repository = file_get_contents(app_path('Repositories/Customer/CustomerChatbotRepository.php'));

        $this->assertStringContainsString('CustomerChatbotService', $controller);
        $this->assertStringContainsString('ChatbotMessageRequest', $controller);
        $this->assertStringNotContainsString('use App\\Models\\', $controller);
        $this->assertStringNotContainsString('Product::', $controller);

        $this->assertStringContainsString('CustomerChatbotRepository', $service);
        $this->assertStringNotContainsString('Product::', $service);
        $this->assertStringNotContainsString('DB::table', $service);

        $this->assertStringContainsString('Product::query()', $repository);
        $this->assertStringContainsString('DB::table', $repository);
    }

    public function test_guest_can_ask_chatbot_for_matching_products_and_message_is_logged(): void
    {
        $this->product('Vitamin C Serum', 'Brightening serum for dull skin', 320000);
        $this->product('Gentle Cleanser', 'Low pH cleanser', 180000);

        $this->postJson('/chatbot/messages', [
            'message' => 'serum vitamin c',
        ])
            ->assertOk()
            ->assertJsonPath('data.intent', 'product_recommendation')
            ->assertJsonPath('data.suggestions.0.name', 'Vitamin C Serum')
            ->assertJsonPath('data.reply', 'Minh tim thay mot vai san pham phu hop cho ban: Vitamin C Serum.');

        $this->assertDatabaseHas('chatbot_messages', [
            'user_id' => null,
            'question' => 'serum vitamin c',
            'intent' => 'product_recommendation',
        ]);
    }

    public function test_authenticated_user_chatbot_message_is_linked_to_user(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson('/chatbot/messages', ['message' => 'phi ship'])
            ->assertOk()
            ->assertJsonPath('data.intent', 'shipping_policy')
            ->assertJsonPath('data.suggestions', []);

        $this->assertDatabaseHas('chatbot_messages', [
            'user_id' => $user->id,
            'question' => 'phi ship',
            'intent' => 'shipping_policy',
        ]);
    }

    public function test_chatbot_validates_message(): void
    {
        $this->postJson('/chatbot/messages', ['message' => ''])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['message']);
    }

    private function product(string $name, string $description, int $price): Product
    {
        $brand = Brand::create(['name' => 'Chatbot Brand ' . uniqid(), 'status' => 1, 'created_by' => 1]);
        $category = Category::create(['name' => 'Chatbot Category ' . uniqid(), 'status' => 1, 'created_by' => 1]);

        return Product::withoutSyncingToSearch(fn () => Product::query()->create([
            'code' => 'CHAT-' . uniqid(),
            'name' => $name,
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'price' => $price,
            'discount_percentage' => 0,
            'discount_from_date' => now()->toDateString(),
            'discount_to_date' => now()->addDay()->toDateString(),
            'media_id' => 1,
            'inventory_qty' => 10,
            'description' => $description,
            'star' => 4.5,
            'featured' => 1,
            'created_by' => 1,
            'status' => 1,
        ]));
    }
}
