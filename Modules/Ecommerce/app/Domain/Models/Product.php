<?php

namespace Modules\Ecommerce\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $shop_id
 * @property string $sku
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property float|string $price
 * @property float|string|null $compare_at_price
 * @property float|string|null $cost_price
 * @property string $currency
 * @property int $stock_qty
 * @property bool $track_inventory
 * @property bool $allow_backorder
 * @property bool $is_active
 * @property string|null $barcode
 * @property float|string|null $weight
 * @property float|string|null $length
 * @property float|string|null $width
 * @property float|string|null $height
 * @property array<string, mixed>|null $meta
 */
final class Product extends Model
{
    use SoftDeletes;

    protected $table = 'ecm_products';

    protected $fillable = [
        'shop_id',
        'sku',
        'name',
        'slug',
        'description',
        'price',
        'compare_at_price',
        'cost_price',
        'currency',
        'stock_qty',
        'track_inventory',
        'allow_backorder',
        'is_active',
        'barcode',
        'weight',
        'length',
        'width',
        'height',
        'meta',
    ];

    protected $casts = [
        'shop_id' => 'integer',
        'price' => 'decimal:2',
        'compare_at_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'stock_qty' => 'integer',
        'track_inventory' => 'boolean',
        'allow_backorder' => 'boolean',
        'is_active' => 'boolean',
        'weight' => 'decimal:3',
        'length' => 'decimal:3',
        'width' => 'decimal:3',
        'height' => 'decimal:3',
        'meta' => 'array',
    ];

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'ecm_category_product', 'product_id', 'category_id')
            ->withPivot(['shop_id'])
            ->wherePivot('shop_id', $this->shop_id);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class, 'product_id')
            ->where('shop_id', $this->shop_id);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class, 'product_id')
            ->where('shop_id', $this->shop_id);
    }
}
