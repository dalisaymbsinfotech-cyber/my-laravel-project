@extends('layouts.app')
@section('title', 'Section Management')
@section('content')
<div class="page-header">
    <h2>📂 Section Management</h2>
    <p>Add and manage academic sections</p>
</div>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

<div class="card">
    <h3>Add New Section</h3>
    <form method="POST" action="{{ route('admin.sections.store') }}">
        @csrf
        <input type="text" name="section_code" placeholder="Section Code (e.g. SEC-11A)" required />
        <input type="text" name="name" placeholder="Section Name (e.g. Section A)" required />
        <select name="college_id">
            <option value="">Select College (optional)</option>
            @foreach($colleges as $college)
                <option value="{{ $college->id }}">{{ $college->name }}</option>
            @endforeach
        </select>
        <select name="department_id">
            <option value="">Select Department (optional)</option>
            @foreach($departments as $dept)
                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-primary">Add Section</button>
    </form>
</div>

<div class="card">
    <h3>All Sections</h3>
    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Section Name</th>
                <th>Department</th>
                <th>College</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sections as $section)
            <tr>
                <td><strong>{{ $section->section_code }}</strong></td>
                <td>{{ $section->name }}</td>
                <td>{{ $section->department?->name ?? '—' }}</td>
                <td>{{ $section->college?->name ?? '—' }}</td>
                <td>
                    <div style="position:relative; display:inline-block;">
                        <button class="btn btn-blue" onclick="toggleSectionMenu('section-{{ $section->id }}')">⋮</button>
                        <div id="section-{{ $section->id }}" style="display:none; position:absolute; right:0; background:white; border:1px solid #ddd; border-radius:6px; min-width:120px; z-index:100; box-shadow:0 2px 8px rgba(0,0,0,0.1);">
                            <button class="btn" style="width:100%; text-align:left; padding:8px 12px;" onclick="openEditSection({{ $section->id }}, '{{ $section->section_code }}', '{{ $section->name }}', {{ $section->department_id ?? 'null' }}, {{ $section->college_id ?? 'null' }})">✏️ Edit</button>
                            <form method="POST" action="{{ route('admin.sections.destroy', $section->id) }}">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn" style="width:100%; text-align:left; padding:8px 12px; color:#c0392b;" onclick="return confirm('Delete?')">🗑️ Delete</button>
                            </form>
                        </div>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align:center; color:#aaa; padding:20px;">No sections added yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Edit Modal -->
<div class="modal" id="editSectionModal">
    <div class="modal-box">
        <h3>Edit Section</h3>
        <form method="POST" id="editSectionForm">
            @csrf @method('PUT')
            <input type="text" name="section_code" id="editSectionCode" placeholder="Section Code" required style="width:100%; margin:5px 0;" />
            <input type="text" name="name" id="editSectionName" placeholder="Section Name" required style="width:100%; margin:5px 0;" />
            <select name="college_id" id="editSectionCollege" style="width:100%; margin:5px 0;">
                <option value="">Select College</option>
                @foreach($colleges as $college)
                    <option value="{{ $college->id }}">{{ $college->name }}</option>
                @endforeach
            </select>
            <select name="department_id" id="editSectionDepartment" style="width:100%; margin:5px 0;">
                <option value="">Select Department</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                @endforeach
            </select>
            <div style="margin-top:15px; display:flex; gap:10px;">
                <button type="submit" class="btn btn-blue" style="flex:1;">Update</button>
                <button type="button" class="btn btn-red" onclick="closeModal('editSectionModal')" style="flex:1;">Cancel</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function toggleSectionMenu(id) {
    const menu = document.getElementById(id);
    document.querySelectorAll('[id^="section-"]').forEach(m => {
        if (m.id !== id) m.style.display = 'none';
    });
    menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
}
function openEditSection(id, code, name, deptId, collegeId) {
    document.getElementById('editSectionForm').action = '/admin/sections/' + id;
    document.getElementById('editSectionCode').value = code;
    document.getElementById('editSectionName').value = name;
    document.getElementById('editSectionDepartment').value = deptId || '';
    document.getElementById('editSectionCollege').value = collegeId || '';
    document.getElementById('editSectionModal').classList.add('active');
    document.querySelectorAll('[id^="section-"]').forEach(m => m.style.display = 'none');
}
function closeModal(id) {
    document.getElementById(id).classList.remove('active');
}
</script>
@endsection
