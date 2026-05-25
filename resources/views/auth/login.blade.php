<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Login</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root { --red: #8B0000; --gold: #FFD700; }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Poppins', sans-serif; min-height: 100vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #8B0000 0%, #4a0000 100%); padding: 20px; }
    .box { background: white; border-radius: 16px; padding: 2.5rem; width: 100%; max-width: 420px; box-shadow: 0 20px 60px rgba(0,0,0,0.35); border-top: 5px solid var(--gold); }
    h1 { color: var(--red); font-size: 1.5rem; margin-bottom: 0.35rem; text-align: center; }
    .sub { color: #666; font-size: 0.85rem; text-align: center; margin-bottom: 1.5rem; }
    .role-row { display: flex; gap: 10px; margin-bottom: 1.25rem; }
    .role-row label { flex: 1; cursor: pointer; }
    .role-row input { position: absolute; opacity: 0; }
    .role-pill { display: block; text-align: center; padding: 12px; border: 2px solid #ddd; border-radius: 10px; font-weight: 600; font-size: 0.9rem; color: #555; transition: 0.2s; }
    .role-row input:checked + .role-pill { border-color: var(--red); background: #fff5f5; color: var(--red); }
    label.field { display: block; font-size: 0.75rem; font-weight: 600; color: #555; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px; }
    input[type="email"], input[type="password"] { width: 100%; padding: 12px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; margin-bottom: 1rem; font-family: inherit; }
    input:focus { outline: none; border-color: var(--red); }
    .remember { display: flex; align-items: center; gap: 8px; font-size: 13px; color: #555; margin-bottom: 1.25rem; }
    button[type="submit"] { width: 100%; padding: 14px; background: var(--red); color: white; border: none; border-radius: 8px; font-size: 15px; font-weight: 600; cursor: pointer; font-family: inherit; }
    button[type="submit"]:hover { background: #a50000; }
    .err { background: #f8d7da; color: #721c24; padding: 10px 12px; border-radius: 8px; font-size: 13px; margin-bottom: 1rem; border-left: 4px solid #c0392b; }
  </style>
</head>
<body>
  <div class="box">
    <a href="{{ route('room.select') }}" style="display:inline-flex;align-items:center;gap:6px;color:#8B0000;font-size:0.82rem;font-weight:600;text-decoration:none;margin-bottom:1.1rem;opacity:0.8;transition:opacity 0.15s;" onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=0.8">
      &larr; Back to Room Select
    </a>
    <h1>Sign in</h1>
    <p class="sub">Attendance Management System</p>

    @if ($errors->any())
      <div class="err">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('login.attempt') }}">
      @csrf
      <div class="role-row">
        <label>
          <input type="radio" name="role" value="admin" {{ old('role', 'admin') === 'admin' ? 'checked' : '' }} required />
          <span class="role-pill">Admin login</span>
        </label>
        <label>
          <input type="radio" name="role" value="professor" {{ old('role') === 'professor' ? 'checked' : '' }} required />
          <span class="role-pill">Professor login</span>
        </label>
      </div>

      <label class="field" for="email">Email</label>
      <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" />

      <label class="field" for="password">Password</label>
      <input id="password" type="password" name="password" required autocomplete="current-password" />

      <label class="remember">
        <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }} />
        Remember me
      </label>

      <button type="submit">Continue</button>
    </form>
  </div>
</body>
</html>