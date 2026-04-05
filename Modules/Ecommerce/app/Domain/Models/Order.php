<?php

namespace Modules\Ecommerce\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $shop_id
 * @property string $code
 * @property int|null $customer_id
 * @property string $status
 * @property string $payment_status
 * @property string $fulfillment_status
 * @property float|string $subtotal
 * @property float|string $discount_total
 * @property float|string $tax_total
 * @property float|string $shipping_total
 * @property float|string $total
 * @property string $currency
 * @property string|null $customer_name
 * @property string|null $customer_email
 * @property string|null $customer_phone
 * @property array<string, mixed>|null $customer_snapshot
 * @property array<string, mixed>|null $shipping_address
 * @property array<string, mixed>|null $billing_address
 * @property string|null $payment_method
 * @property string|null $shipping_method
 * @property string|null $shipping_provider
 * @property string|null $tracking_number
 * @property string|null $note
 * @property array<string, mixed>|null $meta
 */
final class Order extends Model
{
    use SoftDeletes;

    protected $table = 'ecm_orders';

    protected $fillable = [
        'code',
        'shop_id',
        'customer_id',
        'status',
        'payment_status',
        'fulfillment_status',
        'subtotal',
        'discount_total',
        'tax_total',
        'shipping_total',
        'total',
        'currency',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_snapshot',
        'shipping_address',
        'billing_address',
        'payment_method',
        'shipping_method',
        'shipping_provider',
        'tracking_number',
        'note',
        'meta',
        'placed_at',
        'paid_at',
        'cancelled_at',
    ];

    protected $casts = [
        'shop_id' => 'integer',
        'customer_id' => 'integer',
        'subtotal' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'shipping_total' => 'decimal:2',
        'total' => 'decimal:2',
        'customer_snapshot' => 'array',
        'shipping_address' => 'array',
        'billing_address' => 'array',
        'meta' => 'array',
        'placed_at' => 'datetime',
        'paid_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id')
            ->where('shop_id', $this->shop_id);
    }
}
