@extends('master.front')
@section('meta')
    <meta name="keywords" content="{{ $setting->meta_keywords }}">
    <meta name="description" content="{{ $setting->meta_description }}">
@endsection

@section('content')

    @if ($setting->is_slider == 1)
        <style>
            /* Hero Slider & Banner Visual Upgrades */
            .hero-slider .item {
                position: relative;
                overflow: hidden;
            }
            .hero-slider .item::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: linear-gradient(90deg, rgba(15, 23, 42, 0.45) 0%, rgba(15, 23, 42, 0.2) 60%, rgba(15, 23, 42, 0.05) 100%);
                z-index: 1;
                pointer-events: none;
            }
            .hero-slider .item-inner {
                position: relative;
                z-index: 2;
                max-width: 580px;
                padding: 40px 45px !important;
            }
            .hero-slider .title {
                font-size: 34px !important;
                font-weight: 800 !important;
                color: #ffffff !important;
                text-shadow: 0 2px 14px rgba(0, 0, 0, 0.65) !important;
                line-height: 1.2 !important;
                margin-bottom: 12px !important;
                letter-spacing: -0.3px;
                display: block;
                animation: heroLoopFloat 4s cubic-bezier(0.4, 0, 0.2, 1) infinite;
            }
            .hero-slider .subtitle {
                display: inline-block !important;
                background: rgba(15, 23, 42, 0.65) !important;
                backdrop-filter: blur(8px) !important;
                -webkit-backdrop-filter: blur(8px) !important;
                color: #ffffff !important;
                padding: 6px 18px !important;
                border-radius: 30px !important;
                border: 1px solid rgba(255, 255, 255, 0.25) !important;
                font-size: 14px !important;
                font-weight: 600 !important;
                line-height: 1.4 !important;
                margin-bottom: 20px !important;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.25) !important;
                animation: heroBadgeLoop 4s cubic-bezier(0.4, 0, 0.2, 1) 0.2s infinite;
            }
            .hero-slider .btn {
                border-radius: 30px !important;
                padding: 10px 26px !important;
                font-weight: 700 !important;
                text-transform: uppercase !important;
                letter-spacing: 0.5px !important;
                box-shadow: 0 4px 16px rgba(37, 99, 235, 0.45) !important;
                border: none !important;
                background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
                animation: heroBtnLoop 4s cubic-bezier(0.4, 0, 0.2, 1) 0.4s infinite;
            }
            .hero-slider .btn:hover {
                transform: translateY(-2px) scale(1.03) !important;
                box-shadow: 0 8px 24px rgba(37, 99, 235, 0.6) !important;
            }

            /* Right Hero Banners */
            .sright-image {
                position: relative;
                overflow: hidden;
                border-radius: 14px !important;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
                transition: transform 0.3s ease, box-shadow 0.3s ease;
                display: block;
            }
            .sright-image:hover {
                transform: translateY(-4px);
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
            }
            .sright-image .inner-content {
                position: absolute;
                top: 50%;
                transform: translateY(-50%);
                left: 24px;
                right: 20px;
                z-index: 2;
            }
            .sright-image .inner-content p {
                display: inline-block;
                background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
                color: #ffffff !important;
                padding: 3px 12px;
                border-radius: 20px;
                font-size: 11px;
                font-weight: 700;
                letter-spacing: 0.5px;
                text-transform: uppercase;
                margin-bottom: 6px;
                box-shadow: 0 3px 10px rgba(37, 99, 235, 0.35);
                animation: heroBadgeLoop 4s cubic-bezier(0.4, 0, 0.2, 1) 0.2s infinite;
            }
            .sright-image .inner-content h4 {
                font-size: 19px !important;
                font-weight: 800 !important;
                color: #0f172a !important;
                line-height: 1.25 !important;
                margin-bottom: 0 !important;
                text-shadow: 0 1px 4px rgba(255, 255, 255, 0.95);
                animation: heroLoopFloat 4s cubic-bezier(0.4, 0, 0.2, 1) infinite;
            }

            /* 4-Column & Other Homepage Banners */
            .modern-banner-card .banner-title,
            .genius-banner h4 {
                animation: heroLoopFloat 4s cubic-bezier(0.4, 0, 0.2, 1) infinite;
            }
            .modern-banner-card .banner-subtitle,
            .genius-banner p {
                animation: heroBadgeLoop 4s cubic-bezier(0.4, 0, 0.2, 1) 0.2s infinite;
            }

            /* Continuous Looping Keyframes with ~2-second Rest/Pause */
            @keyframes heroLoopFloat {
                0% {
                    transform: translateY(0) scale(1);
                    filter: drop-shadow(0 2px 6px rgba(0, 0, 0, 0.2));
                }
                12% {
                    transform: translateY(-7px) scale(1.025);
                    filter: drop-shadow(0 8px 18px rgba(37, 99, 235, 0.35));
                }
                24% {
                    transform: translateY(0) scale(1);
                    filter: drop-shadow(0 2px 6px rgba(0, 0, 0, 0.2));
                }
                100% {
                    transform: translateY(0) scale(1);
                    filter: drop-shadow(0 2px 6px rgba(0, 0, 0, 0.2));
                }
            }

            @keyframes heroBadgeLoop {
                0% {
                    transform: translateY(0) scale(1);
                    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.15);
                }
                12% {
                    transform: translateY(-5px) scale(1.04);
                    box-shadow: 0 8px 20px rgba(37, 99, 235, 0.4);
                }
                24% {
                    transform: translateY(0) scale(1);
                    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.15);
                }
                100% {
                    transform: translateY(0) scale(1);
                    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.15);
                }
            }

            @keyframes heroBtnLoop {
                0% {
                    transform: translateY(0) scale(1);
                    box-shadow: 0 4px 16px rgba(37, 99, 235, 0.45);
                }
                12% {
                    transform: translateY(-4px) scale(1.05);
                    box-shadow: 0 8px 24px rgba(37, 99, 235, 0.65);
                }
                24% {
                    transform: translateY(0) scale(1);
                    box-shadow: 0 4px 16px rgba(37, 99, 235, 0.45);
                }
                100% {
                    transform: translateY(0) scale(1);
                    box-shadow: 0 4px 16px rgba(37, 99, 235, 0.45);
                }
            }

            /* Responsive Mobile Screen */
            @media (max-width: 575px) {
                .slider-area-wrapper {
                    padding-top: 10px;
                }
                .hero-slider {
                    margin: 0 10px !important;
                    border-radius: 14px !important;
                }
                .hero-slider .item {
                    height: auto !important;
                    aspect-ratio: 16 / 9 !important;
                    background-size: cover !important;
                    background-position: center !important;
                    min-height: 200px !important;
                }
                .hero-slider .item-inner {
                    padding: 16px 14px !important;
                    max-width: 88% !important;
                }
                .hero-slider .title {
                    font-size: 16px !important;
                    margin-bottom: 5px !important;
                    line-height: 1.2 !important;
                }
                .hero-slider .subtitle {
                    font-size: 9.5px !important;
                    padding: 2px 9px !important;
                    margin-bottom: 8px !important;
                    border-radius: 14px !important;
                }
                .hero-slider .btn {
                    padding: 5px 14px !important;
                    font-size: 10px !important;
                    border-radius: 20px !important;
                }
                .brand-logo {
                    max-width: 65px !important;
                    margin-bottom: 3px !important;
                }
            }
        </style>
        <div class="slider-area-wrapper">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8">
                        <!-- Main Slider-->
                        <div class="hero-slider">
                            <div class="hero-slider-main owl-carousel dots-inside">
                                @foreach ($sliders as $slider)
                                    @php
                                        $sliderPhoto = $slider->photo ?? '';
                                        $sliderExt = strtolower(pathinfo($sliderPhoto, PATHINFO_EXTENSION));
                                        $isLottieSlider = in_array($sliderExt, ['json', 'lottie']);
                                    @endphp
                                    <div class="item
                                    @if (DB::table('languages')->where('is_default', 1)->first()->rtl == 1) d-flex justify-content-end @endif
                                    "
                                        @if (!$isLottieSlider)
                                            style="background: url('{{ url('/core/public/storage/images/' . $slider->photo) }}')"
                                        @else
                                            style="background: #f8fafc; position: relative; overflow: hidden;"
                                        @endif
                                    >
                                        @if ($isLottieSlider)
                                            <lottie-player src="{{ url('/core/public/storage/images/' . $slider->photo) }}" background="transparent" speed="1" loop autoplay style="position: absolute; top:0; left:0; width:100%; height:100%; object-fit:contain; z-index:1;"></lottie-player>
                                        @endif
                                        <div class="item-inner" style="position: relative; z-index: 2;">
                                            <div class="from-bottom">
                                                @if ($slider->logo)
                                                    <img class="d-inline-block brand-logo"
                                                        src="{{ url('/core/public/storage/images/' . $slider->logo) }}" alt="logo">
                                                @endif
                                                <div class="title text-body">{{ $slider->title }}</div>
                                                <div class="subtitle text-body">{{ $slider->details }}</div>
                                            </div>
                                            @if ($slider->link != '#')
                                                <a class="btn btn-primary scale-up delay-1" href="{{ $slider->link }}">
                                                    <span>{{ __('Buy Now') }}</span>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    @if (isset($hero_banner))
                        <div class="col-lg-4 d-none d-lg-block">
                            <a href="{{ $hero_banner['url1'] ?? '#' }}" class="sright-image" style="position: relative; overflow: hidden; display: block;">
                                @php
                                    $img1 = $hero_banner['img1'] ?? '';
                                    $ext1 = strtolower(pathinfo($img1, PATHINFO_EXTENSION));
                                @endphp
                                @if (in_array($ext1, ['json', 'lottie']))
                                    <lottie-player src="{{ url('/core/public/storage/images/' . $img1) }}" background="transparent" speed="1" loop autoplay style="width: 100%; height: 215px; object-fit: contain;"></lottie-player>
                                @else
                                    <img src="{{ url('/core/public/storage/images/' . $img1) }}" alt="">
                                @endif
                                <div class="inner-content">

                                    @if (isset($hero_banner['subtitle1']) && !empty($hero_banner['subtitle1']))
                                        <p>{{ $hero_banner['subtitle1'] }}</p>
                                    @endif

                                    @if (isset($hero_banner['title1']) && !empty($hero_banner['title1']))
                                        <h4>{{ $hero_banner['title1'] }}</h4>
                                    @endif
                                </div>
                            </a>
                            <a href="{{ $hero_banner['url2'] ?? '#' }}" class="sright-image mb-0" style="position: relative; overflow: hidden; display: block;">
                                @php
                                    $img2 = $hero_banner['img2'] ?? '';
                                    $ext2 = strtolower(pathinfo($img2, PATHINFO_EXTENSION));
                                @endphp
                                @if (in_array($ext2, ['json', 'lottie']))
                                    <lottie-player src="{{ url('/core/public/storage/images/' . $img2) }}" background="transparent" speed="1" loop autoplay style="width: 100%; height: 215px; object-fit: contain;"></lottie-player>
                                @else
                                    <img src="{{ url('/core/public/storage/images/' . $img2) }}" alt="">
                                @endif
                                <div class="inner-content">
                                    @if (isset($hero_banner['subtitle2']) && !empty($hero_banner['subtitle2']))
                                        <p>{{ $hero_banner['subtitle2'] }}</p>
                                    @endif
                                    @if (isset($hero_banner['title2']) && !empty($hero_banner['title2']))
                                        <h4>{{ $hero_banner['title2'] }}</h4>
                                    @endif
                                </div>
                            </a>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    @endif


    @if ($setting->is_service == 1)
        <style>
            @media (max-width: 575px) {
                .mobile-compact-service {
                    padding: 10px 5px !important;
                    flex-direction: column !important;
                    text-align: center !important;
                    justify-content: center !important;
                }
                .mobile-compact-service img {
                    max-width: 35px !important;
                    height: auto !important;
                    margin-bottom: 8px !important;
                    margin-right: 0 !important;
                    flex-shrink: 0 !important;
                    object-fit: contain !important;
                }
                .mobile-compact-service .content {
                    text-align: center !important;
                    margin-top: 5px !important;
                }
                .mobile-compact-service h6 {
                    font-size: 12px !important;
                    margin-bottom: 4px !important;
                    line-height: 1.2 !important;
                }
                .mobile-compact-service p {
                    font-size: 10px !important;
                    line-height: 1.2 !important;
                }
                .service-section .col-6 {
                    padding-right: 5px;
                    padding-left: 5px;
                    margin-bottom: 10px !important;
                }
            }
        </style>
        <section class="service-section">
            <div class="container">
                <div class="row" style="margin-left: -5px; margin-right: -5px;">
                    @foreach ($services as $service)
                        <div class="col-6 col-lg-3 text-center mb-30">
                            <div class="single-service single-service2 mobile-compact-service" style="height: 100%;">
                                <img src="{{ url('/core/public/storage/images/' . $service->photo) }}" alt="Shipping">
                                <div class="content">
                                    <h6 class="mb-2">{{ $service->title }}</h6>
                                    <p class="text-sm text-muted mb-0">{{ $service->details }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Home Action Cards: View Products & Open My Store --}}
    <style>
        .home-promo-actions-section {
            margin-top: 10px;
            margin-bottom: 25px;
        }
        .home-promo-card {
            position: relative;
            border-radius: 16px;
            padding: 22px 24px;
            overflow: hidden;
            transition: all 0.35s cubic-bezier(0.165, 0.84, 0.44, 1);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.06);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
            text-decoration: none !important;
        }
        .promo-card-shop {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 60%, #334155 100%);
            color: #ffffff !important;
        }
        .promo-card-seller {
            background: linear-gradient(135deg, #18181b 0%, #27272a 60%, #3f3f46 100%);
            color: #ffffff !important;
        }
        .home-promo-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
            color: #ffffff !important;
        }
        .promo-card-shop:hover {
            border-color: rgba(59, 130, 246, 0.5);
        }
        .promo-card-seller:hover {
            border-color: rgba(245, 158, 11, 0.5);
        }
        .promo-card-inner {
            position: relative;
            z-index: 2;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .promo-card-text {
            flex: 1;
            min-width: 0;
        }
        .promo-badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(8px);
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.3px;
            color: #60a5fa;
            margin-bottom: 8px;
            line-height: 1.3;
            white-space: normal;
        }
        .promo-badge-seller {
            color: #fbbf24;
        }
        .promo-title {
            font-size: 20px;
            font-weight: 700;
            color: #ffffff !important;
            margin-top: 0;
            margin-bottom: 6px;
            line-height: 1.25;
            white-space: normal;
            word-break: break-word;
        }
        .promo-desc {
            font-size: 13px;
            color: #cbd5e1;
            line-height: 1.4;
            margin-bottom: 12px;
            max-width: 90%;
        }
        .promo-btn-link {
            display: inline-flex;
            align-items: center;
            font-size: 13px;
            font-weight: 700;
            color: #93c5fd;
            transition: transform 0.2s ease, color 0.2s ease;
        }
        .promo-btn-seller {
            color: #fde047;
        }
        .home-promo-card:hover .promo-btn-link {
            transform: translateX(4px);
            color: #ffffff;
        }
        .promo-card-icon-wrap {
            flex-shrink: 0;
            margin-left: 12px;
        }
        .promo-icon-circle {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.25) 0%, rgba(37, 99, 235, 0.1) 100%);
            border: 1px solid rgba(96, 165, 250, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: #60a5fa;
            transition: transform 0.3s ease;
        }
        .promo-icon-seller {
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.25) 0%, rgba(217, 119, 6, 0.1) 100%);
            border: 1px solid rgba(251, 191, 36, 0.3);
            color: #fbbf24;
        }
        .home-promo-card:hover .promo-icon-circle {
            transform: scale(1.08) rotate(5deg);
        }

        /* Mobile: 2 Cards in 1 Row (Side-by-Side) */
        @media (max-width: 767px) {
            .home-promo-actions-section {
                margin-top: 5px;
                margin-bottom: 15px;
            }
            .home-promo-actions-section .row {
                margin-left: -5px;
                margin-right: -5px;
            }
            .home-promo-actions-section .col-6 {
                padding-left: 5px;
                padding-right: 5px;
            }
            .home-promo-card {
                padding: 12px 10px;
                border-radius: 12px;
                min-height: 120px;
            }
            .promo-card-inner {
                display: flex;
                flex-direction: column;
                align-items: flex-start;
                justify-content: space-between;
                height: 100%;
            }
            .promo-card-text {
                width: 100%;
            }
            .promo-badge {
                display: inline-flex;
                align-items: center;
                padding: 3px 8px;
                font-size: 9.5px;
                font-weight: 600;
                margin-bottom: 6px;
                border-radius: 12px;
                white-space: normal;
                line-height: 1.25;
                max-width: 100%;
            }
            .promo-title {
                font-size: 13.5px;
                font-weight: 700;
                margin-top: 0;
                margin-bottom: 6px;
                line-height: 1.25;
                display: block;
                width: 100%;
            }
            .promo-desc {
                display: none;
            }
            .promo-card-bottom-mobile {
                display: flex;
                align-items: center;
                justify-content: space-between;
                width: 100%;
                margin-top: 4px;
            }
            .promo-btn-link {
                font-size: 11px;
                font-weight: 700;
                margin-top: 0;
                display: inline-flex;
            }
            .promo-icon-circle-mobile {
                width: 26px;
                height: 26px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 12px;
                flex-shrink: 0;
            }
            .promo-icon-circle-mobile.shop-icon {
                background: rgba(59, 130, 246, 0.2);
                color: #60a5fa;
                border: 1px solid rgba(96, 165, 250, 0.3);
            }
            .promo-icon-circle-mobile.seller-icon {
                background: rgba(245, 158, 11, 0.2);
                color: #fbbf24;
                border: 1px solid rgba(251, 191, 36, 0.3);
            }
        }
    </style>
    <div class="home-promo-actions-section">
        <div class="container">
            <div class="row g-2 g-md-3">
                <!-- Card 1: View Products / Shop -->
                <div class="col-6 col-md-6">
                    <a href="{{ route('front.catalog') }}" class="home-promo-card promo-card-shop">
                        <div class="promo-card-inner">
                            <div class="promo-card-text">
                                <span class="promo-badge"><i class="fas fa-shopping-bag mr-1"></i> {{ __('Trending Collection') }}</span>
                                <h3 class="promo-title">{{ __('View Products') }}</h3>
                                <p class="promo-desc">{{ __('Discover thousands of premium products at unbeatable prices') }}</p>
                                <div class="promo-card-bottom-mobile d-flex d-md-none">
                                    <span class="promo-btn-link">
                                        {{ __('Shop Now') }} <i class="fas fa-arrow-right ml-1"></i>
                                    </span>
                                    <div class="promo-icon-circle-mobile shop-icon">
                                        <i class="fas fa-shopping-basket"></i>
                                    </div>
                                </div>
                                <span class="promo-btn-link d-none d-md-inline-flex">
                                    {{ __('Shop Now') }} <i class="fas fa-arrow-right ml-1"></i>
                                </span>
                            </div>
                            <div class="promo-card-icon-wrap d-none d-md-block">
                                <div class="promo-icon-circle">
                                    <i class="fas fa-shopping-basket"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Card 2: Open My Store -->
                <div class="col-6 col-md-6">
                    <a href="{{ route('user.store.apply') }}" class="home-promo-card promo-card-seller">
                        <div class="promo-card-inner">
                            <div class="promo-card-text">
                                <span class="promo-badge promo-badge-seller"><i class="fas fa-rocket mr-1"></i> {{ __('Sell on Tamsal Store') }}</span>
                                <h3 class="promo-title">{{ __('Open My Store') }}</h3>
                                <p class="promo-desc">{{ __('Start your business today & sell to thousands of customers') }}</p>
                                <div class="promo-card-bottom-mobile d-flex d-md-none">
                                    <span class="promo-btn-link promo-btn-seller">
                                        {{ __('Open Store Now') }} <i class="fas fa-arrow-right ml-1"></i>
                                    </span>
                                    <div class="promo-icon-circle-mobile seller-icon">
                                        <i class="fas fa-store"></i>
                                    </div>
                                </div>
                                <span class="promo-btn-link promo-btn-seller d-none d-md-inline-flex">
                                    {{ __('Open Store Now') }} <i class="fas fa-arrow-right ml-1"></i>
                                </span>
                            </div>
                            <div class="promo-card-icon-wrap d-none d-md-block">
                                <div class="promo-icon-circle promo-icon-seller">
                                    <i class="fas fa-store"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if ($setting->campaign_status == 1)
        <div class="deal-of-day-section mt-20">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="section-title">
                            <h2 class="h3">{{ __('Most Selling Products') }}</h2>
                            <div class="right-area">
                                <a class="right_link" href="{{ route('front.campaign') }}">{{ __('View All') }} <i
                                        class="icon-chevron-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row g-3">

                    <div class="col-lg-12 d-none d-md-block">
                        <div class="popular-category-slider owl-carousel">
                            @foreach ($campaign_items as $compaign_item)
                                @php
                                    $item = isset($compaign_item->item) ? $compaign_item->item : $compaign_item;
                                @endphp
                                <div class="slider-item">
                                    <div class="product-card">
                                        <div class="product-thumb">
                                            @if (!$item->is_stock())
                                                <div class="product-badge bg-secondary border-default text-body">
                                                    {{ __('out of stock') }}</div>
                                            @endif

                                            @if ($item->previous_price && $item->previous_price != 0)
                                                <div class="product-badge product-badge2 bg-info">
                                                    -{{ PriceHelper::DiscountPercentage($item) }}</div>
                                            @endif
                                            <img src="{{ url('/core/public/storage/images/' . ($item->photo ?: $item->thumbnail)) }}" class="lazy"
                                                data-src="{{ url('/core/public/storage/images/' . ($item->photo ?: $item->thumbnail)) }}"
                                                alt="{{ $item->name ?? 'Product' }}">
                                            <div class="product-button-group">
                                                <a class="product-button wishlist_store"
                                                    href="{{ route('user.wishlist.store', $item->id) }}"
                                                    title="{{ __('Wishlist') }}"><i class="icon-heart"></i></a>
                                                <a data-target="{{ route('fornt.compare.product', $item->id) }}"
                                                    class="product-button product_compare" href="javascript:;"
                                                    title="{{ __('Compare') }}"><i class="icon-repeat"></i></a>
                                                @if ($item->is_stock())
                                                    <a class="product-button add_to_single_cart"
                                                        data-target="{{ $item->id }}" href="javascript:;"
                                                        title="{{ __('To Cart') }}"><i class="icon-shopping-cart"></i>
                                                    </a>
                                                @else
                                                    <a class="product-button"
                                                        href="{{ route('front.product', $item->slug) }}"
                                                        title="{{ __('Details') }}"><i class="icon-arrow-right"></i></a>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="product-card-body">
                                            <div class="product-category">
                                                @if($item->category)
                                                    <a href="{{ route('front.catalog') . '?category=' . $item->category->slug }}">{{ $item->category->name }}</a>
                                                @endif
                                            </div>
                                            <h3 class="product-title"><a
                                                    href="{{ route('front.product', $item->slug) }}">
                                                    {{ Str::limit($item->name, 35) }}
                                                </a></h3>
                                             <div class="rating-stars">
                                                 {!! Helper::renderStarRating($item) !!}
                                                 @if($item && $item->rating > 0)
                                                     <span class="text-muted ml-1" style="font-size: 11px; font-weight: 600;">({{ number_format($item->rating, 1) }})</span>
                                                 @endif
                                             </div>
                                            <h4 class="product-price">
                                                @if ($item->previous_price != 0)
                                                    <del>{{ PriceHelper::setPreviousPrice($item->previous_price) }}</del>
                                                @endif

                                                {{ PriceHelper::grandCurrencyPrice($item) }}
                                            </h4>

                                        </div>

                                    </div>
                                </div>
                            @endforeach
                        </div>

                    </div>

                    <div class="col-12 d-md-none">
                        <div class="row gx-2 gy-2 mobile-product-grid">
                            @foreach ($campaign_items->take(4) as $compaign_item)
                                @php
                                    $item = isset($compaign_item->item) ? $compaign_item->item : $compaign_item;
                                @endphp
                                <div class="col-6 mb-2">
                                    <div class="product-card">
                                        <div class="product-thumb">
                                            @if (!$item->is_stock())
                                                <div class="product-badge bg-secondary border-default text-body">
                                                    {{ __('out of stock') }}</div>
                                            @endif

                                            @if ($item->previous_price && $item->previous_price != 0)
                                                <div class="product-badge product-badge2 bg-info">
                                                    -{{ PriceHelper::DiscountPercentage($item) }}</div>
                                            @endif
                                            <img src="{{ url('/core/public/storage/images/' . ($item->photo ?: $item->thumbnail)) }}" class="lazy"
                                                data-src="{{ url('/core/public/storage/images/' . ($item->photo ?: $item->thumbnail)) }}"
                                                alt="{{ $item->name ?? 'Product' }}">
                                            <div class="product-button-group">
                                                <a class="product-button wishlist_store"
                                                    href="{{ route('user.wishlist.store', $item->id) }}"
                                                    title="{{ __('Wishlist') }}"><i class="icon-heart"></i></a>
                                                <a data-target="{{ route('fornt.compare.product', $item->id) }}"
                                                    class="product-button product_compare" href="javascript:;"
                                                    title="{{ __('Compare') }}"><i class="icon-repeat"></i></a>
                                                @if ($item->is_stock())
                                                    <a class="product-button add_to_single_cart"
                                                        data-target="{{ $item->id }}" href="javascript:;"
                                                        title="{{ __('To Cart') }}"><i class="icon-shopping-cart"></i>
                                                    </a>
                                                @else
                                                    <a class="product-button"
                                                        href="{{ route('front.product', $item->slug) }}"
                                                        title="{{ __('Details') }}"><i class="icon-arrow-right"></i></a>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="product-card-body">
                                            <div class="product-category">
                                                @if($item->category)
                                                    <a href="{{ route('front.catalog') . '?category=' . $item->category->slug }}">{{ $item->category->name }}</a>
                                                @endif
                                            </div>
                                            <h3 class="product-title"><a
                                                    href="{{ route('front.product', $item->slug) }}">
                                                    {{ Str::limit($item->name, 35) }}
                                                </a></h3>
                                             <div class="rating-stars">
                                                 {!! Helper::renderStarRating($item) !!}
                                                 @if($item && $item->rating > 0)
                                                     <span class="text-muted ml-1" style="font-size: 11px; font-weight: 600;">({{ number_format($item->rating, 1) }})</span>
                                                 @endif
                                             </div>
                                            <h4 class="product-price">
                                                @if ($item->previous_price != 0)
                                                    <del>{{ PriceHelper::setPreviousPrice($item->previous_price) }}</del>
                                                @endif

                                                {{ PriceHelper::grandCurrencyPrice($item) }}
                                            </h4>

                                        </div>

                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>
        </div>
    @endif


    @if ($setting->is_three_c_b_first == 1)
        <div class="bannner-section mt-60">
            <div class="container ">
                <div class="row gx-3 mobile-banner-row">
                    <div class="col-6 col-md-3 mb-3 mobile-banner-col">
                        <a href="{{ $banner_first['firsturl1'] ?? '#' }}" class="genius-banner modern-banner-card">
                            <div class="banner-text-content">
                                @if (!empty($banner_first['subtitle1']))
                                    <span class="banner-subtitle">{{ $banner_first['subtitle1'] }}</span>
                                @endif
                                @if (!empty($banner_first['title1']))
                                    <h4 class="banner-title">{{ $banner_first['title1'] }}</h4>
                                @endif
                            </div>
                            <div class="banner-img-box">
                                <img src="{{ url('/core/public/storage/images/' . ($banner_first['img1'] ?? '')) }}" alt="{{ $banner_first['title1'] ?? '' }}">
                            </div>
                        </a>
                    </div>
                    <div class="col-6 col-md-3 mb-3 mobile-banner-col">
                        <a href="{{ $banner_first['firsturl2'] ?? '#' }}" class="genius-banner modern-banner-card">
                            <div class="banner-text-content">
                                @if (!empty($banner_first['subtitle2']))
                                    <span class="banner-subtitle">{{ $banner_first['subtitle2'] }}</span>
                                @endif
                                @if (!empty($banner_first['title2']))
                                    <h4 class="banner-title">{{ $banner_first['title2'] }}</h4>
                                @endif
                            </div>
                            <div class="banner-img-box">
                                <img src="{{ url('/core/public/storage/images/' . ($banner_first['img2'] ?? '')) }}" alt="{{ $banner_first['title2'] ?? '' }}">
                            </div>
                        </a>
                    </div>
                    <div class="col-6 col-md-3 mb-3 mobile-banner-col">
                        <a href="{{ $banner_first['firsturl3'] ?? '#' }}" class="genius-banner modern-banner-card">
                            <div class="banner-text-content">
                                @if (!empty($banner_first['subtitle3']))
                                    <span class="banner-subtitle">{{ $banner_first['subtitle3'] }}</span>
                                @endif
                                @if (!empty($banner_first['title3']))
                                    <h4 class="banner-title">{{ $banner_first['title3'] }}</h4>
                                @endif
                            </div>
                            <div class="banner-img-box">
                                <img src="{{ url('/core/public/storage/images/' . ($banner_first['img3'] ?? '')) }}" alt="{{ $banner_first['title3'] ?? '' }}">
                            </div>
                        </a>
                    </div>
                    <div class="col-6 col-md-3 mb-3 mobile-banner-col">
                        <a href="{{ $banner_first['firsturl4'] ?? ($banner_first['firsturl3'] ?? '#') }}" class="genius-banner modern-banner-card">
                            <div class="banner-text-content">
                                @if (!empty($banner_first['subtitle4']) || !empty($banner_first['subtitle3']))
                                    <span class="banner-subtitle">{{ $banner_first['subtitle4'] ?? ($banner_first['subtitle3'] ?? '') }}</span>
                                @endif
                                @if (!empty($banner_first['title4']) || !empty($banner_first['title3']))
                                    <h4 class="banner-title">{{ $banner_first['title4'] ?? ($banner_first['title3'] ?? '') }}</h4>
                                @endif
                            </div>
                            <div class="banner-img-box">
                                <img src="{{ url('/core/public/storage/images/' . ($banner_first['img4'] ?? ($banner_first['img3'] ?? ''))) }}" alt="{{ $banner_first['title4'] ?? ($banner_first['title3'] ?? '') }}">
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif


    @if ($setting->is_popular_category == 1)
        <section class="newproduct-section popular-category-sec mt-50">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="section-title">
                            <h2 class="h3">{{ __('Top Rated Products') }}</h2>
                            <div class="right-area">
                                <a class="right_link" href="{{ route('front.top_rated') }}">{{ __('View All') }} <i
                                        class="icon-chevron-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row" id="popular_category_view">
                    <div class="col-lg-12 d-none d-md-block">
                        <div class="popular-category-slider  owl-carousel">
                            @foreach ($popular_category_items as $popular_category_item)
                                <div class="slider-item">
                                    <div class="product-card">
                                        <div class="product-thumb">

                                            @if (!$popular_category_item->is_stock())
                                                <div
                                                    class="product-badge bg-secondary border-default text-body
                                            ">
                                                    {{ __('out of stock') }}</div>
                                            @endif
                                            @if ($popular_category_item->previous_price && $popular_category_item->previous_price != 0)
                                                <div class="product-badge product-badge2 bg-info">
                                                    -{{ PriceHelper::DiscountPercentage($popular_category_item) }}</div>
                                            @endif
                                            <img src="{{ url('/core/public/storage/images/' . ($popular_category_item->photo ?: $popular_category_item->thumbnail)) }}" class="lazy"
                                                data-src="{{ url('/core/public/storage/images/' . ($popular_category_item->photo ?: $popular_category_item->thumbnail)) }}"
                                                alt="{{ $popular_category_item->name ?? 'Product' }}">
                                            <div class="product-button-group">
                                                <a class="product-button wishlist_store"
                                                    href="{{ route('user.wishlist.store', $popular_category_item->id) }}"
                                                    title="{{ __('Wishlist') }}"><i class="icon-heart"></i></a>
                                                <a data-target="{{ route('fornt.compare.product', $popular_category_item->id) }}"
                                                    class="product-button product_compare" href="javascript:;"
                                                    title="{{ __('Compare') }}"><i class="icon-repeat"></i></a>
                                                @include('includes.item_footer', [
                                                    'sitem' => $popular_category_item,
                                                ])
                                            </div>
                                        </div>
                                        <div class="product-card-body">
                                            <div class="product-category"><a
                                                    href="{{ route('front.catalog') . '?category=' . $popular_category_item->category->slug }}">{{ $popular_category_item->category->name }}</a>
                                            </div>
                                            <h3 class="product-title"><a
                                                    href="{{ route('front.product', $popular_category_item->slug) }}">
                                                    {{ Str::limit($popular_category_item->name, 35) }}
                                                </a></h3>
                                             <div class="rating-stars">
                                                 {!! Helper::renderStarRating($popular_category_item->customer_rating) !!}
                                                 @if($popular_category_item->customer_rating > 0)
                                                     <span class="text-muted ml-1" style="font-size: 11px; font-weight: 600;">({{ number_format($popular_category_item->customer_rating, 1) }})</span>
                                                 @endif
                                             </div>
                                            <h4 class="product-price">
                                                @if ($popular_category_item->previous_price != 0)
                                                    <del>{{ PriceHelper::setPreviousPrice($popular_category_item->previous_price) }}</del>
                                                @endif
                                                {{ PriceHelper::grandCurrencyPrice($popular_category_item) }}
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="col-12 d-md-none">
                        <div class="row gx-2 gy-2 mobile-product-grid">
                            @foreach ($popular_category_items->take(4) as $popular_category_item)
                                <div class="col-6 mb-2">
                                    <div class="product-card">
                                        <div class="product-thumb">

                                            @if (!$popular_category_item->is_stock())
                                                <div
                                                    class="product-badge bg-secondary border-default text-body
                                            ">
                                                    {{ __('out of stock') }}</div>
                                            @endif
                                            @if ($popular_category_item->previous_price && $popular_category_item->previous_price != 0)
                                                <div class="product-badge product-badge2 bg-info">
                                                    -{{ PriceHelper::DiscountPercentage($popular_category_item) }}</div>
                                            @endif
                                            <img src="{{ url('/core/public/storage/images/' . ($popular_category_item->photo ?: $popular_category_item->thumbnail)) }}" class="lazy"
                                                data-src="{{ url('/core/public/storage/images/' . ($popular_category_item->photo ?: $popular_category_item->thumbnail)) }}"
                                                alt="{{ $popular_category_item->name ?? 'Product' }}">
                                            <div class="product-button-group">
                                                <a class="product-button wishlist_store"
                                                    href="{{ route('user.wishlist.store', $popular_category_item->id) }}"
                                                    title="{{ __('Wishlist') }}"><i class="icon-heart"></i></a>
                                                <a data-target="{{ route('fornt.compare.product', $popular_category_item->id) }}"
                                                    class="product-button product_compare" href="javascript:;"
                                                    title="{{ __('Compare') }}"><i class="icon-repeat"></i></a>
                                                @include('includes.item_footer', [
                                                    'sitem' => $popular_category_item,
                                                ])
                                            </div>
                                        </div>
                                        <div class="product-card-body">
                                            <div class="product-category"><a
                                                    href="{{ route('front.catalog') . '?category=' . $popular_category_item->category->slug }}">{{ $popular_category_item->category->name }}</a>
                                            </div>
                                            <h3 class="product-title"><a
                                                    href="{{ route('front.product', $popular_category_item->slug) }}">
                                                    {{ Str::limit($popular_category_item->name, 35) }}
                                                </a></h3>
                                             <div class="rating-stars">
                                                 {!! Helper::renderStarRating($popular_category_item->customer_rating) !!}
                                                 @if($popular_category_item->customer_rating > 0)
                                                     <span class="text-muted ml-1" style="font-size: 11px; font-weight: 600;">({{ number_format($popular_category_item->customer_rating, 1) }})</span>
                                                 @endif
                                             </div>
                                            <h4 class="product-price">
                                                @if ($popular_category_item->previous_price != 0)
                                                    <del>{{ PriceHelper::setPreviousPrice($popular_category_item->previous_price) }}</del>
                                                @endif
                                                {{ PriceHelper::grandCurrencyPrice($popular_category_item) }}
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>
        </section>
    @endif

    @if ($setting->is_three_c_b_second == 1)
        <div class="bannner-section mt-60">
            <div class="container ">
                <div class="row gx-3">
                    <div class="col-md-4">
                        <a href="{{ $banner_secend['url1'] }}" class="genius-banner">
                            <img class="lazy" data-src="{{ url('/core/public/storage/images/' . $banner_secend['img1']) }}"
                                alt="">
                            <div class="inner-content">
                                @if (isset($banner_secend['subtitle1']))
                                    <p>{{ $banner_secend['subtitle1'] }}</p>
                                @endif

                                @if (isset($banner_secend['title1']))
                                    <h4>{{ $banner_secend['title1'] }}</h4>
                                @endif
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ $banner_secend['url2'] }}" class="genius-banner">
                            <img class="lazy" data-src="{{ url('/core/public/storage/images/' . $banner_secend['img2']) }}"
                                alt="">
                            <div class="inner-content">
                                @if (isset($banner_secend['subtitle2']))
                                    <p>{{ $banner_secend['subtitle2'] }}</p>
                                @endif

                                @if (isset($banner_secend['title2']))
                                    <h4> {{ $banner_secend['title2'] }}</h4>
                                @endif
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ $banner_secend['url3'] }}" class="genius-banner">
                            <img class="lazy" data-src="{{ url('/core/public/storage/images/' . $banner_secend['img3']) }}"
                                alt="">
                            <div class="inner-content">
                                @if (isset($banner_secend['subtitle3']))
                                    <p>{{ $banner_secend['subtitle3'] }} </p>
                                @endif

                                @if (isset($banner_secend['title3']))
                                    <h4>{{ $banner_secend['title3'] }}</h4>
                                @endif
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($extra_settings->is_t1_falsh == 1)
        @php
            $bundleDeals = (isset($flash_deals) && $flash_deals->isNotEmpty())
                ? $flash_deals
                : (class_exists(\App\Models\Deal::class) ? \App\Models\Deal::where('status', 1)->with(['dealItems.item'])->get() : collect());
        @endphp
        @if($bundleDeals->isNotEmpty())
        <div class="flash-sell-new-section mt-50">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="section-title d-flex align-items-center justify-content-between">
                            <h2 class="h3">{{ __('Shop Bundles') }}</h2>
                            <a class="btn btn-outline-primary btn-sm" href="{{ route('front.deal.index') }}">{{ __('View All Bundles') }}</a>
                        </div>
                    </div>
                </div>
                <!-- Desktop View -->
                <div class="row d-none d-md-flex">
                    @foreach($bundleDeals as $deal)
                        @include('front.deals.card', ['deal' => $deal, 'column' => 'col-lg-3 col-md-4 col-sm-6 mb-4'])
                    @endforeach
                </div>
                <!-- Mobile View: 4 Bundles, 2 in 1 row -->
                <div class="row gx-2 gy-2 d-flex d-md-none mobile-bundle-grid">
                    @foreach($bundleDeals->take(4) as $deal)
                        @include('front.deals.card', ['deal' => $deal, 'column' => 'col-6 mb-2 px-1'])
                    @endforeach
                </div>
            </div>
        </div>
        @include('front.deals.countdown-script')
        @endif
    @endif

    @if ($setting->is_two_c_b == 1)
        <div class="bannner-section mt-50">
            <div class="container ">
                <div class="row gx-3">
                    <div class="col-md-6">
                        <a href="{{ $banner_third['url1'] }}" class="genius-banner">
                            <img class="lazy" data-src="{{ url('/core/public/storage/images/' . $banner_third['img1']) }}"
                                alt="">
                            <div class="inner-content">
                                @if (isset($banner_third['subtitle1']))
                                    <p>{{ $banner_third['subtitle1'] }}</p>
                                @endif
                                @if (isset($banner_third['title1']))
                                    <h4>{{ $banner_third['title1'] }}</h4>
                                @endif
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ $banner_third['url2'] }}" class="genius-banner">
                            <img class="lazy" data-src="{{ url('/core/public/storage/images/' . $banner_third['img2']) }}"
                                alt="">
                            <div class="inner-content">
                                @if (isset($banner_third['subtitle2']))
                                    <p>{{ $banner_third['subtitle2'] }} </p>
                                @endif
                                @if (isset($banner_third['title2']))
                                    <h4>{{ $banner_third['title2'] }}</h4>
                                @endif
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($setting->is_featured_category == 1)
        <section class="selected-product-section featured_cat_sec sps-two mt-50">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="section-title">
                            <h2 class="h3">{{ $feature_category_title ?? __('Newly Listed Products') }}</h2>
                            <div class="right-area">
                                <a class="right_link" href="{{ route('front.newly_listed') }}">{{ __('View All') }} <i
                                        class="icon-chevron-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row g-3 gx-2 gx-md-3" id="feature_category_view">
                    @forelse ($feature_category_items as $feature_category_item)
                        <div class="col-6 col-md-4 col-lg-3 mb-3">
                            <div class="product-card">
                                <div class="product-thumb">

                                    @if (!$feature_category_item->is_stock())
                                        <div
                                            class="product-badge bg-secondary border-default text-body
                                    ">
                                            {{ __('out of stock') }}</div>
                                    @endif
                                    @if ($feature_category_item->previous_price && $feature_category_item->previous_price != 0)
                                        <div class="product-badge product-badge2 bg-info">
                                            -{{ PriceHelper::DiscountPercentage($feature_category_item) }}</div>
                                    @endif
                                    <img src="{{ url('/core/public/storage/images/' . ($feature_category_item->photo ?: $feature_category_item->thumbnail)) }}" class="lazy"
                                        data-src="{{ url('/core/public/storage/images/' . ($feature_category_item->photo ?: $feature_category_item->thumbnail)) }}"
                                        alt="{{ $feature_category_item->name ?? 'Product' }}">
                                    <div class="product-button-group"><a class="product-button wishlist_store"
                                            href="{{ route('user.wishlist.store', $feature_category_item->id) }}"
                                            title="{{ __('Wishlist') }}"><i class="icon-heart"></i></a>
                                        <a data-target="{{ route('fornt.compare.product', $feature_category_item->id) }}"
                                            class="product-button product_compare" href="javascript:;"
                                            title="{{ __('Compare') }}"><i class="icon-repeat"></i></a>

                                        @include('includes.item_footer', [
                                            'sitem' => $feature_category_item,
                                        ])

                                    </div>
                                </div>
                                <div class="product-card-body">
                                    <div class="product-category"><a
                                            href="{{ route('front.catalog') . '?category=' . $feature_category_item->category->slug }}">{{ $feature_category_item->category->name }}</a>
                                    </div>
                                    <h3 class="product-title"><a
                                            href="{{ route('front.product', $feature_category_item->slug) }}">
                                            {{ Str::limit($feature_category_item->name, 35) }}
                                        </a></h3>
                                     <div class="rating-stars">
                                         {!! Helper::renderStarRating($feature_category_item) !!}
                                         @if($feature_category_item->rating > 0)
                                             <span class="text-muted ml-1" style="font-size: 11px; font-weight: 600;">({{ number_format($feature_category_item->rating, 1) }})</span>
                                         @endif
                                     </div>
                                    <h4 class="product-price">
                                        @if ($feature_category_item->previous_price != 0)
                                            <del>{{ PriceHelper::setPreviousPrice($feature_category_item->previous_price) }}</del>
                                        @endif
                                        {{ PriceHelper::grandCurrencyPrice($feature_category_item) }}
                                    </h4>
                                </div>

                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5">
                            <p class="text-muted">{{ __('No newly listed products found.') }}</p>
                        </div>
                    @endforelse

                </div>
            </div>
        </section>
    @endif

    <!-- Browse Categories Section Start -->
    @if(isset($browse_categories) && $browse_categories->count() > 0)
        <section class="browse-categories-section page_section mt-50 mb-30">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="section-title">
                            <div>
                                <h2 class="h3">{{ __('Browse Categories') }}</h2>
                                <p class="text-muted mb-0 d-none d-md-block" style="font-size: 13.5px; margin-top: 2px;">
                                    {{ __('Explore our wide range of categories and collections') }}
                                </p>
                            </div>
                            <div class="right-area">
                                <a class="right_link" href="javascript:;" id="openAllCategoriesBtn" role="button" aria-label="{{ __('View All Categories') }}">
                                    {{ __('View All') }} <i class="icon-chevron-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Homepage Grid: Max 10 on Desktop (5 per row), Max 9 on Mobile (3 per row) --}}
                <div class="browse-cat-grid">
                    @foreach ($browse_categories->take(10) as $index => $bcategory)
                        <div class="browse-cat-item {{ $index >= 9 ? 'browse-cat-desktop-only' : '' }}">
                            <a href="{{ route('front.catalog', ['category' => $bcategory->slug]) }}" class="browse-cat-card">
                                <div class="browse-cat-thumb">
                                    @if(!empty($bcategory->photo))
                                        <img src="{{ url('/core/public/storage/images/' . $bcategory->photo) }}" class="lazy" data-src="{{ url('/core/public/storage/images/' . $bcategory->photo) }}" alt="{{ $bcategory->name }}">
                                    @else
                                        <div class="browse-cat-icon-fallback">
                                            <i class="fas fa-th-large"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="browse-cat-info">
                                    <h4 class="browse-cat-title">{{ $bcategory->name }}</h4>
                                    @if(isset($bcategory->items_count) && $bcategory->items_count > 0)
                                        <span class="browse-cat-count">{{ $bcategory->items_count }} {{ __('Items') }}</span>
                                    @else
                                        <span class="browse-cat-count text-primary">{{ __('Shop Now →') }}</span>
                                    @endif
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- All Categories Modal -->
        <div class="modal fade" id="allCategoriesModal" tabindex="-1" role="dialog" aria-labelledby="allCategoriesModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable" role="document">
                <div class="modal-content all-cat-modal-content">
                    <div class="modal-header border-0 pb-2 pt-3 px-3 px-md-4 bg-white">
                        <div class="d-flex align-items-center">
                            <div class="cat-modal-header-icon mr-2">
                                <i class="fas fa-th-large text-primary"></i>
                            </div>
                            <div>
                                <h5 class="modal-title font-weight-bold text-dark mb-0" id="allCategoriesModalLabel" style="font-size: 18px;">
                                    {{ __('All Categories') }}
                                </h5>
                                <span class="text-muted small">{{ $browse_categories->count() }} {{ __('Categories Available') }}</span>
                            </div>
                        </div>
                        <button type="button" class="close custom-modal-close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close">
                            <i class="icon-x"></i>
                        </button>
                    </div>

                    {{-- Search Input inside Modal --}}
                    <div class="px-3 px-md-4 py-2 border-bottom" style="background: #f8fafc;">
                        <div class="input-group cat-modal-search-box">
                            <span class="input-group-text bg-transparent border-0 text-muted px-3">
                                <i class="icon-search"></i>
                            </span>
                            <input type="text" id="allCatModalSearch" class="form-control border-0 bg-transparent py-2" placeholder="{{ __('Search category...') }}" style="box-shadow: none; font-size: 14px;">
                            <button type="button" id="clearCatSearch" class="btn btn-link text-muted pr-3" style="text-decoration: none; display: none;">
                                <i class="icon-x"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Modal Body with All Categories --}}
                    <div class="modal-body p-3 p-md-4" style="background: #f8fafc; max-height: 65vh; overflow-y: auto;">
                        <div class="all-modal-cat-grid" id="modalCategoriesGrid">
                            @foreach ($browse_categories as $bcat)
                                <div class="modal-cat-item-wrap" data-name="{{ strtolower($bcat->name) }}">
                                    <a href="{{ route('front.catalog', ['category' => $bcat->slug]) }}" class="modal-cat-card">
                                        <div class="modal-cat-thumb">
                                            @if(!empty($bcat->photo))
                                                <img src="{{ url('/core/public/storage/images/' . $bcat->photo) }}" class="lazy" data-src="{{ url('/core/public/storage/images/' . $bcat->photo) }}" alt="{{ $bcat->name }}">
                                            @else
                                                <div class="modal-cat-icon-fallback">
                                                    <i class="fas fa-th-large"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="modal-cat-info">
                                            <h5 class="modal-cat-title">{{ $bcat->name }}</h5>
                                            @if(isset($bcat->items_count) && $bcat->items_count > 0)
                                                <span class="modal-cat-count">{{ $bcat->items_count }} {{ __('Items') }}</span>
                                            @else
                                                <span class="modal-cat-count text-primary">{{ __('Shop Now →') }}</span>
                                            @endif
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                        <div id="noCategoriesFound" class="text-center py-5 d-none">
                            <i class="icon-search text-muted mb-2" style="font-size: 32px; opacity: 0.5;"></i>
                            <p class="text-muted mb-0 font-weight-500">{{ __('No categories matching your search.') }}</p>
                        </div>
                    </div>

                    <div class="modal-footer border-top bg-white px-3 px-md-4 py-2 d-flex justify-content-between align-items-center">
                        <a href="{{ route('front.catalog') }}" class="btn btn-sm btn-outline-primary" style="border-radius: 8px; font-weight: 600;">
                            <i class="icon-grid mr-1"></i> {{ __('View All Products in Shop') }}
                        </a>
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal" data-dismiss="modal" style="border-radius: 8px;">
                            {{ __('Close') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <style>
            /* Browse Categories Section Styles */
            .browse-categories-section {
                position: relative;
            }
            .browse-cat-grid {
                display: grid;
                grid-template-columns: repeat(5, 1fr);
                gap: 14px;
                margin-top: 10px;
            }
            .browse-cat-item {
                display: flex;
                width: 100%;
            }
            .browse-cat-card {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                background: #ffffff;
                border: 1px solid #e9ecef;
                border-radius: 16px;
                padding: 16px 10px 14px;
                text-align: center;
                text-decoration: none !important;
                color: #1e293b !important;
                transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
                width: 100%;
                height: 100%;
                position: relative;
                overflow: hidden;
            }
            .browse-cat-card:hover {
                transform: translateY(-4px);
                border-color: #3b82f6;
                box-shadow: 0 10px 24px rgba(59, 130, 246, 0.12);
                color: #2563eb !important;
            }
            .browse-cat-thumb {
                width: 64px;
                height: 64px;
                border-radius: 50%;
                background: #f8fafc;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-bottom: 10px;
                overflow: hidden;
                border: 2px solid #edf2f7;
                transition: all 0.25s ease;
                flex-shrink: 0;
            }
            .browse-cat-card:hover .browse-cat-thumb {
                transform: scale(1.08);
                border-color: #93c5fd;
                background: #eff6ff;
            }
            .browse-cat-thumb img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }
            .browse-cat-icon-fallback {
                font-size: 22px;
                color: #3b82f6;
            }
            .browse-cat-info {
                width: 100%;
                display: flex;
                flex-direction: column;
                align-items: center;
            }
            .browse-cat-title {
                font-size: 13px;
                font-weight: 700;
                line-height: 1.25;
                margin-bottom: 4px;
                color: inherit;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
                text-overflow: ellipsis;
                min-height: 32px;
            }
            .browse-cat-count {
                font-size: 11.5px;
                color: #64748b;
                font-weight: 500;
                line-height: 1;
            }
            .browse-cat-card:hover .browse-cat-count {
                color: #2563eb;
                font-weight: 600;
            }

            /* Responsive Mobile View: 3 items per row, max 9 items total */
            @media (max-width: 767.98px) {
                .browse-cat-grid {
                    grid-template-columns: repeat(3, 1fr);
                    gap: 8px;
                }
                .browse-cat-desktop-only {
                    display: none !important;
                }
                .browse-cat-card {
                    padding: 10px 5px 8px;
                    border-radius: 12px;
                }
                .browse-cat-thumb {
                    width: 46px;
                    height: 46px;
                    margin-bottom: 6px;
                }
                .browse-cat-title {
                    font-size: 11px;
                    min-height: 26px;
                    margin-bottom: 2px;
                }
                .browse-cat-count {
                    font-size: 9.5px;
                }
            }

            /* All Categories Modal Styling */
            .all-cat-modal-content {
                border-radius: 20px;
                overflow: hidden;
                border: none;
                box-shadow: 0 20px 40px rgba(0,0,0,0.15);
            }
            .cat-modal-header-icon {
                width: 36px;
                height: 36px;
                border-radius: 10px;
                background: #eff6ff;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 16px;
            }
            .custom-modal-close {
                background: #f1f5f9;
                border-radius: 50%;
                width: 32px;
                height: 32px;
                opacity: 1;
                display: flex;
                align-items: center;
                justify-content: center;
                border: none;
                cursor: pointer;
                color: #475569;
                font-size: 14px;
                padding: 0;
            }
            .custom-modal-close:hover {
                background: #e2e8f0;
                color: #0f172a;
            }
            .cat-modal-search-box {
                border-radius: 12px;
                overflow: hidden;
                border: 1px solid #e2e8f0;
                background: #fff;
            }
            .all-modal-cat-grid {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 12px;
            }
            @media (min-width: 992px) {
                .all-modal-cat-grid {
                    grid-template-columns: repeat(5, 1fr);
                    gap: 12px;
                }
            }
            @media (max-width: 767.98px) {
                .all-modal-cat-grid {
                    grid-template-columns: repeat(3, 1fr);
                    gap: 8px;
                }
            }
            .modal-cat-item-wrap {
                display: flex;
                width: 100%;
            }
            .modal-cat-card {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                background: #ffffff;
                border: 1px solid #e2e8f0;
                border-radius: 14px;
                padding: 12px 6px 10px;
                text-align: center;
                text-decoration: none !important;
                color: #1e293b !important;
                transition: all 0.2s ease;
                width: 100%;
                box-shadow: 0 1px 3px rgba(0,0,0,0.02);
            }
            .modal-cat-card:hover {
                transform: translateY(-3px);
                border-color: #3b82f6;
                box-shadow: 0 8px 20px rgba(59, 130, 246, 0.12);
                color: #2563eb !important;
            }
            .modal-cat-thumb {
                width: 52px;
                height: 52px;
                border-radius: 50%;
                background: #f8fafc;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-bottom: 6px;
                overflow: hidden;
                border: 2px solid #edf2f7;
                flex-shrink: 0;
                transition: transform 0.2s ease;
            }
            .modal-cat-card:hover .modal-cat-thumb {
                transform: scale(1.06);
                border-color: #93c5fd;
                background: #eff6ff;
            }
            .modal-cat-thumb img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }
            .modal-cat-icon-fallback {
                font-size: 18px;
                color: #3b82f6;
            }
            .modal-cat-info {
                width: 100%;
                display: flex;
                flex-direction: column;
                align-items: center;
            }
            .modal-cat-title {
                font-size: 12px;
                font-weight: 700;
                line-height: 1.25;
                margin-bottom: 2px;
                color: inherit;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
                text-overflow: ellipsis;
                min-height: 28px;
            }
            .modal-cat-count {
                font-size: 10.5px;
                color: #64748b;
                font-weight: 500;
            }
            .modal-cat-card:hover .modal-cat-count {
                color: #2563eb;
                font-weight: 600;
            }
        </style>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var openBtn = document.getElementById('openAllCategoriesBtn');
                var modalEl = document.getElementById('allCategoriesModal');
                
                if (openBtn && modalEl) {
                    openBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        if (window.bootstrap && bootstrap.Modal) {
                            var modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                            modal.show();
                        } else if (typeof jQuery !== 'undefined' && typeof jQuery.fn.modal !== 'undefined') {
                            jQuery(modalEl).modal('show');
                        } else {
                            modalEl.style.display = 'block';
                            modalEl.classList.add('show');
                            document.body.classList.add('modal-open');
                        }
                    });
                }

                // Modal close handler fallback
                document.querySelectorAll('.custom-modal-close, [data-bs-dismiss="modal"], [data-dismiss="modal"]').forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        if (window.bootstrap && bootstrap.Modal) {
                            var modal = bootstrap.Modal.getInstance(modalEl);
                            if (modal) modal.hide();
                        }
                        if (typeof jQuery !== 'undefined' && typeof jQuery.fn.modal !== 'undefined') {
                            jQuery(modalEl).modal('hide');
                        }
                        if (modalEl) {
                            modalEl.classList.remove('show');
                            modalEl.style.display = 'none';
                        }
                        document.body.classList.remove('modal-open');
                        var backdrops = document.querySelectorAll('.modal-backdrop');
                        backdrops.forEach(function(b) { b.remove(); });
                    });
                });

                // Real-time search filter for categories in modal
                var searchInput = document.getElementById('allCatModalSearch');
                var clearBtn = document.getElementById('clearCatSearch');
                var noResults = document.getElementById('noCategoriesFound');
                
                if (searchInput) {
                    searchInput.addEventListener('input', function() {
                        var q = this.value.toLowerCase().trim();
                        var items = document.querySelectorAll('#modalCategoriesGrid .modal-cat-item-wrap');
                        var matched = 0;

                        if (clearBtn) {
                            clearBtn.style.display = q.length > 0 ? 'inline-block' : 'none';
                        }

                        items.forEach(function(item) {
                            var catName = item.getAttribute('data-name') || '';
                            if (catName.indexOf(q) !== -1) {
                                item.style.display = 'flex';
                                matched++;
                            } else {
                                item.style.display = 'none';
                            }
                        });

                        if (noResults) {
                            if (matched === 0 && q.length > 0) {
                                noResults.classList.remove('d-none');
                            } else {
                                noResults.classList.add('d-none');
                            }
                        }
                    });
                }

                if (clearBtn && searchInput) {
                    clearBtn.addEventListener('click', function() {
                        searchInput.value = '';
                        searchInput.dispatchEvent(new Event('input'));
                        searchInput.focus();
                    });
                }
            });
        </script>
    @endif
    <!-- Browse Categories Section End -->

    @if ($setting->is_blogs == 1)
        <div class="blog-section-h page_section mt-50 mb-30">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="section-title">
                            <h2 class="h3">{{ __('Our Blog') }}</h2>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="home-blog-slider owl-carousel">
                            @foreach ($posts as $post)
                                <div class="slider-item">
                                    <a href="{{ route('front.blog.details', $post->slug) }}" class="blog-post">
                                        <div class="post-thumb">
                                            <img class="lazy"
                                                data-src="{{ url('/core/public/storage/images/' . json_decode($post->photo, true)[array_key_first(json_decode($post->photo, true))]) }}"
                                                alt="Blog Post">
                                        </div>
                                        <div class="post-body">

                                            <h3 class="post-title"> {{ Str::limit($post->title, 55) }}
                                            </h3>
                                            <ul class="post-meta">

                                                <li><i class="icon-user"></i>{{ __('Admin') }}</li>
                                                <li><i
                                                        class="icon-clock"></i>{{ date('jS F, Y', strtotime($post->created_at)) }}
                                                </li>
                                            </ul>
                                            <p>{{ Str::limit(strip_tags($post->details), 120) }}
                                            </p>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
