<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\Conversation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'seller']);
    }

    /**
     * Seller messages inbox page
     */
    public function index(Request $request)
    {
        $vendorId = Auth::id();

        $conversations = Conversation::with(['item', 'user'])
            ->where('vendor_id', $vendorId)
            ->where('deleted_by_vendor', 0)
            ->where('user_id', '!=', 0)
            ->orderByRaw('COALESCE(last_message_at, updated_at) desc')
            ->get();

        $activeChat = null;
        $messages = collect([]);

        if ($request->has('chat_id') && !empty($request->chat_id)) {
            $activeChat = $conversations->where('id', $request->chat_id)->first();
        }

        if ($activeChat) {
            // Mark vendor unread as 0
            if ($activeChat->vendor_unread_count > 0) {
                $activeChat->update(['vendor_unread_count' => 0]);
                ChatMessage::where('conversation_id', $activeChat->id)
                    ->where('sender_type', 'user')
                    ->where('is_read', 0)
                    ->update(['is_read' => 1]);
            }
            $activeChat->vendor_unread_count = 0;

            // Update in collection so active chat renders with 0 unread badge
            foreach ($conversations as $c) {
                if ($c->id == $activeChat->id) {
                    $c->vendor_unread_count = 0;
                }
            }

            $messages = ChatMessage::where('conversation_id', $activeChat->id)
                ->where('deleted_by_vendor', 0)
                ->orderBy('id', 'asc')
                ->get();
        }

        $isChatBlocked = \App\Helpers\ChatPolicyHelper::isUserBlocked(Auth::user());

        return view('seller.message.index', compact('conversations', 'activeChat', 'messages', 'isChatBlocked'));
    }

    /**
     * Send reply from seller
     */
    public function send(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|integer',
            'message' => 'required|string|max:1500',
        ]);

        $user = Auth::user();

        // 1. Check if seller is blocked from chat
        if (\App\Helpers\ChatPolicyHelper::isUserBlocked($user)) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'is_blocked' => true,
                    'message' => __('Your account is blocked from sending chat messages due to policy violations.')
                ], 403);
            }
            return redirect()->back()->withErrors(__('Your account is blocked from sending chat messages due to policy violations.'));
        }

        // 2. Check for phone number / contact exchange attempt
        $text = $request->message;
        if (\App\Helpers\ChatPolicyHelper::containsPhoneNumber($text)) {
            $violation = \App\Helpers\ChatPolicyHelper::handleViolation($user);
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'policy_violation' => true,
                    'is_blocked' => $violation['is_blocked'],
                    'warning_count' => $violation['warning_count'],
                    'message' => $violation['message']
                ], 422);
            }
            return redirect()->back()->withErrors($violation['message']);
        }

        $vendorId = $user->id;
        $conversation = Conversation::where('id', $request->conversation_id)
            ->where('vendor_id', $vendorId)
            ->firstOrFail();

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
            'user_unread_count' => $conversation->user_unread_count + 1,
            'deleted_by_user' => 0, // Reopen for buyer
            'deleted_by_vendor' => 0,
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

        return redirect()->back()->withSuccess(__('Message sent successfully.'));
    }

    /**
     * Fetch messages via AJAX for seller
     */
    public function fetch(Request $request, $id)
    {
        $vendorId = Auth::id();
        $conversation = Conversation::where('id', $id)
            ->where('vendor_id', $vendorId)
            ->firstOrFail();

        // Mark as read
        if ($conversation->vendor_unread_count > 0) {
            $conversation->update(['vendor_unread_count' => 0]);
            ChatMessage::where('conversation_id', $conversation->id)
                ->where('sender_type', 'user')
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

    /**
     * Delete conversation for seller
     */
    public function delete(Request $request, $id)
    {
        $vendorId = Auth::id();
        $conversation = Conversation::where('id', $id)
            ->where('vendor_id', $vendorId)
            ->firstOrFail();

        $conversation->update(['deleted_by_vendor' => 1]);
        ChatMessage::where('conversation_id', $conversation->id)->update(['deleted_by_vendor' => 1]);

        return redirect()->route('seller.message.index')->withSuccess(__('Conversation deleted successfully.'));
    }
}
