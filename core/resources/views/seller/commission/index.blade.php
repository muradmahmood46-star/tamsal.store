@extends('master.seller')

@section('content')

<div class="container-fluid">

    <!-- Page Heading & Breadcrumbs -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between">
                <div>
                    <h3 class="mb-0 font-weight-bold text-dark">
                        <i class="fas fa-percentage text-primary mr-2"></i>{{ __('Order Commission') }}
                    </h3>
                    <p class="text-muted mb-0" style="font-size: 13.5px;">
                        {{ __('Per-Order Sales Commission Overview & Deduction History') }}
                    </p>
                </div>
                <div class="mt-2 mt-sm-0 d-flex align-items-center" style="gap: 8px;">
                    <a href="{{ route('seller.wallet.index') }}" class="btn btn-outline-success btn-sm font-weight-bold">
                        <i class="fas fa-wallet mr-1"></i> {{ __('Wallet & Deposit') }}
                    </a>
                    <ol class="breadcrumb mb-0 bg-transparent p-0 d-none d-md-flex">
                        <li class="breadcrumb-item"><a href="{{ route('seller.dashboard') }}">{{ __('Dashboard') }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ __('Commission') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    @include('alerts.alerts')

    <!-- Pending / Locked Orders Alert -->
    @if($pendingOrdersCount > 0)
        <div class="alert alert-danger shadow-sm border-0 mb-4 p-3 rounded" style="border-left: 5px solid #dc3545 !important;">
            <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between" style="gap: 12px;">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-danger text-white mr-3 flex-shrink-0" style="width: 44px; height: 44px; font-size: 18px;">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div>
                        <h6 class="font-weight-bold text-danger mb-1">
                            {{ __(':count Order(s) Locked Due to Insufficient Wallet Balance!', ['count' => $pendingOrdersCount]) }}
                        </h6>
                        <p class="mb-0 text-dark small" style="line-height: 1.45;">
                            {{ __('Total required commission is :curr :amount. Please top up your wallet to auto-deduct commission and unlock your orders for processing.', [
                                'curr' => PriceHelper::adminCurrency(),
                                'amount' => number_format($pendingCommissionAmount, 2)
                            ]) }}
                        </p>
                    </div>
                </div>
                <div class="w-100 w-md-auto text-right">
                    <a href="{{ route('seller.wallet.index') }}" class="btn btn-danger font-weight-bold btn-sm px-3 py-2 shadow-sm">
                        <i class="fas fa-plus-circle mr-1"></i> {{ __('Top Up Wallet Now') }}
                    </a>
                </div>
            </div>
        </div>
    @endif

    <!-- Top Metric Cards -->
    <style>
        /* Metric Cards Overall & Compact Height Equalization */
        .comm-metric-card {
            border-radius: 8px !important;
            transition: transform 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        .comm-metric-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 .25rem .75rem rgba(0,0,0,.1) !important;
        }
        .comm-metric-body {
            min-height: 82px !important;
            height: 100% !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: space-between !important;
            padding: 8px 10px !important;
            box-sizing: border-box !important;
        }

        /* Fixed Row Header (Title on left, circular icon strictly on right) */
        .comm-card-header {
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            justify-content: space-between !important;
            width: 100% !important;
            margin-bottom: 3px !important;
            gap: 4px !important;
            box-sizing: border-box !important;
        }
        .comm-card-title {
            font-size: 10px !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.3px !important;
            line-height: 1.2 !important;
            flex: 1 1 auto !important;
            min-width: 0 !important;
            margin: 0 !important;
            white-space: normal !important;
            word-break: normal !important;
        }

        /* Compact Circular Icon Badge */
        .comm-stat-icon-wrap {
            width: 24px !important;
            height: 24px !important;
            min-width: 24px !important;
            max-width: 24px !important;
            min-height: 24px !important;
            max-height: 24px !important;
            border-radius: 50% !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            flex-shrink: 0 !important;
            flex-grow: 0 !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12) !important;
            padding: 0 !important;
            margin: 0 !important;
            overflow: hidden !important;
        }
        .comm-stat-icon-wrap i {
            font-size: 11px !important;
            line-height: 1 !important;
            display: inline-block !important;
            text-align: center !important;
        }

        .comm-card-value {
            font-size: 13px !important;
            font-weight: 700 !important;
            margin-bottom: 1px !important;
            line-height: 1.2 !important;
        }
        .comm-card-subtext {
            font-size: 9.5px !important;
            line-height: 1.2 !important;
            color: #6c757d !important;
            display: block !important;
            margin-top: 3px !important;
        }

        .comm-table-wrap {
            width: 100% !important;
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch;
        }
        .comm-table {
            min-width: 900px;
            margin-bottom: 0 !important;
        }
        .comm-table th {
            font-size: 11.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            white-space: nowrap;
            background-color: #f8fafc !important;
            vertical-align: middle !important;
            padding: 12px 10px !important;
            border-bottom: 2px solid #e2e8f0 !important;
            color: #475569;
        }
        .comm-table td {
            font-size: 13px;
            vertical-align: middle !important;
            padding: 10px 10px !important;
            color: #1e293b;
        }

        @media (max-width: 575.98px) {
            .comm-metric-body {
                padding: 7px 8px !important;
                min-height: 76px !important;
            }
            .comm-card-title {
                font-size: 9.5px !important;
                letter-spacing: 0.1px !important;
            }
            .comm-stat-icon-wrap {
                width: 22px !important;
                height: 22px !important;
                min-width: 22px !important;
                max-width: 22px !important;
                min-height: 22px !important;
                max-height: 22px !important;
            }
            .comm-stat-icon-wrap i {
                font-size: 10px !important;
            }
            .comm-card-value {
                font-size: 12.5px !important;
            }
            .comm-card-subtext {
                font-size: 9px !important;
            }
        }
    </style>

    <div class="row mb-3">
        <!-- 1. Commission Rate Card (Mobile Row 1 Left) -->
        <div class="col-6 col-md-6 col-xl-3 mb-2">
            <div class="card comm-metric-card border-left-primary shadow-sm h-100" style="border-left: 3.5px solid #0d6efd !important;">
                <div class="card-body comm-metric-body">
                    <div class="comm-card-header">
                        <div class="comm-card-title text-primary">
                            {{ __('Sales Commission Rate') }}
                        </div>
                        <div class="comm-stat-icon-wrap bg-primary text-white">
                            <i class="fas fa-percent"></i>
                        </div>
                    </div>
                    <div>
                        <div class="comm-card-value text-gray-800">
                            {{ number_format($commissionPercent, 1) }}%
                        </div>
                        <small class="comm-card-subtext">
                            {{ __('Cut per order on marketplace sales') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Total Commission Paid (Mobile Row 1 Right) -->
        <div class="col-6 col-md-6 col-xl-3 mb-2">
            <div class="card comm-metric-card border-left-danger shadow-sm h-100" style="border-left: 3.5px solid #e11d48 !important;">
                <div class="card-body comm-metric-body">
                    <div class="comm-card-header">
                        <div class="comm-card-title text-danger">
                            {{ __('Total Commission Paid') }}
                        </div>
                        <div class="comm-stat-icon-wrap bg-danger text-white">
                            <i class="fas fa-coins"></i>
                        </div>
                    </div>
                    <div>
                        <div class="comm-card-value text-gray-800">
                            {{ PriceHelper::adminCurrency() }} {{ number_format($totalCommissionPaid, 2) }}
                        </div>
                        <small class="comm-card-subtext">
                            {{ __(':count order(s) processed with cut', ['count' => $deductedOrdersCount]) }}
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Free Orders Allowance (Mobile Row 2 Left) -->
        <div class="col-6 col-md-6 col-xl-3 mb-2">
            <div class="card comm-metric-card border-left-success shadow-sm h-100" style="border-left: 3.5px solid #10b981 !important;">
                <div class="card-body comm-metric-body">
                    <div class="comm-card-header">
                        <div class="comm-card-title text-success">
                            {{ __('Free Orders Allowance') }}
                        </div>
                        <div class="comm-stat-icon-wrap bg-success text-white">
                            <i class="fas fa-gift"></i>
                        </div>
                    </div>
                    <div>
                        <div class="comm-card-value text-gray-800">
                            {{ $freeOrdersCount }} / {{ $freeOrdersLimit }}
                        </div>
                        <small class="comm-card-subtext">
                            @if($remainingFreeOrders > 0)
                                <span class="text-success font-weight-bold">{{ $remainingFreeOrders }} {{ __('free order(s) left') }}</span>
                            @else
                                <span class="text-muted">{{ __('Quota completed') }}</span>
                            @endif
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Wallet Balance Card (Mobile Row 2 Right) -->
        <div class="col-6 col-md-6 col-xl-3 mb-2">
            <div class="card comm-metric-card border-left-info shadow-sm h-100" style="border-left: 3.5px solid #0284c7 !important;">
                <div class="card-body comm-metric-body">
                    <div class="comm-card-header">
                        <div class="comm-card-title text-info">
                            {{ __('Wallet Balance') }}
                        </div>
                        <div class="comm-stat-icon-wrap bg-info text-white">
                            <i class="fas fa-wallet"></i>
                        </div>
                    </div>
                    <div>
                        <div class="comm-card-value text-gray-800">
                            {{ PriceHelper::adminCurrency() }} {{ number_format($seller->balance ?? 0, 2) }}
                        </div>
                        <small class="comm-card-subtext">
                            {{ __('Available for auto-deductions') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 10px;">
        <div class="card-body py-3 px-3">
            <form action="{{ route('seller.commission.index') }}" method="GET" class="row align-items-center" style="gap: 8px 0;">
                <div class="col-12 col-md-3 mb-2 mb-md-0">
                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-light border-right-0"><i class="fas fa-search text-muted"></i></span>
                        </div>
                        <input type="text" name="search" class="form-control border-left-0" placeholder="{{ __('Search Order #...') }}" value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-6 col-md-3 mb-2 mb-md-0">
                    <select name="status" class="form-control form-control-sm">
                        <option value="">{{ __('All Commission Types') }}</option>
                        <option value="deducted" {{ request('status') === 'deducted' ? 'selected' : '' }}>{{ __('Paid / Deducted') }}</option>
                        <option value="free" {{ request('status') === 'free' ? 'selected' : '' }}>{{ __('Free Allowance (0.00)') }}</option>
                        <option value="pending_balance" {{ request('status') === 'pending_balance' ? 'selected' : '' }}>{{ __('Pending Balance (Locked)') }}</option>
                    </select>
                </div>

                <div class="col-6 col-md-2 mb-2 mb-md-0">
                    <input type="date" name="date_from" class="form-control form-control-sm" placeholder="{{ __('From Date') }}" value="{{ request('date_from') }}" title="{{ __('From Date') }}">
                </div>

                <div class="col-6 col-md-2 mb-2 mb-md-0">
                    <input type="date" name="date_to" class="form-control form-control-sm" placeholder="{{ __('To Date') }}" value="{{ request('date_to') }}" title="{{ __('To Date') }}">
                </div>

                <div class="col-6 col-md-2 d-flex align-items-center" style="gap: 5px;">
                    <button type="submit" class="btn btn-primary btn-sm flex-fill font-weight-bold">
                        <i class="fas fa-filter mr-1"></i> {{ __('Filter') }}
                    </button>
                    @if(request()->anyFilled(['search', 'status', 'date_from', 'date_to']))
                        <a href="{{ route('seller.commission.index') }}" class="btn btn-light btn-sm border" title="{{ __('Reset Filters') }}">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Commission History Table Card -->
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 10px; overflow: hidden;">
        <div class="card-header bg-white py-3 px-3 d-flex align-items-center justify-content-between border-bottom">
            <h5 class="mb-0 font-weight-bold text-dark" style="font-size: 15px;">
                <i class="fas fa-history text-primary mr-2"></i>{{ __('Order Commission History') }}
            </h5>
            <span class="badge badge-light border px-2 py-1 font-weight-bold" style="font-size: 11px;">
                {{ __('Total Orders:') }} {{ $orders->total() }}
            </span>
        </div>

        <div class="card-body p-0">
            <div class="comm-table-wrap">
                <table class="table table-hover comm-table">
                    <thead>
                        <tr>
                            <th>{{ __('Order #') }}</th>
                            <th>{{ __('Date & Time') }}</th>
                            <th>{{ __('Customer') }}</th>
                            <th class="text-right">{{ __('Gross Sales') }}</th>
                            <th class="text-center">{{ __('Rate') }}</th>
                            <th class="text-right">{{ __('Commission Deducted') }}</th>
                            <th class="text-right">{{ __('Net Store Earnings') }}</th>
                            <th class="text-center">{{ __('Commission Status') }}</th>
                            <th class="text-center">{{ __('Order Status') }}</th>
                            <th class="text-center">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            @php
                                $cart = json_decode($order->cart, true);
                                $sellerCart = is_array($cart) ? $cart : [];
                                $orderGross = 0;
                                $itemsCount = 0;
                                foreach ($sellerCart as $item) {
                                    $p = $item['main_price'] ?? 0;
                                    $ap = $item['attribute_price'] ?? 0;
                                    $q = $item['qty'] ?? 1;
                                    $orderGross += ($p + $ap) * $q;
                                    $itemsCount += $q;
                                }
                                if ($orderGross <= 0) {
                                    $orderGross = (float)PriceHelper::OrderTotal($order);
                                }

                                $commAmount = (float)($order->commission_amount ?? 0);
                                $commStatus = $order->commission_status ?: ($commAmount > 0 ? 'deducted' : 'free');
                                $netEarnings = max(0, $orderGross - $commAmount);

                                $bill = json_decode($order->billing_info, true);
                                $customerName = $bill['bill_first_name'] ?? ($order->user->first_name ?? 'Customer');
                                if (!empty($bill['bill_last_name'])) {
                                    $customerName .= ' ' . $bill['bill_last_name'];
                                }
                            @endphp
                            <tr>
                                <!-- Order # -->
                                <td>
                                    <a href="{{ route('seller.order.invoice', $order->id) }}" class="font-weight-bold text-primary" style="font-size: 13px;">
                                        {{ $order->transaction_number }}
                                    </a>
                                    <div class="small text-muted" style="font-size: 11px;">
                                        {{ $itemsCount }} {{ __('item(s)') }}
                                    </div>
                                </td>

                                <!-- Date & Time -->
                                <td>
                                    <div class="text-dark font-weight-bold" style="font-size: 12.5px;">
                                        {{ $order->created_at->format('d M, Y') }}
                                    </div>
                                    <small class="text-muted" style="font-size: 11px;">
                                        {{ $order->created_at->format('h:i A') }}
                                    </small>
                                </td>

                                <!-- Customer -->
                                <td>
                                    <div class="text-dark font-weight-bold text-truncate" style="max-width: 140px; font-size: 12.5px;">
                                        {{ $customerName }}
                                    </div>
                                    <small class="text-muted d-block text-truncate" style="max-width: 140px; font-size: 11px;">
                                        {{ $order->payment_method }}
                                    </small>
                                </td>

                                <!-- Gross Sales -->
                                <td class="text-right font-weight-bold text-dark">
                                    {{ PriceHelper::adminCurrency() }} {{ number_format($orderGross, 2) }}
                                </td>

                                <!-- Rate -->
                                <td class="text-center">
                                    @if($commStatus === 'free')
                                        <span class="badge badge-success px-2 py-1" style="font-size: 11px;">0% ({{ __('Free') }})</span>
                                    @else
                                        <span class="badge badge-light border px-2 py-1 font-weight-bold" style="font-size: 11px;">{{ number_format($commissionPercent, 1) }}%</span>
                                    @endif
                                </td>

                                <!-- Commission Deducted -->
                                <td class="text-right font-weight-bold">
                                    @if($commStatus === 'deducted')
                                        <span class="text-danger">
                                            - {{ PriceHelper::adminCurrency() }} {{ number_format($commAmount, 2) }}
                                        </span>
                                    @elseif($commStatus === 'pending_balance')
                                        <span class="text-warning font-weight-bold">
                                            {{ PriceHelper::adminCurrency() }} {{ number_format($commAmount, 2) }}
                                        </span>
                                        <small class="d-block text-danger" style="font-size: 10px;">{{ __('Pending') }}</small>
                                    @else
                                        <span class="text-success font-weight-bold">
                                            {{ PriceHelper::adminCurrency() }} 0.00
                                        </span>
                                        <small class="d-block text-muted" style="font-size: 10px;">{{ __('Free') }}</small>
                                    @endif
                                </td>

                                <!-- Net Store Earnings -->
                                <td class="text-right font-weight-bold text-success">
                                    {{ PriceHelper::adminCurrency() }} {{ number_format($netEarnings, 2) }}
                                </td>

                                <!-- Commission Status -->
                                <td class="text-center">
                                    @if($commStatus === 'deducted')
                                        <span class="badge badge-success px-2 py-1 font-weight-bold" style="font-size: 11px;">
                                            <i class="fas fa-check-circle mr-1"></i> {{ __('Paid / Deducted') }}
                                        </span>
                                    @elseif($commStatus === 'free')
                                        <span class="badge badge-info px-2 py-1 font-weight-bold" style="font-size: 11px;">
                                            <i class="fas fa-gift mr-1"></i> {{ __('Free Allowance') }}
                                        </span>
                                    @elseif($commStatus === 'pending_balance')
                                        <span class="badge badge-danger px-2 py-1 font-weight-bold" style="font-size: 11px;" title="{{ __('Order locked until wallet balance covers commission') }}">
                                            <i class="fas fa-clock mr-1"></i> {{ __('Pending Top-up') }}
                                        </span>
                                    @else
                                        <span class="badge badge-secondary px-2 py-1" style="font-size: 11px;">
                                            {{ ucfirst($commStatus) }}
                                        </span>
                                    @endif
                                </td>

                                <!-- Order Status -->
                                <td class="text-center">
                                    @if($order->order_status == 'Delivered')
                                        <span class="badge badge-success" style="font-size: 11px;"><i class="fas fa-check mr-1"></i>{{ __('Delivered') }}</span>
                                    @elseif($order->order_status == 'In Progress')
                                        <span class="badge badge-info" style="font-size: 11px;"><i class="fas fa-truck mr-1"></i>{{ __('In Progress') }}</span>
                                    @elseif($order->order_status == 'Send to Delivery House')
                                        <span class="badge" style="background-color: #6f42c1; color: #fff; font-size: 11px;"><i class="fas fa-warehouse mr-1"></i>{{ __('Delivery House') }}</span>
                                    @elseif($order->order_status == 'Accepted')
                                        <span class="badge badge-primary" style="font-size: 11px;"><i class="fas fa-check-double mr-1"></i>{{ __('Accepted') }}</span>
                                    @elseif($order->order_status == 'Canceled')
                                        <span class="badge badge-danger" style="font-size: 11px;"><i class="fas fa-ban mr-1"></i>{{ __('Canceled') }}</span>
                                    @else
                                        <span class="badge badge-warning text-dark" style="font-size: 11px;"><i class="fas fa-clock mr-1"></i>{{ __('Pending') }}</span>
                                    @endif
                                </td>

                                <!-- Action -->
                                <td class="text-center">
                                    <a href="{{ route('seller.order.invoice', $order->id) }}" class="btn btn-outline-primary btn-sm py-1 px-2" style="border-radius: 5px; font-size: 12px;" title="{{ __('View Order Invoice') }}">
                                        <i class="fas fa-eye mr-1"></i> {{ __('Invoice') }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-5 text-muted">
                                    <div class="py-4">
                                        <i class="fas fa-percentage fa-3x text-muted mb-3 opacity-50"></i>
                                        <h6 class="font-weight-bold text-dark">{{ __('No Commission Records Found') }}</h6>
                                        <p class="small text-muted mb-0">
                                            {{ __('Orders received by your store will have their sales commission details and breakdown recorded here.') }}
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($orders->hasPages())
            <div class="card-footer py-2 bg-light d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    {{ __('Showing :from to :to of :total records', [
                        'from' => $orders->firstItem() ?: 0,
                        'to' => $orders->lastItem() ?: 0,
                        'total' => $orders->total()
                    ]) }}
                </small>
                <div>
                    {{ $orders->appends(request()->input())->links() }}
                </div>
            </div>
        @endif
    </div>

</div>

@endsection
