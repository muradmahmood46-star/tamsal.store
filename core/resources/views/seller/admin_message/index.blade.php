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
        .msg-bubble-content {
            white-space: pre-wrap;
            word-break: break-word;
            line-height: 1.55;
            font-size: 14px;
            font-family: inherit;
        }
        .msg-bubble-content strong,
        .msg-bubble-content b {
            font-weight: 700;
        }
        .msg-bubble-content em,
        .msg-bubble-content i {
            font-style: italic;
        }
        .msg-bubble-content a {
            color: #2563eb;
            text-decoration: underline;
            word-break: break-all;
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
            padding: 10px 18px;
            background: #f0f2f5;
            border-top: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            gap: 6px;
            flex-shrink: 0;
        }
        .admin-msg-tools {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .admin-msg-tool-btn {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            color: #334155;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 12px;
            cursor: pointer;
            line-height: 1.3;
            transition: all 0.15s;
        }
        .admin-msg-tool-btn:hover {
            background: #e2e8f0;
            color: #0f172a;
        }
        .admin-msg-input-wrap {
            display: flex;
            align-items: flex-end;
            gap: 10px;
        }
        .admin-msg-input {
            flex-grow: 1;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            padding: 9px 15px;
            font-size: 14px;
            outline: none;
            transition: border 0.2s;
            resize: none;
            min-height: 42px;
            max-height: 140px;
            line-height: 1.45;
            overflow-y: auto;
            font-family: inherit;
        }
        .admin-msg-input:focus {
            border-color: #008069;
        }
        .admin-msg-send-btn {
            background: #008069;
            color: #ffffff;
            border: none;
            border-radius: 50%;
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.2s;
            flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(0, 128, 105, 0.4);
            margin-bottom: 1px;
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

            .msg-bubble-content {
                font-size: 13.5px !important;
            }

            .admin-msg-footer {
                padding: 8px 10px !important;
                position: sticky !important;
                bottom: 0 !important;
                z-index: 10 !important;
            }

            .admin-msg-input {
                font-size: 13.5px !important;
                padding: 8px 12px !important;
                min-height: 38px !important;
            }

            .admin-msg-send-btn {
                width: 38px !important;
                height: 38px !important;
                font-size: 14px !important;
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
                    <div class="msg-bubble-content">{!! \App\Helpers\Helper::formatChatMessage($msg->message) !!}</div>
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
            <div class="admin-msg-tools">
                <button type="button" class="admin-msg-tool-btn font-weight-bold" onclick="insertMsgFormat('seller_admin_input', '**', '**')" title="{{ __('Bold (**text**)') }}"><b>B</b></button>
                <button type="button" class="admin-msg-tool-btn font-italic" onclick="insertMsgFormat('seller_admin_input', '_', '_')" title="{{ __('Italic (_text_)') }}"><i>I</i></button>
                <button type="button" class="admin-msg-tool-btn" onclick="insertMsgFormat('seller_admin_input', '<u>', '</u>')" title="{{ __('Underline (<u>text</u>)') }}"><u>U</u></button>
                <button type="button" class="admin-msg-tool-btn" onclick="insertMsgFormat('seller_admin_input', '\n• ', '')" title="{{ __('Bullet point') }}"><i class="fas fa-list-ul"></i></button>
                <small class="text-muted ml-auto d-none d-sm-inline" style="font-size: 11px;">
                    <i class="fas fa-info-circle mr-1"></i>{{ __('Line gaps and bold formatting are preserved') }}
                </small>
            </div>
            <div class="admin-msg-input-wrap">
                <textarea id="seller_admin_input" class="admin-msg-input" rows="1" placeholder="{{ __('Type your message or reply to Administration (Shift+Enter for new line gap)...') }}" onkeydown="handleSellerAdminInputKey(event)" oninput="autoExpandTextarea(this)"></textarea>
                <button type="button" class="admin-msg-send-btn" id="seller_admin_send_btn" onclick="sendSellerAdminMessage()" title="{{ __('Send Message') }}">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
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

    function autoExpandTextarea(el) {
        if (!el) return;
        el.style.height = 'auto';
        el.style.height = Math.min(el.scrollHeight, 140) + 'px';
    }

    function insertMsgFormat(elemId, prefix, suffix) {
        const el = document.getElementById(elemId);
        if (!el) return;
        const start = el.selectionStart || 0;
        const end = el.selectionEnd || 0;
        const text = el.value;
        const selected = text.substring(start, end);
        const replacement = prefix + (selected || '') + (suffix || '');
        el.value = text.substring(0, start) + replacement + text.substring(end);
        el.focus();
        const newPos = selected ? start + replacement.length : start + prefix.length;
        el.setSelectionRange(newPos, newPos);
        autoExpandTextarea(el);
    }

    function formatChatMessage(text) {
        if (!text) return '';

        // Step 1: Escape basic HTML entities to prevent XSS
        let escaped = text
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');

        // Step 2: Markdown bold (**text** or __text__)
        escaped = escaped.replace(/\*\*(.+?)\*\*/gs, '<strong>$1</strong>');
        escaped = escaped.replace(/__(.+?)__/gs, '<strong>$1</strong>');

        // Step 3: Markdown single asterisk *bold* and _italic_
        escaped = escaped.replace(/(^|\s)\*([^\s\*].*?[^\s\*]|[^\s\*])\*($|\s|[,\.\?!:;])/gs, '$1<strong>$2</strong>$3');
        escaped = escaped.replace(/(^|\s)_([^\s_].*?[^\s_]|[^\s_])_($|\s|[,\.\?!:;])/gs, '$1<em>$2</em>$3');

        // Step 4: Strikethrough (~~text~~ or ~text~)
        escaped = escaped.replace(/~~(.+?)~~/gs, '<del>$1</del>');
        escaped = escaped.replace(/(^|\s)~([^\s~].*?[^\s~]|[^\s~])~($|\s|[,\.\?!:;])/gs, '$1<del>$2</del>$3');

        // Step 5: Inline code (`text`)
        escaped = escaped.replace(/`(.+?)`/gs, '<code style="background: rgba(0,0,0,0.06); padding: 1px 4px; border-radius: 3px; font-family: monospace;">$1</code>');

        // Step 6: Safe standard formatting tags
        escaped = escaped.replace(/&lt;(\/?)(b|strong|i|em|u|del|s|mark|code)&gt;/gi, '<$1$2>');
        escaped = escaped.replace(/&lt;font color=(&quot;|'|)([a-zA-Z0-9#]+)\1&gt;(.*?)&lt;\/font&gt;/gi, '<font color="$2">$3</font>');

        // Step 7: Auto linkify URLs
        const urlPattern = /(?<!href="|">)(https?:\/\/[^\s<]+)/gi;
        escaped = escaped.replace(urlPattern, '<a href="$1" target="_blank" rel="noopener noreferrer" style="color: #2563eb; text-decoration: underline; word-break: break-all;">$1</a>');

        return escaped;
    }

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
                    <div class="msg-bubble-content">${formatChatMessage(msg.message)}</div>
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
        input.style.height = 'auto';

        const now = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        const tempBubble = `
            <div class="msg-bubble msg-bubble-seller" style="opacity: 0.85;">
                <div style="font-size: 11px; font-weight: 700; color: #008069; margin-bottom: 3px;"><i class="fas fa-store mr-1"></i> {{ __('You (Store Owner)') }}</div>
                <div class="msg-bubble-content">${formatChatMessage(text)}</div>
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
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendSellerAdminMessage();
        }
    }

    // Auto poll every 4 seconds
    setInterval(fetchMessages, 4000);
</script>
@endsection