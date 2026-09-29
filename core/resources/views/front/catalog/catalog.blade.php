<div class="row g-3" id="main_div">
    @if(isset($deals) && $deals->count() > 0)
        <div class="col-12 catalog-deals-block mb-4">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border: 1px solid #86efac !important; border-radius: 14px; overflow: hidden;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap" style="gap:8px;">
                        <div class="d-flex align-items-center">
                            <span class="badge badge-success px-2 py-1 mr-2" style="font-size: 13px; letter-spacing: 0.5px;"><i class="fas fa-boxes mr-1"></i> {{ __('Matching Bundle Offers') }}</span>
                            <span class="text-success font-weight-bold small">({{ $deals->count() }} {{ __('Bundles Found') }})</span>
                        </div>
                        <a href="{{ route('front.deal.index') }}" class="btn btn-sm btn-outline-success font-weight-bold" style="border-radius: 20px; font-size: 12px; padding: 4px 12px;">
                            {{ __('All Bundles') }} <i class="icon-chevron-right ml-1"></i>
                        </a>
                    </div>
                    <div class="row g-3 gx-2 gx-md-3">
                        @foreach ($deals as $deal)
                            @include('front.deals.card', ['deal' => $deal, 'column' => 'col-xxl-3 col-md-4 col-6 mb-2'])
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($items->count() > 0)
        @php
            $categoryGroups = $items->groupBy(function($item) {
                return $item->category_id ?? 0;
            });
        @endphp

        @foreach ($categoryGroups as $catId => $catItems)
            @php
                $catModel = $catItems->first()->category ?? null;
                $catName = ($catModel && !empty($catModel->name)) ? $catModel->name : __('General Products');
                $catSlug = $catModel ? $catModel->slug : null;
            @endphp

            <div class="col-12 catalog-category-block mb-4" data-category-id="{{ $catId }}">
                {{-- Premium Modern Category Header Bar --}}
                <div class="catalog-cat-header-bar mb-3">
                    <div class="d-flex align-items-center justify-content-between flex-wrap">
                        <div class="d-flex align-items-center">
                            <div class="catalog-cat-header-icon mr-2">
                                @if($catModel && !empty($catModel->photo))
                                    <img src="{{ url('/core/public/storage/images/' . $catModel->photo) }}" alt="{{ $catName }}" class="catalog-cat-avatar-img">
                                @else
                                    <i class="fas fa-layer-group"></i>
                                @endif
                            </div>
                            <div>
                                <h3 class="catalog-cat-title-text mb-0">
                                    {{ $catName }}
                                </h3>
                                <span class="catalog-cat-count-badge">
                                    {{ $catItems->count() }} {{ __('Products available') }}
                                </span>
                            </div>
                        </div>
                        @if($catSlug)
                            <a href="{{ route('front.catalog', ['category' => $catSlug]) }}" class="catalog-cat-view-btn">
                                <span>{{ __('Explore Category') }}</span>
                                <i class="icon-chevron-right ml-1"></i>
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Products in this Category --}}
                <div class="row g-3 gx-2 gx-md-3">
                    @if ($checkType != 'list')
                        @foreach ($catItems as $item)
                            <div class="col-xxl-3 col-md-4 col-6">
                                <div class="product-card {{ $item->isPromotionActive() ? 'promoted-card' : '' }}">
                                    @if($item->isPromotionActive())
                                        <div class="promoted-tag-badge">
                                            <i class="fas fa-crown mr-1"></i> {{ $item->promotion_tag }}
                                        </div>
                                    @endif
                                    @if ($item->is_stock())
                                        @if($item->is_type != 'undefine')
                                        <div class="product-badge
                                            @if($item->is_type == 'feature') bg-warning
                                            @elseif($item->is_type == 'new') bg-danger
                                            @elseif($item->is_type == 'top') bg-info
                                            @elseif($item->is_type == 'best') bg-dark
                                            @elseif($item->is_type == 'flash_deal') bg-success
                                            @endif
                                            "> {{ __(str_replace('_',' ',$item->is_type)) }}
                                        </div>
                                        @elseif($item->is_free_delivery == 1)
                                        <div class="product-badge bg-success">{{ __('Free Delivery') }}</div>
                                        @endif
                                    @else
                                        <div class="product-badge bg-secondary border-default text-body">
                                            {{__('out of stock')}}
                                        </div>
                                    @endif

                                    @if($item->previous_price && $item->previous_price !=0)
                                        <div class="product-badge product-badge2 bg-info"> -{{PriceHelper::DiscountPercentage($item)}}</div>
                                    @endif
                                    <div class="product-thumb">
                                        <img src="{{url('/core/public/storage/images/'.($item->photo ?: $item->thumbnail))}}" class="lazy" data-src="{{url('/core/public/storage/images/'.($item->photo ?: $item->thumbnail))}}" alt="{{ $item->name ?? 'Product' }}">
                                        <div class="product-button-group">
                                            <a class="product-button wishlist_store" href="{{route('user.wishlist.store',$item->id)}}" title="{{__('Wishlist')}}"><i class="icon-heart"></i></a>
                                            <a class="product-button product_compare" href="javascript:;" data-target="{{route('fornt.compare.product',$item->id)}}" title="{{__('Compare')}}"><i class="icon-repeat"></i></a>
                                            @include('includes.item_footer',['sitem' => $item])
                                        </div>
                                    </div>
                                    <div class="product-card-body">
                                        <div class="product-category">
                                            @if($item->subcategory && $item->subcategory->name)
                                                <a href="{{route('front.catalog').'?subcategory='.$item->subcategory->slug}}">{{$item->subcategory->name}}</a>
                                            @endif
                                        </div>
                                        <h3 class="product-title"><a href="{{route('front.product',$item->slug)}}">
                                            {{ Str::limit($item->name, 70) }}
                                        </a></h3>
                                        <div class="rating-stars">
                                            {!! Helper::renderStarRating($item) !!}
                                            @if($item->rating > 0)
                                                <span class="text-muted ml-1" style="font-size: 11.5px; font-weight: 600;">({{ number_format($item->rating, 1) }})</span>
                                            @endif
                                        </div>
                                        <h4 class="product-price">
                                            @if ($item->previous_price !=0)
                                                <del>{{PriceHelper::setPreviousPrice($item->previous_price)}}</del>
                                            @endif
                                            {{PriceHelper::grandCurrencyPrice($item)}}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        @foreach ($catItems as $item)
                            <div class="col-lg-12">
                                <div class="product-card product-list {{ $item->isPromotionActive() ? 'promoted-card' : '' }}">
                                    @if($item->isPromotionActive())
                                        <div class="promoted-tag-badge">
                                            <i class="fas fa-crown mr-1"></i> {{ $item->promotion_tag }}
                                        </div>
                                    @endif
                                    <div class="product-thumb">
                                        @if ($item->is_stock())
                                            @if($item->is_type != 'undefine')
                                            <div class="product-badge
                                                @if($item->is_type == 'feature') bg-warning
                                                @elseif($item->is_type == 'new') bg-danger
                                                @elseif($item->is_type == 'top') bg-info
                                                @elseif($item->is_type == 'best') bg-dark
                                                @elseif($item->is_type == 'flash_deal') bg-success
                                                @endif
                                                ">{{ __(ucfirst(str_replace('_',' ',$item->is_type))) }}
                                            </div>
                                            @elseif($item->is_free_delivery == 1)
                                            <div class="product-badge bg-success">{{ __('Free Delivery') }}</div>
                                            @endif
                                        @else
                                            <div class="product-badge bg-secondary border-default text-body">
                                                {{__('out of stock')}}
                                            </div>
                                        @endif
                                        @if($item->previous_price && $item->previous_price !=0)
                                            <div class="product-badge product-badge2 bg-info"> -{{PriceHelper::DiscountPercentage($item)}}</div>
                                        @endif

                                        <img src="{{url('/core/public/storage/images/'.($item->photo ?: $item->thumbnail))}}" class="lazy" data-src="{{url('/core/public/storage/images/'.($item->photo ?: $item->thumbnail))}}" alt="{{ $item->name ?? 'Product' }}">
                                        <div class="product-button-group">
                                            <a class="product-button wishlist_store" href="{{route('user.wishlist.store',$item->id)}}" title="{{__('Wishlist')}}"><i class="icon-heart"></i></a>
                                            <a data-target="{{route('fornt.compare.product',$item->id)}}" class="product-button product_compare" href="javascript:;" title="{{__('Compare')}}"><i class="icon-repeat"></i></a>
                                            @include('includes.item_footer',['sitem' => $item])
                                        </div>
                                    </div>
                                    <div class="product-card-inner">
                                        <div class="product-card-body">
                                            <div class="product-category">
                                                @if($item->subcategory && $item->subcategory->name)
                                                    <a href="{{route('front.catalog').'?subcategory='.$item->subcategory->slug}}">{{$item->subcategory->name}}</a>
                                                @endif
                                            </div>
                                            <h3 class="product-title"><a href="{{route('front.product',$item->slug)}}">
                                                {{ Str::limit($item->name, 70) }}
                                            </a></h3>
                                            <div class="rating-stars">
                                                {!! Helper::renderStarRating($item) !!}
                                                @if($item->rating > 0)
                                                    <span class="text-muted ml-1" style="font-size: 11.5px; font-weight: 600;">({{ number_format($item->rating, 1) }})</span>
                                                @endif
                                            </div>
                                            <h4 class="product-price">
                                                @if ($item->previous_price !=0)
                                                    <del>{{PriceHelper::setPreviousPrice($item->previous_price)}}</del>
                                                @endif
                                                {{PriceHelper::grandCurrencyPrice($item)}}
                                            </h4>
                                            <p class="text-sm sort_details_show text-muted hidden-xs-down my-1">
                                                {{ Str::limit(strip_tags($item->sort_details), 100) }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        @endforeach
    @elseif(!isset($deals) || $deals->count() == 0)
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <h4 class="h4 mb-0 text-muted">{{ __('No Product Found') }}</h4>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Infinite Scroll Sentinel & Loader -->
<div id="infinite-scroll-sentinel" style="height:1px;"></div>
<div id="infinite-scroll-loader" class="text-center py-4" style="display:none;">
    <span class="spinner-border spinner-border-sm text-primary mr-2" role="status"></span>
    <span class="text-muted" style="font-size:14px;">{{ __('Loading more products...') }}</span>
</div>
<div id="infinite-scroll-end" class="text-center py-3" style="display:none;">
    <span class="text-muted" style="font-size:13px;"><i class="fas fa-check-circle text-success mr-1"></i>{{ __("You've seen all products") }}</span>
</div>
<script>
window._infiniteNextUrl = '{{ $items->nextPageUrl() }}';
window._infiniteInit && window._infiniteInit();
</script>

<script type="text/javascript" src="{{asset('assets/front/js/catalog.js')}}"></script>

<style>
.catalog-category-block {
    margin-bottom: 36px;
}

/* Modern Category Header Bar */
.catalog-cat-header-bar {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border: 1px solid #e2e8f0;
    border-left: 4px solid #2563eb;
    border-radius: 14px;
    padding: 12px 16px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
    transition: all 0.25s ease;
}
.catalog-cat-header-bar:hover {
    box-shadow: 0 6px 16px rgba(37, 99, 235, 0.08);
    border-color: #cbd5e1;
    border-left-color: #1d4ed8;
}

.catalog-cat-header-icon {
    width: 42px;
    height: 42px;
    border-radius: 12px;
    background: #eff6ff;
    color: #2563eb;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
    border: 1px solid #dbeafe;
    overflow: hidden;
    margin-right: 12px;
}

.catalog-cat-avatar-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.catalog-cat-title-text {
    font-size: 17px;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.25;
    letter-spacing: -0.2px;
    margin: 0;
}

.catalog-cat-count-badge {
    display: inline-block;
    font-size: 12px;
    font-weight: 500;
    color: #64748b;
    margin-top: 1px;
}

.catalog-cat-view-btn {
    display: inline-flex;
    align-items: center;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    color: #2563eb !important;
    font-size: 12.5px;
    font-weight: 600;
    padding: 6px 16px;
    border-radius: 20px;
    text-decoration: none !important;
    transition: all 0.2s ease;
    box-shadow: 0 1px 3px rgba(0,0,0,0.03);
}
.catalog-cat-view-btn:hover {
    background: #2563eb;
    border-color: #2563eb;
    color: #ffffff !important;
    transform: translateX(2px);
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.22);
}

