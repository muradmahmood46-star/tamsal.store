@extends('master.back')

@section('styles')
<style>
    .txn-table-wrapper {
        width: 100% !important;
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch;
        margin-bottom: 1rem;
        border: 1px solid #ebedf2;
        border-radius: 8px;
    }
    .txn-table-wrapper::-webkit-scrollbar {
        height: 6px;
    }
    .txn-table-wrapper::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    .txn-table-wrapper::-webkit-scrollbar-thumb {
        background: #ced4da;
        border-radius: 4px;
    }
    .txn-table-wrapper::-webkit-scrollbar-thumb:hover {
        background: #6c757d;
    }
    .txn-table {
        min-width: 1100px;
        width: 100% !important;
        margin-bottom: 0 !important;
    }
    .txn-table th {
        font-size: 11.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        white-space: nowrap;
        background-color: #f8f9fa !important;
        vertical-align: middle !important;
        padding: 11px 10px !important;
        border-bottom: 2px solid #dee2e6 !important;
        color: #555;
    }
    .txn-table td {
        font-size: 13px;
        vertical-align: middle !important;
        padding: 10px 10px !important;
    }
    .badge-outline-primary {
        color: #0d6efd;
        border: 1px solid #0d6efd;
        background: transparent;
    }
    .badge-outline-success {
        color: #198754;
        border: 1px solid #198754;
        background: transparent;
    }
    .badge-outline-warning {
        color: #d97706;
        border: 1px solid #d97706;
        background: transparent;
    }
</style>
@endsection

@section('content')

