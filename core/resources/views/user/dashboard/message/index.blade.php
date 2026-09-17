@extends('master.front')

@section('title')
    {{ __('My Messages & Chats') }}
@endsection

@section('content')
<!-- Page Title-->
<div class="page-title">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <ul class="breadcrumbs">
                    <li><a href="{{ route('front.index') }}">{{ __('Home') }}</a></li>
                    <li class="separator"></li>
                    <li>{{ __('My Chats with Stores') }}</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
    .user-chat-box {
        height: 600px;
        background: #ffffff;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        display: flex;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }
    .user-chat-sidebar {
        width: 300px;
        border-right: 1px solid #e2e8f0;
        display: flex;
        flex-direction: column;
        background: #f8fafc;
    }
    .user-chat-list {
        flex-grow: 1;
        overflow-y: auto;
    }
    .user-chat-item {
        display: flex;
        align-items: center;
        padding: 12px 14px;
        border-bottom: 1px solid #f1f5f9;
        cursor: pointer;
        text-decoration: none !important;
        color: inherit;
        transition: all 0.15s ease;
    }
    .user-chat-item:hover {
        background: #f1f5f9;
    }
    .user-chat-item.active {
        background: #eff6ff;
        border-left: 4px solid #008069;
    }
    .user-chat-avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: #008069;
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 16px;
        margin-right: 10px;
        flex-shrink: 0;
    }
    .user-chat-main {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        background: #efeae2;
    }
    .user-chat-header {
        padding: 12px 18px;
        background: #ffffff;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .user-chat-product-banner {
        background: #ffffff;
        padding: 8px 16px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 13px;
    }
    .user-chat-product-banner img {
        width: 38px;
        height: 38px;
        object-fit: cover;
        border-radius: 6px;
        margin-right: 10px;
        border: 1px solid #e2e8f0;
    }
    .user-chat-stream {
        flex-grow: 1;
        overflow-y: auto;
        padding: 16px 20px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        background-color: #efeae2;
        background-image: radial-gradient(#d1d7db 0.8px, transparent 0.8px);
        background-size: 16px 16px;
    }
    .user-bubble {
        max-width: 75%;
        padding: 8px 14px;
        font-size: 14px;
        line-height: 1.4;
        box-shadow: 0 1px 2px rgba(0,0,0,0.12);
        position: relative;
    }
    .user-bubble-me {
        align-self: flex-end;
        background: #d9fdd3;
        color: #111b21;
        border-radius: 10px 0 10px 10px;
    }
    .user-bubble-other {
        align-self: flex-start;
        background: #ffffff;
        color: #111b21;
        border-radius: 0 10px 10px 10px;
    }
    .user-bubble-meta {
        font-size: 10.5px;
        color: #64748b;
        text-align: right;
        margin-top: 3px;
    }
    .user-chat-footer {
        padding: 10px 16px;
        background: #f0f2f5;
        border-top: 1px solid #e2e8f0;
        display: flex;
        gap: 10px;
        align-items: center;
    }
    .user-chat-input {
        flex-grow: 1;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 20px;
        padding: 9px 18px;
        font-size: 14px;
        outline: none;
    }
    .user-chat-input:focus {
        border-color: #008069;
    }
    .user-chat-send {
        background: #008069;
        color: #ffffff;
        border: none;
        border-radius: 50%;
        width: 42px;
        height: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        cursor: pointer;
        flex-shrink: 0;
    }
</style>

<div class="container padding-bottom-3x mb-1">
    <div class="row">
        @include('includes.user_sitebar')
        <div class="col-lg-8">
            <div class="user-chat-box">
                <!-- Sidebar Conversations -->
                <div class="user-chat-sidebar">
                    <div class="p-3 bg-white border-bottom">
                        <h6 class="mb-0 font-weight-bold text-dark"><i class="fas fa-store text-success mr-1"></i> {{ __('Store Chats') }}</h6>
                    </div>
                    <div class="user-chat-list">
                        @forelse($conversations as $conv)
                            @php
                                $isActive = ($activeChat && $activeChat->id == $conv->id);
                            @endphp
                            <a href="{{ route('user.message.index', ['chat_id' => $conv->id]) }}" class="user-chat-item {{ $isActive ? 'active' : '' }}">
                                <div class="user-chat-avatar">
                                    <i class="fas fa-store"></i>
                                </div>
                                <div style="flex-grow: 1; overflow: hidden;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong style="font-size: 13.5px;" class="text-dark">{{ $conv->store_name }}</strong>
                                        <small class="text-muted" style="font-size: 10.5px;">{{ $conv->last_message_at ? $conv->last_message_at->diffForHumans(null, true) : '' }}</small>
                                    </div>
                                    @if($conv->item && $conv->item->name)
                                        <div style="font-size: 11.5px; color: #0d6efd; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            <i class="fas fa-box mr-1"></i> {{ $conv->item->name }}
                                        </div>
                                    @endif
                                    <div class="d-flex justify-content-between align-items-center mt-1">
                                        <span class="text-muted" style="font-size: 12px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 190px;">{{ $conv->last_message ?: __('No messages') }}</span>
                                        @if(!$isActive && $conv->user_unread_count > 0)
                                            <span class="badge badge-success font-weight-bold ml-1" style="font-size: 10px; border-radius: 10px; padding: 2px 7px; flex-shrink: 0;">{{ $conv->user_unread_count }}</span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="text-center py-5 text-muted small">
                                <i class="fas fa-comments fa-3x mb-2 d-block text-secondary"></i>
                                {{ __('No chats yet. Visit any product page to message a store seller!') }}
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Chat Main Pane -->
                <div class="user-chat-main">
                    @if($activeChat)
                        <!-- Header -->
                        <div class="user-chat-header">
                            <div class="d-flex align-items-center">
                                <div class="user-chat-avatar">
                                    <i class="fas fa-store"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 font-weight-bold text-dark">
                                        {{ $activeChat->store_name }}
                                        <span class="badge badge-success ml-1 py-1 px-2 font-weight-normal" style="font-size: 11px;">
                                            <i class="fas fa-check-circle mr-1"></i>{{ ($activeChat->vendor_id > 0) ? __('Verified Store') : __('Official Store') }}
                                        </span>
                                    </h6>
                                    <small class="text-muted">{{ __('Direct Chat with Store Seller') }}</small>
                                </div>
                            </div>
                            <div>
                                <a href="{{ route('user.message.delete', $activeChat->id) }}" class="btn btn-outline-danger btn-sm" onclick="return confirm('{{ __('Are you sure you want to delete this chat?') }}')">
                                    <i class="fas fa-trash-alt mr-1"></i> {{ __('Delete') }}
                                </a>
                            </div>
                        </div>

                        <!-- Product info banner -->
                        @if($activeChat->item && $activeChat->item->name)
                            <div class="user-chat-product-banner">
                                <div class="d-flex align-items-center">
                                    <img src="{{ url('/core/public/storage/images/' . $activeChat->item->photo) }}" alt="{{ $activeChat->item->name }}">
                                    <div>
                                        <strong class="text-dark d-block" style="font-size: 13px;">{{ $activeChat->item->name }}</strong>
                                        <span class="text-success font-weight-bold">{{ PriceHelper::grandCurrencyPrice($activeChat->item) }}</span>
                                    </div>
                                </div>
                                <a href="{{ route('front.product', $activeChat->item->slug) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-external-link-alt mr-1"></i> {{ __('View Item') }}
                                </a>
                            </div>
                        @endif

                        <!-- Messages Stream -->
                        <div class="user-chat-stream" id="user_messages_container">
                            @forelse($messages as $msg)
                                @php
                                    $isMe = ($msg->sender_type === 'user');
                                    $isAdmin = ($msg->sender_type === 'admin');
                                @endphp
                                <div class="user-bubble {{ $isMe ? 'user-bubble-me' : 'user-bubble-other' }}" style="{{ $isAdmin ? 'border-left: 3px solid #4f46e5;' : '' }}">
                                    @if($isAdmin)
                                        <div style="font-size: 11px; font-weight: 700; color: #4f46e5; margin-bottom: 2px;">
                                            <i class="fas fa-user-shield mr-1"></i> {{ __('Administration / Support') }}
                                        </div>
                                    @endif
                                    <div>{{ $msg->message }}</div>
                                    <div class="user-bubble-meta">
                                        {{ $msg->created_at ? $msg->created_at->format('h:i A') : '' }}
                                        @if($isMe)
                                            <span style="color: #53bdeb; margin-left: 2px;">✓✓</span>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-5 text-muted my-auto">
                                    {{ __('No messages yet. Send a message to start conversation!') }}
                                </div>
                            @endforelse
                        </div>

                        <!-- Input -->
                        <div class="user-chat-footer">
                            @if(!empty($isChatBlocked))
                                <div class="alert alert-danger w-100 mb-0 py-2 text-center font-weight-bold" style="font-size: 13px; border-radius: 20px;">
                                    <i class="fas fa-ban mr-1"></i> {{ __('Your account is blocked from sending chat messages due to policy violations.') }}
                                </div>
                            @else
                                <input type="text" id="user_message_input" class="user-chat-input" placeholder="{{ __('Type your message here...') }}" onkeypress="handleUserInputKey(event)">
                                <button type="button" class="user-chat-send" id="user_send_btn" onclick="sendUserMessage()">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-5 my-auto text-muted">
                            <i class="fas fa-comments fa-3x text-secondary mb-2 d-block"></i>
                            <h6 class="font-weight-bold text-dark">{{ __('Select a Chat') }}</h6>
                            <p class="small">{{ __('Select a store conversation from the left to start chatting.') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if($activeChat)
<script>
    const userActiveChatId = {{ $activeChat->id }};
    const userSendUrl = "{{ route('user.message.send') }}";
    const userFetchUrl = "{{ url('/user/messages/fetch/' . $activeChat->id) }}";
    const csrfToken = "{{ csrf_token() }}";
    const uMessagesContainer = document.getElementById('user_messages_container');

    if (uMessagesContainer) {
        uMessagesContainer.scrollTop = uMessagesContainer.scrollHeight;
    }

    function sendUserMessage() {
        const input = document.getElementById('user_message_input');
        const sendBtn = document.getElementById('user_send_btn');
        if (!input) return;
        const text = input.value.trim();
        if (!text) return;

        input.value = '';

        const nowTime = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        const tempBubbleId = 'u_temp_' + Date.now();
        const newBubble = `
            <div id="${tempBubbleId}" class="user-bubble user-bubble-me" style="opacity:0.85;">
                <div>${escapeHtml(text)}</div>
                <div class="user-bubble-meta">
                    ${nowTime} <span style="color:#53bdeb;">✓✓</span>
                </div>
            </div>
        `;
        uMessagesContainer.insertAdjacentHTML('beforeend', newBubble);
        uMessagesContainer.scrollTop = uMessagesContainer.scrollHeight;

        fetch(userSendUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                conversation_id: userActiveChatId,
                message: text
            })
        })
        .then(async res => {
            const data = await res.json().catch(() => ({}));
            const tempEl = document.getElementById(tempBubbleId);

            if (!res.ok || !data.success) {
                if (tempEl) tempEl.remove();

                if (data.policy_violation || data.is_blocked) {
                    const warningHtml = `
                        <div class="user-policy-warning text-center p-2 my-2" style="background: ${data.is_blocked ? '#f8d7da' : '#fff3cd'}; border: 1px solid ${data.is_blocked ? '#f5c6cb' : '#ffeeba'}; border-radius: 8px; color: ${data.is_blocked ? '#721c24' : '#856404'}; font-size: 13px; margin: 8px 15px;">
                            <strong class="d-block mb-1">
                                <i class="fas ${data.is_blocked ? 'fa-ban' : 'fa-exclamation-triangle'} mr-1"></i>
                                ${data.is_blocked ? '{{ __('ACCOUNT BLOCKED') }}' : '{{ __('Policy Violation Warning') }}'}
                            </strong>
                            <span>${escapeHtml(data.message || '{{ __('Phone numbers cannot be shared in chat.') }}')}</span>
                        </div>
                    `;
                    uMessagesContainer.insertAdjacentHTML('beforeend', warningHtml);
                    uMessagesContainer.scrollTop = uMessagesContainer.scrollHeight;

                    if (data.is_blocked) {
                        input.disabled = true;
                        input.placeholder = "{{ __('Account blocked from chat') }}";
                        if (sendBtn) {
                            sendBtn.disabled = true;
                            sendBtn.style.opacity = '0.5';
                        }
                    }
                } else {
                    const errHtml = `
                        <div class="user-bubble user-bubble-me text-danger" style="background:#fee2e2;">
                            <div><i class="fas fa-exclamation-circle mr-1"></i> ${escapeHtml(data.message || '{{ __('Failed to send message.') }}')}</div>
                        </div>
                    `;
                    uMessagesContainer.insertAdjacentHTML('beforeend', errHtml);
                    uMessagesContainer.scrollTop = uMessagesContainer.scrollHeight;
                }
            } else {
                fetchUserMessages();
            }
        })
        .catch(err => {
            console.error(err);
            const tempEl = document.getElementById(tempBubbleId);
            if (tempEl) tempEl.style.opacity = '0.5';
        });
    }

    function handleUserInputKey(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            sendUserMessage();
        }
    }

    function fetchUserMessages() {
        fetch(userFetchUrl, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.messages) {
                let html = '';
                data.messages.forEach(msg => {
                    const isMe = msg.is_me;
                    const isAdmin = (msg.sender_type === 'admin');
                    const bubbleClass = isMe ? 'user-bubble-me' : 'user-bubble-other';
                    const ticks = isMe ? '<span style="color:#53bdeb; margin-left: 2px;">✓✓</span>' : '';
                    const adminBadge = isAdmin ? '<div style="font-size: 11px; font-weight: 700; color: #4f46e5; margin-bottom: 2px;"><i class="fas fa-user-shield mr-1"></i> {{ __("Administration / Support") }}</div>' : '';
                    const borderStyle = isAdmin ? 'border-left: 3px solid #4f46e5;' : '';

                    html += `
                        <div class="user-bubble ${bubbleClass}" style="${borderStyle}">
                            ${adminBadge}
                            <div>${escapeHtml(msg.message)}</div>
                            <div class="user-bubble-meta">
                                ${msg.time} ${ticks}
                            </div>
                        </div>
                    `;
                });
                uMessagesContainer.innerHTML = html;
                uMessagesContainer.scrollTop = uMessagesContainer.scrollHeight;
            }
        })
        .catch(e => console.error('User poll error', e));
    }

    setInterval(fetchUserMessages, 4000);

    function escapeHtml(text) {
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }
</script>
@endif
@endsection
