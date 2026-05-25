<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Enrollment;
use App\Models\Face;
use App\Models\FaceRegistrationLog;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::all();

        $logGroups = FaceRegistrationLog::orderByDesc('id')
            ->get()
            ->groupBy('id_number');

        $students = $students->map(function ($student) use ($logGroups) {
            $logs = $logGroups->get($student->id_number, collect());
            $latestLog = $logs->first();

            $student->registration_count = $logs->count();
            $student->recent_registration_logs = $logs->take(3);
            $student->sort_timestamp = $latestLog?->created_at ?? $student->created_at;

            return $student;
        })->sortByDesc(function ($student) {
            return optional($student->sort_timestamp)->getTimestamp() ?? 0;
        })->values();

        return view('admin.students', compact('students'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_number' => 'required|unique:students',
            'name' => 'required',
            'section' => 'required',
        ]);

        Student::create($request->all());

        return redirect()->route('admin.students')->with('success', 'Student added successfully!');
    }

    public function destroy($id)
    {
        $student = Student::findOrFail($id);

        Enrollment::where('student_id', $student->id_number)->delete();
        Face::where('id_number', $student->id_number)->delete();
        FaceRegistrationLog::where('id_number', $student->id_number)->delete();
        $student->delete();

        return redirect()->route('admin.students')->with('success', 'Student deleted!');
    }

    public function destroyRegistration($id)
    {
        $log = FaceRegistrationLog::findOrFail($id);

        if ($log->enrollment_id) {
            Enrollment::where('id', $log->enrollment_id)->delete();
        } else {
            Enrollment::where('face_registration_log_id', $log->id)->delete();
        }

        $log->delete();

        return redirect()->route('admin.students')->with('success', 'Duplicate registration removed.');
    }
}