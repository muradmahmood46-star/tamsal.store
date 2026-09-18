<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\Conversation;
use App\Models\Item;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BuyerSellerChatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('adminlocalize');
    }

    /**
     * Display all conversations between buyers and 3rd-party sellers
     */
    public function index(Request $request)
    {
        $query = Conversation::with(['item', 'user', 'seller', 'vendor'])
            ->whereNotNull('vendor_id')
            ->where('vendor_id', '>', 0);

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($uq) use ($search) {
                    $uq->where('first_name', 'like', "%{$search}%")
                       ->orWhere('last_name', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%")
                       ->orWhere('phone', 'like', "%{$search}%");
                })
                ->orWhereHas('seller', function($sq) use ($search) {
                    $sq->where('shop_name', 'like', "%{$search}%");
                })
                ->orWhereHas('item', function($iq) use ($search) {
                    $iq->where('name', 'like', "%{$search}%")
                       ->orWhere('sku', 'like', "%{$search}%");
                });
            });
        }

        if ($request->has('vendor_id') && !empty($request->vendor_id)) {
            $query->where('vendor_id', $request->vendor_id);
        }

        $conversations = $query->orderByRaw('COALESCE(last_message_at, updated_at) desc')->get();

        $activeChat = null;
        $messages = collect([]);

        if ($request->has('chat_id') && !empty($request->chat_id)) {
            $activeChat = Conversation::with(['item', 'user', 'seller', 'vendor'])->find($request->chat_id);
        }

        if ($activeChat) {
            // Clear unread count for active chat in DB and in memory
            if ($activeChat->user_id == 0 || $activeChat->user_unread_count > 0 || $activeChat->vendor_unread_count > 0) {
                $activeChat->update([
                    'user_unread_count' => 0,
                    'vendor_unread_count' => 0
                ]);
                $activeChat->user_unread_count = 0;
                $activeChat->vendor_unread_count = 0;

                ChatMessage::where('conversation_id', $activeChat->id)
                    ->where('is_read', 0)
                    ->update(['is_read' => 1]);
            }

            // Also reset in conversations collection so active item has no red badge
            foreach ($conversations as $conv) {
                if ($conv->id == $activeChat->id) {
                    $conv->vendor_unread_count = 0;
                    $conv->user_unread_count = 0;
                }
            }

            $messages = ChatMessage::where('conversation_id', $activeChat->id)
                ->orderBy('id', 'asc')
                ->get();
        }

        $totalBuyerSellerChats = Conversation::whereNotNull('vendor_id')->where('vendor_id', '>', 0)->count();

        // Mark buyer-seller chats as seen by admin
        $now = Carbon::now()->toDateTimeString();
        session(['admin_buyer_seller_last_seen' => $now]);
        \Illuminate\Support\Facades\Cache::put('admin_buyer_seller_last_seen', $now, 60 * 24 * 365);

        return view('back.buyer_seller_chat.index', compact('conversations', 'activeChat', 'messages', 'totalBuyerSellerChats'));
    }

    /**
     * Send Admin intervention/moderator message into buyer-seller chat
     */
    public function send(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|integer',
            'message' => 'required|string|max:1500',
        ]);

        $conversation = Conversation::findOrFail($request->conversation_id);
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

        // Increment unread count for both User and Vendor so both get notified of admin message
        $conversation->update([
            'last_message' => trim($request->message),
            'last_message_at' => Carbon::now(),
            'user_unread_count' => $conversation->user_unread_count + 1,
            'vendor_unread_count' => $conversation->vendor_unread_count + 1,
            'deleted_by_user' => 0,
            'deleted_by_vendor' => 0,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $msg->id,
                    'sender_type' => 'admin',
                    'message' => $msg->message,
                    'time' => $msg->created_at->format('h:i A'),
                    'is_me' => true,
                ]
            ]);
        }

        return redirect()->back()->withSuccess(__('Admin message sent successfully into conversation.'));
    }

    /**
     * Fetch messages for active conversation
     */
    public function fetch(Request $request, $id)
    {
        $conversation = Conversation::findOrFail($id);

        if ($conversation->user_id == 0 || $conversation->user_unread_count > 0 || $conversation->vendor_unread_count > 0) {
            $conversation->update([
                'user_unread_count' => 0,
                'vendor_unread_count' => 0
            ]);
            ChatMessage::where('conversation_id', $conversation->id)
                ->where('is_read', 0)
                ->update(['is_read' => 1]);
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

    /**
     * Delete conversation
     */
    public function delete(Request $request, $id)
    {
        $conversation = Conversation::findOrFail($id);
        ChatMessage::where('conversation_id', $conversation->id)->delete();
        $conversation->delete();

        return redirect()->route('back.buyer_seller_chat.index')->withSuccess(__('Conversation deleted successfully.'));
    }
}