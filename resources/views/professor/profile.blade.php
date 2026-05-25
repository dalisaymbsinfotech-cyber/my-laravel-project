@extends('layouts.professor')
@section('title', 'My profile')
@section('content')
<div class="page-header">
    <h2>👤 My profile</h2>
    <p>Your portal details and profile photo.</p>
</div>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

@if ($errors->any())
    <div class="alert-error">{{ $errors->first() }}</div>
@endif

<div class="card">
    <h3>Profile information</h3>
    <p style="color:#666; font-size:13px; margin-bottom:12px;">Professor ID and email are shown for reference. Name, college, and photo can be updated.</p>

    <div style="display:flex; gap:20px; flex-wrap:wrap; align-items:flex-start; margin-bottom:16px;">
        <div style="width:120px; height:120px; border:2px dashed #ddd; border-radius:10px; overflow:hidden; background:#f9f9f9; display:flex; align-items:center; justify-content:center;">
            @if($user->profile_photo)
                <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="" style="width:100%; height:100%; object-fit:cover;" />
            @else
                <span style="font-size:48px;">👤</span>
            @endif
        </div>
        <div style="flex:1; min-width:200px;">
            <p style="font-size:13px; margin-bottom:6px;"><strong>Email (login)</strong><br>{{ $user->email }}</p>
            <p style="font-size:13px;"><strong>Professor ID</strong><br>{{ $user->employee_id ?: '—' }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('professor.profile.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <label class="field" style="display:block; font-size:12px; color:#666; font-weight:600; margin-bottom:4px;">Display name</label>
        <input type="text" name="name" value="{{ old('name', $user->name) }}" required style="width:100%; max-width:400px; margin-bottom:12px;" />

        <label class="field" style="display:block; font-size:12px; color:#666; font-weight:600; margin-bottom:4px;">Professor ID</label>
        <input type="text" name="employee_id" value="{{ old('employee_id', $user->employee_id) }}" placeholder="e.g. EMP-2024-001" style="width:100%; max-width:400px; margin-bottom:12px;" />

        <label class="field" style="display:block; font-size:12px; color:#666; font-weight:600; margin-bottom:4px;">College</label>
        <input type="text" name="college" value="{{ old('college', $user->college) }}" placeholder="e.g. College of Engineering" style="width:100%; max-width:400px; margin-bottom:12px;" />

        <label class="field" style="display:block; font-size:12px; color:#666; font-weight:600; margin-bottom:4px;">Profile photo</label>
        <input type="file" name="photo" accept="image/*" style="margin-bottom:16px;" />
        <small style="color:#888; display:block; margin-bottom:12px;">PNG, JPG, WebP. Max 2MB.</small>

        <button type="submit" class="btn btn-primary">Save profile</button>
    </form>
</div>
@endsection
