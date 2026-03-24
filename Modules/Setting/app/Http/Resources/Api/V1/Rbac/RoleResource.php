<?php

namespace Modules\Setting\Http\Resources\Api\V1\Rbac;

use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\Permission\Models\Role;

/**
 * @property Role $resource
 */
final class RoleResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var Role $r */
        $r = $this->resource;

        return [
            'id' => $r->id,
            'name' => $r->name,
            'guard_name' => $r->guard_name,
            'permissions' => PermissionResource::collection($r->permissions ?? []),
            'created_at' => $r->created_at?->toISOString(),
            'updated_at' => $r->updated_at?->toISOString(),
        ];
    }
}

