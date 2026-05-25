<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Face;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Enrollment;
use App\Models\FaceRegistrationLog;
use Carbon\Carbon;

class FaceController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'id_number' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'descriptor' => 'required|array|min:1',
            'confirm_overwrite' => 'nullable|boolean',
        ]);

        $incomingDescriptor = array_map('floatval', $request->descriptor);
        $existing = Face::where('id_number', $request->id_number)->first();
        $registrationCount = FaceRegistrationLog::where('id_number', $request->id_number)->count();
        $confirmRaw = $request->input('confirm_overwrite', false);
        $isConfirmOverwrite = in_array($confirmRaw, [true, 1, '1', 'true', 'on'], true);

        if ($registrationCount >= 3) {
            return response()->json([
                'success' => false,
                'limit_reached' => true,
                'message' => 'ID ' . $request->id_number . ' already reached the 3 registration limit.',
            ], 422);
        }

        if ($existing && !$isConfirmOverwrite) {
            return response()->json([
                'success' => false,
                'requires_confirmation' => true,
                'registration_count' => $registrationCount,
                'message' => 'ID ' . $request->id_number . ' is already registered as ' . $existing->name . '. Re-register this ID?',
            ], 409);
        }

        if ($existing) {
            $currentDescriptor = json_decode($existing->descriptor, true);

            if (is_array($currentDescriptor) && count($currentDescriptor) === count($incomingDescriptor)) {
                $selfDistance = $this->euclideanDistance(
                    $incomingDescriptor,
                    array_map('floatval', $currentDescriptor)
                );

                if ($selfDistance < 0.45) {
                    return response()->json([
                        'success' => false,
                        'message' => 'This exact face is already registered under ID ' . $request->id_number . '. Use a different face or stop here.',
                    ], 422);
                }
            }
        }

        foreach (Face::all() as $face) {
            if ($existing && $face->id === $existing->id) {
                continue;
            }

            $savedDescriptor = json_decode($face->descriptor, true);
            if (!is_array($savedDescriptor) || count($savedDescriptor) !== count($incomingDescriptor)) {
                continue;
            }

            $distance = $this->euclideanDistance($incomingDescriptor, array_map('floatval', $savedDescriptor));

            if ($distance < 0.45) {
                return response()->json([
                    'success' => false,
                    'message' => 'This face is already registered under ID ' . $face->id_number . ' (' . $face->name . ').',
                ]);
            }
        }

        if ($existing && $registrationCount === 0) {
            FaceRegistrationLog::create([
                'id_number' => $existing->id_number,
                'name' => $existing->name,
                'face_id' => $existing->id,
            ]);
            $registrationCount = 1;
        }

        if ($existing) {
            $existing->update([
                'name' => $request->name,
                'descriptor' => json_encode($incomingDescriptor),
            ]);
        } else {
            $existing = Face::create([
                'id_number' => $request->id_number,
                'name' => $request->name,
                'descriptor' => json_encode($incomingDescriptor),
            ]);
        }

        $student = Student::firstOrNew(['id_number' => $request->id_number]);
        $student->name = $request->name;
        if (!$student->exists || !$student->section) {
            $student->section = 'Unassigned';
        }
        $student->face_scanned = true;
        $student->save();

        $enrollment = Enrollment::create([
            'student_id' => $student->id_number,
            'student_name' => $student->name,
            'subject_code' => 'FACE-REG',
            'section' => $student->section,
        ]);

        $registrationLog = FaceRegistrationLog::create([
            'id_number' => $request->id_number,
            'name' => $request->name,
            'face_id' => $existing->id,
            'enrollment_id' => $enrollment->id,
        ]);

        $enrollment->update(['face_registration_log_id' => $registrationLog->id]);

        $newRegistrationCount = $registrationCount + 1;

        return response()->json([
            'success' => true,
            'face' => $existing,
            'student_synced' => true,
            'registration_count' => $newRegistrationCount,
            'message' => 'Face registered and linked to student records.',
        ]);
    }

    private function euclideanDistance(array $a, array $b): float
    {
        $sum = 0.0;
        $count = min(count($a), count($b));

        for ($i = 0; $i < $count; $i++) {
            $diff = $a[$i] - $b[$i];
            $sum += $diff * $diff;
        }

        return sqrt($sum);
    }

    public function getFaces()
    {
        $faces = Face::all()->map(function ($face) {
            $face->descriptor = json_decode($face->descriptor, true);
            return $face;
        });

        return response()->json($faces);
    }

    public function logAttendance(Request $request)
    {
        $today = Carbon::today();

        $subject = Subject::where('subject_code', $request->subject_code)->first();

        if (!$subject) {
            return response()->json(['success' => false, 'message' => 'Subject not found!']);
        }

        $student = Student::where('id_number', $request->id_number)->first();

        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Student not found in records!']);
        }

        $existing = Attendance::where('student_id', $student->id)
            ->where('subject_id', $subject->id)
            ->whereDate('attendance_date', $today)
            ->first();

        if ($existing) {
            if (in_array($existing->status, ['on-time', 'late']) && !$existing->time_out) {
                $existing->update(['time_out' => now()->format('H:i:s')]);

                return response()->json([
                    'success' => true,
                    'action' => 'timeout',
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
            'student_id' => $student->id,
            'subject_id' => $subject->id,
            'attendance_date' => $today,
            'time_in' => now()->format('H:i:s'),
            'status' => $status,
        ]);

        $label = $status === 'late' ? '⚠️ LATE' : '✅ ON TIME';

        return response()->json([
            'success' => true,
            'action' => 'timein',
            'message' => $label . ' - Time In recorded for ' . $student->name,
        ]);
    }
}