@extends('admin.layouts.app')
@section('title','Categories')
@section('top-actions')<a class="btn" href="{{ route('admin.categories.create') }}">Add category</a>@endsection
@section('content')
<table>
    <tr><th>Name</th><th>Slug</th><th>Active</th><th>Sort</th><th>Actions</th></tr>
    @foreach($categories as $category)
        <tr><td>{{ $category->name }}</td><td>{{ $category->slug }}</td><td>{{ $category->is_active ? 'Yes' : 'No' }}</td><td>{{ $category->sort_order }}</td><td><a class="btn light" href="{{ route('admin.categories.edit',$category) }}">Edit</a></td></tr>
    @endforeach
</table>
{{ $categories->links('admin.partials.pagination') }}
@endsection
