<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LiveChatMessage extends Model
{
    use HasFactory;

    public const SENDER_CUSTOMER = 'customer';
    public const SENDER_STAFF = 'staff';
    public const STATUS_PENDING = 'PENDING';
    public const STATUS_PROCESSED = 'PROCESSED';

    protected $fillable = [
        'conversation_id',
        'sender_type',
        'sender_id',
        'message',
        'status',
        'processed_at',
        'read_at',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
        'read_at' => 'datetime',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(LiveChatConversation::class, 'conversation_id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'sender_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
