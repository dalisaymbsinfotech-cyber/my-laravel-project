@extends('layouts.app')
@section('title', 'Classroom Management')
@section('content')
<div class="page-header">
    <h2>🚪 Classroom Management</h2>
    <p>Add and manage classrooms</p>
</div>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

<div class="card">
    <h3>Add New Classroom</h3>
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
        <button type="submit" class="btn btn-primary">Add Classroom</button>
    </form>
</div>
    <h3>All Classrooms</h3>
    <table>
        <thead>
            <tr>
                <th>Room Code</th>
                <th>Room Name</th>
                <th>Building</th>
                <th>Capacity</th>
                <th>Schedules</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($classrooms as $classroom)
            <tr>
                <td><strong>{{ $classroom->room_code }}</strong></td>
                <td>{{ $classroom->room_name }}</td>
                <td>{{ $classroom->building ?? '—' }}</td>
                <td>{{ $classroom->capacity }}</td>
                <td>{{ $classroom->schedules_count }}</td>
                <td><span class="badge badge-{{ $classroom->status === 'available' ? 'active' : 'inactive' }}">{{ ucfirst($classroom->status) }}</span></td>
                <td>
                    <div style="position:relative; display:inline-block;">
                        <button class="btn btn-blue" onclick="toggleClassMenu('cls-{{ $classroom->id }}')">⋮</button>
                        <div id="cls-{{ $classroom->id }}" style="display:none; position:absolute; right:0; background:white; border:1px solid #ddd; border-radius:6px; min-width:130px; z-index:100; box-shadow:0 2px 8px rgba(0,0,0,0.1);">
                            <button class="btn" style="width:100%; text-align:left; padding:8px 12px; display:block; text-decoration:none; color:#333;" onclick="openEditClassroom({{ $classroom->id }}, '{{ $classroom->room_code }}', '{{ $classroom->room_name }}', '{{ $classroom->building }}', {{ $classroom->capacity }}, '{{ $classroom->status }}', '{{ $classroom->description }}')">✏️ Edit</button>
                            <form method="POST" action="{{ route('admin.classrooms.destroy', $classroom->id) }}">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn" style="width:100%; text-align:left; padding:8px 12px; color:#c0392b;" onclick="return confirm('Delete?')">🗑️ Delete</button>
                            </form>
                        </div>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align:center; color:#aaa; padding:20px;">No classrooms added yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Edit Modal -->
<div class="modal" id="editClassroomModal">
    <div class="modal-box">
        <h3>Edit Classroom</h3>
        <form method="POST" id="editClassroomForm">
            @csrf @method('PUT')
            <input type="text" name="room_code" id="editRoomCode" placeholder="Room Code" required style="width:100%; margin:5px 0;" />
            <input type="text" name="room_name" id="editRoomName" placeholder="Room Name" required style="width:100%; margin:5px 0;" />
            <input type="text" name="building" id="editBuilding" placeholder="Building (optional)" style="width:100%; margin:5px 0;" />
            <input type="number" name="capacity" id="editCapacity" placeholder="Capacity" style="width:100%; margin:5px 0;" />
            <select name="status" id="editStatus" style="width:100%; margin:5px 0;">
                <option value="available">Available</option>
                <option value="occupied">Occupied</option>
                <option value="maintenance">Under Maintenance</option>
            </select>
            <input type="text" name="description" id="editDescription" placeholder="Description (optional)" style="width:100%; margin:5px 0;" />
            <div style="margin-top:15px; display:flex; gap:10px;">
                <button type="submit" class="btn btn-blue" style="flex:1;">Update</button>
                <button type="button" class="btn btn-red" onclick="closeModal('editClassroomModal')" style="flex:1;">Cancel</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function toggleClassMenu(id) {
    const menu = document.getElementById(id);
    document.querySelectorAll('[id^="cls-"]').forEach(m => {
        if (m.id !== id) m.style.display = 'none';
    });
    menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
}
function openEditClassroom(id, code, name, building, capacity, status, description) {
    document.getElementById('editClassroomForm').action = '/admin/classrooms/' + id;
    document.getElementById('editRoomCode').value = code;
    document.getElementById('editRoomName').value = name;
    document.getElementById('editBuilding').value = building || '';
    document.getElementById('editCapacity').value = capacity || '';
    document.getElementById('editStatus').value = status || 'available';
    document.getElementById('editDescription').value = description || '';
    document.getElementById('editClassroomModal').classList.add('active');
    document.querySelectorAll('[id^="cls-"]').forEach(m => m.style.display = 'none');
}
function closeModal(id) {
    document.getElementById(id).classList.remove('active');
}
document.addEventListener('click', function(e) {
    if (!e.target.closest('[id^="cls-"]') && !e.target.closest('.btn-blue')) {
        document.querySelectorAll('[id^="cls-"]').forEach(m => m.style.display = 'none');
    }
});
</script>
@endsection