@extends('layouts.app')
@section('title', 'Manage Schedules')
@section('content')
<div class="page-header">
    <h2>🗓️ Manage Schedules</h2>
    <p>Add and manage class schedules for all classrooms and subjects.</p>
</div>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

<div class="card">
    <h3>Add New Schedule</h3>
    <form method="POST" action="{{ route('admin.schedules.store') }}">
        @csrf
        <select name="room" required>
            <option value="">Select classroom</option>
            @foreach($rooms as $room)
                <option value="{{ $room->room_name }}">{{ $room->room_name }}</option>
            @endforeach
        </select>
        <select name="subject_code" required>
            <option value="">Select Subject</option>
            @foreach($subjects as $subject)
                <option value="{{ $subject->subject_code }}">{{ $subject->subject_code }} - {{ $subject->subject_name }}</option>
            @endforeach
        </select>
        <select name="day" required>
            <option value="">Select Day</option>
            <option value="Monday">Monday</option>
            <option value="Tuesday">Tuesday</option>
            <option value="Wednesday">Wednesday</option>
            <option value="Thursday">Thursday</option>
            <option value="Friday">Friday</option>
            <option value="Saturday">Saturday</option>
        </select>
        <input type="time" name="time_in" required />
        <input type="time" name="time_out" required />
        <button type="submit" class="btn btn-primary">Add Schedule</button>
    </form>
</div>

<div class="card">
    <h3>All Schedules</h3>
    <table>
        <thead>
            <tr>
                <th>Classroom</th>
                <th>Subject</th>
                <th>Day</th>
                <th>Time In</th>
                <th>Time Out</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($schedules as $schedule)
            <tr>
                <td>{{ $schedule->room }}</td>
                <td>{{ $schedule->subject_code }}</td>
                <td>{{ $schedule->day }}</td>
                <td>{{ $schedule->time_in }}</td>
                <td>{{ $schedule->time_out }}</td>
                <td>
                    <a href="{{ route('admin.schedules.edit', $schedule->id) }}" class="btn btn-blue" style="text-decoration:none;">Edit</a>
                    <form method="POST" action="{{ route('admin.schedules.destroy', $schedule->id) }}" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-red" onclick="return confirm('Delete this schedule?')">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" style="text-align:center; color:#aaa; padding:20px;">No schedules added yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection