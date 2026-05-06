<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SourceResource;
use App\Models\Source;

class SourceController extends Controller
{
    public function index()
    {
        return SourceResource::collection(
            Source::query()->where('is_active', true)->with('category')->orderByDesc('is_high_priority')->orderBy('name')->get()
        );
    }
}
