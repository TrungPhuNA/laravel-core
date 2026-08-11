<?php

namespace Modules\Monitor\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property \Modules\Monitor\Domain\Models\DomainCheckLog $resource
 */
final class DomainCheckLogResource extends JsonResource
{
    public function toArray($request): array
    {
        $log = $this->resource;

        return [
            'id' => $log->id,
            'domain_id' => $log->domain_id,
            'status' => $log->status,
            'expires_at_found' => $log->expires_at_found?->toISOString(),
            'registrar' => $log->registrar,
            'method' => $log->method,
            'error_message' => $log->error_message,
            'raw_response' => $log->raw_response,
            'checked_at' => $log->checked_at?->toISOString(),
        ];
    }
}