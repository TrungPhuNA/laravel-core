<?php

namespace Modules\Setting\Http\Resources\Api\V1\Rbac;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property \App\Models\User $resource
 */
final class UserRbacResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var \App\Models\User $user */
        $user = $this->resource;

        $roles = $user->roles ?? collect();
        $directPermissions = $user->permissions ?? collect();
        $all = method_exists($user, 'getAllPermissions') ? $user->getAllPermissions() : collect();

        return [
            'id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,

            'roles' => $roles->pluck('name')->values(),
            'direct_permissions' => $directPermissions->pluck('name')->values(),
            'all_permissions' => $all->pluck('name')->values(),
        ];
    }
}

