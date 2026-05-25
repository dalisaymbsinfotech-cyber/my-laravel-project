@extends('layouts.app')
@section('title', 'College Management')
@section('content')
<div class="page-header">
    <h2>🏛️ College Management</h2>
    <p>Add and manage colleges</p>
</div>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

<div class="card">
    <h3>Add New College</h3>
    <form method="POST" action="{{ route('admin.colleges.store') }}">
        @csrf
        <input type="text" name="code" placeholder="College Code (e.g. CAS)" required />
        <input type="text" name="name" placeholder="College Name (e.g. College of Arts and Sciences)" required />
        <input type="text" name="dean" placeholder="Dean Name" />
        <button type="submit" class="btn btn-primary">Add College</button>
    </form>
</div>

<div class="card">
    <h3>All Colleges</h3>
    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>College Name</th>
                <th>Dean</th>
                <th>Departments</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($colleges as $college)
            <tr>
                <td><strong>{{ $college->code }}</strong></td>
                <td>{{ $college->name }}</td>
                <td>{{ $college->dean ?? '—' }}</td>
                <td>{{ $college->departments_count ?? 0 }}</td>
                <td>
                    <div style="position:relative; display:inline-block;">
                        <button class="btn btn-blue" onclick="toggleMenu('college-{{ $college->id }}')">⋮</button>
                        <div id="college-{{ $college->id }}" style="display:none; position:absolute; right:0; background:white; border:1px solid #ddd; border-radius:6px; min-width:120px; z-index:100; box-shadow:0 2px 8px rgba(0,0,0,0.1);">
                            <button class="btn" style="width:100%; text-align:left; padding:8px 12px;" onclick="openEditCollege({{ $college->id }}, '{{ $college->code }}', '{{ $college->name }}', '{{ $college->dean }}')">✏️ Edit</button>
                            <form method="POST" action="{{ route('admin.colleges.destroy', $college->id) }}">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn" style="width:100%; text-align:left; padding:8px 12px; color:#c0392b;" onclick="return confirm('Delete?')">🗑️ Delete</button>
                            </form>
                        </div>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align:center; color:#aaa; padding:20px;">No colleges added yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Edit Modal -->
<div class="modal" id="editCollegeModal">
    <div class="modal-box">
        <h3>Edit College</h3>
        <form method="POST" id="editCollegeForm">
            @csrf @method('PUT')
            <input type="text" name="code" id="editCollegeCode" placeholder="College Code" required style="width:100%; margin:5px 0;" />
            <input type="text" name="name" id="editCollegeName" placeholder="College Name" required style="width:100%; margin:5px 0;" />
            <input type="text" name="dean" id="editCollegeDean" placeholder="Dean Name" style="width:100%; margin:5px 0;" />
            <div style="margin-top:15px; display:flex; gap:10px;">
                <button type="submit" class="btn btn-blue" style="flex:1;">Update</button>
                <button type="button" class="btn btn-red" onclick="closeModal('editCollegeModal')" style="flex:1;">Cancel</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function toggleMenu(id) {
    const menu = document.getElementById(id);
    document.querySelectorAll('[id^="college-"], [id^="dept-"]').forEach(m => {
        if (m.id !== id) m.style.display = 'none';
    });
    menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
}
function openEditCollege(id, code, name, dean) {
    document.getElementById('editCollegeForm').action = '/admin/colleges/' + id;
    document.getElementById('editCollegeCode').value = code;
    document.getElementById('editCollegeName').value = name;
    document.getElementById('editCollegeDean').value = dean;
    document.getElementById('editCollegeModal').classList.add('active');
    document.querySelectorAll('[id^="college-"]').forEach(m => m.style.display = 'none');
}
function closeModal(id) {
    document.getElementById(id).classList.remove('active');
}
document.addEventListener('click', function(e) {
    if (!e.target.closest('[id^="college-"]') && !e.target.closest('.btn-blue')) {
        document.querySelectorAll('[id^="college-"]').forEach(m => m.style.display = 'none');
    }
});
</script>
@endsection