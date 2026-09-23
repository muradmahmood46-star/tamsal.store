<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\VendorAnnouncement;
use App\Models\VendorAnnouncementView;
use Illuminate\Support\Facades\Auth;

class VendorAnnouncementController extends Controller
{
    /**
     * Display all announcements posted by admin for vendors.
     * Automatically marks all current announcements as viewed by the vendor.
     */
    public function index()
    {
        VendorAnnouncement::ensureTable();
        VendorAnnouncementView::ensureTable();

        $announcements = VendorAnnouncement::where('status', 1)
            ->latest('id')
            ->paginate(12);

        $vendorId = Auth::id();
        if ($vendorId && $announcements->count() > 0) {
            $activeIds = $announcements->pluck('id')->toArray();
            $alreadyViewedIds = VendorAnnouncementView::where('vendor_id', $vendorId)
                ->whereIn('announcement_id', $activeIds)
                ->pluck('announcement_id')
                ->toArray();

            $newViews = [];
            $now = now();
            foreach ($activeIds as $announcementId) {
                if (!in_array($announcementId, $alreadyViewedIds)) {
                    $newViews[] = [
                        'vendor_id'       => $vendorId,
                        'announcement_id' => $announcementId,
                        'created_at'      => $now,
                        'updated_at'      => $now,
                    ];
                }
            }

            if (!empty($newViews)) {
                VendorAnnouncementView::insert($newViews);
            }
        }

        return view('seller.announcement.index', compact('announcements'));
    }
}