<div class="container-fluid">

    <!-- Page Heading -->
    <div class="card mb-4 shadow-sm border-0" style="border-radius: 12px;">
        <div class="card-body py-3">
            <div class="d-sm-flex align-items-center justify-content-between">
                <div>
                    <h3 class="mb-0 text-dark font-weight-bold">
                        <i class="fas fa-exchange-alt text-primary mr-2"></i> {{ __('Platform Transactions') }}
                    </h3>
                    <p class="text-muted small mb-0">{{ __('Complete audit history of all transactions including orders, fine approvals, store registration fees, and wallet deposits.') }}</p>
                </div>
                <div class="mt-2 mt-sm-0 d-flex gap-2" style="gap: 8px;">
                    <a href="{{ route('back.csv.transaction.export') }}" class="btn btn-outline-info btn-sm font-weight-bold">
                        <i class="fas fa-file-csv mr-1"></i> {{ __('CSV Export') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    @include('alerts.alerts')

    <!-- Summary Stat Cards -->
    <div class="row mb-3">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 10px; border-left: 4px solid #0d6efd !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small text-uppercase font-weight-bold d-block">{{ __('All Transactions') }}</span>
                            <h4 class="font-weight-bold text-primary mb-0">{{ $counts['all'] }}</h4>
                        </div>
                        <div class="p-3 bg-light rounded-circle text-primary">
                            <i class="fas fa-wallet fa-lg"></i>
                        </div>
                    </div>
                    <div class="mt-2 pt-2 border-top small text-muted">
                        {{ __('Total Volume:') }} <strong class="text-dark">{{ PriceHelper::adminCurrency() }} {{ number_format($totals['all_amount'], 2) }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 10px; border-left: 4px solid #0284c7 !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small text-uppercase font-weight-bold d-block">{{ __('Order Payments') }}</span>
                            <h4 class="font-weight-bold text-dark mb-0">{{ $counts['order'] }}</h4>
                        </div>
                        <div class="p-3 bg-light rounded-circle text-info">
                            <i class="fas fa-shopping-cart fa-lg"></i>
                        </div>
                    </div>
                    <div class="mt-2 pt-2 border-top small text-muted">
                        {{ __('Orders Volume:') }} <strong class="text-dark">{{ PriceHelper::adminCurrency() }} {{ number_format($totals['order_amount'], 2) }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 10px; border-left: 4px solid #f59e0b !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small text-uppercase font-weight-bold d-block">{{ __('Fine Approvals') }}</span>
                            <h4 class="font-weight-bold text-warning mb-0">{{ $counts['fine'] }}</h4>
                        </div>
                        <div class="p-3 bg-light rounded-circle text-warning">
                            <i class="fas fa-file-invoice-dollar fa-lg"></i>
                        </div>
                    </div>
                    <div class="mt-2 pt-2 border-top small text-muted">
                        {{ __('Fines Volume:') }} <strong class="text-dark">{{ PriceHelper::adminCurrency() }} {{ number_format($totals['fine_amount'], 2) }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 10px; border-left: 4px solid #10b981 !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small text-uppercase font-weight-bold d-block">{{ __('Store & Deposit Fees') }}</span>
                            <h4 class="font-weight-bold text-success mb-0">{{ $counts['store_request'] + $counts['deposit'] }}</h4>
                        </div>
                        <div class="p-3 bg-light rounded-circle text-success">
                            <i class="fas fa-hand-holding-usd fa-lg"></i>
                        </div>
                    </div>
                    <div class="mt-2 pt-2 border-top small text-muted">
                        {{ __('Fees & Deposits:') }} <strong class="text-dark">{{ PriceHelper::adminCurrency() }} {{ number_format($totals['store_amount'] + $totals['deposit_amount'], 2) }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Tabs & Search Row -->
    <div class="row mb-3">
        <div class="col-12 d-flex flex-wrap align-items-center justify-content-between gap-2">
            <!-- Type Tabs -->
            <div class="btn-group flex-wrap mb-2 mb-lg-0" role="group">
                <a href="{{ route('back.transaction.index', array_filter(['status' => $status, 'search' => $search])) }}" class="btn btn-sm {{ empty($type) ? 'btn-primary font-weight-bold' : 'btn-outline-primary' }}">
                    {{ __('All') }} <span class="badge badge-light ml-1">{{ $counts['all'] }}</span>
                </a>
                <a href="{{ route('back.transaction.index', array_filter(['type' => 'order', 'status' => $status, 'search' => $search])) }}" class="btn btn-sm {{ $type == 'order' ? 'btn-primary font-weight-bold' : 'btn-outline-primary' }}">
                    <i class="fas fa-shopping-cart mr-1"></i> {{ __('Orders') }} <span class="badge badge-light ml-1">{{ $counts['order'] }}</span>
                </a>
                <a href="{{ route('back.transaction.index', array_filter(['type' => 'fine', 'status' => $status, 'search' => $search])) }}" class="btn btn-sm {{ $type == 'fine' ? 'btn-warning text-dark font-weight-bold' : 'btn-outline-warning text-dark' }}">
                    <i class="fas fa-file-invoice-dollar mr-1"></i> {{ __('Fine Approvals') }} <span class="badge badge-warning ml-1">{{ $counts['fine'] }}</span>
                </a>
                <a href="{{ route('back.transaction.index', array_filter(['type' => 'store_request', 'status' => $status, 'search' => $search])) }}" class="btn btn-sm {{ $type == 'store_request' ? 'btn-info text-white font-weight-bold' : 'btn-outline-info' }}">
                    <i class="fas fa-store mr-1"></i> {{ __('Store Requests') }} <span class="badge badge-info ml-1">{{ $counts['store_request'] }}</span>
                </a>
                <a href="{{ route('back.transaction.index', array_filter(['type' => 'deposit', 'status' => $status, 'search' => $search])) }}" class="btn btn-sm {{ $type == 'deposit' ? 'btn-success text-white font-weight-bold' : 'btn-outline-success' }}">
                    <i class="fas fa-hand-holding-usd mr-1"></i> {{ __('Deposit Requests') }} <span class="badge badge-success ml-1">{{ $counts['deposit'] }}</span>
                </a>
            </div>

            <!-- Search & Status Form -->
            <form action="{{ route('back.transaction.index') }}" method="GET" class="form-inline">
                @if($type)
                    <input type="hidden" name="type" value="{{ $type }}">
                @endif
                <div class="input-group input-group-sm mr-2">
                    <select name="status" class="form-control form-control-sm" onchange="this.form.submit()" style="border-radius: 6px;">
                        <option value="">{{ __('All Statuses') }}</option>
                        <option value="Paid" {{ strtolower($status) == 'paid' ? 'selected' : '' }}>{{ __('Paid') }}</option>
                        <option value="Approved" {{ strtolower($status) == 'approved' ? 'selected' : '' }}>{{ __('Approved') }}</option>
                        <option value="Pending" {{ strtolower($status) == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                        <option value="Rejected" {{ strtolower($status) == 'rejected' ? 'selected' : '' }}>{{ __('Rejected') }}</option>
                        <option value="Unpaid" {{ strtolower($status) == 'unpaid' ? 'selected' : '' }}>{{ __('Unpaid') }}</option>
                    </select>
                </div>
                <div class="input-group input-group-sm">
                    <input type="text" name="search" class="form-control" placeholder="{{ __('Search Txn ID, name, store...') }}" value="{{ $search }}" style="border-radius: 20px 0 0 20px; min-width: 220px;">
                    <div class="input-group-append">
                        <button class="btn btn-primary btn-sm" type="submit" style="border-radius: 0 20px 20px 0;">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                @if($search || $status || $type)
                    <a href="{{ route('back.transaction.index') }}" class="btn btn-outline-secondary btn-sm ml-2" title="{{ __('Clear Filters') }}">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Transactions Data Table Card -->
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; overflow: hidden;">
        <div class="card-body p-0">
            <div class="txn-table-wrapper">
                <table class="table table-hover align-items-center mb-0 txn-table" style="vertical-align: middle;">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">#</th>
                            <th style="width: 140px;">{{ __('Type / Source') }}</th>
                            <th>{{ __('Customer / Store / Vendor') }}</th>
                            <th>{{ __('Transaction ID / Ref') }}</th>
                            <th>{{ __('Payment Method') }}</th>
                            <th class="text-center">{{ __('Proof') }}</th>
                            <th class="text-center">{{ __('Status') }}</th>
                            <th class="text-right">{{ __('Amount') }}</th>
                            <th>{{ __('Date & Time') }}</th>
                            <th class="text-center" style="width: 130px;">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 13px;">
                        @include('back.transactions.table', ['datas' => $datas])
                    </tbody>
                </table>
            </div>

            <!-- Pagination Links -->
            @if($datas->hasPages())
                <div class="p-3 d-flex justify-content-between align-items-center flex-wrap bg-light border-top">
                    <div class="small text-muted mb-2 mb-sm-0">
                        {{ __('Showing') }} {{ $datas->firstItem() }} - {{ $datas->lastItem() }} {{ __('of') }} {{ $datas->total() }} {{ __('transactions') }}
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
<!-- INTERACTIVE QUICK VIEW TRANSACTION DETAILS MODAL     -->
<!-- ==================================================== -->
<div class="modal fade" id="viewTxnDetailsModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header bg-primary text-white py-3 d-flex align-items-center justify-content-between">
                <h5 class="modal-title font-weight-bold text-white mb-0 d-flex align-items-center">
                    <i class="fas fa-receipt mr-2"></i> <span id="m_title">{{ __('Transaction Details') }}</span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4 bg-light" style="max-height: 80vh; overflow-y: auto;">
                
                <!-- Overview Header Card -->
                <div class="card border-0 shadow-sm mb-3 bg-white" style="border-radius: 10px;">
                    <div class="card-body p-3">
                        <div class="d-sm-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted small font-weight-bold text-uppercase d-block mb-1" id="m_type_label">-</span>
                                <h3 class="font-weight-bold text-success mb-0" id="m_amount">-</h3>
                            </div>
                            <div class="mt-2 mt-sm-0 text-sm-right">
                                <span class="text-muted small d-block mb-1">{{ __('Status') }}</span>
                                <span class="badge px-3 py-1 font-weight-bold" id="m_status_badge" style="font-size: 13px; border-radius: 20px;">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Left: Transaction Info -->
                    <div class="col-md-6 mb-3">
                        <div class="card h-100 shadow-sm border-0" style="border-radius: 8px;">
                            <div class="card-header bg-white py-2 font-weight-bold text-dark border-bottom">
                                <i class="fas fa-info-circle text-primary mr-1"></i> {{ __('Transaction Information') }}
                            </div>
                            <div class="card-body p-3">
                                <div class="mb-2">
                                    <span class="text-muted small d-block">{{ __('Transaction ID / Ref:') }}</span>
                                    <code class="font-weight-bold text-primary" style="font-size: 14px;" id="m_txnid">-</code>
                                </div>
                                <div class="mb-2">
                                    <span class="text-muted small d-block">{{ __('System Reference / Order #:') }}</span>
                                    <strong class="text-dark" id="m_reference">-</strong>
                                </div>
                                <div class="mb-2">
                                    <span class="text-muted small d-block">{{ __('Payment Method:') }}</span>
                                    <strong class="text-dark" id="m_method">-</strong>
                                </div>
                                <div class="mb-2">
                                    <span class="text-muted small d-block">{{ __('Date & Time:') }}</span>
                                    <span class="text-dark" id="m_date">-</span>
                                </div>
                                <div class="mb-0" id="m_details_wrap">
                                    <span class="text-muted small d-block">{{ __('Details / Note:') }}</span>
                                    <div class="p-2 bg-light rounded border small text-dark mt-1" id="m_details">-</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Store & Sender Info -->
                    <div class="col-md-6 mb-3">
                        <div class="card h-100 shadow-sm border-0" style="border-radius: 8px;">
                            <div class="card-header bg-white py-2 font-weight-bold text-dark border-bottom">
                                <i class="fas fa-user text-success mr-1"></i> {{ __('Sender & Store Information') }}
                            </div>
                            <div class="card-body p-3">
                                <div class="mb-2">
                                    <span class="text-muted small d-block">{{ __('Customer / Sender Name:') }}</span>
                                    <strong class="text-dark" id="m_name">-</strong>
                                </div>
                                <div class="mb-2">
                                    <span class="text-muted small d-block">{{ __('Store Name:') }}</span>
                                    <h6 class="font-weight-bold text-primary mb-0" id="m_store">-</h6>
                                </div>
                                <div class="mb-2">
                                    <span class="text-muted small d-block">{{ __('Email Address:') }}</span>
                                    <span class="text-dark" id="m_email">-</span>
                                </div>
                                <div class="mb-2">
                                    <span class="text-muted small d-block">{{ __('Phone Number:') }}</span>
                                    <span class="text-dark" id="m_phone">-</span>
                                </div>
                                <div class="mb-0" id="m_account_wrap">
                                    <span class="text-muted small d-block">{{ __('Sender Account Info:') }}</span>
                                    <div class="p-2 bg-light rounded border small text-dark mt-1" id="m_account_info">-</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Proof Screenshot Preview -->
                <div class="card shadow-sm border-0 mb-0" id="m_screenshot_card" style="border-radius: 8px;">
                    <div class="card-header bg-white py-2 font-weight-bold text-dark border-bottom d-flex align-items-center justify-content-between">
                        <span><i class="fas fa-image text-info mr-1"></i> {{ __('Payment Receipt / Proof Screenshot') }}</span>
                        <a href="#" id="m_screenshot_link" target="_blank" class="btn btn-xs btn-outline-primary py-0 px-2 small">
                            <i class="fas fa-external-link-alt mr-1"></i> {{ __('Open Original Image') }}
                        </a>
                    </div>
                    <div class="card-body p-3 text-center bg-light">
                        <img src="" id="m_screenshot_img" class="img-fluid rounded shadow-sm border" style="max-height: 280px; object-fit: contain; cursor: pointer;" onclick="previewScreenshot(this.src, document.getElementById('m_txnid').innerText)">
                    </div>
                </div>

            </div>
            <div class="modal-footer bg-white py-2 d-flex justify-content-between align-items-center">
                <button type="button" class="btn btn-secondary btn-sm font-weight-bold" data-dismiss="modal">{{ __('Close') }}</button>
                <div class="d-flex gap-2" style="gap: 6px;">
                    <a href="#" id="m_direct_link" class="btn btn-primary btn-sm font-weight-bold">
                        <i class="fas fa-external-link-alt mr-1"></i> <span id="m_direct_label">{{ __('View Source') }}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ==================================================== -->
<!-- SCREENSHOT PREVIEW LIGHTBOX MODAL                   -->
<!-- ==================================================== -->
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

<!-- ==================================================== -->
<!-- CONFIRM DELETE MODAL                                -->
<!-- ==================================================== -->
<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow" style="border-radius: 10px;">
            <div class="modal-header bg-danger text-white py-2">
                <h5 class="modal-title text-white font-weight-bold mb-0">{{ __('Confirm Delete') }}</h5>
                <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body py-4 text-center">
                <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3 d-block"></i>
                <h5>{{ __('Are you sure you want to delete this transaction record?') }}</h5>
                <p class="text-muted small mb-0">{{ __('This action cannot be undone.') }}</p>
            </div>
            <div class="modal-footer py-2 bg-light d-flex justify-content-between">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">{{ __('Cancel') }}</button>
                <form id="deleteForm" action="" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm font-weight-bold">{{ __('Delete Permanently') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function openTxnModal(item) {
        document.getElementById('m_title').innerText = item.type_label + ' Details';
        document.getElementById('m_type_label').innerText = item.type_label;
        document.getElementById('m_amount').innerText = (item.currency_sign || 'PKR') + ' ' + Number(item.amount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        
        const statusBadge = document.getElementById('m_status_badge');
        statusBadge.innerText = item.status || 'Pending';
        const stLower = (item.status || '').toLowerCase();
        if (['paid', 'approved', 'completed', 'active'].includes(stLower)) {
            statusBadge.className = 'badge badge-success px-3 py-1 font-weight-bold';
        } else if (['pending', 'pending fine'].includes(stLower)) {
            statusBadge.className = 'badge badge-warning text-dark px-3 py-1 font-weight-bold';
        } else if (['rejected', 'canceled', 'unpaid'].includes(stLower)) {
            statusBadge.className = 'badge badge-danger px-3 py-1 font-weight-bold';
        } else {
            statusBadge.className = 'badge badge-secondary px-3 py-1 font-weight-bold';
        }

        document.getElementById('m_txnid').innerText = item.txn_id || '-';
        document.getElementById('m_reference').innerText = item.reference || '-';
        document.getElementById('m_method').innerText = item.payment_method || 'N/A';
        
        let dateStr = '-';
        if (item.created_at) {
            const d = new Date(item.created_at);
            dateStr = d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) + ' - ' + d.toLocaleTimeString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true });
        }
        document.getElementById('m_date').innerText = dateStr;

        const detailsWrap = document.getElementById('m_details_wrap');
        if (item.details) {
            detailsWrap.classList.remove('d-none');
            document.getElementById('m_details').innerText = item.details;
        } else {
            detailsWrap.classList.add('d-none');
        }

        document.getElementById('m_name').innerText = item.name || '-';
        document.getElementById('m_store').innerText = item.store_name || '-';
        document.getElementById('m_email').innerText = item.email || '-';
        document.getElementById('m_phone').innerText = item.phone || '-';

        const accWrap = document.getElementById('m_account_wrap');
        let accInfo = '';
        if (item.bank_name) accInfo += 'Bank: ' + item.bank_name + '\n';
        if (item.account_name) accInfo += 'Title: ' + item.account_name + '\n';
        if (item.account_number) accInfo += 'A/C No: ' + item.account_number;

        if (accInfo.trim()) {
            accWrap.classList.remove('d-none');
            document.getElementById('m_account_info').innerText = accInfo;
        } else {
            accWrap.classList.add('d-none');
        }

        const screenshotCard = document.getElementById('m_screenshot_card');
        if (item.screenshot_url) {
            screenshotCard.classList.remove('d-none');
            document.getElementById('m_screenshot_img').src = item.screenshot_url;
            document.getElementById('m_screenshot_link').href = item.screenshot_url;
        } else {
            screenshotCard.classList.add('d-none');
        }

        const directLink = document.getElementById('m_direct_link');
        const directLabel = document.getElementById('m_direct_label');
        if (item.direct_url) {
            directLink.classList.remove('d-none');
            directLink.href = item.direct_url;
            directLabel.innerText = item.direct_label || 'View Source';
        } else if (item.view_url) {
            directLink.classList.remove('d-none');
            directLink.href = item.view_url;
            directLabel.innerText = 'View Details Page';
        } else {
            directLink.classList.add('d-none');
        }

        $('#viewTxnDetailsModal').modal('show');
    }

    function previewScreenshot(url, title) {
        document.getElementById('previewModalImg').src = url;
        document.getElementById('previewModalTitle').innerHTML = '<i class="fas fa-image mr-2"></i> Payment Proof: ' + (title || '');
        document.getElementById('previewModalSubtitle').innerText = title || '';
        document.getElementById('previewModalDownload').href = url;
        $('#screenshotPreviewModal').modal('show');
    }

    function confirmDeleteTxn(url) {
        document.getElementById('deleteForm').action = url;
        $('#confirm-delete').modal('show');
    }
</script>
@endsection