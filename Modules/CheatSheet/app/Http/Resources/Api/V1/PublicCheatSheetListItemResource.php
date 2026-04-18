<?php

namespace Modules\CheatSheet\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

final class PublicCheatSheetListItemResource extends JsonResource
{
    public function toArray($request): array
    {
        $author = $this->author;

        return [
            'id' => $this->id,
            'title' => $this->title,
            'excerpt' => Str::limit((string) $this->body, 220),
            'published_at' => optional($this->published_at)->toISOString(),
            'tags' => CheatSheetTagResource::collection($this->whenLoaded('tags')),
            'author' => $author ? [
                'id' => $author->id,
                'name' => $author->name,
            ] : null,
            'updated_at' => optional($this->updated_at)->toISOString(),
        ];
    }
}

