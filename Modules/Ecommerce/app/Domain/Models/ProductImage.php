<?php

namespace Modules\Ecommerce\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ProductImage extends Model
{
    protected $table = 'ecm_product_images';

    protected $fillable = [
        'shop_id',
        'product_id',
        'variant_id',
        'url',
        'alt',
        'position',
        'is_primary',
    ];

    protected $casts = [
        'shop_id' => 'integer',
        'product_id' => 'integer',
        'variant_id' => 'integer',
        'position' => 'integer',
        'is_primary' => 'boolean',
    ];

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }
}

