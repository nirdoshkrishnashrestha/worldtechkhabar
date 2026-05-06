<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryRequest;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        return view('admin.categories.index', [
            'categories' => Category::query()->orderBy('sort_order')->orderBy('name')->paginate(30),
        ]);
    }

    public function create()
    {
        return view('admin.categories.form', ['category' => new Category()]);
    }

    public function store(CategoryRequest $request)
    {
        Category::query()->create($this->payload($request));

        return redirect()->route('admin.categories.index')->with('status', 'Category created.');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.form', compact('category'));
    }

    public function update(CategoryRequest $request, Category $category)
    {
        $category->update($this->payload($request));

        return redirect()->route('admin.categories.index')->with('status', 'Category updated.');
    }

    private function payload(CategoryRequest $request): array
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['slug']);
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
