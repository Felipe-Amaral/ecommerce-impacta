<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LiveVisitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'visitor_token',
        'session_id',
        'user_id',
        'ip_address',
        'user_agent',
        'landing_url',
        'current_url',
        'current_path',
        'referrer_url',
        'page_title',
        'country_code',
        'timezone',
        'language',
        'screen_size',
        'metadata',
        'first_seen_at',
        'last_seen_at',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'first_seen_at' => 'datetime',
            'last_seen_at' => 'datetime',
        ];
    }

    public function scopeActive(Builder $query, int $seconds = 90): Builder
    {
        return $query->where('last_seen_at', '>=', now()->subSeconds(max(10, $seconds)));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function chatSessions(): HasMany
    {
        return $this->hasMany(LiveChatSession::class, 'visitor_token', 'visitor_token');
    }
}
