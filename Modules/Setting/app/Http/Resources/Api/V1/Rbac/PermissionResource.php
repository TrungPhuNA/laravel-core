<?php

namespace Modules\Setting\Http\Resources\Api\V1\Rbac;

use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\Permission\Models\Permission;

/**
 * @property Permission $resource
 */
final class PermissionResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var Permission $p */
        $p = $this->resource;

        return [
            'id' => $p->id,
            'name' => $p->name,
            'guard_name' => $p->guard_name,
            'created_at' => $p->created_at?->toISOString(),
            'updated_at' => $p->updated_at?->toISOString(),
        ];
    }
}

