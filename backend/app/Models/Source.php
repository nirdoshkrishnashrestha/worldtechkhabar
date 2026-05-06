<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Source extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'official_url',
        'feed_url',
        'source_type',
        'category_id',
        'trust_level',
        'is_high_priority',
        'is_active',
        'fetch_frequency_minutes',
        'last_fetched_at',
        'last_fetch_status',
        'last_fetch_error',
    ];

    protected function casts(): array
    {
        return [
            'category_id' => 'integer',
            'is_high_priority' => 'boolean',
            'is_active' => 'boolean',
            'fetch_frequency_minutes' => 'integer',
            'last_fetched_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function fetchLogs(): HasMany
    {
        return $this->hasMany(FetchLog::class);
    }

    public function scopeDueForFetch(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(function (Builder $builder): void {
                $builder->whereNull('last_fetched_at')
                    ->orWhereRaw('last_fetched_at <= DATE_SUB(NOW(), INTERVAL fetch_frequency_minutes MINUTE)');
            });
    }
}
