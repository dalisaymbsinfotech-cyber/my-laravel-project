<?php
namespace App\Http\Controllers\Admin;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\College;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::with('college')->get();
        $colleges = College::all();
        return view('admin.departments', compact('departments', 'colleges'));
    }

    public function store(Request $request)
    {
        $request->validate(['code' => 'required', 'name' => 'required']);
        Department::create($request->all());
        return redirect()->route('admin.departments')->with('success', 'Department added!');
    }

    public function update(Request $request, $id)
    {
        Department::findOrFail($id)->update($request->all());
        return redirect()->route('admin.departments')->with('success', 'Department updated!');
    }

    public function destroy($id)
    {
        Department::findOrFail($id)->delete();
        return redirect()->route('admin.departments')->with('success', 'Department deleted!');
    }
}