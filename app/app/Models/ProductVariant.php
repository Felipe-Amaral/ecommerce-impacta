<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'name',
        'sku',
        'attributes',
        'price',
        'promotional_price',
        'production_days',
        'weight_grams',
        'stock_qty',
        'is_active',
        'sort_order',
    ];

    protected $appends = [
        'effective_price',
    ];

    protected function casts(): array
    {
        return [
            'attributes' => 'array',
            'price' => 'decimal:2',
            'promotional_price' => 'decimal:2',
            'production_days' => 'integer',
            'weight_grams' => 'integer',
            'stock_qty' => 'integer',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function getEffectivePriceAttribute(): string
    {
        $price = $this->promotional_price ?: $this->price;

        return number_format((float) $price, 2, '.', '');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class, 'product_variant_id');
    }
}
