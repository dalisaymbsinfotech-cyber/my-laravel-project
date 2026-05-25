@extends('layouts.app')
@section('title', 'Manage Subjects')
@section('content')
<div class="page-header">
    <h2>📚 Manage Subjects</h2>
    <p>Add and manage subjects</p>
</div>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

<div class="card">
    <h3>Add New Subject</h3>
    <form method="POST" action="{{ route('admin.subjects.store') }}">
        @csrf
        <input type="text" name="subject_code" placeholder="Subject Code (e.g. MATH101)" required />
        <input type="text" name="subject_name" placeholder="Subject Name" required />
        <input type="text" name="professor_name" placeholder="Professor Name" required />
        <input type="text" name="section" placeholder="Section (e.g. 11-A)" required />
        <button type="submit" class="btn btn-primary">Add Subject</button>
    </form>
</div>

<div class="card">
    <h3>All Subjects</h3>
    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Name</th>
                <th>Professor</th>
                <th>Section</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($subjects as $subject)
            <tr>
                <td>{{ $subject->subject_code }}</td>
                <td>{{ $subject->subject_name }}</td>
                <td>{{ $subject->professor_name }}</td>
                <td>{{ $subject->section }}</td>
                <td>
                    <a href="{{ route('admin.subjects.edit', $subject->id) }}" class="btn btn-blue" style="text-decoration:none;">Edit</a>
                    <form method="POST" action="{{ route('admin.subjects.destroy', $subject->id) }}" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-red" onclick="return confirm('Delete this subject?')">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align:center; color:#aaa; padding:20px;">No subjects added yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection