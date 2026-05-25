@extends('layouts.app')
@section('title', 'Manage Professors')
@section('content')
<div class="page-header">
    <h2>👨‍🏫 Manage Professors</h2>
    <p>Manage professors and their assigned subjects and sections.</p>
</div>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

<div class="card">
    <h3>Add Professor</h3>
    @if ($errors->any())
        <div class="alert-error" style="margin-bottom:12px;">{{ $errors->first() }}</div>
    @endif
    <form method="POST" action="{{ route('admin.professors.store') }}">
        @csrf
        <input type="text" name="name" placeholder="Professor Name" value="{{ old('name') }}" required />
        <select name="subject_code" required>
            <option value="">Select Subject</option>
            @foreach($subjects as $subject)
                <option value="{{ $subject->subject_code }}" {{ old('subject_code') === $subject->subject_code ? 'selected' : '' }}>{{ $subject->subject_code }} - {{ $subject->subject_name }}</option>
            @endforeach
        </select>
        <input type="text" name="year_section" placeholder="Year & Section (e.g. 11-A)" value="{{ old('year_section') }}" required />
        <p style="color:#666; font-size:12px; margin:10px 0 6px;">Optional — professor portal login (same email can be reused for multiple subject rows):</p>
        <input type="email" name="login_email" value="{{ old('login_email') }}" placeholder="Login email" />
        <input type="password" name="login_password" placeholder="Password (required for new email)" />
        <input type="text" name="employee_id" value="{{ old('employee_id') }}" placeholder="Professor ID" />
        <input type="text" name="college" value="{{ old('college') }}" placeholder="College" />
        <button type="submit" class="btn btn-primary">Add Professor</button>
    </form>
</div>

<div class="card">
    <h3>Professor Records</h3>
    <table>
        <thead>
            <tr>
                <th>Professor</th>
                <th>Subject Code</th>
                <th>Year & Section</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($professors as $professor)
            <tr>
                <td>{{ $professor->name }}</td>
                <td>{{ $professor->subject_code }}</td>
                <td>{{ $professor->year_section }}</td>
                <td>
                    <a href="{{ route('admin.professors.edit', $professor->id) }}" class="btn btn-blue" style="text-decoration:none;">Edit</a>
                    <form method="POST" action="{{ route('admin.professors.destroy', $professor->id) }}" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-red" onclick="return confirm('Delete this professor?')">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" style="text-align:center; color:#aaa; padding:20px;">No professors added yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection