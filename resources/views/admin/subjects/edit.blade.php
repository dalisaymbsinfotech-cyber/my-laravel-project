@extends('layouts.app')
@section('title', 'Edit Subject')
@section('content')
<div class="page-header">
    <h2>✏️ Edit Subject</h2>
    <p>Update subject details</p>
</div>

@if ($errors->any())
    <div class="alert-error" style="margin-bottom:15px;">{{ $errors->first() }}</div>
@endif

<div class="card">
    <form method="POST" action="{{ route('admin.subjects.update', $subject->id) }}">
        @csrf
        @method('PUT')
        <input type="text" name="subject_code" value="{{ old('subject_code', $subject->subject_code) }}" placeholder="Subject Code (e.g. MATH101)" required />
        <input type="text" name="subject_name" value="{{ old('subject_name', $subject->subject_name) }}" placeholder="Subject Name" required />
        <input type="text" name="professor_name" value="{{ old('professor_name', $subject->professor_name) }}" placeholder="Professor Name" required />
        <input type="text" name="section" value="{{ old('section', $subject->section) }}" placeholder="Section (e.g. 11-A)" required />
        <div style="display:flex; gap:10px; flex-wrap:wrap; margin-top:10px;">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="{{ route('admin.subjects') }}" class="btn btn-red" style="text-decoration:none; display:inline-block;">Cancel</a>
        </div>
    </form>
</div>
@endsection
