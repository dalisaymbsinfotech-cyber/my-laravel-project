@extends('layouts.app')
@section('title', 'Attendance Report')
@section('content')
<div class="page-header">
    <h2>📊 Attendance Report</h2>
    <p>View and filter attendance records.</p>
</div>

<div class="card">
    <h3>Filter Records</h3>
    <form method="GET">
        <input type="date" name="date" value="{{ request('date') }}" />
        <input type="text" name="subject" placeholder="Subject Code" value="{{ request('subject') }}" />
        <input type="text" name="section" placeholder="Section" value="{{ request('section') }}" />
        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="{{ route('admin.attendance') }}" class="btn btn-primary" style="background:#aaa;">Reset</a>
    </form>
</div>

<div class="card">
    <h3>Attendance Records</h3>
    <table>
        <thead>
            <tr>
                <th>ID Number</th>
                <th>Name</th>
                <th>Subject</th>
                <th>Section</th>
                <th>Time In</th>
                <th>Time Out</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="7" style="text-align:center; color:#aaa; padding:20px;">No records found.</td>
            </tr>
        </tbody>
    </table>
</div>
@endsection