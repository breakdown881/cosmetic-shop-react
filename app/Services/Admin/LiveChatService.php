<?php

namespace App\Services\Admin;

use App\Models\LiveChatConversation;
use App\Models\LiveChatMessage;
use App\Repositories\Admin\LiveChatRepository;

class LiveChatService
{
    public function __construct(private readonly LiveChatRepository $liveChats) {}

    public function inbox(): array
    {
        $conversations = $this->liveChats->conversations();

        return [
            'unread_count' => $conversations->filter(fn (LiveChatConversation $conversation) => $this->needsStaffReply($conversation))->count(),
            'data' => $conversations->map(fn (LiveChatConversation $conversation) => $this->formatConversation($conversation))->values(),
        ];
    }

    public function find(int|string $id): array
    {
        return $this->formatConversation($this->liveChats->find($id));
    }

    public function reply(int|string $id, int $staffId, string $message): array
    {
        $conversation = $this->liveChats->find($id);

        $this->liveChats->addStaffMessage($conversation, $staffId, $message);

        return $this->formatConversation($this->liveChats->loadConversation($conversation));
    }

    private function formatConversation(LiveChatConversation $conversation): array
    {
        $latestMessage = $conversation->messages->sortByDesc('id')->first();

        return [
            'id' => $conversation->id,
            'status' => $conversation->status,
            'customer' => $conversation->customer ? [
                'id' => $conversation->customer->id,
                'name' => $conversation->customer->name,
                'email' => $conversation->customer->email,
            ] : ['name' => 'Guest customer', 'email' => null],
            'staff' => $conversation->staff ? [
                'id' => $conversation->staff->id,
                'name' => $conversation->staff->name,
            ] : null,
            'latest_message' => $latestMessage?->message,
            'needs_staff_reply' => $this->needsStaffReply($conversation),
            'last_message_at' => $conversation->last_message_at?->toIso8601String(),
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
            'created_at' => $message->created_at?->toIso8601String(),
            'staff' => $message->sender_type === LiveChatMessage::SENDER_STAFF && $message->staff ? [
                'id' => $message->staff->id,
                'name' => $message->staff->name,
            ] : null,
        ];
    }

    private function needsStaffReply(LiveChatConversation $conversation): bool
    {
        return $conversation->messages->sortByDesc('id')->first()?->sender_type === LiveChatMessage::SENDER_CUSTOMER;
    }
}
