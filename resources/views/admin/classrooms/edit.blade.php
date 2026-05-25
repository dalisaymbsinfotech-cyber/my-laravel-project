@extends('layouts.app')
@section('title', 'Edit Classroom')
@section('content')
<div class="page-header">
    <h2>✏️ Edit Classroom</h2>
    <p>Update room information</p>
</div>

<div class="card">
    <h3>Room Information</h3>
    <form method="POST" action="{{ route('admin.classrooms.update', $classroom->id) }}">
        @csrf @method('PUT')
        <input type="text" name="room_name" value="{{ $classroom->room_name }}" placeholder="Room Name" required />
        <input type="text" name="room_code" value="{{ $classroom->room_code }}" placeholder="Room Code" required />
        <input type="text" name="building" value="{{ $classroom->building }}" placeholder="Building (optional)" />
        <input type="number" name="capacity" value="{{ $classroom->capacity }}" placeholder="Capacity" />
        <select name="status">
            <option value="available" {{ $classroom->status === 'available' ? 'selected' : '' }}>Available</option>
            <option value="occupied" {{ $classroom->status === 'occupied' ? 'selected' : '' }}>Occupied</option>
            <option value="maintenance" {{ $classroom->status === 'maintenance' ? 'selected' : '' }}>Under Maintenance</option>
        </select>
        <input type="text" name="description" value="{{ $classroom->description }}" placeholder="Description (optional)" />
        <div style="margin-top:15px; display:flex; gap:10px;">
            <button type="submit" class="btn btn-primary" style="flex:1;">Save Changes</button>
            <a href="{{ route('admin.classrooms') }}" class="btn btn-red" style="flex:1; text-align:center; text-decoration:none;">Cancel</a>
        </div>
    </form>
</div>
@endsection
