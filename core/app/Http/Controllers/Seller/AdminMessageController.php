<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\Conversation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMessageController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'seller']);
    }

    /**
     * Seller direct chat with Admin
     */
    public function index(Request $request)
    {
        $vendorId = Auth::id();

        // Find or create direct Admin-to-Seller conversation
        $conversation = Conversation::firstOrCreate([
            'user_id' => 0,
            'vendor_id' => $vendorId,
            'item_id' => null,
        ], [
            'last_message' => __('Direct line with Administration & Support'),
            'last_message_at' => Carbon::now(),
            'user_unread_count' => 0,
            'vendor_unread_count' => 0,
            'deleted_by_user' => 0,
            'deleted_by_vendor' => 0,
        ]);

        // Mark vendor unread count as 0
        if ($conversation->vendor_unread_count > 0) {
            $conversation->update(['vendor_unread_count' => 0]);
            ChatMessage::where('conversation_id', $conversation->id)
                ->where('sender_type', '!=', 'vendor')
                ->where('is_read', 0)
                ->update(['is_read' => 1]);
        }

        $messages = ChatMessage::where('conversation_id', $conversation->id)
            ->where('deleted_by_vendor', 0)
            ->orderBy('id', 'asc')
            ->get();

        return view('seller.admin_message.index', compact('conversation', 'messages'));
    }

    /**
     * Send message from Seller directly to Admin
     */
    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $vendorId = Auth::id();

        $conversation = Conversation::firstOrCreate([
            'user_id' => 0,
            'vendor_id' => $vendorId,
            'item_id' => null,
        ], [
            'last_message' => __('Direct line with Administration'),
            'last_message_at' => Carbon::now(),
            'user_unread_count' => 0,
            'vendor_unread_count' => 0,
            'deleted_by_user' => 0,
            'deleted_by_vendor' => 0,
        ]);

        $msg = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'vendor',
            'sender_id' => $vendorId,
            'message' => trim($request->message),
            'is_read' => 0,
            'deleted_by_user' => 0,
            'deleted_by_vendor' => 0,
        ]);

        $conversation->update([
            'last_message' => trim($request->message),
            'last_message_at' => Carbon::now(),
            'user_unread_count' => $conversation->user_unread_count + 1, // Admin unread count
            'deleted_by_user' => 0,
            'deleted_by_vendor' => 0,
        ]);

        // If seller has a store unblock request, mark is_seen = 0 so admin gets immediate notification count
        StoreUnblockRequest::where('user_id', $vendorId)->update([
            'is_seen' => 0,
            'message' => trim($request->message),
            'status' => 'Pending',
            'admin_seen_at' => null,
            'updated_at' => Carbon::now()
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $msg->id,
                    'sender_type' => 'vendor',
                    'message' => $msg->message,
                    'time' => $msg->created_at->format('h:i A'),
                    'is_me' => true,
                ]
            ]);
        }

        return redirect()->back()->withSuccess(__('Message sent to Admin successfully.'));
    }

    /**
     * Fetch latest messages with Admin
     */
    public function fetch(Request $request)
    {
        $vendorId = Auth::id();

        $conversation = Conversation::where('user_id', 0)
            ->where('vendor_id', $vendorId)
            ->whereNull('item_id')
            ->first();

        if (!$conversation) {
            return response()->json(['success' => true, 'messages' => []]);
        }

        // Mark vendor unread count as 0
        if ($conversation->vendor_unread_count > 0) {
            $conversation->update(['vendor_unread_count' => 0]);
            ChatMessage::where('conversation_id', $conversation->id)
                ->where('sender_type', '!=', 'vendor')
                ->where('is_read', 0)
                ->update(['is_read' => 1]);
        }

        $messages = ChatMessage::where('conversation_id', $conversation->id)
            ->where('deleted_by_vendor', 0)
            ->orderBy('id', 'asc')
            ->get()
            ->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'sender_type' => $msg->sender_type,
                    'message' => $msg->message,
                    'time' => $msg->created_at ? $msg->created_at->format('h:i A') : '',
                    'date' => $msg->created_at ? $msg->created_at->format('M d, Y') : '',
                    'is_me' => ($msg->sender_type === 'vendor'),
                ];
            });

        return response()->json([
            'success' => true,
            'messages' => $messages
        ]);
    }
}