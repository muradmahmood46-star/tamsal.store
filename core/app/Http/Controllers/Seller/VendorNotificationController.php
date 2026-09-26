<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\VendorNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorNotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'seller']);
        VendorNotification::ensureTable();
    }

    /**
     * Return dropdown partial HTML and mark notifications as seen
     */
    public function index()
    {
        $vendorId = Auth::id();
        $notifications = VendorNotification::where('vendor_id', $vendorId)
            ->latest('id')
            ->take(20)
            ->get();

        // Mark unread as read
        VendorNotification::where('vendor_id', $vendorId)
            ->where('is_read', 0)
            ->update([
                'is_read' => 1,
                'read_at' => Carbon::now()
            ]);

        return view('seller.notification.index', compact('notifications'));
    }

    /**
     * Clear all notifications for the vendor
     */
    public function clear()
    {
        $vendorId = Auth::id();
        VendorNotification::where('vendor_id', $vendorId)->delete();

        return response()->json([
            'status' => 'success',
            'message' => __('All notifications cleared successfully.')
        ]);
    }
}
