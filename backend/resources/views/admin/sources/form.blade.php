@extends('admin.layouts.app')
@section('title', $source->exists ? 'Edit Source' : 'Add Source')
@section('content')
<form class="card form-grid" method="post" action="{{ $source->exists ? route('admin.sources.update',$source) : route('admin.sources.store') }}">
    @csrf
    @if($source->exists) @method('put') @endif
    <div><label>Name</label><input name="name" value="{{ old('name',$source->name) }}" required></div>
    <div><label>Slug</label><input name="slug" value="{{ old('slug',$source->slug) }}" required></div>
    <div class="full"><label>Official URL</label><input name="official_url" value="{{ old('official_url',$source->official_url) }}" required></div>
    <div class="full"><label>Feed URL / API key value</label><input name="feed_url" value="{{ old('feed_url',$source->feed_url) }}"><p class="muted">RSS URL, arXiv category like cs.AI, or GitHub owner/repo.</p></div>
    <div><label>Type</label><select name="source_type">@foreach(['rss','api','webpage'] as $type)<option value="{{ $type }}" @selected(old('source_type',$source->source_type)===$type)>{{ $type }}</option>@endforeach</select></div>
    <div><label>Category</label><select name="category_id"><option value="">None</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(old('category_id',$source->category_id)==$category->id)>{{ $category->name }}</option>@endforeach</select></div>
    <div><label>Trust level</label><select name="trust_level">@foreach(['official','government','research','open_source','company'] as $trust)<option value="{{ $trust }}" @selected(old('trust_level',$source->trust_level)===$trust)>{{ $trust }}</option>@endforeach</select></div>
    <div><label>Fetch frequency minutes</label><input type="number" name="fetch_frequency_minutes" value="{{ old('fetch_frequency_minutes',$source->fetch_frequency_minutes ?: 180) }}" min="15" required></div>
    <label><input type="checkbox" name="is_high_priority" value="1" @checked(old('is_high_priority',$source->is_high_priority)) style="width:auto"> High priority</label>
    <label><input type="checkbox" name="is_active" value="1" @checked(old('is_active',$source->exists ? $source->is_active : true)) style="width:auto"> Active</label>
    <div class="full actions"><button class="btn">Save source</button><a class="btn light" href="{{ route('admin.sources.index') }}">Cancel</a></div>
</form>
@endsection
