@extends('master.back')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
        <div class="card-body py-3">
            <div class="d-sm-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-0 text-dark font-weight-bold">
                        <i class="fas fa-file-invoice-dollar text-warning mr-2"></i> {{ __('Fine Approval & Store Unblock') }}
                    </h4>
                    <p class="text-muted small mb-0">{{ __('Review fine payment proofs submitted by suspended vendor stores, verify transactions, and restore store access.') }}</p>
                </div>
                <div class="mt-2 mt-sm-0">
                    <span class="badge badge-warning text-dark px-3 py-2 font-weight-bold" style="font-size: 13px; border-radius: 20px;">
                        <i class="fas fa-clock mr-1"></i> {{ $counts['pending'] }} {{ __('Pending Fine Verifications') }}
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
                <a href="{{ route('back.fine_approval.index') }}" class="btn btn-sm {{ empty($status) ? 'btn-primary font-weight-bold' : 'btn-outline-primary' }}">
                    {{ __('All Submissions') }} <span class="badge badge-light ml-1">{{ $counts['all'] }}</span>
                </a>
                <a href="{{ route('back.fine_approval.index', ['status' => 'pending']) }}" class="btn btn-sm {{ $status == 'pending' ? 'btn-warning text-dark font-weight-bold' : 'btn-outline-warning text-dark' }}">
                    <i class="fas fa-clock mr-1"></i> {{ __('Pending') }} <span class="badge badge-warning ml-1">{{ $counts['pending'] }}</span>
                </a>
                <a href="{{ route('back.fine_approval.index', ['status' => 'approved']) }}" class="btn btn-sm {{ $status == 'approved' ? 'btn-success text-white font-weight-bold' : 'btn-outline-success' }}">
                    <i class="fas fa-check-circle mr-1"></i> {{ __('Approved / Unblocked') }} <span class="badge badge-success ml-1">{{ $counts['approved'] }}</span>
                </a>
                <a href="{{ route('back.fine_approval.index', ['status' => 'rejected']) }}" class="btn btn-sm {{ $status == 'rejected' ? 'btn-danger text-white font-weight-bold' : 'btn-outline-danger' }}">
                    <i class="fas fa-times-circle mr-1"></i> {{ __('Rejected') }} <span class="badge badge-danger ml-1">{{ $counts['rejected'] }}</span>
                </a>
            </div>
        </div>

        <div class="col-xl-5 col-lg-6 col-12">
            <!-- Search Form -->
            <form action="{{ route('back.fine_approval.index') }}" method="GET" class="d-flex w-100">
                @if($status)
                    <input type="hidden" name="status" value="{{ $status }}">
                @endif
                <div class="input-group input-group-sm w-100">
                    <input type="text" name="search" class="form-control" placeholder="{{ __('Search store, Txn ID, name...') }}" value="{{ request('search') }}" style="border-radius: 20px 0 0 20px; font-size: 13.5px; height: 38px;">
                    <div class="input-group-append">
                        <button class="btn btn-primary px-3" type="submit" style="{{ request('search') ? '' : 'border-radius: 0 20px 20px 0;' }} height: 38px;">
                            <i class="fas fa-search"></i>
                        </button>
                        @if(request('search'))
                            <a href="{{ route('back.fine_approval.index', $status ? ['status' => $status] : []) }}" class="btn btn-secondary px-3" style="border-radius: 0 20px 20px 0; height: 38px;" title="{{ __('Clear Filter') }}">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Fine Payments Table -->
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; overflow: hidden;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-items-center mb-0" style="vertical-align: middle;">
                    <thead class="bg-light text-secondary" style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">
                        <tr>
                            <th class="text-center" style="width: 50px;">#</th>
                            <th>{{ __('Store & Vendor') }}</th>
                            <th class="text-right">{{ __('Fine Amount') }}</th>
                            <th>{{ __('Payment Sent To') }}</th>
                            <th>{{ __('Sender Details') }}</th>
                            <th>{{ __('Transaction ID') }}</th>
                            <th class="text-center">{{ __('Proof Screenshot') }}</th>
                            <th class="text-center">{{ __('Status') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th class="text-center" style="width: 220px;">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 13.5px;">
                        @forelse($datas as $fine)
                            @php
                                $storeName = $fine->seller ? $fine->seller->shop_name : ($fine->unblockRequest->store_name ?? 'Store #' . $fine->user_id);
                                $applicantName = $fine->unblockRequest ? $fine->unblockRequest->full_name : ($fine->user ? $fine->user->first_name . ' ' . $fine->user->last_name : 'N/A');
                                $email = $fine->unblockRequest ? $fine->unblockRequest->email : ($fine->user ? $fine->user->email : '');
                                $phone = $fine->unblockRequest ? $fine->unblockRequest->phone : ($fine->user ? $fine->user->phone : '');
                                $statusClass = 'badge-warning text-dark';
                                if ($fine->status === 'approved') $statusClass = 'badge-success text-white';
                                elseif ($fine->status === 'rejected') $statusClass = 'badge-danger text-white';

                                $screenshotUrl = $fine->screenshot ? (file_exists(public_path('storage/images/fines/' . $fine->screenshot)) ? asset('core/public/storage/images/fines/' . $fine->screenshot) : url('/core/public/storage/images/fines/' . $fine->screenshot)) : '';

                                $fineData = [
                                    'id' => $fine->id,
                                    'storeName' => $storeName,
                                    'storeUrl' => route('front.catalog') . '?vendor=' . $fine->user_id,
                                    'applicantName' => $applicantName,
                                    'email' => $email,
                                    'phone' => $phone,
                                    'fineAmount' => PriceHelper::adminCurrency() . ' ' . number_format($fine->fine_amount, 2),
                                    'fineAmountRaw' => $fine->fine_amount,
                                    'fineImposedAt' => ($fine->unblockRequest && $fine->unblockRequest->fine_imposed_at) ? $fine->unblockRequest->fine_imposed_at->format('M d, Y h:i A') : ($fine->unblockRequest ? $fine->unblockRequest->updated_at->format('M d, Y h:i A') : '-'),
                                    'appealMessage' => $fine->unblockRequest ? $fine->unblockRequest->message : '',
                                    'adminAccount' => $fine->payment_method ?: 'Direct Bank / Easypaisa',
                                    'bankName' => $fine->bank_name ?: '-',
                                    'accountName' => $fine->account_name ?: '-',
                                    'accountNumber' => $fine->account_number ?: '-',
                                    'txnId' => $fine->txn_id ?: '-',
                                    'screenshotUrl' => $screenshotUrl,
                                    'status' => $fine->status,
                                    'submittedAt' => $fine->created_at ? $fine->created_at->format('M d, Y h:i A') : '-',
                                    'approvedAt' => $fine->approved_at ? $fine->approved_at->format('M d, Y h:i A') : '',
                                    'adminNote' => $fine->admin_note ?: '',
                                    'approveUrl' => route('back.fine_approval.approve', $fine->id),
                                    'rejectUrl' => url('admin/fine-approvals/reject/' . $fine->id)
                                ];
                            @endphp
                            <tr>
                                <td class="text-center font-weight-bold text-muted">{{ $fine->id }}</td>
                                <td>
                                    <div class="font-weight-bold text-dark">
                                        <a href="{{ route('front.catalog') }}?vendor={{ $fine->user_id }}" target="_blank" class="text-primary text-decoration-none">
                                            <i class="fas fa-store mr-1"></i> {{ $storeName }}
                                            <i class="fas fa-external-link-alt ml-1" style="font-size: 9px;"></i>
                                        </a>
                                    </div>
                                    <div class="small text-muted font-weight-bold">{{ $applicantName }}</div>
                                    @if($email)
                                        <div class="small text-muted" style="font-size: 11px;"><i class="fas fa-envelope mr-1"></i>{{ $email }}</div>
                                    @endif
                                    @if($phone)
                                        <div class="small text-muted" style="font-size: 11px;"><i class="fas fa-phone mr-1"></i>{{ $phone }}</div>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <span class="font-weight-bold text-dark" style="font-size: 15px;">
                                        {{ PriceHelper::adminCurrency() }} {{ number_format($fine->fine_amount, 2) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="text-dark font-weight-bold small" style="max-width: 180px;">
                                        {{ $fine->payment_method ?: 'N/A' }}
                                    </div>
                                </td>
                                <td>
                                    <div class="text-dark font-weight-bold small">{{ $fine->bank_name ?: '-' }}</div>
                                    <div class="small text-muted">{{ $fine->account_name ?: '' }}</div>
                                    @if($fine->account_number)
                                        <div class="small text-muted font-italic">{{ $fine->account_number }}</div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-light border text-dark font-weight-bold px-2 py-1" style="font-size: 12px; letter-spacing: 0.5px;">
                                        {{ $fine->txn_id ?: '-' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($screenshotUrl)
                                        <a href="javascript:void(0)" onclick="previewScreenshot('{{ $screenshotUrl }}', '{{ addslashes($storeName) }}', '{{ $fine->txn_id }}')" class="d-inline-block shadow-sm rounded overflow-hidden" style="border: 2px solid #e2e8f0; transition: transform 0.2s;" title="{{ __('Click to preview full image') }}">
                                            <img src="{{ $screenshotUrl }}" alt="{{ __('Proof') }}" style="width: 50px; height: 50px; object-fit: cover; display: block;">
                                        </a>
                                    @else
                                        <span class="text-muted small font-italic">{{ __('No Screenshot') }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $statusClass }} px-2 py-1 font-weight-bold" style="font-size: 11px; border-radius: 12px;">
                                        {{ ucfirst($fine->status) }}
                                    </span>
                                    @if($fine->admin_note && $fine->status === 'rejected')
                                        <small class="d-block text-danger mt-1" style="font-size: 10px; max-width: 130px; line-height: 1.2;">
                                            {{ Str::limit($fine->admin_note, 35) }}
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    <span class="d-block text-dark font-weight-bold" style="font-size: 12px;">{{ $fine->created_at ? $fine->created_at->format('M d, Y') : '-' }}</span>
                                    <small class="text-muted" style="font-size: 10.5px;">{{ $fine->created_at ? $fine->created_at->format('h:i A') : '' }}</small>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-1" style="gap: 4px;">
                                        <!-- View Details Button -->
                                        <button type="button" class="btn btn-sm btn-primary font-weight-bold px-2 py-1" title="{{ __('View Full Details & Manage') }}" onclick='openViewDetailsModal({!! json_encode($fineData) !!})'>
                                            <i class="fas fa-eye mr-1"></i> {{ __('View') }}
                                        </button>

                                        @if($fine->status === 'pending')
                                            <!-- Unblock / Approve Button -->
                                            <form action="{{ route('back.fine_approval.approve', $fine->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to APPROVE this fine payment and UNBLOCK store & products immediately?') }}')">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success font-weight-bold px-2 py-1" title="{{ __('Verify Fine Payment & Unblock Store Immediately') }}">
                                                    <i class="fas fa-unlock-alt"></i> {{ __('Unblock') }}
                                                </button>
                                            </form>

                                            <!-- Reject Button -->
                                            <button type="button" class="btn btn-sm btn-outline-danger px-2 py-1" title="{{ __('Reject Payment Proof') }}" onclick="openRejectModal({{ $fine->id }}, '{{ addslashes($storeName) }}')">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @elseif($fine->status === 'approved')
                                            <span class="badge badge-success px-2 py-1 font-weight-bold" style="font-size: 11px;">
                                                <i class="fas fa-check mr-1"></i> {{ __('Unblocked') }}
                                            </span>
                                        @else
                                            <button type="button" class="btn btn-sm btn-outline-secondary px-2 py-1" onclick="openRejectModal({{ $fine->id }}, '{{ addslashes($storeName) }}', '{{ addslashes($fine->admin_note) }}')" title="{{ __('View Rejection Reason') }}">
                                                <i class="fas fa-info-circle"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-5 text-muted">
                                    <i class="fas fa-file-invoice-dollar fa-3x text-secondary mb-3 d-block"></i>
                                    <h5>{{ __('No Fine Payment Submissions Found') }}</h5>
                                    <p class="small text-muted">{{ __('When a blocked vendor pays an imposed unblock fine, payment submissions will appear here for verification.') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($datas->hasPages())
            <div class="card-footer bg-white border-top py-3 d-flex justify-content-center">
                {{ $datas->links() }}
            </div>
        @endif
    </div>
</div>

<!-- ============================================== -->
<!-- VIEW FULL FINE PAYMENT DETAILS MODAL           -->
<!-- ============================================== -->
<div class="modal fade" id="viewFineDetailsModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header bg-primary text-white py-3 d-flex align-items-center justify-content-between">
                <h5 class="modal-title font-weight-bold text-white mb-0 d-flex align-items-center">
                    <i class="fas fa-file-invoice-dollar mr-2"></i> {{ __('Store Fine Payment & Unblock Verification Details') }}
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4 bg-light" style="max-height: 80vh; overflow-y: auto;">
                
                <!-- Status & Fine Requirement Banner -->
                <div class="card border-warning mb-4 shadow-sm" style="border-radius: 10px; border-left: 5px solid #f59e0b !important; background: #fffbe8;">
                    <div class="card-body p-3">
                        <div class="d-sm-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted small font-weight-bold text-uppercase d-block mb-1">{{ __('Imposed Fine Amount on Store') }}</span>
                                <h3 class="font-weight-bold text-danger mb-0" id="vFineAmount">-</h3>
                            </div>
                            <div class="mt-2 mt-sm-0 text-sm-right">
                                <span class="text-muted small d-block mb-1">{{ __('Payment Status') }}</span>
                                <span class="badge px-3 py-1 font-weight-bold" id="vStatusBadge" style="font-size: 13px; border-radius: 20px;">-</span>
                            </div>
                        </div>
                        <div class="mt-2 pt-2 border-top small text-muted d-flex justify-content-between">
                            <span><i class="fas fa-calendar-alt mr-1"></i> {{ __('Fine Imposed At:') }} <strong class="text-dark" id="vFineImposedAt">-</strong></span>
                            <span><i class="fas fa-clock mr-1"></i> {{ __('Proof Submitted At:') }} <strong class="text-dark" id="vSubmittedAt">-</strong></span>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Left Column: Store & Vendor Info -->
                    <div class="col-md-6 mb-3">
                        <div class="card h-100 shadow-sm border-0" style="border-radius: 8px;">
                            <div class="card-header bg-white py-2 font-weight-bold text-dark border-bottom">
                                <i class="fas fa-store text-primary mr-1"></i> {{ __('Store & Vendor Information') }}
                            </div>
                            <div class="card-body p-3">
                                <div class="mb-2">
                                    <span class="text-muted small d-block">{{ __('Store Name:') }}</span>
                                    <h6 class="font-weight-bold text-primary mb-0">
                                        <span id="vStoreName">-</span>
                                        <a href="#" id="vStoreLink" target="_blank" class="ml-1 text-muted small" title="{{ __('View Store') }}"><i class="fas fa-external-link-alt"></i></a>
                                    </h6>
                                </div>
                                <div class="mb-2">
                                    <span class="text-muted small d-block">{{ __('Vendor / Applicant Name:') }}</span>
                                    <strong class="text-dark" id="vApplicantName">-</strong>
                                </div>
                                <div class="mb-2">
                                    <span class="text-muted small d-block">{{ __('Email Address:') }}</span>
                                    <span class="text-dark" id="vEmail">-</span>
                                </div>
                                <div class="mb-2">
                                    <span class="text-muted small d-block">{{ __('Phone Number:') }}</span>
                                    <span class="text-dark" id="vPhone">-</span>
                                </div>
                                <div class="mb-0" id="vAppealWrap">
                                    <span class="text-muted small d-block">{{ __('Vendor Appeal Message:') }}</span>
                                    <div class="p-2 bg-light rounded border small text-dark mt-1" id="vAppealMessage" style="white-space: pre-wrap; max-height: 100px; overflow-y: auto;">-</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Payment Proof Details -->
                    <div class="col-md-6 mb-3">
                        <div class="card h-100 shadow-sm border-0" style="border-radius: 8px;">
                            <div class="card-header bg-white py-2 font-weight-bold text-dark border-bottom">
                                <i class="fas fa-receipt text-success mr-1"></i> {{ __('Vendor Payment Proof Details') }}
                            </div>
                            <div class="card-body p-3">
                                <div class="mb-2">
                                    <span class="text-muted small d-block">{{ __('Admin Account Sent To:') }}</span>
                                    <strong class="text-dark" id="vAdminAccount">-</strong>
                                </div>
                                <div class="mb-2">
                                    <span class="text-muted small d-block">{{ __('Sent From (Bank / Wallet):') }}</span>
                                    <strong class="text-dark" id="vBankName">-</strong>
                                </div>
                                <div class="mb-2">
                                    <span class="text-muted small d-block">{{ __('Sender Account Title:') }}</span>
                                    <strong class="text-dark" id="vAccountName">-</strong>
                                </div>
                                <div class="mb-2">
                                    <span class="text-muted small d-block">{{ __('Sender Account Number:') }}</span>
                                    <code class="font-weight-bold text-primary" style="font-size: 14px;" id="vAccountNumber">-</code>
                                </div>
                                <div class="mb-0">
                                    <span class="text-muted small d-block">{{ __('Transaction ID / Ref No.:') }}</span>
                                    <span class="badge badge-warning text-dark font-weight-bold px-2 py-1" style="font-size: 13px; letter-spacing: 0.5px;" id="vTxnId">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Screenshot Preview Card -->
                <div class="card shadow-sm border-0 mb-3" style="border-radius: 8px;">
                    <div class="card-header bg-white py-2 font-weight-bold text-dark border-bottom d-flex align-items-center justify-content-between">
                        <span><i class="fas fa-image text-info mr-1"></i> {{ __('Payment Receipt / Screenshot') }}</span>
                        <a href="#" id="vScreenshotOpenLink" target="_blank" class="btn btn-xs btn-outline-primary py-0 px-2 small d-none">
                            <i class="fas fa-external-link-alt mr-1"></i> {{ __('Open Original Image') }}
                        </a>
                    </div>
                    <div class="card-body p-3 text-center bg-light">
                        <div id="vScreenshotBox">
                            <img src="" id="vScreenshotImg" class="img-fluid rounded shadow-sm border" style="max-height: 280px; object-fit: contain; cursor: pointer;" onclick="previewScreenshot(this.src, document.getElementById('vStoreName').innerText, document.getElementById('vTxnId').innerText)">
                        </div>
                        <div id="vNoScreenshotBox" class="text-muted font-italic py-3 d-none">
                            <i class="fas fa-image fa-2x mb-2 text-secondary d-block"></i>
                            {{ __('No receipt screenshot uploaded with this submission.') }}
                        </div>
                    </div>
                </div>

                <!-- Rejection Note Box (If Rejected) -->
                <div id="vRejectionBox" class="alert alert-danger mb-0 d-none" style="border-radius: 8px;">
                    <h6 class="font-weight-bold text-danger mb-1"><i class="fas fa-times-circle mr-1"></i> {{ __('Rejection Reason / Note:') }}</h6>
                    <p class="mb-0 small" id="vRejectionNote" style="white-space: pre-wrap;">-</p>
                </div>

            </div>

            <!-- Modal Footer with Direct Actions -->
            <div class="modal-footer bg-white py-3 d-flex justify-content-between align-items-center">
                <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">
                    {{ __('Close') }}
                </button>

                <div id="vPendingActions" class="d-flex align-items-center gap-2" style="gap: 8px;">
                    <!-- Direct Reject Button -->
                    <button type="button" class="btn btn-outline-danger font-weight-bold px-3 py-2" id="vRejectBtn" onclick="openRejectFromViewModal()">
                        <i class="fas fa-times mr-1"></i> {{ __('Reject Proof') }}
                    </button>

                    <!-- Direct Approve & Unblock Form -->
                    <form id="vApproveForm" action="" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to APPROVE this fine payment and UNBLOCK store & products immediately?') }}')">
                        @csrf
                        <button type="submit" class="btn btn-success font-weight-bold px-4 py-2 shadow-sm">
                            <i class="fas fa-unlock-alt mr-1"></i> {{ __('Approve Fine & Unblock Store') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================== -->
<!-- SCREENSHOT PREVIEW LIGHTBOX MODAL              -->
<!-- ============================================== -->
<div class="modal fade" id="screenshotPreviewModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header bg-dark text-white py-2">
                <h6 class="modal-title font-weight-bold text-white mb-0" id="previewModalTitle">
                    <i class="fas fa-image mr-2"></i> {{ __('Payment Proof Screenshot') }}
                </h6>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-2 text-center bg-light">
                <img src="" id="previewModalImg" class="img-fluid rounded shadow-sm" style="max-height: 75vh; object-fit: contain;">
                <div class="mt-2 text-muted small" id="previewModalSubtitle"></div>
            </div>
            <div class="modal-footer py-2 bg-white d-flex justify-content-between">
                <a href="" id="previewModalDownload" target="_blank" download class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-download mr-1"></i> {{ __('Open Original Image') }}
                </a>
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================== -->
<!-- REJECT FINE PAYMENT MODAL                      -->
<!-- ============================================== -->
<div class="modal fade" id="rejectFineModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <form id="rejectFineForm" method="POST" action="">
                @csrf
                <div class="modal-header bg-danger text-white py-3">
                    <h5 class="modal-title font-weight-bold text-white mb-0">
                        <i class="fas fa-times-circle mr-2"></i> {{ __('Reject Fine Payment Proof') }}
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4 bg-light">
                    <p class="text-dark mb-3">
                        {{ __('Rejecting payment for store:') }} <strong id="rejectStoreName" class="text-danger">-</strong>
                    </p>
                    <div class="form-group mb-0">
                        <label class="font-weight-bold text-dark">{{ __('Rejection Reason / Instruction to Vendor:') }} <span class="text-danger">*</span></label>
                        <textarea name="admin_note" id="rejectAdminNote" class="form-control" rows="4" placeholder="{{ __('e.g., Transaction ID not found in bank statement / Screenshot unclear. Please resubmit.') }}" required></textarea>
                        <small class="text-muted">{{ __('This reason will be sent directly to the vendor so they can correct and resubmit their payment proof.') }}</small>
                    </div>
                </div>
                <div class="modal-footer bg-white py-3">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-danger font-weight-bold">
                        <i class="fas fa-ban mr-1"></i> {{ __('Confirm Rejection') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let currentViewFineData = null;

    function openViewDetailsModal(data) {
        currentViewFineData = data;

        // Populate Store & Fine Info
        document.getElementById('vStoreName').innerText = data.storeName;
        document.getElementById('vStoreLink').href = data.storeUrl;
        document.getElementById('vApplicantName').innerText = data.applicantName;
        document.getElementById('vEmail').innerText = data.email || 'N/A';
        document.getElementById('vPhone').innerText = data.phone || 'N/A';
        document.getElementById('vFineAmount').innerText = data.fineAmount;
        document.getElementById('vFineImposedAt').innerText = data.fineImposedAt;
        document.getElementById('vSubmittedAt').innerText = data.submittedAt;

        // Appeal Message
        if (data.appealMessage) {
            document.getElementById('vAppealMessage').innerText = data.appealMessage;
            document.getElementById('vAppealWrap').classList.remove('d-none');
        } else {
            document.getElementById('vAppealWrap').classList.add('d-none');
        }

        // Status Badge
        const statusBadge = document.getElementById('vStatusBadge');
        if (data.status === 'approved') {
            statusBadge.className = 'badge badge-success px-3 py-1 font-weight-bold';
            statusBadge.innerText = 'Approved & Unblocked';
        } else if (data.status === 'rejected') {
            statusBadge.className = 'badge badge-danger px-3 py-1 font-weight-bold';
            statusBadge.innerText = 'Proof Rejected';
        } else {
            statusBadge.className = 'badge badge-warning text-dark px-3 py-1 font-weight-bold';
            statusBadge.innerText = 'Pending Verification';
        }

        // Vendor Payment Proof Details
        document.getElementById('vAdminAccount').innerText = data.adminAccount;
        document.getElementById('vBankName').innerText = data.bankName;
        document.getElementById('vAccountName').innerText = data.accountName;
        document.getElementById('vAccountNumber').innerText = data.accountNumber;
        document.getElementById('vTxnId').innerText = data.txnId;

        // Screenshot
        const screenshotImg = document.getElementById('vScreenshotImg');
        const screenshotBox = document.getElementById('vScreenshotBox');
        const noScreenshotBox = document.getElementById('vNoScreenshotBox');
        const openLink = document.getElementById('vScreenshotOpenLink');

        if (data.screenshotUrl) {
            screenshotImg.src = data.screenshotUrl;
            openLink.href = data.screenshotUrl;
            openLink.classList.remove('d-none');
            screenshotBox.classList.remove('d-none');
            noScreenshotBox.classList.add('d-none');
        } else {
            screenshotBox.classList.add('d-none');
            openLink.classList.add('d-none');
            noScreenshotBox.classList.remove('d-none');
        }

        // Rejection Note
        const rejectionBox = document.getElementById('vRejectionBox');
        if (data.status === 'rejected' && data.adminNote) {
            document.getElementById('vRejectionNote').innerText = data.adminNote;
            rejectionBox.classList.remove('d-none');
        } else {
            rejectionBox.classList.add('d-none');
        }

        // Actions
        const pendingActions = document.getElementById('vPendingActions');
        if (data.status === 'pending') {
            document.getElementById('vApproveForm').action = data.approveUrl;
            pendingActions.classList.remove('d-none');
        } else {
            pendingActions.classList.add('d-none');
        }

        $('#viewFineDetailsModal').modal('show');
    }

    function openRejectFromViewModal() {
        if (!currentViewFineData) return;
        $('#viewFineDetailsModal').modal('hide');
        setTimeout(function() {
            openRejectModal(currentViewFineData.id, currentViewFineData.storeName, currentViewFineData.adminNote);
        }, 350);
    }

    function previewScreenshot(url, store, txnid) {
        document.getElementById('previewModalImg').src = url;
        document.getElementById('previewModalTitle').innerHTML = '<i class="fas fa-image mr-2"></i> Proof - ' + store + (txnid ? ' (Txn: ' + txnid + ')' : '');
        document.getElementById('previewModalSubtitle').innerText = store + ' • Txn ID: ' + (txnid || 'N/A');
        document.getElementById('previewModalDownload').href = url;
        $('#screenshotPreviewModal').modal('show');
    }

    function openRejectModal(id, store, existingNote = '') {
        const form = document.getElementById('rejectFineForm');
        form.action = "{{ url('admin/fine-approvals/reject') }}/" + id;
        document.getElementById('rejectStoreName').innerText = store;
        document.getElementById('rejectAdminNote').value = existingNote || '';
        $('#rejectFineModal').modal('show');
    }
</script>
@endsection