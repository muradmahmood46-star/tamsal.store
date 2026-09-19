@extends('master.back')

@section('content')

<div class="container-fluid">

    <!-- Page Heading -->



    <div class="card mb-4">
        <h3 class="mb-0 px-3 py-4"><b>{{ __('Dashboard') }}</b></h3>
    </div>


    @include('alerts.alerts')
  <!-- ========================================== -->
  <!-- ========================================== -->
  <!-- 1. SALES METRICS (ROW OF 4 CARDS) -->
  <!-- ========================================== -->
  <div class="row">
    <!-- Total Sales -->
    <div class="col-6 col-md-6 col-xl-3 mb-3">
        <div class="card card-stats card-round h-100" style="border-left: 4px solid #1572e8;">
            <div class="card-body py-3">
                <div class="row align-items-center no-gutters">
                    <div class="col-auto col-icon">
                        <div class="icon-big text-center icon-primary bubble-shadow-small">
                            <i class="fas fa-chart-line"></i>
                        </div>
                    </div>
                    <div class="col col-stats">
                        <div class="numbers">
                            <p class="card-category text-muted font-weight-bold"><b>{{ __('Total Sales') }}</b></p>
                            <h4 class="card-title text-primary font-weight-bold">{{ $totalProductSale }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Today Sales -->
    <div class="col-6 col-md-6 col-xl-3 mb-3">
        <div class="card card-stats card-round h-100" style="border-left: 4px solid #1572e8;">
            <div class="card-body py-3">
                <div class="row align-items-center no-gutters">
                    <div class="col-auto col-icon">
                        <div class="icon-big text-center icon-primary bubble-shadow-small">
                            <i class="fas fa-chart-line"></i>
                        </div>
                    </div>
                    <div class="col col-stats">
                        <div class="numbers">
                            <p class="card-category text-muted font-weight-bold"><b>{{ __('Today Sales') }}</b></p>
                            <h4 class="card-title text-primary font-weight-bold">{{ $totalTodayProductSale }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- This Month Sales -->
    <div class="col-6 col-md-6 col-xl-3 mb-3">
        <div class="card card-stats card-round h-100" style="border-left: 4px solid #1572e8;">
            <div class="card-body py-3">
                <div class="row align-items-center no-gutters">
                    <div class="col-auto col-icon">
                        <div class="icon-big text-center icon-primary bubble-shadow-small">
                            <i class="fas fa-chart-line"></i>
                        </div>
                    </div>
                    <div class="col col-stats">
                        <div class="numbers">
                            <p class="card-category text-muted font-weight-bold"><b>{{ __('This Month Sales') }}</b></p>
                            <h4 class="card-title text-primary font-weight-bold">{{ $totalCurrentMonthProductSale }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- This Year Sales -->
    <div class="col-6 col-md-6 col-xl-3 mb-3">
        <div class="card card-stats card-round h-100" style="border-left: 4px solid #1572e8;">
            <div class="card-body py-3">
                <div class="row align-items-center no-gutters">
                    <div class="col-auto col-icon">
                        <div class="icon-big text-center icon-primary bubble-shadow-small">
                            <i class="fas fa-chart-line"></i>
                        </div>
                    </div>
                    <div class="col col-stats">
                        <div class="numbers">
                            <p class="card-category text-muted font-weight-bold"><b>{{ __('This Year Sales') }}</b></p>
                            <h4 class="card-title text-primary font-weight-bold">{{ $totalLatYearProductSale }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>

  <!-- ========================================== -->
  <!-- 2. EARNINGS METRICS (ROW OF 4 CARDS) -->
  <!-- ========================================== -->
  <div class="row">
    <!-- Total Earning -->
    <div class="col-6 col-md-6 col-xl-3 mb-3">
        <div class="card card-stats card-round h-100" style="border-left: 4px solid #f3545d;">
            <div class="card-body py-3">
                <div class="row align-items-center no-gutters">
                    <div class="col-auto col-icon">
                        <div class="icon-big text-center icon-danger bubble-shadow-small">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                    </div>
                    <div class="col col-stats">
                        <div class="numbers">
                            <p class="card-category text-muted font-weight-bold"><b>{{ __('Total Earning') }}</b></p>
                            <h4 class="card-title text-danger font-weight-bold">{{ $totalEarning }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Today Earning -->
    <div class="col-6 col-md-6 col-xl-3 mb-3">
        <div class="card card-stats card-round h-100" style="border-left: 4px solid #f3545d;">
            <div class="card-body py-3">
                <div class="row align-items-center no-gutters">
                    <div class="col-auto col-icon">
                        <div class="icon-big text-center icon-danger bubble-shadow-small">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                    </div>
                    <div class="col col-stats">
                        <div class="numbers">
                            <p class="card-category text-muted font-weight-bold"><b>{{ __('Today Earning') }}</b></p>
                            <h4 class="card-title text-danger font-weight-bold">{{ $totalTodayEarning }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- This Month Earning -->
    <div class="col-6 col-md-6 col-xl-3 mb-3">
        <div class="card card-stats card-round h-100" style="border-left: 4px solid #f3545d;">
            <div class="card-body py-3">
                <div class="row align-items-center no-gutters">
                    <div class="col-auto col-icon">
                        <div class="icon-big text-center icon-danger bubble-shadow-small">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                    </div>
                    <div class="col col-stats">
                        <div class="numbers">
                            <p class="card-category text-muted font-weight-bold"><b>{{ __('This Month Earning') }}</b></p>
                            <h4 class="card-title text-danger font-weight-bold">{{ $totalMonthEarning }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- This Year Earning -->
    <div class="col-6 col-md-6 col-xl-3 mb-3">
        <div class="card card-stats card-round h-100" style="border-left: 4px solid #f3545d;">
            <div class="card-body py-3">
                <div class="row align-items-center no-gutters">
                    <div class="col-auto col-icon">
                        <div class="icon-big text-center icon-danger bubble-shadow-small">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                    </div>
                    <div class="col col-stats">
                        <div class="numbers">
                            <p class="card-category text-muted font-weight-bold"><b>{{ __('This Year Earning') }}</b></p>
                            <h4 class="card-title text-danger font-weight-bold">{{ $totalYearEarning }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>

  <!-- ========================================== -->
  <!-- 3. ORDERS WORKFLOW - ROW 1 (4 CARDS) -->
  <!-- ========================================== -->
  <div class="row">
    <!-- Total Orders -->
    <div class="col-6 col-md-6 col-xl-3 mb-3">
        <div class="card card-stats card-round h-100" style="border-left: 4px solid #1572e8;">
            <div class="card-body">
                <div class="row align-items-center no-gutters">
                    <div class="col-auto col-icon">
                        <div class="icon-big text-center icon-primary bubble-shadow-small">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                    </div>
                    <div class="col col-stats">
                        <div class="numbers">
                            <p class="card-category text-muted font-weight-bold"><b>{{ __('Total Orders') }}</b></p>
                            <h4 class="card-title text-primary font-weight-bold">{{ $totalOrders }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Orders -->
    <div class="col-6 col-md-6 col-xl-3 mb-3">
        <div class="card card-stats card-round h-100" style="border-left: 4px solid #ff9800;">
            <div class="card-body">
                <div class="row align-items-center no-gutters">
                    <div class="col-auto col-icon">
                        <div class="icon-big text-center bubble-shadow-small" style="background-color: #ff9800; color: #fff;">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                    <div class="col col-stats">
                        <div class="numbers">
                            <p class="card-category text-muted font-weight-bold"><b>{{ __('Pending Orders') }}</b></p>
                            <h4 class="card-title text-warning font-weight-bold">{{ $totalPendingOrders }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Accepted Orders -->
    <div class="col-6 col-md-6 col-xl-3 mb-3">
        <div class="card card-stats card-round h-100" style="border-left: 4px solid #007bff;">
            <div class="card-body">
                <div class="row align-items-center no-gutters">
                    <div class="col-auto col-icon">
                        <div class="icon-big text-center bubble-shadow-small" style="background-color: #007bff; color: #fff;">
                            <i class="fas fa-check"></i>
                        </div>
                    </div>
                    <div class="col col-stats">
                        <div class="numbers">
                            <p class="card-category text-muted font-weight-bold"><b>{{ __('Accepted Orders') }}</b></p>
                            <h4 class="card-title text-primary font-weight-bold">{{ $totalAcceptedOrders }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order in Delivery House -->
    <div class="col-6 col-md-6 col-xl-3 mb-3">
        <div class="card card-stats card-round h-100" style="border-left: 4px solid #6f42c1;">
            <div class="card-body">
                <div class="row align-items-center no-gutters">
                    <div class="col-auto col-icon">
                        <div class="icon-big text-center bubble-shadow-small" style="background-color: #6f42c1; color: #fff;">
                            <i class="fas fa-warehouse"></i>
                        </div>
                    </div>
                    <div class="col col-stats">
                        <div class="numbers">
                            <p class="card-category text-muted font-weight-bold"><b>{{ __('Order in Delivery House') }}</b></p>
                            <h4 class="card-title font-weight-bold" style="color: #6f42c1;">{{ $totalSendToDeliveryOrders }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>

  <!-- ========================================== -->
  <!-- 4. ORDERS WORKFLOW - ROW 2 (4 CARDS) -->
  <!-- ========================================== -->
  <div class="row">
    <!-- Delivery in Progress -->
    <div class="col-6 col-md-6 col-xl-3 mb-3">
        <div class="card card-stats card-round h-100" style="border-left: 4px solid #17a2b8;">
            <div class="card-body">
                <div class="row align-items-center no-gutters">
                    <div class="col-auto col-icon">
                        <div class="icon-big text-center bubble-shadow-small" style="background-color: #17a2b8; color: #fff;">
                            <i class="fas fa-truck"></i>
                        </div>
                    </div>
                    <div class="col col-stats">
                        <div class="numbers">
                            <p class="card-category text-muted font-weight-bold"><b>{{ __('Delivery in Progress') }}</b></p>
                            <h4 class="card-title text-info font-weight-bold">{{ $totalInProgressOrders }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delivered Orders -->
    <div class="col-6 col-md-6 col-xl-3 mb-3">
        <div class="card card-stats card-round h-100" style="border-left: 4px solid #28a745;">
            <div class="card-body">
                <div class="row align-items-center no-gutters">
                    <div class="col-auto col-icon">
                        <div class="icon-big text-center icon-success bubble-shadow-small">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                    <div class="col col-stats">
                        <div class="numbers">
                            <p class="card-category text-muted font-weight-bold"><b>{{ __('Delivered Orders') }}</b></p>
                            <h4 class="card-title text-success font-weight-bold">{{ $totalDeliveredOrders }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Canceled Orders -->
    <div class="col-6 col-md-6 col-xl-3 mb-3">
        <div class="card card-stats card-round h-100" style="border-left: 4px solid #dc3545;">
            <div class="card-body">
                <div class="row align-items-center no-gutters">
                    <div class="col-auto col-icon">
                        <div class="icon-big text-center icon-danger bubble-shadow-small">
                            <i class="fas fa-ban"></i>
                        </div>
                    </div>
                    <div class="col col-stats">
                        <div class="numbers">
                            <p class="card-category text-muted font-weight-bold"><b>{{ __('Canceled Orders') }}</b></p>
                            <h4 class="card-title text-danger font-weight-bold">{{ $totalCanceledOrders }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Today Orders -->
    <div class="col-6 col-md-6 col-xl-3 mb-3">
        <div class="card card-stats card-round h-100" style="border-left: 4px solid #20c997;">
            <div class="card-body">
                <div class="row align-items-center no-gutters">
                    <div class="col-auto col-icon">
                        <div class="icon-big text-center bubble-shadow-small" style="background-color: #20c997; color: #fff;">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                    </div>
                    <div class="col col-stats">
                        <div class="numbers">
                            <p class="card-category text-muted font-weight-bold"><b>{{ __('Today Orders') }}</b></p>
                            <h4 class="card-title font-weight-bold" style="color: #20c997;">{{ $totalTodayOrders }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>

  <!-- ========================================== -->
  <!-- 5. PRODUCTS & CATALOG (ROW OF 4 CARDS) -->
  <!-- ========================================== -->
  <div class="row">
    <!-- Total Products -->
    <div class="col-6 col-md-6 col-xl-3 mb-3">
        <div class="card card-stats card-round h-100" style="border-left: 4px solid #17a2b8;">
            <div class="card-body">
                <div class="row align-items-center no-gutters">
                    <div class="col-auto col-icon">
                        <div class="icon-big text-center icon-info bubble-shadow-small">
                            <i class="fab fa-product-hunt"></i>
                        </div>
                    </div>
                    <div class="col col-stats">
                        <div class="numbers">
                            <p class="card-category text-muted font-weight-bold"><b>{{ __('Total Products') }}</b></p>
                            <h4 class="card-title text-info font-weight-bold">{{ $totalItems }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Vendor Products -->
    <div class="col-6 col-md-6 col-xl-3 mb-3">
        <div class="card card-stats card-round h-100" style="border-left: 4px solid #5856d6;">
            <div class="card-body">
                <div class="row align-items-center no-gutters">
                    <div class="col-auto col-icon">
                        <div class="icon-big text-center bubble-shadow-small" style="background-color: #5856d6; color: #fff;">
                            <i class="fas fa-boxes"></i>
                        </div>
                    </div>
                    <div class="col col-stats">
                        <div class="numbers">
                            <p class="card-category text-muted font-weight-bold"><b>{{ __('Total Vendor Products') }}</b></p>
                            <h4 class="card-title font-weight-bold" style="color: #5856d6;">{{ $totalVendorProducts }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Categories -->
    <div class="col-6 col-md-6 col-xl-3 mb-3">
        <div class="card card-stats card-round h-100" style="border-left: 4px solid #6610f2;">
            <div class="card-body">
                <div class="row align-items-center no-gutters">
                    <div class="col-auto col-icon">
                        <div class="icon-big text-center bubble-shadow-small" style="background-color: #6610f2; color: #fff;">
                            <i class="fas fa-list-alt"></i>
                        </div>
                    </div>
                    <div class="col col-stats">
                        <div class="numbers">
                            <p class="card-category text-muted font-weight-bold"><b>{{ __('Total Categories') }}</b></p>
                            <h4 class="card-title font-weight-bold" style="color: #6610f2;">{{ $totalCategory }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Brands -->
    <div class="col-6 col-md-6 col-xl-3 mb-3">
        <div class="card card-stats card-round h-100" style="border-left: 4px solid #4e73df;">
            <div class="card-body">
                <div class="row align-items-center no-gutters">
                    <div class="col-auto col-icon">
                        <div class="icon-big text-center bubble-shadow-small" style="background-color: #4e73df; color: #fff;">
                            <i class="fas fa-tags"></i>
                        </div>
                    </div>
                    <div class="col col-stats">
                        <div class="numbers">
                            <p class="card-category text-muted font-weight-bold"><b>{{ __('Total Brands') }}</b></p>
                            <h4 class="card-title font-weight-bold" style="color: #4e73df;">{{ $totalBrand }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>

  <!-- ========================================== -->
  <!-- 6. USERS, STORES & COMMUNITY (ROW OF 4 CARDS) -->
  <!-- ========================================== -->
  <div class="row">
    <!-- Total Customers -->
    <div class="col-6 col-md-6 col-xl-3 mb-3">
        <div class="card card-stats card-round h-100" style="border-left: 4px solid #1572e8;">
            <div class="card-body">
                <div class="row align-items-center no-gutters">
                    <div class="col-auto col-icon">
                        <div class="icon-big text-center icon-primary bubble-shadow-small">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                    <div class="col col-stats">
                        <div class="numbers">
                            <p class="card-category text-muted font-weight-bold"><b>{{ __('Total Customers') }}</b></p>
                            <h4 class="card-title text-primary font-weight-bold">{{ $totalUsers }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Stores -->
    <div class="col-6 col-md-6 col-xl-3 mb-3">
        <div class="card card-stats card-round h-100" style="border-left: 4px solid #28a745;">
            <div class="card-body">
                <div class="row align-items-center no-gutters">
                    <div class="col-auto col-icon">
                        <div class="icon-big text-center icon-success bubble-shadow-small">
                            <i class="fas fa-store-alt"></i>
                        </div>
                    </div>
                    <div class="col col-stats">
                        <div class="numbers">
                            <p class="card-category text-muted font-weight-bold"><b>{{ __('Total Stores') }}</b></p>
                            <h4 class="card-title text-success font-weight-bold">{{ $totalStores }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total System Users -->
    <div class="col-6 col-md-6 col-xl-3 mb-3">
        <div class="card card-stats card-round h-100" style="border-left: 4px solid #fd7e14;">
            <div class="card-body">
                <div class="row align-items-center no-gutters">
                    <div class="col-auto col-icon">
                        <div class="icon-big text-center bubble-shadow-small" style="background-color: #fd7e14; color: #fff;">
                            <i class="fas fa-user-shield"></i>
                        </div>
                    </div>
                    <div class="col col-stats">
                        <div class="numbers">
                            <p class="card-category text-muted font-weight-bold"><b>{{ __('Total System Users') }}</b></p>
                            <h4 class="card-title font-weight-bold" style="color: #fd7e14;">{{ $totalSystemUserEarning }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Reviews -->
    <div class="col-6 col-md-6 col-xl-3 mb-3">
        <div class="card card-stats card-round h-100" style="border-left: 4px solid #ffc107;">
            <div class="card-body">
                <div class="row align-items-center no-gutters">
                    <div class="col-auto col-icon">
                        <div class="icon-big text-center bubble-shadow-small" style="background-color: #ffc107; color: #fff;">
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                    <div class="col col-stats">
                        <div class="numbers">
                            <p class="card-category text-muted font-weight-bold"><b>{{ __('Total Reviews') }}</b></p>
                            <h4 class="card-title text-warning font-weight-bold">{{ $totalReview }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>

  <!-- ========================================== -->
  <!-- 7. FINANCE & SUPPORT (ROW OF 4 CARDS) -->
  <!-- ========================================== -->
  <div class="row">
    <!-- Total Transactions -->
    <div class="col-6 col-md-6 col-xl-3 mb-3">
        <div class="card card-stats card-round h-100" style="border-left: 4px solid #31ce36;">
            <div class="card-body">
                <div class="row align-items-center no-gutters">
                    <div class="col-auto col-icon">
                        <div class="icon-big text-center bubble-shadow-small" style="background-color: #31ce36; color: #fff;">
                            <i class="fas fa-exchange-alt"></i>
                        </div>
                    </div>
                    <div class="col col-stats">
                        <div class="numbers">
                            <p class="card-category text-muted font-weight-bold"><b>{{ __('Total Transactions') }}</b></p>
                            <h4 class="card-title font-weight-bold" style="color: #31ce36;">{{ $totalTransaction }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Store Fees / Deposits -->
    <div class="col-6 col-md-6 col-xl-3 mb-3">
        <div class="card card-stats card-round h-100" style="border-left: 4px solid #20c997;">
            <div class="card-body">
                <div class="row align-items-center no-gutters">
                    <div class="col-auto col-icon">
                        <div class="icon-big text-center bubble-shadow-small" style="background-color: #20c997; color: #fff;">
                            <i class="fas fa-hand-holding-usd"></i>
                        </div>
                    </div>
                    <div class="col col-stats">
                        <div class="numbers">
                            <p class="card-category text-muted font-weight-bold"><b>{{ __('Total Store Fees') }}</b></p>
                            <h4 class="card-title font-weight-bold" style="color: #20c997;">{{ $totalStoreFees }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Open Tickets -->
    <div class="col-6 col-md-6 col-xl-3 mb-3">
        <div class="card card-stats card-round h-100" style="border-left: 4px solid #17a2b8;">
            <div class="card-body">
                <div class="row align-items-center no-gutters">
                    <div class="col-auto col-icon">
                        <div class="icon-big text-center icon-info bubble-shadow-small">
                            <i class="fas fa-ticket-alt"></i>
                        </div>
                    </div>
                    <div class="col col-stats">
                        <div class="numbers">
                            <p class="card-category text-muted font-weight-bold"><b>{{ __('Open Tickets') }}</b></p>
                            <h4 class="card-title text-info font-weight-bold">{{ $totalTicket }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Tickets -->
    <div class="col-6 col-md-6 col-xl-3 mb-3">
        <div class="card card-stats card-round h-100" style="border-left: 4px solid #dc3545;">
            <div class="card-body">
                <div class="row align-items-center no-gutters">
                    <div class="col-auto col-icon">
                        <div class="icon-big text-center icon-danger bubble-shadow-small">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                    </div>
                    <div class="col col-stats">
                        <div class="numbers">
                            <p class="card-category text-muted font-weight-bold"><b>{{ __('Pending Tickets') }}</b></p>
                            <h4 class="card-title text-danger font-weight-bold">{{ $totalPendingTicket }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>

  <!-- Content Row -->
  <div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <div class="card-title">{{__('Monthly Product Sales Report')}} </div>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="multipleLineChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <div class="card-title">{{__('Monthly Earnings Report')}} </div>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="multipleLineChart2"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-title">{{__('Recent Orders')}}</div>
            </div>
            <div class="card-body p-0">
                @if ($recentOrders->count() > 0)
                <style>
                    .dash-orders-table { width:100%; border-collapse:collapse; }
                    .dash-orders-table th, .dash-orders-table td { padding:7px 12px; border-bottom:1px solid #f0f0f0; font-size:13px; vertical-align:middle; }
                    .dash-orders-table thead th { background:#f8f9fa; font-weight:700; font-size:12px; text-transform:uppercase; letter-spacing:.4px; }
                    .dash-orders-table tbody tr:hover { background:#f8f9fa; }
                    @media(max-width:575px){
                        .dash-orders-table thead { display:none; }
                        .dash-orders-table tr { display:block; border:1px solid #e9ecef; border-radius:8px; margin:6px 8px; padding:4px 2px; }
                        .dash-orders-table td { display:flex; justify-content:space-between; align-items:center; border:none; padding:4px 10px; font-size:12px; }
                        .dash-orders-table td::before { content:attr(data-label); font-weight:700; color:#6c757d; font-size:11px; text-transform:uppercase; margin-right:8px; flex-shrink:0; }
                    }
                </style>
                <table class="dash-orders-table">
                    <thead>
                        <tr>
                            <th>{{ __('Customer') }}</th>
                            <th>{{ __('Order ID') }}</th>
                            <th>{{ __('Payment') }}</th>
                            <th>{{ __('Total') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentOrders as $data)
                        <tr>
                            <td data-label="{{ __('Customer') }}">
                                <a href="{{route('back.user.show',$data->user_id)}}">{{ $data->user->displayName()}}</a>
                            </td>
                            <td data-label="{{ __('Order ID') }}">
                                <a href="{{route('back.order.invoice',$data->id)}}">{{ $data->transaction_number}}</a>
                            </td>
                            <td data-label="{{ __('Payment') }}">{{ $data->payment_method}}</td>
                            <td data-label="{{ __('Total') }}">{{$data->currency_sign}}{{PriceHelper::OrderTotal($data)}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <p class="text-center py-4 text-muted">{{ __('No Order Found') }}</p>
                @endif
            </div>
        </div>
    </div>

  </div>


</div>


@endsection

@section('scripts')
<script>

    multipleLineChart = document.getElementById('multipleLineChart').getContext('2d'),
    multipleLineChart2 = document.getElementById('multipleLineChart2').getContext('2d')


        var myMultipleLineChart = new Chart(multipleLineChart, {
			type: 'line',
			data: {
				labels: [{!! $order_days !!}],
				datasets: [{
					label: "Product Sales",
					borderColor: "#1d7af3",
					pointBorderColor: "#FFF",
					pointBackgroundColor: "#1d7af3",
					pointBorderWidth: 2,
					pointHoverRadius: 4,
					pointHoverBorderWidth: 1,
					pointRadius: 4,
					backgroundColor: 'transparent',
					fill: true,
					borderWidth: 2,
					data: [{!! $order_sales !!}]
				}]
			},
			options : {
				responsive: true,
				maintainAspectRatio: false,
				legend: {
					display: false
				},
				tooltips: {
					bodySpacing: 4,
					mode:"nearest",
					intersect: 0,
					position:"nearest",
					xPadding:10,
					yPadding:10,
					caretPadding:10
				},
				layout:{
					padding:{left:15,right:15,top:15,bottom:15}
				}
			}
		});

        var myMultipleLineChart2 = new Chart(multipleLineChart2, {
			type: 'line',
			data: {
				labels: [{!! $earning_days !!}],
				datasets: [ {
					label: "Earning"+' {{PriceHelper::adminCurrency()}}',
					borderColor: "#f3545d",
					pointBorderColor: "#FFF",
					pointBackgroundColor: "#f3545d",
					pointBorderWidth: 2,
					pointHoverRadius: 4,
					pointHoverBorderWidth: 1,
					pointRadius: 4,
					backgroundColor: 'transparent',
					fill: true,
					borderWidth: 2,
					data: [{!!$total_incomess!!}]
				}]
			},
			options : {
				responsive: true,
				maintainAspectRatio: false,
				legend: {
					display: false
				},
				tooltips: {
					bodySpacing: 4,
					mode:"nearest",
					intersect: 0,
					position:"nearest",
					xPadding:10,
					yPadding:10,
					caretPadding:10
				},
				layout:{
					padding:{left:15,right:15,top:15,bottom:15}
				}
			}
		});


</script>
@endsection






