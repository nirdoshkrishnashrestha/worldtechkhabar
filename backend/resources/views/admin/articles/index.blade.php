@extends('admin.layouts.app')
@section('title','Articles')
@section('content')
<form class="card form-grid" method="get" style="margin-bottom:16px">
    <div><label>Status</label><select name="status"><option value="">All</option>@foreach(['pending','published','rejected','ignored'] as $status)<option value="{{ $status }}" @selected(request('status')===$status)>{{ $status }}</option>@endforeach</select></div>
    <div><label>Source</label><select name="source_id"><option value="">All</option>@foreach($sources as $source)<option value="{{ $source->id }}" @selected(request('source_id')==$source->id)>{{ $source->name }}</option>@endforeach</select></div>
    <div><label>Category</label><select name="category_id"><option value="">All</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(request('category_id')==$category->id)>{{ $category->name }}</option>@endforeach</select></div>
    <div style="align-self:end"><button class="btn">Filter</button></div>
</form>
<table>
    <tr><th>Title</th><th>Status</th><th>Score</th><th>Source</th><th>Category</th><th>Actions</th></tr>
    @foreach($articles as $article)
        <tr>
            <td><strong>{{ $article->title }}</strong><div class="muted">{{ $article->original_published_at?->format('M j, Y H:i') }}</div></td>
            <td><span class="badge">{{ $article->status }}</span></td>
            <td>{{ $article->score }}</td>
            <td>{{ $article->source?->name }}</td>
            <td>{{ $article->category?->name }}</td>
            <td class="actions">
                <a class="btn light" href="{{ route('admin.articles.edit',$article) }}">Edit</a>
                <a class="btn light" href="{{ $article->original_url }}" target="_blank" rel="noopener">Original</a>
                <form method="post" action="{{ route('admin.articles.score',$article) }}">@csrf<button class="btn secondary">Score</button></form>
                <form method="post" action="{{ route('admin.articles.publish',$article) }}">@csrf<button class="btn">Publish</button></form>
                <form method="post" action="{{ route('admin.articles.status',[$article,'rejected']) }}">@csrf<button class="btn danger">Reject</button></form>
                <form method="post" action="{{ route('admin.articles.status',[$article,'ignored']) }}">@csrf<button class="btn secondary">Ignore</button></form>
            </td>
        </tr>
    @endforeach
</table>
{{ $articles->links('admin.partials.pagination') }}
@endsection
