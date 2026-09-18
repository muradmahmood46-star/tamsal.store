@extends('master.seller')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="card mb-3">
        <div class="card-body py-3">
            <div class="d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-0 text-dark font-weight-bold">
                    <i class="fas fa-comments text-primary mr-2"></i> {{ __('Customer Messages & Live Chats') }}
                </h4>
            </div>
        </div>
    </div>

    @include('alerts.alerts')

    <style>
        .chat-app-container {
            height: calc(100vh - 230px);
            min-height: 520px;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.06);
            display: flex;
            border: 1px solid #e2e8f0;
        }
        .chat-sidebar {
            width: 340px;
            min-width: 300px;
            border-right: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            background: #f8fafc;
        }
        .chat-sidebar-header {
            padding: 14px 16px;
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
        }
        .chat-list {
            flex-grow: 1;
            overflow-y: auto;
        }
        .chat-list-item {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            border-bottom: 1px solid #f1f5f9;
            cursor: pointer;
            transition: all 0.15s ease;
            text-decoration: none !important;
            color: inherit;
        }
        .chat-list-item:hover {
            background: #f1f5f9;
        }
        .chat-list-item.active {
            background: #eff6ff;
            border-left: 4px solid #0d6efd;
        }
        .chat-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #e2e8f0;
            color: #475569;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 16px;
            margin-right: 12px;
            flex-shrink: 0;
        }
        .chat-item-content {
            flex-grow: 1;
            overflow: hidden;
        }
        .chat-item-name {
            font-size: 14px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 2px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .chat-item-time {
            font-size: 11px;
            color: #94a3b8;
            font-weight: normal;
        }
        .chat-item-product {
            font-size: 11.5px;
            color: #0d6efd;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 2px;
        }
        .chat-item-msg {
            font-size: 12.5px;
            color: #64748b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Active Chat Main Pane */
        .chat-main {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            background: #efeae2;
        }
        .chat-header {
            padding: 12px 20px;
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 2;
        }
        .chat-product-banner {
            background: #ffffff;
            padding: 10px 18px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 13px;
        }
        .chat-product-banner img {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 6px;
            margin-right: 10px;
            border: 1px solid #e2e8f0;
        }
        .chat-messages-area {
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
        .chat-bubble {
            max-width: 70%;
            padding: 8px 14px;
            font-size: 14px;
            line-height: 1.4;
            box-shadow: 0 1px 2px rgba(0,0,0,0.12);
            position: relative;
        }
        .chat-bubble-me {
            align-self: flex-end;
            background: #d9fdd3;
            color: #111b21;
            border-radius: 10px 0 10px 10px;
        }
        .chat-bubble-other {
            align-self: flex-start;
            background: #ffffff;
            color: #111b21;
            border-radius: 0 10px 10px 10px;
        }
        .chat-bubble-meta {
            font-size: 10.5px;
            color: #64748b;
            text-align: right;
            margin-top: 3px;
        }
        .chat-footer {
            padding: 12px 18px;
            background: #f0f2f5;
            border-top: 1px solid #e2e8f0;
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .chat-input {
            flex-grow: 1;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 20px;
            padding: 10px 18px;
            font-size: 14px;
            outline: none;
        }
        .chat-input:focus {
            border-color: #008069;
        }
        .chat-send-btn {
            background: #008069;
            color: #ffffff;
            border: none;
            border-radius: 50%;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.2s;
            flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(0, 128, 105, 0.4);
        }
        .chat-send-btn:hover {
            background: #006b57;
            transform: scale(1.04);
        }

        @media (max-width: 767.98px) {
            .chat-app-container {
                height: calc(100vh - 130px) !important;
                min-height: 480px !important;
                border-radius: 8px !important;
                flex-direction: column !important;
                width: 100% !important;
                max-width: 100% !important;
            }

            @if($activeChat)
                .chat-sidebar {
                    display: none !important;
                }
                .chat-main {
                    display: flex !important;
                    width: 100% !important;
                    height: 100% !important;
                }
            @else
                .chat-sidebar {
                    display: flex !important;
                    width: 100% !important;
                    height: 100% !important;
                    min-width: 0 !important;
                    border-right: none !important;
                }
                .chat-main {
                    display: none !important;
                }
            @endif

            .chat-header {
                padding: 10px 12px !important;
            }

            .chat-product-banner {
                padding: 8px 12px !important;
                flex-wrap: wrap !important;
                gap: 6px !important;
            }

            .chat-product-banner > div {
                max-width: calc(100% - 95px) !important;
            }

            .chat-messages-area {
                padding: 12px 10px !important;
            }

            .chat-bubble {
                max-width: 85% !important;
                font-size: 13.5px !important;
            }

            .chat-footer {
                padding: 8px 10px !important;
            }

            .chat-input {
                font-size: 13px !important;
                padding: 8px 14px !important;
            }

            .chat-send-btn {
                width: 38px !important;
                height: 38px !important;
                font-size: 14px !important;
            }
        }
    </style>

    <div class="chat-app-container">
        <!-- Sidebar: Recent Conversations List -->
        <div class="chat-sidebar">
            <div class="chat-sidebar-header">
                <h6 class="font-weight-bold text-dark mb-0">
                    <i class="fas fa-inbox text-primary mr-1"></i> {{ __('Recent Customer Inquiries') }}
                    <span class="badge badge-primary badge-pill ml-1">{{ $conversations->count() }}</span>
                </h6>
            </div>
            <div class="chat-list">
                @forelse($conversations as $conv)
                    @php
                        $isActive = ($activeChat && $activeChat->id == $conv->id);
                        $buyerName = $conv->buyer_name;
                        $firstLetter = strtoupper(substr($buyerName, 0, 1)) ?: 'C';
                    @endphp
                    <a href="{{ route('seller.message.index', ['chat_id' => $conv->id]) }}" class="chat-list-item {{ $isActive ? 'active' : '' }}">
                        <div class="chat-avatar">
                            {{ $firstLetter }}
                        </div>
                        <div class="chat-item-content">
                            <div class="chat-item-name">
                                <span>{{ $buyerName }}</span>
                                <span class="chat-item-time">{{ $conv->last_message_at ? $conv->last_message_at->diffForHumans(null, true) : '' }}</span>
                            </div>
                            @if($conv->item && $conv->item->name)
                                <div class="chat-item-product">
                                    <i class="fas fa-box-open mr-1"></i> {{ $conv->item->name }}
                                </div>
                            @endif
                            <div class="d-flex justify-content-between align-items-center mt-1">
                                <span class="chat-item-msg" style="max-width: 190px;">{{ $conv->last_message ?: __('No messages') }}</span>
                                @if(!$isActive && $conv->vendor_unread_count > 0)
                                    <span class="badge badge-success font-weight-bold ml-1" style="font-size: 10px; border-radius: 10px; padding: 2px 7px; flex-shrink: 0;">{{ $conv->vendor_unread_count }}</span>
                                @endif
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-comment-slash fa-3x mb-2 d-block text-secondary"></i>
                        {{ __('No customer messages yet.') }}
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Main Chat Area -->
        <div class="chat-main">
            @if($activeChat)
                <!-- Chat Header -->
                <div class="chat-header">
                    <div class="d-flex align-items-center" style="min-width: 0;">
                        <a href="{{ route('seller.message.index') }}" class="btn btn-sm btn-light border mr-2 d-md-none text-dark shadow-sm" style="border-radius: 6px; padding: 5px 10px; flex-shrink: 0;" title="{{ __('Back to Chats') }}">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <div class="chat-avatar mr-2" style="background: #008069; color: #fff; width: 38px; height: 38px; font-size: 15px;">
                            {{ strtoupper(substr($activeChat->buyer_name, 0, 1)) ?: 'C' }}
                        </div>
                        <div style="min-width: 0;">
                            <h6 class="mb-0 font-weight-bold text-dark text-truncate" style="font-size: 14px;">{{ $activeChat->buyer_name }}</h6>
                            <small class="text-muted text-truncate d-block" style="font-size: 11px;">
                                <span><i class="fas fa-envelope mr-1 text-primary"></i> {{ $activeChat->user->email ?? 'N/A' }}</span>
                                @if($activeChat->user && $activeChat->user->phone)
                                    <span class="mx-1">•</span> <span><i class="fas fa-phone mr-1 text-success"></i> {{ $activeChat->user->phone }}</span>
                                @endif
                            </small>
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('seller.message.delete', $activeChat->id) }}" class="btn btn-outline-danger btn-sm" onclick="return confirm('{{ __('Are you sure you want to delete this chat conversation?') }}')">
                            <i class="fas fa-trash-alt mr-1"></i> {{ __('Delete') }}
                        </a>
                    </div>
                </div>

                <!-- Pinned Product Context Banner -->
                @if($activeChat->item && $activeChat->item->name)
                    <div class="chat-product-banner">
                        <div class="d-flex align-items-center">
                            <img src="{{ url('/core/public/storage/images/' . $activeChat->item->photo) }}" alt="{{ $activeChat->item->name }}">
                            <div>
                                <strong class="text-dark d-block" style="font-size: 13px;">{{ $activeChat->item->name }}</strong>
                                <span class="text-success font-weight-bold">{{ PriceHelper::grandCurrencyPrice($activeChat->item) }}</span>
                                <span class="badge badge-light border ml-1">{{ $activeChat->item->sku ? '#' . $activeChat->item->sku : 'ID #' . $activeChat->item->id }}</span>
                            </div>
                        </div>
                        <a href="{{ route('front.product', $activeChat->item->slug) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-external-link-alt mr-1"></i> {{ __('View Product') }}
                        </a>
                    </div>
                @endif

                <!-- Messages Stream -->
                <div class="chat-messages-area" id="seller_messages_container">
                    @forelse($messages as $msg)
                        @php
                            $isMe = ($msg->sender_type === 'vendor');
                            $isAdmin = ($msg->sender_type === 'admin');
                        @endphp
                        <div class="chat-bubble {{ $isMe ? 'chat-bubble-me' : 'chat-bubble-other' }}" style="{{ $isAdmin ? 'border-left: 3px solid #4f46e5;' : '' }}">
                            @if($isAdmin)
                                <div style="font-size: 11px; font-weight: 700; color: #4f46e5; margin-bottom: 2px;">
                                    <i class="fas fa-user-shield mr-1"></i> {{ __('Administration / Support') }}
                                </div>
                            @endif
                            <div>{{ $msg->message }}</div>
                            <div class="chat-bubble-meta">
                                {{ $msg->created_at ? $msg->created_at->format('h:i A') : '' }}
                                @if($isMe)
                                    <span style="color: #53bdeb; margin-left: 2px;">✓✓</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted my-auto">
                            <i class="fas fa-comments fa-3x text-secondary mb-2 d-block"></i>
                            {{ __('No messages in this conversation yet. Send a message below to start chatting.') }}
                        </div>
                    @endforelse
                </div>

                <!-- Message Input Footer -->
                <div class="chat-footer">
                    @if(!empty($isChatBlocked))
                        <div class="alert alert-danger w-100 mb-0 py-2 text-center font-weight-bold" style="font-size: 13px; border-radius: 20px;">
                            <i class="fas fa-ban mr-1"></i> {{ __('Your seller account and store are blocked from chat due to policy violations.') }}
                        </div>
                    @else
                        <input type="text" id="seller_message_input" class="chat-input" placeholder="{{ __('Type your reply here...') }}" onkeypress="handleSellerInputKey(event)">
                        <button type="button" class="chat-send-btn" id="seller_send_btn" onclick="sendSellerMessage()">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    @endif
                </div>
            @else
                <div class="text-center py-5 my-auto text-muted">
                    <i class="fas fa-comment-dots fa-4x text-secondary mb-3 d-block"></i>
                    <h5 class="text-dark font-weight-bold">{{ __('Select a Conversation') }}</h5>
                    <p>{{ __('Choose a customer inquiry from the left to view messages and reply.') }}</p>
                </div>
            @endif
        </div>
    </div>
</div>

@if($activeChat)
<script>
    const activeChatId = {{ $activeChat->id }};
    const sellerSendUrl = "{{ route('seller.message.send') }}";
    const sellerFetchUrl = "{{ url('/seller/messages/fetch/' . $activeChat->id) }}";
    const csrfToken = "{{ csrf_token() }}";
    const messagesContainer = document.getElementById('seller_messages_container');

    // Auto scroll to bottom
    if (messagesContainer) {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    function sendSellerMessage() {
        const input = document.getElementById('seller_message_input');
        const sendBtn = document.getElementById('seller_send_btn');
        if (!input) return;
        const text = input.value.trim();
        if (!text) return;

        input.value = '';

        // Optimistic UI append
        const nowTime = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        const tempBubbleId = 's_temp_' + Date.now();
        const newBubble = `
            <div id="${tempBubbleId}" class="chat-bubble chat-bubble-me" style="opacity:0.85;">
                <div>${escapeHtml(text)}</div>
                <div class="chat-bubble-meta">
                    ${nowTime} <span style="color:#53bdeb;">✓✓</span>
                </div>
            </div>
        `;
        messagesContainer.insertAdjacentHTML('beforeend', newBubble);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;

        fetch(sellerSendUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                conversation_id: activeChatId,
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
                        <div class="seller-policy-warning text-center p-2 my-2" style="background: ${data.is_blocked ? '#f8d7da' : '#fff3cd'}; border: 1px solid ${data.is_blocked ? '#f5c6cb' : '#ffeeba'}; border-radius: 8px; color: ${data.is_blocked ? '#721c24' : '#856404'}; font-size: 13px; margin: 8px 15px;">
                            <strong class="d-block mb-1">
                                <i class="fas ${data.is_blocked ? 'fa-ban' : 'fa-exclamation-triangle'} mr-1"></i>
                                ${data.is_blocked ? '{{ __('ACCOUNT BLOCKED') }}' : '{{ __('Policy Violation Warning') }}'}
                            </strong>
                            <span>${escapeHtml(data.message || '{{ __('Phone number sharing is prohibited.') }}')}</span>
                        </div>
                    `;
                    messagesContainer.insertAdjacentHTML('beforeend', warningHtml);
                    messagesContainer.scrollTop = messagesContainer.scrollHeight;

                    if (data.is_blocked) {
                        input.disabled = true;
                        input.placeholder = "{{ __('Store blocked from sending messages') }}";
                        if (sendBtn) {
                            sendBtn.disabled = true;
                            sendBtn.style.opacity = '0.5';
                        }
                    }
                } else {
                    const errHtml = `
                        <div class="chat-bubble chat-bubble-me text-danger" style="background:#fee2e2;">
                            <div><i class="fas fa-exclamation-circle mr-1"></i> ${escapeHtml(data.message || '{{ __('Failed to send message.') }}')}</div>
                        </div>
                    `;
                    messagesContainer.insertAdjacentHTML('beforeend', errHtml);
                    messagesContainer.scrollTop = messagesContainer.scrollHeight;
                }
            } else {
                fetchSellerMessages();
            }
        })
        .catch(err => {
            console.error(err);
            const tempEl = document.getElementById(tempBubbleId);
            if (tempEl) tempEl.style.opacity = '0.5';
        });
    }

    function handleSellerInputKey(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            sendSellerMessage();
        }
    }

    function fetchSellerMessages() {
        fetch(sellerFetchUrl, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.messages) {
                let html = '';
                data.messages.forEach(msg => {
                    const isMe = msg.is_me;
                    const isAdmin = (msg.sender_type === 'admin');
                    const bubbleClass = isMe ? 'chat-bubble-me' : 'chat-bubble-other';
                    const ticks = isMe ? '<span style="color:#53bdeb; margin-left: 2px;">✓✓</span>' : '';
                    const adminBadge = isAdmin ? '<div style="font-size: 11px; font-weight: 700; color: #4f46e5; margin-bottom: 2px;"><i class="fas fa-user-shield mr-1"></i> {{ __("Administration / Support") }}</div>' : '';
                    const borderStyle = isAdmin ? 'border-left: 3px solid #4f46e5;' : '';

                    html += `
                        <div class="chat-bubble ${bubbleClass}" style="${borderStyle}">
                            ${adminBadge}
                            <div>${escapeHtml(msg.message)}</div>
                            <div class="chat-bubble-meta">
                                ${msg.time} ${ticks}
                            </div>
                        </div>
                    `;
                });
                messagesContainer.innerHTML = html;
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }
        })
        .catch(e => console.error('Seller poll error', e));
    }

    // Poll every 4 seconds
    setInterval(fetchSellerMessages, 4000);

    function escapeHtml(text) {
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }
</script>
@endif
@endsection
