<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LiveChatSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'visitor_token',
        'user_id',
        'assigned_admin_id',
        'status',
        'visitor_name',
        'visitor_email',
        'visitor_phone',
        'current_url',
        'current_path',
        'first_message_at',
        'last_message_at',
        'closed_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'first_message_at' => 'datetime',
            'last_message_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', 'open');
    }

    public function scopeOpenOrWaiting(Builder $query): Builder
    {
        return $query->whereIn('status', ['open', 'offline_message']);
    }

    public function visitor(): HasOne
    {
        return $this->hasOne(LiveVisitor::class, 'visitor_token', 'visitor_token');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_admin_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(LiveChatMessage::class, 'live_chat_session_id')->orderBy('id');
    }

    public function latestMessage(): HasOne
    {
        return $this->hasOne(LiveChatMessage::class, 'live_chat_session_id')->latestOfMany();
    }
}
