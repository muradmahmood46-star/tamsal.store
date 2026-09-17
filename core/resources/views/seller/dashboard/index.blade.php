@extends('master.seller')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between">
                <div>
                    <h3 class="mb-0 text-dark"><b><i class="fas fa-store text-primary mr-2"></i> {{ $seller->shop_name }} - {{ __('Dashboard') }}</b></h3>
                    <p class="text-muted small mb-0">{{ __('Manage your products, orders, sales, and store profile.') }}</p>
                </div>
                <div>
                    <a href="{{ route('seller.item.add') }}" class="btn btn-primary btn-sm mr-2">
                        <i class="fas fa-plus mr-1"></i> {{ __('Add Product') }}
                    </a>
                    <a href="{{ route('seller.profile') }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-cogs mr-1"></i> {{ __('Store Settings') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    @include('alerts.alerts')

    @if(isset($rejectedProductsCount) && $rejectedProductsCount > 0)
        <div class="alert alert-danger shadow-sm mb-4 border-left border-danger" style="border-left-width: 5px !important;">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-circle fa-2x mr-3 text-danger"></i>
                    <div>
                        <h6 class="mb-1 font-weight-bold text-danger">{{ __('Action Required: :count product(s) rejected by admin', ['count' => $rejectedProductsCount]) }}</h6>
                        <p class="mb-0 text-dark small">{{ __('Admin has reviewed your submissions and requested modifications on some of your products. Please review the feedback and update your listings.') }}</p>
                    </div>
                </div>
                <a href="{{ route('seller.item.index') }}" class="btn btn-danger btn-sm font-weight-bold text-nowrap ml-3">
                    <i class="fas fa-eye mr-1"></i> {{ __('View Rejected Products') }}
                </a>
            </div>
        </div>
    @endif

    <!-- Wallet Balance & Commission Overview Banner -->
    @php
        $dashSetting = \App\Models\Setting::first();
        $freeOrdersLimit = (int)($dashSetting->vendor_free_orders ?? 5);
        $currVendorOrdersCount = \App\Models\Order::where('vendor_id', Auth::id())->count();
        $freeOrdersLeft = max(0, $freeOrdersLimit - $currVendorOrdersCount);
    @endphp
    <div class="card shadow-sm mb-4 border-0" style="border-radius: 12px; background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: #fff;">
        <div class="card-body p-3 p-md-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between">
                <div class="d-flex align-items-center mb-3 mb-md-0">
                    <div class="rounded-circle d-flex align-items-center justify-content-center mr-3 shadow" style="width: 55px; height: 55px; background: rgba(16, 185, 129, 0.2); color: #10b981; font-size: 24px; margin-right: 15px;">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div>
                        <div class="text-uppercase text-white-50 small font-weight-bold" style="letter-spacing: 0.5px;">{{ __('Store Wallet Balance') }}</div>
                        <h3 class="mb-0 text-white font-weight-bold">
                            {{ PriceHelper::adminCurrency() }} {{ number_format($seller->balance ?? 0, 2) }}
                        </h3>
                        <div class="d-flex flex-wrap align-items-center small text-white-50 mt-1" style="gap: 15px;">
                            @if($freeOrdersLeft > 0)
                                <span class="text-success font-weight-bold"><i class="fas fa-gift mr-1"></i> {{ $freeOrdersLeft }} {{ __('Free order(s) left') }}</span>
                            @else
                                <span class="text-warning font-weight-bold"><i class="fas fa-percentage mr-1"></i> {{ $dashSetting->vendor_commission_percent ?? 2 }}% {{ __('Commission Active') }}</span>
                            @endif
                            <span><i class="fas fa-clock mr-1"></i> {{ __('Min Deposit:') }} {{ PriceHelper::adminCurrency() }} {{ number_format($dashSetting->vendor_min_balance ?? 500, 2) }}</span>
                        </div>
                    </div>
                </div>
                <div>
                    <a href="{{ route('seller.wallet.index') }}" class="btn btn-success font-weight-bold shadow-sm px-4 py-2" style="border-radius: 8px;">
                        <i class="fas fa-plus-circle mr-1"></i> {{ __('Add Balance') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales Row -->
    <div class="row">
        <!-- Total Sales -->
        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="card card-stats card-round h-100" style="border-left: 4px solid #1572e8;">
                <div class="card-body py-3">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-primary bubble-shadow-small">
                                <i class="fas fa-chart-line" style="font-size: 22px;"></i>
                            </div>
                        </div>
                        <div class="col col-stats ml-3 ml-sm-0">
                            <div class="numbers">
                                <p class="mb-0 text-muted" style="font-size: 13px; font-weight: 600;"><b>{{ __('Total Sales') }}</b></p>
                                <h4 class="card-title text-primary font-weight-bold" style="font-size: 18px; margin-top: 2px; margin-bottom: 0;">{{ PriceHelper::setCurrencyPrice($totalSales) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today Sales -->
        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="card card-stats card-round h-100" style="border-left: 4px solid #1572e8;">
                <div class="card-body py-3">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-primary bubble-shadow-small">
                                <i class="fas fa-chart-line" style="font-size: 22px;"></i>
                            </div>
                        </div>
                        <div class="col col-stats ml-3 ml-sm-0">
                            <div class="numbers">
                                <p class="mb-0 text-muted" style="font-size: 13px; font-weight: 600;"><b>{{ __('Today Sales') }}</b></p>
                                <h4 class="card-title text-primary font-weight-bold" style="font-size: 18px; margin-top: 2px; margin-bottom: 0;">{{ PriceHelper::setCurrencyPrice($todaySales) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- This Month Sales -->
        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="card card-stats card-round h-100" style="border-left: 4px solid #1572e8;">
                <div class="card-body py-3">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-primary bubble-shadow-small">
                                <i class="fas fa-chart-line" style="font-size: 22px;"></i>
                            </div>
                        </div>
                        <div class="col col-stats ml-3 ml-sm-0">
                            <div class="numbers">
                                <p class="mb-0 text-muted" style="font-size: 13px; font-weight: 600;"><b>{{ __('This Month Sales') }}</b></p>
                                <h4 class="card-title text-primary font-weight-bold" style="font-size: 18px; margin-top: 2px; margin-bottom: 0;">{{ PriceHelper::setCurrencyPrice($thisMonthSales) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- This Year Sales -->
        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="card card-stats card-round h-100" style="border-left: 4px solid #1572e8;">
                <div class="card-body py-3">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-primary bubble-shadow-small">
                                <i class="fas fa-chart-line" style="font-size: 22px;"></i>
                            </div>
                        </div>
                        <div class="col col-stats ml-3 ml-sm-0">
                            <div class="numbers">
                                <p class="mb-0 text-muted" style="font-size: 13px; font-weight: 600;"><b>{{ __('This Year Sales') }}</b></p>
                                <h4 class="card-title text-primary font-weight-bold" style="font-size: 18px; margin-top: 2px; margin-bottom: 0;">{{ PriceHelper::setCurrencyPrice($thisYearSales) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Earnings Row -->
    <div class="row">
        <!-- Total Earnings -->
        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="card card-stats card-round h-100" style="border-left: 4px solid #f3545d;">
                <div class="card-body py-3">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-danger bubble-shadow-small">
                                <i class="fas fa-money-bill-wave" style="font-size: 22px;"></i>
                            </div>
                        </div>
                        <div class="col col-stats ml-3 ml-sm-0">
                            <div class="numbers">
                                <p class="mb-0 text-muted" style="font-size: 13px; font-weight: 600;"><b>{{ __('Total Earnings') }}</b></p>
                                <h4 class="card-title text-danger font-weight-bold" style="font-size: 18px; margin-top: 2px; margin-bottom: 0;">{{ PriceHelper::setCurrencyPrice($totalEarnings) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today Earnings -->
        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="card card-stats card-round h-100" style="border-left: 4px solid #f3545d;">
                <div class="card-body py-3">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-danger bubble-shadow-small">
                                <i class="fas fa-money-bill-wave" style="font-size: 22px;"></i>
                            </div>
                        </div>
                        <div class="col col-stats ml-3 ml-sm-0">
                            <div class="numbers">
                                <p class="mb-0 text-muted" style="font-size: 13px; font-weight: 600;"><b>{{ __('Today Earnings') }}</b></p>
                                <h4 class="card-title text-danger font-weight-bold" style="font-size: 18px; margin-top: 2px; margin-bottom: 0;">{{ PriceHelper::setCurrencyPrice($todayEarnings) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- This Month Earnings -->
        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="card card-stats card-round h-100" style="border-left: 4px solid #f3545d;">
                <div class="card-body py-3">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-danger bubble-shadow-small">
                                <i class="fas fa-money-bill-wave" style="font-size: 22px;"></i>
                            </div>
                        </div>
                        <div class="col col-stats ml-3 ml-sm-0">
                            <div class="numbers">
                                <p class="mb-0 text-muted" style="font-size: 13px; font-weight: 600;"><b>{{ __('This Month Earnings') }}</b></p>
                                <h4 class="card-title text-danger font-weight-bold" style="font-size: 18px; margin-top: 2px; margin-bottom: 0;">{{ PriceHelper::setCurrencyPrice($thisMonthEarnings) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- This Year Earnings -->
        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="card card-stats card-round h-100" style="border-left: 4px solid #f3545d;">
                <div class="card-body py-3">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-danger bubble-shadow-small">
                                <i class="fas fa-money-bill-wave" style="font-size: 22px;"></i>
                            </div>
                        </div>
                        <div class="col col-stats ml-3 ml-sm-0">
                            <div class="numbers">
                                <p class="mb-0 text-muted" style="font-size: 13px; font-weight: 600;"><b>{{ __('This Year Earnings') }}</b></p>
                                <h4 class="card-title text-danger font-weight-bold" style="font-size: 18px; margin-top: 2px; margin-bottom: 0;">{{ PriceHelper::setCurrencyPrice($thisYearEarnings) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Catalog & Orders Overview -->
    <div class="row">
        <!-- Total Products -->
        <div class="col-sm-6 col-md-3">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-info bubble-shadow-small">
                                <i class="fab fa-product-hunt"></i>
                            </div>
                        </div>
                        <div class="col col-stats ml-3 ml-sm-0">
                            <div class="numbers">
                                <p class="card-category">{{ __('Total Products') }}</p>
                                <h4 class="card-title">{{ $totalProducts }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Categories -->
        <div class="col-sm-6 col-md-3">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center bubble-shadow-small" style="background-color: #17a2b8; color: #fff; border-radius: 50%; width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-list-alt"></i>
                            </div>
                        </div>
                        <div class="col col-stats ml-3 ml-sm-0">
                            <div class="numbers">
                                <p class="card-category">{{ __('Total Categories') }}</p>
                                <h4 class="card-title">{{ $totalCategories }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Brands -->
        <div class="col-sm-6 col-md-3">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center bubble-shadow-small" style="background-color: #20c997; color: #fff; border-radius: 50%; width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-tags"></i>
                            </div>
                        </div>
                        <div class="col col-stats ml-3 ml-sm-0">
                            <div class="numbers">
                                <p class="card-category">{{ __('Total Brands') }}</p>
                                <h4 class="card-title">{{ $totalBrands }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Orders -->
        <div class="col-sm-6 col-md-3">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-info bubble-shadow-small">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                        </div>
                        <div class="col col-stats ml-3 ml-sm-0">
                            <div class="numbers">
                                <p class="card-category">{{ __('Total Orders') }}</p>
                                <h4 class="card-title">{{ $totalOrders }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Workflow Overview -->
    <div class="row">
        <!-- Pending Orders -->
        <div class="col-sm-6 col-md-3">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center bubble-shadow-small" style="background-color: #ff9800; color: #fff; border-radius: 50%; width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                        <div class="col col-stats ml-3 ml-sm-0">
                            <div class="numbers">
                                <p class="card-category font-weight-bold text-warning">{{ __('Pending Orders') }}</p>
                                <h4 class="card-title text-warning font-weight-bold">{{ $totalPendingOrders }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Accepted Orders -->
        <div class="col-sm-6 col-md-3">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center bubble-shadow-small" style="background-color: #007bff; color: #fff; border-radius: 50%; width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-check"></i>
                            </div>
                        </div>
                        <div class="col col-stats ml-3 ml-sm-0">
                            <div class="numbers">
                                <p class="card-category">{{ __('Accepted Orders') }}</p>
                                <h4 class="card-title">{{ $totalAcceptedOrders }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order in Delivery House -->
        <div class="col-sm-6 col-md-3">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center bubble-shadow-small" style="background-color: #6f42c1; color: #fff; border-radius: 50%; width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-warehouse"></i>
                            </div>
                        </div>
                        <div class="col col-stats ml-3 ml-sm-0">
                            <div class="numbers">
                                <p class="card-category">{{ __('Order in Delivery House') }}</p>
                                <h4 class="card-title">{{ $totalSendToDeliveryOrders }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delivered Orders -->
        <div class="col-sm-6 col-md-3">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-success bubble-shadow-small">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                        <div class="col col-stats ml-3 ml-sm-0">
                            <div class="numbers">
                                <p class="card-category">{{ __('Delivered Orders') }}</p>
                                <h4 class="card-title">{{ $totalDeliveredOrders }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Status Overview -->
    <div class="row">
        <!-- Delivery in Progress -->
        <div class="col-sm-6 col-md-3">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center bubble-shadow-small" style="background-color: #17a2b8; color: #fff; border-radius: 50%; width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-truck"></i>
                            </div>
                        </div>
                        <div class="col col-stats ml-3 ml-sm-0">
                            <div class="numbers">
                                <p class="card-category">{{ __('Delivery in Progress') }}</p>
                                <h4 class="card-title">{{ $totalInProgressOrders }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Canceled Orders -->
        <div class="col-sm-6 col-md-3">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-danger bubble-shadow-small">
                                <i class="fas fa-ban"></i>
                            </div>
                        </div>
                        <div class="col col-stats ml-3 ml-sm-0">
                            <div class="numbers">
                                <p class="card-category">{{ __('Canceled Orders') }}</p>
                                <h4 class="card-title">{{ $totalCanceledOrders }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Products -->
        <div class="col-sm-6 col-md-3">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center bubble-shadow-small" style="background-color: #ffc107; color: #212529; border-radius: 50%; width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-hourglass-half"></i>
                            </div>
                        </div>
                        <div class="col col-stats ml-3 ml-sm-0">
                            <div class="numbers">
                                <p class="card-category">{{ __('Pending Products') }}</p>
                                <h4 class="card-title">{{ $pendingProductsCount }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Out of Stock -->
        <div class="col-sm-6 col-md-3">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-secondary bubble-shadow-small">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                        </div>
                        <div class="col col-stats ml-3 ml-sm-0">
                            <div class="numbers">
                                <p class="card-category">{{ __('Out of Stock') }}</p>
                                <h4 class="card-title">{{ $stockOutProducts }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Sales & Earnings Charts Row -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header py-3">
                    <div class="card-title font-weight-bold text-primary"><i class="fas fa-chart-line mr-2"></i>{{ __('Monthly Product Sales Report') }}</div>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="min-height: 300px;">
                        <canvas id="sellerSalesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header py-3">
                    <div class="card-title font-weight-bold text-danger"><i class="fas fa-coins mr-2"></i>{{ __('Monthly Earnings Report') }}</div>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="min-height: 300px;">
                        <canvas id="sellerEarningsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders & Recent Products Row -->
    <div class="row">
        <!-- Recent Orders -->
        <div class="col-lg-7 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-shopping-bag mr-1"></i> {{ __('Recent Orders') }}</h6>
                    <a href="{{ route('seller.order.index') }}" class="btn btn-outline-primary btn-xs">{{ __('View All') }}</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>{{ __('Order ID') }}</th>
                                    <th>{{ __('Customer') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentOrders as $order)
                                    @php
                                        $bill = json_decode($order->billing_info, true);
                                    @endphp
                                    <tr>
                                        <td><strong>{{ $order->transaction_number }}</strong></td>
                                        <td>{{ $bill['bill_first_name'] ?? ($order->user->first_name ?? 'Customer') }}</td>
                                        <td>
                                            @if($order->order_status == 'Delivered')
                                                <span class="badge badge-success">{{ __('Delivered') }}</span>
                                            @elseif($order->order_status == 'In Progress')
                                                <span class="badge badge-info">{{ __('In Progress') }}</span>
                                            @elseif($order->order_status == 'Send to Delivery House')
                                                <span class="badge" style="background-color: #6f42c1; color: #fff;">{{ __('Send to Delivery House') }}</span>
                                            @elseif($order->order_status == 'Accepted')
                                                <span class="badge badge-primary">{{ __('Accepted') }}</span>
                                            @elseif($order->order_status == 'Canceled')
                                                <span class="badge badge-danger">{{ __('Canceled') }}</span>
                                            @else
                                                <span class="badge badge-warning text-dark">{{ __('Pending') }}</span>
                                            @endif
                                        </td>
                                        <td><small>{{ $order->created_at->format('M d, Y') }}</small></td>
                                        <td>
                                            <a href="{{ route('seller.order.invoice', $order->id) }}" class="btn btn-info btn-xs"><i class="fas fa-eye"></i></a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">{{ __('No orders received yet.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Products -->
        <div class="col-lg-5 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fab fa-product-hunt mr-1"></i> {{ __('Recent Products') }}</h6>
                    <a href="{{ route('seller.item.index') }}" class="btn btn-outline-primary btn-xs">{{ __('View All') }}</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>{{ __('Image') }}</th>
                                    <th>{{ __('Product') }}</th>
                                    <th>{{ __('Price') }}</th>
                                    <th>{{ __('Stock') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentProducts as $prod)
                                    <tr>
                                        <td>
                                            <img src="{{ $prod->photo ? asset('core/public/storage/images/' . $prod->photo) : asset('core/public/storage/images/placeholder.png') }}" alt="" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                        </td>
                                        <td>
                                            <a href="{{ route('seller.item.edit', $prod->id) }}" class="font-weight-bold text-dark">{{ Str::limit($prod->name, 25) }}</a>
                                        </td>
                                        <td><strong class="text-primary">{{ PriceHelper::setCurrencyPrice($prod->discount_price) }}</strong></td>
                                        <td>
                                            @if($prod->stock > 0)
                                                <span class="badge badge-success">{{ $prod->stock }}</span>
                                            @else
                                                <span class="badge badge-danger">{{ __('Out of Stock') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">{{ __('No products added yet.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    var sellerSalesCtx = document.getElementById('sellerSalesChart').getContext('2d');
    var sellerEarningsCtx = document.getElementById('sellerEarningsChart').getContext('2d');

    var mySellerSalesChart = new Chart(sellerSalesCtx, {
        type: 'line',
        data: {
            labels: [{!! $order_days !!}],
            datasets: [{
                label: "{{ __('Product Sales') }} ({{ PriceHelper::adminCurrency() }})",
                borderColor: "#1d7af3",
                pointBorderColor: "#FFF",
                pointBackgroundColor: "#1d7af3",
                pointBorderWidth: 2,
                pointHoverRadius: 4,
                pointHoverBorderWidth: 1,
                pointRadius: 4,
                backgroundColor: 'rgba(29, 122, 243, 0.08)',
                fill: true,
                borderWidth: 2,
                data: [{!! $order_sales !!}]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: {
                display: false
            },
            tooltips: {
                bodySpacing: 4,
                mode: "nearest",
                intersect: 0,
                position: "nearest",
                xPadding: 10,
                yPadding: 10,
                caretPadding: 10
            },
            layout: {
                padding: {left: 15, right: 15, top: 15, bottom: 15}
            }
        }
    });

    var mySellerEarningsChart = new Chart(sellerEarningsCtx, {
        type: 'line',
        data: {
            labels: [{!! $earning_days !!}],
            datasets: [{
                label: "{{ __('Earnings') }} ({{ PriceHelper::adminCurrency() }})",
                borderColor: "#f3545d",
                pointBorderColor: "#FFF",
                pointBackgroundColor: "#f3545d",
                pointBorderWidth: 2,
                pointHoverRadius: 4,
                pointHoverBorderWidth: 1,
                pointRadius: 4,
                backgroundColor: 'rgba(243, 84, 93, 0.08)',
                fill: true,
                borderWidth: 2,
                data: [{!! $total_incomess !!}]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: {
                display: false
            },
            tooltips: {
                bodySpacing: 4,
                mode: "nearest",
                intersect: 0,
                position: "nearest",
                xPadding: 10,
                yPadding: 10,
                caretPadding: 10
            },
            layout: {
                padding: {left: 15, right: 15, top: 15, bottom: 15}
            }
        }
    });
</script>
@endsection
