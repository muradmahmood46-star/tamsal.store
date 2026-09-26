<?php

namespace App\Http\Controllers\Back;

use App\Helpers\PriceHelper;
use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\Conversation;
use App\Models\FinePayment;
use App\Models\Item;
use App\Models\Seller;
use App\Models\StoreRequest;
use App\Models\StoreUnblockRequest;
use App\Models\User;
use App\Models\VendorTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FineApprovalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('adminlocalize');
    }

    /**
     * Display list of fine payment submissions
     */
    public function index(Request $request)
    {
        $query = FinePayment::with(['user', 'seller', 'unblockRequest'])->latest();

        $status = $request->status;
        if ($status && in_array($status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $status);
        }

        if ($request->has('search') && !empty($request->search)) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('txn_id', 'like', "%{$search}%")
                  ->orWhere('bank_name', 'like', "%{$search}%")
                  ->orWhere('account_name', 'like', "%{$search}%")
                  ->orWhere('account_number', 'like', "%{$search}%")
                  ->orWhereHas('unblockRequest', function ($uq) use ($search) {
                      $uq->where('store_name', 'like', "%{$search}%")
                         ->orWhere('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  })
                  ->orWhereHas('seller', function ($sq) use ($search) {
                      $sq->where('shop_name', 'like', "%{$search}%");
                  });
            });
        }

        $datas = $query->paginate(15);

        $counts = [
            'all' => FinePayment::count(),
            'pending' => FinePayment::where('status', 'pending')->count(),
            'approved' => FinePayment::where('status', 'approved')->count(),
            'rejected' => FinePayment::where('status', 'rejected')->count(),
        ];

        return view('back.fine_approval.index', compact('datas', 'counts', 'status'));
    }

    /**
     * Approve fine payment and immediately unblock store
     */
    public function approve(Request $request, $id)
    {
        $fine = FinePayment::findOrFail($id);
        if ($fine->status === 'approved') {
            return redirect()->back()->withErrors(__('This fine payment is already approved.'));
        }

        $user = User::find($fine->user_id);
        if (!$user) {
            return redirect()->back()->withErrors(__('Associated user account not found.'));
        }

        $adminId = Auth::guard('admin')->id() ?: 0;
        $curr = PriceHelper::adminCurrency();
        $fineAmount = (float)$fine->fine_amount;

        // 1. Unblock User & restore permissions
        $user->is_seller_blocked = 0;
        $user->chat_blocked = 0;
        $user->chat_warnings_count = 0;
        $user->chat_blocked_reason = null;
        $user->save();

        // 2. Restore seller profile
        Seller::where('user_id', $user->id)->update(['status' => 1]);

        // 3. Restore StoreRequest
        StoreRequest::where('user_id', $user->id)->update(['seller_status' => 'Active']);

        // 4. Restore hidden products
        Item::where('vendor_id', $user->id)
            ->where('is_hidden_by_block', 1)
            ->update([
                'status' => 1,
                'is_hidden_by_block' => 0
            ]);

        // 5. Update FinePayment status
        $fine->update([
            'status' => 'approved',
            'approved_at' => Carbon::now(),
            'admin_note' => $request->admin_note ?: __('Fine payment verified and store unblocked.')
        ]);

        // 6. Update StoreUnblockRequest status
        if ($fine->store_unblock_request_id) {
            $unblockReq = StoreUnblockRequest::find($fine->store_unblock_request_id);
            if ($unblockReq) {
                $unblockReq->update([
                    'fine_status' => 'paid',
                    'status' => 'Unblocked',
                    'unblocked_at' => Carbon::now(),
                    'admin_id' => $adminId,
                ]);
            }
        }

        // 7. Update VendorTransaction
        $seller = Seller::where('user_id', $user->id)->first();
        $storeName = $seller ? $seller->shop_name : ($unblockReq->store_name ?? 'Store');
        
        $vTxn = VendorTransaction::where('fine_payment_id', $fine->id)->first();
        if ($vTxn) {
            $vTxn->update([
                'status' => 'completed',
                'details' => __('Fine Payment Approved & Verified (Txn ID: :txnid) - Store Unblocked', ['txnid' => $fine->txn_id])
            ]);
        } else {
            VendorTransaction::create([
                'seller_id' => $seller ? $seller->id : null,
                'user_id' => $user->id,
                'type' => 'fine_payment',
                'amount' => $fineAmount,
                'balance_after' => $seller ? $seller->balance : 0,
                'fine_payment_id' => $fine->id,
                'details' => __('Fine Payment Approved & Verified (Txn ID: :txnid) - Store Unblocked', ['txnid' => $fine->txn_id]),
                'status' => 'completed'
            ]);
        }

        // 8. Post confirmation notice into direct chat
        $conversation = Conversation::firstOrCreate([
            'user_id' => 0,
            'vendor_id' => $user->id,
            'item_id' => null,
        ], [
            'last_message' => __('Fine Payment Approved - Store Unblocked'),
            'last_message_at' => Carbon::now(),
            'user_unread_count' => 0,
            'vendor_unread_count' => 0,
            'deleted_by_user' => 0,
            'deleted_by_vendor' => 0,
        ]);

        $noticeMsg = __('Congratulations! Your fine payment of :curr :amount has been verified and approved by Administration. Your store ":store" has been unblocked, and all product listings have been restored to active status.', [
            'curr' => $curr,
            'amount' => number_format($fineAmount, 2),
            'store' => $storeName
        ]);

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

        \App\Models\VendorNotification::log(
            $user->id,
            'fine_approved',
            __('Fine Payment Approved! Store Unblocked'),
            __('Your fine payment of :curr :amount has been verified and your store ":store" is unblocked.', [
                'curr' => $curr,
                'amount' => number_format($fineAmount, 2),
                'store' => $storeName
            ]),
            route('seller.dashboard'),
            'fas fa-shield-alt',
            'success'
        );

        return redirect()->back()->withSuccess(__('Fine payment approved! Store ":store" has been unblocked and all products restored successfully.', ['store' => $storeName]));
    }

    /**
     * Reject fine payment submission
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'admin_note' => 'nullable|string|max:1000'
        ]);

        $fine = FinePayment::findOrFail($id);
        if ($fine->status === 'approved') {
            return redirect()->back()->withErrors(__('An approved fine payment cannot be rejected.'));
        }

        $reason = trim($request->admin_note ?: __('Invalid or unverified payment proof submitted.'));
        $adminId = Auth::guard('admin')->id() ?: 0;

        // 1. Update FinePayment
        $fine->update([
            'status' => 'rejected',
            'admin_note' => $reason
        ]);

        // 2. Update StoreUnblockRequest (keep blocked)
        if ($fine->store_unblock_request_id) {
            $unblockReq = StoreUnblockRequest::find($fine->store_unblock_request_id);
            if ($unblockReq) {
                $unblockReq->update([
                    'fine_status' => 'rejected',
                    'status' => 'Pending Fine',
                    'admin_id' => $adminId,
                ]);
            }
        }

        // 3. Update VendorTransaction
        $vTxn = VendorTransaction::where('fine_payment_id', $fine->id)->first();
        if ($vTxn) {
            $vTxn->update([
                'status' => 'rejected',
                'details' => __('Fine Payment Proof Rejected: :reason', ['reason' => $reason])
            ]);
        }

        // 4. Post rejection notification in chat
        $conversation = Conversation::firstOrCreate([
            'user_id' => 0,
            'vendor_id' => $fine->user_id,
            'item_id' => null,
        ], [
            'last_message' => __('Fine Payment Proof Rejected'),
            'last_message_at' => Carbon::now(),
            'user_unread_count' => 0,
            'vendor_unread_count' => 0,
            'deleted_by_user' => 0,
            'deleted_by_vendor' => 0,
        ]);

        $rejectionMsg = __('Important: Your fine payment proof has been rejected by Administration.') . "\n\n" . __('Reason: ') . $reason . "\n\n" . __('Please resubmit valid payment proof with correct Transaction ID and screenshot to proceed with unblocking your store.');

        ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'admin',
            'sender_id' => $adminId,
            'message' => $rejectionMsg,
            'is_read' => 0,
            'deleted_by_user' => 0,
            'deleted_by_vendor' => 0,
        ]);

        $conversation->update([
            'last_message' => __('Fine Payment Rejected: ') . $reason,
            'last_message_at' => Carbon::now(),
            'vendor_unread_count' => $conversation->vendor_unread_count + 1,
            'deleted_by_vendor' => 0,
        ]);

        \App\Models\VendorNotification::log(
            $fine->user_id,
            'fine_rejected',
            __('Fine Payment Proof Rejected'),
            __('Your fine payment proof was rejected. Reason: :reason', ['reason' => $reason]),
            route('seller.dashboard'),
            'fas fa-exclamation-triangle',
            'danger'
        );

        return redirect()->back()->withSuccess(__('Fine payment proof rejected. Vendor has been notified to resubmit correct proof.'));
    }
}
