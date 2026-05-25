<?php

namespace App\Http\Controllers\Admin;

use App\Models\Professor;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ProfessorController extends Controller
{
    protected function syncProfessorAccount(Request $request, ?int $currentUserId): ?int
    {
        if (! $request->filled('login_email')) {
            return $currentUserId;
        }

        $request->validate([
            'login_email' => 'required|email',
            'login_password' => 'nullable|min:6',
            'employee_id' => 'nullable|string|max:100',
            'college' => 'nullable|string|max:255',
        ]);

        $existing = User::where('email', $request->login_email)->first();

        if ($existing) {
            if ($existing->role !== 'professor') {
                throw ValidationException::withMessages([
                    'login_email' => __('This email is already used by another account type.'),
                ]);
            }
            $updates = array_filter([
                'name' => $request->name,
                'employee_id' => $request->employee_id,
                'college' => $request->college,
            ], fn ($v) => $v !== null && $v !== '');
            if ($request->filled('login_password')) {
                $updates['password'] = $request->login_password;
            }
            $existing->update($updates);

            return $existing->id;
        }

        if (! $request->filled('login_password')) {
            throw ValidationException::withMessages([
                'login_password' => __('Password is required when creating a new professor login.'),
            ]);
        }

        return User::create([
            'name' => $request->name,
            'email' => $request->login_email,
            'password' => $request->login_password,
            'role' => 'professor',
            'employee_id' => $request->employee_id,
            'college' => $request->college,
        ])->id;
    }

    public function index()
    {
        $professors = Professor::all();
        $subjects = Subject::all();
        return view('admin.professors', compact('professors', 'subjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'subject_code' => 'required',
            'year_section' => 'required',
        ]);

        $userId = $this->syncProfessorAccount($request, null);

        Professor::create(array_merge(
            $request->only(['name', 'subject_code', 'year_section']),
            ['user_id' => $userId]
        ));

        return redirect()->route('admin.professors')->with('success', 'Professor added successfully!');
    }

    public function edit($id)
    {
        $professor = Professor::with('user')->findOrFail($id);
        $subjects = Subject::orderBy('subject_code')->get();
        return view('admin.professors.edit', compact('professor', 'subjects'));
    }

    public function update(Request $request, $id)
    {
        $professor = Professor::findOrFail($id);

        $request->validate([
            'name' => 'required',
            'subject_code' => 'required',
            'year_section' => 'required',
        ]);

        $userId = $this->syncProfessorAccount($request, $professor->user_id);

        $professor->update(array_merge(
            $request->only(['name', 'subject_code', 'year_section']),
            ['user_id' => $userId]
        ));

        return redirect()->route('admin.professors')->with('success', 'Professor updated successfully!');
    }

    public function destroy($id)
    {
        Professor::findOrFail($id)->delete();
        return redirect()->route('admin.professors')->with('success', 'Professor deleted!');
    }
}
