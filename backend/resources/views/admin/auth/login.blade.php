<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login - World Tech Khabar</title>
    <style>body{margin:0;font-family:Inter,Arial,sans-serif;background:#0f172a;display:grid;place-items:center;min-height:100vh}.card{width:min(420px,92vw);background:#fff;border-radius:8px;padding:28px}input{width:100%;box-sizing:border-box;margin:8px 0 16px;padding:12px;border:1px solid #cbd5e1;border-radius:6px}.btn{width:100%;border:0;background:#0d63ce;color:#fff;border-radius:6px;padding:12px;font-weight:800}.error{background:#fee2e2;color:#991b1b;padding:10px;border-radius:6px;margin-bottom:12px}</style>
</head>
<body>
<form class="card" method="post" action="{{ route('admin.login.store') }}">
    @csrf
    <h1>World Tech Khabar</h1>
    <p>Admin dashboard login</p>
    @if($errors->any())<div class="error">{{ $errors->first() }}</div>@endif
    <label>Email</label>
    <input type="email" name="email" value="{{ old('email') }}" required autofocus>
    <label>Password</label>
    <input type="password" name="password" required>
    <label style="display:flex;gap:8px;align-items:center"><input style="width:auto;margin:0" type="checkbox" name="remember" value="1"> Remember me</label>
    <button class="btn">Login</button>
</form>
</body>
</html>
