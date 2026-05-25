<!DOCTYPE html>
<html>
<head>
    <title>School Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; color: #1a1a1a; font-size: 1.1rem; }
        .navbar { background-color: #1a1a1a !important; border-bottom: 2px solid #8B0000; }
        .navbar-brand { color: white !important; font-weight: 700; font-size: 1.2rem; }
        .nav-link { color: #aaaaaa !important; font-size: 0.95rem; transition: color 0.2s; }
        .nav-link:hover { color: #ffffff !important; }
        .nav-link.active { color: #ffffff !important; border-bottom: 2px solid #8B0000; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="{{ route('dashboard') }}">
            <i class="fas fa-school"></i> SCHOOL SYSTEM
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="{{ route('dashboard') }}">🏠 Dashboard</a>
                <a class="nav-link" href="{{ route('admin.classrooms') }}">🚪 Classrooms</a>
                <a class="nav-link" href="{{ route('admin.subjects') }}">📚 Subjects</a>
                <a class="nav-link" href="{{ route('admin.schedules') }}">🗓️ Schedules</a>
                <a class="nav-link" href="{{ route('admin.students') }}">🎓 Students</a>
                <a class="nav-link" href="{{ route('admin.enrollment') }}">📝 Enrollment</a>
                <a class="nav-link" href="{{ route('admin.professors') }}">👨‍🏫 Manage Professors</a>
                <a class="nav-link" href="{{ route('admin.attendance') }}">📋 Attendance</a>
            </div>
        </div>
    </div>
</nav>

<div class="container mt-5">
    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>