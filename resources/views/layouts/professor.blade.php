<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ ($settings->company_name ?? 'EARIST') }} - @yield('title')</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
    :root { --red: #8B0000; --red-light: #a50000; --gold: #FFD700; --gold-light: #FFE44D; --white: #ffffff; --gray: #f5f5f5; --text: #333333; --sidebar-width: 260px; }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Poppins', Arial, sans-serif; background: var(--gray); color: var(--text); display: flex; min-height: 100vh; }
    .sidebar { width: var(--sidebar-width); background: #8B0000; min-height: 100vh; position: fixed; top: 0; left: 0; display: flex; flex-direction: column; z-index: 100; box-shadow: 2px 0 10px rgba(0,0,0,0.2); }
    .sidebar-header { padding: 20px 15px; border-bottom: 1px solid rgba(255,255,255,0.1); display: flex; align-items: center; gap: 10px; }
    .sidebar-header img { width: 45px; height: 45px; object-fit: cover; border-radius: 6px; }
    .sidebar-header h2 { color: white; font-size: 16px; font-weight: 700; line-height: 1.2; }
    .sidebar-header span { color: var(--gold); font-size: 10px; display: block; font-weight: 400; }
    .sidebar-nav { flex: 1; padding: 15px 0; overflow-y: auto; }
    .sidebar-nav .nav-label { color: rgba(255,255,255,0.4); font-size: 10px; font-weight: 600; letter-spacing: 1px; padding: 10px 20px 5px; text-transform: uppercase; }
    .sidebar-nav a { display: flex; align-items: center; gap: 12px; padding: 12px 20px; color: rgba(255,255,255,0.85); text-decoration: none; font-size: 14px; transition: all 0.2s; border-left: 3px solid transparent; }
    .sidebar-nav a:hover, .sidebar-nav a.active { background: rgba(255,255,255,0.08); color: white; border-left: 3px solid var(--gold); }
    .sidebar-nav a .icon { font-size: 18px; width: 24px; text-align: center; }
    .sidebar-footer { padding: 15px; border-top: 1px solid rgba(255,255,255,0.1); display: flex; flex-direction: column; gap: 8px; }
    .sidebar-footer form button { display: flex; align-items: center; gap: 10px; width: 100%; background: none; border: none; color: rgba(255,255,255,0.7); font-size: 14px; padding: 10px; border-radius: 6px; cursor: pointer; font-family: inherit; text-align: left; }
    .sidebar-footer form button:hover { background: rgba(255,255,255,0.1); color: white; }
    .main-content { margin-left: var(--sidebar-width); flex: 1; display: flex; flex-direction: column; min-height: 100vh; }
    .topbar { background: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border-bottom: 2px solid var(--gold); position: sticky; top: 0; z-index: 50; }
    .topbar h3 { color: var(--red); font-size: 18px; font-weight: 600; }
    .topbar-right { display: flex; align-items: center; gap: 15px; flex-wrap: wrap; }
    .topbar-right span { color: #666; font-size: 13px; }
    .hamburger { display: none; background: none; border: none; cursor: pointer; font-size: 22px; color: var(--red); }
    .container { padding: 25px 30px; flex: 1; }
    .card { background: white; border-radius: 10px; padding: 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 20px; }
    .card h3 { color: var(--red); margin-bottom: 15px; font-size: 16px; border-bottom: 2px solid var(--gold); padding-bottom: 8px; }
    .btn { padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; font-family: 'Poppins', sans-serif; font-weight: 500; transition: all 0.2s; text-decoration: none; display: inline-block; }
    .btn-primary { background: var(--red); color: white; }
    .btn-primary:hover { background: var(--red-light); }
    .btn-red { background: #c0392b; color: white; }
    input, select, textarea { padding: 10px 12px; margin: 5px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; font-family: 'Poppins', sans-serif; outline: none; }
    input:focus, select:focus, textarea:focus { border-color: var(--red); }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th { background: var(--red); color: white; padding: 10px; text-align: left; font-size: 13px; }
    td { padding: 10px; border-bottom: 1px solid #eee; font-size: 13px; }
    tr:hover { background: #f9f9f9; }
    .alert-success { background: #d4edda; color: #155724; padding: 10px 15px; border-radius: 6px; margin-bottom: 15px; border-left: 4px solid #28a745; }
    .alert-error { background: #f8d7da; color: #721c24; padding: 10px 15px; border-radius: 6px; margin-bottom: 15px; border-left: 4px solid #c0392b; }
    .page-header { margin-bottom: 20px; }
    .page-header h2 { color: var(--red); font-size: 22px; }
    .page-header p { color: #666; font-size: 14px; }
    .badge { padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
    .badge-green { background: #d4edda; color: #155724; }
    .pagination-nav { margin-top: 16px; display: flex; align-items: center; gap: 12px; flex-wrap: wrap; font-size: 14px; }
    .pagination-nav a { color: var(--red); font-weight: 600; }
    .badge { padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
    .badge-green { background: #d4edda; color: #155724; }
    @media (max-width: 768px) {
      .sidebar { transform: translateX(-100%); transition: transform 0.3s; }
      .sidebar.open { transform: translateX(0); }
      .main-content { margin-left: 0; }
      .hamburger { display: block; }
      .container { padding: 15px; }
    }
  </style>
  @yield('styles')
</head>
<body>
@php
    try { $settings = \App\Models\Setting::first(); } catch (\Exception $e) { $settings = null; }
@endphp
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        @if(auth()->user()?->profile_photo)
            <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}" alt="" />
        @elseif($settings && $settings->logo_path)
            <img src="{{ asset('storage/' . $settings->logo_path) }}" alt="Logo" />
        @else
            <span style="font-size:30px;">👨‍🏫</span>
        @endif
        <div>
            <h2>{{ $settings->company_name ?? 'EARIST' }}</h2>
            <span>Professor portal</span>
        </div>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-label">Menu</div>
        <a href="{{ route('professor.dashboard') }}" class="{{ request()->routeIs('professor.dashboard') ? 'active' : '' }}"><span class="icon">🏠</span> Dashboard</a>
        <a href="{{ route('professor.profile') }}" class="{{ request()->routeIs('professor.profile') ? 'active' : '' }}"><span class="icon">👤</span> My profile</a>
        <a href="{{ route('professor.schedule') }}" class="{{ request()->routeIs('professor.schedule') ? 'active' : '' }}"><span class="icon">🗓️</span> Schedule</a>
        <a href="{{ route('professor.students') }}" class="{{ request()->routeIs('professor.students') ? 'active' : '' }}"><span class="icon">🎓</span> Students</a>
        <a href="{{ route('professor.attendance') }}" class="{{ request()->routeIs('professor.attendance') ? 'active' : '' }}"><span class="icon">📋</span> Attendance report</a>
    </nav>
    <div class="sidebar-footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"><span>🚪</span> Logout</button>
        </form>
    </div>
</div>
<div class="main-content">
    <div class="topbar">
        <div style="display:flex; align-items:center; gap:15px;">
            <button class="hamburger" type="button" onclick="toggleSidebar()">☰</button>
            <h3>@yield('title')</h3>
        </div>
        <div class="topbar-right">
            <span>{{ auth()->user()->name }}</span>
            <span>{{ now()->format('l, F d, Y') }}</span>
        </div>
    </div>
    <div class="container">@yield('content')</div>
</div>
<div id="sidebarOverlay" onclick="toggleSidebar()" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:99;"></div>
<script>
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('open');
    document.getElementById('sidebarOverlay').style.display = document.getElementById('sidebar').classList.contains('open') ? 'block' : 'none';
}
</script>
@yield('scripts')
</body>
</html>
