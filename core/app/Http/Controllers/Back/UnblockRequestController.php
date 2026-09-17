<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\Conversation;
use App\Models\Item;
use App\Models\Seller;
use App\Models\StoreRequest;
use App\Models\StoreUnblockRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UnblockRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('adminlocalize');
    }

    /**
     * Display all store unblock requests
     */
    public function index(Request $request)
    {
        $query = StoreUnblockRequest::with(['user', 'seller', 'admin'])->latest();

        $status = $request->status;
        if ($status && in_array($status, ['Pending', 'Pending Fine', 'Replied', 'Unblocked'])) {
            $query->where('status', $status);
        }

        if ($request->has('search') && !empty($request->search)) {
            $search = trim($request->search);
            $query->where(function($q) use ($search) {
                $q->where('store_name', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $requests = $query->paginate(15);

        $counts = [
            'all' => StoreUnblockRequest::count(),
            'pending' => StoreUnblockRequest::where('status', 'Pending')->count(),
            'pending_fine' => StoreUnblockRequest::where('status', 'Pending Fine')->count(),
            'unseen' => StoreUnblockRequest::where('is_seen', 0)->where('status', 'Pending')->count(),
            'replied' => StoreUnblockRequest::where('status', 'Replied')->count(),
            'unblocked' => StoreUnblockRequest::where('status', 'Unblocked')->count(),
        ];

        return view('back.unblock_request.index', compact('requests', 'counts', 'status'));
    }

    /**
     * Send reply from Admin to blocked store owner
     */
    public function reply(Request $request, $id)
    {
        $request->validate([
            'reply' => 'required|string|max:3000'
        ]);

        $unblockReq = StoreUnblockRequest::findOrFail($id);
        $adminId = Auth::guard('admin')->id() ?: 0;

        $unblockReq->update([
            'admin_reply' => trim($request->reply),
            'admin_replied_at' => Carbon::now(),
            'admin_id' => $adminId,
            'status' => $unblockReq->status === 'Unblocked' ? 'Unblocked' : 'Replied',
        ]);

        // Sync to direct Conversation so vendor gets notification in chat too
        $conversation = Conversation::firstOrCreate([
            'user_id' => 0,
            'vendor_id' => $unblockReq->user_id,
            'item_id' => null,
        ], [
            'last_message' => __('Administration Support Reply on Store Appeal'),
            'last_message_at' => Carbon::now(),
            'user_unread_count' => 0,
            'vendor_unread_count' => 0,
            'deleted_by_user' => 0,
            'deleted_by_vendor' => 0,
        ]);

        ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'admin',
            'sender_id' => $adminId,
            'message' => 'Official Admin Reply on Unblock Appeal: ' . trim($request->reply),
            'is_read' => 0,
            'deleted_by_user' => 0,
            'deleted_by_vendor' => 0,
        ]);

        $conversation->update([
            'last_message' => 'Admin Reply: ' . trim($request->reply),
            'last_message_at' => Carbon::now(),
            'vendor_unread_count' => $conversation->vendor_unread_count + 1,
            'deleted_by_vendor' => 0,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Reply sent to store owner successfully!'),
                'admin_reply' => $unblockReq->admin_reply,
                'admin_replied_at' => $unblockReq->admin_replied_at->format('M d, Y h:i A')
            ]);
        }

        return redirect()->back()->withSuccess(__('Reply sent to store owner successfully!'));
    }

    /**
     * Unblock Store & restore full seller access
     */
    public function unblock(Request $request, $id)
    {
        $unblockReq = StoreUnblockRequest::findOrFail($id);
        $user = User::find($unblockReq->user_id);

        if (!$user) {
            return redirect()->back()->withErrors(__('Associated user account not found.'));
        }

        // Unblock user
        $user->is_seller_blocked = 0;
        $user->chat_blocked = 0;
        $user->chat_warnings_count = 0;
        $user->chat_blocked_reason = null;
        $user->save();

        // Restore seller profile status
        Seller::where('user_id', $user->id)->update(['status' => 1]);

        // Restore StoreRequest status
        StoreRequest::where('user_id', $user->id)->update(['seller_status' => 'Active']);

        // Restore hidden products
        Item::where('vendor_id', $user->id)
            ->where('is_hidden_by_block', 1)
            ->update([
                'status' => 1,
                'is_hidden_by_block' => 0
            ]);

        $adminId = Auth::guard('admin')->id() ?: 0;

        $unblockData = [
            'status' => 'Unblocked',
            'unblocked_at' => Carbon::now(),
            'admin_id' => $adminId,
        ];

        if ($request->has('reply') && !empty($request->reply)) {
            $unblockData['admin_reply'] = trim($request->reply);
            $unblockData['admin_replied_at'] = Carbon::now();
        }

        $unblockReq->update($unblockData);

        // Notify in direct line
        $conversation = Conversation::firstOrCreate([
            'user_id' => 0,
            'vendor_id' => $unblockReq->user_id,
            'item_id' => null,
        ], [
            'last_message' => __('Store Unblocked by Administration'),
            'last_message_at' => Carbon::now(),
            'user_unread_count' => 0,
            'vendor_unread_count' => 0,
            'deleted_by_user' => 0,
            'deleted_by_vendor' => 0,
        ]);

        $noticeMsg = __('Good news! Your store ":store" has been reviewed and unblocked by Administration. Full seller dashboard access and products have been restored.', ['store' => $unblockReq->store_name]);
        if (!empty($request->reply)) {
            $noticeMsg .= "\n\n" . __('Admin Note: ') . trim($request->reply);
        }

        ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'admin',
            'sender_id' => $adminId,
            'message' => $noticeMsg,
            'is_read' => 0,
            'deleted_by_user' => 0,
            'deleted_by_vendor' => 0,
        ]);

        $conversation->update([
            'last_message' => $noticeMsg,
            'last_message_at' => Carbon::now(),
            'vendor_unread_count' => $conversation->vendor_unread_count + 1,
            'deleted_by_vendor' => 0,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Store has been unblocked successfully! Seller dashboard and products are now fully active.')
            ]);
        }

        return redirect()->back()->withSuccess(__('Store has been unblocked successfully! Seller dashboard and products are now fully active.'));
    }

    /**
     * Re-block store if needed
     */
    public function reblock(Request $request, $id)
    {
        $unblockReq = StoreUnblockRequest::findOrFail($id);
        $user = User::find($unblockReq->user_id);

        $reason = trim($request->reason ?: $request->reply ?: $request->message ?: '');

        if ($user) {
            $user->is_seller_blocked = 1;
            if (!empty($reason)) {
                $user->chat_blocked_reason = $reason;
            }
            $user->save();

            Seller::where('user_id', $user->id)->update(['status' => 0]);

            // Hide products
            Item::where('vendor_id', $user->id)
                ->where('status', 1)
                ->update([
                    'status' => 0,
                    'is_hidden_by_block' => 1
                ]);

            StoreRequest::where('user_id', $user->id)->update(['seller_status' => 'Blocked']);
        }

        $updateData = [
            'status' => 'Pending',
            'unblocked_at' => null,
        ];
        if (!empty($reason)) {
            $updateData['admin_reply'] = $reason;
            $updateData['admin_replied_at'] = Carbon::now();
        }
        $unblockReq->update($updateData);

        // Also post notice into direct chat conversation
        $adminId = Auth::guard('admin')->id() ?: 0;
        $conversation = Conversation::firstOrCreate([
            'user_id' => 0,
            'vendor_id' => $unblockReq->user_id,
            'item_id' => null,
        ], [
            'last_message' => __('Store Blocked by Administration'),
            'last_message_at' => Carbon::now(),
            'user_unread_count' => 0,
            'vendor_unread_count' => 0,
            'deleted_by_user' => 0,
            'deleted_by_vendor' => 0,
        ]);

        $blockMsg = __('Your store ":store" has been blocked by Administration.', ['store' => $unblockReq->store_name]);
        if (!empty($reason)) {
            $blockMsg .= "\n\n" . __('Block Reason: ') . $reason;
        }

        ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'admin',
            'sender_id' => $adminId,
            'message' => $blockMsg,
            'is_read' => 0,
            'deleted_by_user' => 0,
            'deleted_by_vendor' => 0,
        ]);

        $conversation->update([
            'last_message' => $blockMsg,
            'last_message_at' => Carbon::now(),
            'vendor_unread_count' => $conversation->vendor_unread_count + 1,
            'deleted_by_vendor' => 0,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Store has been blocked successfully.')
            ]);
        }

        return redirect()->back()->withSuccess(__('Store has been blocked successfully.'));
    }

    /**
     * Impose fine on blocked store to unblock
     */
    public function imposeFine(Request $request, $id)
    {
        $request->validate([
            'fine_amount' => 'required|numeric|min:1',
            'reason' => 'nullable|string|max:2000'
        ]);

        $unblockReq = StoreUnblockRequest::findOrFail($id);
        $adminId = Auth::guard('admin')->id() ?: 0;
        $fineAmount = (float) $request->fine_amount;
        $curr = \App\Helpers\PriceHelper::adminCurrency();

        $unblockReq->update([
            'fine_amount' => $fineAmount,
            'fine_status' => 'pending',
            'fine_imposed_at' => Carbon::now(),
            'status' => 'Pending Fine',
            'admin_id' => $adminId,
            'is_seen' => 1,
            'admin_seen_at' => Carbon::now(),
        ]);

        // Post official fine notice into direct line
        $conversation = Conversation::firstOrCreate([
            'user_id' => 0,
            'vendor_id' => $unblockReq->user_id,
            'item_id' => null,
        ], [
            'last_message' => __('Fine Imposed by Administration'),
            'last_message_at' => Carbon::now(),
            'user_unread_count' => 0,
            'vendor_unread_count' => 0,
            'deleted_by_user' => 0,
            'deleted_by_vendor' => 0,
        ]);

        $fineNotice = "To unblock your store, a fine of {$curr} " . number_format($fineAmount, 2) . " has been imposed by the system. Please pay the fine to continue operating your store.";
        if ($request->has('reason') && !empty($request->reason)) {
            $fineNotice .= "\n\n" . __('Admin Note: ') . trim($request->reason);
        }

        ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'admin',
            'sender_id' => $adminId,
            'message' => $fineNotice,
            'is_read' => 0,
            'deleted_by_user' => 0,
            'deleted_by_vendor' => 0,
        ]);

        $conversation->update([
            'last_message' => "Fine Imposed: {$curr} " . number_format($fineAmount, 2),
            'last_message_at' => Carbon::now(),
            'vendor_unread_count' => $conversation->vendor_unread_count + 1,
            'deleted_by_vendor' => 0,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Fine of :curr :amount imposed successfully! Notification sent to store owner.', ['curr' => $curr, 'amount' => number_format($fineAmount, 2)]),
                'fine_amount' => $fineAmount,
                'fine_status' => 'pending',
                'status' => 'Pending Fine',
            ]);
        }

        return redirect()->back()->withSuccess(__('Fine of :curr :amount imposed successfully! Notification sent to store owner.', ['curr' => $curr, 'amount' => number_format($fineAmount, 2)]));
    }

    /**
     * Fetch complete chat history for a store unblock request
     */
    public function fetchChat($id)
    {
        $unblockReq = StoreUnblockRequest::with(['user', 'seller'])->findOrFail($id);

        // Mark as seen by admin
        $unblockReq->update([
            'is_seen' => 1,
            'admin_seen_at' => Carbon::now(),
        ]);

        $conversation = Conversation::where('user_id', 0)->where('vendor_id', $unblockReq->user_id)->first();

        $messages = [];
        if ($conversation) {
            $conversation->update(['user_unread_count' => 0]);
            ChatMessage::where('conversation_id', $conversation->id)
                ->where('sender_type', '!=', 'admin')
                ->where('is_read', 0)
                ->update(['is_read' => 1]);

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
        }

        $remainingUnseen = StoreUnblockRequest::where('is_seen', 0)->where('status', 'Pending')->count();

        return response()->json([
            'success' => true,
            'remaining_unseen' => $remainingUnseen,
            'request' => [
                'id' => $unblockReq->id,
                'store_name' => $unblockReq->store_name,
                'store_url' => $unblockReq->store_url,
                'first_name' => $unblockReq->first_name,
                'last_name' => $unblockReq->last_name,
                'full_name' => $unblockReq->full_name,
                'email' => $unblockReq->email,
                'phone' => $unblockReq->phone,
                'message' => $unblockReq->message,
                'fine_amount' => $unblockReq->fine_amount,
                'fine_status' => $unblockReq->fine_status,
                'fine_imposed_at' => $unblockReq->fine_imposed_at ? $unblockReq->fine_imposed_at->format('M d, Y h:i A') : null,
                'admin_reply' => $unblockReq->admin_reply,
                'admin_replied_at' => $unblockReq->admin_replied_at ? $unblockReq->admin_replied_at->format('M d, Y h:i A') : null,
                'status' => $unblockReq->status,
                'is_seen' => 1,
                'is_seller_blocked' => $unblockReq->user ? $unblockReq->user->is_seller_blocked : 0,
                'created_at' => $unblockReq->created_at->format('M d, Y h:i A'),
            ],
            'messages' => $messages
        ]);
    }

    /**
     * Delete unblock request record
     */
    public function delete($id)
    {
        $unblockReq = StoreUnblockRequest::findOrFail($id);
        $unblockReq->delete();

        return redirect()->back()->withSuccess(__('Unblock request record deleted successfully.'));
    }
}
