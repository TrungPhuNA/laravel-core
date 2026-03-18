<?php

namespace Modules\Setting\Domain\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model cho bảng `job_batches`.
 */
final class QueueJobBatch extends Model
{
    protected $table = 'job_batches';

    public $timestamps = false;

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'total_jobs' => 'int',
            'pending_jobs' => 'int',
            'failed_jobs' => 'int',
            'failed_job_ids' => 'array',
            'options' => 'array',
            'cancelled_at' => 'int',
            'created_at' => 'int',
            'finished_at' => 'int',
        ];
    }
}

