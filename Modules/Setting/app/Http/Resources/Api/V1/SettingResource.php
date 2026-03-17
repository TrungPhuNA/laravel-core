<?php

namespace Modules\Setting\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

final class SettingResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'key' => $this->key,
            'value' => $this->value,
            'group' => $this->group,
            'is_public' => $this->is_public,
            'description' => $this->description,
            'updated_by' => $this->updated_by,
            'updated_at' => optional($this->updated_at)->toISOString(),
        ];
    }
}

