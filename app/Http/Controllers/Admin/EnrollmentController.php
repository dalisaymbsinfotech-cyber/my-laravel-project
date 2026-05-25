<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Models\Enrollment;
use App\Models\Face;
use App\Models\FaceRegistrationLog;
use App\Models\Setting;
use App\Models\Student;
use App\Models\Subject;

class EnrollmentController extends Controller
{
    public function index()
    {
        $enrollments = Enrollment::latest()->get();
        $students = Student::orderBy('name')->get();
        $subjects = Subject::all();
        $sections = \App\Models\Section::orderBy('name')->pluck('name')->all();
        if (empty($sections)) {
            $sections = [
                '11-A', '11-B', '12-A', '12-B', '1st Year', '2nd Year', '3rd Year', '4th Year'
            ];
        }

        return view('admin.enrollment', compact('enrollments', 'students', 'subjects', 'sections'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required',
            'student_name' => 'required',
            'subject_code' => 'required',
            'section' => 'required',
        ]);

        Enrollment::updateOrCreate(
            [
                'student_id' => $request->student_id,
                'subject_code' => $request->subject_code,
            ],
            [
                'student_name' => $request->student_name,
                'section' => $request->section,
            ]
        );

        // Keep the Student record in sync with the enrollment's section/name
        $student = Student::where('id_number', $request->student_id)->first();
        if ($student) {
            $student->update([
                'name' => $request->student_name,
                'section' => $request->section,
            ]);
        }

        return redirect()->route('admin.enrollment')->with('success', 'Student enrolled successfully!');
    }

    public function destroy($id)
    {
        $enrollment = Enrollment::findOrFail($id);

        if ($enrollment->subject_code === 'FACE-REG') {
            if ($enrollment->face_registration_log_id) {
                FaceRegistrationLog::where('id', $enrollment->face_registration_log_id)->delete();
            } else {
                FaceRegistrationLog::where('enrollment_id', $enrollment->id)->delete();
            }

            $enrollment->delete();

            return redirect()->route('admin.enrollment')->with('success', 'Face registration record removed.');
        }

        $enrollment->delete();

        return redirect()->route('admin.enrollment')->with('success', 'Enrollment removed!');
    }
}