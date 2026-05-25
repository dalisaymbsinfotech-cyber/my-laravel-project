@extends('layouts.app')
@section('title', 'Edit Professor')
@section('content')
<div class="page-header">
    <h2>✏️ Edit Professor</h2>
    <p>Update name, subject, and section assignment</p>
</div>

@if ($errors->any())
    <div class="alert-error" style="margin-bottom:15px;">{{ $errors->first() }}</div>
@endif

<div class="card">
    <form method="POST" action="{{ route('admin.professors.update', $professor->id) }}">
        @csrf
        @method('PUT')
        <input type="text" name="name" value="{{ old('name', $professor->name) }}" placeholder="Professor Name" required />
        <select name="subject_code" required>
            <option value="">Select Subject</option>
            @if($subjects->where('subject_code', $professor->subject_code)->isEmpty() && $professor->subject_code)
                <option value="{{ $professor->subject_code }}" {{ old('subject_code', $professor->subject_code) === $professor->subject_code ? 'selected' : '' }}>{{ $professor->subject_code }} (current)</option>
            @endif
            @foreach($subjects as $subject)
                <option value="{{ $subject->subject_code }}" {{ old('subject_code', $professor->subject_code) === $subject->subject_code ? 'selected' : '' }}>{{ $subject->subject_code }} - {{ $subject->subject_name }}</option>
            @endforeach
        </select>
        <input type="text" name="year_section" value="{{ old('year_section', $professor->year_section) }}" placeholder="Year & Section (e.g. 11-A)" required />

        <p style="color:#666; font-size:12px; margin:12px 0 6px;">Professor portal account:</p>
        <input type="email" name="login_email" value="{{ old('login_email', $professor->user?->email) }}" placeholder="Login email" />
        <input type="password" name="login_password" placeholder="New password (optional)" />
        <input type="text" name="employee_id" value="{{ old('employee_id', $professor->user?->employee_id) }}" placeholder="Professor ID" />
        <input type="text" name="college" value="{{ old('college', $professor->user?->college) }}" placeholder="College" />

        <div style="display:flex; gap:10px; flex-wrap:wrap; margin-top:10px;">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="{{ route('admin.professors') }}" class="btn btn-red" style="text-decoration:none; display:inline-block;">Cancel</a>
        </div>
    </form>
</div>
@endsection
