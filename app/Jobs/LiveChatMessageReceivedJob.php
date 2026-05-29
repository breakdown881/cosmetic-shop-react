<?php

namespace App\Jobs;

use App\Models\LiveChatMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class LiveChatMessageReceivedJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public function __construct(
        public readonly int $conversationId,
        public readonly int $messageId,
    ) {
        $this->onConnection(config('queue.live_chat_connection', 'rabbitmq'));
        $this->onQueue(config('queue.live_chat_queue', 'live-chat'));
    }

    public function handle(): void
    {
        $message = LiveChatMessage::query()->find($this->messageId);

        if (! $message) {
            return;
        }

        $message->forceFill([
            'status' => LiveChatMessage::STATUS_PROCESSED,
            'processed_at' => now(),
        ])->save();

        Log::info('Live chat customer message queued for staff notification.', [
            'conversation_id' => $this->conversationId,
            'message_id' => $this->messageId,
        ]);
    }
}
