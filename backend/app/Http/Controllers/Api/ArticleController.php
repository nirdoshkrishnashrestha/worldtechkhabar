<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $articles = $this->publishedQuery()
            ->when($request->filled('category'), fn (Builder $query) => $query->whereHas('category', fn (Builder $category) => $category->where('slug', (string) $request->string('category'))))
            ->when($request->filled('source'), fn (Builder $query) => $query->whereHas('source', fn (Builder $source) => $source->where('slug', (string) $request->string('source'))))
            ->when($request->filled('search'), fn (Builder $query) => $this->applySearch($query, (string) $request->string('search')))
            ->when($request->filled('tag'), fn (Builder $query) => $query->where('tags', 'like', '%'.(string) $request->string('tag').'%'))
            ->latest('published_on_site_at')
            ->paginate(12)
            ->withQueryString();

        return ArticleResource::collection($articles);
    }

    public function show(string $slug)
    {
        $article = $this->publishedQuery()->where('slug', $slug)->firstOrFail();

        return new ArticleResource($article);
    }

    public function latest()
    {
        return ArticleResource::collection(
            $this->publishedQuery()->latest('published_on_site_at')->limit(10)->get()
        );
    }

    public function trending()
    {
        return ArticleResource::collection(
            $this->publishedQuery()->orderByDesc('score')->latest('published_on_site_at')->limit(10)->get()
        );
    }

    public function search(Request $request)
    {
        $request->validate(['q' => ['nullable', 'string', 'max:120']]);
        $query = (string) $request->string('q');

        return ArticleResource::collection(
            $this->publishedQuery()
                ->when($query !== '', fn (Builder $builder) => $this->applySearch($builder, $query))
                ->latest('published_on_site_at')
                ->paginate(12)
                ->withQueryString()
        );
    }

    private function publishedQuery(): Builder
    {
        return Article::query()->published()->with(['category', 'source']);
    }

    private function applySearch(Builder $query, string $term): Builder
    {
        return $query->where(function (Builder $builder) use ($term): void {
            $builder->where('title', 'like', "%{$term}%")
                ->orWhere('summary', 'like', "%{$term}%")
                ->orWhere('content_excerpt', 'like', "%{$term}%")
                ->orWhere('tags', 'like', "%{$term}%");
        });
    }
}
