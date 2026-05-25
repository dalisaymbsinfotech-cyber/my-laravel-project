<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Face;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\Subject;
use Carbon\Carbon;

class FaceController extends Controller
{
    // Register a face with ID number
    public function register(Request $request)
    {
        $existing = Face::where('id_number', $request->id_number)->first();
        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'ID Number ' . $request->id_number . ' is already registered under ' . $existing->name
            ]);
        }

        $face = Face::create([
            'id_number' => $request->id_number,
            'name'      => $request->name,
            'descriptor' => json_encode($request->descriptor),
        ]);

        // Mark student as face_scanned if they exist in students table
        Student::where('id_number', $request->id_number)
            ->update(['face_scanned' => true]);

        return response()->json(['success' => true, 'face' => $face]);
    }

    // Return all registered face descriptors
    public function getFaces()
    {
        $faces = Face::all()->map(function ($face) {
            $face->descriptor = json_decode($face->descriptor);
            return $face;
        });

        return response()->json($faces);
    }

    // Log attendance via face recognition
    public function logAttendance(Request $request)
    {
        $today = Carbon::today();

        // Find the subject by subject_code from the schedule
        $subject = Subject::where('subject_code', $request->subject_code)->first();

        if (!$subject) {
            return response()->json(['success' => false, 'message' => 'Subject not found!']);
        }

        // Find the student record
        $student = Student::where('id_number', $request->id_number)->first();

        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Student not found in records!']);
        }

        // Check for existing attendance today
        $existing = Attendance::where('student_id', $student->id)
            ->where('subject_id', $subject->id)
            ->whereDate('attendance_date', $today)
            ->first();

        if ($existing) {
            // If already timed in but no time out yet, record time out
            if (in_array($existing->status, ['on-time', 'late']) && !$existing->time_out) {
                $existing->update(['time_out' => now()->format('H:i:s')]);
                return response()->json([
                    'success' => true,
                    'action'  => 'timeout',
                    'message' => 'Time Out recorded for ' . $student->name,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $student->name . ' has already completed attendance today!',
            ]);
        }

        $status = $request->attendance_status === 'late' ? 'late' : 'on-time';

        Attendance::create([
            'student_id'      => $student->id,
            'subject_id'      => $subject->id,
            'attendance_date' => $today,
            'time_in'         => now()->format('H:i:s'),
            'status'          => $status,
        ]);

        $label = $status === 'late' ? '⚠️ LATE' : '✅ ON TIME';

        return response()->json([
            'success' => true,
            'action'  => 'timein',
            'message' => $label . ' - Time In recorded for ' . $student->name,
        ]);
    }
}