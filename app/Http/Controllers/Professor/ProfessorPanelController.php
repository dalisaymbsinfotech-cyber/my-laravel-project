<?php

namespace App\Http\Controllers\Professor;

use Illuminate\Routing\Controller;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Enrollment;
use App\Models\Professor;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfessorPanelController extends Controller
{
    private const SECTION_COLORS = ['#8B0000', '#FFD700', '#2980b9', '#27ae60', '#9b59b6', '#e67e22', '#1abc9c', '#34495e'];

    private function subjectCodes(Request $request): array
    {
        $user = $request->user();
        if (! $user instanceof User) {
            return [];
        }

        return Professor::query()
            ->where('user_id', $user->id)
            ->pluck('subject_code')
            ->unique()
            ->values()
            ->all();
    }

    private function subjectIds(array $codes): array
    {
        if ($codes === []) {
            return [];
        }

        return Subject::query()->whereIn('subject_code', $codes)->pluck('id')->all();
    }

    public function dashboard(Request $request)
    {
        $codes = $this->subjectCodes($request);
        $ids = $this->subjectIds($codes);

        if ($codes === []) {
            $sectionRows = collect();
        } else {
            $sectionRows = Enrollment::query()
                ->selectRaw('section, COUNT(*) as total')
                ->whereNotNull('section')
                ->where('section', '!=', '')
                ->whereIn('subject_code', $codes)
                ->groupBy('section')
                ->orderBy('section')
                ->get();
        }

        $enrollmentBySectionEmpty = $sectionRows->isEmpty();

        if ($sectionRows->isEmpty()) {
            $sectionLabels = ['No enrollment data'];
            $sectionData = [1];
            $sectionColors = ['#dddddd'];
        } else {
            $sectionLabels = $sectionRows->pluck('section')->values()->all();
            $sectionData = $sectionRows->pluck('total')->map(fn ($n) => (int) $n)->values()->all();
            $sectionColors = collect($sectionLabels)->keys()
                ->map(fn ($i) => self::SECTION_COLORS[$i % count(self::SECTION_COLORS)])
                ->values()->all();
        }

        $weekStart = now()->copy()->startOfWeek(Carbon::MONDAY);
        $weekEnd = $weekStart->copy()->addDays(6)->endOfDay();

        $presentByDay = array_fill(0, 5, 0);
        $lateByDay = array_fill(0, 5, 0);
        $absentByDay = array_fill(0, 5, 0);

        if ($ids !== []) {
            foreach (
                Attendance::query()
                    ->whereIn('subject_id', $ids)
                    ->whereBetween('attendance_date', [$weekStart->toDateString(), $weekEnd->toDateString()])
                    ->get(['attendance_date', 'status']) as $rec
            ) {
                $d = $rec->attendance_date;
                $n = (int) $d->format('N');
                if ($n < 1 || $n > 5) {
                    continue;
                }
                $idx = $n - 1;
                $st = strtolower((string) $rec->status);
                if ($st === 'present' || $st === 'on-time') {
                    $presentByDay[$idx]++;
                } elseif ($st === 'late') {
                    $lateByDay[$idx]++;
                } elseif ($st === 'absent') {
                    $absentByDay[$idx]++;
                }
            }
        }

        $now = now();
        $yearStart = $now->month >= 8 ? $now->year : $now->year - 1;
        $periodStart = Carbon::create($yearStart, 8, 1)->startOfDay();
        $periodEnd = $now->copy()->endOfMonth();

        $trendLabels = [];
        $trendData = [];
        for ($cursor = $periodStart->copy(); $cursor <= $periodEnd; $cursor->addMonth()) {
            $trendLabels[] = $cursor->format('M');
            if ($codes === []) {
                $trendData[] = 0;
            } else {
                $trendData[] = (int) Enrollment::query()
                    ->whereYear('created_at', $cursor->year)
                    ->whereMonth('created_at', $cursor->month)
                    ->whereIn('subject_code', $codes)
                    ->count();
            }
        }

        $filterSections = $codes === []
            ? collect()
            : Enrollment::query()
                ->whereNotNull('section')
                ->where('section', '!=', '')
                ->whereIn('subject_code', $codes)
                ->distinct()
                ->orderBy('section')
                ->pluck('section');

        $studentQuery = Student::query();
        if ($codes !== []) {
            $idNumbers = Enrollment::query()->whereIn('subject_code', $codes)->pluck('student_id')->unique();
            $studentQuery->whereIn('id_number', $idNumbers);
        }

        return view('professor.dashboard', [
            'subjectCodes' => $codes,
            'totalStudents' => $codes === [] ? 0 : $studentQuery->count(),
            'totalSubjects' => count($codes),
            'totalEnrollments' => $codes === [] ? 0 : Enrollment::query()->whereIn('subject_code', $codes)->count(),
            'totalSchedules' => $codes === [] ? 0 : Schedule::query()->whereIn('subject_code', $codes)->count(),
            'totalClassrooms' => Classroom::count(),
            'totalProfAssignments' => Professor::query()->where('user_id', $request->user()->id)->count(),
            'recentEnrollments' => $codes === []
                ? collect()
                : Enrollment::query()->whereIn('subject_code', $codes)->latest()->take(5)->get(),
            'rooms' => Classroom::query()->latest()->take(5)->get(),
            'sectionLabels' => $sectionLabels,
            'sectionData' => $sectionData,
            'sectionColors' => $sectionColors,
            'enrollmentBySectionEmpty' => $enrollmentBySectionEmpty,
            'attendancePresent' => $presentByDay,
            'attendanceLate' => $lateByDay,
            'attendanceAbsent' => $absentByDay,
            'attendanceWeekLabel' => $weekStart->format('M j') . ' – ' . $weekEnd->format('M j, Y'),
            'trendLabels' => $trendLabels,
            'trendData' => $trendData,
            'filterSections' => $filterSections,
        ]);
    }

    public function schedule(Request $request)
    {
        $codes = $this->subjectCodes($request);
        $schedules = $codes === []
            ? collect()
            : Schedule::query()->whereIn('subject_code', $codes)->orderBy('day')->orderBy('time_in')->get();

        return view('professor.schedule', compact('schedules', 'codes'));
    }

    public function profileEdit(Request $request)
    {
        return view('professor.profile', ['user' => $request->user()]);
    }

    public function profileUpdate(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'employee_id' => 'nullable|string|max:100',
            'college' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',
        ]);

        $data = $request->only(['name', 'employee_id', 'college']);

        if ($request->hasFile('photo')) {
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $data['profile_photo'] = $request->file('photo')->store('professor-profiles', 'public');
        }

        $user->update($data);

        foreach (Professor::query()->where('user_id', $user->id)->get() as $row) {
            $row->update(['name' => $request->name]);
        }

        return redirect()->route('professor.profile')->with('success', 'Profile updated.');
    }

    public function students(Request $request)
    {
        $codes = $this->subjectCodes($request);
        $q = Enrollment::query()->orderBy('student_name');
        if ($codes !== []) {
            $q->whereIn('subject_code', $codes);
        } else {
            $q->whereRaw('1 = 0');
        }

        if ($request->filled('search')) {
            $s = '%' . $request->search . '%';
            $q->where(function ($qq) use ($s) {
                $qq->where('student_name', 'like', $s)->orWhere('student_id', 'like', $s);
            });
        }

        if ($request->filled('section')) {
            $q->where('section', $request->section);
        }

        $enrollments = $q->paginate(20)->withQueryString();

        $sections = $codes === []
            ? collect()
            : Enrollment::query()
                ->whereIn('subject_code', $codes)
                ->whereNotNull('section')
                ->distinct()
                ->orderBy('section')
                ->pluck('section');

        return view('professor.students', compact('enrollments', 'sections', 'codes'));
    }

    public function attendanceReport(Request $request)
    {
        $codes = $this->subjectCodes($request);
        $ids = $this->subjectIds($codes);

        $q = Attendance::query()
            ->with(['student', 'subject'])
            ->orderByDesc('attendance_date')
            ->orderByDesc('id');

        if ($ids === []) {
            $q->whereRaw('1 = 0');
        } else {
            $q->whereIn('subject_id', $ids);
        }

        if ($request->filled('subject_code')) {
            $sid = Subject::query()->where('subject_code', $request->subject_code)->value('id');
            if ($sid) {
                $q->where('subject_id', $sid);
            }
        }

        if ($request->filled('from')) {
            $q->whereDate('attendance_date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $q->whereDate('attendance_date', '<=', $request->to);
        }

        $records = $q->paginate(30)->withQueryString();

        $subjectFilter = $codes === []
            ? collect()
            : Subject::query()->whereIn('subject_code', $codes)->orderBy('subject_code')->get();

        return view('professor.attendance-report', compact('records', 'subjectFilter', 'codes'));
    }
}