/* Category Badge on Product Card */
.product-card .product-category {
    margin-bottom: 4px;
    display: block;
}
.product-card .product-category a {
    display: inline-block;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    color: #15803d !important;
    background: transparent !important;
    padding: 0 !important;
    border-radius: 0;
    text-decoration: none !important;
    transition: all 0.2s ease;
    border: none !important;
    box-shadow: none !important;
    max-width: 100%;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    line-height: 1.2;
}
.product-card .product-category a:hover {
    background: transparent !important;
    border: none !important;
    box-shadow: none !important;
    color: #0f5132 !important;
    text-decoration: underline !important;
}

/* Mobile responsive adjustments */
@media (max-width: 767.98px) {
    .catalog-category-block {
        margin-bottom: 24px;
    }
    .catalog-cat-header-bar {
        padding: 9px 12px;
        border-radius: 10px;
    }
    .catalog-cat-header-icon {
        width: 34px;
        height: 34px;
        font-size: 14px;
        border-radius: 8px;
        margin-right: 10px;
    }
    .catalog-cat-title-text {
        font-size: 14px;
    }
    .catalog-cat-count-badge {
        font-size: 10.5px;
    }
    .catalog-cat-view-btn {
        font-size: 11px;
        padding: 4px 10px;
        border-radius: 16px;
    }
    .product-card .product-category {
        margin-bottom: 2px;
    }
    .product-card .product-category a {
        font-size: 10px;
        padding: 0 !important;
        border-radius: 0;
        border: none !important;
        background: transparent !important;
    }
}
</style>
