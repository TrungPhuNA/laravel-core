<?php

namespace Modules\Ecommerce\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class ProductVariant extends Model
{
    use SoftDeletes;

    protected $table = 'ecm_product_variants';

    protected $fillable = [
        'shop_id',
        'product_id',
        'sku',
        'name',
        'options',
        'price',
        'compare_at_price',
        'cost_price',
        'currency',
        'stock_qty',
        'is_active',
        'meta',
    ];

    protected $casts = [
        'shop_id' => 'integer',
        'product_id' => 'integer',
        'options' => 'array',
        'price' => 'decimal:2',
        'compare_at_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'stock_qty' => 'integer',
        'is_active' => 'boolean',
        'meta' => 'array',
    ];

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class, 'variant_id')
            ->where('shop_id', $this->shop_id);
    }
}
