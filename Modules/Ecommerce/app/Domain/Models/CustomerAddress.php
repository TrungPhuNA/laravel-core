<?php

namespace Modules\Ecommerce\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $shop_id
 * @property int $customer_id
 * @property string|null $label
 * @property string|null $name
 * @property string|null $phone
 * @property string $line1
 * @property string|null $line2
 * @property string|null $city
 * @property string|null $state
 * @property string|null $postal_code
 * @property string $country
 * @property bool $is_default_shipping
 * @property bool $is_default_billing
 */
final class CustomerAddress extends Model
{
    protected $table = 'ecm_customer_addresses';

    protected $fillable = [
        'shop_id',
        'customer_id',
        'label',
        'name',
        'phone',
        'line1',
        'line2',
        'city',
        'state',
        'postal_code',
        'country',
        'is_default_shipping',
        'is_default_billing',
    ];

    protected $casts = [
        'shop_id' => 'integer',
        'customer_id' => 'integer',
        'is_default_shipping' => 'boolean',
        'is_default_billing' => 'boolean',
    ];

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
