@extends('layouts.professor')
@section('title', 'Schedule')
@section('content')
<div class="page-header">
    <h2>🗓️ My schedule</h2>
    <p>Class times for your assigned subjects.</p>
</div>

@if(empty($codes))
<div class="alert-error">No subjects linked to your account.</div>
@else
<div class="card">
    <h3>Weekly schedule</h3>
    <table>
        <thead>
            <tr>
                <th>Subject</th>
                <th>Classroom</th>
                <th>Day</th>
                <th>Time in</th>
                <th>Time out</th>
            </tr>
        </thead>
        <tbody>
            @forelse($schedules as $row)
            <tr>
                <td><strong>{{ $row->subject_code }}</strong></td>
                <td>{{ $row->room }}</td>
                <td>{{ $row->day }}</td>
                <td>{{ strlen((string) $row->time_in) >= 5 ? substr((string) $row->time_in, 0, 5) : $row->time_in }}</td>
                <td>{{ strlen((string) $row->time_out) >= 5 ? substr((string) $row->time_out, 0, 5) : $row->time_out }}</td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align:center; color:#aaa; padding:20px;">No schedule rows for your subjects yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endif
@endsection
