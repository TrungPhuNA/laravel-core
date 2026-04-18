<?php

namespace Modules\CheatSheet\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

final class CheatSheetTopicResource extends JsonResource
{
    public function toArray($request): array
    {
        $count = $this->cheat_sheets_count ?? $this->count ?? null;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'count' => is_numeric($count) ? (int) $count : 0,
        ];
    }
}

