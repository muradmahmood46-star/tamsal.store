<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\VendorAnnouncement;
use App\Models\VendorAnnouncementView;
use Illuminate\Http\Request;

class VendorAnnouncementController extends Controller
{
    /**
     * Display a listing of announcements and the creation form.
     */
    public function index()
    {
        VendorAnnouncement::ensureTable();
        VendorAnnouncementView::ensureTable();

        $announcements = VendorAnnouncement::withCount('views')
            ->latest('id')
            ->paginate(15);

        return view('back.vendor_announcement.index', compact('announcements'));
    }

    /**
     * Store a new announcement for all vendors.
     */
    public function store(Request $request)
    {
        VendorAnnouncement::ensureTable();

        $request->validate([
            'title'      => 'required|string|max:255',
            'message'    => 'required|string',
            'badge_type' => 'nullable|string|in:info,warning,danger,success,primary',
        ]);

        VendorAnnouncement::create([
            'title'      => $request->title,
            'message'    => $request->message,
            'badge_type' => $request->badge_type ?: 'info',
            'status'     => 1,
        ]);

        return redirect()->back()->withSuccess(__('Announcement posted successfully to all vendors.'));
    }

    /**
     * Update an existing announcement.
     */
    public function update(Request $request, $id)
    {
        VendorAnnouncement::ensureTable();

        $request->validate([
            'title'      => 'required|string|max:255',
            'message'    => 'required|string',
            'badge_type' => 'nullable|string|in:info,warning,danger,success,primary',
        ]);

        $announcement = VendorAnnouncement::findOrFail($id);
        $announcement->update([
            'title'      => $request->title,
            'message'    => $request->message,
            'badge_type' => $request->badge_type ?: 'info',
        ]);

        return redirect()->back()->withSuccess(__('Announcement updated successfully.'));
    }

    /**
     * Delete an announcement.
     */
    public function delete($id)
    {
        VendorAnnouncement::ensureTable();
        VendorAnnouncementView::ensureTable();

        $announcement = VendorAnnouncement::findOrFail($id);
        VendorAnnouncementView::where('announcement_id', $id)->delete();
        $announcement->delete();

        return redirect()->back()->withSuccess(__('Announcement deleted successfully.'));
    }
}
