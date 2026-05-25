@extends('layouts.app')
@section('title', 'View Classroom')
@section('styles')
<style>
    .info-section { background: white; border: 1px solid #eee; border-radius: 10px; padding: 1.5rem; margin-bottom: 1.5rem; }
    .info-section h3 { color: var(--red); font-size: 15px; font-weight: 600; border-bottom: 2px solid var(--gold); padding-bottom: 8px; margin-bottom: 1.2rem; }
    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .info-item label { font-size: 11px; color: #888; font-weight: 500; text-transform: uppercase; display: block; margin-bottom: 4px; }
    .info-item p { font-size: 14px; color: #333; font-weight: 500; }
    .sched-table { width: 100%; border-collapse: collapse; }
    .sched-table th { background: var(--red); color: white; padding: 10px; text-align: left; font-size: 12px; }
    .sched-table td { padding: 10px; border-bottom: 1px solid #eee; font-size: 13px; }
    .sched-table tr:hover { background: #f9f9f9; }
    .badge-available { background: #d4edda; color: #155724; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    .badge-occupied { background: #f8d7da; color: #721c24; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    .qr-box { background: #f9f9f9; border: 1px solid #eee; border-radius: 8px; padding: 1.5rem; text-align: center; }
    .qr-code { font-size: 80px; margin-bottom: 8px; }
    .qr-box p { font-size: 13px; color: #888; }
    .qr-box strong { font-size: 16px; color: #333; display: block; margin: 8px 0; }
</style>
@endsection

@section('content')
<div class="page-header" style="display:flex; justify-content:space-between; align-items:center;">
    <div>
        <h2>👁️ {{ $classroom->room_name }}</h2>
        <p>Room Code: {{ $classroom->room_code }}</p>
    </div>
    <div style="display:flex; gap:8px;">
        <a href="{{ route('admin.classrooms.edit', $classroom->id) }}" class="btn btn-blue">✏️ Edit</a>
        <a href="{{ route('admin.classrooms') }}" class="btn btn-red">← Back</a>
    </div>
</div>

<div style="display:grid; grid-template-columns: 2fr 1fr; gap:16px;">
    <div>
        <!-- Room Info -->
        <div class="info-section">
            <h3>Room Information</h3>
            <div class="info-grid">
                <div class="info-item">
                    <label>Room Name</label>
                    <p>{{ $classroom->room_name }}</p>
                </div>
                <div class="info-item">
                    <label>Room Code</label>
                    <p>{{ $classroom->room_code }}</p>
                </div>
                <div class="info-item">
                    <label>Building</label>
                    <p>{{ $classroom->building ?? '—' }}</p>
                </div>
                <div class="info-item">
                    <label>Capacity</label>
                    <p>{{ $classroom->capacity }} students</p>
                </div>
                <div class="info-item">
                    <label>Status</label>
                    <p><span class="badge-{{ $classroom->status }}">{{ ucfirst($classroom->status) }}</span></p>
                </div>
            </div>
            @if($classroom->description)
            <div class="info-item" style="margin-top:16px;">
                <label>Description / Purpose</label>
                <p>{{ $classroom->description }}</p>
            </div>
            @endif
        </div>

        <!-- Schedule Info -->
        <div class="info-section">
            <h3>Schedule Information</h3>
            @if($classroom->schedules->count() > 0)
            <table class="sched-table">
                <thead>
                    <tr>
                        <th>Academic Year</th>
                        <th>Semester</th>
                        <th>Day</th>
                        <th>Room No.</th>
                        <th>Date of Use</th>
                        <th>Time In</th>
                        <th>Time Out</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($classroom->schedules as $sched)
                    <tr>
                        <td>{{ $sched->academic_year }}</td>
                        <td>{{ $sched->semester }}</td>
                        <td>{{ $sched->day }}</td>
                        <td>{{ $sched->room_no }}</td>
                        <td>{{ $sched->date_of_use }}</td>
                        <td>{{ $sched->time_in }}</td>
                        <td>{{ $sched->time_out }}</td>
                        <td>{{ $sched->description ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p style="color:#aaa; text-align:center; padding:20px;">No schedules added yet.</p>
            @endif
        </div>
    </div>

    <!-- QR Code -->
    <div>
        <div class="info-section">
            <h3>Room QR Code</h3>
            <div class="qr-box">
                <div class="qr-code">⬛</div>
                <strong>{{ $classroom->room_code }}</strong>
                <p>Scan to access room information</p>
                <p style="margin-top:8px; font-size:11px; background:#f0f0f0; padding:6px; border-radius:4px; font-family:monospace;">{{ $classroom->room_code }}</p>
            </div>
        </div>
    </div>
</div>
@endsection