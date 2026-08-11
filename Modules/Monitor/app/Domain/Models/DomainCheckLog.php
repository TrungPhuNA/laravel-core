<?php

namespace Modules\Monitor\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $domain_id
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $expires_at_found
 * @property string|null $registrar
 * @property string|null $method
 * @property string|null $error_message
 * @property string|null $raw_response
 * @property \Illuminate\Support\Carbon $checked_at
 */
final class DomainCheckLog extends Model
{
    protected $table = 'dmn_domain_check_logs';

    protected $fillable = [
        'domain_id',
        'status',
        'expires_at_found',
        'registrar',
        'method',
        'error_message',
        'raw_response',
        'checked_at',
    ];

    protected $casts = [
        'expires_at_found' => 'datetime',
        'checked_at' => 'datetime',
    ];

    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class, 'domain_id');
    }
}