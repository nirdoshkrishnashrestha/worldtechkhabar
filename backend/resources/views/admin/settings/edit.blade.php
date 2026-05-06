@extends('admin.layouts.app')
@section('title','Settings')
@section('content')
<form class="card form-grid" method="post" action="{{ route('admin.settings.update') }}">
    @csrf
    @method('put')
    @foreach($keys as $key)
        @php($long = in_array($key, ['site_description','seo_description'], true))
        <div class="{{ $long ? 'full' : '' }}">
            <label>{{ str_replace('_',' ',ucwords($key,'_')) }}</label>
            @if($long)
                <textarea name="{{ $key }}">{{ old($key,$settings[$key]) }}</textarea>
            @else
                <input name="{{ $key }}" value="{{ old($key,$settings[$key]) }}">
            @endif
        </div>
    @endforeach
    <div class="full"><button class="btn">Save settings</button></div>
</form>
@endsection
