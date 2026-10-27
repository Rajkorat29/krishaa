<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Drop extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'title',
        'subtitle',
        'drop_date',
        'drop_time',
        'metal_label',
        'weight_label',
        'price_label',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
