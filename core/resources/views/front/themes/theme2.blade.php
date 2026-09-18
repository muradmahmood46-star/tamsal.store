@extends('master.front')
@section('meta')
    <meta name="keywords" content="{{ $setting->meta_keywords }}">
    <meta name="description" content="{{ $setting->meta_description }}">
@endsection

@section('content')


    @if ($extra_settings->is_t2_slider == 1)
        <style>
            /* Hero Slider & Banner Visual Upgrades */
            .hero-slider .item {
                position: relative;
                overflow: hidden;
            }
            .hero-slider .item-inner {
                position: relative;
                z-index: 2;
            }
            .hero-slider .title {
                animation: heroLoopFloat 4s cubic-bezier(0.4, 0, 0.2, 1) infinite;
            }
            .hero-slider .subtitle {
                animation: heroBadgeLoop 4s cubic-bezier(0.4, 0, 0.2, 1) 0.2s infinite;
            }
            .hero-slider .btn {
                animation: heroBtnLoop 4s cubic-bezier(0.4, 0, 0.2, 1) 0.4s infinite;
            }
            .modern-banner-card .banner-title,
            .genius-banner h4 {
                animation: heroLoopFloat 4s cubic-bezier(0.4, 0, 0.2, 1) infinite;
            }
            .modern-banner-card .banner-subtitle,
            .genius-banner p {
                animation: heroBadgeLoop 4s cubic-bezier(0.4, 0, 0.2, 1) 0.2s infinite;
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

            @media (max-width: 575px) {
                .slider-area-wrapper {
                    padding-top: 10px;
                }
                .hero-slider {
                    margin: 0 10px !important;
                    border-radius: 12px !important;
                }
                .hero-slider .item {
                    height: auto !important;
                    aspect-ratio: 16 / 9 !important;
                    background-size: cover !important;
                    background-position: center !important;
                }
            }
        </style>
        <div class="slider-area-wrapper mt-0">
            <div class="container">
                <div class="row">
                    <div class="@if(isset($hero_banner) && (!empty($hero_banner['img1']) || !empty($hero_banner['img2']))) col-lg-8 @else col-lg-12 @endif">
                        <!-- Main Slider-->
                        <div class="hero-slider">
                            <div class="hero-slider-main owl-carousel dots-inside">
                                @foreach ($sliders as $slider)
                                    @php
                                        $sliderPhoto = $slider->photo ?? '';
                                        $sliderExt = strtolower(pathinfo($sliderPhoto, PATHINFO_EXTENSION));
                                        $isLottieSlider = in_array($sliderExt, ['json', 'lottie']);

                                        $sliderLogo = $slider->logo ?? '';
                                        $logoExt = strtolower(pathinfo($sliderLogo, PATHINFO_EXTENSION));
                                        $isLottieLogo = in_array($logoExt, ['json', 'lottie']);
                                    @endphp
                                    <div class="item
                                        @if (DB::table('languages')->where('is_default', 1)->first()->rtl == 1) d-flex justify-content-end @endif
                                        "
                                        @if (!$isLottieSlider)
                                            style="background: url('{{ url('/core/public/storage/images/' . $slider->photo) }}'); position: relative; overflow: hidden;"
                                        @else
                                            style="background: #f8fafc; position: relative; overflow: hidden;"
                                        @endif
                                    >
                                        @if ($isLottieSlider)
                                            <lottie-player src="{{ url('/core/public/storage/images/' . $slider->photo) }}" background="transparent" speed="1" loop autoplay style="position: absolute; top:0; left:0; width:100%; height:100%; object-fit:contain; z-index:1;"></lottie-player>
                                        @endif
                                        <div class="container" style="position: relative; z-index: 2;">
                                            <div class="row">
                                                <div class="col-lg-8">
                                                    <div class="item-inner">
                                                        <div class="from-bottom">
                                                            @if (!empty($slider->logo))
                                                                @if ($isLottieLogo)
                                                                    <lottie-player src="{{ url('/core/public/storage/images/' . $slider->logo) }}" background="transparent" speed="1" loop autoplay class="d-inline-block brand-logo" style="width: 100px; height: 50px; object-fit: contain;"></lottie-player>
                                                                @else
                                                                    <img class="d-inline-block brand-logo"
                                                                        src="{{ url('/core/public/storage/images/' . $slider->logo) }}" alt="logo">
                                                                @endif
                                                            @endif

                                                            <div class="title text-body">{{ $slider->title }}</div>
                                                            <div class="subtitle text-body">{{ $slider->details }}</div>
                                                        </div>
                                                        @if($slider->link != '#')
                                                        <a class="btn btn-primary scale-up delay-1" href="{{ $slider->link }}">
                                                            <span>{{ __('Buy Now') }}</span>
                                                        </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    @if (isset($hero_banner) && (!empty($hero_banner['img1']) || !empty($hero_banner['img2'])))
                        <div class="col-lg-4 d-none d-lg-block">
                            @if (!empty($hero_banner['img1']))
                                <a href="{{ $hero_banner['url1'] ?? '#' }}" class="sright-image mb-3" style="position: relative; overflow: hidden; display: block; border-radius: 12px;">
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
                            @endif

                            @if (!empty($hero_banner['img2']))
                                <a href="{{ $hero_banner['url2'] ?? '#' }}" class="sright-image mb-0" style="position: relative; overflow: hidden; display: block; border-radius: 12px;">
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
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    @if ($extra_settings->is_t2_service_section == 1)
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
        <section class="service-section mt-30 pt-0">
            <div class="container">
                <div class="row" style="margin-left: -5px; margin-right: -5px;">
                    @foreach ($services as $service)
                        <div class="col-6 col-lg-3 text-center mb-30">
                            <div class="single-service single-service2 mobile-compact-service" style="height: 100%;">
                                @php
                                    $srv_ext = strtolower(pathinfo($service->photo ?? '', PATHINFO_EXTENSION));
                                @endphp
                                @if (in_array($srv_ext, ['json', 'lottie']))
                                    <lottie-player src="{{ url('/core/public/storage/images/' . $service->photo) }}" background="transparent" speed="1" loop autoplay style="max-width: 45px; height: 45px; margin: 0 auto 8px; display: block;"></lottie-player>
                                @else
                                    <img src="{{ url('/core/public/storage/images/' . $service->photo) }}" alt="Shipping">
                                @endif
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

    @if ($extra_settings->is_t2_3_column_banner_first == 1)
        <div class="bannner-section mt-30">
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
                                @php
                                    $b1_img1 = $banner_first['img1'] ?? '';
                                    $b1_ext1 = strtolower(pathinfo($b1_img1, PATHINFO_EXTENSION));
                                @endphp
                                @if (in_array($b1_ext1, ['json', 'lottie']))
                                    <lottie-player src="{{ url('/core/public/storage/images/' . $b1_img1) }}" background="transparent" speed="1" loop autoplay style="width: 100%; height: 80px; object-fit: contain;"></lottie-player>
                                @else
                                    <img src="{{ url('/core/public/storage/images/' . $b1_img1) }}" alt="{{ $banner_first['title1'] ?? '' }}">
                                @endif
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
                                @php
                                    $b1_img2 = $banner_first['img2'] ?? '';
                                    $b1_ext2 = strtolower(pathinfo($b1_img2, PATHINFO_EXTENSION));
                                @endphp
                                @if (in_array($b1_ext2, ['json', 'lottie']))
                                    <lottie-player src="{{ url('/core/public/storage/images/' . $b1_img2) }}" background="transparent" speed="1" loop autoplay style="width: 100%; height: 80px; object-fit: contain;"></lottie-player>
                                @else
                                    <img src="{{ url('/core/public/storage/images/' . $b1_img2) }}" alt="{{ $banner_first['title2'] ?? '' }}">
                                @endif
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
                                @php
                                    $b1_img3 = $banner_first['img3'] ?? '';
                                    $b1_ext3 = strtolower(pathinfo($b1_img3, PATHINFO_EXTENSION));
                                @endphp
                                @if (in_array($b1_ext3, ['json', 'lottie']))
                                    <lottie-player src="{{ url('/core/public/storage/images/' . $b1_img3) }}" background="transparent" speed="1" loop autoplay style="width: 100%; height: 80px; object-fit: contain;"></lottie-player>
                                @else
                                    <img src="{{ url('/core/public/storage/images/' . $b1_img3) }}" alt="{{ $banner_first['title3'] ?? '' }}">
                                @endif
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
                                @php
                                    $b1_img4 = $banner_first['img4'] ?? ($banner_first['img3'] ?? '');
                                    $b1_ext4 = strtolower(pathinfo($b1_img4, PATHINFO_EXTENSION));
                                @endphp
                                @if (in_array($b1_ext4, ['json', 'lottie']))
                                    <lottie-player src="{{ url('/core/public/storage/images/' . $b1_img4) }}" background="transparent" speed="1" loop autoplay style="width: 100%; height: 80px; object-fit: contain;"></lottie-player>
                                @else
                                    <img src="{{ url('/core/public/storage/images/' . $b1_img4) }}" alt="{{ $banner_first['title4'] ?? ($banner_first['title3'] ?? '') }}">
                                @endif
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif


    @if ($extra_settings->is_t2_falsh == 1)
        <div class="flash-sell-new-section mt-50">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12 ">
                        <div class="section-title section-title2 section-title3section-title section-title2 section-title3">
                            <h2 class="h3">{{ __('Flash Deal') }}</h2>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-content">
                            <div class="flash-deal-slider owl-carousel">
                                @foreach ($products->orderBy('id', 'DESC')->get() as $item)
                                    @if ($item->is_type == 'flash_deal' && $item->date != null)
                                        <div class="slider-item">
                                            <div class="product-card ">
                                                <div class="product-thumb">
                                                    @if (!$item->is_stock())
                                                        <div
                                                            class="product-badge bg-secondary border-default text-body
                                                ">
                                                            {{ __('out of stock') }}</div>
                                                    @endif
                                                    @if ($item->previous_price && $item->previous_price != 0)
                                                        <div class="product-badge product-badge2 bg-info">
                                                            -{{ PriceHelper::DiscountPercentage($item) }}</div>
                                                    @endif
                                                    <img src="{{ url('/core/public/storage/images/' . ($item->photo ?: $item->thumbnail)) }}" class="lazy"
                                                        data-src="{{ url('/core/public/storage/images/' . ($item->photo ?: $item->thumbnail)) }}"
                                                        alt="{{ $item->name ?? 'Product' }}">
                                                    <div class="product-button-group"><a
                                                            class="product-button wishlist_store"
                                                            href="{{ route('user.wishlist.store', $item->id) }}"
                                                            title="{{ __('Wishlist') }}"><i class="icon-heart"></i></a>
                                                        <a data-target="{{ route('fornt.compare.product', $item->id) }}"
                                                            class="product-button product_compare" href="javascript:;"
                                                            title="{{ __('Compare') }}"><i class="icon-repeat"></i></a>
                                                        @include('includes.item_footer', [
                                                            'sitem' => $item,
                                                        ])
                                                    </div>
                                                </div>
                                                <div class="product-card-inner">
                                                    <div class="product-card-body">

                                                        <div class="product-category"><a
                                                                href="{{ route('front.catalog') . '?category=' . $item->category->slug }}">{{ $item->category->name }}</a>
                                                        </div>
                                                        <h3 class="product-title"><a
                                                                href="{{ route('front.product', $item->slug) }}">
                                                                {{ Str::limit($item->name, 50) }}
                                                            </a></h3>
                                                        <div class="rating-stars">
                                                            {!! Helper::renderStarRating($item) !!}
                                                            @if($item->rating > 0)
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
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif


    @if ($extra_settings->is_t2_new_product == 1)
        <section class="selected-product-section mt-50 theme2">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="section-title section-title2 section-title3">
                            <h2 class="h3">{{ __('New  Products') }}</h2>
                        </div>
                    </div>
                </div>
                <div class="row">

                    <div class="col-lg-12">

                        <div class="features-slider  owl-carousel">
                            @foreach ($products->orderBy('id', 'DESC')->get() as $key => $item)
                                @if ($key <= 9)
                                    <div class="slider-item">
                                        <div class="product-card ">
                                            <div class="product-thumb">
                                                @if (!$item->is_stock())
                                                    <div
                                                        class="product-badge bg-secondary border-default text-body
                                                ">
                                                        {{ __('out of stock') }}</div>
                                                @endif
                                                @if ($item->previous_price && $item->previous_price != 0)
                                                    <div class="product-badge product-badge2 bg-info">
                                                        -{{ PriceHelper::DiscountPercentage($item) }}</div>
                                                @endif
                                                <img src="{{ url('/core/public/storage/images/' . ($item->photo ?: $item->thumbnail)) }}" class="lazy"
                                                    data-src="{{ url('/core/public/storage/images/' . ($item->photo ?: $item->thumbnail)) }}"
                                                    alt="{{ $item->name ?? 'Product' }}">
                                                <div class="product-button-group"><a class="product-button wishlist_store"
                                                        href="{{ route('user.wishlist.store', $item->id) }}"
                                                        title="{{ __('Wishlist') }}"><i class="icon-heart"></i></a>
                                                    <a data-target="{{ route('fornt.compare.product', $item->id) }}"
                                                        class="product-button product_compare" href="javascript:;"
                                                        title="{{ __('Compare') }}"><i class="icon-repeat"></i></a>
                                                    @include('includes.item_footer', ['sitem' => $item])
                                                </div>
                                            </div>
                                            <div class="product-card-inner">
                                                <div class="product-card-body">
                                                    <div class="product-category"><a
                                                            href="{{ route('front.catalog') . '?category=' . $item->category->slug }}">{{ $item->category->name }}</a>
                                                    </div>
                                                    <h3 class="product-title"><a
                                                            href="{{ route('front.product', $item->slug) }}">
                                                            {{ Str::limit($item->name, 35) }}
                                                        </a></h3>
                                                    <div class="rating-stars">
                                                        {!! Helper::renderStarRating($item) !!}
                                                        @if($item->rating > 0)
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
                                    </div>
                                @else
                                @break
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif

@if ($extra_settings->is_t2_3_column_banner_second == 1)
    <div class="bannner-section mt-60">
        <div class="container ">
            <div class="row gx-3">
                <div class="col-md-4">
                    <a href="{{ $banner_secend['url1'] ?? '#' }}" class="genius-banner">
                        @php
                            $b2_img1 = $banner_secend['img1'] ?? '';
                            $b2_ext1 = strtolower(pathinfo($b2_img1, PATHINFO_EXTENSION));
                        @endphp
                        @if (in_array($b2_ext1, ['json', 'lottie']))
                            <lottie-player src="{{ url('/core/public/storage/images/' . $b2_img1) }}" background="transparent" speed="1" loop autoplay style="width: 100%; height: 180px; object-fit: contain;"></lottie-player>
                        @else
                            <img class="lazy" data-src="{{ url('/core/public/storage/images/' . $b2_img1) }}"
                                alt="">
                        @endif
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
                    <a href="{{ $banner_secend['url2'] ?? '#' }}" class="genius-banner">
                        @php
                            $b2_img2 = $banner_secend['img2'] ?? '';
                            $b2_ext2 = strtolower(pathinfo($b2_img2, PATHINFO_EXTENSION));
                        @endphp
                        @if (in_array($b2_ext2, ['json', 'lottie']))
                            <lottie-player src="{{ url('/core/public/storage/images/' . $b2_img2) }}" background="transparent" speed="1" loop autoplay style="width: 100%; height: 180px; object-fit: contain;"></lottie-player>
                        @else
                            <img class="lazy" data-src="{{ url('/core/public/storage/images/' . $b2_img2) }}"
                                alt="">
                        @endif
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
                    <a href="{{ $banner_secend['url3'] ?? '#' }}" class="genius-banner">
                        @php
                            $b2_img3 = $banner_secend['img3'] ?? '';
                            $b2_ext3 = strtolower(pathinfo($b2_img3, PATHINFO_EXTENSION));
                        @endphp
                        @if (in_array($b2_ext3, ['json', 'lottie']))
                            <lottie-player src="{{ url('/core/public/storage/images/' . $b2_img3) }}" background="transparent" speed="1" loop autoplay style="width: 100%; height: 180px; object-fit: contain;"></lottie-player>
                        @else
                            <img class="lazy" data-src="{{ url('/core/public/storage/images/' . $b2_img3) }}"
                                alt="">
                        @endif
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

@if ($extra_settings->is_t2_featured_product == 1)
    <section class="selected-product-section mt-50 theme2">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title section-title2 section-title3">
                        <h2 class="h3">{{ __('Featured Products') }}</h2>
                    </div>
                </div>
            </div>
            <div class="row">

                <div class="col-lg-12">

                    <div class="features-slider  owl-carousel">
                        @foreach ($products->orderBy('id', 'DESC')->get() as $item)
                            @if ($item->is_type == 'feature')
                                <div class="slider-item">
                                    <div class="product-card ">
                                        <div class="product-thumb">
                                            @if (!$item->is_stock())
                                                <div
                                                    class="product-badge bg-secondary border-default text-body
                                                    ">
                                                    {{ __('out of stock') }}</div>
                                            @endif
                                            @if ($item->previous_price && $item->previous_price != 0)
                                                <div class="product-badge product-badge2 bg-info">
                                                    -{{ PriceHelper::DiscountPercentage($item) }}</div>
                                            @endif
                                            <img src="{{ url('/core/public/storage/images/' . ($item->photo ?: $item->thumbnail)) }}" class="lazy"
                                                data-src="{{ url('/core/public/storage/images/' . ($item->photo ?: $item->thumbnail)) }}"
                                                alt="{{ $item->name ?? 'Product' }}">
                                            <div class="product-button-group"><a class="product-button wishlist_store"
                                                    href="{{ route('user.wishlist.store', $item->id) }}"
                                                    title="{{ __('Wishlist') }}"><i class="icon-heart"></i></a>
                                                <a data-target="{{ route('fornt.compare.product', $item->id) }}"
                                                    class="product-button product_compare" href="javascript:;"
                                                    title="{{ __('Compare') }}"><i class="icon-repeat"></i></a>
                                                @include('includes.item_footer', ['sitem' => $item])
                                            </div>
                                        </div>
                                        <div class="product-card-inner">
                                            <div class="product-card-body">
                                                <div class="product-category"><a
                                                        href="{{ route('front.catalog') . '?category=' . $item->category->slug }}">{{ $item->category->name }}</a>
                                                </div>
                                                <h3 class="product-title"><a
                                                        href="{{ route('front.product', $item->slug) }}">
                                                        {{ Str::limit($item->name, 35) }}
                                                    </a></h3>
                                                <div class="rating-stars">
                                                    {!! Helper::renderStarRating($item) !!}
                                                    @if($item->rating > 0)
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
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </section>
@endif

@if ($extra_settings->is_t2_bestseller_product == 1)
    <section class="selected-product-section mt-50  theme2">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title section-title2  section-title3">
                        <h2 class="h3">{{ __('Best Seller') }}</h2>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="features-slider  owl-carousel">
                        @foreach ($products->orderBy('id', 'DESC')->get() as $item)
                            @if ($item->is_type == 'best')
                                <div class="slider-item">
                                    <div class="product-card ">
                                        <div class="product-thumb">
                                            @if (!$item->is_stock())
                                                <div
                                                    class="product-badge bg-secondary border-default text-body
                                                ">
                                                    {{ __('out of stock') }}</div>
                                            @endif
                                            @if ($item->previous_price && $item->previous_price != 0)
                                                <div class="product-badge product-badge2 bg-info">
                                                    -{{ PriceHelper::DiscountPercentage($item) }}</div>
                                            @endif
                                            <img src="{{ url('/core/public/storage/images/' . ($item->photo ?: $item->thumbnail)) }}" class="lazy"
                                                data-src="{{ url('/core/public/storage/images/' . ($item->photo ?: $item->thumbnail)) }}"
                                                alt="{{ $item->name ?? 'Product' }}">
                                            <div class="product-button-group"><a class="product-button wishlist_store"
                                                    href="{{ route('user.wishlist.store', $item->id) }}"
                                                    title="{{ __('Wishlist') }}"><i class="icon-heart"></i></a>
                                                <a data-target="{{ route('fornt.compare.product', $item->id) }}"
                                                    class="product-button product_compare" href="javascript:;"
                                                    title="{{ __('Compare') }}"><i class="icon-repeat"></i></a>
                                                @include('includes.item_footer', ['sitem' => $item])
                                            </div>

                                        </div>
                                        <div class="product-card-inner">
                                            <div class="product-card-body">
                                                <div class="product-category"><a
                                                        href="{{ route('front.catalog') . '?category=' . $item->category->slug }}">{{ $item->category->name }}</a>
                                                </div>
                                                <h3 class="product-title"><a
                                                        href="{{ route('front.product', $item->slug) }}">
                                                        {{ Str::limit($item->name, 35) }}
                                                    </a></h3>
                                                <div class="rating-stars">
                                                    {!! Helper::renderStarRating($item) !!}
                                                    @if($item->rating > 0)
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
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </section>
@endif

@if ($extra_settings->is_t2_toprated_product == 1)
    <section class="selected-product-section mt-50  theme2">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title section-title2  section-title3">
                        <h2 class="h3">{{ __('Top Rated') }}</h2>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">

                    <div class="features-slider  owl-carousel">
                        @foreach ($products->orderBy('id', 'DESC')->get() as $item)
                            @if ($item->is_type == 'top')
                                <div class="slider-item">
                                    <div class="product-card ">
                                        <div class="product-thumb">
                                            @if (!$item->is_stock())
                                                <div
                                                    class="product-badge bg-secondary border-default text-body
                                                ">
                                                    {{ __('out of stock') }}</div>
                                            @endif
                                            @if ($item->previous_price && $item->previous_price != 0)
                                                <div class="product-badge product-badge2 bg-info">
                                                    -{{ PriceHelper::DiscountPercentage($item) }}</div>
                                            @endif
                                            <img src="{{ url('/core/public/storage/images/' . ($item->photo ?: $item->thumbnail)) }}" class="lazy"
                                                data-src="{{ url('/core/public/storage/images/' . ($item->photo ?: $item->thumbnail)) }}"
                                                alt="{{ $item->name ?? 'Product' }}">
                                            <div class="product-button-group"><a class="product-button wishlist_store"
                                                    href="{{ route('user.wishlist.store', $item->id) }}"
                                                    title="{{ __('Wishlist') }}"><i class="icon-heart"></i></a>
                                                <a data-target="{{ route('fornt.compare.product', $item->id) }}"
                                                    class="product-button product_compare" href="javascript:;"
                                                    title="{{ __('Compare') }}"><i class="icon-repeat"></i></a>
                                                @include('includes.item_footer', ['sitem' => $item])
                                            </div>
                                        </div>
                                        <div class="product-card-inner">
                                            <div class="product-card-body">
                                                <div class="product-category"><a
                                                        href="{{ route('front.catalog') . '?category=' . $item->category->slug }}">{{ $item->category->name }}</a>
                                                </div>
                                                <h3 class="product-title"><a
                                                        href="{{ route('front.product', $item->slug) }}">
                                                        {{ Str::limit($item->name, 35) }}
                                                    </a></h3>
                                                <div class="rating-stars">
                                                    {!! Helper::renderStarRating($item) !!}
                                                    @if($item->rating > 0)
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
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </section>
@endif

@if ($extra_settings->is_t2_2_column_banner == 1)
    <div class="bannner-section mt-50">
        <div class="container ">
            <div class="row gx-3">
                <div class="col-md-6">
                    <a href="{{ $banner_third['url1'] ?? '#' }}" class="genius-banner">
                        @php
                            $b3_img1 = $banner_third['img1'] ?? '';
                            $b3_ext1 = strtolower(pathinfo($b3_img1, PATHINFO_EXTENSION));
                        @endphp
                        @if (in_array($b3_ext1, ['json', 'lottie']))
                            <lottie-player src="{{ url('/core/public/storage/images/' . $b3_img1) }}" background="transparent" speed="1" loop autoplay style="width: 100%; height: 220px; object-fit: contain;"></lottie-player>
                        @else
                            <img class="lazy" data-src="{{ url('/core/public/storage/images/' . $b3_img1) }}"
                                alt="">
                        @endif
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
                    <a href="{{ $banner_third['url2'] ?? '#' }}" class="genius-banner">
                        @php
                            $b3_img2 = $banner_third['img2'] ?? '';
                            $b3_ext2 = strtolower(pathinfo($b3_img2, PATHINFO_EXTENSION));
                        @endphp
                        @if (in_array($b3_ext2, ['json', 'lottie']))
                            <lottie-player src="{{ url('/core/public/storage/images/' . $b3_img2) }}" background="transparent" speed="1" loop autoplay style="width: 100%; height: 220px; object-fit: contain;"></lottie-player>
                        @else
                            <img class="lazy" data-src="{{ url('/core/public/storage/images/' . $b3_img2) }}"
                                alt="">
                        @endif
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

@if ($extra_settings->is_t2_three_column_category == 1)
    <div class="flash-sell-area three_column_product mt-50">
        <div class="container">
            <div class="row gx-3 justify-content-center">
                @foreach ($two_column_categoriess as $two_column_key => $two_column_category)
                    <div class="col-xl-4 col-lg-6">
                        <div class="section-title">
                            <h2 class="h3">{{ $two_column_category['name']->name }}</h2>
                        </div>
                        <div class="main-content">
                            <div class="newproduct-slider owl-carousel">
                                @foreach ($two_column_categoriess[$two_column_key]['items']->chunk(4) as $two_column_category_itemt)
                                    <div class="slider-item">
                                        @foreach ($two_column_category_itemt as $two_column_category_item)
                                            <div class="product-card p-col">
                                                <a class="product-thumb"
                                                    href="{{ route('front.product', $two_column_category_item->slug) }}">
                                                    @if (!$two_column_category_item->is_stock())
                                                        <div
                                                            class="product-badge bg-secondary border-default text-body
                                                ">
                                                            {{ __('out of stock') }}</div>
                                                    @endif

                                                    <img src="{{ url('/core/public/storage/images/' . ($two_column_category_item->photo ?: $two_column_category_item->thumbnail)) }}" class="lazy"
                                                        data-src="{{ url('/core/public/storage/images/' . ($two_column_category_item->photo ?: $two_column_category_item->thumbnail)) }}"
                                                        alt="{{ $two_column_category_item->name ?? 'Product' }}">
                                                </a>
                                                <div class="product-card-body">
                                                    <h3 class="product-title"><a
                                                            href="{{ route('front.product', $two_column_category_item->slug) }}">
                                                            {{ Str::limit($two_column_category_item->name, 40) }}
                                                        </a></h3>
                                                    <div class="rating-stars">
                                                        {!! Helper::renderStarRating($two_column_category_item) !!}
                                                        @if($two_column_category_item->rating > 0)
                                                            <span class="text-muted ml-1" style="font-size: 11px; font-weight: 600;">({{ number_format($two_column_category_item->rating, 1) }})</span>
                                                        @endif
                                                    </div>
                                                    <h4 class="product-price">
                                                        @if ($two_column_category_item->previous_price != 0)
                                                            <del>{{ PriceHelper::setPreviousPrice($two_column_category_item->previous_price) }}</del>
                                                        @endif
                                                        {{ PriceHelper::grandCurrencyPrice($two_column_category_item) }}
                                                    </h4>
                                                </div>
                                            </div>
                                        @endforeach

                                    </div>
                                @endforeach
                            </div>

                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </div>
@endif

@if ($extra_settings->is_t2_blog_section == 1)
    <div class="blog-section-h page_section mt-50 mb-30">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title section-title2 section-title3">
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
                                        <p>{{ Str::limit(strip_tags($post->details), 120, '...') }}
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
