<?php

namespace Modules\Setting\Http\Resources\Api\V1\Queue;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

/**
 * @property \Modules\Setting\Domain\Models\QueueJob $resource
 */
final class QueueJobResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        $job = $this->resource;
        $now = time();

        $status = 'pending';
        if ($job->reserved_at !== null) {
            $status = 'reserved';
        } elseif ($job->available_at > $now) {
            $status = 'delayed';
        }

        $payload = $this->safeJsonDecode((string) $job->payload);

        return [
            'id' => $job->id,
            'queue' => $job->queue,
            'attempts' => (int) $job->attempts,
            'status' => $status,

            'reserved_at' => $this->ts($job->reserved_at),
            'available_at' => $this->ts($job->available_at),
            'created_at' => $this->ts($job->created_at),

            'display_name' => $payload['displayName'] ?? null,
            'job' => $payload['job'] ?? null,
            'payload_preview' => $this->truncate((string) $job->payload, 500),
        ];
    }

    private function ts(?int $value): ?string
    {
        if (!$value) {
            return null;
        }

        return Carbon::createFromTimestamp($value)->toISOString();
    }

    /**
     * @return array<string, mixed>
     */
    private function safeJsonDecode(string $value): array
    {
        $decoded = json_decode($value, true);

        return is_array($decoded) ? $decoded : [];
    }

    private function truncate(string $value, int $max): string
    {
        if (strlen($value) <= $max) {
            return $value;
        }

        return substr($value, 0, $max).'...';
    }
}

