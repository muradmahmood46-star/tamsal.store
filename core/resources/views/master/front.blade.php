<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if (url()->current() == route('front.index'))
        <title>{{ $setting->title }}{{ !empty($setting->home_page_title) ? ' - ' . $setting->home_page_title : '' }}</title>
    @else
        <title>{{ $setting->title }} - @yield('title')</title>
    @endif

    <!-- SEO Meta Tags-->
    @if (url()->current() == route('front.index'))
        <meta name="author" content="GeniusDevs">
        <meta name="distribution" content="web">
        <meta name="description" content="{{ $setting->meta_description }}">
        <meta name="keywords" content="{{ $setting->meta_keywords }}">
        <meta name="image" content="{{ url('/core/public/storage/images/' . $setting->meta_image) }}">
        <meta property="og:title" content="{{ $setting->title}}">
        <meta property="og:description" content="{{ $setting->meta_description }}">
        <meta property="og:image" content="{{ url('/core/public/storage/images/' . $setting->meta_image) }}">
        <meta property="og:image:secure_url" content="{{ url('/core/public/storage/images/' . $setting->meta_image) }}" />
        <meta property="og:image:type" content="image/jpeg" />
        <meta property="og:image:width" content="1200" />
        <meta property="og:image:height" content="627" />
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:site_name" content="{{ $setting->title }}">
        <meta property="og:type" content="website">
    @else
        @yield('meta')
    @endif

    <!-- Mobile Specific Meta Tag-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <!-- Favicon Icons-->
    <link rel="icon" href="{{ url('/core/public/storage/images/' . $setting->favicon) }}?v={{ !empty($setting->favicon) ? md5($setting->favicon) : time() }}">
    <link rel="shortcut icon" href="{{ url('/core/public/storage/images/' . $setting->favicon) }}?v={{ !empty($setting->favicon) ? md5($setting->favicon) : time() }}">
    <link rel="apple-touch-icon" href="{{ url('/core/public/storage/images/' . $setting->favicon) }}?v={{ !empty($setting->favicon) ? md5($setting->favicon) : time() }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ url('/core/public/storage/images/' . $setting->favicon) }}?v={{ !empty($setting->favicon) ? md5($setting->favicon) : time() }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ url('/core/public/storage/images/' . $setting->favicon) }}?v={{ !empty($setting->favicon) ? md5($setting->favicon) : time() }}">
    <link rel="apple-touch-icon" sizes="167x167" href="{{ url('/core/public/storage/images/' . $setting->favicon) }}?v={{ !empty($setting->favicon) ? md5($setting->favicon) : time() }}">

    <!-- Vendor Styles including: Bootstrap, Font Icons, Plugins, etc.-->
    <link rel="stylesheet" media="screen" href="{{ asset('assets/front/css/plugins.min.css') }}">

    @yield('styleplugins')
    @yield('styles')

    <link id="mainStyles" rel="stylesheet" media="screen" href="{{ asset('assets/front/css/styles.min.css') }}">

    <link id="mainStyles" rel="stylesheet" media="screen" href="{{ asset('assets/front/css/responsive.css') }}">
    <!-- Color css -->
    <link
        href="{{ asset('assets/front/css/color.php?primary_color=') . str_replace('#', '', $setting->primary_color) }}"
        rel="stylesheet">

    <!-- Modernizr-->
    <script src="{{ asset('assets/front/js/modernizr.min.js') }}"></script>

    @if (DB::table('languages')->where('is_default', 1)->first()->rtl == 1)
        <link rel="stylesheet" href="{{ asset('assets/front/css/rtl.css') }}">
    @endif
    <style>
        {{ $setting->custom_css }}

        /* User & Header Dropdown Menu Styling Fix - High Contrast & Visible */
        .menu-top-area .t-h-dropdown-menu,
        .t-h-dropdown .t-h-dropdown-menu {
            background: #ffffff !important;
            border-radius: 8px !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.18) !important;
            border: 1px solid #e2e8f0 !important;
            padding: 6px 0 !important;
            z-index: 99999 !important;
        }

        .menu-top-area .t-h-dropdown-menu a,
        .t-h-dropdown .t-h-dropdown-menu a {
            color: #1e293b !important; /* Bold dark text so it is always 100% visible */
            font-size: 13px !important;
            font-weight: 600 !important;
            padding: 8px 16px !important;
            display: flex !important;
            align-items: center !important;
            transition: all 0.2s ease !important;
            text-decoration: none !important;
            line-height: 1.5 !important;
            background: #ffffff !important;
            border: none !important;
        }

        .menu-top-area .t-h-dropdown-menu a i,
        .t-h-dropdown .t-h-dropdown-menu a i {
            color: #64748b !important;
            font-size: 11px !important;
            margin-right: 8px !important;
            display: inline-block !important;
        }

        .menu-top-area .t-h-dropdown-menu a:hover,
        .t-h-dropdown .t-h-dropdown-menu a:hover {
            background-color: #f1f5f9 !important;
            color: #2563eb !important; /* Vibrant primary blue on hover */
        }

        .menu-top-area .t-h-dropdown-menu a:hover i,
        .t-h-dropdown .t-h-dropdown-menu a:hover i {
            color: #2563eb !important;
        }

        .t-h-dropdown .main-link {
            cursor: pointer !important;
        }

        .t-h-dropdown-menu.show-dropdown {
            opacity: 1 !important;
            visibility: visible !important;
            display: block !important;
        }
    </style>
    {{-- Google AdSense Start --}}
    @if ($setting->is_google_adsense == '1')
        {!! $setting->google_adsense !!}
    @endif
    {{-- Google AdSense End --}}

    {{-- Google AnalyTics Start --}}
    @if ($setting->is_google_analytics == '1')
        {!! $setting->google_analytics !!}
    @endif
    {{-- Google AnalyTics End --}}

    {{-- Facebook pixel  Start --}}
    @if ($setting->is_facebook_pixel == '1')
        {!! $setting->facebook_pixel !!}
    @endif
    {{-- Facebook pixel End --}}

