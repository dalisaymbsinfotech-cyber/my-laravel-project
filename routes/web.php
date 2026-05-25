<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\EnrollmentController;
use App\Http\Controllers\Admin\ProfessorController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\CollegeController;
use App\Http\Controllers\Admin\ClassroomController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Professor\ProfessorPanelController;
use App\Http\Controllers\FaceController;

// ── Public: Room Select (default landing page) ────────────────────────────────
Route::get('/', function () {
    $classrooms = \App\Models\Classroom::orderBy('building')->orderBy('room_name')->get();
    return view('room-select', compact('classrooms'));
})->name('room.select');

// ── Public: Face Recognition Kiosk ───────────────────────────────────────────
Route::get('/face/{classroom_id}', function ($classroom_id) {
    $classroom = \App\Models\Classroom::findOrFail($classroom_id);
    return view('face', compact('classroom'));
})->name('face');

// Face API endpoints (called by face.blade.php via JS)
Route::post('/register-face',  [FaceController::class, 'register']);
Route::get('/get-faces',       [FaceController::class, 'getFaces']);
Route::post('/log-attendance', [FaceController::class, 'logAttendance']);

// Current class API (used by face kiosk to detect active schedule)
Route::get('/room/{classroom_id}/current-class', function ($classroom_id) {
    $classroom = \App\Models\Classroom::findOrFail($classroom_id);
    $today     = \Carbon\Carbon::now()->format('l'); // e.g. "Monday"
    $nowTime   = \Carbon\Carbon::now()->format('H:i:s');
    $now       = \Carbon\Carbon::now();

    // Match schedule by room_name (schedules store room as a string)
    $schedule = \App\Models\Schedule::where('room', $classroom->room_name)
        ->where('day', $today)
        ->whereTime('time_in', '<=', $nowTime)
        ->whereTime('time_out', '>=', $nowTime)
        ->first();

    if (!$schedule) {
        return response()->json(['error' => 'No active class right now!']);
    }

    $subject = \App\Models\Subject::where('subject_code', $schedule->subject_code)->first();

    if (!$subject) {
        return response()->json(['error' => 'Schedule found but subject not configured!']);
    }

    // Late threshold: 15 minutes after class starts
    $startTime     = \Carbon\Carbon::parse($schedule->time_in);
    $lateThreshold = $startTime->copy()->addMinutes(15);
    $status        = $now->greaterThan($lateThreshold) ? 'late' : 'open';

    return response()->json([
        'schedule' => [
            'id'             => $schedule->id,
            'room'           => $schedule->room,
            'subject_code'   => $schedule->subject_code,
            'subject_name'   => $subject->subject_name,
            'professor_name' => $subject->professor_name,
            'day'            => $schedule->day,
            'time_in'        => \Carbon\Carbon::parse($schedule->time_in)->format('h:i A'),
            'time_out'       => \Carbon\Carbon::parse($schedule->time_out)->format('h:i A'),
        ],
        'status'         => $status,
        'late_threshold' => $lateThreshold->format('h:i A'),
    ]);
});

