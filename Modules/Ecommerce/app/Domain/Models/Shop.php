<?php

namespace Modules\Ecommerce\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

/**
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string|null $domain
 * @property string $timezone
 * @property string $currency
 * @property bool $is_active
 * @property array<string, mixed>|null $meta
 * @property int|null $created_by
 */
final class Shop extends Model
{
    use SoftDeletes;

    protected $table = 'ecm_shops';

    protected $fillable = [
        'code',
        'name',
        'domain',
        'timezone',
        'currency',
        'is_active',
        'meta',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'meta' => 'array',
        'created_by' => 'integer',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'ecm_shop_users', 'shop_id', 'user_id')
            ->withPivot(['role'])
            ->withTimestamps();
    }
}

