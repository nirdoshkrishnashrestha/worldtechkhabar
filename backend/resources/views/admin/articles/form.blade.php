@extends('admin.layouts.app')
@section('title','Edit Article')
@section('content')
<form class="card form-grid" method="post" action="{{ route('admin.articles.update',$article) }}">
    @csrf
    @method('put')
    <div class="full"><label>Title</label><input name="title" value="{{ old('title',$article->title) }}" required></div>
    <div><label>Slug</label><input name="slug" value="{{ old('slug',$article->slug) }}" required></div>
    <div><label>Category</label><select name="category_id"><option value="">None</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(old('category_id',$article->category_id)==$category->id)>{{ $category->name }}</option>@endforeach</select></div>
    <div><label>Status</label><select name="status">@foreach(['pending','published','rejected','ignored'] as $status)<option value="{{ $status }}" @selected(old('status',$article->status)===$status)>{{ $status }}</option>@endforeach</select></div>
    <div><label>Score</label><input type="number" name="score" value="{{ old('score',$article->score) }}" min="0" max="100" required></div>
    <div><label>Author</label><input name="author" value="{{ old('author',$article->author) }}"></div>
    <div><label>Image URL</label><input name="image_url" value="{{ old('image_url',$article->image_url) }}"></div>
    <div class="full"><label>Summary</label><textarea name="summary">{{ old('summary',$article->summary) }}</textarea></div>
    <div class="full"><label>Excerpt</label><textarea name="content_excerpt">{{ old('content_excerpt',$article->content_excerpt) }}</textarea></div>
    <div class="full"><label>AI summary</label><textarea name="ai_summary">{{ old('ai_summary',$article->ai_summary) }}</textarea></div>
    <div><label>Meta title</label><input name="meta_title" value="{{ old('meta_title',$article->meta_title) }}"></div>
    <div><label>Tags, comma separated</label><input name="tags" value="{{ old('tags',implode(', ', $article->tags ?? [])) }}"></div>
    <div class="full"><label>Meta description</label><textarea name="meta_description">{{ old('meta_description',$article->meta_description) }}</textarea></div>
    <div class="full muted">Source: {{ $article->source?->name }} | <a href="{{ $article->original_url }}" target="_blank" rel="noopener">Open original URL</a></div>
    <div class="full actions"><button class="btn">Save article</button><a class="btn light" href="{{ route('admin.articles.index') }}">Cancel</a></div>
</form>
@endsection
