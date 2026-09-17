<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\Conversation;
use App\Models\Item;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Initialize or load conversation for a product
     */
    public function init(Request $request)
    {
        $itemId = $request->item_id;
        $item = Item::with('seller', 'user')->find($itemId);

        if (!$item) {
            return response()->json(['success' => false, 'message' => __('Product not found.')], 404);
        }

        if (!Auth::check()) {
            return response()->json([
                'success' => true,
                'auth_required' => true,
                'login_url' => route('user.login'),
                'store_name' => $item->store_name,
                'product' => [
                    'id' => $item->id,
                    'name' => $item->name,
                    'price' => \App\Helpers\PriceHelper::grandCurrencyPrice($item),
                    'photo' => url('/core/public/storage/images/' . $item->photo),
                    'sku' => $item->sku ?: ('#' . $item->id),
                ]
            ]);
        }

        $userId = Auth::id();
        $vendorId = $item->vendor_id ? (int)$item->vendor_id : 0;

        // Find or create conversation
        $conversation = Conversation::firstOrCreate([
            'item_id' => $item->id,
            'user_id' => $userId,
            'vendor_id' => $vendorId,
        ], [
            'last_message' => __('Started conversation about :item', ['item' => $item->name]),
            'last_message_at' => Carbon::now(),
            'user_unread_count' => 0,
            'vendor_unread_count' => 0,
            'deleted_by_user' => 0,
            'deleted_by_vendor' => 0,
        ]);

        // Reopen for user if previously deleted
        if ($conversation->deleted_by_user) {
            $conversation->update(['deleted_by_user' => 0]);
        }

        // Mark user unread count as 0
        if ($conversation->user_unread_count > 0) {
            $conversation->update(['user_unread_count' => 0]);
            ChatMessage::where('conversation_id', $conversation->id)
                ->where('sender_type', '!=', 'user')
                ->where('is_read', 0)
                ->update(['is_read' => 1]);
        }

        $messages = ChatMessage::where('conversation_id', $conversation->id)
            ->where('deleted_by_user', 0)
            ->orderBy('id', 'asc')
            ->get()
            ->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'sender_type' => $msg->sender_type,
                    'message' => $msg->message,
                    'time' => $msg->created_at ? $msg->created_at->format('h:i A') : '',
                    'date' => $msg->created_at ? $msg->created_at->format('M d, Y') : '',
                    'is_me' => ($msg->sender_type === 'user'),
                ];
            });

        $isBlocked = \App\Helpers\ChatPolicyHelper::isUserBlocked(Auth::user());

        return response()->json([
            'success' => true,
            'auth_required' => false,
            'is_chat_blocked' => $isBlocked,
            'chat_blocked_message' => $isBlocked ? __('Your account is blocked from sending chat messages due to policy violations.') : null,
            'conversation_id' => $conversation->id,
            'store_name' => $item->store_name,
            'is_vendor' => ($vendorId > 0),
            'product' => [
                'id' => $item->id,
                'name' => $item->name,
                'price' => \App\Helpers\PriceHelper::grandCurrencyPrice($item),
                'photo' => url('/core/public/storage/images/' . $item->photo),
                'sku' => $item->sku ?: ('#' . $item->id),
            ],
            'messages' => $messages
        ]);
    }

    /**
     * Send message from buyer
     */
    public function send(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => __('Please login to send message.')], 401);
        }

        $request->validate([
            'conversation_id' => 'required|integer',
            'message' => 'required|string|max:1500',
        ]);

        $user = Auth::user();

        // 1. Check if user is already blocked
        if (\App\Helpers\ChatPolicyHelper::isUserBlocked($user)) {
            return response()->json([
                'success' => false,
                'is_blocked' => true,
                'message' => __('Your account is blocked from sending chat messages due to policy violations.')
            ], 403);
        }

        // 2. Check for prohibited phone number / contact exchange
        $text = $request->message;
        if (\App\Helpers\ChatPolicyHelper::containsPhoneNumber($text)) {
            $violation = \App\Helpers\ChatPolicyHelper::handleViolation($user);
            return response()->json([
                'success' => false,
                'policy_violation' => true,
                'is_blocked' => $violation['is_blocked'],
                'warning_count' => $violation['warning_count'],
                'message' => $violation['message']
            ], 422);
        }

        $conversation = Conversation::where('id', $request->conversation_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $msg = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'user',
            'sender_id' => Auth::id(),
            'message' => trim($request->message),
            'is_read' => 0,
            'deleted_by_user' => 0,
            'deleted_by_vendor' => 0,
        ]);

        $conversation->update([
            'last_message' => trim($request->message),
            'last_message_at' => Carbon::now(),
            'vendor_unread_count' => $conversation->vendor_unread_count + 1,
            'deleted_by_vendor' => 0, // Reopen for seller
            'deleted_by_user' => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $msg->id,
                'sender_type' => 'user',
                'message' => $msg->message,
                'time' => $msg->created_at->format('h:i A'),
                'is_me' => true,
            ]
        ]);
    }

    /**
     * Fetch latest messages
     */
    public function fetch(Request $request, $id)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false], 401);
        }

        $conversation = Conversation::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Mark as read
        if ($conversation->user_unread_count > 0) {
            $conversation->update(['user_unread_count' => 0]);
            ChatMessage::where('conversation_id', $conversation->id)
                ->where('sender_type', '!=', 'user')
                ->where('is_read', 0)
                ->update(['is_read' => 1]);
        }

        $messages = ChatMessage::where('conversation_id', $conversation->id)
            ->where('deleted_by_user', 0)
            ->orderBy('id', 'asc')
            ->get()
            ->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'sender_type' => $msg->sender_type,
                    'message' => $msg->message,
                    'time' => $msg->created_at ? $msg->created_at->format('h:i A') : '',
                    'date' => $msg->created_at ? $msg->created_at->format('M d, Y') : '',
                    'is_me' => ($msg->sender_type === 'user'),
                ];
            });

        return response()->json([
            'success' => true,
            'messages' => $messages
        ]);
    }

    /**
     * Delete conversation for buyer
     */
    public function delete(Request $request, $id)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false], 401);
        }

        $conversation = Conversation::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $conversation->update(['deleted_by_user' => 1]);
        ChatMessage::where('conversation_id', $conversation->id)->update(['deleted_by_user' => 1]);

        return response()->json(['success' => true, 'message' => __('Chat deleted successfully.')]);
    }
}
