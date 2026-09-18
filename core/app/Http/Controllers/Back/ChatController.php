<?php

namespace App\Http\Controllers\Back;

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
        $this->middleware('auth:admin');
        $this->middleware('adminlocalize');
    }

    /**
     * Admin messages inbox page - Only direct Customer-to-Admin inquiries
     */
    public function index(Request $request)
    {
        $query = Conversation::with(['item', 'user'])
            ->where('deleted_by_vendor', 0)
            ->where(function($q) {
                $q->whereNull('vendor_id')->orWhere('vendor_id', 0);
            })
            ->where('user_id', '>', 0);

        if ($request->has('search') && !empty($request->search)) {
            $search = trim($request->search);
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($uq) use ($search) {
                    $uq->where('first_name', 'like', "%{$search}%")
                       ->orWhere('last_name', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%")
                       ->orWhere('phone', 'like', "%{$search}%");
                })
                ->orWhereHas('item', function($iq) use ($search) {
                    $iq->where('name', 'like', "%{$search}%");
                });
            });
        }

        $conversations = $query->orderByRaw('COALESCE(last_message_at, updated_at) desc')->get();

        $activeChat = null;
        $messages = collect([]);

        if ($request->has('chat_id') && !empty($request->chat_id)) {
            $activeChat = Conversation::with(['item', 'user'])->where(function($q) {
                $q->whereNull('vendor_id')->orWhere('vendor_id', 0);
            })->find($request->chat_id);
        }

        if ($activeChat) {
            // Mark unread as 0 for this chat in DB
            $activeChat->update([
                'vendor_unread_count' => 0,
            ]);
            $activeChat->vendor_unread_count = 0;

            ChatMessage::where('conversation_id', $activeChat->id)
                ->where('sender_type', 'user')
                ->where('is_read', 0)
                ->update(['is_read' => 1]);

            $activeChat->vendor_unread_count = 0;

            // Also reset in conversations collection so active item has no red badge
            foreach ($conversations as $conv) {
                if ($conv->id == $activeChat->id) {
                    $conv->vendor_unread_count = 0;
                }
            }

            $messages = ChatMessage::where('conversation_id', $activeChat->id)
                ->where('deleted_by_vendor', 0)
                ->orderBy('id', 'asc')
                ->get();
        }

        $directAdminConvIds = $conversations->pluck('id');
        $unreadMessagesCount = ChatMessage::whereIn('conversation_id', $directAdminConvIds)
            ->where('sender_type', 'user')
            ->where('deleted_by_vendor', 0)
            ->where('is_read', 0)
            ->count();

        $convUnreadSum = $conversations->sum('vendor_unread_count');
        $unreadCount = max($unreadMessagesCount, $convUnreadSum);

        return view('back.message.index', compact('conversations', 'activeChat', 'messages', 'unreadCount'));
    }

    /**
     * Send reply from Admin as ORIVO
     */
    public function send(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|integer',
            'message' => 'required|string|max:1500',
        ]);

        $conversation = Conversation::findOrFail($request->conversation_id);

        $adminId = Auth::guard('admin')->id();

        $msg = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'vendor',
            'sender_id' => $adminId ?: 0,
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
     * Fetch messages via AJAX for admin live chat
     */
    public function fetch(Request $request, $id)
    {
        $conversation = Conversation::findOrFail($id);

        // Mark as read
        if ($conversation->vendor_unread_count > 0) {
            $conversation->update([
                'vendor_unread_count' => 0,
            ]);
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
                    'is_me' => ($msg->sender_type === 'vendor' || $msg->sender_type === 'admin'),
                ];
            });

        return response()->json([
            'success' => true,
            'messages' => $messages
        ]);
    }

    /**
     * Delete conversation for admin
     */
    public function delete(Request $request, $id)
    {
        $conversation = Conversation::findOrFail($id);

        $conversation->update(['deleted_by_vendor' => 1]);
        ChatMessage::where('conversation_id', $conversation->id)->update(['deleted_by_vendor' => 1]);

        return redirect()->route('back.message.index')->withSuccess(__('Conversation deleted successfully.'));
    }
}
