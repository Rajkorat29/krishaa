<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'size_label',
        'quantity',
        'unit_price',
        'mrp',
        'image_snapshot',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'mrp' => 'decimal:2',
        'quantity' => 'integer',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getSubtotalAttribute(): float
    {
        return (float) ($this->unit_price * $this->quantity);
    }

    public function getFormattedUnitPriceAttribute(): string
    {
        return Product::formatInr($this->unit_price);
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return Product::formatInr($this->subtotal);
    }
}
