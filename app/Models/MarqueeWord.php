<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarqueeWord extends Model
{
    use HasFactory;

    protected $fillable = [
        'word',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];
}
