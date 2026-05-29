<?php

namespace Tests\Feature;

use App\Jobs\LiveChatMessageReceivedJob;
use App\Jobs\LiveChatStaffReplyJob;
use App\Jobs\ProcessCustomerOrderJob;
use App\Models\Admin;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class RabbitMqQueueIntegrationTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'queue.order_connection' => 'rabbitmq',
            'queue.live_chat_connection' => 'rabbitmq',
            'scout.driver' => 'database',
        ]);
    }

    public function test_rabbitmq_connection_is_configured_for_order_and_live_chat_queues(): void
    {
        $this->assertSame('rabbitmq', config('queue.connections.rabbitmq.driver'));
        $this->assertSame('orders', config('queue.order_queue'));
        $this->assertSame('live-chat', config('queue.live_chat_queue'));
        $this->assertSame('rabbitmq', config('queue.order_connection'));
        $this->assertSame('rabbitmq', config('queue.live_chat_connection'));
    }

    public function test_customer_checkout_queues_order_creation_without_creating_order_inline(): void
    {
        Queue::fake();

        $customer = User::factory()->create();
        $product = $this->product('RabbitMQ Order Serum', 250000);

        $response = $this->actingAs($customer)
            ->withSession(['customer_cart' => [$product->id => 1]])
            ->postJson('/checkout', $this->checkoutPayload())
            ->assertAccepted()
            ->assertJsonPath('data.status', 'QUEUED');

        $checkoutRequestId = $response->json('data.id');

        $this->assertDatabaseHas('customer_checkout_requests', [
            'id' => $checkoutRequestId,
            'customer_id' => $customer->id,
            'status' => 'QUEUED',
        ]);
        $this->assertDatabaseMissing('orders', [
            'customer_id' => $customer->id,
            'shipping_mobile' => '0900111222',
        ]);

        Queue::assertPushed(ProcessCustomerOrderJob::class, function (ProcessCustomerOrderJob $job) use ($checkoutRequestId) {
            return $job->checkoutRequestId === $checkoutRequestId
                && $job->connection === 'rabbitmq'
                && $job->queue === 'orders';
        });
    }

    public function test_customer_live_chat_message_dispatches_staff_notification_job_to_rabbitmq(): void
    {
        Queue::fake();

        $response = $this->postJson('/live-chat/messages', [
            'message' => 'Can nhan vien tu van qua RabbitMQ.',
        ])->assertCreated();

        $conversationId = $response->json('data.id');
        $messageId = $response->json('data.messages.0.id');

        Queue::assertPushed(LiveChatMessageReceivedJob::class, function (LiveChatMessageReceivedJob $job) use ($conversationId, $messageId) {
            return $job->conversationId === $conversationId
                && $job->messageId === $messageId
                && $job->connection === 'rabbitmq'
                && $job->queue === 'live-chat';
        });
        $this->assertDatabaseHas('live_chat_messages', [
            'id' => $messageId,
            'status' => 'PENDING',
        ]);
    }

    public function test_staff_reply_dispatches_customer_notification_job_to_rabbitmq(): void
    {
        Queue::fake();

        $staff = Admin::create([
            'name' => 'RabbitMQ Staff',
            'email' => 'rabbitmq-staff-' . uniqid('', true) . '@example.test',
            'password' => 'password',
            'role' => 'STAFF',
            'is_active' => true,
        ]);

        $conversation = $this->postJson('/live-chat/messages', [
            'message' => 'Customer waiting for RabbitMQ reply.',
        ])->assertCreated()->json('data');

        Queue::fake();

        $response = $this->actingAs($staff, 'admin')
            ->postJson('/admin/api/live-chat/conversations/' . $conversation['id'] . '/messages', [
                'message' => 'RabbitMQ staff reply.',
            ])
            ->assertCreated();

        $messageId = collect($response->json('data.messages'))->last()['id'];

        Queue::assertPushed(LiveChatStaffReplyJob::class, function (LiveChatStaffReplyJob $job) use ($conversation, $messageId) {
            return $job->conversationId === $conversation['id']
                && $job->messageId === $messageId
                && $job->connection === 'rabbitmq'
                && $job->queue === 'live-chat';
        });
        $this->assertDatabaseHas('live_chat_messages', [
            'id' => $messageId,
            'status' => 'PENDING',
        ]);
    }

    private function checkoutPayload(): array
    {
        return [
            'shipping_fullname' => 'Nguyen Van A',
            'shipping_mobile' => '0900111222',
            'shipping_ward_id' => '001',
            'shipping_housenumber_street' => '123 Queue Street',
            'payment_method' => 0,
        ];
    }

    private function product(string $name, int $price): Product
    {
        $brand = Brand::create(['name' => 'RabbitMQ Brand ' . uniqid(), 'status' => 1, 'created_by' => 1]);
        $category = Category::create(['name' => 'RabbitMQ Category ' . uniqid(), 'status' => 1, 'created_by' => 1]);

        return Product::withoutSyncingToSearch(fn () => Product::query()->create([
            'code' => 'RABBIT-' . uniqid(),
            'name' => $name,
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'price' => $price,
            'discount_percentage' => 0,
            'discount_from_date' => now()->toDateString(),
            'discount_to_date' => now()->addDay()->toDateString(),
            'media_id' => 1,
            'inventory_qty' => 10,
            'description' => 'RabbitMQ product description',
            'star' => 4.5,
            'featured' => 0,
            'created_by' => 1,
            'status' => 1,
        ]));
    }
}
