<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'original_url' => $this->original_url,
            'summary' => $this->summary,
            'content_excerpt' => $this->content_excerpt,
            'image_url' => $this->image_url,
            'author' => $this->author,
            'original_published_at' => $this->original_published_at?->toIso8601String(),
            'published_on_site_at' => $this->published_on_site_at?->toIso8601String(),
            'score' => $this->score,
            'ai_summary' => $this->ai_summary,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'tags' => $this->tags ?? [],
            'category' => new CategoryResource($this->whenLoaded('category')),
            'source' => new SourceResource($this->whenLoaded('source')),
        ];
    }
}
