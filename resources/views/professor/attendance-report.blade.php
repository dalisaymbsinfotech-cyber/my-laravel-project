@extends('layouts.professor')
@section('title', 'Attendance report')
@section('content')
<div class="page-header">
    <h2>📋 Attendance report</h2>
    <p>Student attendance for your subjects.</p>
</div>

@if(empty($codes))
<div class="alert-error">No subjects linked to your account.</div>
@else
<div class="card">
    <h3>Filters</h3>
    <form method="GET" action="{{ route('professor.attendance') }}" style="display:flex; flex-wrap:wrap; gap:10px; align-items:flex-end;">
        <div>
            <label style="display:block; font-size:12px; color:#666; margin-bottom:4px;">Subject</label>
            <select name="subject_code" style="margin:0; min-width:180px;">
                <option value="">All your subjects</option>
                @foreach($subjectFilter as $sub)
                    <option value="{{ $sub->subject_code }}" {{ request('subject_code') === $sub->subject_code ? 'selected' : '' }}>{{ $sub->subject_code }} — {{ $sub->subject_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label style="display:block; font-size:12px; color:#666; margin-bottom:4px;">From</label>
            <input type="date" name="from" value="{{ request('from') }}" style="margin:0;" />
        </div>
        <div>
            <label style="display:block; font-size:12px; color:#666; margin-bottom:4px;">To</label>
            <input type="date" name="to" value="{{ request('to') }}" style="margin:0;" />
        </div>
        <button type="submit" class="btn btn-primary" style="margin:0;">Apply</button>
        <a href="{{ route('professor.attendance') }}" class="btn btn-red" style="margin:0;">Reset</a>
    </form>
</div>

<div class="card">
    <h3>Records</h3>
    <table>
        <thead>
            <tr>
                <th>Student</th>
                <th>Section</th>
                <th>Subject</th>
                <th>Date</th>
                <th>Time in</th>
                <th>Time out</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $rec)
            @php
                $student = $rec->student;
                $subject = $rec->subject;
                $tin = $rec->time_in;
                $tout = $rec->time_out;
                if ($tin instanceof \Carbon\CarbonInterface) { $tin = $tin->format('H:i'); } elseif (is_string($tin) && strlen($tin) >= 5) { $tin = substr($tin, 0, 5); }
                if ($tout instanceof \Carbon\CarbonInterface) { $tout = $tout->format('H:i'); } elseif (is_string($tout) && strlen($tout) >= 5) { $tout = substr($tout, 0, 5); }
            @endphp
            <tr>
                <td>{{ $student?->name ?? '—' }}</td>
                <td>{{ $student?->section ?? '—' }}</td>
                <td>{{ $subject?->subject_code ?? '—' }} @if($subject) <small style="color:#888;">{{ $subject->subject_name }}</small> @endif</td>
                <td>{{ $rec->attendance_date?->format('Y-m-d') }}</td>
                <td>{{ $tin ?: '—' }}</td>
                <td>{{ $tout ?: '—' }}</td>
                <td>{{ ucfirst(str_replace('-', ' ', (string) $rec->status)) }}</td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align:center; color:#aaa; padding:20px;">No attendance records found.</td></tr>
            @endforelse
        </tbody>
    </table>

    @if($records->hasPages())
    <div class="pagination-nav">
        @if(!$records->onFirstPage())
        <a href="{{ $records->previousPageUrl() }}">« Previous</a>
        @endif
        <span>Page {{ $records->currentPage() }} of {{ $records->lastPage() }}</span>
        @if($records->hasMorePages())
        <a href="{{ $records->nextPageUrl() }}">Next »</a>
        @endif
    </div>
    @endif
</div>
@endif
@endsection
