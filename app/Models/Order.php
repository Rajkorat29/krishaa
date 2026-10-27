<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_address_line',
        'shipping_city',
        'shipping_state',
        'shipping_pincode',
        'subtotal',
        'discount',
        'delivery_fee',
        'total',
        'payment_method',
        'payment_status',
        'order_status',
        'coupon_code',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'DSY-' . strtoupper(Str::random(6));
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function canCancel(): bool
    {
        return in_array($this->order_status, ['placed']);
    }

    public function getFormattedTotalAttribute(): string
    {
        return Product::formatInr($this->total);
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return Product::formatInr($this->subtotal);
    }

    public function getFormattedDiscountAttribute(): string
    {
        return Product::formatInr($this->discount);
    }

    public function getFormattedDeliveryFeeAttribute(): string
    {
        return $this->delivery_fee > 0 ? Product::formatInr($this->delivery_fee) : 'Free';
    }
}
