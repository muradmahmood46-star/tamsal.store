<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>{{ $setting->title }} - {{ __('Seller Dashboard') }}</title>
    <meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />
    <link rel="icon" type="image/x-icon" href="{{ url('/core/public/storage/images/' . $setting->favicon) }}?v={{ !empty($setting->favicon) ? md5($setting->favicon) : time() }}" />

    <!-- Fonts and icons -->
    <script src="{{ asset('assets/back/js/plugin/webfont/webfont.min.js') }}"></script>
    <script id="setFont" data-src="{{ asset('assets/back/css/fonts.css') }}"
        src="{{ asset('assets/back/js/plugin/webfont/setfont.js') }}"></script>

    <!-- CSS Files -->
    <link rel="stylesheet" href="{{ asset('assets/back/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/back/css/azzara.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/back/css/tagify.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/back/css/sweetalert2/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/back/css/editor.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/back/css/bootstrap-iconpicker.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/back/css/magnific-popup.css') }}">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/back/css/custom.css') }}">

    @if (DB::table('languages')->where('type', 'Dashboard')->where('is_default', 1)->first() && DB::table('languages')->where('type', 'Dashboard')->where('is_default', 1)->first()->rtl == 1)
        <link rel="stylesheet" href="{{ asset('assets/back/css/rtl.css') }}">
    @endif

    <style>
        /* Mobile Width & Viewport Optimization for Vendor Panel */
        html, body {
            overflow-x: hidden !important;
            max-width: 100vw !important;
        }

        /* Topbar Header Icons (Hamburger 3 lines, Minimize, 3 Dots) always White */
        .main-header .logo-header .navbar-toggler,
        .main-header .logo-header .sidenav-toggler,
        .main-header .logo-header .navbar-toggler-icon,
        .main-header .logo-header .navbar-toggler-icon i,
        .main-header .logo-header .topbar-toggler,
        .main-header .logo-header .topbar-toggler i,
        .main-header .logo-header .btn-minimize,
        .main-header .logo-header .btn-minimize i,
        .main-header .logo-header .more,
        .main-header .logo-header .more i,
        .main-header .logo-header i.fa-bars,
        .main-header .logo-header i.fa-ellipsis-v,
        .main-header .navbar-toggler i,
        .main-header .sidenav-toggler i {
            color: #ffffff !important;
            fill: #ffffff !important;
        }

        .main-header .logo-header .btn-minimize:hover i,
        .main-header .logo-header .navbar-toggler:hover i,
        .main-header .logo-header .topbar-toggler:hover i {
            color: #ffffff !important;
            opacity: 0.9;
        }

        @media (max-width: 991.98px) {
            .wrapper {
                overflow-x: hidden !important;
                width: 100% !important;
                max-width: 100vw !important;
                min-height: 100vh !important;
            }

            .main-panel {
                width: 100% !important;
                max-width: 100% !important;
                float: none !important;
                margin-left: 0 !important;
                padding: 0 !important;
                overflow-x: hidden !important;
            }

            .main-panel > .content,
            .main-panel > .content-full {
                width: 100% !important;
                max-width: 100% !important;
                margin-top: 57px !important;
                padding: 0 !important;
                overflow-x: hidden !important;
            }

            .page-inner {
                padding: 10px 0 !important;
                width: 100% !important;
                max-width: 100% !important;
            }

            .container-fluid {
                padding-left: 10px !important;
                padding-right: 10px !important;
                width: 100% !important;
                max-width: 100% !important;
            }

            .row {
                margin-left: -5px !important;
                margin-right: -5px !important;
            }

            .row > [class*="col-"] {
                padding-left: 5px !important;
                padding-right: 5px !important;
            }

            .card {
                margin-bottom: 14px !important;
                border-radius: 8px !important;
                width: 100% !important;
                max-width: 100% !important;
                box-shadow: 0 1px 10px rgba(0, 0, 0, 0.05) !important;
            }

            .card .card-body {
                padding: 12px 10px !important;
            }

            .card .card-header {
                padding: 10px 12px !important;
            }

            .main-header .logo-header {
                width: 100% !important;
                padding: 0 10px !important;
                display: flex !important;
                align-items: center !important;
                justify-content: space-between !important;
            }

            .main-header .logo-header .navbar-toggler {
                display: block !important;
                opacity: 1 !important;
                color: #ffffff !important;
                padding: 6px 8px !important;
            }

            .main-header .logo-header .more {
                display: block !important;
                opacity: 1 !important;
                width: auto !important;
                color: #ffffff !important;
                margin-left: 8px !important;
            }

            .main-header .navbar-header .navbar-nav {
                flex-direction: row !important;
                flex-wrap: wrap !important;
                padding: 8px 10px !important;
                gap: 6px !important;
            }

            .main-header .navbar-header .navbar-nav .nav-item {
                margin-right: 0 !important;
                margin-bottom: 4px !important;
            }
        }

        @media (max-width: 767.98px) {
            .page-title, h3, .h3 {
                font-size: 16px !important;
                line-height: 1.35 !important;
            }

            .card-title, h4, .h4 {
                font-size: 15px !important;
            }

            h6, .h6 {
                font-size: 13.5px !important;
            }

            .d-sm-flex.justify-content-between,
            .d-flex.justify-content-between:not(.custom-control):not(.form-check):not(.nav) {
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 8px !important;
            }

            .d-sm-flex.justify-content-between > div,
            .d-flex.justify-content-between > div {
                width: 100% !important;
            }

            .d-sm-flex.justify-content-between .btn,
            .d-sm-flex.justify-content-between .btn-group,
            .d-sm-flex.justify-content-between > a.btn {
                width: 100% !important;
                margin-top: 4px !important;
                margin-left: 0 !important;
                margin-right: 0 !important;
                text-align: center !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
            }

            .btn-group,
            .btn-group-sm {
                display: flex !important;
                flex-wrap: wrap !important;
                width: 100% !important;
                gap: 4px !important;
            }

            .btn-group > .btn,
            .btn-group-sm > .btn,
            .btn-group > a,
            .btn-group-sm > a {
                flex: 1 1 calc(50% - 4px) !important;
                min-width: 110px !important;
                border-radius: 4px !important;
                margin: 0 !important;
                padding: 7px 6px !important;
                font-size: 11.5px !important;
                white-space: normal !important;
                line-height: 1.25 !important;
                text-align: center !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
            }

            .gd-responsive-table,
            .table-responsive,
            .dataTables_wrapper {
                width: 100% !important;
                max-width: 100% !important;
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch !important;
                display: block !important;
                border: 1px solid #e2e8f0 !important;
                border-radius: 6px !important;
                margin-bottom: 12px !important;
                background: #ffffff !important;
                padding: 6px !important;
            }

            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_filter {
                width: 100% !important;
                display: flex !important;
                justify-content: space-between !important;
                align-items: center !important;
                margin-bottom: 8px !important;
                float: none !important;
                text-align: left !important;
                font-size: 12px !important;
            }

            .dataTables_wrapper .dataTables_filter input {
                width: 100% !important;
                max-width: 160px !important;
                margin-left: 6px !important;
                height: 32px !important;
                font-size: 12px !important;
            }

            .dataTables_wrapper .dataTables_info,
            .dataTables_wrapper .dataTables_paginate {
                width: 100% !important;
                text-align: center !important;
                margin-top: 8px !important;
                float: none !important;
                font-size: 12px !important;
            }

            .dataTables_wrapper .dataTables_paginate ul.pagination {
                justify-content: center !important;
                flex-wrap: wrap !important;
                margin-top: 4px !important;
            }

            .gd-responsive-table table,
            .table-responsive table,
            .dataTables_wrapper table {
                width: 100% !important;
                min-width: 620px !important;
                max-width: none !important;
                margin-bottom: 0 !important;
            }

            .table th,
            .table td {
                padding: 9px 10px !important;
                font-size: 12.5px !important;
                vertical-align: middle !important;
            }

            .table th {
                white-space: nowrap !important;
            }

            .form-group {
                margin-bottom: 12px !important;
                padding: 0 !important;
                width: 100% !important;
            }

            .form-control,
            .custom-select {
                font-size: 13px !important;
                height: auto !important;
                padding: 8px 10px !important;
                max-width: 100% !important;
            }

            .input-group {
                flex-wrap: nowrap !important;
                width: 100% !important;
            }

            .input-group .form-control {
                min-width: 0 !important;
                width: 100% !important;
                font-size: 13px !important;
            }

            .input-group-prepend .input-group-text,
            .input-group-append .input-group-text {
                font-size: 11.5px !important;
                padding: 6px 8px !important;
                white-space: nowrap !important;
            }

            .custom-file,
            .custom-file-input,
            .custom-file-label {
                width: 100% !important;
                max-width: 100% !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
                white-space: nowrap !important;
                font-size: 12px !important;
            }

            .tags,
            .tagify,
            tagify {
                width: 100% !important;
                max-width: 100% !important;
                box-sizing: border-box !important;
            }

            #specifications-section .d-flex,
            .special-box .d-flex {
                flex-direction: column !important;
                gap: 6px !important;
                margin-bottom: 12px !important;
                padding: 10px !important;
                background: #f8fafc !important;
                border: 1px solid #e2e8f0 !important;
                border-radius: 6px !important;
            }

            #specifications-section .d-flex > div,
            .special-box .d-flex > div {
                width: 100% !important;
                margin-right: 0 !important;
            }

            #specifications-section .d-flex .flex-btn button,
            .special-box .d-flex .flex-btn button {
                width: 100% !important;
                padding: 7px !important;
                font-size: 13px !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
            }

            .btn-xs {
                font-size: 10.5px !important;
                padding: 3px 6px !important;
                margin-bottom: 4px !important;
            }

            .switch-primary {
                display: flex !important;
                align-items: flex-start !important;
                width: 100% !important;
                max-width: 100% !important;
                cursor: pointer;
                margin-bottom: 0 !important;
            }

            .switch-primary .switch-body,
            .switch-body {
                display: inline-block !important;
                flex-shrink: 0 !important;
                float: none !important;
                margin-right: 10px !important;
                margin-top: 2px !important;
            }

            .switch-primary .switch-text,
            .switch-text {
                flex: 1 1 auto !important;
                min-width: 0 !important;
                font-size: 13.5px !important;
                word-break: break-word !important;
                overflow-wrap: break-word !important;
                white-space: normal !important;
                line-height: 1.35 !important;
                max-width: 100% !important;
            }

            .note-editor.note-frame {
                width: 100% !important;
                max-width: 100% !important;
            }

            .note-toolbar {
                display: flex !important;
                flex-wrap: wrap !important;
                padding: 4px !important;
                gap: 2px !important;
            }

            .note-toolbar .note-btn-group {
                margin-right: 2px !important;
                margin-bottom: 2px !important;
            }

            .note-toolbar .note-btn {
                padding: 4px 6px !important;
                font-size: 11px !important;
            }

        /* =========================================================
           VENDOR DASHBOARD STAT CARDS — ZERO OVERLAP & BULLETPROOF FLEX
           ========================================================= */
        .card-stats {
            position: relative !important;
            overflow: hidden !important;
            border-radius: 10px !important;
        }

        .card-stats .card-body {
            padding: 12px 14px !important;
            display: flex !important;
            align-items: center !important;
        }

        .card-stats .card-body > .row,
        .card-stats .row {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            align-items: center !important;
            width: 100% !important;
            margin: 0 !important;
        }

        .card-stats .col-icon,
        .card-stats .col-auto.col-icon {
            flex: 0 0 46px !important;
            width: 46px !important;
            height: 46px !important;
            min-width: 46px !important;
            max-width: 46px !important;
            min-height: 46px !important;
            max-height: 46px !important;
            margin: 0 !important;
            padding: 0 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .card-stats .icon-big {
            width: 46px !important;
            height: 46px !important;
            min-width: 46px !important;
            max-width: 46px !important;
            min-height: 46px !important;
            max-height: 46px !important;
            border-radius: 10px !important;
            font-size: 20px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            margin: 0 !important;
            flex-shrink: 0 !important;
            line-height: 1 !important;
        }

        .card-stats .icon-big i {
            font-size: 20px !important;
            line-height: 1 !important;
            margin: 0 !important;
        }

        .card-stats .col-stats,
        .card-stats .col.col-stats {
            flex: 1 1 0% !important;
            min-width: 0 !important;
            padding-left: 12px !important;
            padding-right: 0 !important;
            margin: 0 !important;
            overflow: hidden !important;
            display: flex !important;
            align-items: center !important;
        }

        .card-stats .numbers {
            width: 100% !important;
            min-width: 0 !important;
            overflow: hidden !important;
            text-align: left !important;
        }

        .card-stats .numbers .card-category,
        .card-stats .numbers p {
            font-size: 12px !important;
            font-weight: 600 !important;
            line-height: 1.25 !important;
            margin: 0 0 3px 0 !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }

        .card-stats .numbers .card-title,
        .card-stats .numbers h4 {
            font-size: 16px !important;
            font-weight: 700 !important;
            line-height: 1.25 !important;
            margin: 0 !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }

        @media (max-width: 767.98px) {
            .card-stats .card-body {
                padding: 10px 8px !important;
            }

            .card-stats .col-icon,
            .card-stats .col-auto.col-icon {
                flex: 0 0 36px !important;
                width: 36px !important;
                height: 36px !important;
                min-width: 36px !important;
                max-width: 36px !important;
                min-height: 36px !important;
                max-height: 36px !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .card-stats .icon-big {
                width: 36px !important;
                height: 36px !important;
                min-width: 36px !important;
                max-width: 36px !important;
                min-height: 36px !important;
                max-height: 36px !important;
                border-radius: 8px !important;
                font-size: 16px !important;
                margin: 0 !important;
            }

            .card-stats .icon-big i {
                font-size: 16px !important;
                line-height: 1 !important;
            }

            .card-stats .col-stats,
            .card-stats .col.col-stats {
                padding-left: 8px !important;
                padding-right: 0 !important;
                min-width: 0 !important;
                flex: 1 1 0% !important;
            }

            .card-stats .numbers .card-category,
            .card-stats .numbers p {
                font-size: 11px !important;
                line-height: 1.2 !important;
                margin-bottom: 2px !important;
                white-space: nowrap !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
            }

            .card-stats .numbers .card-title,
            .card-stats .numbers h4 {
                font-size: 13.5px !important;
                line-height: 1.2 !important;
                white-space: nowrap !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
            }
        }

        @media (max-width: 380px) {
            .card-stats .card-body {
                padding: 8px 6px !important;
            }

            .card-stats .col-icon,
            .card-stats .col-auto.col-icon {
                flex: 0 0 30px !important;
                width: 30px !important;
                height: 30px !important;
                min-width: 30px !important;
                max-width: 30px !important;
                min-height: 30px !important;
                max-height: 30px !important;
            }

            .card-stats .icon-big {
                width: 30px !important;
                height: 30px !important;
                min-width: 30px !important;
                max-width: 30px !important;
                min-height: 30px !important;
                max-height: 30px !important;
                border-radius: 6px !important;
                font-size: 13px !important;
            }

            .card-stats .icon-big i {
                font-size: 13px !important;
            }

            .card-stats .col-stats,
            .card-stats .col.col-stats {
                padding-left: 6px !important;
            }

            .card-stats .numbers .card-category,
            .card-stats .numbers p {
                font-size: 9.5px !important;
            }

            .card-stats .numbers .card-title,
            .card-stats .numbers h4 {
                font-size: 12px !important;
            }
        }
    </style>

    @yield('styles')
</head>

<body>
    @php
        $sellerUser = Auth::user();
        $sellerProfile = $sellerUser ? $sellerUser->seller : null;
        $storeName = $sellerProfile && !empty($sellerProfile->shop_name) ? $sellerProfile->shop_name : ($sellerUser ? ($sellerUser->first_name . ' ' . $sellerUser->last_name) : 'Seller');
        $storeLogo = $sellerProfile ? $sellerProfile->logoUrl() : ($sellerUser ? $sellerUser->storeLogoUrl() : asset('storage/images/placeholder.png'));
        $userName = $sellerUser ? ($sellerUser->first_name . ' ' . $sellerUser->last_name) : 'Seller';
        $userEmail = $sellerUser ? $sellerUser->email : '';
        $userFirstName = $sellerUser ? $sellerUser->first_name : 'Seller';
    @endphp

    <div class="wrapper">
        <div class="main-header" style="background: linear-gradient(135deg, #1572e8 0%, #0d56b3 100%);">
            <!-- Logo Header -->
            <div class="logo-header">
                <a href="{{ route('seller.dashboard') }}" class="logo">
                    <img src="{{ $setting->logo ? url('/core/public/storage/images/' . $setting->logo) : url('/core/public/storage/images/placeholder.png') }}"
                        alt="brand" class="navbar-brand" style="max-height: 40px;">
                </a>
                <button class="navbar-toggler sidenav-toggler ml-auto" type="button" data-toggle="collapse"
                    data-target="collapse" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon">
                        <i class="fa fa-bars text-white" style="color: #ffffff !important; font-size: 20px;"></i>
                    </span>
                </button>
                <button class="topbar-toggler more"><i class="fa fa-ellipsis-v text-white" style="color: #ffffff !important; font-size: 20px;"></i></button>
                <div class="navbar-minimize">
                    <button class="btn btn-minimize">
                        <i class="fa fa-bars text-white" style="color: #ffffff !important; font-size: 18px;"></i>
                    </button>
                </div>
            </div>
            <!-- End Logo Header -->

            <!-- Navbar Header -->
            <nav class="navbar navbar-header navbar-expand-lg">
                <div class="container-fluid">
                    <ul class="navbar-nav topbar-nav ml-md-auto align-items-center">
                        <li class="nav-item mr-3">
                            <span class="badge badge-warning text-dark font-weight-bold px-3 py-2">
                                <i class="fas fa-store mr-1"></i> {{ $storeName }}
                            </span>
                        </li>
                        <li class="nav-item mr-3">
                            <a class="btn btn-sm btn-outline-light py-1 text-white font-weight-bold" title="website"
                                href="{{ route('front.index') }}" target="_blank">
                                <i class="fas fa-globe mr-1"></i> {{ __('View Store') }}
                            </a>
                        </li>
                        <li class="nav-item mr-3">
                            <a class="btn btn-sm btn-info py-1 text-white font-weight-bold"
                                href="{{ route('user.dashboard') }}">
                                <i class="fas fa-user mr-1"></i> {{ __('Customer Area') }}
                            </a>
                        </li>

                        <li class="nav-item dropdown hidden-caret">
                            <a class="dropdown-toggle profile-pic" data-toggle="dropdown"
                                href="#" aria-expanded="false">
                                <div class="avatar-sm">
                                    <img src="{{ $storeLogo }}"
                                        alt="..." class="avatar-img rounded-circle border border-white" style="width: 38px; height: 38px; object-fit: cover;">
                                </div>
                            </a>
                            <ul class="dropdown-menu dropdown-user animated fadeIn">
                                <li>
                                    <div class="user-box">
                                        <div class="avatar-lg"><img
                                                src="{{ $storeLogo }}"
                                                alt="image profile" class="avatar-img rounded" style="width: 60px; height: 60px; object-fit: cover;"></div>

                                        <div class="u-text">
                                            <h4>{{ $storeName }}</h4>
                                            <p class="text-muted">{{ $userEmail }}</p>
                                            <a href="{{ route('seller.profile') }}" class="btn btn-secondary btn-sm">{{ __('Store Profile') }}</a>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="dropdown-divider"></div>
                                    @if(session('admin_impersonating_vendor'))
                                        <a class="dropdown-item font-weight-bold text-danger" href="{{ route('seller.impersonate.leave') }}"><i class="fas fa-user-shield mr-2"></i> {{ __('Return to Admin Panel') }}</a>
                                        <div class="dropdown-divider"></div>
                                    @endif
                                    <a class="dropdown-item" href="{{ route('seller.profile') }}"><i class="fas fa-cogs mr-2"></i> {{ __('Store Settings') }}</a>
                                    <a class="dropdown-item" href="{{ route('user.dashboard') }}"><i class="fas fa-user mr-2"></i> {{ __('Customer Dashboard') }}</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item text-danger" href="{{ route('user.logout') }}"><i class="fas fa-sign-out-alt mr-2"></i> {{ __('Logout') }}</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
            <!-- End Navbar -->
        </div>

        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-background"></div>
            <div class="sidebar-wrapper scrollbar-inner">
                <div class="sidebar-content">
                    <div class="user">
                        <div class="avatar-sm float-left mr-2">
                            <img src="{{ $storeLogo }}"
                                alt="..." class="avatar-img rounded-circle border shadow-sm" style="width: 40px; height: 40px; object-fit: cover;">
                        </div>
                        <div class="info">
                            <a data-toggle="collapse" href="#collapseSeller" aria-expanded="true">
                                <span>
                                    {{ $storeName }}
                                    <span class="user-level"><i class="fas fa-certificate text-success mr-1"></i>{{ __('Verified Seller') }}</span>
                                </span>
                            </a>
                        </div>
                    </div>

                    @include('master.inc.seller_sitebar')

                    <div class="sidebar-footer text-primary d-block text-center pt-3">
                        <span class="d-inline-block small text-muted"><b>{{ $storeName }}</b></span>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Sidebar -->

        <div class="main-panel">
            @if(session('admin_impersonating_vendor'))
                <div class="alert alert-warning border-0 rounded-0 mb-0 shadow py-2 px-4 d-flex align-items-center justify-content-between" style="background: linear-gradient(90deg, #ffc107, #ff9800); color: #000; z-index: 9999; font-size: 13.5px; position: sticky; top: 0;">
                    <div>
                        <i class="fas fa-user-shield mr-2"></i>
                        <strong>{{ __('Admin Mode:') }}</strong> {{ __('You are currently logged in as store') }} <strong>{{ session('impersonated_store_name', $storeName) }}</strong>.
                    </div>
                    <a href="{{ route('seller.impersonate.leave') }}" class="btn btn-dark btn-sm font-weight-bold shadow-sm py-1 px-3">
                        <i class="fas fa-arrow-left mr-1"></i> {{ __('Return to Admin Panel') }}
                    </a>
                </div>
            @endif
            <div class="content">
                <div class="page-inner">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <script>
        var summernot_upload_url = '{{ route('back.summernote.image.upload') }}';
    </script>
    <!-- Core JS Files -->
    <script src="{{ asset('assets/back/js/core/jquery.3.6.0.min.js') }}"></script>
    <script src="{{ asset('assets/back/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('assets/back/js/core/bootstrap.min.js') }}"></script>

    <!-- jQuery UI -->
    <script src="{{ asset('assets/back/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/back/js/plugin/jquery-ui-touch-punch/jquery.ui.touch-punch.min.js') }}"></script>

    <!-- jQuery Scrollbar -->
    <script src="{{ asset('assets/back/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>

    <!-- Moment JS -->
    <script src="{{ asset('assets/back/js/plugin/moment/moment.min.js') }}"></script>

    <!-- Datatables -->
    <script src="{{ asset('assets/back/js/plugin/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/back/js/plugin/datatables/dataTables.bootstrap4.min.js') }}"></script>

    <!-- sweetalert2 -->
    <script src="{{ asset('assets/back/js/plugin/sweetalert2/sweetalert2.min.js') }}"></script>

    <!-- Chartjs -->
    <script src="{{ asset('assets/back/js/plugin/chart.min.js') }}"></script>

    <!-- Editor -->
    <script src="{{ asset('assets/back/js/plugin/editor.js') }}"></script>
    <script src="{{ asset('assets/back/js/plugin/datepicker/bootstrap-datetimepicker.min.js') }}"></script>

    <!-- Tagify -->
    <script src="{{ asset('assets/back/js/tagify.js') }}"></script>

    <!-- JS Color -->
    <script src="{{ asset('assets/back/js/jscolor.js') }}"></script>

    <!-- Magnific Popup -->
    <script src="{{ asset('assets/back/js/jquery.magnific-popup.min.js') }}"></script>

    <!-- Azzara JS -->
    <script src="{{ asset('assets/back/js/ready.min.js') }}"></script>

    @yield('scripts')
    <script src="{{ asset('assets/back/js/custom.js') }}"></script>
</body>

</html>
