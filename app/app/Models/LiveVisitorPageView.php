<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LiveVisitorPageView extends Model
{
    use HasFactory;

    protected $fillable = [
        'visitor_token',
        'user_id',
        'session_id',
        'path',
        'url',
        'page_title',
        'referrer_url',
        'entered_at',
        'left_at',
        'duration_seconds',
        'exit_type',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'entered_at' => 'datetime',
            'left_at' => 'datetime',
            'duration_seconds' => 'integer',
            'metadata' => 'array',
        ];
    }

    public function visitor(): BelongsTo
    {
        return $this->belongsTo(LiveVisitor::class, 'visitor_token', 'visitor_token');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
