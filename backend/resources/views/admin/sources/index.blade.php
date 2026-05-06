@extends('admin.layouts.app')
@section('title','Sources')
@section('top-actions')<a class="btn" href="{{ route('admin.sources.create') }}">Add source</a>@endsection
@section('content')
<table>
    <tr><th>Name</th><th>Type</th><th>Category</th><th>Trust</th><th>Status</th><th>Last fetch</th><th>Actions</th></tr>
    @foreach($sources as $source)
        <tr>
            <td><strong>{{ $source->name }}</strong><div class="muted">{{ $source->official_url }}</div></td>
            <td>{{ $source->source_type }}</td>
            <td>{{ $source->category?->name }}</td>
            <td>{{ $source->trust_level }} @if($source->is_high_priority)<span class="badge">priority</span>@endif</td>
            <td>{{ $source->is_active ? 'Active' : 'Disabled' }}</td>
            <td>{{ $source->last_fetch_status ?? 'Never' }}<div class="muted">{{ $source->last_fetch_error }}</div></td>
            <td class="actions">
                <a class="btn light" href="{{ route('admin.sources.edit',$source) }}">Edit</a>
                <form method="post" action="{{ route('admin.sources.test',$source) }}">@csrf<button class="btn secondary">Test fetch</button></form>
                <form method="post" action="{{ route('admin.sources.destroy',$source) }}">@csrf @method('delete')<button class="btn danger" onclick="return confirm('Delete this source?')">Delete</button></form>
            </td>
        </tr>
    @endforeach
</table>
{{ $sources->links('admin.partials.pagination') }}
@endsection
