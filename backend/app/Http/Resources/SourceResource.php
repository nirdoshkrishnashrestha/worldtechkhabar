<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SourceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'official_url' => $this->official_url,
            'source_type' => $this->source_type,
            'trust_level' => $this->trust_level,
            'is_high_priority' => $this->is_high_priority,
            'category' => new CategoryResource($this->whenLoaded('category')),
        ];
    }
}
