@extends('admin.layouts.app')
@section('title', $category->exists ? 'Edit Category' : 'Add Category')
@section('content')
<form class="card form-grid" method="post" action="{{ $category->exists ? route('admin.categories.update',$category) : route('admin.categories.store') }}">
    @csrf
    @if($category->exists) @method('put') @endif
    <div><label>Name</label><input name="name" value="{{ old('name',$category->name) }}" required></div>
    <div><label>Slug</label><input name="slug" value="{{ old('slug',$category->slug) }}" required></div>
    <div><label>Sort order</label><input type="number" name="sort_order" value="{{ old('sort_order',$category->sort_order ?: 0) }}"></div>
    <label><input type="checkbox" name="is_active" value="1" @checked(old('is_active',$category->exists ? $category->is_active : true)) style="width:auto"> Active</label>
    <div class="full"><label>Description</label><textarea name="description">{{ old('description',$category->description) }}</textarea></div>
    <div class="full actions"><button class="btn">Save category</button><a class="btn light" href="{{ route('admin.categories.index') }}">Cancel</a></div>
</form>
@endsection
