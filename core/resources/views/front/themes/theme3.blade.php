@extends('master.front')
@section('meta')
    <meta name="keywords" content="{{ $setting->meta_keywords }}">
    <meta name="description" content="{{ $setting->meta_description }}">
@endsection

@section('content')


   

    @if ($extra_settings->is_t3_slider == 1)
        <div  class="hero-area3" >
            <div class="background"></div>
            <div class="heroarea-slider owl-carousel">
                @foreach ($sliders as $slider)
                <div class="item" style="background: url('{{ url('/core/public/storage/images/' . $slider->photo) }}')">
                    <div class="container">
                    <div class="row">
                        <div class="col-xl-5 col-lg-6 d-flex align-self-center">
                            <div class="left-content color-white">
                                <div class="content">
                                    <div class="layer-1">
                                        <p class="subtitle">
                                            {{ $slider->title }}
                                        </p>
                                    </div>
                                    <div class="layer-2">
                                        <h1 class="title">
                                            {{ $slider->details }}
                                        </h1>
                                    </div>
                                    @if($slider->link != '#')
                                    <div class="layer-3">
                                        <div class="links">
                                        <a href="{{ $slider->link }}" class="btn btn-primary">
                                            <span>
                                                {{ __('Buy Now') }}
                                            </span>
                                        </a>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-7 col-lg-6 order-first order-lg-last">
                        <div class="layer-4">
                            <div class="right-img">
                            <img class="img-fluid full-img" src="{{ url('/core/public/storage/images/' . $slider->logo) }}" alt="">
                            </div>
                        </div>
                        </div>
                    </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    @endif

    @if ($extra_settings->is_t3_service_section == 1)
        <section class="service-section mt-30 pt-0">
            <div class="container">
                <div class="row">
                    @foreach ($services as $service)
                        <div class="col-lg-3 col-sm-6 text-center mb-30">
                            <div class="single-service single-service2">
                                <img src="{{ url('/core/public/storage/images/'.$service->photo) }}" alt="Shipping">
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

    @if ($extra_settings->is_t3_3_column_banner_first == 1)
        <div class="bannner-section mt-60">
            <div class="container ">
                <div class="row gx-3">
                    <div class="col-md-4 mb-3">
                        <a href="{{$banner_first['firsturl1']}}" class="genius-banner modern-banner-card">
                            <div class="banner-text-content">
                                @if (!empty($banner_first['subtitle1']))
                                    <span class="banner-subtitle">{{$banner_first['subtitle1']}}</span>
                                @endif
                                @if (!empty($banner_first['title1']))
                                    <h4 class="banner-title">{{$banner_first['title1']}}</h4>
                                @endif
                            </div>
                            <div class="banner-img-box">
                                <img src="{{ url('/core/public/storage/images/'.$banner_first['img1']) }}" alt="{{ $banner_first['title1'] ?? '' }}">
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4 mb-3">
                        <a href="{{$banner_first['firsturl2']}}" class="genius-banner modern-banner-card">
                            <div class="banner-text-content">
                                @if (!empty($banner_first['subtitle2']))
                                    <span class="banner-subtitle">{{$banner_first['subtitle2']}}</span>
                                @endif
                                @if (!empty($banner_first['title2']))
                                    <h4 class="banner-title">{{$banner_first['title2']}}</h4>
                                @endif
                            </div>
                            <div class="banner-img-box">
                                <img src="{{ url('/core/public/storage/images/'.$banner_first['img2']) }}" alt="{{ $banner_first['title2'] ?? '' }}">
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4 mb-3">
                        <a href="{{$banner_first['firsturl3']}}" class="genius-banner modern-banner-card">
                            <div class="banner-text-content">
                                @if (!empty($banner_first['subtitle3']))
                                    <span class="banner-subtitle">{{$banner_first['subtitle3']}}</span>
                                @endif
                                @if (!empty($banner_first['title3']))
                                    <h4 class="banner-title">{{$banner_first['title3']}}</h4>
                                @endif
                            </div>
                            <div class="banner-img-box">
                                <img src="{{ url('/core/public/storage/images/'.$banner_first['img3']) }}" alt="{{ $banner_first['title3'] ?? '' }}">
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($extra_settings->is_t3_pecialpick == 1)
        <section class="selected-product-section mt-50">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="section-title section-title2  section-title3">
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
                                            <div class="product-thumb" >
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


    @if ($extra_settings->is_t3_falsh == 1)
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

    @if ($extra_settings->is_t3_3_column_banner_second == 1)
    <div class="bannner-section mt-60">
        <div class="container ">
            <div class="row gx-3">
                <div class="col-md-4">
                    <a href="{{$banner_secend['url1']}}" class="genius-banner">
                        <img class="lazy" data-src="{{ url('/core/public/storage/images/'.$banner_secend['img1']) }}" alt="">
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
                    <a href="{{$banner_secend['url2']}}" class="genius-banner">
                        <img class="lazy" data-src="{{ url('/core/public/storage/images/'.$banner_secend['img2']) }}" alt="">
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
                    <a href="{{$banner_secend['url3']}}" class="genius-banner">
                        <img class="lazy" data-src="{{ url('/core/public/storage/images/'.$banner_secend['img3']) }}" alt="">
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

    @if ($extra_settings->is_t3_popular_category == 1)
        <section class="newproduct-section mt-50">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="section-title section-title2 section-title3">
                            <h2 class="h3">{{ __('Top Rated Products') }}</h2>
                            <div class="right-area">
                                <a class="right_link" href="{{ route('front.top_rated') }}">{{ __('View All') }} <i class="icon-chevron-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row" id="popular_category_view">
                    <div class="col-lg-12">
                        <div class="popular-category-slider  owl-carousel">
                            @foreach ($popular_category_items as $popular_category_item)
                            <div class="slider-item">
                                <div class="product-card">
                                    <div class="product-thumb">
                                        @if (!$popular_category_item->is_stock())
                                            <div class="product-badge bg-secondary border-default text-body
                                            ">{{__('out of stock')}}</div>
                                        @endif
                                        @if($popular_category_item->previous_price && $popular_category_item->previous_price !=0)
                                        <div class="product-badge product-badge2 bg-info"> -{{PriceHelper::DiscountPercentage($popular_category_item)}}</div>
                                        @endif
                                            <img src="{{url('/core/public/storage/images/'.($popular_category_item->photo ?: $popular_category_item->thumbnail))}}" class="lazy" data-src="{{url('/core/public/storage/images/'.($popular_category_item->photo ?: $popular_category_item->thumbnail))}}" alt="{{ $popular_category_item->name ?? 'Product' }}">
                                        <div class="product-button-group"><a class="product-button wishlist_store" href="{{route('user.wishlist.store',$popular_category_item->id)}}" title="{{__('Wishlist')}}"><i class="icon-heart"></i></a>
                                            <a data-target="{{route('fornt.compare.product',$popular_category_item->id)}}" class="product-button product_compare" href="javascript:;" title="{{__('Compare')}}"><i class="icon-repeat"></i></a>

                                        @include('includes.item_footer',['sitem' => $popular_category_item])
                                        </div>
                                    </div>
                                    <div class="product-card-body">
                                        <div class="product-category"><a href="{{route('front.catalog').'?category='.$popular_category_item->category->slug}}">{{$popular_category_item->category->name}}</a></div>
                                        <h3 class="product-title"><a href="{{route('front.product',$popular_category_item->slug)}}">
                                            {{ Str::limit($popular_category_item->name,35) }}
                                        </a></h3>
                                         <div class="rating-stars">
                                             {!! Helper::renderStarRating($popular_category_item->customer_rating) !!}
                                             @if($popular_category_item->customer_rating > 0)
                                                 <span class="text-muted ml-1" style="font-size: 11px; font-weight: 600;">({{ number_format($popular_category_item->customer_rating, 1) }})</span>
                                             @endif
                                         </div>
                                        <h4 class="product-price">
                                            @if ($popular_category_item->previous_price != 0)
                                            <del>{{PriceHelper::setPreviousPrice($popular_category_item->previous_price)}}</del>
                                            @endif

                                            {{PriceHelper::grandCurrencyPrice($popular_category_item)}}
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

    @if ($extra_settings->is_t3_three_column_category == 1)
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
                                        <a class="product-thumb" href="{{route('front.product',$two_column_category_item->slug)}}">
                                            @if(!$two_column_category_item->is_stock())
                                                <div class="product-badge bg-secondary border-default text-body
                                                ">{{__('out of stock')}}</div>
                                                @endif

                                            <img src="{{url('/core/public/storage/images/'.($two_column_category_item->photo ?: $two_column_category_item->thumbnail))}}" class="lazy" data-src="{{url('/core/public/storage/images/'.($two_column_category_item->photo ?: $two_column_category_item->thumbnail))}}" alt="{{ $two_column_category_item->name ?? 'Product' }}"></a>
                                        <div class="product-card-body">
                                            <h3 class="product-title"><a href="{{route('front.product',$two_column_category_item->slug)}}">
                                                {{ Str::limit($two_column_category_item->name,40) }}
                                            </a></h3>
                                            <div class="rating-stars">
                                                {!! Helper::renderStarRating($two_column_category_item) !!}
                                                @if($two_column_category_item->rating > 0)
                                                    <span class="text-muted ml-1" style="font-size: 11px; font-weight: 600;">({{ number_format($two_column_category_item->rating, 1) }})</span>
                                                @endif
                                            </div>
                                            <h4 class="product-price">
                                            @if ($two_column_category_item->previous_price != 0)
                                            <del>{{PriceHelper::setPreviousPrice($two_column_category_item->previous_price)}}</del>
                                            @endif
                                                {{PriceHelper::grandCurrencyPrice($two_column_category_item)}}
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

    @if ($extra_settings->is_t3_2_column_banner == 1)
    <div class="bannner-section mt-50">
        <div class="container ">
            <div class="row gx-3">
                <div class="col-md-6">
                    <a href="{{$banner_third['url1']}}" class="genius-banner">
                        <img class="lazy" data-src="{{ url('/core/public/storage/images/'.$banner_third['img1']) }}" alt="">
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
                    <a href="{{$banner_third['url2']}}" class="genius-banner">
                        <img class="lazy" data-src="{{ url('/core/public/storage/images/'.$banner_third['img2']) }}" alt="">
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

    @if ($extra_settings->is_t3_blog_section == 1)
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
@endsection