</head>
<!-- Body-->

<body
    class="
@if ($setting->theme == 'theme1') body_theme1
@elseif($setting->theme == 'theme2')
body_theme2
@elseif($setting->theme == 'theme3')
body_theme3
@elseif($setting->theme == 'theme4')
body_theme4 @endif
">
    @if ($setting->is_loader == 1)
        <!-- Preloader Start -->
        @if ($setting->is_loader == 1)
            <div id="preloader">
                <img src="{{ url('/core/public/storage/images/' . $setting->loader) }}" alt="{{ __('Loading...') }}">
            </div>
        @endif

        <!-- Preloader endif -->
    @endif

    <!-- Header-->

    <header class="site-header navbar-sticky">
        <div class="menu-top-area">
            <div class="container">
                <!-- Desktop Topbar (Hidden on Mobile) -->
                <div class="row d-none d-lg-flex">
                    <div class="col-md-4">
                        <div class="t-m-s-a">
                            <a class="track-order-link" href="{{ route('front.order.track') }}"><i class="icon-map-pin"></i>{{ __('Track Order') }}</a>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="right-area">
                            <div class="t-h-dropdown ">
                                <a class="main-link" href="#">{{ __('Currency') }}<i class="icon-chevron-down"></i></a>
                                <div class="t-h-dropdown-menu">
                                    @foreach (DB::table('currencies')->get() as $currency)
                                        <a class="{{ Session::get('currency') == $currency->id ? 'active' : ($currency->is_default == 1 && !Session::has('currency') ? 'active' : '') }}"
                                            href="{{ route('front.currency.setup', $currency->id) }}"><i class="icon-chevron-right pr-2"></i>{{ $currency->name }}</a>
                                    @endforeach
                                </div>
                            </div>
                            <div class="login-register ">
                                @if (!Auth::user())
                                    <a class="track-order-link mr-0" href="{{ route('user.login') }}">{{ __('Login') }}</a>
                                @else
                                    <div class="t-h-dropdown">
                                        @php
                                            $headerUserUnread = \App\Models\Conversation::where('user_id', Auth::id())->where('user_unread_count', '>', 0)->where('deleted_by_user', 0)->sum('user_unread_count');
                                        @endphp
                                        <a class="main-link" href="javascript:;">
                                            {{ Auth::user()->first_name }}
                                            @if($headerUserUnread > 0)
                                                <span class="badge badge-success ml-1" style="font-size: 10px; padding: 2px 5px; border-radius: 10px;">{{ $headerUserUnread }}</span>
                                            @endif
                                            <i class="icon-chevron-down"></i>
                                        </a>
                                        <div class="t-h-dropdown-menu">
                                            <a href="{{ route('user.dashboard') }}"><i class="icon-chevron-right pr-2"></i>{{ __('Dashboard') }}</a>
                                            <a href="{{ route('user.message.index') }}">
                                                <i class="icon-chevron-right pr-2"></i>{{ __('My Chats & Messages') }}
                                                @if($headerUserUnread > 0)
                                                    <span class="badge badge-success ml-1 font-weight-bold" style="font-size: 10px; border-radius: 10px; padding: 2px 6px;">{{ $headerUserUnread }}</span>
                                                @endif
                                            </a>
                                            <a href="{{ route('user.profile') }}"><i class="icon-chevron-right pr-2"></i>{{ __('Profile') }}</a>
                                            @if(Auth::user()->isSeller())
                                                <a href="{{ route('seller.dashboard') }}" class="text-primary font-weight-bold"><i class="icon-chevron-right pr-2"></i>{{ __('Seller Dashboard') }}</a>
                                            @else
                                                <a href="{{ route('user.store.apply') }}"><i class="icon-chevron-right pr-2"></i>{{ __('Open Shop / List Product') }}</a>
                                            @endif
                                            <a href="{{ route('user.ticket') }}"><i class="icon-chevron-right pr-2"></i>{{ __('Support Ticket') }}</a>
                                            <a href="{{ route('user.order.index') }}"><i class="icon-chevron-right pr-2"></i>{{ __('Orders') }}</a>
                                            <a href="{{ route('user.logout') }}"><i class="icon-chevron-right pr-2"></i>{{ __('Logout') }}</a>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mobile Topbar (Hidden on Desktop) -->
                <div class="d-flex d-lg-none justify-content-between align-items-center w-100" style="font-size: 11px; padding: 5px 0 8px 0;">
                    
                    <!-- Left: Track Order -->
                    <div class="d-flex align-items-center" style="gap: 4px;">
                        <a class="track-order-link" href="{{ route('front.order.track') }}" style="margin:0; padding:0; line-height: 1.2;"><i class="icon-map-pin" style="font-size:10px;"></i>{{ __('Track') }}</a>
                    </div>

                    <!-- Middle: Currency -->
                    <div class="d-flex justify-content-center">
                        <div class="t-h-dropdown" style="margin:0;">
                            <a class="main-link" href="#" style="padding:0; font-size:13px; font-weight:600; color:#333;">{{ __('Currency') }}<i class="icon-chevron-down" style="font-size:10px; margin-left: 3px;"></i></a>
                            <div class="t-h-dropdown-menu" style="min-width: 100px; left: 50%; transform: translateX(-50%);">
                                @foreach (DB::table('currencies')->get() as $currency)
                                    <a class="{{ Session::get('currency') == $currency->id ? 'active' : ($currency->is_default == 1 && !Session::has('currency') ? 'active' : '') }}"
                                        href="{{ route('front.currency.setup', $currency->id) }}" style="font-size:12px; padding: 6px 8px;"><i class="icon-chevron-right pr-2"></i>{{ $currency->name }}</a>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Right: Login -->
                    <div class="d-flex align-items-center" style="gap: 4px;">
                        <div class="login-register" style="margin:0; padding:0; line-height: 1.2;">
                            @if (!Auth::user())
                                <a class="track-order-link mr-0" href="{{ route('user.login') }}" style="margin:0; padding:0;"><i class="icon-user" style="font-size:10px;"></i>{{ __('Login') }}</a>
                            @else
                                <div class="t-h-dropdown" style="margin:0;">
                                    <a class="main-link" href="javascript:;" style="padding:0; font-size:11px; font-weight:600; color:#333;">
                                        {{ Auth::user()->first_name }}
                                        @if($headerUserUnread > 0)
                                            <span class="badge badge-success ml-1" style="font-size: 9px; padding: 1px 4px; border-radius: 8px;">{{ $headerUserUnread }}</span>
                                        @endif
                                        <i class="icon-chevron-down" style="font-size:9px; margin-left: 2px;"></i>
                                    </a>
                                    <div class="t-h-dropdown-menu" style="min-width: 130px; right: 0; left: auto;">
                                        <a href="{{ route('user.dashboard') }}" style="font-size:11px; padding: 4px 8px;">{{ __('Dashboard') }}</a>
                                        <a href="{{ route('user.message.index') }}" style="font-size:11px; padding: 4px 8px;">
                                            {{ __('My Chats') }}
                                            @if($headerUserUnread > 0)
                                                <span class="badge badge-success ml-1 font-weight-bold" style="font-size: 9px; border-radius: 8px; padding: 1px 4px;">{{ $headerUserUnread }}</span>
                                            @endif
                                        </a>
                                        <a href="{{ route('user.profile') }}" style="font-size:11px; padding: 4px 8px;">{{ __('Profile') }}</a>
                                        @if(Auth::user()->isSeller())
                                            <a href="{{ route('seller.dashboard') }}" style="font-size:11px; padding: 4px 8px; font-weight: bold; color: #007bff;">{{ __('Seller Dashboard') }}</a>
                                        @else
                                            <a href="{{ route('user.store.apply') }}" style="font-size:11px; padding: 4px 8px;">{{ __('Open Shop / List Product') }}</a>
                                        @endif
                                        <a href="{{ route('user.ticket') }}" style="font-size:11px; padding: 4px 8px;">{{ __('Support Ticket') }}</a>
                                        <a href="{{ route('user.order.index') }}" style="font-size:11px; padding: 4px 8px;">{{ __('Orders') }}</a>
                                        <a href="{{ route('user.logout') }}" style="font-size:11px; padding: 4px 8px;">{{ __('Logout') }}</a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Topbar-->
        <div class="topbar">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="d-flex justify-content-between">
                            <!-- Logo-->
                            <div class="site-branding"><a class="site-logo align-self-center"
                                    href="{{ route('front.index') }}"><img
                                        src="{{ url('/core/public/storage/images/' . $setting->logo) }}"
                                        alt="{{ $setting->title }}"></a></div>
                            <!-- Search / Categories-->
                            <div class="search-box-wrap d-none d-lg-block d-flex">
                                <div class="search-box-inner align-self-center">
                                    <div class="search-box d-flex">
                                        <select name="category" id="category_select" class="categoris">
                                            <option value="">{{ __('All') }}</option>
                                            @foreach (DB::table('categories')->whereStatus(1)->take($setting->buyer_category_limit ?? 50)->get() as $category)
                                                <option value="{{ $category->slug }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                        <form class="input-group" id="header_search_form"
                                            action="{{ route('front.catalog') }}" method="get">
                                            <input type="hidden" name="category" value=""
                                                id="search__category">
                                            <span class="input-group-btn">
                                                <button type="submit"><i class="icon-search"></i></button>
                                            </span>
                                            <input class="form-control" type="text"
                                                data-target="{{ route('front.search.suggest') }}"
                                                id="__product__search" name="search"
                                                placeholder="{{ __('Search by product name') }}">
                                            <div class="serch-result d-none">
                                                {{-- search result --}}
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <span class="d-block d-lg-none close-m-serch"><i class="icon-x"></i></span>
                            </div>
                            <!-- Toolbar-->
                            <div class="toolbar d-flex">

                                <div class="toolbar-item close-m-serch visible-on-mobile"><a href="#">
                                        <div>
                                            <i class="icon-search"></i>
                                        </div>
                                    </a>
                                </div>
                                <div class="toolbar-item visible-on-mobile mobile-menu-toggle"><a href="#">
                                        <div><i class="icon-menu"></i><span
                                                class="text-label">{{ __('Menu') }}</span></div>
                                    </a>
                                </div>


                                @if (Auth::check())
                                    <div class="toolbar-item"><a
                                            href="{{ route('user.wishlist.index') }}">
                                            <div><span class="compare-icon"><i class="icon-heart"></i><span
                                                        class="count-label wishlist_count">{{ (Auth::user() && Auth::user()->wishlists) ? Auth::user()->wishlists->count() : 0 }}</span></span><span
                                                    class="text-label">{{ __('Wishlist') }}</span></div>
                                        </a>
                                    </div>
                                @else
                                    <div class="toolbar-item"><a
                                            href="{{ route('user.wishlist.index') }}">
                                            <div><span class="compare-icon"><i class="icon-heart"></i></span><span
                                                    class="text-label">{{ __('Wishlist') }}</span></div>
                                        </a>
                                    </div>
                                @endif
                                <div class="toolbar-item"><a href="{{ route('front.cart') }}">
                                        <div><span class="cart-icon"><i class="icon-shopping-cart"></i><span
                                                    class="count-label cart_count">{{ Session::has('cart') ? count(Session::get('cart')) : '0' }}
                                                </span></span><span class="text-label">{{ __('Cart') }}</span>
                                        </div>
                                    </a>
                                    <div class="toolbar-dropdown cart-dropdown widget-cart  cart_view_header"
                                        id="header_cart_load" data-target="{{ route('front.header.cart') }}">
                                        @include('includes.header_cart')
                                    </div>
                                </div>
                            </div>

                            <!-- Mobile Menu-->
                            <div class="mobile-menu">
                                <!-- Slideable (Mobile) Menu-->
                                <div class="mm-heading-area">
                                    <h4>{{ __('Navigation') }}</h4>
                                    <div class="toolbar-item visible-on-mobile mobile-menu-toggle mm-t-two">
                                        <a href="#">
                                            <div> <i class="icon-x"></i></div>
                                        </a>
                                    </div>
                                </div>
                                <ul class="nav nav-tabs" role="tablist">
                                    <li class="nav-item" role="presentation99">
                                        <span class="active" id="mmenu-tab" data-bs-toggle="tab"
                                            data-bs-target="#mmenu" role="tab" aria-controls="mmenu"
                                            aria-selected="true">{{ __('Menu') }}</span>
                                    </li>
                                    <li class="nav-item" role="presentation99">
                                        <span class="" id="mcat-tab" data-bs-toggle="tab"
                                            data-bs-target="#mcat" role="tab" aria-controls="mcat"
                                            aria-selected="false">{{ __('Category') }}</span>
                                    </li>

                                </ul>
                                <div class="tab-content p-0">
                                    <div class="tab-pane fade show active" id="mmenu" role="tabpanel"
                                        aria-labelledby="mmenu-tab">
                                        <nav class="slideable-menu">
                                            <ul>
                                                <li class="{{ request()->routeIs('front.index') ? 'active' : '' }}"><a
                                                        href="{{ route('front.index') }}"><i
                                                            class="icon-chevron-right"></i>{{ __('Home') }}</a>
                                                </li>
                                                @if ($setting->is_shop == 1)
                                                    <li
                                                        class="{{ request()->routeIs('front.catalog*') ? 'active' : '' }}">
                                                        <a href="{{ route('front.catalog') }}"><i
                                                                class="icon-chevron-right"></i>{{ __('Shop') }}</a>
                                                    </li>
                                                @endif
                                                @if ($setting->is_campaign == 1)
                                                    <li
                                                        class="{{ request()->routeIs('front.campaign') ? 'active' : '' }}">
                                                        <a href="{{ route('front.campaign') }}"><i
                                                                class="icon-chevron-right"></i>{{ __('Campaign') }}</a>
                                                    </li>
                                                @endif
                                                @if ($setting->is_brands == 1)
                                                    <li
                                                        class="{{ request()->routeIs('front.brand') ? 'active' : '' }}">
                                                        <a href="{{ route('front.brand') }}"><i
                                                                class="icon-chevron-right"></i>{{ __('Brand') }}</a>
                                                    </li>
                                                @endif

                                                @if ($setting->is_blog == 1)
                                                    <li
                                                        class="{{ request()->routeIs('front.blog*') ? 'active' : '' }}">
                                                        <a href="{{ route('front.blog') }}"><i
                                                                class="icon-chevron-right"></i>{{ __('Blog') }}</a>
                                                    </li>
                                                @endif
                                                <li class="t-h-dropdown">
                                                    <a class="" href="#"><i
                                                            class="icon-chevron-right"></i>{{ __('Pages') }} <i
                                                            class="icon-chevron-down"></i></a>
                                                    <div class="t-h-dropdown-menu">
                                                        @if ($setting->is_faq == 1)
                                                            <a class="{{ request()->routeIs('front.faq*') ? 'active' : '' }}"
                                                                href="{{ route('front.faq') }}"><i
                                                                    class="icon-chevron-right pr-2"></i>{{ __('Faq') }}</a>
                                                        @endif
                                                        @foreach (DB::table('pages')->wherePos(0)->orwhere('pos', 2)->get() as $page)
                                                            <a class="{{ request()->url() == route('front.page', $page->slug) ? 'active' : '' }} "
                                                                href="{{ route('front.page', $page->slug) }}"><i
                                                                    class="icon-chevron-right pr-2"></i>{{ $page->title }}</a>
                                                        @endforeach
                                                    </div>
                                                </li>

                                                @if ($setting->is_contact == 1)
                                                    <li
                                                        class="{{ request()->routeIs('front.contact') ? 'active' : '' }}">
                                                        <a href="{{ route('front.contact') }}"><i
                                                                class="icon-chevron-right"></i>{{ __('Contact') }}</a>
                                                    </li>
                                                @endif
                                            </ul>
                                        </nav>
                                    </div>
                                    <div class="tab-pane fade" id="mcat" role="tabpanel"
                                        aria-labelledby="mcat-tab">
                                        <nav class="slideable-menu">
                                            @include('includes.mobile-category')

                                        </nav>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Navbar-->
        <div class="navbar">
            <div class="container">
                <div class="row g-3 w-100">
                    @if ($setting->is_show_category == 1)
                        <div class="col-lg-3">
                            @include('includes.categories')
                        </div>
                    @endif
                    <div class="col-lg-9 d-flex justify-content-between">
                        <div class="nav-inner">
                            @include('master.inc.site-menu')
                        </div>
                        @php
                            $free_shipping = DB::table('shipping_services')
                                ->whereStatus(1)
                                ->whereIsCondition(1)
                                ->first();
                        @endphp

                    </div>
                </div>
            </div>
        </div>

    </header>
    <!-- Page Content-->
    @yield('content')

    <!--    announcement banner section start   -->
    <a class="announcement-banner" href="#announcement-modal"></a>
    <div id="announcement-modal" class="mfp-hide white-popup">
        @if ($setting->announcement_type == 'newletter')
            <div class="announcement-with-content">
                <div class="left-area">
                    <img src="{{ url('/core/public/storage/images/' . $setting->announcement) }}" alt="">
                </div>
                <div class="right-area">
                    <h3 class="">{{ $setting->announcement_title }}</h3>
                    <p>{{ $setting->announcement_details }}</p>
                    <form class="subscriber-form" action="{{ route('front.subscriber.submit') }}" method="post">
                        @csrf
                        <div class="input-group">
                            <input class="form-control" type="email" name="email"
                                placeholder="{{ __('Your e-mail') }}">
                            <span class="input-group-addon"><i class="icon-mail"></i></span>
                        </div>
                        <div aria-hidden="true">
                            <input type="hidden" name="b_c7103e2c981361a6639545bd5_1194bb7544" tabindex="-1">
                        </div>

                        <button class="btn btn-primary btn-block mt-2" type="submit">
                            <span>{{ __('Subscribe') }}</span>
                        </button>
                    </form>
                </div>
            </div>
        @else
            <a href="{{ $setting->announcement_link }}">
                <img src="{{ url('/core/public/storage/images/' . $setting->announcement) }}" alt="">
            </a>
        @endif


    </div>
    <!--    announcement banner section end   -->

    <!-- Site Footer-->
    <footer class="site-footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <!-- Contact Info-->
                    <section class="widget widget-light-skin">
                        <h3 class="widget-title">{{ __('Get In Touch') }}</h3>
                        <p class="mb-1"><strong>{{ __('Address') }}: </strong> {{ $setting->footer_address }}</p>
                        <p class="mb-1"><strong>{{ __('Phone') }}: </strong> {{ $setting->footer_phone }}</p>
                        <p class="mb-1"><strong>{{ __('Email') }}: </strong> {{ $setting->footer_email }}</p>
                        <ul class="list-unstyled text-sm">
                            <li><span class=""><strong>{{ $setting->working_days_from_to }}:
                                    </strong></span>{{ $setting->friday_start }} - {{ $setting->friday_end }}</li>
                        </ul>
                        @php
                            $links = json_decode($setting->social_link, true)['links'];
                            $icons = json_decode($setting->social_link, true)['icons'];

                        @endphp
                        <div class="footer-social-links">
                            @foreach ($links as $link_key => $link)
                                <a href="{{ $link }}"><span><i
                                            class="{{ $icons[$link_key] }}"></i></span></a>
                            @endforeach
                        </div>
                    </section>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <!-- Customer Info-->
                    <div class="widget widget-links widget-light-skin">
                        <h3 class="widget-title">{{ __('Usefull Links') }}</h3>
                        <ul>
                            @if ($setting->is_faq == 1)
                                <li>
                                    <a class="" href="{{ route('front.faq') }}">{{ __('Faq') }}</a>
                                </li>
                            @endif
                            @foreach (DB::table('pages')->wherePos(2)->orwhere('pos', 1)->get() as $page)
                                <li><a href="{{ route('front.page', $page->slug) }}">{{ $page->title }}</a></li>
                            @endforeach

                        </ul>
                    </div>
                </div>
                <div class="col-lg-4">
                    <!-- Subscription-->
                    <section class="widget">
                        <h3 class="widget-title">{{ __('Newsletter') }}</h3>
                        <form class="row subscriber-form" action="{{ route('front.subscriber.submit') }}"
                            method="post">
                            @csrf
                            <div class="col-sm-12">
                                <div class="input-group">
                                    <input class="form-control" type="email" name="email"
                                        placeholder="{{ __('Your e-mail') }}">
                                    <span class="input-group-addon"><i class="icon-mail"></i></span>
                                </div>
                                <div aria-hidden="true">
                                    <input type="hidden" name="b_c7103e2c981361a6639545bd5_1194bb7544"
                                        tabindex="-1">
                                </div>

                            </div>
                            <div class="col-sm-12">
                                <button class="btn btn-primary btn-block mt-2" type="submit">
                                    <span>{{ __('Subscribe') }}</span>
                                </button>
                            </div>
                            <div class="col-lg-12">
                                <p class="text-sm opacity-80 pt-2">
                                    {{ __('Subscribe to our Newsletter to receive early discount offers, latest news, sales and promo information.') }}
                                </p>
                            </div>
                        </form>
                        <div class="pt-3">
                            <div class="d-inline-flex align-items-center" style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.2); padding: 8px 14px; border-radius: 6px;">
                                <i class="fas fa-truck text-primary mr-2" style="font-size: 15px;"></i>
                                <span class="text-white font-weight-bold" style="font-size: 13.5px; letter-spacing: 0.2px;">
                                    {{ __('Cash on Delivery all over the Pakistan') }}
                                </span>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
            <!-- Copyright-->
            <p class="footer-copyright" style="background: transparent !important; border-top: none !important; padding-top: 20px; box-shadow: none !important;"> {{ $setting->copy_right }}</p>
        </div>
    </footer>

    <!-- Back To Top Button-->
    <a class="scroll-to-top-btn" href="#">
        <i class="icon-chevron-up"></i>
    </a>
    <!-- Backdrop-->
    <div class="site-backdrop"></div>

    <!-- Cookie alert dialog  -->
    @if ($setting->is_cookie == 1)
        @include('cookie-consent::index')
    @endif
    <!-- Cookie alert dialog  -->


    @php
        $mainbs = [];
        $mainbs['is_announcement'] = $setting->is_announcement;
        $mainbs['announcement_delay'] = $setting->announcement_delay;
        $mainbs['overlay'] = $setting->overlay;
        $mainbs = json_encode($mainbs);
    @endphp

    <script>
        var mainbs = {!! $mainbs !!};
        var decimal_separator = '{!! $setting->decimal_separator !!}';
        var thousand_separator = '{!! $setting->thousand_separator !!}';
    </script>

    <script>
        let language = {
            Days: '{{ __('Days') }}',
            Hrs: '{{ __('Hrs') }}',
            Min: '{{ __('Min') }}',
            Sec: '{{ __('Sec') }}',
        }
    </script>



    <!-- JavaScript (jQuery) libraries, plugins and custom scripts-->
    <script type="text/javascript" src="{{ asset('assets/front/js/plugins.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/back/js/plugin/bootstrap-notify/bootstrap-notify.min.js') }}">
    </script>
    <script type="text/javascript" src="{{ asset('assets/front/js/scripts.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/front/js/lazy.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/front/js/lazy.plugin.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/front/js/myscript.js') }}"></script>
    @yield('script')
    @yield('scripts')

    @if ($setting->is_facebook_messenger == '1')
        <!-- Messenger Chat Plugin Code -->
        <div id="fb-root"></div>

        <!-- Your Chat Plugin code -->
        <div id="fb-customer-chat" class="fb-customerchat">
        </div>

        <script>
            var chatbox = document.getElementById('fb-customer-chat');
            chatbox.setAttribute("page_id", "{{ $setting->facebook_messenger }}");
            chatbox.setAttribute("attribution", "biz_inbox");
            window.fbAsyncInit = function() {
                FB.init({
                    xfbml: true,
                    version: 'v11.0'
                });
            };

            (function(d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) return;
                js = d.createElement(s);
                js.id = id;
                js.src = 'https://connect.facebook.net/en_US/sdk/xfbml.customerchat.js';
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));
        </script>
    @endif



    <script type="text/javascript">
        let mainurl = '{{ route('front.index') }}';

        let view_extra_index = 0;
        // Notifications
        function SuccessNotification(title) {
            $.notify({
                title: ` <strong>${title}</strong>`,
                message: '',
                icon: 'fas fa-check-circle'
            }, {
                element: 'body',
                position: null,
                type: "success",
                allow_dismiss: true,
                newest_on_top: false,
                showProgressbar: false,
                placement: {
                    from: "top",
                    align: "right"
                },
                offset: 20,
                spacing: 10,
                z_index: 1031,
                delay: 5000,
                timer: 1000,
                url_target: '_blank',
                mouse_over: null,
                animate: {
                    enter: 'animated fadeInDown',
                    exit: 'animated fadeOutUp'
                },
                onShow: null,
                onShown: null,
                onClose: null,
                onClosed: null,
                icon_type: 'class'
            });
        }

        function DangerNotification(title) {
            $.notify({
                // options
                title: ` <strong>${title}</strong>`,
                message: '',
                icon: 'fas fa-exclamation-triangle'
            }, {
                // settings
                element: 'body',
                position: null,
                type: "danger",
                allow_dismiss: true,
                newest_on_top: false,
                showProgressbar: false,
                placement: {
                    from: "top",
                    align: "right"
                },
                offset: 20,
                spacing: 10,
                z_index: 1031,
                delay: 5000,
                timer: 1000,
                url_target: '_blank',
                mouse_over: null,
                animate: {
                    enter: 'animated fadeInDown',
                    exit: 'animated fadeOutUp'
                },
                onShow: null,
                onShown: null,
                onClose: null,
                onClosed: null,
                icon_type: 'class'
            });
        }
        // Notifications Ends
    </script>

    @if (Session::has('error'))
        <script>
            $(document).ready(function() {
                DangerNotification('{{ Session::get('error') }}')
            })
        </script>
    @endif
    @if (Session::has('success'))
        <script>
            $(document).ready(function() {
                SuccessNotification('{{ Session::get('success') }}');
            })
        </script>
    @endif
    @php
        $global_popup = \Illuminate\Support\Facades\DB::table('global_popup_settings')->first();
    @endphp
    @if($global_popup && $global_popup->is_enabled)
        <style>
            .custom-global-modal {
                background: #fff;
                width: 92%;
                max-width: 600px;
                height: 480px;
                max-height: 85vh;
                border-radius: 12px;
                display: flex;
                flex-direction: column;
                position: relative;
                box-shadow: 0 10px 40px rgba(0,0,0,0.4);
            }
            .custom-global-modal .platform-link {
                display: flex; align-items: center; justify-content: space-between; padding: 12px 15px; border: 1px solid #e0e0e0; border-radius: 8px; text-decoration: none; color: #333; transition: 0.3s; background: #fafafa;
            }
            .custom-global-modal .platform-links-container {
                flex-grow: 1; overflow-y: auto; padding-right: 5px; display: flex; flex-direction: column; gap: 12px;
            }
            @media (max-width: 575px) {
                .custom-global-modal {
                    height: 570px !important;
                    max-height: 95vh !important;
                }
                .custom-global-modal .platform-link {
                    padding: 10px 12px !important;
                }
                .custom-global-modal .platform-links-container {
                    gap: 8px !important;
                }
            }
        </style>
        <div id="globalPopupOverlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px); z-index: 9999999; display: none; justify-content: center; align-items: center; opacity: 1;">
            <div class="custom-global-modal">
                
                <!-- Close Button -->
                <button onclick="closeGlobalPopup()" style="position: absolute; top: 18px; right: 20px; background: none; border: none; font-size: 28px; cursor: pointer; color: #ffffff; z-index: 10; padding:0; line-height:1;">
                    &times;
                </button>

                <!-- Screen 1: Message -->
                <div id="globalPopupScreen1" style="display: flex; flex-direction: column; height: 100%; padding: 30px 20px;">
                    <h3 style="text-align: center; margin: -30px -20px 20px -20px; padding: 20px 10px; background: #0d6efd; color: #ffffff; font-weight: bold; font-size: 22px; border-radius: 12px 12px 0 0;">{{ __('Exclusive Welcome Offer') }}</h3>
                    <div style="flex-grow: 1; overflow-y: auto; padding-right: 5px; font-size: 15px; line-height: 1.6; color: #555;">
                        {!! nl2br(e($global_popup->message)) !!}
                    </div>
                    <div style="text-align: center; margin-top: 20px;">
                        <button onclick="showGlobalPopupScreen2()" style="background: #e3273A; color: #fff; border: none; padding: 14px 30px; font-size: 16px; border-radius: 8px; cursor: pointer; font-weight: bold; width: 100%;">
                            {{ __('Join Channel') }}
                        </button>
                    </div>
                </div>

                <!-- Screen 2: Social Links -->
                <div id="globalPopupScreen2" style="display: none; flex-direction: column; height: 100%; padding: 30px 20px;">
                    <h3 style="text-align: center; margin: -30px -20px 15px -20px; padding: 20px 10px; background: #0d6efd; color: #ffffff; font-weight: bold; font-size: 22px; border-radius: 12px 12px 0 0;">{{ __('Connect With Us') }}</h3>
                    <p style="text-align: center; color: #777; margin-bottom: 20px; font-size: 14px;">{{ __('Select a platform to connect with NA Martzone') }}</p>
                    
                    <div class="platform-links-container">
                        @if($global_popup->whatsapp_link && $global_popup->is_whatsapp_enabled)
                        <a href="{{ $global_popup->whatsapp_link }}" target="_blank" class="platform-link">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <i class="fab fa-whatsapp" style="font-size: 24px; color: #25D366;"></i> <b style="font-size: 15px;">WhatsApp Channel</b>
                            </div>
                            <span style="background: #25D366; color: #fff; padding: 6px 14px; border-radius: 6px; font-size: 12px; font-weight: bold;">Join Now</span>
                        </a>
                        @endif
                        @if($global_popup->support_link && $global_popup->is_support_enabled)
                        <a href="{{ $global_popup->support_link }}" target="_blank" class="platform-link">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <i class="fas fa-headset" style="font-size: 22px; color: #e3273A;"></i> <b style="font-size: 15px;">Customer Support</b>
                            </div>
                            <span style="background: #e3273A; color: #fff; padding: 6px 14px; border-radius: 6px; font-size: 12px; font-weight: bold;">Contact Us</span>
                        </a>
                        @endif
                        @if($global_popup->tiktok_link && $global_popup->is_tiktok_enabled)
                        <a href="{{ $global_popup->tiktok_link }}" target="_blank" class="platform-link">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <svg xmlns="http://www.w3.org/2000/svg" height="22" width="22" viewBox="0 0 448 512" fill="#000"><path d="M448 209.91a210.06 210.06 0 0 1-122.77-39.25V349.38A162.55 162.55 0 1 1 185 188.31V278.2a74.62 74.62 0 1 0 52.23 71.18V0l88 0a121.18 121.18 0 0 0 1.86 22.17h0A122.18 122.18 0 0 0 381 102.39a121.43 121.43 0 0 0 67 20.14Z"/></svg> <b style="font-size: 15px;">TikTok</b>
                            </div>
                            <span style="background: #000; color: #fff; padding: 6px 14px; border-radius: 6px; font-size: 12px; font-weight: bold;">Follow Us</span>
                        </a>
                        @endif
                        @if($global_popup->youtube_link && $global_popup->is_youtube_enabled)
                        <a href="{{ $global_popup->youtube_link }}" target="_blank" class="platform-link">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <i class="fab fa-youtube" style="font-size: 22px; color: #FF0000;"></i> <b style="font-size: 15px;">YouTube</b>
                            </div>
                            <span style="background: #FF0000; color: #fff; padding: 6px 14px; border-radius: 6px; font-size: 12px; font-weight: bold;">Subscribe</span>
                        </a>
                        @endif
                        @if($global_popup->facebook_link && $global_popup->is_facebook_enabled)
                        <a href="{{ $global_popup->facebook_link }}" target="_blank" class="platform-link">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <i class="fab fa-facebook" style="font-size: 24px; color: #1877F2;"></i> <b style="font-size: 15px;">Facebook</b>
                            </div>
                            <span style="background: #1877F2; color: #fff; padding: 6px 14px; border-radius: 6px; font-size: 12px; font-weight: bold;">Follow Us</span>
                        </a>
                        @endif
                        @if($global_popup->telegram_link && $global_popup->is_telegram_enabled)
                        <a href="{{ $global_popup->telegram_link }}" target="_blank" class="platform-link">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <i class="fab fa-telegram" style="font-size: 24px; color: #0088cc;"></i> <b style="font-size: 15px;">Telegram</b>
                            </div>
                            <span style="background: #0088cc; color: #fff; padding: 6px 14px; border-radius: 6px; font-size: 12px; font-weight: bold;">Join Now</span>
                        </a>
                        @endif
                    </div>
                </div>

            </div>
        </div>

        <script>
            function closeGlobalPopup() {
                document.getElementById('globalPopupOverlay').style.display = 'none';
            }
            function showGlobalPopupScreen2() {
                document.getElementById('globalPopupScreen1').style.display = 'none';
                document.getElementById('globalPopupScreen2').style.display = 'flex';
            }

            document.addEventListener("DOMContentLoaded", function() {
                var overlay = document.getElementById('globalPopupOverlay');
                if (!overlay) return;

                var isReload = false;
                if (window.performance && window.performance.navigation) {
                    isReload = window.performance.navigation.type === 1;
                }
                if (window.performance && window.performance.getEntriesByType) {
                    var navEntries = window.performance.getEntriesByType("navigation");
                    if (navEntries.length > 0) {
                        isReload = navEntries[0].type === "reload";
                    }
                }

                var referrer = document.referrer || "";
                var currentDomain = window.location.hostname;
                var isInternalClick = referrer.indexOf(currentDomain) !== -1;

                // Show ONLY if it's a direct refresh OR a completely new visit (from outside)
                if (isReload || !isInternalClick) {
                    overlay.style.display = 'flex';
                }
            });
        </script>
    @endif
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Click-to-toggle support for header dropdowns (User & Currency)
            document.querySelectorAll('.t-h-dropdown .main-link').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    var menu = this.parentElement.querySelector('.t-h-dropdown-menu');
                    if (menu) {
                        var isShown = menu.classList.contains('show-dropdown');
                        document.querySelectorAll('.t-h-dropdown-menu').forEach(function(m) {
                            m.classList.remove('show-dropdown');
                        });
                        if (!isShown) {
                            menu.classList.add('show-dropdown');
                        }
                    }
                });
            });

            document.addEventListener('click', function() {
                document.querySelectorAll('.t-h-dropdown-menu').forEach(function(m) {
                    m.classList.remove('show-dropdown');
                });
            });
        });
    </script>
</body>

</html>

