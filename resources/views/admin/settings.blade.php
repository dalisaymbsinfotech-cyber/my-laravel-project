@extends('layouts.app')
@section('title', 'System Settings')

@section('styles')
<style>
    .settings-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .settings-section { background: white; border-radius: 10px; padding: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.06); border-top: 4px solid var(--red); }
    .settings-section h3 { color: var(--red); font-size: 16px; font-weight: 600; margin-bottom: 4px; }
    .settings-section p { color: #888; font-size: 13px; margin-bottom: 1.2rem; border-bottom: 1px solid #eee; padding-bottom: 12px; }
    .form-group { display: flex; flex-direction: column; gap: 4px; margin-bottom: 14px; }
    .form-group label { font-size: 12px; color: #666; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
    .form-group input { width: 100%; margin: 0; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; }
    .form-group textarea { width: 100%; margin: 0; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; min-height: 180px; resize: vertical; font-family: inherit; }
    .form-group textarea:focus { border-color: var(--red); outline: none; }
    .form-group input:focus { border-color: var(--red); outline: none; }
    .logo-preview { width: 100px; height: 100px; border: 2px dashed #ddd; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-bottom: 12px; overflow: hidden; background: #f9f9f9; }
    .logo-preview img { width: 100%; height: 100%; object-fit: contain; }
    .logo-placeholder { font-size: 40px; }
    .save-btn { background: var(--red); color: white; border: none; border-radius: 6px; padding: 10px 24px; font-size: 14px; font-weight: 600; cursor: pointer; width: 100%; font-family: 'Poppins', sans-serif; transition: background 0.2s; }
    .save-btn:hover { background: #6b0000; }
    .alert-success-custom { background: #d4edda; color: #155724; padding: 10px 15px; border-radius: 6px; margin-bottom: 15px; border-left: 4px solid #28a745; font-size: 13px; }
    .full-width { grid-column: 1 / -1; }
    @media (max-width: 768px) { .settings-grid { grid-template-columns: 1fr; } }
</style>
@endsection

@section('content')
<div class="page-header">
    <h2>⚙️ System Settings</h2>
    <p>Manage company name, system tagline, logo, and admin credentials</p>
</div>

<div class="settings-grid">

    <!-- Logo & System Name -->
    <div class="settings-section">
        <h3>🖼️ Logo & sidebar title</h3>
        <p>Upload your logo and set the company name (white heading) and system name (gold subheading) shown beside it in the sidebar.</p>

        @if(session('success_system'))
            <div class="alert-success-custom">✅ {{ session('success_system') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert-success-custom" style="background:#f8d7da;color:#721c24;border-left-color:#c0392b;">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('admin.settings.system') }}" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label>Current Logo</label>
                <div class="logo-preview">
                    @if($settings->logo_path)
                        <img src="{{ asset('storage/' . $settings->logo_path) }}" alt="Logo" id="logoPreview" />
                    @else
                        <span class="logo-placeholder" id="logoPlaceholder">🏫</span>
                        <img src="" alt="" id="logoPreview" style="display:none;" />
                    @endif
                </div>
            </div>

            <div class="form-group">
                <label>Upload New Logo</label>
                <input type="file" name="logo" accept="image/*" onchange="previewLogo(this)" />
                <small style="color:#888; font-size:11px;">PNG, JPG, WebP accepted. Max 2MB.</small>
            </div>

            <div class="form-group">
                <label>Company name</label>
                <input type="text" name="company_name" value="{{ old('company_name', $settings->company_name ?? 'EARIST') }}" placeholder="e.g. EARIST" required />
                <small style="color:#888; font-size:11px;">Large white text next to the logo.</small>
            </div>

            <div class="form-group">
                <label>System name</label>
                <input type="text" name="system_name" value="{{ old('system_name', $settings->system_name) }}" placeholder="e.g. School Admin System" required />
                <small style="color:#888; font-size:11px;">Smaller gold line under the company name.</small>
            </div>

            <button type="submit" class="save-btn">💾 Save Changes</button>
        </form>
    </div>

    <!-- Admin Credentials -->
    <div class="settings-section">
        <h3>🔐 Admin Credentials</h3>
        <p>Change your admin username and password</p>

        @if(session('success_credentials'))
            <div class="alert-success-custom">✅ {{ session('success_credentials') }}</div>
        @endif

        <form method="POST" action="{{ route('admin.settings.credentials') }}">
            @csrf

            <div class="form-group">
                <label>Current Username</label>
                <input type="text" value="{{ $settings->admin_username }}" disabled style="background:#f5f5f5; color:#888;" />
            </div>

            <div class="form-group">
                <label>New Username</label>
                <input type="text" name="admin_username" placeholder="Enter new username" required />
            </div>

            <div class="form-group">
                <label>New Password</label>
                <input type="password" name="admin_password" placeholder="Enter new password (min 6 chars)" required />
            </div>

            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="admin_password_confirmation" placeholder="Confirm new password" required />
            </div>

            <button type="submit" class="save-btn">🔒 Update Credentials</button>
        </form>
    </div>

    

</div>
@endsection

@section('scripts')
<script>
function previewLogo(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('logoPreview');
            const placeholder = document.getElementById('logoPlaceholder');
            preview.src = e.target.result;
            preview.style.display = 'block';
            if (placeholder) placeholder.style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection