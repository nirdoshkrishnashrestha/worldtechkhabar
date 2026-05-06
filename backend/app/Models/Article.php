<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_id',
        'category_id',
        'title',
        'slug',
        'original_url',
        'summary',
        'content_excerpt',
        'image_url',
        'author',
        'original_published_at',
        'fetched_at',
        'published_on_site_at',
        'hash',
        'status',
        'score',
        'ai_summary',
        'meta_title',
        'meta_description',
        'tags',
    ];

    protected function casts(): array
    {
        return [
            'source_id' => 'integer',
            'category_id' => 'integer',
            'original_published_at' => 'datetime',
            'fetched_at' => 'datetime',
            'published_on_site_at' => 'datetime',
            'score' => 'integer',
            'tags' => 'array',
        ];
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')->whereNotNull('published_on_site_at');
    }
}
