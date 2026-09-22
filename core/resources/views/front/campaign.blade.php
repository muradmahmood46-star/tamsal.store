@extends('master.front')

@section('title')
    {{ __('Most Selling Products') }}
@endsection

@section('meta')
<meta name="keywords" content="{{$setting->meta_keywords}}">
<meta name="description" content="{{$setting->meta_description}}">
@endsection

@section('content')
<div class="page-title">
    <div class="container">
      <div class="row">
          <div class="col-lg-12">
            <ul class="breadcrumbs">
                <li><a href="{{route('front.index')}}">{{__('Home')}}</a>
                </li>
                <li class="separator"></li>
                <li><a href="{{route('front.campaign')}}">{{ __('Most Selling Products') }}</a>
                </li>
              </ul>
          </div>
      </div>
    </div>
  </div>
  <!-- Page Content-->

    <div class="deal-of-day-section pb-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title">
                        <h2 class="h3">{{ __('Most Selling Products') }}</h2>
                    </div>
                </div>
            </div>
            <div class="row g-3">
                @foreach ($campaign_items as $compaign_item)
                @php
                    $item = isset($compaign_item->item) ? $compaign_item->item : $compaign_item;
                @endphp
                <div class="col-gd">
                <div class="product-card">
                    <div class="product-thumb">
                        @if ($item->is_stock())
                            @if($item->is_type != 'undefine')
                            <div class="product-badge
                            @if($item->is_type == 'feature')
                            bg-warning
                            @elseif($item->is_type == 'new')
                            bg-danger
                            @elseif($item->is_type == 'top')
                            bg-info
                            @elseif($item->is_type == 'best')
                            bg-dark
                            @elseif($item->is_type == 'flash_deal')
                            bg-success
                            @endif
                            ">{{ ucfirst(str_replace('_',' ',$item->is_type)) }}</div>
                            @endif
                        @else
                            <div class="product-badge bg-secondary border-default text-body">{{__('out of stock')}}</div>
                        @endif

                        @if($item->previous_price && $item->previous_price !=0)
                            <div class="product-badge product-badge2 bg-info"> -{{PriceHelper::DiscountPercentage($item)}}</div>
                        @endif
                        @if($item->is_free_delivery == 1)
                            <div class="product-badge product-badge-free-delivery">{{ __('Free Delivery') }}</div>
                        @endif

                        <img src="{{url('/core/public/storage/images/'.($item->photo ?: $item->thumbnail))}}" alt="{{ $item->name ?? 'Product' }}">
                        <div class="product-button-group">
                            <a class="product-button wishlist_store" href="{{route('user.wishlist.store',$item->id)}}" title="{{__('Wishlist')}}"><i class="icon-heart"></i></a>
                            <a data-target="{{route('fornt.compare.product',$item->id)}}" class="product-button product_compare" href="javascript:;" title="{{__('Compare')}}"><i class="icon-repeat"></i></a>
                            @include('includes.item_footer',['sitem' => $item])
                        </div>
                    </div>
                    <div class="product-card-body">

                        <div class="product-category">
                            @if($item->category)
                                <a href="{{route('front.catalog').'?category='.$item->category->slug}}">{{$item->category->name}}</a>
                            @endif
                        </div>
                        <h3 class="product-title"><a href="{{route('front.product',$item->slug)}}">
                            {{ Str::limit($item->name, 70) }}
                        </a></h3>
                        <div class="rating-stars">
                            {!! Helper::renderStarRating($item) !!}
                            @if($item && $item->rating > 0)
                                <span class="text-muted ml-1" style="font-size: 11.5px; font-weight: 600;">({{ number_format($item->rating, 1) }})</span>
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
                @endforeach
            </div>
        </div>
    </div>
@endsection
