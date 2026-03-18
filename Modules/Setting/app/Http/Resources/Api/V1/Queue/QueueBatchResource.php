<?php

namespace Modules\Setting\Http\Resources\Api\V1\Queue;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

/**
 * @property \Modules\Setting\Domain\Models\QueueJobBatch $resource
 */
final class QueueBatchResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        $batch = $this->resource;

        return [
            'id' => $batch->id,
            'name' => $batch->name,
            'total_jobs' => (int) $batch->total_jobs,
            'pending_jobs' => (int) $batch->pending_jobs,
            'failed_jobs' => (int) $batch->failed_jobs,
            'cancelled_at' => $this->ts($batch->cancelled_at),
            'created_at' => $this->ts($batch->created_at),
            'finished_at' => $this->ts($batch->finished_at),
        ];
    }

    private function ts(?int $value): ?string
    {
        if (!$value) {
            return null;
        }

        return Carbon::createFromTimestamp($value)->toISOString();
    }
}

