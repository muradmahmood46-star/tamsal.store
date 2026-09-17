<!-- WhatsApp Style Interactive Floating Chatbox Component -->
<style>
    /* WhatsApp Floating Chatbox Styles */
    #whatsapp_chat_widget {
        position: fixed;
        bottom: 25px;
        right: 25px;
        width: 380px;
        max-width: 92vw;
        height: 520px;
        max-height: 85vh;
        background: #efeae2;
        border-radius: 16px;
        box-shadow: 0 12px 35px rgba(0, 0, 0, 0.28);
        display: none;
        flex-direction: column;
        z-index: 999999;
        overflow: hidden;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        animation: chatSlideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        border: 1px solid rgba(0, 0, 0, 0.08);
    }
    @keyframes chatSlideUp {
        from { opacity: 0; transform: translateY(30px) scale(0.96); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }

    /* WhatsApp Header */
    .wa-chat-header {
        background: #008069;
        color: #ffffff;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 2px 5px rgba(0,0,0,0.15);
        z-index: 2;
    }
    .wa-avatar-box {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: #ffffff;
        color: #008069;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 19px;
        font-weight: bold;
        margin-right: 12px;
        flex-shrink: 0;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .wa-header-info {
        flex-grow: 1;
        overflow: hidden;
    }
    .wa-header-title {
        font-size: 15.5px;
        font-weight: 700;
        margin: 0;
        line-height: 1.2;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        color: #ffffff;
    }
    .wa-header-status {
        font-size: 12px;
        color: #d1fae5;
        margin: 0;
        display: flex;
        align-items: center;
    }
    .wa-online-dot {
        width: 8px;
        height: 8px;
        background: #22c55e;
        border-radius: 50%;
        display: inline-block;
        margin-right: 5px;
        box-shadow: 0 0 6px #22c55e;
    }
    .wa-close-btn {
        background: transparent;
        border: none;
        color: #ffffff;
        font-size: 20px;
        cursor: pointer;
        padding: 4px 8px;
        border-radius: 50%;
        transition: background 0.2s;
        line-height: 1;
    }
    .wa-close-btn:hover {
        background: rgba(255,255,255,0.2);
    }

    /* Pinned Product Card */
    .wa-product-pinned {
        background: #ffffff;
        padding: 8px 12px;
        display: flex;
        align-items: center;
        border-bottom: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        z-index: 1;
    }
    .wa-product-pinned img {
        width: 44px;
        height: 44px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
        margin-right: 10px;
        flex-shrink: 0;
    }
    .wa-product-info {
        flex-grow: 1;
        overflow: hidden;
    }
    .wa-product-title {
        font-size: 13px;
        font-weight: 600;
        color: #1e293b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin: 0 0 2px 0;
    }
    .wa-product-price {
        font-size: 12.5px;
        font-weight: 700;
        color: #008069;
        margin: 0;
    }

    /* Messages Body */
    .wa-messages-body {
        flex-grow: 1;
        overflow-y: auto;
        padding: 14px 12px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        background-color: #efeae2;
        background-image: radial-gradient(#d1d7db 0.8px, transparent 0.8px);
        background-size: 16px 16px;
    }

    /* Message Bubbles */
    .wa-msg {
        max-width: 82%;
        padding: 7px 11px 5px 11px;
        font-size: 13.5px;
        line-height: 1.4;
        position: relative;
        word-wrap: break-word;
        box-shadow: 0 1px 1.5px rgba(0,0,0,0.13);
    }
    .wa-msg-outgoing {
        align-self: flex-end;
        background: #d9fdd3;
        color: #111b21;
        border-radius: 10px 0 10px 10px;
    }
    .wa-msg-incoming {
        align-self: flex-start;
        background: #ffffff;
        color: #111b21;
        border-radius: 0 10px 10px 10px;
    }
    .wa-msg-meta {
        font-size: 10.5px;
        color: #667781;
        margin-top: 3px;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 3px;
    }
    .wa-ticks {
        color: #53bdeb;
        font-size: 11px;
        font-weight: bold;
    }

    /* Quick Chips */
    .wa-quick-chips {
        display: flex;
        flex-wrap: nowrap;
        overflow-x: auto;
        gap: 6px;
        padding: 6px 12px;
        background: #f0f2f5;
        border-top: 1px solid #e2e8f0;
    }
    .wa-quick-chips::-webkit-scrollbar { display: none; }
    .wa-chip-btn {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        color: #008069;
        font-size: 11.5px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 12px;
        white-space: nowrap;
        cursor: pointer;
        transition: all 0.2s;
    }
    .wa-chip-btn:hover {
        background: #008069;
        color: #ffffff;
        border-color: #008069;
    }

    /* Input Footer */
    .wa-chat-footer {
        background: #f0f2f5;
        padding: 10px 12px;
        display: flex;
        align-items: center;
        gap: 8px;
        border-top: 1px solid #e2e8f0;
    }
    .wa-chat-input {
        flex-grow: 1;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 9px 15px;
        font-size: 13.5px;
        outline: none;
        transition: border 0.2s;
        box-shadow: inset 0 1px 2px rgba(0,0,0,0.04);
    }
    .wa-chat-input:focus {
        border-color: #008069;
    }
    .wa-send-btn {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #008069;
        color: #ffffff;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        transition: all 0.2s;
        flex-shrink: 0;
        box-shadow: 0 2px 6px rgba(0, 128, 105, 0.4);
    }
    .wa-send-btn:hover {
        background: #006b57;
        transform: scale(1.05);
    }

    /* Mobile Sticky Bottom Bar (Daraz Style) */
    .mobile-daraz-bar {
        display: none;
    }
    @media (max-width: 767px) {
        .mobile-daraz-bar {
            display: flex;
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 60px;
            background: #ffffff;
            box-shadow: 0 -3px 15px rgba(0,0,0,0.12);
            z-index: 99999;
            align-items: center;
            justify-content: space-between;
            padding: 6px 10px;
            border-top: 1px solid #e2e8f0;
        }
        .daraz-icon-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #475569;
            text-decoration: none;
            font-size: 11px;
            font-weight: 600;
            padding: 0 8px;
            border: none;
            background: transparent;
            cursor: pointer;
            line-height: 1.1;
        }
        .daraz-icon-btn i {
            font-size: 18px;
            margin-bottom: 3px;
        }
        .daraz-icon-btn.chat-btn i {
            color: #008069;
        }
        .daraz-actions-group {
            display: flex;
            gap: 6px;
            flex-grow: 1;
            margin-left: 8px;
        }
        .daraz-btn-cart {
            flex: 1;
            background: #ff9900;
            color: #ffffff;
            border: none;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 700;
            padding: 10px 5px;
            cursor: pointer;
        }
        .daraz-btn-buy {
            flex: 1;
            background: #f85606;
            color: #ffffff;
            border: none;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 700;
            padding: 10px 5px;
            cursor: pointer;
        }

        #whatsapp_chat_widget {
            bottom: 0;
            right: 0;
            width: 100vw;
            max-width: 100vw;
            height: 90vh;
            max-height: 90vh;
            border-radius: 20px 20px 0 0;
        }
    }
</style>

<!-- Floating WhatsApp Chatbox Modal Container -->
<div id="whatsapp_chat_widget">
    <!-- Header -->
    <div class="wa-chat-header">
        <div class="d-flex align-items-center">
            <div class="wa-avatar-box">
                <i class="fas fa-store"></i>
            </div>
            <div class="wa-header-info">
                <h6 class="wa-header-title" id="wa_store_name">{{ $item->store_name }}</h6>
                <div class="wa-header-status">
                    <span class="wa-online-dot"></span>
                    <span id="wa_status_text">{{ __('Online • Typically replies instantly') }}</span>
                </div>
            </div>
        </div>
        <button class="wa-close-btn" onclick="toggleWhatsAppChat()"><i class="fas fa-times"></i></button>
    </div>

    <!-- Pinned Product Information -->
    <div class="wa-product-pinned">
        <img src="{{ url('/core/public/storage/images/' . $item->photo) }}" alt="{{ $item->name }}">
        <div class="wa-product-info">
            <h6 class="wa-product-title">{{ $item->name }}</h6>
            <div class="d-flex justify-content-between align-items-center">
                <span class="wa-product-price">{{ PriceHelper::grandCurrencyPrice($item) }}</span>
                <span class="badge badge-light border text-muted" style="font-size: 10.5px;">{{ $item->sku ? '#' . $item->sku : 'ID #' . $item->id }}</span>
            </div>
        </div>
    </div>

    <!-- Messages Container -->
    <div class="wa-messages-body" id="wa_messages_container">
        <div class="text-center py-4 text-muted small" id="wa_loading_indicator">
            <i class="fas fa-spinner fa-spin fa-2x text-success mb-2 d-block"></i>
            {{ __('Connecting to seller...') }}
        </div>
    </div>

    <!-- Quick Action Suggestion Chips -->
    <div class="wa-quick-chips">
        <button type="button" class="wa-chip-btn" onclick="sendQuickChip('{{ __('Is this product available in stock?') }}')">
            {{ __('Is this in stock?') }}
        </button>
        <button type="button" class="wa-chip-btn" onclick="sendQuickChip('{{ __('What is the delivery time for this item?') }}')">
            {{ __('Delivery time?') }}
        </button>
        <button type="button" class="wa-chip-btn" onclick="sendQuickChip('{{ __('Is any discount or best price possible?') }}')">
            {{ __('Best price / discount?') }}
        </button>
    </div>

    <!-- Input Footer -->
    <div class="wa-chat-footer">
        <input type="text" id="wa_message_input" class="wa-chat-input" placeholder="{{ __('Type message to seller...') }}" onkeypress="handleWaInputKey(event)">
        <button type="button" class="wa-send-btn" id="wa_send_btn" onclick="sendWhatsAppMessage()">
            <i class="fas fa-paper-plane"></i>
        </button>
    </div>
</div>

<!-- Mobile Sticky Bottom Action Bar (Daraz App Style) -->
<div class="mobile-daraz-bar">
    <a href="{{ route('front.catalog') . '?vendor=' . ($item->vendor_id ?: 'admin') }}" class="daraz-icon-btn">
        <i class="fas fa-store text-primary"></i>
        <span>{{ __('Store') }}</span>
    </a>
    <button type="button" class="daraz-icon-btn chat-btn" onclick="openWhatsAppChat()">
        <i class="fas fa-comment-dots"></i>
        <span>{{ __('Chat') }}</span>
    </button>
    <div class="daraz-actions-group">
        @if ($item->is_stock())
            <button type="button" class="daraz-btn-cart" onclick="document.getElementById('add_to_cart').click()">
                {{ __('Add to Cart') }}
            </button>
            <button type="button" class="daraz-btn-buy" onclick="document.getElementById('but_to_cart').click()">
                {{ __('Buy Now') }}
            </button>
        @else
            <button type="button" class="daraz-btn-cart" style="background:#94a3b8;" disabled>
                {{ __('Out of stock') }}
            </button>
        @endif
    </div>
</div>

<!-- JavaScript Engine for WhatsApp Live Chat -->
<script>
    let currentConversationId = null;
    let chatPollInterval = null;
    const currentItemId = {{ $item->id }};
    const chatInitUrl = "{{ route('front.chat.init') }}";
    const chatSendUrl = "{{ route('front.chat.send') }}";
    const chatFetchUrlBase = "{{ url('/chat/fetch') }}";
    const csrfToken = "{{ csrf_token() }}";

    function toggleWhatsAppChat() {
        const widget = document.getElementById('whatsapp_chat_widget');
        if (widget.style.display === 'flex') {
            widget.style.display = 'none';
            if (chatPollInterval) clearInterval(chatPollInterval);
        } else {
            openWhatsAppChat();
        }
    }

    function openWhatsAppChat() {
        const widget = document.getElementById('whatsapp_chat_widget');
        widget.style.display = 'flex';
        initChatSession();
    }

    function initChatSession() {
        const container = document.getElementById('wa_messages_container');
        container.innerHTML = `
            <div class="text-center py-4 text-muted small">
                <i class="fas fa-spinner fa-spin fa-2x text-success mb-2 d-block"></i>
                {{ __('Connecting to seller...') }}
            </div>
        `;

        fetch(chatInitUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ item_id: currentItemId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.auth_required) {
                container.innerHTML = `
                    <div class="text-center p-4 bg-white rounded shadow-sm my-auto">
                        <div class="rounded-circle bg-light d-inline-flex p-3 mb-2 text-primary">
                            <i class="fas fa-user-lock fa-2x"></i>
                        </div>
                        <h6 class="font-weight-bold text-dark mb-1">{{ __('Sign in to Chat') }}</h6>
                        <p class="text-muted small mb-3">{{ __('Please login to chat directly with :store and view your messages anytime.', ['store' => $item->store_name]) }}</p>
                        <a href="${data.login_url}" class="btn btn-success btn-sm w-100 py-2 font-weight-bold" style="background:#008069; border-color:#008069; border-radius: 20px;">
                            <i class="fas fa-sign-in-alt mr-1"></i> {{ __('Login / Register to Chat') }}
                        </a>
                    </div>
                `;
                return;
            }

            if (data.success) {
                currentConversationId = data.conversation_id;
                renderMessages(data.messages);
                
                if (data.is_chat_blocked) {
                    const input = document.getElementById('wa_message_input');
                    const sendBtn = document.getElementById('wa_send_btn');
                    if (input) {
                        input.disabled = true;
                        input.placeholder = "{{ __('Account blocked from chat') }}";
                    }
                    if (sendBtn) {
                        sendBtn.disabled = true;
                        sendBtn.style.opacity = '0.5';
                    }
                    container.insertAdjacentHTML('beforeend', `
                        <div class="wa-policy-warning text-center p-2 my-2" style="background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 8px; color: #721c24; font-size: 12px; margin: 8px 10px;">
                            <i class="fas fa-ban mr-1"></i> ${escapeHtml(data.chat_blocked_message || "{{ __('Your account is blocked from chat.') }}")}
                        </div>
                    `);
                    container.scrollTop = container.scrollHeight;
                } else {
                    document.getElementById('wa_message_input').focus();
                }

                // Start polling for new messages every 4 seconds
                if (chatPollInterval) clearInterval(chatPollInterval);
                chatPollInterval = setInterval(fetchLatestMessages, 4000);
            }
        })
        .catch(err => {
            console.error('Chat init error:', err);
            container.innerHTML = `<div class="text-center text-danger p-3">{{ __('Could not connect. Please try again.') }}</div>`;
        });
    }

    function renderMessages(messages) {
        const container = document.getElementById('wa_messages_container');
        if (!messages || messages.length === 0) {
            container.innerHTML = `
                <div class="text-center my-auto p-3 text-muted small bg-white rounded shadow-sm mx-2">
                    <i class="fas fa-comments fa-2x text-success mb-2 d-block" style="color:#008069 !important;"></i>
                    <strong>{{ __('Say hello to :store!', ['store' => $item->store_name]) }}</strong>
                    <div class="mt-1 text-muted" style="font-size: 11.5px;">{{ __('Ask anything about this product or delivery.') }}</div>
                </div>
            `;
            return;
        }

        let html = '';
        messages.forEach(msg => {
            const isMe = msg.is_me;
            const isAdmin = (msg.sender_type === 'admin');
            const bubbleClass = isMe ? 'wa-msg-outgoing' : 'wa-msg-incoming';
            const ticks = isMe ? '<span class="wa-ticks">✓✓</span>' : '';
            const adminBadge = isAdmin ? '<div style="font-size:10px; font-weight:700; color:#4f46e5; margin-bottom:2px;"><i class="fas fa-shield-alt mr-1"></i> {{ __("Administration / Support") }}</div>' : '';

            html += `
                <div class="wa-msg ${bubbleClass}">
                    ${adminBadge}
                    <div class="wa-msg-text">${escapeHtml(msg.message)}</div>
                    <div class="wa-msg-meta">
                        <span>${msg.time}</span>
                        ${ticks}
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
        container.scrollTop = container.scrollHeight;
    }

    function fetchLatestMessages() {
        if (!currentConversationId) return;

        fetch(`${chatFetchUrlBase}/${currentConversationId}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.messages) {
                renderMessages(data.messages);
            }
        })
        .catch(e => console.error('Poll error', e));
    }

    function sendWhatsAppMessage() {
        const input = document.getElementById('wa_message_input');
        const sendBtn = document.getElementById('wa_send_btn');
        const text = input.value.trim();
        if (!text) return;

        if (!currentConversationId) {
            initChatSession();
            return;
        }

        input.value = '';

        // Optimistic UI append
        const container = document.getElementById('wa_messages_container');
        const nowTime = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        const tempBubbleId = 'wa_temp_' + Date.now();
        const tempMsgHtml = `
            <div id="${tempBubbleId}" class="wa-msg wa-msg-outgoing" style="opacity: 0.85;">
                <div class="wa-msg-text">${escapeHtml(text)}</div>
                <div class="wa-msg-meta">
                    <span>${nowTime}</span>
                    <span class="text-muted"><i class="fas fa-clock" style="font-size: 10px;"></i></span>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', tempMsgHtml);
        container.scrollTop = container.scrollHeight;

        fetch(chatSendUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                conversation_id: currentConversationId,
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
                        <div class="wa-policy-warning text-center p-2 my-2" style="background: ${data.is_blocked ? '#f8d7da' : '#fff3cd'}; border: 1px solid ${data.is_blocked ? '#f5c6cb' : '#ffeeba'}; border-radius: 8px; color: ${data.is_blocked ? '#721c24' : '#856404'}; font-size: 12px; margin: 6px 10px; animation: fadeIn 0.3s;">
                            <div style="font-weight: 700; margin-bottom: 2px;">
                                <i class="fas ${data.is_blocked ? 'fa-ban' : 'fa-exclamation-triangle'} mr-1"></i>
                                ${data.is_blocked ? '{{ __('ACCOUNT BLOCKED') }}' : '{{ __('Chat Policy Warning') }}'}
                            </div>
                            <div>${escapeHtml(data.message || '{{ __('Phone number sharing is not allowed.') }}')}</div>
                        </div>
                    `;
                    container.insertAdjacentHTML('beforeend', warningHtml);
                    container.scrollTop = container.scrollHeight;

                    if (data.is_blocked) {
                        input.disabled = true;
                        input.placeholder = "{{ __('Account blocked from sending messages') }}";
                        sendBtn.disabled = true;
                        sendBtn.style.opacity = '0.5';
                    }
                } else {
                    const errorHtml = `
                        <div class="wa-msg wa-msg-outgoing text-danger" style="background:#fee2e2;">
                            <div class="wa-msg-text"><i class="fas fa-exclamation-circle mr-1"></i> ${escapeHtml(data.message || '{{ __('Failed to send message.') }}')}</div>
                        </div>
                    `;
                    container.insertAdjacentHTML('beforeend', errorHtml);
                    container.scrollTop = container.scrollHeight;
                }
            } else {
                fetchLatestMessages();
            }
        })
        .catch(err => {
            console.error('Send error:', err);
            const tempEl = document.getElementById(tempBubbleId);
            if (tempEl) {
                tempEl.style.opacity = '0.5';
            }
        });
    }

    function sendQuickChip(text) {
        document.getElementById('wa_message_input').value = text;
        sendWhatsAppMessage();
    }

    function handleWaInputKey(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            sendWhatsAppMessage();
        }
    }

    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }
</script>
