@extends('master.front')
@section('meta')
    <meta name="keywords" content="{{ $setting->meta_keywords }}">
    <meta name="description" content="{{ $setting->meta_description }}">
@endsection

@section('content')

 

    @if ($extra_settings->is_t4_slider == 1)
        <div class="hero-area3 hero-area4">
            <div class="background"></div>
            <div class="heroarea-slider owl-carousel">
                @foreach ($sliders as $slider)
                    @php
                        $sliderPhoto = $slider->photo ?? '';
                        $sliderExt = strtolower(pathinfo($sliderPhoto, PATHINFO_EXTENSION));
                        $isLottieSlider = in_array($sliderExt, ['json', 'lottie']);
                    @endphp
                    <a href="{{$slider->link}}">
                        <div class="item"
                            @if (!$isLottieSlider)
                                style="background: url('{{ url('/core/public/storage/images/' . $slider->photo) }}'); position: relative; overflow: hidden;"
                            @else
                                style="background: #f8fafc; position: relative; overflow: hidden;"
                            @endif
                        >
                            @if ($isLottieSlider)
                                <lottie-player src="{{ url('/core/public/storage/images/' . $slider->photo) }}" background="transparent" speed="1" loop autoplay style="width: 100%; height: 100%; object-fit: cover;"></lottie-player>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    @if ($extra_settings->is_t4_featured_banner == 1)
        <div class="featured-for-home3 mt-60">
            <div class="container">
                <div class="row">
                    <div class="col-lg-3 col-md-3 col-sm-12">
                        <a href="{{isset($home_page4_banner['url1']) ? $home_page4_banner['url1'] : ''}}" class="h3-category">
                            @php
                                $h4_img1 = $home_page4_banner['img1'] ?? '';
                                $h4_ext1 = strtolower(pathinfo($h4_img1, PATHINFO_EXTENSION));
                            @endphp
                            @if (in_array($h4_ext1, ['json', 'lottie']))
                                <lottie-player src="{{url('/core/public/storage/images/'.$h4_img1)}}" background="transparent" speed="1" loop autoplay style="width: 100%; height: 120px; object-fit: contain;"></lottie-player>
                            @else
                                <img class="lazy" data-src="{{url('/core/public/storage/images/'.$h4_img1)}}" alt="">
                            @endif
                            <h4>{{isset($home_page4_banner['label1']) ? $home_page4_banner['label1'] : ''}}</h4>
                        </a>
                        <a href="{{isset($home_page4_banner['url2']) ? $home_page4_banner['url2'] : ''}}" class="h3-category">
                            @php
                                $h4_img2 = $home_page4_banner['img2'] ?? '';
                                $h4_ext2 = strtolower(pathinfo($h4_img2, PATHINFO_EXTENSION));
                            @endphp
                            @if (in_array($h4_ext2, ['json', 'lottie']))
                                <lottie-player src="{{url('/core/public/storage/images/'.$h4_img2)}}" background="transparent" speed="1" loop autoplay style="width: 100%; height: 120px; object-fit: contain;"></lottie-player>
                            @else
                                <img class="lazy" data-src="{{url('/core/public/storage/images/'.$h4_img2)}}" alt="">
                            @endif
                            <h4>{{isset($home_page4_banner['label2']) ? $home_page4_banner['label2'] : ''}}</h4>
                        </a>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 middleImage">
                        <a href="{{isset($home_page4_banner['url3']) ? $home_page4_banner['url3'] : ''}}" class="h3-category">
                            @php
                                $h4_img3 = $home_page4_banner['img3'] ?? '';
                                $h4_ext3 = strtolower(pathinfo($h4_img3, PATHINFO_EXTENSION));
                            @endphp
                            @if (in_array($h4_ext3, ['json', 'lottie']))
                                <lottie-player src="{{url('/core/public/storage/images/'.$h4_img3)}}" background="transparent" speed="1" loop autoplay style="width: 100%; height: 260px; object-fit: contain;"></lottie-player>
                            @else
                                <img class="lazy" data-src="{{url('/core/public/storage/images/'.$h4_img3)}}" alt="">
                            @endif
                            <h4>{{isset($home_page4_banner['label3']) ? $home_page4_banner['label3'] : ''}}</h4>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12">
                        <a href="{{isset($home_page4_banner['url4']) ? $home_page4_banner['url4'] : ''}}" class="h3-category">
                            @php
                                $h4_img4 = $home_page4_banner['img4'] ?? '';
                                $h4_ext4 = strtolower(pathinfo($h4_img4, PATHINFO_EXTENSION));
                            @endphp
                            @if (in_array($h4_ext4, ['json', 'lottie']))
                                <lottie-player src="{{url('/core/public/storage/images/'.$h4_img4)}}" background="transparent" speed="1" loop autoplay style="width: 100%; height: 120px; object-fit: contain;"></lottie-player>
                            @else
                                <img src="{{url('/core/public/storage/images/'.$h4_img4)}}" alt="">
                            @endif
                            <h4>{{isset($home_page4_banner['label4']) ? $home_page4_banner['label4'] : ''}}</h4>
                        </a>
                        <a href="{{isset($home_page4_banner['url5']) ? $home_page4_banner['url5'] : ''}}" class="h3-category">
                            @php
                                $h4_img5 = $home_page4_banner['img5'] ?? '';
                                $h4_ext5 = strtolower(pathinfo($h4_img5, PATHINFO_EXTENSION));
                            @endphp
                            @if (in_array($h4_ext5, ['json', 'lottie']))
                                <lottie-player src="{{url('/core/public/storage/images/'.$h4_img5)}}" background="transparent" speed="1" loop autoplay style="width: 100%; height: 120px; object-fit: contain;"></lottie-player>
                            @else
                                <img src="{{url('/core/public/storage/images/'.$h4_img5)}}" alt="">
                            @endif
                            <h4>{{isset($home_page4_banner['label5']) ? $home_page4_banner['label5'] : ''}}</h4>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif


    @if ($extra_settings->is_t4_specialpick == 1)
        <section class="selected-product-section mt-50">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="section-title section-title2 section-title3">
                            <h2 class="h3">{{ __('Special Pick') }}</h2>
                        </div>
                        <div class="popular-category theme3">
                            <div class="links">
                                <a data-href="{{route('front.get.product','feature')}}" data-target="type_product_view" href="javascript:;" class="product_get active">{{__('Featured')}}</a>
                                <a data-href="{{route('front.get.product','best')}}" data-target="type_product_view" class="product_get" href="javascript:;">{{__('Best Seller')}}</a>
                                <a data-href="{{route('front.get.product','top')}}" data-target="type_product_view" class="product_get" href="javascript:;">{{__('Top Rated')}}</a>
                                <a data-href="{{route('front.get.product','new')}}" data-target="type_product_view" class="product_get" href="javascript:;">{{__('New Product')}}</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="type_product_view d-none">
                        <img  src="{{url('/core/public/storage/images/ajax_loader.gif')}}" alt="">
                    </div>
                    <div class="col-lg-12" id="type_product_view">

                        <div class="features-slider  owl-carousel" >
                            @foreach ($products->orderBy('id','DESC')->get()  as $item)
                                @if ($item->is_type == 'feature')
                                    <div class="slider-item">
                                        <div class="product-card ">
                                            <div class="product-thumb">
                                            @if (!$item->is_stock())
                                                <div class="product-badge bg-secondary border-default text-body
                                                ">{{__('out of stock')}}</div>
                                            @endif
                                            @if($item->previous_price && $item->previous_price !=0)
                                            <div class="product-badge product-badge2 bg-info"> -{{PriceHelper::DiscountPercentage($item)}}</div>
                                            @endif
                                            <img src="{{url('/core/public/storage/images/'.($item->photo ?: $item->thumbnail))}}" class="lazy" data-src="{{url('/core/public/storage/images/'.($item->photo ?: $item->thumbnail))}}" alt="{{ $item->name ?? 'Product' }}">
                                            <div class="product-button-group"><a class="product-button wishlist_store" href="{{route('user.wishlist.store',$item->id)}}" title="{{__('Wishlist')}}"><i class="icon-heart"></i></a>
                                                <a data-target="{{route('fornt.compare.product',$item->id)}}" class="product-button product_compare" href="javascript:;" title="{{__('Compare')}}"><i class="icon-repeat"></i></a>
                                                @include('includes.item_footer',['sitem' => $item])
                                            </div>
                                        </div>
                                            <div class="product-card-inner">
                                            <div class="product-card-body">
                                                <div class="product-category"><a href="{{route('front.catalog').'?category='.$item->category->slug}}">{{$item->category->name}}</a></div>
                                                <h3 class="product-title"><a href="{{route('front.product',$item->slug)}}">
                                                    {{ Str::limit($item->name,35) }}
                                                </a></h3>
                                                <div class="rating-stars">
                                                    {!! Helper::renderStarRating($item) !!}
                                                    @if($item->rating > 0)
                                                        <span class="text-muted ml-1" style="font-size: 11px; font-weight: 600;">({{ number_format($item->rating, 1) }})</span>
                                                    @endif
                                                </div>
                                                <h4 class="product-price">
                                                @if ($item->previous_price != 0)
                                                <del>{{PriceHelper::setPreviousPrice($item->previous_price)}}</del>
                                                @endif
                                                {{PriceHelper::grandCurrencyPrice($item)}}
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

    @if ($extra_settings->is_t4_3_column_banner_first == 1)
    <div class="bannner-section mt-60">
        <div class="container ">
            <div class="row gx-3">
                <div class="col-md-4 mb-3">
                    <a href="{{$banner_first['firsturl1'] ?? '#'}}" class="genius-banner modern-banner-card">
                        <div class="banner-text-content">
                            @if (!empty($banner_first['subtitle1']))
                                <span class="banner-subtitle">{{$banner_first['subtitle1']}}</span>
                            @endif
                            @if (!empty($banner_first['title1']))
                                <h4 class="banner-title">{{$banner_first['title1']}}</h4>
                            @endif
                        </div>
                        <div class="banner-img-box">
                            @php
                                $b1_img1 = $banner_first['img1'] ?? '';
                                $b1_ext1 = strtolower(pathinfo($b1_img1, PATHINFO_EXTENSION));
                            @endphp
                            @if (in_array($b1_ext1, ['json', 'lottie']))
                                <lottie-player src="{{ url('/core/public/storage/images/'.$b1_img1) }}" background="transparent" speed="1" loop autoplay style="width: 100%; height: 180px; object-fit: contain;"></lottie-player>
                            @else
                                <img src="{{ url('/core/public/storage/images/'.$b1_img1) }}" alt="{{ $banner_first['title1'] ?? '' }}">
                            @endif
                        </div>
                    </a>
                </div>
                <div class="col-md-4 mb-3">
                    <a href="{{$banner_first['firsturl2'] ?? '#'}}" class="genius-banner modern-banner-card">
                        <div class="banner-text-content">
                            @if (!empty($banner_first['subtitle2']))
                                <span class="banner-subtitle">{{$banner_first['subtitle2']}}</span>
                            @endif
                            @if (!empty($banner_first['title2']))
                                <h4 class="banner-title">{{$banner_first['title2']}}</h4>
                            @endif
                        </div>
                        <div class="banner-img-box">
                            @php
                                $b1_img2 = $banner_first['img2'] ?? '';
                                $b1_ext2 = strtolower(pathinfo($b1_img2, PATHINFO_EXTENSION));
                            @endphp
                            @if (in_array($b1_ext2, ['json', 'lottie']))
                                <lottie-player src="{{ url('/core/public/storage/images/'.$b1_img2) }}" background="transparent" speed="1" loop autoplay style="width: 100%; height: 180px; object-fit: contain;"></lottie-player>
                            @else
                                <img src="{{ url('/core/public/storage/images/'.$b1_img2) }}" alt="{{ $banner_first['title2'] ?? '' }}">
                            @endif
                        </div>
                    </a>
                </div>
                <div class="col-md-4 mb-3">
                    <a href="{{$banner_first['firsturl3'] ?? '#'}}" class="genius-banner modern-banner-card">
                        <div class="banner-text-content">
                            @if (!empty($banner_first['subtitle3']))
                                <span class="banner-subtitle">{{$banner_first['subtitle3']}}</span>
                            @endif
                            @if (!empty($banner_first['title3']))
                                <h4 class="banner-title">{{$banner_first['title3']}}</h4>
                            @endif
                        </div>
                        <div class="banner-img-box">
                            @php
                                $b1_img3 = $banner_first['img3'] ?? '';
                                $b1_ext3 = strtolower(pathinfo($b1_img3, PATHINFO_EXTENSION));
                            @endphp
                            @if (in_array($b1_ext3, ['json', 'lottie']))
                                <lottie-player src="{{ url('/core/public/storage/images/'.$b1_img3) }}" background="transparent" speed="1" loop autoplay style="width: 100%; height: 180px; object-fit: contain;"></lottie-player>
                            @else
                                <img src="{{ url('/core/public/storage/images/'.$b1_img3) }}" alt="{{ $banner_first['title3'] ?? '' }}">
                            @endif
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif


    @if ($extra_settings->is_t4_flashdeal == 1)
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
                            <div class="flash-deal-slider owl-carousel" >
                                @foreach ($products->orderBy('id','DESC')->get()  as $item)
                                @if ($item->is_type == 'flash_deal' && $item->date != null)
                                    <div class="slider-item">
                                        <div class="product-card ">
                                            <div class="product-thumb">
                                                @if (!$item->is_stock())
                                                <div class="product-badge bg-secondary border-default text-body
                                                ">{{__('out of stock')}}</div>
                                                @endif
                                                @if($item->previous_price && $item->previous_price !=0)
                                                <div class="product-badge product-badge2 bg-info"> -{{PriceHelper::DiscountPercentage($item)}}</div>
                                                @endif
                                                <img src="{{url('/core/public/storage/images/'.($item->photo ?: $item->thumbnail))}}" class="lazy" data-src="{{url('/core/public/storage/images/'.($item->photo ?: $item->thumbnail))}}" alt="{{ $item->name ?? 'Product' }}">
                                                <div class="product-button-group"><a class="product-button wishlist_store" href="{{route('user.wishlist.store',$item->id)}}" title="{{__('Wishlist')}}"><i class="icon-heart"></i></a>
                                                    <a data-target="{{route('fornt.compare.product',$item->id)}}" class="product-button product_compare" href="javascript:;" title="{{__('Compare')}}"><i class="icon-repeat"></i></a>
                                                    @include('includes.item_footer',['sitem' => $item])
                                                </div>
                                            </div>
                                            <div class="product-card-inner">
                                                <div class="product-card-body">

                                                    <div class="product-category"><a href="{{route('front.catalog').'?category='.$item->category->slug}}">{{$item->category->name}}</a></div>
                                                    <h3 class="product-title"><a href="{{route('front.product',$item->slug)}}">
                                                        {{ Str::limit($item->name,50) }}
                                                    </a></h3>
                                                    <div class="rating-stars">
                                                        {!! Helper::renderStarRating($item) !!}
                                                        @if($item->rating > 0)
                                                            <span class="text-muted ml-1" style="font-size: 11px; font-weight: 600;">({{ number_format($item->rating, 1) }})</span>
                                                        @endif
                                                    </div>
                                                    <h4 class="product-price">
                                                    @if ($item->previous_price != 0)
                                                    <del>{{PriceHelper::setPreviousPrice($item->previous_price)}}</del>
                                                    @endif

                                                    {{PriceHelper::grandCurrencyPrice($item)}}
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

    @if ($extra_settings->is_t4_3_column_banner_second == 1)
    <div class="bannner-section mt-60">
        <div class="container ">
            <div class="row gx-3">
                <div class="col-md-4">
                    <a href="{{$banner_secend['url1'] ?? '#'}}" class="genius-banner">
                        @php
                            $b2_img1 = $banner_secend['img1'] ?? '';
                            $b2_ext1 = strtolower(pathinfo($b2_img1, PATHINFO_EXTENSION));
                        @endphp
                        @if (in_array($b2_ext1, ['json', 'lottie']))
                            <lottie-player src="{{ url('/core/public/storage/images/'.$b2_img1) }}" background="transparent" speed="1" loop autoplay style="width: 100%; height: 180px; object-fit: contain;"></lottie-player>
                        @else
                            <img class="lazy" data-src="{{ url('/core/public/storage/images/'.$b2_img1) }}" alt="">
                        @endif
                        <div class="inner-content">
                            @if (isset($banner_secend['subtitle1']))
                                <p>{{$banner_secend['subtitle1']}}</p>
                            @endif

                            @if (isset($banner_secend['title1']))
                                <h4>{{$banner_secend['title1']}}</h4>
                            @endif
                        </div>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="{{$banner_secend['url2'] ?? '#'}}" class="genius-banner">
                        @php
                            $b2_img2 = $banner_secend['img2'] ?? '';
                            $b2_ext2 = strtolower(pathinfo($b2_img2, PATHINFO_EXTENSION));
                        @endphp
                        @if (in_array($b2_ext2, ['json', 'lottie']))
                            <lottie-player src="{{ url('/core/public/storage/images/'.$b2_img2) }}" background="transparent" speed="1" loop autoplay style="width: 100%; height: 180px; object-fit: contain;"></lottie-player>
                        @else
                            <img class="lazy" data-src="{{ url('/core/public/storage/images/'.$b2_img2) }}" alt="">
                        @endif
                        <div class="inner-content">
                            @if (isset($banner_secend['subtitle2']))
                                <p>{{$banner_secend['subtitle2']}}</p>
                            @endif

                            @if (isset($banner_secend['title2']))
                                <h4> {{$banner_secend['title2']}}</h4>
                            @endif
                        </div>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="{{$banner_secend['url3'] ?? '#'}}" class="genius-banner">
                        @php
                            $b2_img3 = $banner_secend['img3'] ?? '';
                            $b2_ext3 = strtolower(pathinfo($b2_img3, PATHINFO_EXTENSION));
                        @endphp
                        @if (in_array($b2_ext3, ['json', 'lottie']))
                            <lottie-player src="{{ url('/core/public/storage/images/'.$b2_img3) }}" background="transparent" speed="1" loop autoplay style="width: 100%; height: 180px; object-fit: contain;"></lottie-player>
                        @else
                            <img class="lazy" data-src="{{ url('/core/public/storage/images/'.$b2_img3) }}" alt="">
                        @endif
                        <div class="inner-content">
                            @if (isset($banner_secend['subtitle3']))
                                <p>{{$banner_secend['subtitle3']}} </p>
                            @endif

                            @if (isset($banner_secend['title3']))
                                <h4>{{$banner_secend['title3']}}</h4>
                            @endif
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if ($extra_settings->is_t4_popular_category == 1)
        @if (count($pupular_cateogry_home4)>0)
            @foreach ($pupular_cateogry_home4 as $popularcategory)

            <div class="flash-sell-area theme2 mt-50">
                <div class="container">
                    <div class="row">
                        <div class="col-xxl-12">
                            <div class="section-title section-title2 section-title3">
                                <h2 class="h3">{{$popularcategory->name}}</h2>
                            </div>
                            <div class="main-content">
                                <div class="features-slider  owl-carousel" >
                                    @foreach ($popularcategory->items  as $item)

                                        <div class="slider-item">
                                            <div class="product-card ">
                                                <div class="product-thumb" >
                                                    @if ($item->is_stock())
                                                    @if($item->is_type == 'new')
                                                    @else
                                                        <div class="product-badge
                                                            @if($item->is_type == 'feature')
                                                            bg-warning
                                                            @elseif($item->is_type == 'new')

                                                            @elseif($item->is_type == 'top')
                                                            bg-info
                                                            @elseif($item->is_type == 'best')
                                                            bg-dark
                                                            @elseif($item->is_type == 'flash_deal')
                                                            bg-success
                                                            @endif
                                                            "> {{   ucfirst(str_replace('_',' ',$item->is_type))   }}
                                                        </div>
                                                    @endif
                                                    @else
                                                <div class="product-badge bg-secondary border-default text-body
                                                ">{{__('out of stock')}}</div>
                                                @endif
                                                    @if($item->previous_price && $item->previous_price !=0)
                                                    <div class="product-badge product-badge2 bg-info"> -{{PriceHelper::DiscountPercentage($item)}}</div>
                                                    @endif
                                                <img src="{{url('/core/public/storage/images/'.($item->photo ?: $item->thumbnail))}}" class="lazy" data-src="{{url('/core/public/storage/images/'.($item->photo ?: $item->thumbnail))}}" alt="{{ $item->name ?? 'Product' }}">
                                                <div class="product-button-group"><a class="product-button wishlist_store" href="{{route('user.wishlist.store',$item->id)}}" title="{{__('Wishlist')}}"><i class="icon-heart"></i></a>
                                                    <a data-target="{{route('fornt.compare.product',$item->id)}}" class="product-button product_compare" href="javascript:;" title="{{__('Compare')}}"><i class="icon-repeat"></i></a>
                                                    @include('includes.item_footer',['sitem' => $item])
                                                </div>
                                            </div>
                                                <div class="product-card-inner">
                                                <div class="product-card-body">

                                                    <div class="product-category"><a href="{{route('front.catalog').'?category='.$item->category->slug}}">{{$item->category->name}}</a></div>
                                                    <h3 class="product-title"><a href="{{route('front.product',$item->slug)}}">
                                                        {{ Str::limit($item->name,35) }}
                                                    </a></h3>
                                                    <div class="rating-stars">
                                                        {!! Helper::renderStarRating($item) !!}
                                                        @if($item->rating > 0)
                                                            <span class="text-muted ml-1" style="font-size: 11px; font-weight: 600;">({{ number_format($item->rating, 1) }})</span>
                                                        @endif
                                                    </div>
                                                    <h4 class="product-price">
                                                    @if ($item->previous_price != 0)
                                                    <del>{{PriceHelper::setPreviousPrice($item->previous_price)}}</del>
                                                    @endif

                                                    {{PriceHelper::grandCurrencyPrice($item)}}
                                                    </h4>

                                                </div>
                                                </div>
                                            </div>
                                        </div>

                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        @endif
    @endif

    @if ($extra_settings->is_t4_2_column_banner == 1)
    <div class="bannner-section mt-50">
        <div class="container ">
            <div class="row gx-3">
                <div class="col-md-6">
                    <a href="{{$banner_third['url1'] ?? '#'}}" class="genius-banner">
                        @php
                            $b3_img1 = $banner_third['img1'] ?? '';
                            $b3_ext1 = strtolower(pathinfo($b3_img1, PATHINFO_EXTENSION));
                        @endphp
                        @if (in_array($b3_ext1, ['json', 'lottie']))
                            <lottie-player src="{{ url('/core/public/storage/images/'.$b3_img1) }}" background="transparent" speed="1" loop autoplay style="width: 100%; height: 220px; object-fit: contain;"></lottie-player>
                        @else
                            <img class="lazy" data-src="{{ url('/core/public/storage/images/'.$b3_img1) }}" alt="">
                        @endif
                        <div class="inner-content">
                            @if (isset($banner_third['subtitle1']))
                                <p>{{$banner_third['subtitle1']}}</p>
                            @endif
                            @if (isset($banner_third['title1']))
                                <h4>{{$banner_third['title1']}}</h4>
                            @endif
                        </div>
                    </a>
                </div>
                <div class="col-md-6">
                    <a href="{{$banner_third['url2'] ?? '#'}}" class="genius-banner">
                        @php
                            $b3_img2 = $banner_third['img2'] ?? '';
                            $b3_ext2 = strtolower(pathinfo($b3_img2, PATHINFO_EXTENSION));
                        @endphp
                        @if (in_array($b3_ext2, ['json', 'lottie']))
                            <lottie-player src="{{ url('/core/public/storage/images/'.$b3_img2) }}" background="transparent" speed="1" loop autoplay style="width: 100%; height: 220px; object-fit: contain;"></lottie-player>
                        @else
                            <img class="lazy" data-src="{{ url('/core/public/storage/images/'.$b3_img2) }}" alt="">
                        @endif
                        <div class="inner-content">
                            @if (isset($banner_third['subtitle2']))
                                <p>{{$banner_third['subtitle2']}} </p>
                            @endif
                            @if (isset($banner_third['title2']))
                                <h4>{{$banner_third['title2']}}</h4>
                            @endif
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if ($extra_settings->is_t4_blog_section == 1)
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
                                    <a href="{{route('front.blog.details',$post->slug)}}" class="blog-post">
                                        <div class="post-thumb">
                                            <img class="lazy" data-src="{{ url('/core/public/storage/images/' . json_decode($post->photo, true)[array_key_first(json_decode($post->photo, true))]) }}"
                                                alt="Blog Post">
                                            </div>
                                        <div class="post-body">

                                            <h3 class="post-title"> {{ Str::limit($post->title, 55) }}
                                            </h3>
                                            <ul class="post-meta">

                                                <li><i class="icon-user"></i>{{ __('Admin') }}</li>
                                                <li><i class="icon-clock"></i>{{ date('jS F, Y', strtotime($post->created_at)) }}</li>
                                            </ul>
                                            <p>{{ Str::limit(strip_tags($post->content), 120, '...') }}
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

    @if ($extra_settings->is_t4_service_section == 1)
        <section class="service-section mt-50 pt-0 mb-30">
            <div class="container">
                <div class="row">
                    @foreach ($services as $service)
                        <div class="col-lg-3 col-sm-6 text-center mb-30">
                            <div class="single-service single-service2">
                                <img class="lazy" data-src="{{ url('/core/public/storage/images/'.$service->photo) }}" alt="Shipping">
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

@endsection

