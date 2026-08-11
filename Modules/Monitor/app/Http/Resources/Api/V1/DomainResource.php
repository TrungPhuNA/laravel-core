<?php

namespace Modules\Monitor\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property \Modules\Monitor\Domain\Models\Domain $resource
 */
final class DomainResource extends JsonResource
{
    public function toArray($request): array
    {
        $d = $this->resource;

        return [
            'id' => $d->id,
            'domain' => $d->domain,
            'note' => $d->note,
            'is_active' => (bool) $d->is_active,
            'expires_at' => $d->expires_at?->toISOString(),
            'registrar' => $d->registrar,
            'nameservers' => $d->nameservers ?? [],
            'check_status' => $d->check_status,
            'last_check_at' => $d->last_check_at?->toISOString(),
            'last_check_error' => $d->last_check_error,
            'days_remaining' => $d->daysRemaining(),
            'badge' => $d->badge(),
            'created_at' => $d->created_at?->toISOString(),
            'updated_at' => $d->updated_at?->toISOString(),
        ];
    }
}