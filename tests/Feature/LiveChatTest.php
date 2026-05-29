<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LiveChatTest extends TestCase
{
    use DatabaseTransactions;

    public function test_live_chat_uses_controller_service_repository_pattern(): void
    {
        $customerController = file_get_contents(app_path('Http/Controllers/Customer/LiveChatController.php'));
        $adminController = file_get_contents(app_path('Http/Controllers/Api/Admin/LiveChatController.php'));
        $customerService = file_get_contents(app_path('Services/Customer/CustomerLiveChatService.php'));
        $adminService = file_get_contents(app_path('Services/Admin/LiveChatService.php'));
        $customerRepository = file_get_contents(app_path('Repositories/Customer/CustomerLiveChatRepository.php'));
        $adminRepository = file_get_contents(app_path('Repositories/Admin/LiveChatRepository.php'));

        $this->assertStringContainsString('CustomerLiveChatService', $customerController);
        $this->assertStringContainsString('LiveChatMessageRequest', $customerController);
        $this->assertStringNotContainsString('use App\\Models\\', $customerController);
        $this->assertStringNotContainsString('LiveChatConversation::', $customerController);

        $this->assertStringContainsString('LiveChatService', $adminController);
        $this->assertStringContainsString('LiveChatReplyRequest', $adminController);
        $this->assertStringNotContainsString('use App\\Models\\', $adminController);
        $this->assertStringNotContainsString('LiveChatConversation::', $adminController);

        $this->assertStringContainsString('CustomerLiveChatRepository', $customerService);
        $this->assertStringContainsString('LiveChatRepository', $adminService);
        $this->assertStringNotContainsString('LiveChatConversation::', $customerService);
        $this->assertStringNotContainsString('LiveChatConversation::', $adminService);

        $this->assertStringContainsString('LiveChatConversation::query()', $customerRepository);
        $this->assertStringContainsString('LiveChatConversation::query()', $adminRepository);
    }

    public function test_customer_can_start_live_chat_and_staff_sees_notification(): void
    {
        $staff = $this->staff();

        $this->postJson('/live-chat/messages', [
            'message' => 'Em can tu van serum cho da kho.',
        ])
            ->assertCreated()
            ->assertJsonPath('data.status', 'OPEN')
            ->assertJsonPath('data.messages.0.sender_type', 'customer')
            ->assertJsonPath('data.messages.0.message', 'Em can tu van serum cho da kho.')
            ->assertSessionHas('customer_live_chat_conversation_id');

        $this->assertDatabaseHas('live_chat_messages', [
            'sender_type' => 'customer',
            'message' => 'Em can tu van serum cho da kho.',
        ]);

        $this->actingAs($staff, 'admin')
            ->getJson('/admin/api/live-chat/conversations')
            ->assertOk()
            ->assertJsonPath('unread_count', 1)
            ->assertJsonPath('data.0.latest_message', 'Em can tu van serum cho da kho.')
            ->assertJsonPath('data.0.needs_staff_reply', true);
    }

    public function test_authenticated_customer_chat_is_linked_to_user(): void
    {
        $customer = User::factory()->create(['name' => 'Customer Live Chat']);

        $this->actingAs($customer)
            ->postJson('/live-chat/messages', ['message' => 'Shop co freeship khong?'])
            ->assertCreated()
            ->assertJsonPath('data.customer.name', 'Customer Live Chat');

        $this->assertDatabaseHas('live_chat_conversations', [
            'user_id' => $customer->id,
            'status' => 'OPEN',
        ]);
    }

    public function test_staff_can_reply_and_customer_can_fetch_conversation(): void
    {
        $staff = $this->staff();

        $response = $this->postJson('/live-chat/messages', [
            'message' => 'Tu van giup em kem chong nang.',
        ])->assertCreated();

        $conversationId = $response->json('data.id');

        $this->actingAs($staff, 'admin')
            ->postJson("/admin/api/live-chat/conversations/{$conversationId}/messages", [
                'message' => 'Da, shop goi y kem chong nang SPF50 cho da nhay cam a.',
            ])
            ->assertCreated()
            ->assertJsonPath('data.messages.1.sender_type', 'staff')
            ->assertJsonPath('data.messages.1.staff.name', 'Live Chat Staff');

        $this->withSession(['customer_live_chat_conversation_id' => $conversationId])
            ->getJson('/live-chat/conversation')
            ->assertOk()
            ->assertJsonPath('data.id', $conversationId)
            ->assertJsonPath('data.messages.1.message', 'Da, shop goi y kem chong nang SPF50 cho da nhay cam a.');

        $this->actingAs($staff, 'admin')
            ->getJson('/admin/api/live-chat/conversations')
            ->assertOk()
            ->assertJsonPath('unread_count', 0);
    }

    public function test_live_chat_validates_message(): void
    {
        $this->postJson('/live-chat/messages', ['message' => ''])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['message']);
    }

    private function staff(): Admin
    {
        return Admin::create([
            'name' => 'Live Chat Staff',
            'email' => 'live-chat-staff-' . uniqid('', true) . '@example.test',
            'password' => 'password',
            'role' => 'STAFF',
            'is_active' => true,
        ]);
    }
}
