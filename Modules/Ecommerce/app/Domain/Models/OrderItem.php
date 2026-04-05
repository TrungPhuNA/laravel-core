<?php

namespace Modules\Ecommerce\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $shop_id
 * @property int $order_id
 * @property int|null $product_id
 * @property int|null $variant_id
 * @property string|null $sku
 * @property string $name
 * @property int $quantity
 * @property float|string $unit_price
 * @property float|string $total_price
 * @property float|string $discount_total
 * @property float|string $tax_total
 * @property array<string, mixed>|null $meta
 */
final class OrderItem extends Model
{
    protected $table = 'ecm_order_items';

    protected $fillable = [
        'shop_id',
        'order_id',
        'product_id',
        'variant_id',
        'sku',
        'name',
        'quantity',
        'unit_price',
        'total_price',
        'discount_total',
        'tax_total',
        'meta',
    ];

    protected $casts = [
        'shop_id' => 'integer',
        'order_id' => 'integer',
        'product_id' => 'integer',
        'variant_id' => 'integer',
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'meta' => 'array',
    ];

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
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

