<?php

namespace Modules\Webhook\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $webhook_id
 * @property string $name
 * @property string $url
 * @property string $http_method
 * @property bool $is_active
 * @property array<string, string>|null $headers
 * @property string $send_mode
 * @property array<int, array{from?: string, to?: string}>|null $field_mappings
 * @property bool $drop_mapped_sources
 * @property int $timeout_seconds
 */
final class WebhookDestination extends Model
{
    protected $table = 'wh_webhook_destinations';

    protected $fillable = [
        'webhook_id',
        'name',
        'url',
        'http_method',
        'is_active',
        'type',
        'headers',
        'send_mode',
        'field_mappings',
        'drop_mapped_sources',
        'timeout_seconds',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'headers' => 'array',
        'field_mappings' => 'array',
        'drop_mapped_sources' => 'boolean',
        'timeout_seconds' => 'integer',
    ];

    public function webhook(): BelongsTo
    {
        return $this->belongsTo(Webhook::class, 'webhook_id');
    }
}

