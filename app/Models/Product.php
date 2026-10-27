<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'metal_id',
        'name',
        'slug',
        'sku',
        'short_description',
        'description',
        'stone',
        'weight_grams',
        'price',
        'mrp',
        'stock',
        'rating_avg',
        'rating_count',
        'art_symbol',
        'color_class',
        'is_featured',
        'is_active',
        'published_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'mrp' => 'decimal:2',
        'weight_grams' => 'decimal:2',
        'rating_avg' => 'decimal:2',
        'rating_count' => 'integer',
        'stock' => 'integer',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
            if (empty($product->sku)) {
                $product->sku = 'DSY-' . strtoupper(Str::random(6));
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function metal(): BelongsTo
    {
        return $this->belongsTo(Metal::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderByDesc('is_cover')->orderBy('sort_order');
    }

    public function coverImage()
    {
        $cover = $this->images->firstWhere('is_cover', true);
        return $cover ? $cover->path : ($this->images->first()?->path ?? null);
    }

    public function sizes(): HasMany
    {
        return $this->hasMany(ProductSize::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class)->latest();
    }

    public function approvedReviews(): HasMany
    {
        return $this->hasMany(Review::class)->where('is_approved', true)->latest();
    }

    public function getDiscountPercentageAttribute(): int
    {
        if ($this->mrp && $this->mrp > $this->price) {
            return (int) round((($this->mrp - $this->price) / $this->mrp) * 100);
        }
        return 0;
    }

    public function getFormattedPriceAttribute(): string
    {
        return self::formatInr($this->price);
    }

    public function getFormattedMrpAttribute(): ?string
    {
        return $this->mrp ? self::formatInr($this->mrp) : null;
    }

    public static function formatInr($amount): string
    {
        $num = (int) round($amount);
        $negative = $num < 0 ? '-' : '';
        $num = abs($num);
        $str = (string) $num;
        $len = strlen($str);

        if ($len <= 3) {
            return $negative . '₹' . $str;
        }

        $last3 = substr($str, -3);
        $rest = substr($str, 0, -3);
        $groups = [];

        while (strlen($rest) > 2) {
            $groups[] = substr($rest, -2);
            $rest = substr($rest, 0, -2);
        }
        if ($rest !== '') {
            $groups[] = $rest;
        }

        $formatted = implode(',', array_reverse($groups)) . ',' . $last3;
        return $negative . '₹' . $formatted;
    }
}
