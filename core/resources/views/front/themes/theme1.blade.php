@extends('master.front')
@section('meta')
    <meta name="keywords" content="{{ $setting->meta_keywords }}">
    <meta name="description" content="{{ $setting->meta_description }}">
@endsection

@section('content')

    @if ($setting->is_slider == 1)
        <style>
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
        <div class="slider-area-wrapper">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8">
                        <!-- Main Slider-->
                        <div class="hero-slider">
                            <div class="hero-slider-main owl-carousel dots-inside">
                                @foreach ($sliders as $slider)
                                    <div class="item
                                    @if (DB::table('languages')->where('is_default', 1)->first()->rtl == 1) d-flex justify-content-end @endif
                                    "
                                        style="background: url('{{ url('/core/public/storage/images/' . $slider->photo) }}')">
                                        <div class="item-inner">
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
                            <a href="{{ $hero_banner['url1'] }}" class="sright-image">
                                <img src="{{ url('/core/public/storage/images/' . $hero_banner['img1']) }}" alt="">
                                <div class="inner-content">

                                    @if (isset($hero_banner['subtitle1']))
                                        <p>{{ $hero_banner['subtitle1'] }}</p>
                                    @endif

                                    @if (isset($hero_banner['title1']))
                                        <h4>{{ $hero_banner['title1'] }}</h4>
                                    @endif
                                </div>
                            </a>
                            <a href="{{ $hero_banner['url2'] }}" class="sright-image mb-0">
                                <img src="{{ url('/core/public/storage/images/' . $hero_banner['img2']) }}" alt="">
                                <div class="inner-content">
                                    @if (isset($hero_banner['subtitle2']))
                                        <p>{{ $hero_banner['subtitle2'] }}</p>
                                    @endif
                                    @if (isset($hero_banner['title2']))
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
            padding: 20px 24px;
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
            letter-spacing: 0.5px;
            text-transform: uppercase;
            color: #60a5fa;
            margin-bottom: 8px;
            line-height: 1.2;
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
                min-height: 105px;
            }
            .promo-card-inner {
                display: flex;
                flex-direction: column;
                align-items: flex-start;
                justify-content: space-between;
                height: 100%;
            }
            .promo-card-header-mobile {
                display: flex;
                align-items: center;
                justify-content: space-between;
                width: 100%;
                margin-bottom: 6px;
            }
            .promo-badge {
                display: inline-block;
                padding: 3px 7px;
                font-size: 9px;
                margin-bottom: 0;
                border-radius: 12px;
                max-width: calc(100% - 35px);
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }
            .promo-title {
                font-size: 13.5px;
                font-weight: 700;
                margin-top: 2px;
                margin-bottom: 4px;
                line-height: 1.25;
                display: block;
                width: 100%;
            }
            .promo-desc {
                display: none;
            }
            .promo-btn-link {
                font-size: 11px;
                font-weight: 700;
                margin-top: 4px;
                display: inline-flex;
            }
            .promo-card-icon-wrap {
                margin-left: 0;
            }
            .promo-icon-circle {
                width: 28px;
                height: 28px;
                font-size: 13px;
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
                                <div class="promo-card-header-mobile d-flex d-md-none">
                                    <span class="promo-badge"><i class="fas fa-shopping-bag mr-1"></i> {{ __('Trending') }}</span>
                                    <div class="promo-card-icon-wrap">
                                        <div class="promo-icon-circle">
                                            <i class="fas fa-shopping-basket"></i>
                                        </div>
                                    </div>
                                </div>
                                <span class="promo-badge d-none d-md-inline-flex"><i class="fas fa-shopping-bag mr-1"></i> {{ __('Trending Collection') }}</span>
                                <h3 class="promo-title">{{ __('View Products') }}</h3>
                                <p class="promo-desc">{{ __('Discover thousands of premium products at unbeatable prices') }}</p>
                                <span class="promo-btn-link">
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
                                <div class="promo-card-header-mobile d-flex d-md-none">
                                    <span class="promo-badge promo-badge-seller"><i class="fas fa-rocket mr-1"></i> {{ __('Tamsal Store') }}</span>
                                    <div class="promo-card-icon-wrap">
                                        <div class="promo-icon-circle promo-icon-seller">
                                            <i class="fas fa-store"></i>
                                        </div>
                                    </div>
                                </div>
                                <span class="promo-badge promo-badge-seller d-none d-md-inline-flex"><i class="fas fa-rocket mr-1"></i> {{ __('Sell on Tamsal Store') }}</span>
                                <h3 class="promo-title">{{ __('Open My Store') }}</h3>
                                <p class="promo-desc">{{ __('Start your business today & sell to thousands of customers') }}</p>
                                <span class="promo-btn-link promo-btn-seller">
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
                                                 {!! Helper::renderStarRating($popular_category_item) !!}
                                                 @if($popular_category_item->rating > 0)
                                                     <span class="text-muted ml-1" style="font-size: 11px; font-weight: 600;">({{ number_format($popular_category_item->rating, 1) }})</span>
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
                                                 {!! Helper::renderStarRating($popular_category_item) !!}
                                                 @if($popular_category_item->rating > 0)
                                                     <span class="text-muted ml-1" style="font-size: 11px; font-weight: 600;">({{ number_format($popular_category_item->rating, 1) }})</span>
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
