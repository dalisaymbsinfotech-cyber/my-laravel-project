@extends('layouts.professor')
@section('title', 'Students')
@section('content')
<div class="page-header">
    <h2>🎓 Students</h2>
    <p>Enrollments for your assigned subjects.</p>
</div>

@if(empty($codes))
<div class="alert-error">No subjects linked to your account.</div>
@else
<div class="card">
    <h3>Search & filter</h3>
    <form method="GET" action="{{ route('professor.students') }}" style="display:flex; flex-wrap:wrap; gap:10px; align-items:flex-end;">
        <div>
            <label style="display:block; font-size:12px; color:#666; margin-bottom:4px;">Name or student ID</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." style="margin:0; min-width:200px;" />
        </div>
        <div>
            <label style="display:block; font-size:12px; color:#666; margin-bottom:4px;">Section</label>
            <select name="section" style="margin:0; min-width:140px;">
                <option value="">All sections</option>
                @foreach($sections as $sec)
                    <option value="{{ $sec }}" {{ request('section') === $sec ? 'selected' : '' }}>{{ $sec }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary" style="margin:0;">Apply</button>
        <a href="{{ route('professor.students') }}" class="btn btn-red" style="margin:0;">Reset</a>
    </form>
</div>

<div class="card">
    <h3>Enrollment list</h3>
    <table>
        <thead>
            <tr>
                <th>Student</th>
                <th>Student ID</th>
                <th>Subject</th>
                <th>Section</th>
            </tr>
        </thead>
        <tbody>
            @forelse($enrollments as $row)
            <tr>
                <td>{{ $row->student_name }}</td>
                <td>{{ $row->student_id }}</td>
                <td>{{ $row->subject_code }}</td>
                <td>{{ $row->section }}</td>
            </tr>
            @empty
            <tr><td colspan="4" style="text-align:center; color:#aaa; padding:20px;">No matching enrollments.</td></tr>
            @endforelse
        </tbody>
    </table>

    @if($enrollments->hasPages())
    <div class="pagination-nav">
        @if($enrollments->onFirstPage())
        @else
        <a href="{{ $enrollments->previousPageUrl() }}">« Previous</a>
        @endif
        <span>Page {{ $enrollments->currentPage() }} of {{ $enrollments->lastPage() }}</span>
        @if($enrollments->hasMorePages())
        <a href="{{ $enrollments->nextPageUrl() }}">Next »</a>
        @endif
    </div>
    @endif
</div>
@endif
@endsection
