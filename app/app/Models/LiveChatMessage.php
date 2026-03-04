<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LiveChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'live_chat_session_id',
        'sender_role',
        'user_id',
        'body',
        'is_read_by_visitor',
        'is_read_by_admin',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'is_read_by_visitor' => 'boolean',
            'is_read_by_admin' => 'boolean',
            'metadata' => 'array',
        ];
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(LiveChatSession::class, 'live_chat_session_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
