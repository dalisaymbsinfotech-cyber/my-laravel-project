<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Enrollment;
use App\Models\Face;
use App\Models\Professor;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\Subject;
use Carbon\Carbon;
use Illuminate\Routing\Controller;

class DashboardController extends Controller
{
    private const SECTION_COLORS = ['#8B0000', '#FFD700', '#2980b9', '#27ae60', '#9b59b6', '#e67e22', '#1abc9c', '#34495e'];

    public function index()
    {
        $sectionRows = Enrollment::query()
            ->selectRaw('section, COUNT(*) as total')
            ->whereNotNull('section')
            ->where('section', '!=', '')
            ->groupBy('section')
            ->orderBy('section')
            ->get();

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

        foreach (
            Attendance::query()
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

        $now = now();
        $yearStart = $now->month >= 8 ? $now->year : $now->year - 1;
        $periodStart = Carbon::create($yearStart, 8, 1)->startOfDay();
        $periodEnd = $now->copy()->endOfMonth();

        $trendLabels = [];
        $trendData = [];
        for ($cursor = $periodStart->copy(); $cursor <= $periodEnd; $cursor->addMonth()) {
            $trendLabels[] = $cursor->format('M');
            $trendData[] = (int) Enrollment::query()
                ->whereYear('created_at', $cursor->year)
                ->whereMonth('created_at', $cursor->month)
                ->count();
        }

        $filterSections = Enrollment::query()
            ->whereNotNull('section')
            ->where('section', '!=', '')
            ->distinct()
            ->orderBy('section')
            ->pluck('section');

        return view('dashboard', [
            'totalStudents' => Student::count(),
            'totalFaces' => Face::count(),
            'totalProfessors' => Professor::count(),
            'totalRooms' => Classroom::count(),
            'totalSubjects' => Subject::count(),
            'totalEnrollments' => Enrollment::count(),
            'totalSchedules' => Schedule::count(),
            'recentEnrollments' => Enrollment::latest()->take(5)->get(),
            'rooms' => Classroom::latest()->take(5)->get(),
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
}
