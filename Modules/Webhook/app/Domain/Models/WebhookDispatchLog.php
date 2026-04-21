<?php

namespace Modules\Webhook\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $webhook_id
 * @property int $webhook_request_id
 * @property int $destination_id
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $dispatched_at
 * @property int|null $duration_ms
 * @property string|null $request_body
 * @property int|null $response_status
 * @property array<string, mixed>|null $response_headers
 * @property string|null $response_body
 * @property string|null $error_type
 * @property string|null $error_message
 */
final class WebhookDispatchLog extends Model
{
    protected $table = 'wh_webhook_dispatch_logs';

    protected $fillable = [
        'webhook_id',
        'webhook_request_id',
        'destination_id',
        'status',
        'dispatched_at',
        'duration_ms',
        'request_body',
        'response_status',
        'response_headers',
        'response_body',
        'error_type',
        'error_message',
    ];

    protected $casts = [
        'dispatched_at' => 'datetime',
        'duration_ms' => 'integer',
        'response_status' => 'integer',
        'response_headers' => 'array',
    ];

    public function webhook(): BelongsTo
    {
        return $this->belongsTo(Webhook::class, 'webhook_id');
    }

    public function request(): BelongsTo
    {
        return $this->belongsTo(WebhookRequest::class, 'webhook_request_id');
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(WebhookDestination::class, 'destination_id');
    }
}

