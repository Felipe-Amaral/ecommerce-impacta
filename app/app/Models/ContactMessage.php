<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'subject',
        'service_interest',
        'preferred_contact',
        'order_reference',
        'message',
        'lgpd_consent',
        'status',
        'source_url',
        'ip_address',
        'user_agent',
        'read_at',
        'responded_at',
    ];

    protected function casts(): array
    {
        return [
            'lgpd_consent' => 'boolean',
            'read_at' => 'datetime',
            'responded_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
