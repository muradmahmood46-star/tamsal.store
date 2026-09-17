<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Seller;
use App\Models\StoreRequest;
use App\Models\User;
use Illuminate\Http\Request;

class StoreRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('adminlocalize');
    }

    public function index(Request $request)
    {
        $status = $request->status;
        $search = $request->search;

        $query = StoreRequest::with('user')->latest();

        if ($status && in_array($status, ['Pending', 'Approved', 'Rejected'])) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('shop_name', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('cnic', 'like', "%{$search}%")
                  ->orWhere('transaction_id', 'like', "%{$search}%");
            });
        }

        $datas = $query->paginate(20);

        $counts = [
            'all' => StoreRequest::count(),
            'pending' => StoreRequest::where('status', 'Pending')->count(),
            'approved' => StoreRequest::where('status', 'Approved')->count(),
            'rejected' => StoreRequest::where('status', 'Rejected')->count(),
        ];

        return view('back.store_request.index', compact('datas', 'counts', 'status', 'search'));
    }

    public function approve(Request $request, $id)
    {
        $storeRequest = StoreRequest::findOrFail($id);
        $storeRequest->status = 'Approved';
        $storeRequest->seller_status = 'Active';
        $storeRequest->reject_reason = null;
        $storeRequest->save();

        if ($storeRequest->user_id) {
            $user = User::find($storeRequest->user_id);
            if ($user) {
                $user->is_seller = 1;
                $user->is_seller_blocked = 0;
                $user->save();

                Seller::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'shop_name' => $storeRequest->shop_name ?: ($user->first_name . '\'s Store'),
                        'shop_address' => $storeRequest->shop_address ?: $user->ship_address1,
                        'product_types' => $storeRequest->product_types,
                        'shop_phone' => $storeRequest->phone ?: $user->phone,
                        'shop_email' => $storeRequest->email ?: $user->email,
                        'status' => 1
                    ]
                );
            }
        }

        return redirect()->back()->withSuccess(__('Store request approved successfully! User has been upgraded to Seller.'));
    }

    public function blockSeller(Request $request, $id)
    {
        $storeRequest = StoreRequest::findOrFail($id);
        $storeRequest->seller_status = 'Blocked';
        $storeRequest->save();

        if ($storeRequest->user_id) {
            $user = User::find($storeRequest->user_id);
            if ($user) {
                $user->is_seller_blocked = 1;
                $user->save();

                Seller::where('user_id', $user->id)->update(['status' => 0]);

                // Temporarily hide all active products of this seller from public store
                Item::where('vendor_id', $user->id)
                    ->where('status', 1)
                    ->update([
                        'status' => 0,
                        'is_hidden_by_block' => 1
                    ]);
            }
        }

        return redirect()->back()->withSuccess(__('Seller has been blocked successfully. All their products have been hidden from public store and seller dashboard access is suspended.'));
    }

    public function unblockSeller(Request $request, $id)
    {
        $storeRequest = StoreRequest::findOrFail($id);
        $storeRequest->seller_status = 'Active';
        $storeRequest->save();

        if ($storeRequest->user_id) {
            $user = User::find($storeRequest->user_id);
            if ($user) {
                $user->is_seller_blocked = 0;
                $user->save();

                Seller::where('user_id', $user->id)->update(['status' => 1]);

                // Restore previously hidden products back to public store
                Item::where('vendor_id', $user->id)
                    ->where('is_hidden_by_block', 1)
                    ->update([
                        'status' => 1,
                        'is_hidden_by_block' => 0
                    ]);
            }
        }

        return redirect()->back()->withSuccess(__('Seller has been unblocked successfully. All their products have been restored to the public store and seller dashboard access is reinstated.'));
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'reject_reason' => 'nullable|string|max:1000'
        ]);

        $storeRequest = StoreRequest::findOrFail($id);
        $storeRequest->status = 'Rejected';
        $storeRequest->reject_reason = $request->reject_reason;
        $storeRequest->save();

        if ($storeRequest->user_id) {
            $user = User::find($storeRequest->user_id);
            if ($user) {
                $user->is_seller = 0;
                $user->save();
            }
        }

        return redirect()->back()->withSuccess(__('Store request has been rejected.'));
    }

    public function delete($id)
    {
        $storeRequest = StoreRequest::findOrFail($id);
        $storeRequest->delete();

        return redirect()->back()->withSuccess(__('Store request record deleted successfully.'));
    }

    /**
     * Send direct message to store applicant from Store Request Details modal
     */
    public function sendMessage(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string|max:2000'
        ]);

        $storeRequest = StoreRequest::findOrFail($id);
        $userId = $storeRequest->user_id;

        if (!$userId && $storeRequest->email) {
            $user = User::where('email', $storeRequest->email)->first();
            if ($user) {
                $userId = $user->id;
                $storeRequest->user_id = $userId;
                $storeRequest->save();
            }
        }

        if (!$userId) {
            return response()->json(['success' => false, 'message' => __('No linked user account found for this applicant.')], 422);
        }

        $conversation = \App\Models\Conversation::firstOrCreate([
            'user_id' => 0,
            'vendor_id' => $userId,
            'item_id' => null,
        ], [
            'last_message' => __('Direct line with Administration'),
            'last_message_at' => \Carbon\Carbon::now(),
            'user_unread_count' => 0,
            'vendor_unread_count' => 0,
            'deleted_by_user' => 0,
            'deleted_by_vendor' => 0,
        ]);

        $adminId = \Illuminate\Support\Facades\Auth::guard('admin')->id() ?: 0;

        $msg = \App\Models\ChatMessage::create([
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
            'last_message_at' => \Carbon\Carbon::now(),
            'vendor_unread_count' => $conversation->vendor_unread_count + 1,
            'deleted_by_vendor' => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Message sent to store owner successfully!'),
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
     * Fetch direct message history for a store applicant
     */
    public function fetchMessages($id)
    {
        $storeRequest = StoreRequest::findOrFail($id);
        $userId = $storeRequest->user_id;

        if (!$userId && $storeRequest->email) {
            $user = User::where('email', $storeRequest->email)->first();
            if ($user) {
                $userId = $user->id;
            }
        }

        if (!$userId) {
            return response()->json(['success' => true, 'messages' => []]);
        }

        $conversation = \App\Models\Conversation::where('user_id', 0)
            ->where('vendor_id', $userId)
            ->whereNull('item_id')
            ->first();

        if (!$conversation) {
            return response()->json(['success' => true, 'messages' => []]);
        }

        $messages = \App\Models\ChatMessage::where('conversation_id', $conversation->id)
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
