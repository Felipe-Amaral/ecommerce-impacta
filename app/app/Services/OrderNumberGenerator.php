<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Str;

class OrderNumberGenerator
{
    public function generate(): string
    {
        for ($attempt = 0; $attempt < 10; $attempt++) {
            $candidate = 'GI-'.now()->format('Ymd').'-'.Str::upper(Str::random(6));

            if (! Order::query()->where('order_number', $candidate)->exists()) {
                return $candidate;
            }
        }

        return 'GI-'.now()->format('Ymd-His').'-'.Str::upper(Str::random(8));
    }
}
