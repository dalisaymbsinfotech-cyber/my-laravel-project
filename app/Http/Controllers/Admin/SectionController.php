<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Models\Section;
use App\Models\Department;
use App\Models\College;

class SectionController extends Controller
{
    public function index()
    {
        $sections = Section::orderBy('name')->get();
        $departments = Department::orderBy('name')->get();
        $colleges = College::orderBy('name')->get();
        
        if (request()->wantsJson()) {
            return response()->json($sections);
        }

        return view('admin.sections.sections', compact('sections', 'departments', 'colleges'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'section_code' => 'required|string|unique:sections,section_code',
            'department_id' => 'nullable|exists:departments,id',
            'college_id' => 'nullable|exists:colleges,id',
        ]);
        
        $section = Section::create($request->only(['name', 'section_code', 'department_id', 'college_id']));
        
        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'section' => $section], 201);
        }

        return redirect()->route('admin.sections')->with('success', 'Section added.');
    }

    public function update(Request $request, $id)
    {
        $section = Section::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string',
            'section_code' => 'required|string|unique:sections,section_code,' . $id,
            'department_id' => 'nullable|exists:departments,id',
            'college_id' => 'nullable|exists:colleges,id',
        ]);
        
        $section->update($request->only(['name', 'section_code', 'department_id', 'college_id']));
        
        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'section' => $section]);
        }

        return redirect()->route('admin.sections')->with('success', 'Section updated.');
    }

    public function destroy($id)
    {
        $section = Section::findOrFail($id);
        $section->delete();
        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('admin.sections')->with('success', 'Section removed.');
    }
}
