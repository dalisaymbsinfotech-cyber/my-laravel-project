@extends('layouts.app')
@section('title', 'Enrollment')
@section('styles')
<style>
    .select2-container { width: 100% !important; }
    .select2-container .select2-selection--single { height: 42px; border: 1px solid #ddd; border-radius: 6px; }
    .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 42px; padding-left: 12px; font-size: 14px; color: #333; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 42px; }
    .select2-container--default .select2-results__option--highlighted { background: #8B0000 !important; }
    .select2-dropdown { border: 1px solid #ddd; border-radius: 6px; }
    .form-row { display: flex; gap: 10px; flex-wrap: wrap; align-items: flex-end; }
    .form-row .form-item { flex: 1; min-width: 150px; }
    .form-row .form-item label { font-size: 12px; color: #666; font-weight: 600; display: block; margin-bottom: 4px; }
    .form-row select, .form-row input { width: 100%; margin: 0; }
    /* Modal styles for Manage Sections */
    .modal-backdrop-custom { position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: none; align-items: center; justify-content: center; z-index: 9999; }
    .modal-window { background: white; border-radius: 8px; width: 520px; max-width: 95%; padding: 18px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
    .modal-window h4 { margin: 0 0 8px 0; }
    .sections-list { max-height: 220px; overflow: auto; border: 1px solid #eee; border-radius:6px; padding:8px; }
    .section-item { display:flex; justify-content:space-between; align-items:center; padding:6px 8px; border-bottom:1px solid #f2f2f2; }
    .section-item:last-child { border-bottom: none; }
</style>
@endsection

@section('content')
<div class="page-header">
    <h2>📝 Enrollment</h2>
    <p>Enroll students to their respective subjects and sections.</p>
</div>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

<div class="card">
    <h3>Enroll Student</h3>
    <form method="POST" action="{{ route('admin.enrollment.store') }}">
        @csrf
        <div class="form-row">
            <div class="form-item">
                <label>Student ID</label>
                <select name="student_id" id="studentId" required>
                    <option value="">Select Student ID</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id_number }}" data-name="{{ $student->name }}">{{ $student->id_number }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-item">
                <label>Student Name</label>
                <select name="student_name" id="studentName" required>
                    <option value="">Select Student Name</option>
                    @foreach($students as $student)
                        <option value="{{ $student->name }}" data-id="{{ $student->id_number }}">{{ $student->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-item">
                <label>Subject</label>
                <select name="subject_code" required>
                    <option value="">Select Subject</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->subject_code }}">{{ $subject->subject_code }} - {{ $subject->subject_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-item">
                <label>Section</label>
                <button type="button" id="manageSectionsBtn" class="btn" style="margin-left:8px; font-size:12px; padding:6px 10px; display:inline-block; margin-bottom:6px;">Manage sections</button>
                <select name="section" id="sectionSelect" required>
                    <option value="">Select Section</option>
                    @foreach(($sections ?? []) as $section)
                        <option value="{{ $section }}">{{ $section }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-item" style="flex:0;">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary">Enroll Student</button>
            </div>
        </div>
    </form>
</div>

<!-- Manage Sections Modal -->
<div id="sectionsModal" class="modal-backdrop-custom">
    <div class="modal-window" role="dialog" aria-modal="true">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
            <h4>Manage Sections</h4>
            <button id="closeSectionsModal" class="btn">Close</button>
        </div>

        <div style="margin-bottom:10px; display:flex; gap:8px;">
            <input type="text" id="newSectionName" placeholder="New year level" style="flex:1; padding:8px; border:1px solid #ddd; border-radius:6px;" />
            <button id="addSectionBtn" class="btn btn-primary">Add</button>
        </div>

        <div class="sections-list" id="sectionsList">
            <!-- populated dynamically -->
        </div>
    </div>
</div>

<div class="card">
    <h3>Enrollment Records</h3>
    <table>
        <thead>
            <tr>
                <th>Student ID</th>
                <th>Name</th>
                <th>Subject</th>
                <th>Section</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($enrollments as $enrollment)
            <tr>
                <td><strong>{{ $enrollment->student_id }}</strong></td>
                <td>{{ $enrollment->student_name }}</td>
                <td>{{ $enrollment->subject_code }}</td>
                <td>{{ $enrollment->section }}</td>
                <td>
                    <form method="POST" action="{{ route('admin.enrollment.destroy', $enrollment->id) }}" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-red" onclick="return confirm('Remove this enrollment?')">Remove</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align:center; color:#aaa; padding:20px;">No enrollments yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

@section('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('#studentId').select2({
        placeholder: 'Search Student ID...',
        allowClear: true
    });
    $('#studentName').select2({
        placeholder: 'Search Student Name...',
        allowClear: true
    });

    // Sync Student ID and Name
    $('#studentId').on('change', function() {
        const selectedId = String($(this).val() || '');
        const option = $('#studentName option').filter(function() {
            return String($(this).data('id') || '') === selectedId;
        });
        if (option.length) $('#studentName').val(option.val()).trigger('change');
    });

    $('#studentName').on('change', function() {
        const selectedName = String($(this).val() || '');
        const option = $('#studentId option').filter(function() {
            return String($(this).data('name') || '') === selectedName;
        });
        if (option.length) $('#studentId').val(option.val()).trigger('change');
    });

    // Sections modal logic
    function csrfToken() {
        return $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}';
    }

    function openSectionsModal() {
        $('#sectionsModal').css('display', 'flex');
        loadSections();
    }

    function closeSectionsModal() {
        $('#sectionsModal').hide();
    }

    $('#manageSectionsBtn').on('click', function() { openSectionsModal(); });
    $('#closeSectionsModal').on('click', function() { closeSectionsModal(); });

    function loadSections() {
        $.getJSON('{{ route('admin.sections') }}', function(data) {
            const list = $('#sectionsList').empty();
            const select = $('#sectionSelect').empty();
            select.append('<option value="">Select Section</option>');
            if (!Array.isArray(data)) return;
            data.forEach(function(item) {
                const row = $("<div class='section-item'></div>");
                row.append('<div>' + $('<div>').text(item.name).html() + '</div>');
                const del = $("<button class='btn btn-red' style='font-size:12px;padding:4px 8px'>Delete</button>");
                del.on('click', function() { deleteSection(item.id); });
                row.append(del);
                list.append(row);

                select.append('<option value="' + $('<div>').text(item.name).html() + '">' + $('<div>').text(item.name).html() + '</option>');
            });
        }).fail(function() {
            $('#sectionsList').html('<div style="color:#888; padding:10px;">Unable to load sections.</div>');
        });
    }

    function addSection() {
        const name = String($('#newSectionName').val() || '').trim();
        if (!name) return alert('Please enter a year level.');
        $.ajax({
            url: '{{ route('admin.sections') }}',
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken() },
            data: { name: name },
            dataType: 'json'
        }).done(function(res) {
            $('#newSectionName').val('');
            loadSections();
        }).fail(function(xhr) {
            const msg = xhr.responseJSON && xhr.responseJSON.errors && xhr.responseJSON.errors.name ? xhr.responseJSON.errors.name[0] : 'Unable to add section.';
            alert(msg);
        });
    }

    function deleteSection(id) {
        if (!confirm('Remove this section?')) return;
        $.ajax({
            url: '{{ url('admin/sections') }}/' + id,
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken() },
            dataType: 'json'
        }).done(function() {
            loadSections();
        }).fail(function() {
            alert('Unable to remove section.');
        });
    }

    $('#addSectionBtn').on('click', function(e) { e.preventDefault(); addSection(); });

    // Close modal when clicking backdrop
    $('#sectionsModal').on('click', function(e) {
        if (e.target.id === 'sectionsModal') closeSectionsModal();
    });
});
</script>
@endsection