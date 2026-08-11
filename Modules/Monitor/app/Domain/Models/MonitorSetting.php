<?php

namespace Modules\Monitor\Domain\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $key
 * @property string|null $value
 */
final class MonitorSetting extends Model
{
    protected $table = 'dmn_settings';

    protected $fillable = [
        'key',
        'value',
    ];
}