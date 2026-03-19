<?php

namespace Modules\Webhook\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

/**
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $public_id
 * @property bool $is_active
 * @property array<int, string>|null $allowed_methods
 * @property string $auth_type
 * @property string|null $auth_token_hash
 * @property string|null $auth_secret_encrypted
 * @property array<string, mixed>|null $validation_rules
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $last_received_at
 */
final class Webhook extends Model
{
    use SoftDeletes;

    protected $table = 'wh_webhooks';

    protected $fillable = [
        'user_id',
        'name',
        'public_id',
        'is_active',
        'allowed_methods',
        'auth_type',
        'auth_token_hash',
        'auth_secret_encrypted',
        'validation_rules',
        'description',
        'last_received_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'allowed_methods' => 'array',
        'validation_rules' => 'array',
        'last_received_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