// ── Auth ──────────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',  [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// ── Admin Panel ───────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('admin')->name('admin.')->group(function () {

        Route::get('/subjects', [SubjectController::class, 'index'])->name('subjects');
        Route::post('/subjects', [SubjectController::class, 'store'])->name('subjects.store');
        Route::get('/subjects/{id}/edit', [SubjectController::class, 'edit'])->name('subjects.edit');
        Route::put('/subjects/{id}', [SubjectController::class, 'update'])->name('subjects.update');
        Route::delete('/subjects/{id}', [SubjectController::class, 'destroy'])->name('subjects.destroy');

        Route::get('/schedules', [ScheduleController::class, 'index'])->name('schedules');
        Route::post('/schedules', [ScheduleController::class, 'store'])->name('schedules.store');
        Route::get('/schedules/{id}/edit', [ScheduleController::class, 'edit'])->name('schedules.edit');
        Route::put('/schedules/{id}', [ScheduleController::class, 'update'])->name('schedules.update');
        Route::delete('/schedules/{id}', [ScheduleController::class, 'destroy'])->name('schedules.destroy');

        Route::get('/students', [StudentController::class, 'index'])->name('students');
        Route::post('/students', [StudentController::class, 'store'])->name('students.store');
        Route::delete('/students/{id}', [StudentController::class, 'destroy'])->name('students.destroy');
        Route::delete('/students/registration/{id}', [StudentController::class, 'destroyRegistration'])->name('students.registration.destroy');

        Route::get('/sections', [\App\Http\Controllers\Admin\SectionController::class, 'index'])->name('sections');
        Route::post('/sections', [\App\Http\Controllers\Admin\SectionController::class, 'store'])->name('sections.store');
        Route::put('/sections/{id}', [\App\Http\Controllers\Admin\SectionController::class, 'update'])->name('sections.update');
        Route::delete('/sections/{id}', [\App\Http\Controllers\Admin\SectionController::class, 'destroy'])->name('sections.destroy');

        Route::get('/enrollment', [EnrollmentController::class, 'index'])->name('enrollment');
        Route::post('/enrollment', [EnrollmentController::class, 'store'])->name('enrollment.store');
        Route::delete('/enrollment/{id}', [EnrollmentController::class, 'destroy'])->name('enrollment.destroy');

        Route::get('/professors', [ProfessorController::class, 'index'])->name('professors');
        Route::post('/professors', [ProfessorController::class, 'store'])->name('professors.store');
        Route::get('/professors/{id}/edit', [ProfessorController::class, 'edit'])->name('professors.edit');
        Route::put('/professors/{id}', [ProfessorController::class, 'update'])->name('professors.update');
        Route::delete('/professors/{id}', [ProfessorController::class, 'destroy'])->name('professors.destroy');

        Route::get('/attendance', function () {
            return view('admin.attendance');
        })->name('attendance');

        Route::get('/colleges', [CollegeController::class, 'index'])->name('colleges');
        Route::post('/colleges', [CollegeController::class, 'store'])->name('colleges.store');
        Route::put('/colleges/{id}', [CollegeController::class, 'update'])->name('colleges.update');
        Route::delete('/colleges/{id}', [CollegeController::class, 'destroy'])->name('colleges.destroy');

        Route::get('/departments', [DepartmentController::class, 'index'])->name('departments');
        Route::post('/departments', [DepartmentController::class, 'store'])->name('departments.store');
        Route::put('/departments/{id}', [DepartmentController::class, 'update'])->name('departments.update');
        Route::delete('/departments/{id}', [DepartmentController::class, 'destroy'])->name('departments.destroy');

        Route::get('/classrooms', [ClassroomController::class, 'index'])->name('classrooms');
        Route::get('/classrooms/create', [ClassroomController::class, 'create'])->name('classrooms.create');
        Route::post('/classrooms', [ClassroomController::class, 'store'])->name('classrooms.store');
        Route::get('/classrooms/{id}', [ClassroomController::class, 'show'])->name('classrooms.show');
        Route::get('/classrooms/{id}/edit', [ClassroomController::class, 'edit'])->name('classrooms.edit');
        Route::put('/classrooms/{id}', [ClassroomController::class, 'update'])->name('classrooms.update');
        Route::delete('/classrooms/{id}', [ClassroomController::class, 'destroy'])->name('classrooms.destroy');

        Route::get('/settings', [SettingController::class, 'index'])->name('settings');
        Route::post('/settings/system', [SettingController::class, 'updateSystem'])->name('settings.system');
        Route::post('/settings/credentials', [SettingController::class, 'updateCredentials'])->name('settings.credentials');
    });
});

// ── Professor Panel ───────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:professor'])->prefix('professor')->name('professor.')->group(function () {
    Route::get('/dashboard', [ProfessorPanelController::class, 'dashboard'])->name('dashboard');
    Route::get('/schedule',  [ProfessorPanelController::class, 'schedule'])->name('schedule');
    Route::get('/profile',   [ProfessorPanelController::class, 'profileEdit'])->name('profile');
    Route::put('/profile',   [ProfessorPanelController::class, 'profileUpdate'])->name('profile.update');
    Route::get('/students',  [ProfessorPanelController::class, 'students'])->name('students');
    Route::get('/attendance-report', [ProfessorPanelController::class, 'attendanceReport'])->name('attendance');
});