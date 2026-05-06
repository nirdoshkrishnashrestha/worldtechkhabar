<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ArticleRequest;
use App\Models\Article;
use App\Models\Category;
use App\Models\Source;
use App\Services\ArticleContentPolicy;
use App\Services\NewsScoringService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.articles.index', [
            'articles' => Article::query()
                ->with(['source', 'category'])
                ->when($request->filled('status'), fn (Builder $query) => $query->where('status', $request->string('status')))
                ->when($request->filled('source_id'), fn (Builder $query) => $query->where('source_id', $request->integer('source_id')))
                ->when($request->filled('category_id'), fn (Builder $query) => $query->where('category_id', $request->integer('category_id')))
                ->latest()
                ->paginate(20)
                ->withQueryString(),
            'sources' => Source::query()->orderBy('name')->get(),
            'categories' => Category::query()->orderBy('name')->get(),
        ]);
    }

    public function edit(Article $article)
    {
        return view('admin.articles.form', [
            'article' => $article->load(['source', 'category']),
            'categories' => Category::query()->orderBy('name')->get(),
        ]);
    }

    public function update(ArticleRequest $request, Article $article, ArticleContentPolicy $contentPolicy)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['slug']);
        $data['tags'] = $this->tags($data['tags'] ?? '');
        if ($data['status'] === 'published' && ! $contentPolicy->hasPublishableText($data['content_excerpt'] ?? '')) {
            return back()
                ->withErrors(['content_excerpt' => 'Published articles must have at least 150 content words.'])
                ->withInput();
        }

        if ($data['status'] === 'published' && ! $article->published_on_site_at) {
            $data['published_on_site_at'] = now();
        }
        if ($data['status'] !== 'published') {
            $data['published_on_site_at'] = null;
        }

        $article->update($data);

        return redirect()->route('admin.articles.index')->with('status', 'Article updated.');
    }

    public function publish(Article $article, ArticleContentPolicy $contentPolicy)
    {
        if (! $contentPolicy->hasPublishableContent($article)) {
            return back()->withErrors(['content_excerpt' => 'Published articles must have at least 150 content words.']);
        }

        $article->forceFill(['status' => 'published', 'published_on_site_at' => now()])->save();

        return back()->with('status', 'Article published.');
    }

    public function status(Article $article, string $status)
    {
        abort_unless(in_array($status, ['pending', 'rejected', 'ignored'], true), 404);
        $article->forceFill(['status' => $status, 'published_on_site_at' => null])->save();

        return back()->with('status', 'Article moved to '.$status.'.');
    }

    public function score(Article $article, NewsScoringService $scoringService)
    {
        $article->forceFill(['score' => $scoringService->score($article)])->save();

        return back()->with('status', 'Article score refreshed.');
    }

    private function tags(string $value): array
    {
        return collect(explode(',', $value))
            ->map(fn ($tag) => trim($tag))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
