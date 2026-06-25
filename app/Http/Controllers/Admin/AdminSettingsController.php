<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlatformSetting;
use Illuminate\Http\Request;

class AdminSettingsController extends Controller
{
    public function index()
    {
        $flags = PlatformSetting::allFlags();
        return view('admin.settings', compact('flags'));
    }

    public function update(Request $request)
    {
        // Unchecked checkboxes are absent from the payload → treat as false.
        foreach (array_keys(PlatformSetting::FLAGS) as $key) {
            PlatformSetting::set($key, $request->boolean("flags.$key"));
        }
        return back()->with('success', 'Feature flags updated.');
    }
}
