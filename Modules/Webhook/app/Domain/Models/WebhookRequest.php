<?php

namespace Modules\Webhook\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Log request nhan vao webhook de debug (tuỳ nhu cầu co the prune sau).
 *
 * @property int $id
 * @property int $webhook_id
 * @property string $method
 * @property string|null $ip
 * @property array<string, mixed>|null $headers
 * @property array<string, mixed>|null $query
 * @property string|null $body
 * @property \Illuminate\Support\Carbon $received_at
 */
final class WebhookRequest extends Model
{
    protected $table = 'wh_webhook_requests';

    protected $fillable = [
        'webhook_id',
        'method',
        'ip',
        'headers',
        'query',
        'body',
        'mapped_payload',
        'status',
        'error_type',
        'error_message',
        'received_at',
    ];

    protected $casts = [
        'headers' => 'array',
        'query' => 'array',
        'mapped_payload' => 'array',
        'received_at' => 'datetime',
    ];

    public function webhook(): BelongsTo
    {
        return $this->belongsTo(Webhook::class, 'webhook_id');
    }
}

