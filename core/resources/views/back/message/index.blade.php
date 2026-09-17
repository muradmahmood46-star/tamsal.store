@extends('master.back')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="card mb-3 shadow-sm border-0" style="border-radius: 10px;">
        <div class="card-body py-3">
            <div class="d-sm-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-0 text-dark font-weight-bold">
                        <i class="fas fa-headset text-primary mr-2"></i> {{ __('Customer Live Chats & Support Messages') }}
                    </h4>
                    <p class="text-muted small mb-0">{{ __('Direct official communication between customers and platform administration support.') }}</p>
                </div>
                <div class="mt-2 mt-sm-0">
                    <span class="badge badge-primary px-3 py-2 font-weight-bold" style="font-size: 13px; border-radius: 20px;">
                        <i class="fas fa-headset mr-1"></i> {{ $conversations->count() }} {{ __('Customer Inquiries') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    @include('alerts.alerts')

    <style>
        .admin-chat-container {
            height: calc(100vh - 210px);
            min-height: 550px;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            display: flex;
            border: 1px solid #e2e8f0;
        }
        .admin-chat-sidebar {
            width: 360px;
            min-width: 310px;
            border-right: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            background: #f8fafc;
        }
        .admin-chat-sidebar-header {
            padding: 14px 16px;
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
        }
        .admin-chat-list {
            flex-grow: 1;
            overflow-y: auto;
        }
        .admin-chat-item {
            display: flex;
            align-items: center;
            padding: 8px 12px;
            border-bottom: 1px solid #f1f5f9;
            cursor: pointer;
            transition: all 0.15s ease;
            text-decoration: none !important;
            color: inherit;
            min-height: 58px;
            height: 62px;
        }
        .admin-chat-item:hover {
            background: #f1f5f9;
        }
        .admin-chat-item.active {
            background: #eff6ff;
            border-left: 4px solid #1572e8;
        }
        .admin-chat-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: #1572e8;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14.5px;
            margin-right: 10px;
            flex-shrink: 0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .admin-chat-main {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            background: #efeae2;
        }
        .admin-chat-header {
            padding: 12px 20px;
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
            z-index: 2;
        }
        .admin-chat-product-bar {
            background: #ffffff;
            padding: 10px 20px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 1;
        }
        .admin-chat-product-bar img {
            width: 42px;
            height: 42px;
            object-fit: cover;
            border-radius: 6px;
            margin-right: 12px;
            border: 1px solid #e2e8f0;
        }
        .admin-chat-stream {
            flex-grow: 1;
            overflow-y: auto;
            padding: 18px 22px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            background-color: #efeae2;
            background-image: radial-gradient(#d1d7db 0.8px, transparent 0.8px);
            background-size: 16px 16px;
        }
        .bubble-buyer {
            align-self: flex-start;
            background: #ffffff;
            color: #111b21;
            border-radius: 0 12px 12px 12px;
            max-width: 72%;
            padding: 8px 14px 6px 14px;
            font-size: 13.5px;
            line-height: 1.45;
            box-shadow: 0 1px 2px rgba(0,0,0,0.12);
            border-left: 3px solid #0d6efd;
        }
        .bubble-seller {
            align-self: flex-end;
            background: #d9fdd3;
            color: #111b21;
            border-radius: 12px 0 12px 12px;
            max-width: 72%;
            padding: 8px 14px 6px 14px;
            font-size: 13.5px;
            line-height: 1.45;
            box-shadow: 0 1px 2px rgba(0,0,0,0.12);
            border-right: 3px solid #008069;
        }
        .bubble-meta {
            font-size: 10.5px;
            color: #64748b;
            margin-top: 3px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 3px;
        }
        .admin-chat-footer {
            padding: 12px 18px;
            background: #f0f2f5;
            border-top: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .admin-chat-input {
            flex-grow: 1;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 24px;
            padding: 10px 18px;
            font-size: 14px;
            outline: none;
            transition: all 0.2s;
        }
        .admin-chat-input:focus {
            border-color: #1572e8;
            box-shadow: 0 0 0 3px rgba(21, 114, 232, 0.15);
        }
        .admin-chat-send {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #1572e8;
            color: #ffffff;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            transition: all 0.2s;
            flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(21, 114, 232, 0.4);
        }
        .admin-chat-send:hover {
            background: #0d56b3;
            transform: scale(1.05);
        }
        .admin-quick-replies {
            display: flex;
            gap: 6px;
            padding: 6px 18px;
            background: #e9ecef;
            border-top: 1px solid #dee2e6;
            overflow-x: auto;
        }
        .admin-quick-btn {
            background: #ffffff;
            border: 1px solid #ced4da;
            color: #495057;
            font-size: 11.5px;
            padding: 3px 10px;
            border-radius: 12px;
            white-space: nowrap;
            cursor: pointer;
        }
        .admin-quick-btn:hover {
            background: #1572e8;
            color: #ffffff;
            border-color: #1572e8;
        }
    </style>

    <div class="admin-chat-container">
        <!-- Sidebar Conversations -->
        <div class="admin-chat-sidebar">
            <div class="admin-chat-sidebar-header">
                <input type="text" id="admin_search_chats" class="form-control form-control-sm" placeholder="{{ __('Search customer or product...') }}" onkeyup="filterConversations()">
            </div>
            <div class="admin-chat-list" id="admin_conversations_list">
                @forelse($conversations as $conv)
                    @php
                        $isActive = ($activeChat && $activeChat->id == $conv->id);
                        $buyerName = $conv->user ? trim($conv->user->first_name . ' ' . $conv->user->last_name) : __('Customer');
                    @endphp
                    <a href="{{ route('back.message.index', ['chat_id' => $conv->id]) }}" class="admin-chat-item {{ $isActive ? 'active' : '' }}" data-search="{{ strtolower($buyerName . ' ' . ($conv->item ? $conv->item->name : '')) }}">
                        <div class="admin-chat-avatar">
                            {{ strtoupper(substr($buyerName, 0, 1)) }}
                        </div>
                        <div style="flex-grow: 1; overflow: hidden; min-width: 0;">
                            <!-- Row 1: Badges & Timestamp -->
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div class="d-flex align-items-center" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 230px;">
                                    <span class="badge badge-primary px-2 py-1 text-white mr-1 text-truncate shadow-sm" style="font-size: 11px; font-weight: 700; border-radius: 4px; max-width: 140px;">
                                        <i class="fas fa-user mr-1"></i>{{ $buyerName }}
                                    </span>
                                    <span class="text-muted mx-1 font-weight-bold" style="font-size: 11px;">↔</span>
                                    <span class="badge badge-dark px-2 py-1 text-white ml-1 shadow-sm" style="font-size: 11px; font-weight: 700; border-radius: 4px;">
                                        <i class="fas fa-shield-alt mr-1"></i>{{ __('Admin') }}
                                    </span>
                                </div>
                                <small class="text-muted" style="font-size: 10px; flex-shrink: 0; margin-left: 4px;">
                                    {{ $conv->last_message_at ? $conv->last_message_at->diffForHumans(null, true) : '' }}
                                </small>
                            </div>

                            <!-- Row 2: Message preview & Unread badge -->
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted" style="font-size: 11.5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 235px; line-height: 1.2;">
                                    @if($conv->item && $conv->item->name)
                                        <span class="text-primary font-weight-bold mr-1" style="font-size: 10.5px;">[{{ $conv->item->name }}]</span>
                                    @endif
                                    {{ $conv->last_message ?: __('No messages') }}
                                </span>
                                @if(!$isActive && $conv->vendor_unread_count > 0)
                                    <span class="badge badge-success font-weight-bold ml-1" style="font-size: 10px; border-radius: 10px; padding: 2px 7px; flex-shrink: 0;">{{ $conv->vendor_unread_count }}</span>
                                @endif
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="text-center py-5 text-muted small">
                        <i class="fas fa-comments fa-3x mb-2 d-block text-secondary"></i>
                        {{ __('No customer support conversations found.') }}
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Chat Main Pane -->
        <div class="admin-chat-main">
            @if($activeChat)
                @php
                    $activeBuyer = $activeChat->user ? trim($activeChat->user->first_name . ' ' . $activeChat->user->last_name) : __('Customer');
                @endphp
                <!-- Header -->
                <div class="admin-chat-header">
                    <div class="d-flex align-items-center">
                        <div class="admin-chat-avatar mr-3">
                            {{ strtoupper(substr($activeBuyer, 0, 1)) }}
                        </div>
                        <div>
                            <div class="d-flex align-items-center flex-wrap">
                                <span class="badge badge-primary text-white px-3 py-2 shadow-sm" style="font-size: 13px; font-weight: 700; border-radius: 6px;">
                                    <i class="fas fa-user mr-1"></i> {{ $activeBuyer }}
                                </span>
                                <span class="text-secondary mx-2 font-weight-bold" style="font-size: 17px;">↔</span>
                                <span class="badge badge-dark text-white px-3 py-2 shadow-sm" style="font-size: 13px; font-weight: 700; border-radius: 6px;">
                                    <i class="fas fa-shield-alt mr-1"></i> {{ __('Platform Administration Support') }}
                                </span>
                            </div>
                            <div class="small text-muted mt-1">
                                <span><i class="fas fa-envelope mr-1 text-primary"></i> {{ $activeChat->user ? $activeChat->user->email : 'N/A' }}</span>
                                @if($activeChat->user && $activeChat->user->phone)
                                    <span class="mx-2">|</span>
                                    <span><i class="fas fa-phone mr-1 text-success"></i> {{ $activeChat->user->phone }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('back.message.delete', $activeChat->id) }}" class="btn btn-outline-danger btn-sm" onclick="return confirm('{{ __('Are you sure you want to delete this chat conversation?') }}')">
                            <i class="fas fa-trash-alt mr-1"></i> {{ __('Delete') }}
                        </a>
                    </div>
                </div>

                <!-- Product Info Bar -->
                @if($activeChat->item && $activeChat->item->name)
                    <div class="admin-chat-product-bar">
                        <div class="d-flex align-items-center">
                            <img src="{{ url('/core/public/storage/images/' . $activeChat->item->photo) }}" alt="{{ $activeChat->item->name }}">
                            <div>
                                <strong class="text-dark d-block" style="font-size: 13.5px;">{{ $activeChat->item->name }}</strong>
                                <span class="text-success font-weight-bold">{{ PriceHelper::grandCurrencyPrice($activeChat->item) }}</span>
                                <span class="badge badge-light border ml-2" style="font-size: 10.5px;">{{ $activeChat->item->sku ? '#' . $activeChat->item->sku : 'ID #' . $activeChat->item->id }}</span>
                            </div>
                        </div>
                        <a href="{{ route('front.product', $activeChat->item->slug) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-external-link-alt mr-1"></i> {{ __('View Product') }}
                        </a>
                    </div>
                @endif

                <!-- Messages Stream -->
                <div class="admin-chat-stream" id="admin_messages_container">
                    @forelse($messages as $msg)
                        @php
                            $isMe = ($msg->sender_type === 'vendor' || $msg->sender_type === 'admin');
                        @endphp
                        <div class="{{ $isMe ? 'bubble-seller' : 'bubble-buyer' }}">
                            <div style="font-size: 11px; font-weight: 700; color: {{ $isMe ? '#166534' : '#0d6efd' }}; margin-bottom: 2px;">
                                {{ $isMe ? __('Platform Admin Support') : $activeBuyer }}
                            </div>
                            <div style="white-space: pre-line;">{{ $msg->message }}</div>
                            <div class="bubble-meta">
                                <span>{{ $msg->created_at ? $msg->created_at->format('h:i A') : '' }}</span>
                                @if($isMe)
                                    <span style="color: #53bdeb; margin-left: 3px; font-weight: bold;">✓✓</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted my-auto">
                            <i class="fas fa-comments fa-3x text-secondary mb-2 d-block"></i>
                            {{ __('No messages yet in this conversation.') }}
                        </div>
                    @endforelse
                </div>

                <!-- Quick Replies -->
                <div class="admin-quick-replies">
                    <button type="button" class="admin-quick-btn" onclick="sendAdminQuick('{{ __('Hello! Yes, this item is in stock and ready to dispatch.') }}')">
                        <i class="fas fa-check-circle mr-1 text-success"></i> {{ __('In stock & ready') }}
                    </button>
                    <button type="button" class="admin-quick-btn" onclick="sendAdminQuick('{{ __('Delivery typically takes 2-4 working days across Pakistan.') }}')">
                        <i class="fas fa-shipping-fast mr-1 text-primary"></i> {{ __('2-4 Days Delivery') }}
                    </button>
                    <button type="button" class="admin-quick-btn" onclick="sendAdminQuick('{{ __('Thank you for contacting customer support. How may we assist you today?') }}')">
                        <i class="fas fa-headset mr-1 text-info"></i> {{ __('How can we help?') }}
                    </button>
                </div>

                <!-- Input Footer -->
                <div class="admin-chat-footer">
                    <input type="text" id="admin_message_input" class="admin-chat-input" placeholder="{{ __('Type reply to customer...') }}" onkeypress="handleAdminInputKey(event)">
                    <button type="button" class="admin-chat-send" id="admin_send_btn" onclick="sendAdminMessage()" title="{{ __('Send Reply') }}">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            @else
                <div class="text-center py-5 my-auto text-muted">
                    <i class="fas fa-comments fa-4x text-secondary mb-3 d-block"></i>
                    <h5 class="font-weight-bold text-dark">{{ __('Select a Customer Conversation') }}</h5>
                    <p class="small">{{ __('Select any conversation from the left panel to read and reply.') }}</p>
                </div>
            @endif
        </div>
    </div>
</div>

@if($activeChat)
<script>
    const activeConversationId = {{ $activeChat->id }};
    const adminChatFetchUrl = "{{ route('back.message.fetch', $activeChat->id) }}";
    const adminChatSendUrl = "{{ route('back.message.send') }}";
    const csrfToken = "{{ csrf_token() }}";
    let pollInterval = null;

    function scrollToBottom() {
        const c = document.getElementById('admin_messages_container');
        if (c) c.scrollTop = c.scrollHeight;
    }

    scrollToBottom();

    function fetchAdminMessages() {
        fetch(adminChatFetchUrl, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success && data.messages) {
                renderAdminMessages(data.messages);
            }
        })
        .catch(e => console.error('Poll error', e));
    }

    function renderAdminMessages(messages) {
        const c = document.getElementById('admin_messages_container');
        if (!c) return;

        let html = '';
        messages.forEach(msg => {
            const isMe = msg.is_me;
            const bubbleClass = isMe ? 'bubble-seller' : 'bubble-buyer';
            const ticks = isMe ? '<span style="color:#53bdeb; margin-left:3px; font-weight:bold;">✓✓</span>' : '';
            const senderTag = isMe 
                ? '<div style="font-size:11px; font-weight:700; color:#166534; margin-bottom:2px;">{{ __("Platform Admin Support") }}</div>' 
                : '<div style="font-size:11px; font-weight:700; color:#0d6efd; margin-bottom:2px;">{{ $activeBuyer }}</div>';

            html += `
                <div class="${bubbleClass}">
                    ${senderTag}
                    <div style="white-space:pre-line;">${escapeHtml(msg.message)}</div>
                    <div class="bubble-meta">
                        <span>${msg.time}</span>
                        ${ticks}
                    </div>
                </div>
            `;
        });
        c.innerHTML = html;
    }

    function sendAdminMessage() {
        const input = document.getElementById('admin_message_input');
        const text = input.value.trim();
        if (!text) return;

        input.value = '';

        // Optimistic UI append
        const c = document.getElementById('admin_messages_container');
        const now = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        c.insertAdjacentHTML('beforeend', `
            <div class="bubble-seller" style="opacity:0.85;">
                <div style="font-size:11px; font-weight:700; color:#166534; margin-bottom:2px;">{{ __("Platform Admin Support") }}</div>
                <div style="white-space:pre-line;">${escapeHtml(text)}</div>
                <div class="bubble-meta">
                    <span>${now}</span>
                    <span class="text-muted"><i class="fas fa-clock" style="font-size:10px;"></i></span>
                </div>
            </div>
        `);
        scrollToBottom();

        fetch(adminChatSendUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                conversation_id: activeConversationId,
                message: text
            })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                fetchAdminMessages();
            }
        })
        .catch(e => console.error(e));
    }

    function sendAdminQuick(text) {
        document.getElementById('admin_message_input').value = text;
        sendAdminMessage();
    }

    function handleAdminInputKey(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            sendAdminMessage();
        }
    }

    function filterConversations() {
        const query = document.getElementById('admin_search_chats').value.toLowerCase();
        const items = document.querySelectorAll('.admin-chat-item');
        items.forEach(item => {
            const data = item.getAttribute('data-search') || '';
            item.style.display = data.includes(query) ? 'flex' : 'none';
        });
    }

    function escapeHtml(t) {
        const m = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return t.replace(/[&<>"']/g, function(k) { return m[k]; });
    }

    // Auto poll every 3.5s
    pollInterval = setInterval(fetchAdminMessages, 3500);
</script>
@endif
@endsection