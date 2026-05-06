<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FetchLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_id',
        'status',
        'message',
        'items_found',
        'items_created',
        'fetched_at',
    ];

    protected function casts(): array
    {
        return [
            'source_id' => 'integer',
            'items_found' => 'integer',
            'items_created' => 'integer',
            'fetched_at' => 'datetime',
        ];
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class);
    }
}
