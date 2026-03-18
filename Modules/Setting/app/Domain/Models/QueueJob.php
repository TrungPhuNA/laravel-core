<?php

namespace Modules\Setting\Domain\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model cho bảng `jobs` (database queue).
 */
final class QueueJob extends Model
{
    protected $table = 'jobs';

    public $timestamps = false;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'attempts' => 'int',
            'reserved_at' => 'int',
            'available_at' => 'int',
            'created_at' => 'int',
        ];
    }
}

