<?php
namespace App\Http\Controllers\Admin;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Models\College;

class CollegeController extends Controller
{
    public function index()
    {
        $colleges = College::all();
        return view('admin.colleges', compact('colleges'));
    }

    public function store(Request $request)
    {
        $request->validate(['code' => 'required', 'name' => 'required']);
        College::create($request->all());
        return redirect()->route('admin.colleges')->with('success', 'College added!');
    }

    public function update(Request $request, $id)
    {
        College::findOrFail($id)->update($request->all());
        return redirect()->route('admin.colleges')->with('success', 'College updated!');
    }

    public function destroy($id)
    {
        College::findOrFail($id)->delete();
        return redirect()->route('admin.colleges')->with('success', 'College deleted!');
    }
}