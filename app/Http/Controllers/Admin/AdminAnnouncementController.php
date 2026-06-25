<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Company;
use Illuminate\Http\Request;

class AdminAnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::with('targetCompany')->latest()->paginate(20);
        $companies     = Company::orderBy('name')->get(['id', 'name']);
        return view('admin.announcements', compact('announcements', 'companies'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'             => 'required|string|max:140',
            'body'              => 'required|string|max:2000',
            'type'              => 'required|in:INFO,WARNING,MAINTENANCE,FEATURE',
            'target_plan'       => 'nullable|in:STARTER,GROWTH,ENTERPRISE',
            'target_company_id' => 'nullable|exists:companies,id',
            'published_at'      => 'nullable|date',
            'expires_at'        => 'nullable|date|after:published_at',
        ]);
        $data['active']     = true;
        $data['created_by'] = $request->user()->id;

        Announcement::create($data);
        return back()->with('success', 'Announcement published.');
    }

    public function toggle(Announcement $announcement)
    {
        $announcement->update(['active' => ! $announcement->active]);
        return back()->with('success', 'Announcement ' . ($announcement->active ? 'activated.' : 'deactivated.'));
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        return back()->with('success', 'Announcement deleted.');
    }
}
