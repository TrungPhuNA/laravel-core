<?php

namespace Modules\CheatSheet\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

final class CheatSheetResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'body' => $this->body,
            'visibility' => $this->visibility,
            'published_at' => optional($this->published_at)->toISOString(),
            'tags' => CheatSheetTagResource::collection($this->whenLoaded('tags')),
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
            'deleted_at' => optional($this->deleted_at)->toISOString(),
        ];
    }
}

