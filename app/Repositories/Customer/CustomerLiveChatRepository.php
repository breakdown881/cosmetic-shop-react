<?php

namespace App\Repositories\Customer;

use App\Models\LiveChatConversation;
use App\Models\LiveChatMessage;

class CustomerLiveChatRepository
{
    public function findOpenForCustomer(int|string $id, string $sessionId, ?int $userId): ?LiveChatConversation
    {
        return LiveChatConversation::query()
            ->where('id', $id)
            ->where('status', LiveChatConversation::STATUS_OPEN)
            ->with(['customer', 'staff', 'messages.staff', 'messages.customer'])
            ->first();
    }

    public function create(string $sessionId, ?int $userId): LiveChatConversation
    {
        return LiveChatConversation::query()->create([
            'user_id' => $userId,
            'session_id' => $sessionId,
            'status' => LiveChatConversation::STATUS_OPEN,
            'last_message_at' => now(),
        ]);
    }

    public function attachCustomer(LiveChatConversation $conversation, ?int $userId): LiveChatConversation
    {
        if ($userId && ! $conversation->user_id) {
            $conversation->forceFill(['user_id' => $userId])->save();
        }

        return $conversation;
    }

    public function addCustomerMessage(LiveChatConversation $conversation, ?int $userId, string $message): LiveChatMessage
    {
        $chatMessage = $conversation->messages()->create([
            'sender_type' => LiveChatMessage::SENDER_CUSTOMER,
            'sender_id' => $userId,
            'message' => $message,
        ]);

        $conversation->forceFill(['last_message_at' => now()])->save();

        return $chatMessage;
    }

    public function loadConversation(LiveChatConversation $conversation): LiveChatConversation
    {
        return $conversation->refresh()->load(['customer', 'staff', 'messages.staff', 'messages.customer']);
    }
}
