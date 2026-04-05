<?php

namespace Modules\Ecommerce\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $shop_id
 * @property string|null $code
 * @property string|null $name
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $gender
 * @property \Illuminate\Support\Carbon|null $birthday
 * @property array<int, string>|null $tags
 * @property string|null $note
 */
final class Customer extends Model
{
    use SoftDeletes;

    protected $table = 'ecm_customers';

    protected $fillable = [
        'shop_id',
        'code',
        'name',
        'email',
        'phone',
        'gender',
        'birthday',
        'tags',
        'note',
    ];

    protected $casts = [
        'shop_id' => 'integer',
        'birthday' => 'date',
        'tags' => 'array',
    ];

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(CustomerAddress::class, 'customer_id')
            ->where('shop_id', $this->shop_id);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'customer_id')
            ->where('shop_id', $this->shop_id);
    }
}
