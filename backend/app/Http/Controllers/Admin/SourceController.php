<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SourceRequest;
use App\Models\Category;
use App\Models\Source;
use App\Services\NewsFetchService;
use Illuminate\Support\Str;

class SourceController extends Controller
{
    public function index()
    {
        return view('admin.sources.index', [
            'sources' => Source::query()->with('category')->latest()->paginate(20),
        ]);
    }

    public function create()
    {
        return view('admin.sources.form', ['source' => new Source(), 'categories' => $this->categories()]);
    }

    public function store(SourceRequest $request)
    {
        Source::query()->create($this->payload($request));

        return redirect()->route('admin.sources.index')->with('status', 'Source created.');
    }

    public function edit(Source $source)
    {
        return view('admin.sources.form', ['source' => $source, 'categories' => $this->categories()]);
    }

    public function update(SourceRequest $request, Source $source)
    {
        $source->update($this->payload($request));

        return redirect()->route('admin.sources.index')->with('status', 'Source updated.');
    }

    public function destroy(Source $source)
    {
        $source->delete();

        return redirect()->route('admin.sources.index')->with('status', 'Source deleted.');
    }

    public function test(Source $source, NewsFetchService $newsFetchService)
    {
        $result = $newsFetchService->fetchSource($source);

        return redirect()->route('admin.sources.index')->with('status', "{$source->name}: {$result['status']}, found {$result['found']}, created {$result['created']}.");
    }

    private function categories()
    {
        return Category::query()->orderBy('sort_order')->orderBy('name')->get();
    }

    private function payload(SourceRequest $request): array
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['slug']);
        $data['is_high_priority'] = $request->boolean('is_high_priority');
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
