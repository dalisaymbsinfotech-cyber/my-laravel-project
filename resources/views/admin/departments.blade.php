@extends('layouts.app')
@section('title', 'Departments')
@section('content')
<div class="page-header">
    <h2>🎓 Departments</h2>
    <p>Add and manage departments under colleges</p>
</div>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

<div class="card">
    <h3>Add New Department</h3>
    <form method="POST" action="{{ route('admin.departments.store') }}">
        @csrf
        <input type="text" name="code" placeholder="Department Code (e.g. CS)" required />
        <input type="text" name="name" placeholder="Department Name (e.g. Computer Science)" required />
        <input type="text" name="head" placeholder="Department Head (optional)" />
        <select name="college_id">
            <option value="">Select College (optional)</option>
            @foreach($colleges as $college)
                <option value="{{ $college->id }}">{{ $college->code }} - {{ $college->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-primary">Add Department</button>
    </form>
</div>

<div class="card">
    <h3>All Departments</h3>
    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Department Name</th>
                <th>Head</th>
                <th>College</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($departments as $dept)
            <tr>
                <td><strong>{{ $dept->code }}</strong></td>
                <td>{{ $dept->name }}</td>
                <td>{{ $dept->head ?? '—' }}</td>
                <td>{{ $dept->college->code ?? '—' }}</td>
                <td>
                    <div style="position:relative; display:inline-block;">
                        <button class="btn btn-blue" onclick="toggleDeptMenu('dept-{{ $dept->id }}')">⋮</button>
                        <div id="dept-{{ $dept->id }}" style="display:none; position:absolute; right:0; background:white; border:1px solid #ddd; border-radius:6px; min-width:120px; z-index:100; box-shadow:0 2px 8px rgba(0,0,0,0.1);">
                            <button class="btn" style="width:100%; text-align:left; padding:8px 12px;" onclick="openEditDept({{ $dept->id }}, '{{ $dept->code }}', '{{ $dept->name }}', '{{ $dept->head }}', '{{ $dept->college_id }}')">✏️ Edit</button>
                            <form method="POST" action="{{ route('admin.departments.destroy', $dept->id) }}">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn" style="width:100%; text-align:left; padding:8px 12px; color:#c0392b;" onclick="return confirm('Delete?')">🗑️ Delete</button>
                            </form>
                        </div>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align:center; color:#aaa; padding:20px;">No programs added yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Edit Modal -->
<div class="modal" id="editDeptModal">
    <div class="modal-box">
        <h3>Edit Program</h3>
        <form method="POST" id="editDeptForm">
            @csrf @method('PUT')
            <input type="text" name="code" id="editDeptCode" placeholder="Program Code" required style="width:100%; margin:5px 0;" />
            <input type="text" name="name" id="editDeptName" placeholder="Program Name" required style="width:100%; margin:5px 0;" />
            <input type="text" name="head" id="editDeptHead" placeholder="Program Head" style="width:100%; margin:5px 0;" />
            <select name="college_id" id="editDeptCollege" style="width:100%; margin:5px 0;">
                <option value="">Select College</option>
                @foreach($colleges as $college)
                    <option value="{{ $college->id }}">{{ $college->code }} - {{ $college->name }}</option>
                @endforeach
            </select>
            <div style="margin-top:15px; display:flex; gap:10px;">
                <button type="submit" class="btn btn-blue" style="flex:1;">Update</button>
                <button type="button" class="btn btn-red" onclick="closeModal('editDeptModal')" style="flex:1;">Cancel</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function toggleDeptMenu(id) {
    const menu = document.getElementById(id);
    document.querySelectorAll('[id^="dept-"]').forEach(m => {
        if (m.id !== id) m.style.display = 'none';
    });
    menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
}
function openEditDept(id, code, name, head, collegeId) {
    document.getElementById('editDeptForm').action = '/admin/departments/' + id;
    document.getElementById('editDeptCode').value = code;
    document.getElementById('editDeptName').value = name;
    document.getElementById('editDeptHead').value = head;
    document.getElementById('editDeptCollege').value = collegeId;
    document.getElementById('editDeptModal').classList.add('active');
    document.querySelectorAll('[id^="dept-"]').forEach(m => m.style.display = 'none');
}
function closeModal(id) {
    document.getElementById(id).classList.remove('active');
}
</script>
@endsection