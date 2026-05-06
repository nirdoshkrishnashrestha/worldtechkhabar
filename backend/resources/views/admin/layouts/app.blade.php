<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') - World Tech Khabar</title>
    <style>
        body{margin:0;font-family:Inter,Arial,sans-serif;background:#f4f7fb;color:#172033}
        a{color:#0d63ce;text-decoration:none}.wrap{display:flex;min-height:100vh}.side{width:240px;background:#0f172a;color:#fff;padding:24px}.side a{display:block;color:#dbeafe;padding:10px 0}.brand{font-weight:800;font-size:20px;margin-bottom:24px}.main{flex:1;padding:28px}.top{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px}
        .card{background:#fff;border:1px solid #e5e7eb;border-radius:8px;padding:18px;box-shadow:0 1px 2px #00000008}.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px}.num{font-size:30px;font-weight:800}
        table{width:100%;border-collapse:collapse;background:#fff;border-radius:8px;overflow:hidden}th,td{padding:12px;border-bottom:1px solid #e5e7eb;text-align:left;vertical-align:top}th{background:#f8fafc;font-size:13px;text-transform:uppercase;color:#64748b}
        input,select,textarea{width:100%;box-sizing:border-box;border:1px solid #cbd5e1;border-radius:6px;padding:10px;background:#fff}textarea{min-height:120px}.form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}.full{grid-column:1/-1}
        .btn{display:inline-flex;align-items:center;border:0;border-radius:6px;background:#0d63ce;color:#fff;padding:9px 13px;cursor:pointer;font-weight:700}.btn.secondary{background:#334155}.btn.danger{background:#dc2626}.btn.light{background:#e2e8f0;color:#172033}.actions{display:flex;gap:8px;flex-wrap:wrap}.notice{background:#dcfce7;color:#166534;padding:12px 14px;border-radius:8px;margin-bottom:16px}.error{background:#fee2e2;color:#991b1b;padding:12px 14px;border-radius:8px;margin-bottom:16px}.muted{color:#64748b;font-size:13px}.badge{display:inline-block;border-radius:99px;padding:3px 8px;background:#e0f2fe;color:#075985;font-size:12px;font-weight:700}
        .pagination{display:flex;align-items:center;justify-content:space-between;gap:16px;margin-top:18px;background:#fff;border:1px solid #e5e7eb;border-radius:8px;padding:12px 14px}.pagination-info{color:#64748b;font-size:13px}.pagination-links{display:flex;align-items:center;gap:6px;flex-wrap:wrap}.page-link,.page-current,.page-disabled{display:inline-flex;min-width:34px;height:34px;align-items:center;justify-content:center;border-radius:6px;padding:0 10px;font-size:13px;font-weight:800}.page-link{background:#f8fafc;border:1px solid #dbe3ee;color:#0d63ce}.page-link:hover{background:#0d63ce;color:#fff}.page-current{background:#0d63ce;color:#fff;border:1px solid #0d63ce}.page-disabled{background:#f1f5f9;color:#94a3b8;border:1px solid #e2e8f0}
        @media(max-width:800px){.wrap{display:block}.side{width:auto}.form-grid{grid-template-columns:1fr}.main{padding:16px}}
    </style>
</head>
<body>
<div class="wrap">
    <aside class="side">
        <div class="brand">World Tech Khabar</div>
        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
        <a href="{{ route('admin.articles.index') }}">Articles</a>
        <a href="{{ route('admin.sources.index') }}">Sources</a>
        <a href="{{ route('admin.categories.index') }}">Categories</a>
        <a href="{{ route('admin.settings.edit') }}">Settings</a>
        <form method="post" action="{{ route('admin.logout') }}" style="margin-top:24px">@csrf<button class="btn secondary">Logout</button></form>
    </aside>
    <main class="main">
        <div class="top">
            <div><h1>@yield('title', 'Admin')</h1></div>
            @yield('top-actions')
        </div>
        @if(session('status'))<div class="notice">{{ session('status') }}</div>@endif
        @if($errors->any())<div class="error">{{ $errors->first() }}</div>@endif
        @yield('content')
    </main>
</div>
</body>
</html>
