<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'sender_role',
        'body',
        'metadata',
        'read_by_client_at',
        'read_by_admin_at',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'read_by_client_at' => 'datetime',
            'read_by_admin_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
