<?php

namespace Modules\CheatSheet\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

final class PublicCheatSheetResource extends JsonResource
{
    public function toArray($request): array
    {
        $author = $this->author;

        return [
            'id' => $this->id,
            'title' => $this->title,
            'body' => $this->body,
            'published_at' => optional($this->published_at)->toISOString(),
            'tags' => CheatSheetTagResource::collection($this->whenLoaded('tags')),
            'author' => $author ? [
                'id' => $author->id,
                'name' => $author->name,
            ] : null,
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
        ];
    }
}

