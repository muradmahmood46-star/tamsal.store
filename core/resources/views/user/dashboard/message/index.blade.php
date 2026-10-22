@extends('master.front')

@section('title')
    {{ __('My Messages & Chats') }}
@endsection

@section('content')
<!-- Page Title-->
<div class="page-title d-none d-lg-block">
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
        max-width: 78%;
        padding: 9px 13px;
        font-size: 14px;
        line-height: 1.5;
        box-shadow: 0 1px 2px rgba(11, 20, 26, 0.12);
        position: relative;
        word-break: break-word;
        transition: transform 0.15s ease;
    }
    .user-bubble-me {
        align-self: flex-end;
        background: #d9fdd3;
        color: #111b21;
        border-radius: 14px 14px 2px 14px;
        border: 1px solid rgba(0, 128, 105, 0.12);
        margin-left: 15%;
    }
    .user-bubble-other {
        align-self: flex-start;
        background: #ffffff;
        color: #111b21;
        border-radius: 14px 14px 14px 2px;
        border: 1px solid rgba(0, 0, 0, 0.05);
        margin-right: 15%;
    }
    .user-bubble-sender {
        font-size: 11px;
        font-weight: 700;
        margin-bottom: 3px;
        display: flex;
        align-items: center;
        letter-spacing: -0.1px;
    }
    .user-bubble-meta {
        font-size: 10px;
        color: #64748b;
        text-align: right;
        margin-top: 4px;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 3px;
        user-select: none;
    }
    .user-bubble-meta .msg-ticks {
        color: #53bdeb;
        font-weight: 700;
        font-size: 11px;
    }
    .chat-date-pill {
        align-self: center;
        margin: 10px 0;
        user-select: none;
    }
    .chat-date-pill span {
        background: rgba(255, 255, 255, 0.9);
        color: #54656f;
        font-size: 11px;
        font-weight: 600;
        padding: 4px 12px;
        border-radius: 12px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.08);
        border: 1px solid rgba(0,0,0,0.05);
    }

    /* Swipe styles for chat list (both left and right) */
    .user-chat-swipe-wrapper {
        position: relative;
        overflow: hidden;
        background: #dc2626;
        border-bottom: 1px solid #f1f5f9;
        touch-action: pan-y;
    }
    .user-chat-swipe-btn {
        position: absolute;
        top: 0;
        width: 82px;
        height: 100%;
        background: #dc2626;
        color: #ffffff;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        font-size: 11.5px;
        font-weight: 700;
        gap: 4px;
        cursor: pointer;
        z-index: 1;
        border: none;
        outline: none;
        transition: background 0.2s;
    }
    .user-chat-swipe-btn-left {
        left: 0;
    }
    .user-chat-swipe-btn-right {
        right: 0;
    }
    .user-chat-swipe-btn:active {
        background: #b91c1c;
    }
    .user-chat-item {
        position: relative;
        background: #ffffff;
        z-index: 2;
        transition: transform 0.22s cubic-bezier(0.2, 0, 0, 1), background-color 0.15s ease;
        display: flex;
        align-items: center;
        padding: 12px 14px;
        text-decoration: none !important;
        color: inherit;
        user-select: none;
        -webkit-user-select: none;
        border-bottom: none !important;
    }
    .user-chat-item.is-swiped-right {
        transform: translateX(82px) !important;
    }
    .user-chat-item.is-swiped-left {
        transform: translateX(-82px) !important;
    }
    .chat-desktop-delete-btn {
        opacity: 0;
        transition: opacity 0.2s ease;
        padding: 4px;
    }
    .user-chat-item:hover .chat-desktop-delete-btn {
        opacity: 0.7;
    }
    .chat-desktop-delete-btn:hover {
        opacity: 1 !important;
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

    /* Search Bar in Chat Sidebar */
    .chat-search-wrap {
        position: relative;
        width: 100%;
    }
    .chat-search-icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 13px;
        pointer-events: none;
    }
    .chat-search-field {
        border-radius: 20px !important;
        padding-left: 34px !important;
        padding-right: 32px !important;
        background: #f1f5f9 !important;
        border: 1px solid #e2e8f0 !important;
        font-size: 13px !important;
        height: 36px !important;
        transition: all 0.2s ease;
    }
    .chat-search-field:focus {
        background: #ffffff !important;
        border-color: #008069 !important;
        box-shadow: 0 0 0 2px rgba(0, 128, 105, 0.15) !important;
    }
    .chat-search-clear-btn {
        position: absolute;
        right: 6px;
        top: 50%;
        transform: translateY(-50%);
        border: none;
        background: transparent;
        color: #94a3b8;
        padding: 4px 8px;
        font-size: 14px;
        cursor: pointer;
    }
    .chat-search-clear-btn:hover {
        color: #64748b;
    }

    @media (max-width: 991.98px) {
        /* Screen locked on mobile: outer website does not scroll */
        html, body, html.ios-shell-scroll, html.ios-shell-scroll body {
            overflow: hidden !important;
            height: 100% !important;
            height: 100dvh !important;
            max-height: 100dvh !important;
            position: fixed !important;
            width: 100% !important;
            top: 0 !important;
            left: 0 !important;
            margin: 0 !important;
            padding: 0 !important;
            overscroll-behavior: none !important;
        }

        .site-header,
        .site-footer,
        .page-title,
        .scroll-to-top-btn {
            display: none !important;
        }

        .user-messages-container {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: {{ $activeChat ? '0' : '58px' }} !important;
            width: 100% !important;
            height: {{ $activeChat ? '100dvh' : 'calc(100dvh - 58px)' }} !important;
            max-height: {{ $activeChat ? '100dvh' : 'calc(100dvh - 58px)' }} !important;
            padding: 0 !important;
            margin: 0 !important;
            overflow: hidden !important;
            z-index: 999 !important;
            background: #ffffff !important;
        }
        .user-messages-container .row {
            margin: 0 !important;
            height: 100% !important;
        }
        .user-messages-container .p-0-mobile {
            padding: 0 !important;
            height: 100% !important;
        }
        .user-chat-box {
            height: 100% !important;
            max-height: 100% !important;
            border-radius: 0 !important;
            border: none !important;
            flex-direction: column !important;
            width: 100% !important;
            margin: 0 !important;
            box-shadow: none !important;
        }

        @if($activeChat)
            .user-chat-sidebar {
                display: none !important;
            }
            .user-chat-main {
                display: flex !important;
                flex-direction: column !important;
                width: 100% !important;
                height: 100% !important;
                max-height: 100dvh !important;
                overflow: hidden !important;
            }
        @else
            .user-chat-sidebar {
                display: flex !important;
                flex-direction: column !important;
                width: 100% !important;
                height: 100% !important;
                min-width: 0 !important;
                border-right: none !important;
                overflow: hidden !important;
            }
            .user-chat-main {
                display: none !important;
            }
        @endif

        .user-chat-list {
            flex-grow: 1 !important;
            overflow-y: auto !important;
            -webkit-overflow-scrolling: touch !important;
            touch-action: pan-y !important;
            overscroll-behavior-y: contain;
            padding-bottom: 35px !important;
        }

        .user-chat-header {
            padding: 10px 12px !important;
            flex-shrink: 0 !important;
        }

        .user-chat-product-banner {
            padding: 8px 12px !important;
            flex-wrap: wrap !important;
            gap: 6px !important;
            flex-shrink: 0 !important;
        }

        .user-chat-product-banner > div {
            max-width: calc(100% - 95px) !important;
        }

        .user-chat-stream {
            flex-grow: 1 !important;
            overflow-y: auto !important;
            -webkit-overflow-scrolling: touch !important;
            padding: 12px 10px !important;
        }

        .user-bubble {
            max-width: 88% !important;
            font-size: 13.5px !important;
        }

        .user-chat-footer {
            padding: 8px 10px !important;
            flex-shrink: 0 !important;
            background: #f0f2f5 !important;
        }

        .user-chat-input {
            font-size: 13.5px !important;
            padding: 8px 14px !important;
        }

        .user-chat-send {
            width: 38px !important;
            height: 38px !important;
            font-size: 14px !important;
        }
    }
