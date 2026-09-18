@extends('master.back')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
        <div class="card-body py-3">
            <div class="d-sm-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-0 text-dark font-weight-bold">
                        <i class="fas fa-unlock-alt text-primary mr-2"></i> {{ __('Store Unblock Requests') }}
                    </h4>
                    <p class="text-muted small mb-0">{{ __('Manage unblock appeals from suspended vendor stores, view complete chat history, and restore store access.') }}</p>
                </div>
                <div class="mt-2 mt-sm-0">
                    <span class="badge badge-warning text-dark px-3 py-2 font-weight-bold" style="font-size: 13px; border-radius: 20px;">
                        <i class="fas fa-clock mr-1"></i> {{ $counts['pending'] }} {{ __('Pending Appeals') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    @include('alerts.alerts')

    <!-- Status Tabs & Search -->
    <div class="row mb-3">
        <div class="col-xl-7 col-lg-6 col-12 mb-2 mb-lg-0">
            <div class="btn-group flex-wrap w-100 w-lg-auto" role="group">
                <a href="{{ route('back.unblock_request.index') }}" class="btn btn-sm {{ empty($status) ? 'btn-primary font-weight-bold' : 'btn-outline-primary' }}">
                    {{ __('All Stores') }} <span class="badge badge-light ml-1">{{ $counts['all'] }}</span>
                </a>
                <a href="{{ route('back.unblock_request.index', ['status' => 'Pending']) }}" class="btn btn-sm {{ $status == 'Pending' ? 'btn-warning text-dark font-weight-bold' : 'btn-outline-warning text-dark' }}">
                    <i class="fas fa-clock mr-1"></i> {{ __('Pending') }} <span class="badge badge-warning ml-1">{{ $counts['pending'] }}</span>
                </a>
                <a href="{{ route('back.unblock_request.index', ['status' => 'Pending Fine']) }}" class="btn btn-sm {{ $status == 'Pending Fine' ? 'btn-warning text-dark font-weight-bold' : 'btn-outline-warning text-dark' }}" style="border-color: #f59e0b;">
                    <i class="fas fa-file-invoice-dollar mr-1 text-danger"></i> {{ __('Pending Fine') }} <span class="badge badge-warning ml-1">{{ $counts['pending_fine'] ?? 0 }}</span>
                </a>
                <a href="{{ route('back.unblock_request.index', ['status' => 'Replied']) }}" class="btn btn-sm {{ $status == 'Replied' ? 'btn-info text-white font-weight-bold' : 'btn-outline-info' }}">
                    <i class="fas fa-reply mr-1"></i> {{ __('Replied') }} <span class="badge badge-info ml-1">{{ $counts['replied'] }}</span>
                </a>
                <a href="{{ route('back.unblock_request.index', ['status' => 'Unblocked']) }}" class="btn btn-sm {{ $status == 'Unblocked' ? 'btn-success text-white font-weight-bold' : 'btn-outline-success' }}">
                    <i class="fas fa-check-circle mr-1"></i> {{ __('Unblocked') }} <span class="badge badge-success ml-1">{{ $counts['unblocked'] }}</span>
                </a>
            </div>
        </div>

        <div class="col-xl-5 col-lg-6 col-12">
            <!-- Search Form -->
            <form action="{{ route('back.unblock_request.index') }}" method="GET" class="d-flex w-100">
                @if($status)
                    <input type="hidden" name="status" value="{{ $status }}">
                @endif
                <div class="input-group input-group-sm w-100">
                    <input type="text" name="search" class="form-control" placeholder="{{ __('Search store, name, email...') }}" value="{{ request('search') }}" style="border-radius: 20px 0 0 20px; font-size: 13.5px; height: 38px;">
                    <div class="input-group-append">
                        <button class="btn btn-primary px-3" type="submit" style="{{ request('search') ? '' : 'border-radius: 0 20px 20px 0;' }} height: 38px;">
                            <i class="fas fa-search"></i>
                        </button>
                        @if(request('search'))
                            <a href="{{ route('back.unblock_request.index', $status ? ['status' => $status] : []) }}" class="btn btn-secondary px-3" style="border-radius: 0 20px 20px 0; height: 38px;" title="{{ __('Clear Filter') }}">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Requests Table -->
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; overflow: hidden;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-items-center mb-0" style="vertical-align: middle;">
                    <thead class="bg-light text-secondary" style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">
                        <tr>
                            <th class="text-center" style="width: 60px;">#</th>
                            <th>{{ __('Store Name') }}</th>
                            <th>{{ __('Applicant Details') }}</th>
                            <th>{{ __('Latest Appeal Message') }}</th>
                            <th>{{ __('Admin Reply / Reason') }}</th>
                            <th class="text-center">{{ __('Appeal Status') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th class="text-center" style="width: 190px;">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 13.5px;">
                        @forelse($requests as $req)
                            @php
                                $statusBadge = 'badge-warning text-dark';
                                if ($req->status === 'Unblocked') $statusBadge = 'badge-success text-white';
                                elseif ($req->status === 'Replied') $statusBadge = 'badge-info text-white';
                                elseif ($req->status === 'Pending Fine') $statusBadge = 'badge-warning text-dark font-weight-bold';
                                $isBlocked = ($req->user && $req->user->is_seller_blocked);
                            @endphp
                            <tr>
                                <td class="text-center font-weight-bold text-muted">{{ $req->id }}</td>
                                <td>
                                    <div class="font-weight-bold text-dark d-flex align-items-center flex-wrap">
                                        <a href="{{ $req->store_url }}" target="_blank" class="text-primary text-decoration-none mr-2">
                                            <i class="fas fa-store mr-1"></i>{{ $req->store_name ?: __('Vendor Store') }}
                                            <i class="fas fa-external-link-alt ml-1" style="font-size: 9px;"></i>
                                        </a>
                                        @if($req->is_seen == 0 && $req->status == 'Pending')
                                            <span class="badge badge-warning text-dark unseen-tag-{{ $req->id }}" style="font-size: 10px; padding: 2px 6px; font-weight: bold; border-radius: 8px;">
                                                <i class="fas fa-bell mr-1 text-danger"></i>{{ __('New') }}
                                            </span>
                                        @endif
                                    </div>
                                    @if($isBlocked)
                                        <span class="badge badge-danger" style="font-size: 10px; padding: 2px 6px;">{{ __('Currently Blocked') }}</span>
                                    @else
                                        <span class="badge badge-success" style="font-size: 10px; padding: 2px 6px;">{{ __('Active / Unblocked') }}</span>
                                    @endif
                                    @if($req->fine_amount > 0)
                                        <div class="small mt-1 text-danger font-weight-bold">
                                            <i class="fas fa-file-invoice-dollar mr-1"></i>{{ PriceHelper::adminCurrency() }} {{ number_format($req->fine_amount, 2) }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="font-weight-bold text-dark">{{ $req->full_name }}</div>
                                    <div class="small text-muted"><i class="fas fa-envelope mr-1"></i>{{ $req->email }}</div>
                                    @if($req->phone)
                                        <div class="small text-muted"><i class="fas fa-phone mr-1"></i>{{ $req->phone }}</div>
                                    @endif
                                </td>
                                <td style="max-width: 250px;">
                                    <div class="text-dark" style="white-space: normal; line-height: 1.35; font-size: 13px;">
                                        {{ Str::limit($req->message, 90) }}
                                    </div>
                                </td>
                                <td style="max-width: 200px;">
                                    @if($req->admin_reply)
                                        <div class="text-success small" style="white-space: normal; line-height: 1.35;">
                                            <i class="fas fa-reply mr-1"></i> {{ Str::limit($req->admin_reply, 70) }}
                                        </div>
                                        <small class="text-muted d-block" style="font-size: 10.5px;">{{ $req->admin_replied_at ? $req->admin_replied_at->format('M d, h:i A') : '' }}</small>
                                    @else
                                        <span class="text-muted small font-italic">{{ __('No reply / reason') }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $statusBadge }} px-2 py-1 font-weight-bold" style="font-size: 11px; border-radius: 12px;">
                                        {{ $req->status }}
                                    </span>
                                    @if($req->fine_status === 'paid')
                                        <span class="badge badge-success text-white px-2 py-1 font-weight-bold d-block mt-1" style="font-size: 10px; border-radius: 12px;">
                                            <i class="fas fa-check-circle mr-1"></i>{{ __('Fine Paid') }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="d-block text-dark font-weight-bold" style="font-size: 12px;">{{ $req->updated_at ? $req->updated_at->format('M d, Y') : $req->created_at->format('M d, Y') }}</span>
                                    <small class="text-muted" style="font-size: 10.5px;">{{ $req->updated_at ? $req->updated_at->diffForHumans() : $req->created_at->diffForHumans() }}</small>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <!-- View Full Chat & Reply Details Modal Button -->
                                        <button type="button" class="btn btn-outline-primary" title="{{ __('View Details & Full Chat History') }}" onclick="openAppealChatModal({{ $req->id }})">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        <!-- Unblock / Reblock Store Button -->
                                        @if($isBlocked)
                                            <form action="{{ route('back.unblock_request.unblock', $req->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to UNBLOCK this store and restore its full access?') }}')">
                                                @csrf
                                                <button type="submit" class="btn btn-success" title="{{ __('Unblock Store Immediately') }}">
                                                    <i class="fas fa-unlock-alt"></i> {{ __('Unblock') }}
                                                </button>
                                            </form>
                                        @else
                                            <button type="button" class="btn btn-outline-danger" title="{{ __('Re-Block Store') }}" onclick="promptReblockStore({{ $req->id }}, '{{ addslashes($req->store_name) }}')">
                                                <i class="fas fa-ban"></i> {{ __('Block') }}
                                            </button>
                                        @endif

                                        <!-- Delete Request Record -->
                                        <a href="{{ route('back.unblock_request.delete', $req->id) }}" class="btn btn-outline-secondary" title="{{ __('Delete Record') }}" onclick="return confirm('{{ __('Delete this appeal record?') }}')">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="fas fa-inbox fa-3x text-secondary mb-3 d-block"></i>
                                    <h5>{{ __('No Store Unblock Requests Found') }}</h5>
                                    <p class="small text-muted">{{ __('When a blocked vendor submits an unblock appeal, it will appear here.') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($requests->hasPages())
            <div class="card-footer bg-white border-top py-3 d-flex justify-content-center">
                {{ $requests->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Appeal Details & Full Chat Modal -->
<div class="modal fade" id="appealChatModal" tabindex="-1" role="dialog" aria-labelledby="appealChatModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title font-weight-bold text-white d-flex align-items-center" id="appealChatModalLabel">
                    <i class="fas fa-store mr-2"></i> <span id="m_store_title">{{ __('Store Unblock Appeal & Full Chat History') }}</span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4 bg-light">
                <!-- Applicant & Store Profile Bar -->
                <div class="card shadow-sm border-0 mb-3" style="border-radius: 10px;">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <small class="text-muted d-block font-weight-bold">{{ __('Store Name:') }}</small>
                                <h6 class="font-weight-bold text-dark mb-0" id="m_store_name">-</h6>
                            </div>
                            <div class="col-md-6 mb-2">
                                <small class="text-muted d-block font-weight-bold">{{ __('Store Status:') }}</small>
                                <span id="m_block_badge" class="badge badge-danger mr-1">-</span>
                                <span id="m_status_badge" class="badge badge-warning text-dark font-weight-bold">-</span>
                            </div>
                            <div class="col-md-6 mb-1">
                                <small class="text-muted d-block font-weight-bold">{{ __('Applicant Name:') }}</small>
                                <span class="text-dark font-weight-bold" id="m_applicant_name">-</span>
                            </div>
                            <div class="col-md-6 mb-1">
                                <small class="text-muted d-block font-weight-bold">{{ __('Contact Info:') }}</small>
                                <span class="text-dark" id="m_contact_info">-</span>
                            </div>
                            <div class="col-md-12 mt-2 pt-2 border-top" id="m_fine_info_area" style="display: none;">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                    <div>
                                        <small class="text-muted font-weight-bold mr-1">{{ __('Imposed Fine:') }}</small>
                                        <span class="badge badge-warning text-dark font-weight-bold px-2 py-1" style="font-size: 13px;" id="m_fine_amount_display">-</span>
                                    </div>
                                    <div id="m_fine_status_tag"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Complete Chat Messages Stream -->
                <div class="card shadow-sm border-0 mb-3" style="border-radius: 10px;">
                    <div class="card-header bg-white py-2 font-weight-bold text-dark d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-comments text-primary mr-2"></i> {{ __('Full Appeal & Chat History:') }}</span>
                        <small class="text-muted" id="m_msg_count"></small>
                    </div>
                    <div class="card-body p-3" style="background: #efeae2; max-height: 320px; min-height: 180px; overflow-y: auto; display: flex; flex-direction: column; gap: 10px;" id="m_chat_container">
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-spinner fa-spin fa-2x"></i>
                            <div class="small mt-2">{{ __('Loading chat history...') }}</div>
                        </div>
                    </div>
                </div>

                <!-- 1. Add / Impose Fine Card -->
                <div class="card shadow-sm border-0 mb-3" style="border-radius: 10px; border-left: 4px solid #ffc107 !important;">
                    <div class="card-header bg-white py-2 font-weight-bold text-dark d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-file-invoice-dollar text-warning mr-2"></i> {{ __('Add Fine / Impose Unblock Fee:') }}</span>
                        <span class="badge badge-warning text-dark" style="font-size: 10px;">{{ __('Status: Pending Fine') }}</span>
                    </div>
                    <div class="card-body p-3 bg-white">
                        <form id="modalFineForm" onsubmit="submitModalFine(event)">
                            <div class="row align-items-center">
                                <div class="col-md-5 mb-2 mb-md-0">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text font-weight-bold text-dark">{{ PriceHelper::adminCurrency() }}</span>
                                        </div>
                                        <input type="number" step="0.01" min="1" name="fine_amount" id="m_fine_amount_input" class="form-control font-weight-bold" placeholder="{{ __('Enter Fine Amount') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <button type="submit" class="btn btn-warning btn-sm text-dark font-weight-bold" id="m_send_fine_btn">
                                        <i class="fas fa-paper-plane mr-1"></i> {{ __('Add Fine & Notify Store') }}
                                    </button>
                                </div>
                            </div>
                            <small class="text-muted d-block mt-2" style="font-size: 11.5px;">
                                <i class="fas fa-info-circle text-warning mr-1"></i> {{ __('Sending a fine updates the appeal status to "Pending Fine" and prompts the vendor with a "Pay Fine" deposit flow to unblock their store.') }}
                            </small>
                        </form>
                    </div>
                </div>

                <!-- 2. Reply Composer Form -->
                <form id="modalReplyForm" onsubmit="submitModalReply(event)">
                    @csrf
                    <div class="card shadow-sm border-0" style="border-radius: 10px;">
                        <div class="card-header bg-white py-2 font-weight-bold text-dark">
                            <i class="fas fa-paper-plane text-info mr-2"></i> {{ __('Send Reply / Action Message to Vendor:') }}
                        </div>
                        <div class="card-body p-3 bg-white">
                            <div class="form-group mb-2">
                                <textarea name="reply" id="m_reply_input" class="form-control" rows="3" placeholder="{{ __('Type your official reply, block reason, or instructions to the store owner here...') }}"></textarea>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                                <div>
                                    <button type="submit" class="btn btn-primary font-weight-bold" id="m_send_btn">
                                        <i class="fas fa-paper-plane mr-1"></i> {{ __('Send Reply to Vendor') }}
                                    </button>
                                </div>
                                <div id="m_unblock_action_area">
                                    <!-- Dynamic Unblock / Reblock Button -->
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Hidden Unblock Form for Modal -->
                <form id="modalDirectUnblockForm" method="POST" action="" style="display: none;">
                    @csrf
                    <input type="hidden" name="reply" id="modal_unblock_note">
                </form>

                <!-- Hidden Reblock Form for Modal -->
                <form id="modalDirectReblockForm" method="POST" action="" style="display: none;">
                    @csrf
                    <input type="hidden" name="reason" id="modal_reblock_reason">
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .bubble-vendor {
        align-self: flex-start;
        background: #ffffff;
        color: #111b21;
        border-radius: 0 12px 12px 12px;
        max-width: 80%;
        padding: 8px 14px 6px 14px;
        font-size: 13.5px;
        line-height: 1.45;
        box-shadow: 0 1px 2px rgba(0,0,0,0.12);
        border-left: 3px solid #0d6efd;
    }
    .bubble-admin {
        align-self: flex-end;
        background: #d9fdd3;
        color: #111b21;
        border-radius: 12px 0 12px 12px;
        max-width: 80%;
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
</style>

<script>
    let activeRequestId = null;
    const csrfToken = "{{ csrf_token() }}";

    function openAppealChatModal(requestId) {
        activeRequestId = requestId;
        const container = document.getElementById('m_chat_container');
        container.innerHTML = `
            <div class="text-center py-4 text-muted">
                <i class="fas fa-spinner fa-spin fa-2x"></i>
                <div class="small mt-2">{{ __('Loading chat history...') }}</div>
            </div>
        `;

        $('#appealChatModal').modal('show');

        fetch("{{ url('admin/unblock-requests/chat') }}/" + requestId)
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    renderModalContent(data.request, data.messages);

                    // Update sidebar unblock counter dynamically
                    if (data.remaining_unseen !== undefined) {
                        const sidebarBadges = document.querySelectorAll('.unblock-sidebar-badge');
                        sidebarBadges.forEach(badge => {
                            if (data.remaining_unseen > 0) {
                                badge.innerText = data.remaining_unseen;
                                badge.style.display = '';
                            } else {
                                badge.style.display = 'none';
                            }
                        });
                    }

                    // Remove "New" indicator from the request row
                    const newTag = document.querySelector('.unseen-tag-' + requestId);
                    if (newTag) {
                        newTag.remove();
                    }
                } else {
                    container.innerHTML = '<div class="alert alert-danger">{{ __("Failed to load chat history.") }}</div>';
                }
            })
            .catch(e => {
                console.error(e);
                container.innerHTML = '<div class="alert alert-danger">{{ __("Error loading chat.") }}</div>';
            });
    }

    function renderModalContent(req, messages) {
        document.getElementById('m_store_title').innerText = req.store_name ? (req.store_name + ' - Unblock Appeal') : 'Unblock Appeal #' + req.id;
        document.getElementById('m_store_name').innerText = req.store_name || 'N/A';
        document.getElementById('m_applicant_name').innerText = req.full_name || 'N/A';
        document.getElementById('m_contact_info').innerText = req.email + (req.phone ? ' • ' + req.phone : '');

        // Badges
        const blockBadge = document.getElementById('m_block_badge');
        if (req.is_seller_blocked) {
            blockBadge.className = 'badge badge-danger font-weight-bold';
            blockBadge.innerText = '{{ __("Currently Blocked") }}';
        } else {
            blockBadge.className = 'badge badge-success font-weight-bold';
            blockBadge.innerText = '{{ __("Active / Unblocked") }}';
        }

        const statusBadge = document.getElementById('m_status_badge');
        statusBadge.innerText = req.status;
        statusBadge.className = 'badge font-weight-bold ' + (req.status === 'Unblocked' ? 'badge-success text-white' : (req.status === 'Replied' ? 'badge-info text-white' : 'badge-warning text-dark'));

        // Fine Information Display
        const fineArea = document.getElementById('m_fine_info_area');
        const fineInput = document.getElementById('m_fine_amount_input');
        if (req.fine_amount > 0) {
            fineArea.style.display = 'block';
            document.getElementById('m_fine_amount_display').innerText = '{{ PriceHelper::adminCurrency() }} ' + parseFloat(req.fine_amount).toFixed(2);
            if (fineInput) fineInput.value = parseFloat(req.fine_amount).toFixed(2);

            let fineTagHtml = '';
            if (req.fine_status === 'paid') {
                fineTagHtml = '<span class="badge badge-success px-2 py-1"><i class="fas fa-check mr-1"></i> {{ __("Fine Paid & Approved") }}</span>';
            } else if (req.fine_status === 'submitted') {
                fineTagHtml = '<span class="badge badge-info px-2 py-1"><i class="fas fa-clock mr-1"></i> {{ __("Fine Proof Submitted (Pending Verification)") }}</span>';
            } else if (req.fine_status === 'rejected') {
                fineTagHtml = '<span class="badge badge-danger px-2 py-1"><i class="fas fa-times mr-1"></i> {{ __("Fine Proof Rejected") }}</span>';
            } else {
                fineTagHtml = '<span class="badge badge-warning text-dark px-2 py-1"><i class="fas fa-clock mr-1"></i> {{ __("Awaiting Vendor Payment") }}</span>';
            }
            document.getElementById('m_fine_status_tag').innerHTML = fineTagHtml;
        } else {
            fineArea.style.display = 'none';
            if (fineInput) fineInput.value = '';
        }

        // Render Chat Messages
        const container = document.getElementById('m_chat_container');
        if (!messages || messages.length === 0) {
            // Render original appeal message as fallback
            container.innerHTML = `
                <div class="bubble-vendor">
                    <div style="font-size: 11px; font-weight: 700; color: #0d6efd; margin-bottom: 2px;">${escapeHtml(req.full_name || 'Store Owner')} (Appeal)</div>
                    <div style="white-space: pre-wrap;">${escapeHtml(req.message || '')}</div>
                    <div class="bubble-meta">
                        <span>${req.created_at}</span>
                    </div>
                </div>
            `;
            if (req.admin_reply) {
                container.insertAdjacentHTML('beforeend', `
                    <div class="bubble-admin">
                        <div style="font-size: 11px; font-weight: 700; color: #166534; margin-bottom: 2px;">{{ __('Platform Admin Support') }}</div>
                        <div style="white-space: pre-wrap;">${escapeHtml(req.admin_reply)}</div>
                        <div class="bubble-meta">
                            <span>${req.admin_replied_at || ''}</span>
                            <span style="color: #53bdeb; margin-left: 3px; font-weight: bold;">✓✓</span>
                        </div>
                    </div>
                `);
            }
        } else {
            let html = '';
            messages.forEach(msg => {
                const isAdmin = msg.is_admin;
                const bubbleClass = isAdmin ? 'bubble-admin' : 'bubble-vendor';
                const senderTag = isAdmin 
                    ? '<div style="font-size: 11px; font-weight: 700; color: #166534; margin-bottom: 2px;">{{ __("Platform Admin Support") }}</div>'
                    : `<div style="font-size: 11px; font-weight: 700; color: #0d6efd; margin-bottom: 2px;">${escapeHtml(req.full_name || 'Store Owner')}</div>`;
                const ticks = isAdmin ? '<span style="color: #53bdeb; margin-left: 3px; font-weight: bold;">✓✓</span>' : '';

                html += `
                    <div class="${bubbleClass}">
                        ${senderTag}
                        <div style="white-space: pre-wrap;">${escapeHtml(msg.message)}</div>
                        <div class="bubble-meta">
                            <span>${msg.date} ${msg.time}</span>
                            ${ticks}
                        </div>
                    </div>
                `;
            });
            container.innerHTML = html;
        }

        // Auto scroll to bottom
        container.scrollTop = container.scrollHeight;

        // Unblock/Reblock action button
        const actionArea = document.getElementById('m_unblock_action_area');
        if (req.is_seller_blocked) {
            actionArea.innerHTML = `
                <button type="button" class="btn btn-success font-weight-bold" onclick="submitModalDirectUnblock(${req.id})">
                    <i class="fas fa-unlock-alt mr-1"></i> {{ __('Unblock Store Now') }}
                </button>
            `;
        } else {
            actionArea.innerHTML = `
                <button type="button" class="btn btn-danger font-weight-bold" onclick="submitModalDirectReblock(${req.id})">
                    <i class="fas fa-ban mr-1"></i> {{ __('Re-Block Store') }}
                </button>
            `;
        }
    }

    function submitModalReply(e) {
        e.preventDefault();
        const input = document.getElementById('m_reply_input');
        const text = input.value.trim();
        if (!text || !activeRequestId) return;

        const sendBtn = document.getElementById('m_send_btn');
        sendBtn.disabled = true;
        sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> {{ __("Sending...") }}';

        // Optimistic UI Append
        const container = document.getElementById('m_chat_container');
        const now = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        container.insertAdjacentHTML('beforeend', `
            <div class="bubble-admin" style="opacity: 0.85;">
                <div style="font-size: 11px; font-weight: 700; color: #166534; margin-bottom: 2px;">{{ __('Platform Admin Support') }}</div>
                <div style="white-space: pre-wrap;">${escapeHtml(text)}</div>
                <div class="bubble-meta">
                    <span>${now}</span>
                    <span class="text-muted"><i class="fas fa-clock" style="font-size: 10px;"></i></span>
                </div>
            </div>
        `);
        container.scrollTop = container.scrollHeight;
        input.value = '';

        fetch("{{ url('admin/unblock-requests/reply') }}/" + activeRequestId, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ reply: text })
        })
        .then(r => r.json())
        .then(data => {
            sendBtn.disabled = false;
            sendBtn.innerHTML = '<i class="fas fa-paper-plane mr-1"></i> {{ __("Send Reply to Vendor") }}';
            if (data.success) {
                // Refresh modal content to sync fully
                fetch("{{ url('admin/unblock-requests/chat') }}/" + activeRequestId)
                    .then(r => r.json())
                    .then(res => {
                        if (res.success) renderModalContent(res.request, res.messages);
                    });
            }
        })
        .catch(e => {
            console.error(e);
            sendBtn.disabled = false;
            sendBtn.innerHTML = '<i class="fas fa-paper-plane mr-1"></i> {{ __("Send Reply to Vendor") }}';
        });
    }

    function submitModalFine(e) {
        e.preventDefault();
        const amountInput = document.getElementById('m_fine_amount_input');
        const amount = amountInput.value.trim();
        if (!amount || !activeRequestId) return;

        const fineBtn = document.getElementById('m_send_fine_btn');
        fineBtn.disabled = true;
        fineBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> {{ __("Imposing...") }}';

        fetch("{{ url('admin/unblock-requests/fine') }}/" + activeRequestId, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ fine_amount: amount })
        })
        .then(r => r.json())
        .then(data => {
            fineBtn.disabled = false;
            fineBtn.innerHTML = '<i class="fas fa-paper-plane mr-1"></i> {{ __("Add Fine & Notify Store") }}';
            if (data.success) {
                // Refresh modal content to sync fully
                fetch("{{ url('admin/unblock-requests/chat') }}/" + activeRequestId)
                    .then(r => r.json())
                    .then(res => {
                        if (res.success) renderModalContent(res.request, res.messages);
                    });
            }
        })
        .catch(e => {
            console.error(e);
            fineBtn.disabled = false;
            fineBtn.innerHTML = '<i class="fas fa-paper-plane mr-1"></i> {{ __("Add Fine & Notify Store") }}';
        });
    }

    function submitModalDirectUnblock(id) {
        if (confirm('{{ __("Are you sure you want to UNBLOCK this store and restore its full dashboard and product access?") }}')) {
            const replyText = document.getElementById('m_reply_input').value.trim();
            const form = document.getElementById('modalDirectUnblockForm');
            form.action = "{{ url('admin/unblock-requests/unblock') }}/" + id;
            document.getElementById('modal_unblock_note').value = replyText;
            form.submit();
        }
    }

    function promptReblockStore(id, storeName) {
        const reason = prompt('{{ __("Enter reason / message for blocking this store:") }}', 'Your store has been blocked due to policy review.');
        if (reason !== null && reason.trim() !== '') {
            const form = document.getElementById('modalDirectReblockForm');
            form.action = "{{ url('admin/unblock-requests/reblock') }}/" + id;
            document.getElementById('modal_reblock_reason').value = reason.trim();
            form.submit();
        }
    }

    function submitModalDirectReblock(id) {
        let reason = document.getElementById('m_reply_input').value.trim();
        if (!reason) {
            reason = prompt('{{ __("Enter reason / message for blocking this store:") }}', 'Your store has been blocked due to policy review.');
            if (reason === null || reason.trim() === '') return;
        }
        if (confirm('{{ __("Are you sure you want to RE-BLOCK this store?") }}')) {
            const form = document.getElementById('modalDirectReblockForm');
            form.action = "{{ url('admin/unblock-requests/reblock') }}/" + id;
            document.getElementById('modal_reblock_reason').value = reason;
            form.submit();
        }
    }

    function escapeHtml(t) {
        const m = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return String(t).replace(/[&<>"']/g, function(k) { return m[k]; });
    }
</script>
@endsection