@extends('admin.layouts.app')
@section('title','Dashboard')
@section('content')
<div class="grid">
    <div class="card"><div class="muted">Total articles</div><div class="num">{{ $totalArticles }}</div></div>
    <div class="card"><div class="muted">Published</div><div class="num">{{ $publishedArticles }}</div></div>
    <div class="card"><div class="muted">Pending automation</div><div class="num">{{ $pendingArticles }}</div></div>
    <div class="card"><div class="muted">Active sources</div><div class="num">{{ $activeSources }}</div></div>
    <div class="card"><div class="muted">Fetched today</div><div class="num">{{ $fetchedToday }}</div></div>
    <div class="card"><div class="muted">Last fetch</div><strong>{{ $lastFetch?->status ?? 'None' }}</strong><p>{{ $lastFetch?->source?->name }}</p></div>
</div>
<h2>Recent articles</h2>
<table>
    <tr><th>Title</th><th>Status</th><th>Score</th><th>Source</th><th>Actions</th></tr>
    @foreach($recentArticles as $article)
        <tr>
            <td>{{ $article->title }}</td><td><span class="badge">{{ $article->status }}</span></td><td>{{ $article->score }}</td><td>{{ $article->source?->name }}</td>
            <td><a href="{{ route('admin.articles.edit',$article) }}">Edit</a></td>
        </tr>
    @endforeach
</table>
@endsection
