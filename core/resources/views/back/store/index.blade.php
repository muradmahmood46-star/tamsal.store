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
                                    @if($owner)
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
                                        <!-- Login As Store (Impersonate) -->
                                        <a href="{{ route('back.stores.loginAs', $seller->id) }}" 
                                           target="_blank"
                                           class="btn btn-success btn-sm font-weight-bold shadow-sm" 
                                           title="{{ __('Login as Store (Open Vendor Panel)') }}"
                                           onclick="return confirm('Open vendor panel and login as {{ addslashes($seller->shop_name) }}?');">
                                            <i class="fas fa-sign-in-alt mr-1"></i> {{ __('Login as Store') }}
                                        </a>

                                        <!-- Toggle Status (Block/Unblock) -->
                                        @if($seller->status == 1)
                                            <form action="{{ route('back.stores.status', ['id' => $seller->id, 'status' => 0]) }}" method="POST" class="d-inline" onsubmit="return confirm('Block this store? Products will be hidden.');">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-danger btn-sm" title="{{ __('Block Store') }}">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('back.stores.status', ['id' => $seller->id, 'status' => 1]) }}" method="POST" class="d-inline" onsubmit="return confirm('Activate and unblock this store?');">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-success btn-sm" title="{{ __('Activate Store') }}">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        @endif
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