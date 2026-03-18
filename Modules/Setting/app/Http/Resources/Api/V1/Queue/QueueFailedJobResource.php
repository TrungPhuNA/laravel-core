<?php

namespace Modules\Setting\Http\Resources\Api\V1\Queue;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property \Modules\Setting\Domain\Models\QueueFailedJob $resource
 */
final class QueueFailedJobResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        $job = $this->resource;

        return [
            'id' => $job->id,
            'uuid' => $job->uuid,
            'connection' => $job->connection,
            'queue' => $job->queue,
            'failed_at' => $job->failed_at?->toISOString(),
            'exception_preview' => $this->truncate((string) $job->exception, 800),
            'payload_preview' => $this->truncate((string) $job->payload, 500),
        ];
    }

    private function truncate(string $value, int $max): string
    {
        if (strlen($value) <= $max) {
            return $value;
        }

        return substr($value, 0, $max).'...';
    }
}

