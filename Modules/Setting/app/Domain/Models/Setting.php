<?php

namespace Modules\Setting\Domain\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

final class Setting extends Model
{
    protected $table = 'settings';

    protected $fillable = [
        'key',
        'value',
        'group',
        'is_public',
        'description',
        'updated_by',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    protected function value(): Attribute
    {
        return Attribute::make(
            get: static fn ($value) => $value === null ? null : json_decode((string) $value, true),
            set: static fn ($value) => $value === null ? null : json_encode($value),
        );
    }
}

