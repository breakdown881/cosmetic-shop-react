<?php

namespace App\Services\Customer;

use App\Jobs\LiveChatMessageReceivedJob;
use App\Models\LiveChatConversation;
use App\Models\LiveChatMessage;
use App\Models\User;
use App\Repositories\Customer\CustomerLiveChatRepository;
use Illuminate\Session\Store;

class CustomerLiveChatService
{
    private const SESSION_KEY = 'customer_live_chat_conversation_id';

    public function __construct(private readonly CustomerLiveChatRepository $liveChats) {}

    public function conversation(Store $session, ?User $user): ?array
    {
        $conversation = $this->currentConversation($session, $user);

        return $conversation ? $this->formatConversation($conversation) : null;
    }

    public function send(Store $session, ?User $user, string $message): array
    {
        $conversation = $this->currentConversation($session, $user)
            ?? $this->liveChats->create($session->getId(), $user?->id);

        $this->liveChats->attachCustomer($conversation, $user?->id);
        $chatMessage = $this->liveChats->addCustomerMessage($conversation, $user?->id, $message);
        $session->put(self::SESSION_KEY, $conversation->id);
        LiveChatMessageReceivedJob::dispatch($conversation->id, $chatMessage->id);

        return $this->formatConversation($this->liveChats->loadConversation($conversation));
    }

    private function currentConversation(Store $session, ?User $user): ?LiveChatConversation
    {
        $conversationId = $session->get(self::SESSION_KEY);

        if (! $conversationId) {
            return null;
        }

        return $this->liveChats->findOpenForCustomer($conversationId, $session->getId(), $user?->id);
    }

    private function formatConversation(LiveChatConversation $conversation): array
    {
        return [
            'id' => $conversation->id,
            'status' => $conversation->status,
            'customer' => $conversation->customer ? [
                'id' => $conversation->customer->id,
                'name' => $conversation->customer->name,
                'email' => $conversation->customer->email,
            ] : null,
            'staff' => $conversation->staff ? [
                'id' => $conversation->staff->id,
                'name' => $conversation->staff->name,
            ] : null,
            'messages' => $conversation->messages
                ->sortBy('created_at')
                ->map(fn (LiveChatMessage $message) => $this->formatMessage($message))
                ->values(),
        ];
    }

    private function formatMessage(LiveChatMessage $message): array
    {
        return [
            'id' => $message->id,
            'sender_type' => $message->sender_type,
            'message' => $message->message,
            'status' => $message->status,
            'created_at' => $message->created_at?->toIso8601String(),
            'staff' => $message->sender_type === LiveChatMessage::SENDER_STAFF && $message->staff ? [
                'id' => $message->staff->id,
                'name' => $message->staff->name,
            ] : null,
        ];
    }
}