</style>

<div class="container user-messages-container padding-bottom-2x mb-1">
    <div class="row">
        @include('includes.user_sitebar')
        <div class="col-12 col-lg-8 p-0-mobile">
            <div class="user-chat-box">
                <!-- Sidebar Conversations -->
                <div class="user-chat-sidebar">
                    <div class="user-chat-sidebar-header p-2 p-sm-3 bg-white border-bottom">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="d-flex align-items-center">
                                <a href="{{ route('user.dashboard') }}" class="btn btn-sm btn-light border mr-2 d-lg-none shadow-sm" style="border-radius: 8px; padding: 5px 10px; margin-right: 8px;" title="{{ __('Back to Dashboard') }}">
                                    <i class="fas fa-arrow-left"></i>
                                </a>
                                <h6 class="mb-0 font-weight-bold text-dark d-flex align-items-center" style="font-size: 16px;">
                                    <i class="icon-message-square text-success mr-2" style="margin-right: 6px; font-size: 18px;"></i> {{ __('My Messages') }}
                                </h6>
                            </div>
                            @php
                                $totalUnread = $conversations->sum('user_unread_count');
                            @endphp
                            @if($totalUnread > 0)
                                <span class="badge badge-success font-weight-bold" style="font-size: 11px; border-radius: 10px; padding: 3px 8px;">{{ $totalUnread }} {{ __('new') }}</span>
                            @endif
                        </div>

                        <!-- Search Input for Messages -->
                        <div class="chat-search-wrap position-relative">
                            <i class="fas fa-search chat-search-icon"></i>
                            <input type="text" id="chat_search_input" class="form-control form-control-sm chat-search-field" placeholder="{{ __('Search stores, products, messages...') }}" autocomplete="off">
                            <button type="button" id="chat_search_clear" class="btn btn-sm chat-search-clear-btn" style="display: none;" title="{{ __('Clear search') }}">
                                <i class="fas fa-times-circle"></i>
                            </button>
                        </div>
                    </div>

                    <div class="user-chat-list">
                        <!-- No search match banner -->
                        <div id="chat_no_match_item" class="p-4 text-center text-muted" style="display: none;">
                            <div class="mb-2" style="width: 48px; height: 48px; border-radius: 50%; background: #f1f5f9; display: inline-flex; align-items: center; justify-content: center; margin: 0 auto;">
                                <i class="fas fa-search text-secondary" style="font-size: 20px;"></i>
                            </div>
                            <h6 class="font-weight-bold text-dark mb-1" style="font-size: 14px;">{{ __('No matching conversations') }}</h6>
                            <p class="small text-muted mb-0">{{ __('No store, product, or message matched') }} "<span class="no-match-query font-weight-bold text-dark"></span>".</p>
                        </div>

                        @forelse($conversations as $conv)
                            @php
                                $isActive = ($activeChat && $activeChat->id == $conv->id);
                                $allMsgText = $conv->messages->pluck('message')->implode(' ');
                            @endphp
                            <div class="user-chat-swipe-wrapper" id="chat_wrapper_{{ $conv->id }}">
                                <button type="button" class="user-chat-swipe-btn user-chat-swipe-btn-left" onclick="confirmDeleteChat({{ $conv->id }}, '{{ addslashes($conv->store_name) }}')" title="{{ __('Delete') }}">
                                    <i class="fas fa-trash-alt"></i>
                                    <span>{{ __('Delete') }}</span>
                                </button>
                                <button type="button" class="user-chat-swipe-btn user-chat-swipe-btn-right" onclick="confirmDeleteChat({{ $conv->id }}, '{{ addslashes($conv->store_name) }}')" title="{{ __('Delete') }}">
                                    <i class="fas fa-trash-alt"></i>
                                    <span>{{ __('Delete') }}</span>
                                </button>
                                <a href="{{ route('user.message.index', ['chat_id' => $conv->id]) }}" 
                                   class="user-chat-item {{ $isActive ? 'active' : '' }}"
                                   data-chat-id="{{ $conv->id }}"
                                   data-store-name="{{ strtolower($conv->store_name) }}"
                                   data-store-display="{{ $conv->store_name }}"
                                   data-product-name="{{ strtolower($conv->item ? $conv->item->name : '') }}"
                                   data-last-message="{{ strtolower($conv->last_message) }}"
                                   data-all-messages="{{ strtolower($allMsgText) }}">
                                    <div class="user-chat-avatar">
                                        <i class="fas fa-store"></i>
                                    </div>
                                    <div style="flex-grow: 1; overflow: hidden;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="chat-title-row d-flex align-items-center text-truncate mr-1">
                                                <strong style="font-size: 13.5px;" class="text-dark text-truncate">{{ $conv->store_name }}</strong>
                                            </div>
                                            <div class="d-flex align-items-center flex-shrink-0">
                                                <small class="text-muted mr-1" style="font-size: 10.5px;">{{ $conv->last_message_at ? $conv->last_message_at->diffForHumans(null, true) : '' }}</small>
                                                <button type="button" class="btn btn-sm text-danger p-0 chat-desktop-delete-btn d-none d-lg-inline-block" onclick="event.preventDefault(); event.stopPropagation(); confirmDeleteChat({{ $conv->id }}, '{{ addslashes($conv->store_name) }}');" title="{{ __('Delete Conversation') }}">
                                                    <i class="fas fa-trash-alt" style="font-size: 11px;"></i>
                                                </button>
                                            </div>
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
                            </div>
                        @empty
                            <div class="text-center py-5 px-3 text-muted">
                                <div class="mb-3" style="width: 60px; height: 60px; border-radius: 50%; background: #f1f5f9; display: inline-flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-comment-slash fa-2x text-muted"></i>
                                </div>
                                <h6 class="font-weight-bold text-dark mb-1" style="font-size: 15px;">{{ __('No messages to show') }}</h6>
                                <p class="text-muted small mb-3">{{ __('You don\'t have any store messages yet. Visit any product and click "Chat with Seller" to message a store!') }}</p>
                                <a href="{{ route('front.catalog') }}" class="btn btn-sm btn-primary px-3 shadow-sm" style="border-radius: 20px; font-size: 12px;">
                                    <i class="icon-shopping-bag mr-1"></i> {{ __('Browse Products') }}
                                </a>
                            </div>
                        @endforelse

                        @if($conversations->isNotEmpty())
                            <div class="chat-swipe-hint text-center py-3" style="font-size: 11px; color: #94a3b8; user-select: none;">
                                <i class="fas fa-arrows-alt-h mr-1" style="font-size: 10px;"></i> {{ __('Swipe left/right for actions') }}
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Chat Main Pane -->
                <div class="user-chat-main">
                    @if($activeChat)
                        <!-- Header -->
                        <div class="user-chat-header">
                            <div class="d-flex align-items-center" style="min-width: 0;">
                                <a href="{{ route('user.message.index') }}" class="btn btn-sm btn-light border mr-2 d-lg-none text-dark shadow-sm" style="border-radius: 6px; padding: 5px 10px; flex-shrink: 0;" title="{{ __('Back to Chats') }}">
                                    <i class="fas fa-arrow-left"></i>
                                </a>
                                <div class="user-chat-avatar mr-2" style="width: 38px; height: 38px; font-size: 15px;">
                                    <i class="fas fa-store"></i>
                                </div>
                                <div style="min-width: 0;">
                                    <h6 class="mb-0 font-weight-bold text-dark text-truncate" style="font-size: 14px;">
                                        {{ $activeChat->store_name }}
                                        <span class="badge badge-success ml-1 py-1 px-2 font-weight-normal d-none d-sm-inline-block" style="font-size: 11px;">
                                            <i class="fas fa-check-circle mr-1"></i>{{ ($activeChat->vendor_id > 0) ? __('Verified Store') : __('Official Store') }}
                                        </span>
                                    </h6>
                                    <small class="text-muted d-block text-truncate" style="font-size: 11px;">{{ __('Direct Chat with Store Seller') }}</small>
                                </div>
                            </div>
                            <div class="flex-shrink-0 ml-2">
                                <a href="{{ route('user.message.delete', $activeChat->id) }}" class="btn btn-outline-danger btn-sm" onclick="return confirm('{{ __('Are you sure you want to delete this chat?') }}')">
                                    <i class="fas fa-trash-alt mr-1"></i> <span class="d-none d-sm-inline">{{ __('Delete') }}</span>
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
                            @php $lastDate = null; @endphp
                            @forelse($messages as $msg)
                                @php
                                    $isMe = ($msg->sender_type === 'user');
                                    $isAdmin = ($msg->sender_type === 'admin');
                                    $msgDate = $msg->created_at ? $msg->created_at->format('M d, Y') : '';
                                @endphp
                                @if($msgDate && $msgDate !== $lastDate)
                                    <div class="chat-date-pill"><span>{{ $msgDate }}</span></div>
                                    @php $lastDate = $msgDate; @endphp
                                @endif
                                <div class="user-bubble {{ $isMe ? 'user-bubble-me' : 'user-bubble-other' }}" style="{{ $isAdmin ? 'border-left: 3px solid #4f46e5;' : '' }}">
                                    @if($isAdmin)
                                        <div class="user-bubble-sender" style="color: #4f46e5;">
                                            <i class="fas fa-user-shield mr-1"></i> {{ __('Administration / Support') }}
                                        </div>
                                    @elseif(!$isMe)
                                        <div class="user-bubble-sender text-success">
                                            <i class="fas fa-store mr-1"></i> {{ $activeChat->store_name }}
                                        </div>
                                    @endif
                                    <div style="white-space: pre-wrap; word-break: break-word; line-height: 1.55;">{!! \App\Helpers\Helper::formatChatMessage($msg->message) !!}</div>
                                    <div class="user-bubble-meta">
                                        {{ $msg->created_at ? $msg->created_at->format('h:i A') : '' }}
                                        @if($isMe)
                                            <span class="msg-ticks">✓✓</span>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-5 text-muted my-auto">
                                    <i class="fas fa-comment-dots fa-2x text-muted mb-2 d-block"></i>
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
                        <div class="text-center py-5 my-auto text-muted px-3">
                            @if($conversations->isEmpty())
                                <div class="mb-3" style="width: 70px; height: 70px; border-radius: 50%; background: #f8fafc; display: inline-flex; align-items: center; justify-content: center; border: 1px solid #e2e8f0;">
                                    <i class="fas fa-comment-slash fa-2x text-secondary"></i>
                                </div>
                                <h5 class="font-weight-bold text-dark mb-1">{{ __('No messages to show') }}</h5>
                                <p class="small text-muted mb-3">{{ __('No chats available. When you message a store seller from a product page, your conversation will appear here.') }}</p>
                                <a href="{{ route('front.catalog') }}" class="btn btn-sm btn-primary px-3 shadow-sm" style="border-radius: 20px; font-size: 13px;">
                                    <i class="icon-shopping-bag mr-1"></i> {{ __('Explore Shop') }}
                                </a>
                            @else
                                <i class="fas fa-comments fa-3x text-secondary mb-2 d-block"></i>
                                <h6 class="font-weight-bold text-dark">{{ __('Select a Chat') }}</h6>
                                <p class="small">{{ __('Select a store conversation from the left to start chatting.') }}</p>
                            @endif
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
                <div style="white-space: pre-wrap; word-break: break-word; line-height: 1.55;">${formatChatMessage(text)}</div>
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

    function fetchUserMessages() {
        fetch(userFetchUrl, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.messages) {
                let html = '';
                let lastDate = '';
                const storeName = "{{ addslashes($activeChat->store_name) }}";

                data.messages.forEach(msg => {
                    const isMe = msg.is_me;
                    const isAdmin = (msg.sender_type === 'admin');
                    const bubbleClass = isMe ? 'user-bubble-me' : 'user-bubble-other';
                    const ticks = isMe ? '<span class="msg-ticks">✓✓</span>' : '';
                    
                    if (msg.date && msg.date !== lastDate) {
                        html += `<div class="chat-date-pill"><span>${escapeHtml(msg.date)}</span></div>`;
                        lastDate = msg.date;
                    }

                    let senderHeader = '';
                    if (isAdmin) {
                        senderHeader = '<div class="user-bubble-sender" style="color: #4f46e5;"><i class="fas fa-user-shield mr-1"></i> {{ __("Administration / Support") }}</div>';
                    } else if (!isMe) {
                        senderHeader = `<div class="user-bubble-sender text-success"><i class="fas fa-store mr-1"></i> ${escapeHtml(storeName)}</div>`;
                    }
                    const borderStyle = isAdmin ? 'border-left: 3px solid #4f46e5;' : '';

                    html += `
                        <div class="user-bubble ${bubbleClass}" style="${borderStyle}">
                            ${senderHeader}
                            <div style="white-space: pre-wrap; word-break: break-word; line-height: 1.55;">${formatChatMessage(msg.message)}</div>
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

<!-- Custom Clean Delete Confirmation Modal (Self-contained, No Bootstrap backdrop trap) -->
<div id="customDeletePopup" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; width: 100vw; height: 100dvh; background: rgba(0,0,0,0.55); z-index: 999999; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: #ffffff; width: 100%; max-width: 320px; border-radius: 18px; padding: 22px 18px; text-align: center; box-shadow: 0 16px 36px rgba(0,0,0,0.3);">
        <div style="width: 50px; height: 50px; border-radius: 50%; background: #fee2e2; color: #dc2626; display: inline-flex; align-items: center; justify-content: center; font-size: 22px; margin-bottom: 12px; margin-left: auto; margin-right: auto;">
            <i class="fas fa-trash-alt"></i>
        </div>
        <h6 style="font-size: 16px; font-weight: 700; color: #0f172a; margin-bottom: 6px;">{{ __('Delete Conversation?') }}</h6>
        <p style="font-size: 12.5px; color: #64748b; margin-bottom: 18px; line-height: 1.4;">
            {{ __('Are you sure you want to delete this chat with') }} <strong id="customDeleteStoreName" style="color: #0f172a;"></strong>?
        </p>
        <div style="display: flex; gap: 10px;">
            <button type="button" onclick="closeCustomDeleteModal()" style="flex: 1; padding: 9px 0; border: 1px solid #cbd5e1; background: #f8fafc; color: #334155; border-radius: 10px; font-weight: 600; font-size: 13px; cursor: pointer;">
                {{ __('Cancel') }}
            </button>
            <button type="button" onclick="executeCustomDelete()" style="flex: 1; padding: 9px 0; border: none; background: #dc2626; color: #ffffff; border-radius: 10px; font-weight: 600; font-size: 13px; cursor: pointer; box-shadow: 0 2px 8px rgba(220,38,38,0.35);">
                <i class="fas fa-trash-alt mr-1"></i> {{ __('Delete') }}
            </button>
        </div>
    </div>
</div>

<script>
let pendingDeleteId = null;
let currentSwipedItem = null;

function confirmDeleteChat(chatId, storeName) {
    pendingDeleteId = chatId;
    const storeLabel = document.getElementById('customDeleteStoreName');
    if (storeLabel) storeLabel.textContent = storeName || 'this store';
    const popup = document.getElementById('customDeletePopup');
    if (popup) {
        popup.style.display = 'flex';
    } else {
        if (confirm("{{ __('Are you sure you want to delete this conversation?') }}")) {
            executeCustomDelete();
        }
    }
}

function closeCustomDeleteModal() {
    pendingDeleteId = null;
    const popup = document.getElementById('customDeletePopup');
    if (popup) popup.style.display = 'none';
}

function executeCustomDelete() {
    if (!pendingDeleteId) return;
    const chatId = pendingDeleteId;
    closeCustomDeleteModal();

    const deleteUrl = "{{ url('/user/messages/delete') }}/" + chatId;
    const wrapper = document.getElementById('chat_wrapper_' + chatId);

    fetch(deleteUrl, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (wrapper) {
            wrapper.style.transition = 'all 0.3s ease';
            wrapper.style.maxHeight = wrapper.offsetHeight + 'px';
            wrapper.style.opacity = '1';
            setTimeout(() => {
                wrapper.style.maxHeight = '0px';
                wrapper.style.opacity = '0';
                wrapper.style.padding = '0';
                wrapper.style.margin = '0';
                wrapper.style.border = 'none';
            }, 10);
            setTimeout(() => {
                wrapper.remove();
                const remaining = document.querySelectorAll('.user-chat-swipe-wrapper');
                if (remaining.length === 0) {
                    location.href = "{{ route('user.message.index') }}";
                }
            }, 350);
        } else {
            location.href = "{{ route('user.message.index') }}";
        }
    })
    .catch(err => {
        location.href = deleteUrl;
    });
}

function setupSwipeHandlers() {
    const wrappers = document.querySelectorAll('.user-chat-swipe-wrapper');
    
    wrappers.forEach(wrapper => {
        const item = wrapper.querySelector('.user-chat-item');
        if (!item) return;

        let startX = 0;
        let startY = 0;
        let isSwiping = false;
        let isScrolling = false;
        let initialTransform = 0;

        item.addEventListener('touchstart', function(e) {
            if (e.touches.length !== 1) return;
            const touch = e.touches[0];
            startX = touch.clientX;
            startY = touch.clientY;
            isSwiping = false;
            isScrolling = false;

            if (item.classList.contains('is-swiped-right')) {
                initialTransform = 82;
            } else if (item.classList.contains('is-swiped-left')) {
                initialTransform = -82;
            } else {
                initialTransform = 0;
            }

            // Close other swiped items
            if (currentSwipedItem && currentSwipedItem !== item) {
                currentSwipedItem.classList.remove('is-swiped-right', 'is-swiped-left');
                currentSwipedItem.style.transform = '';
                currentSwipedItem = null;
            }
        }, { passive: true });

        item.addEventListener('touchmove', function(e) {
            if (e.touches.length !== 1) return;
            const touch = e.touches[0];
            const diffX = touch.clientX - startX;
            const diffY = touch.clientY - startY;

            // Detect vertical scroll
            if (!isSwiping && Math.abs(diffY) > Math.abs(diffX) && Math.abs(diffY) > 6) {
                isScrolling = true;
                return;
            }

            // Detect horizontal swipe (left OR right)
            if (!isScrolling && Math.abs(diffX) > 8) {
                isSwiping = true;
                item.style.transition = 'none';

                let targetX = initialTransform + diffX;
                if (targetX > 110) targetX = 110;
                if (targetX < -110) targetX = -110;
                item.style.transform = `translateX(${targetX}px)`;
            }
        }, { passive: true });

        item.addEventListener('touchend', function(e) {
            if (isSwiping) {
                item.style.transition = 'transform 0.22s cubic-bezier(0.2, 0, 0, 1)';
                const match = /translateX\(([\d\.-]+)px\)/.exec(item.style.transform);
                const currentVal = match ? parseFloat(match[1]) : 0;

                if (currentVal > 40) {
                    // Swiped Right -> reveals Left Delete Button
                    item.classList.add('is-swiped-right');
                    item.classList.remove('is-swiped-left');
                    item.style.transform = 'translateX(82px)';
                    currentSwipedItem = item;
                } else if (currentVal < -40) {
                    // Swiped Left -> reveals Right Delete Button
                    item.classList.add('is-swiped-left');
                    item.classList.remove('is-swiped-right');
                    item.style.transform = 'translateX(-82px)';
                    currentSwipedItem = item;
                } else {
                    // Snap Closed
                    item.classList.remove('is-swiped-right', 'is-swiped-left');
                    item.style.transform = '';
                    if (currentSwipedItem === item) currentSwipedItem = null;
                }
            }
        });

        item.addEventListener('touchcancel', function() {
            item.style.transition = 'transform 0.22s cubic-bezier(0.2, 0, 0, 1)';
            if (!item.classList.contains('is-swiped-right') && !item.classList.contains('is-swiped-left')) {
                item.style.transform = '';
            }
        });

        // Click handler: if item is swiped open, click closes it instead of navigating
        item.addEventListener('click', function(e) {
            if (item.classList.contains('is-swiped-right') || item.classList.contains('is-swiped-left')) {
                e.preventDefault();
                e.stopPropagation();
                item.classList.remove('is-swiped-right', 'is-swiped-left');
                item.style.transform = '';
                currentSwipedItem = null;
                return false;
            }
        });
    });

    // Tap outside to close swiped item
    document.addEventListener('touchstart', function(e) {
        if (currentSwipedItem && !currentSwipedItem.contains(e.target) && !e.target.closest('.user-chat-swipe-btn')) {
            currentSwipedItem.classList.remove('is-swiped-right', 'is-swiped-left');
            currentSwipedItem.style.transform = '';
            currentSwipedItem = null;
        }
    }, { passive: true });
}

document.addEventListener('DOMContentLoaded', function() {
    setupSwipeHandlers();

    const searchInput = document.getElementById('chat_search_input');
    const clearBtn = document.getElementById('chat_search_clear');
    const chatList = document.querySelector('.user-chat-list');
    const noMatchEl = document.getElementById('chat_no_match_item');
    const hintEl = chatList ? chatList.querySelector('.chat-swipe-hint') : null;
    if (!searchInput || !chatList) return;

    const chatWrappers = Array.from(chatList.querySelectorAll('.user-chat-swipe-wrapper'));

    chatWrappers.forEach((wrapper, idx) => {
        wrapper.dataset.originalIndex = idx;
    });

    searchInput.addEventListener('input', function() {
        const query = this.value.trim().toLowerCase();

        if (clearBtn) {
            clearBtn.style.display = query ? 'block' : 'none';
        }

        if (hintEl) {
            hintEl.style.display = query ? 'none' : 'block';
        }

        if (!query) {
            chatWrappers.sort((a, b) => parseInt(a.dataset.originalIndex) - parseInt(b.dataset.originalIndex));
            chatWrappers.forEach(wrapper => {
                wrapper.style.display = 'block';
                chatList.appendChild(wrapper);
                const badge = wrapper.querySelector('.search-match-badge');
                if (badge) badge.remove();
            });
            if (hintEl) chatList.appendChild(hintEl);
            if (noMatchEl) noMatchEl.style.display = 'none';
            return;
        }

        let matchedCount = 0;
        const scoredItems = [];

        chatWrappers.forEach(wrapper => {
            const item = wrapper.querySelector('.user-chat-item');
            if (!item) return;

            const store = item.dataset.storeName || '';
            const product = item.dataset.productName || '';
            const allMsg = item.dataset.allMessages || '';
            const lastMsg = item.dataset.lastMessage || '';

            let score = 0;
            let matchType = '';

            // Priority 1: Store Name Match (Highest priority)
            if (store.includes(query)) {
                score = 300;
                if (store.startsWith(query)) score += 50;
                matchType = 'store';
            }
            // Priority 2: Product Title Match (2nd priority)
            else if (product.includes(query)) {
                score = 200;
                if (product.startsWith(query)) score += 30;
                matchType = 'product';
            }
            // Priority 3: Message Text Match (3rd priority)
            else if (lastMsg.includes(query) || allMsg.includes(query)) {
                score = 100;
                matchType = 'message';
            }

            if (score > 0) {
                matchedCount++;
                scoredItems.push({ wrapper, item, score, matchType });
            } else {
                wrapper.style.display = 'none';
                const badge = item.querySelector('.search-match-badge');
                if (badge) badge.remove();
            }
        });

        if (matchedCount > 0) {
            if (noMatchEl) noMatchEl.style.display = 'none';

            scoredItems.sort((a, b) => b.score - a.score);

            scoredItems.forEach(({ wrapper, item, matchType }) => {
                wrapper.style.display = 'block';
                chatList.appendChild(wrapper);

                let badge = item.querySelector('.search-match-badge');
                if (!badge) {
                    badge = document.createElement('span');
                    badge.className = 'search-match-badge ml-1';
                    const titleRow = item.querySelector('.chat-title-row');
                    if (titleRow) {
                        titleRow.appendChild(badge);
                    }
                }
                if (matchType === 'store') {
                    badge.innerHTML = '<span class="badge badge-success" style="font-size: 9px; padding: 2px 5px;"><i class="fas fa-store mr-1"></i>Store</span>';
                } else if (matchType === 'product') {
                    badge.innerHTML = '<span class="badge badge-primary" style="font-size: 9px; padding: 2px 5px;"><i class="fas fa-box mr-1"></i>Product</span>';
                } else {
                    badge.innerHTML = '<span class="badge badge-secondary" style="font-size: 9px; padding: 2px 5px;"><i class="fas fa-comment mr-1"></i>Message</span>';
                }
            });
        } else {
            if (noMatchEl) {
                noMatchEl.style.display = 'block';
                const queryText = noMatchEl.querySelector('.no-match-query');
                if (queryText) queryText.textContent = query;
            }
        }
    });

    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            searchInput.value = '';
            searchInput.dispatchEvent(new Event('input'));
            searchInput.focus();
        });
    }
});
</script>
@endsection
