<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_token',
        'coupon_code',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function getSubtotalAttribute(): float
    {
        return (float) $this->items->sum(function ($item) {
            return ($item->product->price ?? 0) * $item->quantity;
        });
    }

    public function getMrpTotalAttribute(): float
    {
        return (float) $this->items->sum(function ($item) {
            $mrp = $item->product->mrp ?? $item->product->price ?? 0;
            return max($mrp, $item->product->price ?? 0) * $item->quantity;
        });
    }

    public function getTotalQuantityAttribute(): int
    {
        return (int) $this->items->sum('quantity');
    }

    public function getSavingsAttribute(): float
    {
        return max(0, $this->mrp_total - $this->subtotal);
    }
}
