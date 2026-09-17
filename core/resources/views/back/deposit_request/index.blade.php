@extends('master.back')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
        <div class="card-body py-3">
            <div class="d-sm-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-0 text-dark font-weight-bold">
                        <i class="fas fa-hand-holding-usd text-primary mr-2"></i> {{ __('Vendor Deposit Requests') }}
                    </h4>
                    <p class="text-muted small mb-0">{{ __('Review wallet balance deposit requests submitted by vendors, verify payment proofs, and credit vendor store balances.') }}</p>
                </div>
                <div class="mt-2 mt-sm-0 d-flex gap-2" style="gap: 8px;">
                    <a href="{{ route('back.store_setting.index') }}" class="btn btn-outline-primary btn-sm font-weight-bold">
                        <i class="fas fa-cogs mr-1"></i> {{ __('Stores Rate & Setting') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    @include('alerts.alerts')

    <!-- Status Filter Tabs & Search -->
    <div class="row mb-3">
        <div class="col-12 d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div class="btn-group flex-wrap mb-2 mb-md-0" role="group">
                <a href="{{ route('back.deposit_request.index', array_filter(['search' => $search])) }}" class="btn btn-sm {{ empty($status) ? 'btn-primary font-weight-bold' : 'btn-outline-primary' }}">
                    {{ __('All Requests') }} <span class="badge badge-light ml-1">{{ $pendingCount + $approvedCount + $rejectedCount }}</span>
                </a>
                <a href="{{ route('back.deposit_request.index', array_filter(['status' => 'pending', 'search' => $search])) }}" class="btn btn-sm {{ $status == 'pending' ? 'btn-warning text-dark font-weight-bold' : 'btn-outline-warning text-dark' }}">
                    <i class="fas fa-clock mr-1"></i> {{ __('Pending') }}
                    @if($pendingCount > 0)
                        <span class="badge badge-warning ml-1">{{ $pendingCount }}</span>
                    @endif
                </a>
                <a href="{{ route('back.deposit_request.index', array_filter(['status' => 'approved', 'search' => $search])) }}" class="btn btn-sm {{ $status == 'approved' ? 'btn-success text-white font-weight-bold' : 'btn-outline-success' }}">
                    <i class="fas fa-check-circle mr-1"></i> {{ __('Approved') }} <span class="badge badge-success ml-1">{{ $approvedCount }}</span>
                </a>
                <a href="{{ route('back.deposit_request.index', array_filter(['status' => 'rejected', 'search' => $search])) }}" class="btn btn-sm {{ $status == 'rejected' ? 'btn-danger text-white font-weight-bold' : 'btn-outline-danger' }}">
                    <i class="fas fa-times-circle mr-1"></i> {{ __('Rejected') }} <span class="badge badge-danger ml-1">{{ $rejectedCount }}</span>
                </a>
            </div>

            <!-- Search Form -->
            <form action="{{ route('back.deposit_request.index') }}" method="GET" class="form-inline">
                @if($status)
                    <input type="hidden" name="status" value="{{ $status }}">
                @endif
                <div class="input-group input-group-sm">
                    <input type="text" name="search" class="form-control" placeholder="{{ __('Search Txn ID, store, vendor...') }}" value="{{ $search }}" style="border-radius: 20px 0 0 20px; min-width: 220px;">
                    <div class="input-group-append">
                        <button class="btn btn-primary btn-sm" type="submit" style="border-radius: 0 20px 20px 0;">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                @if($search || $status)
                    <a href="{{ route('back.deposit_request.index') }}" class="btn btn-outline-secondary btn-sm ml-2" title="{{ __('Clear Filters') }}">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Deposit Requests Table Card -->
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; overflow: hidden;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-items-center mb-0" width="100%" cellspacing="0" style="vertical-align: middle;">
                    <thead class="bg-light text-secondary" style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">
                        <tr>
                            <th class="text-center" style="width: 50px;">#</th>
                            <th>{{ __('Vendor & Store') }}</th>
                            <th>{{ __('Payment Sent To') }}</th>
                            <th>{{ __('Sender Account Details') }}</th>
                            <th class="text-right">{{ __('Amount') }}</th>
                            <th>{{ __('Txn ID') }}</th>
                            <th class="text-center">{{ __('Proof Screenshot') }}</th>
                            <th class="text-center">{{ __('Status') }}</th>
                            <th>{{ __('Date & Time') }}</th>
                            <th class="text-center" style="width: 220px;">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 13.5px;">
                        @forelse($datas as $deposit)
                            @php
                                $store = $deposit->seller ?: ($deposit->user ? $deposit->user->seller : null);
                                $storeName = $store ? $store->shop_name : ($deposit->user ? ($deposit->user->first_name . ' ' . $deposit->user->last_name) : 'Vendor #' . $deposit->user_id);
                                $applicantName = $deposit->user ? ($deposit->user->first_name . ' ' . $deposit->user->last_name) : ($deposit->account_name ?: 'Vendor');
                                $email = $deposit->user ? $deposit->user->email : '';
                                $phone = $deposit->user ? $deposit->user->phone : '';
                                $walletBalance = $store ? (float)$store->balance : 0;

                                $statusClass = 'badge-warning text-dark';
                                if ($deposit->status === 'approved') $statusClass = 'badge-success text-white';
                                elseif ($deposit->status === 'rejected') $statusClass = 'badge-danger text-white';

                                $screenshotUrl = '';
                                if ($deposit->screenshot) {
                                    if (file_exists(public_path('storage/images/deposits/' . $deposit->screenshot))) {
                                        $screenshotUrl = asset('core/public/storage/images/deposits/' . $deposit->screenshot);
                                    } else {
                                        $screenshotUrl = url('/core/public/storage/images/deposits/' . $deposit->screenshot);
                                    }
                                }

                                $depositData = [
                                    'id' => $deposit->id,
                                    'storeName' => $storeName,
                                    'storeUrl' => $deposit->user_id ? route('front.catalog') . '?vendor=' . $deposit->user_id : '',
                                    'applicantName' => $applicantName,
                                    'email' => $email,
                                    'phone' => $phone,
                                    'walletBalance' => PriceHelper::adminCurrency() . ' ' . number_format($walletBalance, 2),
                                    'amount' => PriceHelper::adminCurrency() . ' ' . number_format($deposit->amount, 2),
                                    'amountRaw' => $deposit->amount,
                                    'paymentMethod' => $deposit->payment_method ?: 'Direct Transfer',
                                    'bankName' => $deposit->bank_name ?: '-',
                                    'accountName' => $deposit->account_name ?: '-',
                                    'accountNumber' => $deposit->account_number ?: '-',
                                    'txnId' => $deposit->txn_id ?: '-',
                                    'screenshotUrl' => $screenshotUrl,
                                    'status' => $deposit->status,
                                    'adminNote' => $deposit->admin_note ?: '',
                                    'submittedAt' => $deposit->created_at ? $deposit->created_at->format('M d, Y - h:i A') : '-',
                                    'approveUrl' => route('back.deposit_request.approve', $deposit->id),
                                    'rejectUrl' => url('admin/deposit-requests/reject/' . $deposit->id)
                                ];
                            @endphp
                            <tr>
                                <td class="text-center font-weight-bold text-muted">{{ $deposit->id }}</td>
                                <td>
                                    <div class="font-weight-bold text-dark">
                                        @if($deposit->user_id)
                                            <a href="{{ route('front.catalog') }}?vendor={{ $deposit->user_id }}" target="_blank" class="text-primary text-decoration-none">
                                                <i class="fas fa-store mr-1"></i> {{ $storeName }}
                                                <i class="fas fa-external-link-alt ml-1" style="font-size: 8.5px;"></i>
                                            </a>
                                        @else
                                            <span class="text-primary"><i class="fas fa-store mr-1"></i> {{ $storeName }}</span>
                                        @endif
                                    </div>
                                    <div class="small text-muted font-weight-bold">{{ $applicantName }}</div>
                                    @if($email)
                                        <div class="small text-muted" style="font-size: 11px;"><i class="fas fa-envelope mr-1"></i>{{ $email }}</div>
                                    @endif
                                    @if($store)
                                        <div class="small text-success font-weight-bold mt-1" style="font-size: 11px;">
                                            <i class="fas fa-wallet mr-1"></i>{{ __('Balance:') }} {{ PriceHelper::adminCurrency() }} {{ number_format($store->balance ?? 0, 2) }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-dark font-weight-bold small" style="max-width: 180px;">
                                        {{ $deposit->payment_method }}
                                    </div>
                                </td>
                                <td>
                                    @if($deposit->bank_name)
                                        <span class="badge badge-light border text-primary font-weight-bold mb-1" style="font-size: 11px;">{{ $deposit->bank_name }}</span>
                                    @endif
                                    <div class="text-dark font-weight-bold small">{{ $deposit->account_name }}</div>
                                    <small class="text-muted font-italic">{{ $deposit->account_number }}</small>
                                </td>
                                <td class="text-right">
                                    <strong class="text-success font-weight-bold" style="font-size: 15px;">
                                        + {{ PriceHelper::adminCurrency() }} {{ number_format($deposit->amount, 2) }}
                                    </strong>
                                </td>
                                <td>
                                    <span class="badge badge-light border text-dark font-weight-bold px-2 py-1" style="font-size: 12px; letter-spacing: 0.3px;">
                                        {{ $deposit->txn_id }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($screenshotUrl)
                                        <a href="javascript:void(0)" onclick="previewScreenshot('{{ $screenshotUrl }}', '{{ addslashes($storeName) }}', '{{ $deposit->txn_id }}')" class="d-inline-block shadow-sm rounded overflow-hidden" style="border: 2px solid #e2e8f0; transition: transform 0.2s;" title="{{ __('Click to preview full image') }}">
                                            <img src="{{ $screenshotUrl }}" alt="Proof" style="width: 48px; height: 48px; object-fit: cover; display: block;">
                                        </a>
                                    @else
                                        <span class="text-muted small font-italic">{{ __('No Proof') }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $statusClass }} px-2 py-1 font-weight-bold" style="font-size: 11px; border-radius: 12px;">
                                        {{ ucfirst($deposit->status) }}
                                    </span>
                                    @if($deposit->admin_note && $deposit->status === 'rejected')
                                        <small class="d-block text-danger mt-1" style="font-size: 10px; max-width: 130px; line-height: 1.2;">
                                            {{ Str::limit($deposit->admin_note, 35) }}
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    <span class="d-block text-dark font-weight-bold" style="font-size: 12px;">{{ $deposit->created_at->format('M d, Y') }}</span>
                                    <small class="text-muted" style="font-size: 10.5px;">{{ $deposit->created_at->format('h:i A') }}</small>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-1" style="gap: 4px;">
                                        <!-- View Details Button -->
                                        <button type="button" class="btn btn-sm btn-primary font-weight-bold px-2 py-1" title="{{ __('View Full Details & Manage') }}" onclick='openViewDetailsModal({!! json_encode($depositData) !!})'>
                                            <i class="fas fa-eye mr-1"></i> {{ __('View') }}
                                        </button>

                                        @if($deposit->status === 'pending')
                                            <!-- Direct Approve Button -->
                                            <a href="{{ route('back.deposit_request.approve', $deposit->id) }}" class="btn btn-sm btn-success font-weight-bold px-2 py-1" onclick="return confirm('{{ __('Are you sure you want to APPROVE this deposit of :currency :amount for :vendor? This will credit the vendor wallet balance immediately.', ['currency' => PriceHelper::adminCurrency(), 'amount' => number_format($deposit->amount, 2), 'vendor' => $storeName]) }}')" title="{{ __('Approve Deposit & Credit Wallet Immediately') }}">
                                                <i class="fas fa-check"></i> {{ __('Approve') }}
                                            </a>

                                            <!-- Direct Reject Button -->
                                            <button type="button" class="btn btn-sm btn-outline-danger px-2 py-1" onclick="openRejectModal({{ $deposit->id }}, '{{ addslashes($storeName) }}', '{{ number_format($deposit->amount, 2) }}')" title="{{ __('Reject Deposit Request') }}">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @elseif($deposit->status === 'approved')
                                            <span class="badge badge-success px-2 py-1 font-weight-bold" style="font-size: 11px;">
                                                <i class="fas fa-check mr-1"></i> {{ __('Approved') }}
                                            </span>
                                        @else
                                            <button type="button" class="btn btn-sm btn-outline-secondary px-2 py-1" onclick="openRejectModal({{ $deposit->id }}, '{{ addslashes($storeName) }}', '{{ number_format($deposit->amount, 2) }}', '{{ addslashes($deposit->admin_note) }}')" title="{{ __('View Rejection Reason') }}">
                                                <i class="fas fa-info-circle"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-5 text-muted">
                                    <i class="fas fa-hand-holding-usd fa-3x text-secondary mb-3 d-block"></i>
                                    <h5>{{ __('No Deposit Requests Found') }}</h5>
                                    <p class="small text-muted">{{ __('When vendors submit wallet deposit requests, they will appear here for verification and approval.') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Links -->
            @if($datas->hasPages())
                <div class="p-3 d-flex justify-content-between align-items-center flex-wrap bg-light border-top">
                    <div class="small text-muted mb-2 mb-sm-0">
                        {{ __('Showing') }} {{ $datas->firstItem() }} - {{ $datas->lastItem() }} {{ __('of') }} {{ $datas->total() }} {{ __('requests') }}
                    </div>
                    <div>
                        {{ $datas->appends(request()->query())->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- ==================================================== -->
<!-- VIEW FULL DEPOSIT REQUEST DETAILS MODAL              -->
<!-- ==================================================== -->
<div class="modal fade" id="viewDepositDetailsModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header bg-primary text-white py-3 d-flex align-items-center justify-content-between">
                <h5 class="modal-title font-weight-bold text-white mb-0 d-flex align-items-center">
                    <i class="fas fa-hand-holding-usd mr-2"></i> {{ __('Deposit Request & Payment Verification Details') }}
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4 bg-light" style="max-height: 80vh; overflow-y: auto;">
                
                <!-- Status & Deposit Amount Banner -->
                <div class="card border-0 shadow-sm mb-4 bg-white" style="border-radius: 10px; border-left: 5px solid #10b981 !important;">
                    <div class="card-body p-3">
                        <div class="d-sm-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted small font-weight-bold text-uppercase d-block mb-1">{{ __('Deposit Request Amount') }}</span>
                                <h3 class="font-weight-bold text-success mb-0" id="vAmount">-</h3>
                            </div>
                            <div class="mt-2 mt-sm-0 text-sm-right">
                                <span class="text-muted small d-block mb-1">{{ __('Status') }}</span>
                                <span class="badge px-3 py-1 font-weight-bold" id="vStatusBadge" style="font-size: 13px; border-radius: 20px;">-</span>
                            </div>
                        </div>
                        <div class="mt-2 pt-2 border-top small text-muted d-flex justify-content-between">
                            <span><i class="fas fa-clock mr-1"></i> {{ __('Submitted At:') }} <strong class="text-dark" id="vSubmittedAt">-</strong></span>
                            <span><i class="fas fa-wallet mr-1"></i> {{ __('Current Store Balance:') }} <strong class="text-primary" id="vWalletBalance">-</strong></span>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Left: Store & Vendor Info -->
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
                                <div class="mb-0">
                                    <span class="text-muted small d-block">{{ __('Phone Number:') }}</span>
                                    <span class="text-dark" id="vPhone">-</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Payment Proof Details -->
                    <div class="col-md-6 mb-3">
                        <div class="card h-100 shadow-sm border-0" style="border-radius: 8px;">
                            <div class="card-header bg-white py-2 font-weight-bold text-dark border-bottom">
                                <i class="fas fa-receipt text-success mr-1"></i> {{ __('Payment Proof Details') }}
                            </div>
                            <div class="card-body p-3">
                                <div class="mb-2">
                                    <span class="text-muted small d-block">{{ __('Admin Account / Method Sent To:') }}</span>
                                    <strong class="text-dark" id="vPaymentMethod">-</strong>
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

                <!-- Proof Screenshot Preview Card -->
                <div class="card shadow-sm border-0 mb-3" style="border-radius: 8px;">
                    <div class="card-header bg-white py-2 font-weight-bold text-dark border-bottom d-flex align-items-center justify-content-between">
                        <span><i class="fas fa-image text-info mr-1"></i> {{ __('Payment Receipt / Proof Screenshot') }}</span>
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
                        <i class="fas fa-times mr-1"></i> {{ __('Reject Request') }}
                    </button>

                    <!-- Direct Approve & Credit Form -->
                    <a href="#" id="vApproveLink" class="btn btn-success font-weight-bold px-4 py-2 shadow-sm" onclick="return confirm('{{ __('Are you sure you want to APPROVE this deposit request and credit the vendor store wallet immediately?') }}')">
                        <i class="fas fa-check-circle mr-1"></i> {{ __('Approve & Credit Wallet') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ==================================================== -->
<!-- REJECT DEPOSIT REQUEST MODAL                         -->
<!-- ==================================================== -->
<div class="modal fade" id="rejectDepositModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header bg-danger text-white py-3">
                <h5 class="modal-title text-white font-weight-bold mb-0">
                    <i class="fas fa-times-circle mr-2"></i> {{ __('Reject Deposit Request') }}
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="rejectDepositForm" method="POST" action="">
                @csrf
                <div class="modal-body p-4 bg-light">
                    <p class="text-dark mb-3">
                        {{ __('Rejecting deposit request for store:') }} <strong id="rejectStoreName" class="text-danger"></strong> (<span id="rejectAmount" class="font-weight-bold"></span>)
                    </p>

                    <div class="form-group mb-0">
                        <label for="admin_note" class="font-weight-bold text-dark">{{ __('Reason for Rejection / Instruction to Vendor:') }} <span class="text-danger">*</span></label>
                        <textarea name="admin_note" id="admin_note" rows="4" class="form-control" placeholder="{{ __('e.g. Transaction ID not found in bank statement / Screenshot unclear. Please resubmit.') }}" required></textarea>
                        <small class="text-muted">{{ __('This reason will be visible to the vendor so they can correct and resubmit their deposit proof.') }}</small>
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

<!-- ==================================================== -->
<!-- SCREENSHOT LIGHTBOX MODAL                            -->
<!-- ==================================================== -->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header bg-dark text-white py-2">
                <h6 class="modal-title text-white font-weight-bold mb-0" id="imageModalTitle">
                    <i class="fas fa-image mr-2"></i> {{ __('Payment Proof Screenshot') }}
                </h6>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-2 text-center bg-light">
                <img id="modalImg" src="" alt="Proof Screenshot" class="img-fluid rounded shadow-sm" style="max-height: 75vh; object-fit: contain;">
                <div class="mt-2 text-muted small" id="imageModalSubtitle"></div>
            </div>
            <div class="modal-footer bg-white py-2 d-flex justify-content-between">
                <a id="modalDownloadBtn" href="" target="_blank" download class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-external-link-alt mr-1"></i> {{ __('Open Original Image') }}
                </a>
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    let currentDepositData = null;

    function openViewDetailsModal(data) {
        currentDepositData = data;

        document.getElementById('vAmount').innerText = '+ ' + data.amount;
        
        const statusBadge = document.getElementById('vStatusBadge');
        statusBadge.innerText = (data.status || '').toUpperCase();
        if (data.status === 'approved') {
            statusBadge.className = 'badge badge-success px-3 py-1 font-weight-bold';
        } else if (data.status === 'rejected') {
            statusBadge.className = 'badge badge-danger px-3 py-1 font-weight-bold';
        } else {
            statusBadge.className = 'badge badge-warning text-dark px-3 py-1 font-weight-bold';
        }

        document.getElementById('vSubmittedAt').innerText = data.submittedAt || '-';
        document.getElementById('vWalletBalance').innerText = data.walletBalance || '-';
        
        document.getElementById('vStoreName').innerText = data.storeName || '-';
        const storeLink = document.getElementById('vStoreLink');
        if (data.storeUrl) {
            storeLink.href = data.storeUrl;
            storeLink.classList.remove('d-none');
        } else {
            storeLink.classList.add('d-none');
        }

        document.getElementById('vApplicantName').innerText = data.applicantName || '-';
        document.getElementById('vEmail').innerText = data.email || '-';
        document.getElementById('vPhone').innerText = data.phone || '-';

        document.getElementById('vPaymentMethod').innerText = data.paymentMethod || '-';
        document.getElementById('vBankName').innerText = data.bankName || '-';
        document.getElementById('vAccountName').innerText = data.accountName || '-';
        document.getElementById('vAccountNumber').innerText = data.accountNumber || '-';
        document.getElementById('vTxnId').innerText = data.txnId || '-';

        const screenshotBox = document.getElementById('vScreenshotBox');
        const noScreenshotBox = document.getElementById('vNoScreenshotBox');
        const screenshotOpenLink = document.getElementById('vScreenshotOpenLink');

        if (data.screenshotUrl) {
            document.getElementById('vScreenshotImg').src = data.screenshotUrl;
            screenshotOpenLink.href = data.screenshotUrl;
            screenshotOpenLink.classList.remove('d-none');
            screenshotBox.classList.remove('d-none');
            noScreenshotBox.classList.add('d-none');
        } else {
            screenshotOpenLink.classList.add('d-none');
            screenshotBox.classList.add('d-none');
            noScreenshotBox.classList.remove('d-none');
        }

        const rejectionBox = document.getElementById('vRejectionBox');
        if (data.status === 'rejected' && data.adminNote) {
            rejectionBox.classList.remove('d-none');
            document.getElementById('vRejectionNote').innerText = data.adminNote;
        } else {
            rejectionBox.classList.add('d-none');
        }

        const pendingActions = document.getElementById('vPendingActions');
        const approveLink = document.getElementById('vApproveLink');
        if (data.status === 'pending') {
            pendingActions.classList.remove('d-none');
            approveLink.href = data.approveUrl;
        } else {
            pendingActions.classList.add('d-none');
        }

        $('#viewDepositDetailsModal').modal('show');
    }

    function openRejectFromViewModal() {
        if (!currentDepositData) return;
        $('#viewDepositDetailsModal').modal('hide');
        openRejectModal(currentDepositData.id, currentDepositData.storeName, currentDepositData.amount, currentDepositData.adminNote);
    }

    function openRejectModal(id, storeName, amount, currentNote) {
        const form = document.getElementById('rejectDepositForm');
        form.action = "{{ url('admin/deposit-requests/reject') }}/" + id;
        document.getElementById('rejectStoreName').innerText = storeName;
        document.getElementById('rejectAmount').innerText = (amount.toString().includes('PKR') ? amount : 'PKR ' + amount);
        const noteField = document.getElementById('admin_note');
        if (currentNote) {
            noteField.value = currentNote;
        } else {
            noteField.value = '';
        }
        $('#rejectDepositModal').modal('show');
    }

    function previewScreenshot(imgUrl, storeName, txnId) {
        document.getElementById('modalImg').src = imgUrl;
        document.getElementById('imageModalTitle').innerHTML = '<i class="fas fa-image mr-2"></i> Payment Proof: ' + (storeName || '');
        document.getElementById('imageModalSubtitle').innerText = txnId ? 'Txn ID: ' + txnId : '';
        document.getElementById('modalDownloadBtn').href = imgUrl;
        $('#imageModal').modal('show');
    }
</script>
@endsection