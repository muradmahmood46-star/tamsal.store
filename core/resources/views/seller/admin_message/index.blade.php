@extends('master.seller')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="card mb-3 shadow-sm border-0 d-none d-md-block" style="border-radius: 10px;">
        <div class="card-body py-3">
            <div class="d-sm-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-0 text-dark font-weight-bold">
                        <i class="fas fa-user-shield text-primary mr-2"></i> {{ __('Admin Messages & Official Support') }}
                    </h4>
                    <p class="text-muted small mb-0">{{ __('Direct official communication line with Administration regarding store approvals, inquiries, and platform notices.') }}</p>
                </div>
                <div class="mt-2 mt-sm-0">
                    <span class="badge badge-success px-3 py-2 font-weight-bold" style="font-size: 12.5px; border-radius: 20px;">
                        <i class="fas fa-check-circle mr-1"></i> {{ __('Official Support Line Active') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    @include('alerts.alerts')

    <style>
        .admin-msg-container {
            height: calc(100vh - 220px);
            min-height: 520px;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.06);
            display: flex;
            flex-direction: column;
            border: 1px solid #e2e8f0;
        }
        .admin-msg-header {
            padding: 14px 22px;
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 2;
            flex-shrink: 0;
        }
        .admin-msg-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #1e1b4b;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 19px;
            margin-right: 14px;
            box-shadow: 0 2px 6px rgba(30, 27, 75, 0.35);
            flex-shrink: 0;
        }
        .admin-msg-stream {
            flex-grow: 1;
            overflow-y: auto;
            padding: 20px 24px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            background-color: #efeae2;
            background-image: radial-gradient(#d1d7db 0.8px, transparent 0.8px);
            background-size: 16px 16px;
        }
        .msg-bubble {
            max-width: 72%;
            padding: 9px 15px 7px 15px;
            font-size: 14px;
            line-height: 1.45;
            position: relative;
            word-wrap: break-word;
            box-shadow: 0 1px 2px rgba(0,0,0,0.12);
        }
        .msg-bubble-admin {
            align-self: flex-start;
            background: #ffffff;
            color: #111b21;
            border-radius: 0 12px 12px 12px;
            border-left: 4px solid #4f46e5;
        }
        .msg-bubble-seller {
            align-self: flex-end;
            background: #d9fdd3;
            color: #111b21;
            border-radius: 12px 0 12px 12px;
            border-right: 4px solid #008069;
        }
        .msg-bubble-meta {
            font-size: 10.5px;
            color: #64748b;
            margin-top: 4px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 4px;
        }
        .admin-msg-footer {
            padding: 12px 20px;
            background: #f0f2f5;
            border-top: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }
        .admin-msg-input {
            flex-grow: 1;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 24px;
            padding: 10px 20px;
            font-size: 14px;
            outline: none;
            transition: border 0.2s;
        }
        .admin-msg-input:focus {
            border-color: #008069;
        }
        .admin-msg-send-btn {
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
        .admin-msg-send-btn:hover {
            background: #006b57;
            transform: scale(1.04);
        }

        @media (max-width: 991.98px) {
            .admin-msg-container {
                height: calc(100vh - 75px) !important;
                min-height: calc(100vh - 75px) !important;
                max-height: calc(100vh - 75px) !important;
                border-radius: 0 !important;
                border: none !important;
                margin: -10px -10px -10px -10px !important;
                width: calc(100% + 20px) !important;
                max-width: calc(100% + 20px) !important;
                box-shadow: none !important;
                position: relative !important;
            }

            .admin-msg-header {
                padding: 10px 12px !important;
            }

            .admin-msg-avatar {
                width: 36px !important;
                height: 36px !important;
                font-size: 15px !important;
                margin-right: 8px !important;
            }

            .admin-msg-stream {
                padding: 12px 10px !important;
            }

            .msg-bubble {
                max-width: 88% !important;
                font-size: 13.5px !important;
                padding: 8px 12px 6px 12px !important;
            }

            .admin-msg-footer {
                padding: 8px 10px !important;
                position: sticky !important;
                bottom: 0 !important;
                z-index: 10 !important;
            }

            .admin-msg-input {
                font-size: 13.5px !important;
                padding: 8px 14px !important;
                height: 40px !important;
            }

            .admin-msg-send-btn {
                width: 40px !important;
                height: 40px !important;
                font-size: 15px !important;
            }
        }
    </style>

    <div class="admin-msg-container">
        <!-- Header -->
        <div class="admin-msg-header">
            <div class="d-flex align-items-center">
                <div class="admin-msg-avatar">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div>
                    <h6 class="mb-0 font-weight-bold text-dark">
                        {{ __('Administration & Support Team') }}
                        <span class="badge badge-primary ml-2 font-weight-normal py-1 px-2" style="font-size: 11px;">
                            <i class="fas fa-shield-alt mr-1"></i> {{ __('Official Support') }}
                        </span>
                    </h6>
                    <small class="text-muted">
                        <span class="text-success font-weight-bold"><i class="fas fa-circle" style="font-size: 8px;"></i> {{ __('Direct Store Support Line') }}</span>
                    </small>
                </div>
            </div>
            <div>
                <span class="text-muted small"><i class="fas fa-lock mr-1 text-secondary"></i> {{ __('Secure Official Messages') }}</span>
            </div>
        </div>

        <!-- Messages Stream -->
        <div class="admin-msg-stream" id="admin_seller_messages_container">
            @forelse($messages as $msg)
                @php
                    $isMe = ($msg->sender_type === 'vendor');
                @endphp
                <div class="msg-bubble {{ $isMe ? 'msg-bubble-seller' : 'msg-bubble-admin' }}">
                    <div style="font-size: 11px; font-weight: 700; color: {{ $isMe ? '#008069' : '#4f46e5' }}; margin-bottom: 3px;">
                        <i class="fas {{ $isMe ? 'fa-store' : 'fa-user-shield' }} mr-1"></i>
                        {{ $isMe ? __('You (Store Owner)') : __('Administration & Support') }}
                    </div>
                    <div>{{ $msg->message }}</div>
                    <div class="msg-bubble-meta">
                        <span>{{ $msg->created_at ? $msg->created_at->format('h:i A') : '' }}</span>
                        @if($isMe)
                            <span style="color: #53bdeb; font-weight: bold;">✓✓</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-5 my-auto text-muted">
                    <div class="rounded-circle bg-light d-inline-flex p-3 mb-2 text-primary shadow-sm">
                        <i class="fas fa-user-shield fa-2x"></i>
                    </div>
                    <h6 class="font-weight-bold text-dark">{{ __('Direct Communication with Administration') }}</h6>
                    <p class="small text-muted mb-0" style="max-width: 420px; margin: 0 auto;">
                        {{ __('Any messages or updates sent by Admin regarding your store, document verifications, or inquiries will appear here. You can also write directly to the Admin team below.') }}
                    </p>
                </div>
            @endforelse
        </div>

        <!-- Input Footer -->
        <div class="admin-msg-footer">
            <input type="text" id="seller_admin_input" class="admin-msg-input" placeholder="{{ __('Type your message or reply to Administration & Support...') }}" onkeypress="handleSellerAdminInputKey(event)">
            <button type="button" class="admin-msg-send-btn" id="seller_admin_send_btn" onclick="sendSellerAdminMessage()">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>
</div>

<script>
    const sellerAdminSendUrl = "{{ route('seller.admin_message.send') }}";
    const sellerAdminFetchUrl = "{{ route('seller.admin_message.fetch') }}";
    const csrfToken = "{{ csrf_token() }}";
    const streamContainer = document.getElementById('admin_seller_messages_container');

    function scrollToBottom() {
        if (streamContainer) {
            streamContainer.scrollTop = streamContainer.scrollHeight;
        }
    }

    scrollToBottom();

    function fetchMessages() {
        fetch(sellerAdminFetchUrl, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success && data.messages) {
                renderMessages(data.messages);
            }
        })
        .catch(e => console.error('Fetch error', e));
    }

    function renderMessages(messages) {
        if (!streamContainer) return;

        if (messages.length === 0) {
            streamContainer.innerHTML = `
                <div class="text-center py-5 my-auto text-muted">
                    <div class="rounded-circle bg-light d-inline-flex p-3 mb-2 text-primary shadow-sm">
                        <i class="fas fa-user-shield fa-2x"></i>
                    </div>
                    <h6 class="font-weight-bold text-dark">{{ __('Direct Communication with Administration') }}</h6>
                    <p class="small text-muted mb-0" style="max-width: 420px; margin: 0 auto;">
                        {{ __('Any messages or updates sent by Admin regarding your store will appear here.') }}
                    </p>
                </div>
            `;
            return;
        }

        let html = '';
        messages.forEach(msg => {
            const isMe = msg.is_me;
            const bubbleClass = isMe ? 'msg-bubble-seller' : 'msg-bubble-admin';
            const senderTag = isMe 
                ? '<div style="font-size: 11px; font-weight: 700; color: #008069; margin-bottom: 3px;"><i class="fas fa-store mr-1"></i> {{ __('You (Store Owner)') }}</div>'
                : '<div style="font-size: 11px; font-weight: 700; color: #4f46e5; margin-bottom: 3px;"><i class="fas fa-user-shield mr-1"></i> {{ __('Administration & Support') }}</div>';
            const ticks = isMe ? '<span style="color: #53bdeb; font-weight: bold;">✓✓</span>' : '';

            html += `
                <div class="msg-bubble ${bubbleClass}">
                    ${senderTag}
                    <div>${escapeHtml(msg.message)}</div>
                    <div class="msg-bubble-meta">
                        <span>${msg.time}</span>
                        ${ticks}
                    </div>
                </div>
            `;
        });

        streamContainer.innerHTML = html;
        scrollToBottom();
    }

    function sendSellerAdminMessage() {
        const input = document.getElementById('seller_admin_input');
        const text = input.value.trim();
        if (!text) return;

        input.value = '';

        const now = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        const tempBubble = `
            <div class="msg-bubble msg-bubble-seller" style="opacity: 0.85;">
                <div style="font-size: 11px; font-weight: 700; color: #008069; margin-bottom: 3px;"><i class="fas fa-store mr-1"></i> {{ __('You (Store Owner)') }}</div>
                <div>${escapeHtml(text)}</div>
                <div class="msg-bubble-meta">
                    <span>${now}</span>
                    <span style="color: #53bdeb; font-weight: bold;">✓✓</span>
                </div>
            </div>
        `;
        streamContainer.insertAdjacentHTML('beforeend', tempBubble);
        scrollToBottom();

        fetch(sellerAdminSendUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ message: text })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                fetchMessages();
            }
        })
        .catch(err => console.error(err));
    }

    function handleSellerAdminInputKey(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            sendSellerAdminMessage();
        }
    }

    function escapeHtml(t) {
        const m = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return t.replace(/[&<>"']/g, function(k) { return m[k]; });
    }

    // Auto poll every 4 seconds
    setInterval(fetchMessages, 4000);
</script>
@endsection