@extends('layouts.app')
@section('title', 'Manage Students')
@section('content')
<div class="page-header">
    <h2>🎓 Manage Students</h2>
    <p>View and manage registered students. Students are automatically added via face scan.</p>
</div>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

<div class="card">
    <h3>All Registered Students</h3>
    <p style="color:#666; font-size:13px; margin-bottom:10px;">
        Students are registered through the <strong>Face Scanner</strong>. 
        Admin can only view and delete students from this list.
    </p>
    <table>
        <thead>
            <tr>
                <th>ID Number</th>
                <th>Name</th>
                <th>Section</th>
                <th>Face Data</th>
                <th>Latest Registration</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $student)
            <tr>
                <td><strong>{{ $student->id_number }}</strong></td>
                <td>
                    {{ $student->name }}
                    @if(($student->registration_count ?? 0) > 1)
                        <div style="margin-top:6px;">
                            <span class="badge badge-absent">⚠️ Duplicate ID ({{ $student->registration_count }}/3)</span>
                        </div>
                    @endif
                </td>
                <td>{{ $student->section }}</td>
                <td>
                    @if($student->face_scanned)
                        <span class="badge badge-active">✅ Scanned</span>
                    @else
                        <span class="badge badge-absent">⏳ Pending</span>
                    @endif
                </td>
                <td>{{ optional($student->sort_timestamp)->format('M d, Y h:i A') ?? 'N/A' }}</td>
                <td>
                    <form method="POST" action="{{ route('admin.students.destroy', $student->id) }}" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-red" onclick="return confirm('Delete this student?')">Delete</button>
                    </form>
                </td>
            </tr>
            @if(($student->registration_count ?? 0) > 1)
            <tr>
                <td colspan="6" style="background:#fff8e1; padding:10px 14px; border-top:0;">
                    <strong style="color:#8B0000;">Re-registration history for {{ $student->id_number }}:</strong>
                    <span style="color:#666; font-size:12px; margin-left:8px;">(latest 3 attempts)</span>
                    <div style="margin-top:8px; color:#444; font-size:13px;">
                        @foreach($student->recent_registration_logs as $index => $log)
                            <div style="padding:4px 0; display:flex; justify-content:space-between; gap:12px; align-items:center;">
                                <span>#{{ $index + 1 }} {{ $log->name }} - {{ $log->created_at->format('M d, Y h:i A') }}</span>
                                <form method="POST" action="{{ route('admin.students.registration.destroy', $log->id) }}" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-red" style="padding:6px 10px; font-size:12px;" onclick="return confirm('Delete this duplicate registration only?')">Delete</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </td>
            </tr>
            @endif
            @empty
            <tr>
                <td colspan="6" style="text-align:center; color:#aaa; padding:20px;">
                    No students registered yet. Students will appear here after face scanning.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection