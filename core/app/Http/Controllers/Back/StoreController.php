<?php

namespace App\Http\Controllers\Back;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\Conversation;
use App\Models\Item;
use App\Models\Order;
use App\Models\Seller;
use App\Models\StoreRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoreController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('adminlocalize');
    }

    /**
     * Display all marketplace stores / vendors.
     */
    public function index(Request $request)
    {

        $status = $request->status;
        $search = $request->search;

        $query = Seller::with('user')->latest();

        if ($status !== null && $status !== '') {
            $query->where('status', (int)$status);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('shop_name', 'like', "%{$search}%")
                  ->orWhere('shop_email', 'like', "%{$search}%")
                  ->orWhere('shop_phone', 'like', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        $sellers = $query->paginate(20);

        // Append product & order counts safely
        foreach ($sellers as $seller) {
            try {
                $vendorId = $seller->user_id;
                $seller->total_products_count = Item::where('vendor_id', $vendorId)->count();
            } catch (\Throwable $e) {
                $seller->total_products_count = 0;
            }

            try {
                $vendorId = $seller->user_id;
                $seller->total_orders_count = Order::where('vendor_id', $vendorId)->count();
                $seller->pending_orders_count = Order::where('vendor_id', $vendorId)->where('order_status', 'Pending')->count();
            } catch (\Throwable $e) {
                $seller->total_orders_count = 0;
                $seller->pending_orders_count = 0;
            }
        }

        try {
            $counts = [
                'all' => Seller::count(),
                'active' => Seller::where('status', 1)->count(),
                'blocked' => Seller::where('status', 0)->count(),
            ];
        } catch (\Throwable $e) {
            $counts = [
                'all' => 0,
                'active' => 0,
                'blocked' => 0,
            ];
        }

        return view('back.store.index', compact('sellers', 'counts', 'status', 'search'));
    }

    /**
     * Login as Store (Admin Impersonation into Seller Panel).
     */
    public function loginAs($id)
    {
        $seller = Seller::findOrFail($id);
        $user = $seller->user;

        if (!$user) {
            $user = User::find($seller->user_id);
        }

        if (!$user) {
            return redirect()->back()->withErrors(__('Associated user account for this store could not be found.'));
        }

        // Ensure user is seller flag is active
        if ($user->is_seller != 1) {
            $user->is_seller = 1;
            $user->save();
        }

        $admin = Auth::guard('admin')->user();

        // Store impersonation metadata in session
        session([
            'admin_impersonating_vendor' => true,
            'admin_id' => $admin ? $admin->id : 1,
            'admin_name' => $admin ? $admin->name : 'Admin',
            'impersonated_store_id' => $seller->id,
            'impersonated_store_name' => $seller->shop_name,
            'impersonated_vendor_id' => $user->id
        ]);

        // Login as the user on the web guard
        Auth::guard('web')->login($user);

        return redirect()->route('seller.dashboard')->withSuccess(
            __('Logged in as store ":store". You now have full access to this vendor dashboard. You can return to Admin Panel anytime via the top bar.', ['store' => $seller->shop_name])
        );
    }

    /**
     * Toggle Store Active/Blocked status.
     */
    public function status(Request $request, $id, $status)
    {
        $seller = Seller::findOrFail($id);
        $seller->status = (int)$status;
        $seller->save();

        if ($seller->user_id) {
            $user = User::find($seller->user_id);
            if ($user) {
                $user->is_seller_blocked = ($status == 1) ? 0 : 1;
                $user->save();

                // If blocking, hide items; if unblocking, restore items
                if ($status == 0) {
                    Item::where('vendor_id', $user->id)->where('status', 1)->update([
                        'status' => 0,
                        'is_hidden_by_block' => 1
                    ]);
                    StoreRequest::where('user_id', $user->id)->update(['seller_status' => 'Blocked']);
                } else {
                    Item::where('vendor_id', $user->id)->where('is_hidden_by_block', 1)->update([
                        'status' => 1,
                        'is_hidden_by_block' => 0
                    ]);
                    StoreRequest::where('user_id', $user->id)->update(['seller_status' => 'Active']);
                }
            }
        }

        $msg = ($status == 1) ? __('Store activated successfully.') : __('Store blocked successfully.');
        return redirect()->back()->withSuccess($msg);
    }

    /**
     * Delete Store.
     */
    public function destroy($id)
    {
        $seller = Seller::findOrFail($id);
        $shopName = $seller->shop_name;
        $userId = $seller->user_id;

        if ($userId) {
            $user = User::find($userId);
            if ($user) {
                $user->is_seller = 0;
                $user->save();
            }
        }

        $seller->delete();

        return redirect()->back()->withSuccess(__('Store ":store" deleted successfully.', ['store' => $shopName]));
    }

    /**
     * Send direct message to vendor from Store Details view/modal
     */
    public function sendMessage(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string|max:2000'
        ]);

        $seller = Seller::findOrFail($id);
        $userId = $seller->user_id;

        if (!$userId && $seller->shop_email) {
            $user = User::where('email', $seller->shop_email)->first();
            if ($user) {
                $userId = $user->id;
                $seller->user_id = $userId;
                $seller->save();
            }
        }

        if (!$userId) {
            return response()->json(['success' => false, 'message' => __('No linked user account found for this store.')], 422);
        }

        $conversation = Conversation::firstOrCreate([
            'user_id' => 0,
            'vendor_id' => $userId,
            'item_id' => null,
        ], [
            'last_message' => __('Direct line with Administration'),
            'last_message_at' => Carbon::now(),
            'user_unread_count' => 0,
            'vendor_unread_count' => 0,
            'deleted_by_user' => 0,
            'deleted_by_vendor' => 0,
        ]);

        $adminId = Auth::guard('admin')->id() ?: 0;

        $msg = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'admin',
            'sender_id' => $adminId,
            'message' => trim($request->message),
            'is_read' => 0,
            'deleted_by_user' => 0,
            'deleted_by_vendor' => 0,
        ]);

        $conversation->update([
            'last_message' => trim($request->message),
            'last_message_at' => Carbon::now(),
            'vendor_unread_count' => $conversation->vendor_unread_count + 1,
            'deleted_by_vendor' => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Message sent to :store successfully!', ['store' => $seller->shop_name]),
            'data' => [
                'id' => $msg->id,
                'message' => $msg->message,
                'time' => $msg->created_at->format('h:i A'),
                'date' => $msg->created_at->format('M d, Y'),
                'sender_type' => 'admin',
            ]
        ]);
    }

    /**
     * Fetch direct message history for a store
     */
    public function fetchMessages($id)
    {
        $seller = Seller::findOrFail($id);
        $userId = $seller->user_id;

        if (!$userId && $seller->shop_email) {
            $user = User::where('email', $seller->shop_email)->first();
            if ($user) {
                $userId = $user->id;
            }
        }

        if (!$userId) {
            return response()->json(['success' => true, 'messages' => []]);
        }

        $conversation = Conversation::where('user_id', 0)
            ->where('vendor_id', $userId)
            ->whereNull('item_id')
            ->first();

        if (!$conversation) {
            return response()->json(['success' => true, 'messages' => []]);
        }

        $messages = ChatMessage::where('conversation_id', $conversation->id)
            ->orderBy('id', 'asc')
            ->get()
            ->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'sender_type' => $msg->sender_type,
                    'message' => $msg->message,
                    'time' => $msg->created_at ? $msg->created_at->format('h:i A') : '',
                    'date' => $msg->created_at ? $msg->created_at->format('M d, Y') : '',
                    'is_admin' => ($msg->sender_type === 'admin'),
                ];
            });

        return response()->json([
            'success' => true,
            'messages' => $messages
        ]);
    }
}