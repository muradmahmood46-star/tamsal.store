<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $setting->title }}</title>
    @php
        $favPath = $setting->favicon ?? '';
        $favUrl = !empty($favPath)
            ? (\Illuminate\Support\Str::startsWith($favPath, 'images/') ? url('/core/public/storage/' . $favPath) : url('/core/public/storage/images/' . $favPath))
            : asset('favicon.ico');
        $favVersion = !empty($favPath) ? md5($favPath) : time();
    @endphp
    <link rel="icon" type="image/x-icon" href="{{ $favUrl }}?v={{ $favVersion }}" />

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

    <!-- Lottie Web Animation Player -->
    <!-- Lottie & dotLottie Web Animation Players -->
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
    <script type="module" src="https://unpkg.com/@dotlottie/player-component@latest/dist/dotlottie-player.mjs"></script>

    @if (DB::table('languages')->where('type', 'Dashboard')->where('is_default', 1)->first()->rtl == 1)
        <link rel="stylesheet" href="{{ asset('assets/back/css/rtl.css') }}">
    @endif

    @yield('styles')

    <style>
        /* Force white icons on admin header togglers & logo header */
        .main-header .logo-header .navbar-toggler,
        .main-header .logo-header .navbar-toggler i,
        .main-header .logo-header .navbar-toggler-icon,
        .main-header .logo-header .navbar-toggler-icon i,
        .main-header .logo-header .more,
        .main-header .logo-header .more i,
        .main-header .logo-header .topbar-toggler,
        .main-header .logo-header .topbar-toggler i,
        .main-header .logo-header .btn-minimize,
        .main-header .logo-header .btn-minimize i,
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

        /* General Dropdown list & Notification Scrollable */
        .dropdown-list {
            width: 350px !important;
            max-width: 90vw !important;
            border-radius: 10px !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
            padding: 0 !important;
            overflow: hidden !important;
        }

        .notf-list-scrollable {
            max-height: 300px;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 #f8fafc;
        }

        .notf-list-scrollable::-webkit-scrollbar {
            width: 5px;
        }

        .notf-list-scrollable::-webkit-scrollbar-track {
            background: #f8fafc;
        }

        .notf-list-scrollable::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        .notf-list-scrollable::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
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
                transform: none !important;
            }

            html.nav_open .main-panel,
            html.nav_open .main-header {
                transform: none !important;
            }

            .main-panel > .content,
            .main-panel > .content-full {
                width: 100% !important;
                max-width: 100% !important;
                margin-top: 58px !important;
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

            /* Header & Logo Bar */
            .main-header {
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                right: 0 !important;
                width: 100% !important;
                z-index: 10020 !important;
                background: linear-gradient(135deg, #1572e8 0%, #0d56b3 100%) !important;
            }

            .main-header .logo-header {
                width: 100% !important;
                height: 58px !important;
                padding: 0 12px !important;
                display: flex !important;
                align-items: center !important;
                justify-content: space-between !important;
                background: linear-gradient(135deg, #1572e8 0%, #0d56b3 100%) !important;
            }

            .main-header .logo-header .navbar-toggler {
                display: block !important;
                opacity: 1 !important;
                color: #ffffff !important;
                padding: 6px 10px !important;
                border: 0 !important;
                background: transparent !important;
            }

            .main-header .logo-header .navbar-toggler:focus {
                outline: none !important;
            }

            .main-header .logo-header .more {
                display: block !important;
                opacity: 1 !important;
                width: auto !important;
                color: #ffffff !important;
                margin-left: 8px !important;
                padding: 6px 10px !important;
                border: 0 !important;
                background: transparent !important;
            }

            .main-header .logo-header .more:focus {
                outline: none !important;
            }

            /* Topbar dropdown drawer (3 dots menu) */
            .main-header .navbar-header {
                position: absolute !important;
                top: 58px !important;
                left: 0 !important;
                right: 0 !important;
                width: 100% !important;
                max-height: calc(100vh - 65px) !important;
                overflow-y: auto !important;
                -webkit-overflow-scrolling: touch !important;
                background: #0d56b3 !important;
                box-shadow: 0 10px 25px rgba(0,0,0,0.25) !important;
                border-radius: 0 0 12px 12px !important;
                transform: translate3d(0, -150%, 0) !important;
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
                padding: 0 !important;
                z-index: 10015 !important;
                display: block !important;
                visibility: hidden !important;
            }

            html.topbar_open .main-header .navbar-header {
                transform: translate3d(0, 0, 0) !important;
                visibility: visible !important;
            }

            .main-header .navbar-header .navbar-nav {
                display: flex !important;
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 8px !important;
                padding: 14px 16px !important;
                width: 100% !important;
            }

            .main-header .navbar-header .navbar-nav .nav-item {
                margin-right: 0 !important;
                margin-bottom: 0 !important;
                width: 100% !important;
            }

            .main-header .navbar-header .navbar-nav .nav-item .btn,
            .main-header .navbar-header .navbar-nav .nav-item .badge {
                width: 100% !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                padding: 8px 12px !important;
                font-size: 13px !important;
                border-radius: 6px !important;
            }

            .main-header .navbar-header .navbar-nav .nav-item > a.nav-link {
                width: 100% !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                padding: 8px 12px !important;
                background: rgba(255,255,255,0.12) !important;
                border-radius: 8px !important;
                color: #ffffff !important;
                font-size: 13px !important;
            }

            .main-header .navbar-header .navbar-nav .nav-item.dropdown .profile-pic {
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                padding: 8px !important;
                background: rgba(255,255,255,0.12) !important;
                border-radius: 8px !important;
            }

            .main-header .navbar-header .dropdown-user,
            .main-header .navbar-header .dropdown-list {
                position: static !important;
                width: 100% !important;
                max-width: 100% !important;
                float: none !important;
                margin-top: 8px !important;
                box-shadow: 0 4px 15px rgba(0,0,0,0.15) !important;
                border-radius: 10px !important;
                background: #ffffff !important;
                overflow: hidden !important;
            }

            .main-header .navbar-header .dropdown-list .notf-list-scrollable {
                max-height: 280px !important;
                overflow-y: auto !important;
                -webkit-overflow-scrolling: touch !important;
            }

            .main-header .navbar-header .dropdown-user .user-box {
                padding: 12px 14px !important;
                display: flex !important;
                align-items: center !important;
            }

            .main-header .navbar-header .dropdown-user .dropdown-item {
                padding: 10px 16px !important;
                font-size: 13px !important;
                color: #334155 !important;
            }

            /* Responsive Sidebar Drawer (3 lines Hamburger menu) */
            .sidebar {
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                bottom: 0 !important;
                width: 280px !important;
                max-width: 85vw !important;
                background: #ffffff !important;
                z-index: 10050 !important;
                box-shadow: 0 0 30px rgba(0,0,0,0.3) !important;
                transform: translate3d(-100%, 0, 0) !important;
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
                overflow-y: auto !important;
                -webkit-overflow-scrolling: touch !important;
                padding-top: 0 !important;
            }

            html.nav_open .sidebar {
                transform: translate3d(0, 0, 0) !important;
            }

            .sidebar .sidebar-wrapper {
                width: 100% !important;
                max-height: 100vh !important;
                height: 100vh !important;
                padding-top: 15px !important;
                padding-bottom: 70px !important;
                overflow-y: auto !important;
            }

            .sidebar .user {
                margin: 10px 12px 15px 12px !important;
                padding: 12px 14px !important;
                background: #f8fafc !important;
                border-radius: 10px !important;
                border: 1px solid #e2e8f0 !important;
                display: flex !important;
                align-items: center !important;
            }

            .sidebar .nav {
                margin-top: 10px !important;
                padding: 0 10px !important;
            }

            .sidebar .nav > .nav-item > a {
                padding: 11px 16px !important;
                font-size: 14px !important;
                font-weight: 600 !important;
                color: #475569 !important;
                border-radius: 8px !important;
                display: flex !important;
                align-items: center !important;
            }

            .sidebar .nav > .nav-item.active > a,
            .sidebar .nav > .nav-item > a:hover {
                background: #eff6ff !important;
                color: #1572e8 !important;
            }

            .sidebar .nav-collapse {
                margin: 4px 0 8px 15px !important;
                padding: 4px 0 6px 12px !important;
                border-left: 2px solid #e2e8f0 !important;
                background: transparent !important;
            }

            /* Backdrop Overlay */
            .sidebar-overlay-backdrop {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 10040;
                backdrop-filter: blur(2px);
                transition: opacity 0.3s ease;
            }

            html.nav_open .sidebar-overlay-backdrop {
                display: block !important;
            }
        }

        /* =========================================================
           ADMIN DASHBOARD STAT CARDS — SAME SIZE, STYLE & DESIGN AS VENDOR PANEL
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

</head>

<body>
    <div class="wrapper">
        <div class="main-header" style="background: linear-gradient(135deg, #1572e8 0%, #0d56b3 100%);">
            <!-- Logo Header -->
            <div class="logo-header">

                <a href="{{ route('back.dashboard') }}" class="logo">
                    <img src="{{ $setting->logo ? url('/core/public/storage/images/' . $setting->logo) : url('/core/public/storage/images/placeholder.png') }}"
                        alt="navbar brand" class="navbar-brand">
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
                        <li class="nav-item mr-4">
                            <a class="btn btn-sm btn-primary py-1 text-white" title="website"
                                href="{{ route('front.index') }}" target="_blank">
                                <b> {{ __('View Website') }}</b>
                            </a>
                        </li>
                        <!-- Nav Item - Alerts -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-bell fa-fw"></i>
                                <!-- Counter - Alerts -->
                                <span
                                    class="badge badge-danger badge-counter">{{ App\Models\Notification::countRegistration() + App\Models\Notification::countOrder() }}</span>
                            </a>
                            <!-- Dropdown - Alerts -->
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="alertsDropdown" id="display-notf"
                                data-href={{ route('back.notifications') }}>
                                @include('back.notification.index')
                            </div>
                        </li>

                        <li class="nav-item dropdown hidden-caret">
                            <a class="dropdown-toggle profile-pic" data-toggle="dropdown"
                                href="{{ route('back.dashboard') }}" aria-expanded="false">
                                <div class="avatar-sm avatar avatar-sm">
                                    <img src="{{ Auth::guard('admin')->user()->photo ? url('/core/public/storage/images/' . Auth::guard('admin')->user()->photo) : url('/core/public/storage/images/noimage.png') }}"
                                        alt="..." class="avatar-img rounded-circle">
                                </div>
                            </a>
                            <ul class="dropdown-menu dropdown-user animated fadeIn">
                                <li>
                                    <div class="user-box">
                                        <div class="avatar-lg"><img
                                                src="{{ Auth::guard('admin')->user()->photo ? url('/core/public/storage/images/' . Auth::guard('admin')->user()->photo) : url('/core/public/storage/images/noimage.png') }}"
                                                alt="image profile" class="avatar-img rounded"></div>

                                        <div class="u-text">
                                            <h4>{{ Auth::guard('admin')->user()->name }}</h4>
                                            <p class="text-muted">{{ Auth::guard('admin')->user()->email }}</p><a
                                                href="{{ route('back.profile') }}"
                                                class="btn  btn-secondary btn-sm">{{ __('Update Profile') }}</a>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item"
                                        href="{{ route('back.profile') }}">{{ __('Update Profile') }}</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item"
                                        href="{{ route('back.password') }}">{{ __('Change Password') }}</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="{{ route('back.logout') }}">{{ __('Logout') }}</a>
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
                            <a href="{{ route('back.profile') }}" title="{{ __('Update Profile Photo') }}">
                                <img src="{{ Auth::guard('admin')->user()->photo ? url('/core/public/storage/images/' . Auth::guard('admin')->user()->photo) : url('/core/public/storage/images/noimage.png') }}"
                                    alt="..." class="avatar-img rounded-circle shadow-sm">
                            </a>
                        </div>
                        <div class="info">
                            <a href="{{ route('back.profile') }}" title="{{ __('Update Profile') }}">
                                <span>
                                    {{ Auth::guard('admin')->user()->name }}
                                    <span class="user-level">{{ __('Administrator') }}</span>
                                </span>
                            </a>
                        </div>
                    </div>

                    @if (Auth::guard('admin')->user()->id == 1)
                        @include('master.inc.super')
                    @else
                        @include('master.inc.normal')
                    @endif
                    <div class="sidebar-footer text-primary d-block text-center pt-3">
                        <span class="d-inline-block"><b>{{ __('Version') }} {{ $setting->version }}</b></span>
                    </div>

                </div>
            </div>
        </div>
        <!-- End Sidebar -->
        <div class="sidebar-overlay-backdrop"></div>

        <div class="main-panel">
            <div class="content">
                <div class="page-inner">
                    @yield('content')
                </div>
            </div>
        </div>

    </div>
    @php
        $mainbs = [];
        $mainbs['is_announcement'] = $setting->is_announcement;
        $mainbs['announcement_delay'] = $setting->announcement_delay;
        $mainbs['overlay'] = $setting->overlay;
        $mainbs = json_encode($mainbs);

    @endphp

    <script>
        var mainbs = {!! $mainbs !!};
        var summernot_upload_url = '{{ route('back.summernote.image.upload') }}';
    </script>
    <!--   Core JS Files   -->
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

    <!-- Bootstrap Notify -->
    <script src="{{ asset('assets/back/js/plugin/bootstrap-notify/bootstrap-notify.min.js') }}"></script>

    <!-- sweetalert2 -->
    <script src="{{ asset('assets/back/js/plugin/sweetalert2/sweetalert2.min.js') }}"></script>

    <!-- Menu Builder -->
    <script src="{{ asset('assets/back/js/plugin/jquery-menu-editor.js') }}"></script>

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

    <!-- Sortable -->
    <script src="{{ asset('assets/back/js/sortable.js') }}"></script>

    <!-- Icon Picker -->
    <script src="{{ asset('assets/back/js/bootstrap-iconpicker.bundle.min.js') }}"></script>

    <!-- Azzara JS -->
    <script src="{{ asset('assets/back/js/ready.min.js') }}"></script>

    <!-- Custom JS -->

    @yield('scripts')
    <script src="{{ asset('assets/back/js/custom.js') }}?v={{ time() }}"></script>

    <script>
        $(document).ready(function() {
            // Dismiss sidebar & topbar when tapping on backdrop on mobile
            $(document).on('click', '.sidebar-overlay-backdrop', function() {
                $('html').removeClass('nav_open');
                $('html').removeClass('topbar_open');
                $('.sidenav-toggler').removeClass('toggled');
                $('.topbar-toggler').removeClass('toggled');
            });

            // Auto-collapse sidebar when a direct page link is clicked on mobile
            if ($(window).width() <= 991.98) {
                $('.sidebar .nav-item a:not([data-toggle="collapse"])').on('click', function() {
                    $('html').removeClass('nav_open');
                    $('.sidenav-toggler').removeClass('toggled');
                });
            }
        });
    </script>
</body>

</html>
