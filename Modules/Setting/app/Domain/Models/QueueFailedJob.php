<?php

namespace Modules\Setting\Domain\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model cho bảng `failed_jobs`.
 */
final class QueueFailedJob extends Model
{
    protected $table = 'failed_jobs';

    public $timestamps = false;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'failed_at' => 'datetime',
        ];
    }
}

