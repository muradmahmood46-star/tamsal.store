@extends('master.back')

@section('styles')
<style>
    .store-table-wrapper {
        width: 100% !important;
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch;
        margin-bottom: 1rem;
        border: 1px solid #ebedf2;
        border-radius: 6px;
    }
    .store-table-wrapper::-webkit-scrollbar {
        height: 6px;
    }
    .store-table-wrapper::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    .store-table-wrapper::-webkit-scrollbar-thumb {
        background: #ced4da;
        border-radius: 4px;
    }
    .store-table-wrapper::-webkit-scrollbar-thumb:hover {
        background: #6c757d;
    }
    .store-table {
        min-width: 1050px;
        width: 100% !important;
        margin-bottom: 0 !important;
    }
    .store-table th {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        white-space: nowrap;
        background-color: #f8f9fa !important;
        vertical-align: middle !important;
        padding: 12px 10px !important;
        border-bottom: 2px solid #dee2e6 !important;
    }
    .store-table td {
        font-size: 13px;
        vertical-align: middle !important;
        padding: 10px 10px !important;
    }
    .action-btn-group {
        display: inline-flex !important;
        white-space: nowrap !important;
        gap: 4px;
    }
    .action-btn-group .btn {
        padding: 5px 9px !important;
        font-size: 12px !important;
        border-radius: 4px;
    }

    /* Store Details Modal Custom Styles */
    .store-details-modal .modal-content {
        border-radius: 12px;
        overflow: hidden;
    }
    .store-modal-header {
        padding: 14px 20px !important;
    }
    .store-modal-body {
        padding: 16px 20px !important;
        max-height: 75vh;
        overflow-y: auto;
    }
    .store-banner-box {
        max-height: 130px;
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 15px;
    }
    .store-banner-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .store-info-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 14px 16px;
        height: 100%;
    }
    .store-info-card-title {
        font-size: 13.5px;
        font-weight: 700;
        color: #1e40af;
        border-bottom: 1px solid #e2e8f0;
        padding-bottom: 8px;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
    }
    .store-info-list {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .store-info-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 4px 0;
        border-bottom: 1px dashed #e2e8f0;
        font-size: 12.5px;
        line-height: 1.4;
    }
    .store-info-row:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }
    .store-info-label {
        color: #64748b;
        font-weight: 600;
        flex: 0 0 38%;
        max-width: 38%;
        padding-right: 8px;
        word-break: break-word;
    }
    .store-info-value {
        color: #1e293b;
        flex: 0 0 62%;
        max-width: 62%;
        text-align: right;
        word-break: break-word;
    }
    .store-info-row.is-block {
        flex-direction: column;
        align-items: stretch;
    }
    .store-info-row.is-block .store-info-label {
        flex: 1 1 100%;
        max-width: 100%;
        margin-bottom: 2px;
    }
    .store-info-row.is-block .store-info-value {
        flex: 1 1 100%;
        max-width: 100%;
        text-align: left;
    }

    /* Mobile specific styling for store details modal */
    @media (max-width: 767.98px) {
        .store-details-modal .modal-dialog {
            margin: 8px auto !important;
            max-width: calc(100% - 16px) !important;
            width: calc(100% - 16px) !important;
        }
        .store-modal-header {
            padding: 10px 12px !important;
        }
        .store-modal-header .modal-title {
            font-size: 14.5px !important;
            line-height: 1.2;
        }
        .store-modal-header img {
            width: 36px !important;
            height: 36px !important;
            margin-right: 8px !important;
        }
        .store-modal-body {
            padding: 10px 8px !important;
            max-height: 78vh !important;
        }
        .store-banner-box {
            max-height: 75px !important;
            margin-bottom: 8px !important;
        }
        .store-info-card {
            padding: 10px !important;
            margin-bottom: 8px !important;
            border-radius: 8px !important;
        }
        .store-info-card-title {
            font-size: 12.5px !important;
            padding-bottom: 5px !important;
            margin-bottom: 6px !important;
        }
        .store-info-list {
            gap: 3px !important;
        }
        .store-info-row {
            padding: 3px 0 !important;
            font-size: 11.5px !important;
            line-height: 1.35 !important;
        }
        .store-info-label {
            font-size: 11.5px !important;
            flex: 0 0 38% !important;
            max-width: 38% !important;
            padding-right: 4px !important;
        }
        .store-info-value {
            font-size: 11.5px !important;
            flex: 0 0 62% !important;
            max-width: 62% !important;
        }
        .store-chat-card .card-header {
            padding: 7px 10px !important;
            font-size: 11.5px !important;
        }
        .store-chat-card .card-body {
            padding: 8px !important;
        }
        .store-chat-tools {
            gap: 3px !important;
            margin-bottom: 5px !important;
        }
        .store-chat-tools button {
            padding: 2px 6px !important;
            font-size: 10.5px !important;
        }
        .store-chat-tools small {
            display: none !important;
        }
        .store-modal-footer {
            padding: 8px 10px !important;
            flex-direction: column !important;
            gap: 6px !important;
        }
        .store-modal-footer > div {
            width: 100% !important;
            display: flex !important;
            justify-content: stretch !important;
            gap: 6px !important;
        }
        .store-modal-footer .btn {
            flex: 1 1 0 !important;
            font-size: 11px !important;
            padding: 6px 6px !important;
            white-space: nowrap !important;
            text-align: center !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between">
                <div>
                    <h3 class="mb-0 text-dark"><b><i class="fas fa-store-alt text-primary mr-2"></i> {{ __('All Stores (Marketplace Vendors)') }}</b></h3>
                    <p class="text-muted small mb-0">{{ __('View and manage all registered stores on the platform. Click "Login as Store" to instantly access any vendor panel.') }}</p>
                </div>
                <div class="mt-2 mt-sm-0">
                    <a href="{{ route('back.store_request.index') }}" class="btn btn-outline-primary btn-sm mr-2">
                        <i class="fas fa-file-signature mr-1"></i> {{ __('Store Requests') }}
                    </a>
                    <a href="{{ route('back.store_setting.index') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-cogs mr-1"></i> {{ __('Stores Rate & Setting') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    @include('alerts.alerts')

    <!-- Status Tabs & Filter -->
    <div class="row mb-3">
        <div class="col-md-7 mb-2 mb-md-0">
            <div class="btn-group flex-wrap" role="group">
                <a href="{{ route('back.stores.index') }}" class="btn btn-sm {{ ($status === null || $status === '') ? 'btn-primary' : 'btn-outline-primary' }}">
                    {{ __('All Stores') }} <span class="badge badge-light ml-1">{{ $counts['all'] }}</span>
                </a>
                <a href="{{ route('back.stores.index', ['status' => '1']) }}" class="btn btn-sm {{ $status === '1' ? 'btn-success font-weight-bold' : 'btn-outline-success' }}">
                    <i class="fas fa-check-circle mr-1"></i> {{ __('Active') }} <span class="badge badge-success ml-1">{{ $counts['active'] }}</span>
                </a>
                <a href="{{ route('back.stores.index', ['status' => '0']) }}" class="btn btn-sm {{ $status === '0' ? 'btn-danger font-weight-bold' : 'btn-outline-danger' }}">
                    <i class="fas fa-ban mr-1"></i> {{ __('Blocked') }} <span class="badge badge-danger ml-1">{{ $counts['blocked'] }}</span>
                </a>
            </div>
        </div>
        <div class="col-md-5">
            <form action="{{ route('back.stores.index') }}" method="GET" class="d-flex">
                @if($status !== null && $status !== '')
                    <input type="hidden" name="status" value="{{ $status }}">
                @endif
                <div class="input-group input-group-sm">
                    <input type="text" name="search" class="form-control" placeholder="{{ __('Search by store name, owner, email, phone...') }}" value="{{ $search ?? '' }}">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                        @if(!empty($search) || ($status !== null && $status !== ''))
                            <a href="{{ route('back.stores.index') }}" class="btn btn-secondary" title="{{ __('Clear Filter') }}"><i class="fas fa-times"></i></a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Stores Table -->
    <div class="card shadow-sm mb-4">
        <div class="card-body p-0">
            <div class="store-table-wrapper">
                <table class="table table-hover store-table table-bordered mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 50px;" class="text-center">#</th>
                            <th>{{ __('Store Info') }}</th>
                            <th>{{ __('Store Owner') }}</th>
                            <th>{{ __('Wallet Balance') }}</th>
                            <th class="text-center">{{ __('Products') }}</th>
                            <th class="text-center">{{ __('Orders') }}</th>
                            <th class="text-center">{{ __('Status') }}</th>
                            <th class="text-center" style="width: 170px;">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sellers as $seller)
                            @php
                                $owner = $seller->user;
                            @endphp
                            <tr>
                                <td class="text-center font-weight-bold">{{ $seller->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $seller->logoUrl() }}" alt="Logo" class="rounded mr-2 border shadow-sm" style="width: 45px; height: 45px; object-fit: cover; flex-shrink: 0;">
                                        <div>
                                            <div class="font-weight-bold text-dark" style="font-size: 14px;">{{ $seller->shop_name }}</div>
                                            <div class="text-muted small">
                                                <i class="fas fa-envelope mr-1 text-muted"></i> {{ $seller->shop_email ?: ($owner->email ?? 'N/A') }}
                                            </div>
                                            @if($seller->shop_phone)
                                                <div class="text-muted small">
                                                    <i class="fas fa-phone mr-1 text-muted"></i> {{ $seller->shop_phone }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($owner && !empty($owner->id))
                                        <div class="font-weight-bold text-dark">
                                            <a href="{{ route('back.user.show', $owner->id) }}" class="text-primary">
                                                {{ $owner->first_name }} {{ $owner->last_name }}
                                            </a>
                                        </div>
                                        <div class="small text-muted"><i class="fas fa-user-tag mr-1"></i> User ID: #{{ $owner->id }}</div>
                                        @if($owner->phone)
                                            <div class="small text-muted"><i class="fas fa-phone-alt mr-1"></i> {{ $owner->phone }}</div>
                                        @endif
                                    @else
                                        <span class="text-muted small">{{ __('No associated user') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="font-weight-bold text-success" style="font-size: 14px;">
                                        {{ PriceHelper::adminCurrency() }} {{ number_format($seller->balance ?? 0, 2) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-info font-weight-bold" style="font-size: 12px; padding: 4px 8px;">
                                        <i class="fab fa-product-hunt mr-1"></i> {{ $seller->total_products_count ?? 0 }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-primary font-weight-bold" style="font-size: 12px; padding: 4px 8px;">
                                        <i class="fas fa-shopping-cart mr-1"></i> {{ $seller->total_orders_count ?? 0 }}
                                    </span>
                                    @if(isset($seller->pending_orders_count) && $seller->pending_orders_count > 0)
                                        <div class="small text-warning font-weight-bold mt-1">
                                            <i class="fas fa-clock mr-1"></i> {{ $seller->pending_orders_count }} {{ __('pending') }}
                                        </div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($seller->status == 1 && (!$owner || $owner->is_seller_blocked == 0))
                                        <span class="badge badge-success font-weight-bold" style="padding: 5px 10px; font-size: 11px;">
                                            <i class="fas fa-check-circle mr-1"></i> {{ __('Active') }}
                                        </span>
                                    @else
                                        <span class="badge badge-danger font-weight-bold" style="padding: 5px 10px; font-size: 11px;">
                                            <i class="fas fa-ban mr-1"></i> {{ __('Blocked') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="action-btn-group">
                                        <!-- View Store Details Modal -->
                                        <button type="button" 
                                                class="btn btn-info btn-sm font-weight-bold shadow-sm" 
                                                data-toggle="modal" 
                                                data-target="#storeDetailsModal{{ $seller->id }}" 
                                                title="{{ __('View Store Details') }}">
                                            <i class="fas fa-eye mr-1"></i> {{ __('View') }}
                                        </button>

                                        <!-- Login As Store (Impersonate) -->
                                        <a href="{{ route('back.stores.loginAs', $seller->id) }}" 
                                           target="_blank"
                                           class="btn btn-success btn-sm font-weight-bold shadow-sm" 
                                           title="{{ __('Login as Store (Open Vendor Panel)') }}"
                                           onclick="return confirm('Open vendor panel and login as {{ addslashes($seller->shop_name) }}?');">
                                            <i class="fas fa-sign-in-alt mr-1"></i> {{ __('Login') }}
                                        </a>

                                        <!-- Toggle Status & Impose Fine Buttons -->
                                        @if($seller->status == 1)
                                            <button type="button" 
                                                    class="btn btn-outline-danger btn-sm font-weight-bold" 
                                                    data-toggle="modal" 
                                                    data-target="#blockOrFineModal{{ $seller->id }}" 
                                                    title="{{ __('Block Store or Impose Fine') }}">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        @else
                                            <form action="{{ route('back.stores.status', ['id' => $seller->id, 'status' => 1]) }}" method="POST" class="d-inline" onsubmit="return confirm('Activate and unblock this store?');">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-success btn-sm font-weight-bold" title="{{ __('Activate Store') }}">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <button type="button" 
                                                    class="btn btn-outline-warning btn-sm font-weight-bold" 
                                                    data-toggle="modal" 
                                                    data-target="#blockOrFineModal{{ $seller->id }}" 
                                                    title="{{ __('Impose / Edit Fine') }}">
                                                <i class="fas fa-coins"></i>
                                            </button>
                                        @endif

                                        <!-- Delete Store -->
                                        <form action="{{ route('back.stores.destroy', $seller->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to permanently delete store &quot;{{ addslashes($seller->shop_name) }}&quot;?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm" title="{{ __('Delete Store') }}">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>

                                    <!-- Store Details Modal -->
                                    <div class="modal fade store-details-modal" id="storeDetailsModal{{ $seller->id }}" tabindex="-1" role="dialog" aria-labelledby="storeDetailsModalLabel{{ $seller->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-centered text-left" role="document">
                                            <div class="modal-content border-0 shadow-lg">
                                                <div class="modal-header bg-primary text-white store-modal-header">
                                                    <div class="d-flex align-items-center">
                                                        <img src="{{ $seller->logoUrl() }}" alt="Logo" class="rounded-circle mr-3 border bg-white shadow-sm" style="width: 48px; height: 48px; object-fit: cover; flex-shrink: 0;">
                                                        <div>
                                                            <h5 class="modal-title font-weight-bold text-white mb-0" id="storeDetailsModalLabel{{ $seller->id }}">
                                                                {{ $seller->shop_name }}
                                                            </h5>
                                                            <small class="text-white-50">
                                                                {{ __('Store ID') }}: #{{ $seller->id }} &bull; 
                                                                @if($seller->status == 1)
                                                                    <span class="badge badge-success">{{ __('Active') }}</span>
                                                                @else
                                                                    <span class="badge badge-danger">{{ __('Blocked') }}</span>
                                                                @endif
                                                            </small>
                                                        </div>
                                                    </div>
                                                    <button type="button" class="close text-white opacity-1" data-dismiss="modal" aria-label="Close" style="opacity: 0.9;">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body store-modal-body">
                                                    @if($seller->bannerUrl())
                                                        <div class="store-banner-box shadow-sm">
                                                            <img src="{{ $seller->bannerUrl() }}" alt="Banner">
                                                        </div>
                                                    @endif

                                                    <div class="row">
                                                        <!-- Left: Store Information -->
                                                        <div class="col-md-6 mb-3">
                                                            <div class="store-info-card">
                                                                <div class="store-info-card-title">
                                                                    <i class="fas fa-store mr-2"></i> {{ __('Store Details') }}
                                                                </div>
                                                                <div class="store-info-list">
                                                                    <div class="store-info-row">
                                                                        <div class="store-info-label">{{ __('Shop Name') }}:</div>
                                                                        <div class="store-info-value font-weight-bold text-dark">{{ $seller->shop_name }}</div>
                                                                    </div>
                                                                    <div class="store-info-row">
                                                                        <div class="store-info-label">{{ __('Shop Email') }}:</div>
                                                                        <div class="store-info-value">{{ $seller->shop_email ?: 'N/A' }}</div>
                                                                    </div>
                                                                    <div class="store-info-row">
                                                                        <div class="store-info-label">{{ __('Shop Phone') }}:</div>
                                                                        <div class="store-info-value">{{ $seller->shop_phone ?: 'N/A' }}</div>
                                                                    </div>
                                                                    <div class="store-info-row">
                                                                        <div class="store-info-label">{{ __('Courier Company') }}:</div>
                                                                        <div class="store-info-value">{{ $seller->courier_company ?: 'N/A' }}</div>
                                                                    </div>
                                                                    <div class="store-info-row">
                                                                        <div class="store-info-label">{{ __('Product Types') }}:</div>
                                                                        <div class="store-info-value">{{ $seller->product_types ?: 'N/A' }}</div>
                                                                    </div>
                                                                    <div class="store-info-row is-block">
                                                                        <div class="store-info-label">{{ __('Address') }}:</div>
                                                                        <div class="store-info-value">{{ $seller->shop_address ?: 'N/A' }}</div>
                                                                    </div>
                                                                    @if($seller->shop_details)
                                                                        <div class="store-info-row is-block">
                                                                            <div class="store-info-label">{{ __('About Store') }}:</div>
                                                                            <div class="store-info-value">{{ $seller->shop_details }}</div>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Right: Owner & Performance Stats -->
                                                        <div class="col-md-6 mb-3">
                                                            <div class="store-info-card">
                                                                <div class="store-info-card-title">
                                                                    <i class="fas fa-user-circle mr-2"></i> {{ __('Owner & Statistics') }}
                                                                </div>
                                                                <div class="store-info-list">
                                                                    <div class="store-info-row">
                                                                        <div class="store-info-label">{{ __('Owner Name') }}:</div>
                                                                        <div class="store-info-value">
                                                                            @if($owner && !empty($owner->id))
                                                                                <a href="{{ route('back.user.show', $owner->id) }}" class="font-weight-bold text-primary" target="_blank">
                                                                                    {{ $owner->first_name }} {{ $owner->last_name }} <i class="fas fa-external-link-alt small ml-1"></i>
                                                                                </a>
                                                                            @else
                                                                                <span class="text-muted">N/A</span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                    <div class="store-info-row">
                                                                        <div class="store-info-label">{{ __('User ID') }}:</div>
                                                                        <div class="store-info-value text-dark">{{ $owner ? ('#' . $owner->id) : 'N/A' }}</div>
                                                                    </div>
                                                                    <div class="store-info-row">
                                                                        <div class="store-info-label">{{ __('Owner Email') }}:</div>
                                                                        <div class="store-info-value">{{ $owner->email ?? 'N/A' }}</div>
                                                                    </div>
                                                                    <div class="store-info-row">
                                                                        <div class="store-info-label">{{ __('Owner Phone') }}:</div>
                                                                        <div class="store-info-value">{{ $owner->phone ?? 'N/A' }}</div>
                                                                    </div>
                                                                    <div class="store-info-row">
                                                                        <div class="store-info-label">{{ __('Wallet Balance') }}:</div>
                                                                        <div class="store-info-value">
                                                                            <span class="badge badge-success font-weight-bold" style="font-size: 12px; padding: 3px 8px;">
                                                                                {{ PriceHelper::adminCurrency() }} {{ number_format($seller->balance ?? 0, 2) }}
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                    @if($seller->unblockRequest && $seller->unblockRequest->fine_amount > 0)
                                                                        <div class="store-info-row">
                                                                            <div class="store-info-label">{{ __('Fine Status') }}:</div>
                                                                            <div class="store-info-value">
                                                                                <span class="badge badge-warning font-weight-bold" style="font-size: 11px; padding: 3px 6px;">
                                                                                    <i class="fas fa-gavel mr-1"></i> {{ PriceHelper::adminCurrency() }} {{ number_format($seller->unblockRequest->fine_amount, 2) }} ({{ ucfirst($seller->unblockRequest->fine_status ?: 'pending') }})
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                    <div class="store-info-row">
                                                                        <div class="store-info-label">{{ __('Total Products') }}:</div>
                                                                        <div class="store-info-value font-weight-bold text-dark">{{ $seller->total_products_count ?? 0 }}</div>
                                                                    </div>
                                                                    <div class="store-info-row">
                                                                        <div class="store-info-label">{{ __('Total Orders') }}:</div>
                                                                        <div class="store-info-value font-weight-bold text-dark">
                                                                            {{ $seller->total_orders_count ?? 0 }}
                                                                            @if(isset($seller->pending_orders_count) && $seller->pending_orders_count > 0)
                                                                                <span class="badge badge-warning ml-1" style="font-size: 10px; padding: 2px 6px;">{{ $seller->pending_orders_count }} {{ __('pending') }}</span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                    <div class="store-info-row">
                                                                        <div class="store-info-label">{{ __('Registered On') }}:</div>
                                                                        <div class="store-info-value">{{ $seller->created_at ? $seller->created_at->format('M d, Y - h:i A') : 'N/A' }}</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Direct Message / Chat with Vendor -->
                                                    <div class="card border mt-2 shadow-sm store-chat-card" style="border-radius: 8px; border-color: #cbd5e1 !important;">
                                                        <div class="card-header bg-dark text-white py-2 d-flex justify-content-between align-items-center" style="border-radius: 7px 7px 0 0;">
                                                            <span class="font-weight-bold" style="font-size: 13px;">
                                                                <i class="fas fa-paper-plane mr-1 text-info"></i> {{ __('Direct Message / Chat with Vendor') }}
                                                            </span>
                                                            <span class="badge badge-info small">{{ __('Direct Line') }}</span>
                                                        </div>
                                                        <div class="card-body p-3 bg-white">
                                                            <!-- History of messages -->
                                                            <div id="modalChatHistory{{ $seller->id }}" class="p-2 mb-2 bg-light border rounded" style="max-height: 180px; overflow-y: auto; font-size: 12.5px; display: none;"></div>

                                                            <!-- Formatting Tools -->
                                                            <div class="d-flex align-items-center mb-1 flex-wrap store-chat-tools" style="gap: 5px;">
                                                                <button type="button" class="btn btn-light btn-xs border px-2 py-0" style="font-size: 12px; border-radius: 4px;" onclick="insertMsgFormat('modalStoreMessageInput{{ $seller->id }}', '**', '**')" title="{{ __('Bold (**text**)') }}"><b>B</b></button>
                                                                <button type="button" class="btn btn-light btn-xs border px-2 py-0 font-italic" style="font-size: 12px; border-radius: 4px;" onclick="insertMsgFormat('modalStoreMessageInput{{ $seller->id }}', '_', '_')" title="{{ __('Italic (_text_)') }}"><i>I</i></button>
                                                                <button type="button" class="btn btn-light btn-xs border px-2 py-0" style="font-size: 12px; border-radius: 4px;" onclick="insertMsgFormat('modalStoreMessageInput{{ $seller->id }}', '<u>', '</u>')" title="{{ __('Underline (<u>text</u>)') }}"><u>U</u></button>
                                                                <button type="button" class="btn btn-light btn-xs border px-2 py-0" style="font-size: 12px; border-radius: 4px;" onclick="insertMsgFormat('modalStoreMessageInput{{ $seller->id }}', '\n• ', '')" title="{{ __('Bullet Point') }}"><i class="fas fa-list-ul"></i></button>
                                                                <small class="text-muted ml-auto" style="font-size: 11px;">
                                                                    <i class="fas fa-info-circle mr-1"></i>{{ __('Line gaps & formatting preserved') }}
                                                                </small>
                                                            </div>

                                                            <div class="d-flex flex-column">
                                                                <textarea id="modalStoreMessageInput{{ $seller->id }}" class="form-control" rows="2" placeholder="{{ __('Type direct message to :store...', ['store' => $seller->shop_name]) }}" style="font-size: 12.5px; line-height: 1.4; border-radius: 6px; resize: none; min-height: 42px; max-height: 120px;" onkeydown="handleStoreMsgKey(event, {{ $seller->id }})" oninput="autoExpandTextarea(this)"></textarea>
                                                                <div class="d-flex justify-content-between align-items-center mt-2">
                                                                    <small class="text-muted" style="font-size: 11px;">{{ __('Shift+Enter for newline') }}</small>
                                                                    <button type="button" class="btn btn-primary font-weight-bold px-3 py-1 btn-sm" id="modalSendStoreMsgBtn{{ $seller->id }}" onclick="sendDirectStoreMessage({{ $seller->id }})">
                                                                        <i class="fas fa-paper-plane mr-1"></i> {{ __('Send Message') }}
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            <div id="modalMsgSuccessAlert{{ $seller->id }}" class="alert alert-success mt-2 py-1 px-2 small d-none"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer bg-light py-2 px-3 store-modal-footer justify-content-between">
                                                    <div>
                                                        @if($seller->user_id)
                                                            <a href="{{ $seller->getStoreUrl() }}" target="_blank" class="btn btn-outline-primary btn-sm font-weight-bold">
                                                                <i class="fas fa-external-link-alt mr-1"></i> {{ __('Public Storefront') }}
                                                            </a>
                                                        @endif
                                                    </div>
                                                    <div class="d-flex align-items-center flex-wrap" style="gap: 4px;">
                                                        <a href="{{ route('back.stores.loginAs', $seller->id) }}" target="_blank" class="btn btn-success btn-sm font-weight-bold mr-1" onclick="return confirm('Open vendor panel and login as {{ addslashes($seller->shop_name) }}?');">
                                                            <i class="fas fa-sign-in-alt mr-1"></i> {{ __('Login as Store') }}
                                                        </a>
                                                        @if($seller->status == 1)
                                                            <button type="button" class="btn btn-danger btn-sm font-weight-bold mr-1" data-toggle="modal" data-target="#blockOrFineModal{{ $seller->id }}">
                                                                <i class="fas fa-ban mr-1"></i> {{ __('Block / Fine') }}
                                                            </button>
                                                        @else
                                                            <form action="{{ route('back.stores.status', ['id' => $seller->id, 'status' => 1]) }}" method="POST" class="d-inline mr-1" onsubmit="return confirm('Activate and unblock this store?');">
                                                                @csrf
                                                                <button type="submit" class="btn btn-success btn-sm font-weight-bold">
                                                                    <i class="fas fa-check mr-1"></i> {{ __('Activate') }}
                                                                </button>
                                                            </form>
                                                            <button type="button" class="btn btn-warning btn-sm font-weight-bold text-dark mr-1" data-toggle="modal" data-target="#blockOrFineModal{{ $seller->id }}">
                                                                <i class="fas fa-coins mr-1"></i> {{ __('Impose / Edit Fine') }}
                                                            </button>
                                                        @endif
                                                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">{{ __('Close') }}</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Block & Impose Fine Modal -->
                                    <div class="modal fade" id="blockOrFineModal{{ $seller->id }}" tabindex="-1" role="dialog" aria-labelledby="blockOrFineModalLabel{{ $seller->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered text-left" role="document" style="max-width: 520px;">
                                            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
                                                <div class="modal-header bg-dark text-white py-3 px-4">
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-shield-alt fa-lg text-warning mr-2"></i>
                                                        <div>
                                                            <h5 class="modal-title font-weight-bold text-white mb-0" id="blockOrFineModalLabel{{ $seller->id }}">
                                                                {{ __('Block / Fine Store') }}
                                                            </h5>
                                                            <small class="text-white-50">{{ $seller->shop_name }} &bull; #{{ $seller->id }}</small>
                                                        </div>
                                                    </div>
                                                    <button type="button" class="close text-white opacity-1" data-dismiss="modal" aria-label="Close" style="opacity: 0.9;">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <!-- Store Overview Banner -->
                                                    <div class="d-flex align-items-center p-2 mb-3 bg-light rounded border">
                                                        <img src="{{ $seller->logoUrl() }}" alt="Logo" class="rounded-circle mr-3 border bg-white shadow-sm" style="width: 44px; height: 44px; object-fit: cover; flex-shrink: 0;">
                                                        <div class="overflow-hidden">
                                                            <div class="font-weight-bold text-dark text-truncate" style="font-size: 14px;">{{ $seller->shop_name }}</div>
                                                            <small class="text-muted d-block text-truncate">
                                                                {{ __('Owner') }}: {{ $owner ? ($owner->first_name . ' ' . $owner->last_name) : 'N/A' }} &bull;
                                                                @if($seller->status == 1)
                                                                    <span class="badge badge-success">{{ __('Active') }}</span>
                                                                @else
                                                                    <span class="badge badge-danger">{{ __('Blocked') }}</span>
                                                                @endif
                                                            </small>
                                                        </div>
                                                    </div>

                                                    <!-- Option Tabs -->
                                                    <ul class="nav nav-pills nav-fill mb-3 bg-light p-1 rounded" role="tablist">
                                                        <li class="nav-item">
                                                            <a class="nav-link active font-weight-bold py-2" id="tab-fine-{{ $seller->id }}" data-toggle="pill" href="#pane-fine-{{ $seller->id }}" role="tab" style="font-size: 13px;">
                                                                <i class="fas fa-gavel text-warning mr-1"></i> {{ __('Block & Impose Fine') }}
                                                            </a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a class="nav-link font-weight-bold py-2" id="tab-block-{{ $seller->id }}" data-toggle="pill" href="#pane-block-{{ $seller->id }}" role="tab" style="font-size: 13px;">
                                                                <i class="fas fa-ban text-danger mr-1"></i> {{ __('Block Store Only') }}
                                                            </a>
                                                        </li>
                                                    </ul>

                                                    <div class="tab-content">
                                                        <!-- Option 1: Block & Impose Fine -->
                                                        <div class="tab-pane fade show active" id="pane-fine-{{ $seller->id }}" role="tabpanel">
                                                            <form action="{{ route('back.stores.fine', $seller->id) }}" method="POST">
                                                                @csrf
                                                                <div class="alert alert-warning py-2 px-3 small mb-3" style="font-size: 12px; line-height: 1.4;">
                                                                    <i class="fas fa-info-circle mr-1"></i> {{ __('Store will be blocked and products hidden. The vendor must pay the specified fine to request unblocking.') }}
                                                                </div>

                                                                <div class="form-group mb-3">
                                                                    <label class="font-weight-bold text-dark mb-1" style="font-size: 13px;">
                                                                        {{ __('Fine Amount') }} ({{ PriceHelper::adminCurrency() }}) <span class="text-danger">*</span>
                                                                    </label>
                                                                    <div class="input-group">
                                                                        <div class="input-group-prepend">
                                                                            <span class="input-group-text font-weight-bold bg-warning text-dark border-0">{{ PriceHelper::adminCurrency() }}</span>
                                                                        </div>
                                                                        <input type="number" step="0.01" min="1" name="fine_amount" class="form-control form-control-lg font-weight-bold text-danger" placeholder="{{ __('Enter amount (e.g. 1000)') }}" value="{{ ($seller->unblockRequest && $seller->unblockRequest->fine_amount > 0) ? $seller->unblockRequest->fine_amount : '' }}" required>
                                                                    </div>
                                                                </div>

                                                                <div class="form-group mb-3">
                                                                    <label class="font-weight-bold text-dark mb-1" style="font-size: 13px;">
                                                                        {{ __('Reason / Violation Note (Optional)') }}
                                                                    </label>
                                                                    <textarea name="reason" rows="2" class="form-control" placeholder="{{ __('e.g. Delayed orders dispatch, policy violation...') }}" style="font-size: 12.5px;">{{ $seller->unblockRequest ? $seller->unblockRequest->admin_reply : '' }}</textarea>
                                                                </div>

                                                                <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                                                                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">{{ __('Cancel') }}</button>
                                                                    <button type="submit" class="btn btn-warning font-weight-bold text-dark px-3 py-2 shadow-sm">
                                                                        <i class="fas fa-check-circle mr-1"></i> {{ __('Done / Impose Fine') }}
                                                                    </button>
                                                                </div>
                                                            </form>
                                                        </div>

                                                        <!-- Option 2: Block Store Only -->
                                                        <div class="tab-pane fade" id="pane-block-{{ $seller->id }}" role="tabpanel">
                                                            <form action="{{ route('back.stores.status', ['id' => $seller->id, 'status' => 0]) }}" method="POST">
                                                                @csrf
                                                                <div class="alert alert-danger py-2 px-3 small mb-3" style="font-size: 12px; line-height: 1.4;">
                                                                    <i class="fas fa-exclamation-triangle mr-1"></i> {{ __('This will block the store and hide its products from the marketplace without requiring any penalty fine.') }}
                                                                </div>

                                                                <div class="form-group mb-3">
                                                                    <label class="font-weight-bold text-dark mb-1" style="font-size: 13px;">
                                                                        {{ __('Block Reason (Optional)') }}
                                                                    </label>
                                                                    <textarea name="reason" rows="2" class="form-control" placeholder="{{ __('e.g. Temporary suspension under review...') }}" style="font-size: 12.5px;"></textarea>
                                                                </div>

                                                                <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                                                                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">{{ __('Cancel') }}</button>
                                                                    <button type="submit" class="btn btn-danger font-weight-bold px-3 py-2 shadow-sm" onclick="return confirm('Are you sure you want to block this store?');">
                                                                        <i class="fas fa-ban mr-1"></i> {{ __('Block Store Only') }}
                                                                    </button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="fas fa-store-slash fa-3x mb-3 text-muted"></i>
                                    <p class="mb-0">{{ __('No stores found matching your criteria.') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($sellers->hasPages())
            <div class="card-footer py-2">
                {{ $sellers->appends(request()->input())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // When any store details modal opens, load chat history
        $('.modal[id^="storeDetailsModal"]').on('shown.bs.modal', function() {
            const modalId = $(this).attr('id');
            const sellerId = modalId.replace('storeDetailsModal', '');
            if (sellerId) {
                loadStoreMessages(sellerId);
            }
        });
    });

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

    function loadStoreMessages(id) {
        const historyBox = $('#modalChatHistory' + id);
        historyBox.html('<div class="text-center text-muted small py-2"><i class="fas fa-spinner fa-spin mr-1"></i> {{ __("Loading message history...") }}</div>').show();

        fetch("{{ url('admin/stores/messages') }}/" + id, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(res => {
            if (res.success && res.messages && res.messages.length > 0) {
                let html = '';
                res.messages.forEach(m => {
                    const isAdmin = m.is_admin;
                    html += `
                        <div class="p-2 mb-2 rounded ${isAdmin ? 'bg-white border text-right' : 'bg-info-light border text-left'}" style="background: ${isAdmin ? '#eff6ff' : '#f0fdf4'}; border-color: ${isAdmin ? '#bfdbfe' : '#bbf7d0'} !important;">
                            <div class="font-weight-bold" style="font-size: 11px; color: ${isAdmin ? '#1e40af' : '#15803d'};">
                                <i class="fas ${isAdmin ? 'fa-user-shield' : 'fa-store'} mr-1"></i>
                                ${isAdmin ? '{{ __("Admin") }}' : '{{ __("Vendor") }}'} • <span class="text-muted font-weight-normal">${m.time} (${m.date})</span>
                            </div>
                            <div class="text-dark mt-1" style="font-size: 13px; white-space: pre-wrap; word-break: break-word; line-height: 1.55; font-family: inherit; text-align: left;">${formatChatMessage(m.message)}</div>
                        </div>
                    `;
                });
                historyBox.html(html).show();
                historyBox.scrollTop(historyBox[0].scrollHeight);
            } else {
                historyBox.html('<div class="text-center text-muted small py-2">{{ __("No messages exchanged yet with this vendor. Send a direct message below.") }}</div>').show();
            }
        })
        .catch(e => {
            historyBox.html('<div class="text-center text-muted small py-2">{{ __("No previous messages.") }}</div>').show();
        });
    }

    function sendDirectStoreMessage(id) {
        const input = $('#modalStoreMessageInput' + id);
        const text = input.val().trim();
        if (!text) return;

        const btn = $('#modalSendStoreMsgBtn' + id);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> {{ __("Sending...") }}');

        fetch("{{ url('admin/stores/send-message') }}/" + id, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ message: text })
        })
        .then(r => r.json())
        .then(res => {
            btn.prop('disabled', false).html('<i class="fas fa-paper-plane mr-1"></i> {{ __("Send Message") }}');
            if (res.success) {
                input.val('');
                input.css('height', 'auto');
                $('#modalMsgSuccessAlert' + id).removeClass('d-none').text(res.message);
                setTimeout(() => {
                    $('#modalMsgSuccessAlert' + id).addClass('d-none');
                }, 4000);
                loadStoreMessages(id);
            } else {
                alert(res.message || '{{ __("Failed to send message.") }}');
            }
        })
        .catch(err => {
            btn.prop('disabled', false).html('<i class="fas fa-paper-plane mr-1"></i> {{ __("Send Message") }}');
            alert('{{ __("Error sending message.") }}');
        });
    }

    function handleStoreMsgKey(e, id) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendDirectStoreMessage(id);
        }
    }
</script>
@endsection