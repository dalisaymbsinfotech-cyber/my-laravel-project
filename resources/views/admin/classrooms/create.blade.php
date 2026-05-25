@extends('layouts.app')
@section('title', 'Add Classroom')
@section('content')
<div class="page-header">
    <h2>🚪 Add New Classroom</h2>
    <p>Create a new classroom</p>
</div>

<div class="card">
    <h3>Room Information</h3>
    <form method="POST" action="{{ route('admin.classrooms.store') }}">
        @csrf
        <input type="text" name="room_name" placeholder="Room Name (e.g. Computer Lab 1)" required />
        <input type="text" name="room_code" placeholder="Room Code (e.g. CL-101)" required />
        <input type="text" name="building" placeholder="Building (optional)" />
        <input type="number" name="capacity" placeholder="Capacity (e.g. 40)" value="40" />
        <select name="status">
            <option value="available">Available</option>
            <option value="occupied">Occupied</option>
            <option value="maintenance">Under Maintenance</option>
        </select>
        <input type="text" name="description" placeholder="Description (optional)" />
        <div style="margin-top:15px; display:flex; gap:10px;">
            <button type="submit" class="btn btn-primary" style="flex:1;">Save Classroom</button>
            <a href="{{ route('admin.classrooms') }}" class="btn btn-red" style="flex:1; text-align:center; text-decoration:none;">Cancel</a>
        </div>
    </form>
</div>
@endsection