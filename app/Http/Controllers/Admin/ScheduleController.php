<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\Classroom;
use App\Models\Subject;

class ScheduleController extends Controller
{
    public function index()
    {
        $schedules = Schedule::all();
        $rooms = Classroom::orderBy('room_name')->get();
        $subjects = Subject::all();
        return view('admin.schedules.schedules', compact('schedules', 'rooms', 'subjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'room' => 'required',
            'subject_code' => 'required',
            'day' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
            'time_in' => 'required',
            'time_out' => 'required',
        ]);

        Schedule::create($request->only(['room', 'subject_code', 'day', 'time_in', 'time_out']));

        return redirect()->route('admin.schedules')->with('success', 'Schedule added successfully!');
    }

    public function edit($id)
    {
        $schedule = Schedule::findOrFail($id);
        $rooms = Classroom::orderBy('room_name')->get();
        $subjects = Subject::orderBy('subject_code')->get();
        return view('admin.schedules.edit', compact('schedule', 'rooms', 'subjects'));
    }

    public function update(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);

        $request->validate([
            'room' => 'required',
            'subject_code' => 'required',
            'day' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
            'time_in' => 'required',
            'time_out' => 'required',
        ]);

        $schedule->update($request->only(['room', 'subject_code', 'day', 'time_in', 'time_out']));

        return redirect()->route('admin.schedules')->with('success', 'Schedule updated successfully!');
    }

    public function destroy($id)
    {
        Schedule::findOrFail($id)->delete();
        return redirect()->route('admin.schedules')->with('success', 'Schedule deleted!');
    }
}