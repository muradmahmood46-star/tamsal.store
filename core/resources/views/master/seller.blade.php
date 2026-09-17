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
                        <i class="fa fa-bars"></i>
                    </span>
                </button>
                <button class="topbar-toggler more"><i class="fa fa-ellipsis-v"></i></button>
                <div class="navbar-minimize">
                    <button class="btn btn-minimize">
                        <i class="fa fa-bars"></i>
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
