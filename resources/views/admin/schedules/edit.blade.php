@extends('layouts.app')
@section('title', 'Edit Schedule')
@section('content')
@php
    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    $timeIn = old('time_in', $schedule->time_in);
    $timeOut = old('time_out', $schedule->time_out);
    if (is_string($timeIn) && strlen($timeIn) >= 5) {
        $timeIn = substr($timeIn, 0, 5);
    }
    if (is_string($timeOut) && strlen($timeOut) >= 5) {
        $timeOut = substr($timeOut, 0, 5);
    }
@endphp
<div class="page-header">
    <h2>✏️ Edit Schedule</h2>
    <p>Update classroom, subject, day, and times</p>
</div>

@if ($errors->any())
    <div class="alert-error" style="margin-bottom:15px;">{{ $errors->first() }}</div>
@endif

<div class="card">
    <form method="POST" action="{{ route('admin.schedules.update', $schedule->id) }}">
        @csrf
        @method('PUT')
        <select name="room" required>
            <option value="">Select classroom</option>
            @if($rooms->where('room_name', $schedule->room)->isEmpty() && $schedule->room)
                <option value="{{ $schedule->room }}" {{ old('room', $schedule->room) === $schedule->room ? 'selected' : '' }}>{{ $schedule->room }} (current)</option>
            @endif
            @foreach($rooms as $room)
                <option value="{{ $room->room_name }}" {{ old('room', $schedule->room) === $room->room_name ? 'selected' : '' }}>{{ $room->room_name }}</option>
            @endforeach
        </select>
        <select name="subject_code" required>
            <option value="">Select Subject</option>
            @if($subjects->where('subject_code', $schedule->subject_code)->isEmpty() && $schedule->subject_code)
                <option value="{{ $schedule->subject_code }}" {{ old('subject_code', $schedule->subject_code) === $schedule->subject_code ? 'selected' : '' }}>{{ $schedule->subject_code }} (current)</option>
            @endif
            @foreach($subjects as $subject)
                <option value="{{ $subject->subject_code }}" {{ old('subject_code', $schedule->subject_code) === $subject->subject_code ? 'selected' : '' }}>{{ $subject->subject_code }} - {{ $subject->subject_name }}</option>
            @endforeach
        </select>
        <select name="day" required>
            <option value="">Select Day</option>
            @foreach($days as $d)
                <option value="{{ $d }}" {{ old('day', $schedule->day) === $d ? 'selected' : '' }}>{{ $d }}</option>
            @endforeach
        </select>
        <input type="time" name="time_in" value="{{ $timeIn }}" required />
        <input type="time" name="time_out" value="{{ $timeOut }}" required />
        <div style="display:flex; gap:10px; flex-wrap:wrap; margin-top:10px;">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="{{ route('admin.schedules') }}" class="btn btn-red" style="text-decoration:none; display:inline-block;">Cancel</a>
        </div>
    </form>
</div>
@endsection
