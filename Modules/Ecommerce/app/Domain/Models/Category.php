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
 * @property int|null $parent_id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $image_url
 * @property string|null $seo_title
 * @property string|null $seo_description
 * @property int $position
 * @property bool $is_active
 */
final class Category extends Model
{
    use SoftDeletes;

    protected $table = 'ecm_categories';

    protected $fillable = [
        'shop_id',
        'parent_id',
        'name',
        'slug',
        'description',
        'image_url',
        'seo_title',
        'seo_description',
        'position',
        'is_active',
    ];

    protected $casts = [
        'shop_id' => 'integer',
        'parent_id' => 'integer',
        'position' => 'integer',
        'is_active' => 'boolean',
    ];

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id')
            ->where('shop_id', $this->shop_id);
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')
            ->where('shop_id', $this->shop_id);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'ecm_category_product', 'category_id', 'product_id')
            ->withPivot(['shop_id'])
            ->wherePivot('shop_id', $this->shop_id);
    }
}
