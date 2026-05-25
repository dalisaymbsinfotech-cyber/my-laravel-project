<?php
namespace App\Http\Controllers\Admin;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Models\Classroom;
use App\Models\ClassroomSchedule;

class ClassroomController extends Controller
{
    public function index()
    {
        $classrooms = Classroom::withCount('schedules')->get();
        return view('admin.classrooms.index', compact('classrooms'));
    }

    public function create()
    {
        return view('admin.classrooms.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'room_name' => 'required',
            'room_code' => 'required|unique:classrooms',
            'capacity' => 'required|numeric',
        ]);
        Classroom::create($request->all());
        return redirect()->route('admin.classrooms')->with('success', 'Classroom added!');
    }

    public function show($id)
    {
        $classroom = Classroom::with('schedules')->findOrFail($id);
        return view('admin.classrooms.show', compact('classroom'));
    }

    public function edit($id)
    {
        $classroom = Classroom::with('schedules')->findOrFail($id);
        return view('admin.classrooms.edit', compact('classroom'));
    }

    public function update(Request $request, $id)
    {
        $classroom = Classroom::findOrFail($id);
        $classroom->update($request->except(['schedule', '_token', '_method']));

        if ($request->has('schedule')) {
            foreach ($request->schedule as $sched) {
                if (!empty($sched['day'])) {
                    ClassroomSchedule::create([
                        'classroom_id' => $classroom->id,
                        'academic_year' => $sched['academic_year'],
                        'semester' => $sched['semester'],
                        'day' => $sched['day'],
                        'room_no' => $sched['room_no'],
                        'date_of_use' => $sched['date_of_use'],
                        'time_in' => $sched['time_in'],
                        'time_out' => $sched['time_out'],
                        'description' => $sched['description'] ?? null,
                    ]);
                }
            }
        }

        return redirect()->route('admin.classrooms')->with('success', 'Classroom updated!');
    }

    public function destroy($id)
    {
        Classroom::findOrFail($id)->delete();
        return redirect()->route('admin.classrooms')->with('success', 'Classroom deleted!');
    }
}