<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::first();
        if (!$settings) {
            $settings = Setting::create([
                'company_name' => 'EARIST',
                'system_name' => 'School Admin System',
                'admin_username' => 'admin',
                'admin_password' => Hash::make('admin123'),
            ]);
        }
        return view('admin.settings', compact('settings'));
    }

    public function updateSystem(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'system_name' => 'required|string|max:255',
        ]);

        $settings = Setting::firstOrFail();

        if ($request->hasFile('logo')) {
            $request->validate(['logo' => 'image|mimes:png,jpg,jpeg,webp|max:2048']);
            if ($settings->logo_path) {
                Storage::disk('public')->delete($settings->logo_path);
            }
            $logoPath = $request->file('logo')->store('logos', 'public');
            $settings->update(['logo_path' => $logoPath]);
        }

        $settings->update([
            'company_name' => $request->company_name,
            'system_name' => $request->system_name,
        ]);

        return redirect()->route('admin.settings')->with('success_system', 'System settings updated!');
    }

    public function updateCredentials(Request $request)
    {
        $request->validate([
            'admin_username' => 'required',
            'admin_password' => 'required|min:6|confirmed',
        ]);
        $settings = Setting::first();
        $settings->update([
            'admin_username' => $request->admin_username,
            'admin_password' => Hash::make($request->admin_password),
        ]);

        return redirect()->route('admin.settings')->with('success_credentials', 'Credentials updated!');
    }
}