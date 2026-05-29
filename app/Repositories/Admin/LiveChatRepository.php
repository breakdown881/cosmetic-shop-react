<?php

namespace App\Repositories\Admin;

use App\Models\LiveChatConversation;
use App\Models\LiveChatMessage;
use Illuminate\Support\Collection;

class LiveChatRepository
{
    public function conversations(): Collection
    {
        return LiveChatConversation::query()
            ->with(['customer', 'staff', 'messages.staff', 'messages.customer'])
            ->latest('last_message_at')
            ->get();
    }

    public function find(int|string $id): LiveChatConversation
    {
        return LiveChatConversation::query()
            ->with(['customer', 'staff', 'messages.staff', 'messages.customer'])
            ->findOrFail($id);
    }

    public function addStaffMessage(LiveChatConversation $conversation, int $staffId, string $message): LiveChatMessage
    {
        $chatMessage = $conversation->messages()->create([
            'sender_type' => LiveChatMessage::SENDER_STAFF,
            'sender_id' => $staffId,
            'message' => $message,
            'status' => LiveChatMessage::STATUS_PENDING,
        ]);

        $conversation->forceFill([
            'staff_id' => $staffId,
            'last_message_at' => now(),
        ])->save();

        return $chatMessage;
    }

    public function loadConversation(LiveChatConversation $conversation): LiveChatConversation
    {
        return $conversation->refresh()->load(['customer', 'staff', 'messages.staff', 'messages.customer']);
    }

    public function markMessageProcessed(int|string $messageId): void
    {
        LiveChatMessage::query()
            ->whereKey($messageId)
            ->update([
                'status' => LiveChatMessage::STATUS_PROCESSED,
                'processed_at' => now(),
            ]);
    }
}
