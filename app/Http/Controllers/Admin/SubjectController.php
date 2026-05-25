<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Models\Subject;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::all();
        return view('admin.subjects.subjects', compact('subjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject_code' => 'required',
            'subject_name' => 'required',
            'professor_name' => 'required',
            'section' => 'required',
        ]);

        Subject::create($request->only(['subject_code', 'subject_name', 'professor_name', 'section']));

        return redirect()->route('admin.subjects')->with('success', 'Subject added successfully!');
    }

    public function edit($id)
    {
        $subject = Subject::findOrFail($id);
        return view('admin.subjects.edit', compact('subject'));
    }

    public function update(Request $request, $id)
    {
        $subject = Subject::findOrFail($id);

        $request->validate([
            'subject_code' => 'required',
            'subject_name' => 'required',
            'professor_name' => 'required',
            'section' => 'required',
        ]);

        $subject->update($request->only(['subject_code', 'subject_name', 'professor_name', 'section']));

        return redirect()->route('admin.subjects')->with('success', 'Subject updated successfully!');
    }

    public function destroy($id)
    {
        Subject::findOrFail($id)->delete();
        return redirect()->route('admin.subjects')->with('success', 'Subject deleted!');
    }
}