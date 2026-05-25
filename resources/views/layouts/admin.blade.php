<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - School Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold fs-4" href="/admin/dashboard">
                <i class="fas fa-graduation-cap me-2"></i>School Admin
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="/admin/classrooms"><i class="fas fa-door-open me-1"></i>Classrooms</a>
                <a class="nav-link" href="/admin/subjects"><i class="fas fa-book me-1"></i>Subjects</a>
                <a class="nav-link" href="/admin/schedules"><i class="fas fa-calendar-alt me-1"></i>Schedules</a>
                <a class="nav-link" href="/admin/students"><i class="fas fa-user-graduate me-1"></i>Students</a>
                <a class="nav-link" href="/admin/professors"><i class="fas fa-chalkboard-teacher me-1"></i>Manage Professors</a>
                <a class="nav-link" href="/admin/enrollments"><i class="fas fa-clipboard-list me-1"></i>Enrollments</a>
                <a class="nav-link" href="/admin/attendance"><i class="fas fa-calendar-check me-1"></i>Attendance</a>
            </div>
        </div>
    </nav>

    <main class="container my-5">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>