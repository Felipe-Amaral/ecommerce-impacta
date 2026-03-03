<?php

namespace App\Models;

use App\Enums\ArtworkFileStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArtworkFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_item_id',
        'storage_disk',
        'path',
        'original_name',
        'mime_type',
        'size_bytes',
        'checklist',
        'status',
        'reviewed_at',
        'review_notes',
        'metadata',
        'uploaded_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'checklist' => 'array',
            'status' => ArtworkFileStatus::class,
            'reviewed_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }
}
