@extends('layouts.professor')

@section('title', 'Dashboard')

@section('styles')
<style>
  .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 12px; }
  .top-bar h2 { font-size: 22px; font-weight: 600; color: var(--red); }
  .top-bar p { font-size: 13px; color: #666; margin-top: 2px; }
  .search-bar { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
  .search-bar input { padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 13px; width: 200px; }
  .search-bar select { padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 13px; }
  .search-bar button { padding: 8px 16px; background: var(--red); color: white; border: none; border-radius: 6px; font-size: 13px; cursor: pointer; }
  .metric-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 12px; margin-bottom: 1.5rem; }
  .metric-card { background: #f9f9f9; border-radius: 8px; padding: 1rem; border: 1px solid #eee; }
  .metric-card .label { font-size: 12px; color: #888; margin-bottom: 6px; }
  .metric-card .value { font-size: 28px; font-weight: 600; color: var(--red); }
  .metric-card .change { font-size: 11px; margin-top: 4px; color: #27ae60; }
  .chart-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 1.5rem; }
  .chart-card { background: white; border: 1px solid #eee; border-radius: 10px; padding: 1.25rem; }
  .chart-card h3 { font-size: 14px; font-weight: 600; color: var(--red); margin-bottom: 4px; border-bottom: 2px solid var(--gold); padding-bottom: 8px; }
  .chart-card p { font-size: 12px; color: #888; margin-bottom: 1rem; margin-top: 6px; }
  .full-chart { background: white; border: 1px solid #eee; border-radius: 10px; padding: 1.25rem; margin-bottom: 1.5rem; }
  .full-chart h3 { font-size: 14px; font-weight: 600; color: var(--red); margin-bottom: 4px; border-bottom: 2px solid var(--gold); padding-bottom: 8px; }
  .full-chart p { font-size: 12px; color: #888; margin-bottom: 1rem; margin-top: 6px; }
  .legend { display: flex; flex-wrap: wrap; gap: 12px; margin-bottom: 8px; font-size: 12px; color: #666; }
  .legend span { display: flex; align-items: center; gap: 4px; }
  .legend-dot { width: 10px; height: 10px; border-radius: 2px; display: inline-block; }
  .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
  .info-card { background: white; border: 1px solid #eee; border-radius: 10px; padding: 1.25rem; }
  .info-card h3 { font-size: 14px; font-weight: 600; color: var(--red); margin-bottom: 1rem; border-bottom: 2px solid var(--gold); padding-bottom: 8px; }
  .info-row { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #f5f5f5; font-size: 13px; }
  .info-row:last-child { border-bottom: none; }
  .info-row .name { color: #333; font-weight: 500; }
  .info-row .meta { color: #888; font-size: 12px; }
  @media (max-width: 768px) {
    .chart-grid { grid-template-columns: 1fr; }
    .info-grid { grid-template-columns: 1fr; }
    .top-bar { flex-direction: column; align-items: flex-start; }
  }
</style>
@endsection

@section('content')

@if(empty($subjectCodes))
<div class="alert-error" style="margin-bottom:1rem;">No subjects are linked to your account yet. Ask an administrator to connect your professor assignments (Manage Professors) to your login email.</div>
@endif

<div class="top-bar">
    <div>
        <h2>My dashboard</h2>
        <p>Overview for your assigned subjects@unless(empty($subjectCodes)) ({{ implode(', ', $subjectCodes) }}).</p>
    </div>
    <div class="search-bar">
        <input type="text" placeholder="Search students..." id="searchInput" oninput="filterData()" />
        <select id="filterSelect" onchange="filterData()">
            <option value="">All sections</option>
            @foreach($filterSections ?? [] as $sec)
            <option value="{{ $sec }}">{{ $sec }}</option>
            @endforeach
        </select>
        <button type="button" onclick="filterData()">Search</button>
    </div>
</div>

<div class="metric-grid">
    <div class="metric-card">
        <div class="label">Students (your subjects)</div>
        <div class="value">{{ $totalStudents ?? 0 }}</div>
        <div class="change">Unique students in enrollments</div>
    </div>
    <div class="metric-card">
        <div class="label">Your subjects</div>
        <div class="value">{{ $totalSubjects ?? 0 }}</div>
        <div class="change">Distinct subject codes</div>
    </div>
    <div class="metric-card">
        <div class="label">Enrollments</div>
        <div class="value">{{ $totalEnrollments ?? 0 }}</div>
        <div class="change">Rows for your subjects</div>
    </div>
    <div class="metric-card">
        <div class="label">Schedules</div>
        <div class="value">{{ $totalSchedules ?? 0 }}</div>
        <div class="change">Class schedules for your codes</div>
    </div>
    <div class="metric-card">
        <div class="label">Classrooms</div>
        <div class="value">{{ $totalClassrooms ?? 0 }}</div>
        <div class="change">System total</div>
    </div>
    <div class="metric-card">
        <div class="label">Assignments</div>
        <div class="value">{{ $totalProfAssignments ?? 0 }}</div>
        <div class="change">Your teaching rows</div>
    </div>
</div>

<div class="chart-grid">
    <div class="chart-card">
        <h3>Enrollments per section</h3>
        <p>For your assigned subjects only</p>
        <div class="legend">
            @foreach(($sectionLabels ?? []) as $i => $label)
            <span><span class="legend-dot" style="background:{{ ($sectionColors ?? [])[$i] ?? '#ccc' }};"></span>{{ $label }}</span>
            @endforeach
        </div>
        <div style="position:relative; height:220px;">
            <canvas id="sectionChart"></canvas>
        </div>
    </div>
    <div class="chart-card">
        <h3>Attendance overview</h3>
        <p>Your subjects · Mon–Fri this week ({{ $attendanceWeekLabel ?? '' }})</p>
        <div class="legend">
            <span><span class="legend-dot" style="background:#8B0000;"></span>Present</span>
            <span><span class="legend-dot" style="background:#FFD700;"></span>Late</span>
            <span><span class="legend-dot" style="background:#e74c3c;"></span>Absent</span>
        </div>
        <div style="position:relative; height:220px;">
            <canvas id="attendChart"></canvas>
        </div>
    </div>
</div>

<div class="full-chart">
    <h3>Monthly enrollment trend</h3>
    <p>New enrollments per month for your subjects (Aug–{{ now()->format('M Y') }})</p>
    <div style="position:relative; height:200px;">
        <canvas id="trendChart"></canvas>
    </div>
</div>

<div class="info-grid">
    <div class="info-card">
        <h3>Recent enrollments</h3>
        <div id="enrollment-list">
            @forelse($recentEnrollments ?? [] as $enrollment)
            <div class="info-row">
                <div>
                    <div class="name">{{ $enrollment->student_name }}</div>
                    <div class="meta">{{ $enrollment->subject_code }} · {{ $enrollment->section }}</div>
                </div>
                <span class="badge badge-green">Enrolled</span>
            </div>
            @empty
            <div class="info-row">
                <div class="meta">No recent enrollments for your subjects.</div>
            </div>
            @endforelse
        </div>
    </div>
    <div class="info-card">
        <h3>Classroom availability</h3>
        @forelse($rooms ?? [] as $room)
        <div class="info-row">
            <div>
                <div class="name">{{ $room->room_name }}</div>
                <div class="meta">{{ $room->room_code }}@if($room->building) · {{ $room->building }}@endif</div>
            </div>
            <span class="badge badge-green">{{ ucfirst($room->status) }}</span>
        </div>
        @empty
        <div class="info-row">
            <div class="meta">No classrooms in the system.</div>
        </div>
        @endforelse
    </div>
</div>

@endsection

@section('scripts')
@php
    $emptyWeek = array(0, 0, 0, 0, 0);
@endphp
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
<script>
const sectionEmpty = @json($enrollmentBySectionEmpty ?? true);

new Chart(document.getElementById('sectionChart'), {
    type: 'doughnut',
    data: {
        labels: @json($sectionLabels ?? array()),
        datasets: [{ data: @json($sectionData ?? array()), backgroundColor: @json($sectionColors ?? array()), borderWidth: 2 }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: function(ctx) {
                        if (sectionEmpty) return ' No data for your subjects';
                        const v = ctx.raw;
                        const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                        const pct = total ? Math.round((v / total) * 100) : 0;
                        return ' ' + v + ' (' + pct + '%)';
                    }
                }
            }
        },
        cutout: '60%'
    }
});

new Chart(document.getElementById('attendChart'), {
    type: 'bar',
    data: {
        labels: ['Mon','Tue','Wed','Thu','Fri'],
        datasets: [
            { label: 'Present', data: @json($attendancePresent ?? $emptyWeek), backgroundColor: '#8B0000' },
            { label: 'Late', data: @json($attendanceLate ?? $emptyWeek), backgroundColor: '#FFD700' },
            { label: 'Absent', data: @json($attendanceAbsent ?? $emptyWeek), backgroundColor: '#e74c3c' }
        ]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: { stacked: true, ticks: { font: { size: 11 } } },
            y: { stacked: true, ticks: { font: { size: 11 } }, beginAtZero: true }
        }
    }
});

new Chart(document.getElementById('trendChart'), {
    type: 'line',
    data: {
        labels: @json($trendLabels ?? array()),
        datasets: [{
            label: 'Enrollments',
            data: @json($trendData ?? array()),
            borderColor: '#8B0000',
            backgroundColor: 'rgba(139,0,0,0.08)',
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#8B0000'
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: { ticks: { font: { size: 11 } } },
            y: { ticks: { font: { size: 11 } }, beginAtZero: true }
        }
    }
});

function filterData() {
    const q = document.getElementById('searchInput').value.toLowerCase();
    const sec = document.getElementById('filterSelect').value;
    const rows = document.querySelectorAll('#enrollment-list .info-row');
    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        const matchQ = q === '' || text.includes(q);
        const matchS = sec === '' || text.includes(sec.toLowerCase());
        row.style.display = matchQ && matchS ? '' : 'none';
    });
}
</script>
@endsection
