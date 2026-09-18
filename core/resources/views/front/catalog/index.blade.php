@extends('master.front')
@section('meta')
<meta name="keywords" content="{{$setting->meta_keywords}}">
<meta name="description" content="{{$setting->meta_description}}">
@endsection
@section('title')
    @if(isset($vendorStore) && $vendorStore)
        {{ $vendorStore->name }} - {{ __('Store Products') }}
    @else
        {{__('Products')}}
    @endif
@endsection

@section('content')
    <!-- Page Title-->
<div class="page-title">
    <div class="container">
      <div class="row">
          <div class="col-lg-12">
            <ul class="breadcrumbs">
                <li><a href="{{route('front.index')}}">{{__('Home')}}</a> </li>
                <li class="separator"></li>
                @if(isset($vendorStore) && $vendorStore)
                    <li><a href="{{route('front.catalog')}}">{{__('Stores')}}</a></li>
                    <li class="separator"></li>
                    <li>{{ $vendorStore->name }}</li>
                @elseif(request()->filled('tag'))
                    <li><a href="{{route('front.catalog')}}">{{__('Shop')}}</a></li>
                    <li class="separator"></li>
                    <li>#{{ request('tag') }}</li>
                @else
                    <li>{{__('Shop')}}</li>
                @endif
              </ul>
          </div>
      </div>
    </div>
  </div>
  <!-- Page Content-->
  <div class="container padding-bottom-3x mb-1">
        @if(isset($vendorStore) && $vendorStore)
            <!-- Store Hero Header Banner -->
            <div class="store-hero-banner mb-4">
                <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 14px; background: {{ !empty($vendorStore->banner_url) ? 'url(' . $vendorStore->banner_url . ') center/cover no-repeat' : 'linear-gradient(135deg, #0d6efd 0%, #063970 100%)' }}; position: relative;">
                    <div style="background: {{ !empty($vendorStore->banner_url) ? 'rgba(15, 23, 42, 0.78)' : 'transparent' }}; padding: 24px 24px;">
                        <div class="d-flex flex-wrap align-items-center justify-content-between">
                            <div class="d-flex align-items-center flex-wrap mb-3 mb-md-0">
                                {{-- Store Logo / Avatar --}}
                                <div class="store-logo-box mr-3 mr-md-4 mb-2 mb-sm-0" style="margin-right: 20px;">
                                    @if(!empty($vendorStore->logo_url))
                                        <img src="{{ $vendorStore->logo_url }}" alt="{{ $vendorStore->name }}" class="rounded-circle border bg-white p-1 shadow" style="width: 80px; height: 80px; object-fit: cover; border: 3px solid rgba(255,255,255,0.9) !important;">
                                    @else
                                        <div class="rounded-circle d-flex align-items-center justify-content-center text-primary bg-white shadow" style="width: 80px; height: 80px; font-size: 32px; border: 3px solid rgba(255,255,255,0.9);">
                                            <i class="fas fa-store"></i>
                                        </div>
                                    @endif
                                </div>

                                {{-- Store Details --}}
                                <div class="text-white">
                                    <div class="d-flex align-items-center flex-wrap mb-1" style="gap: 8px;">
                                        <h3 class="mb-0 text-white font-weight-bold" style="font-size: 22px; letter-spacing: -0.2px;">
                                            {{ $vendorStore->name }}
                                        </h3>
                                        <span class="badge {{ $vendorStore->is_admin ? 'badge-warning text-dark' : 'badge-success text-white' }} py-1 px-2 font-weight-bold" style="font-size: 11.5px; border-radius: 6px;">
                                            <i class="fas fa-check-circle mr-1"></i> {{ $vendorStore->type }}
                                        </span>
                                    </div>

                                    <div class="d-flex flex-wrap align-items-center text-white-50 small mt-1" style="gap: 15px; font-size: 12.5px;">
                                        <span><i class="fas fa-boxes text-warning mr-1"></i> <strong class="text-white">{{ $vendorStore->products_count }}</strong> {{ __('Products in this Store') }}</span>
                                        @if(!empty($vendorStore->address))
                                            <span><i class="fas fa-map-marker-alt text-danger mr-1"></i> {{ $vendorStore->address }}</span>
                                        @endif
                                    </div>

                                    @if(!empty($vendorStore->details))
                                        <p class="text-white-50 small mb-0 mt-2" style="max-width: 680px; line-height: 1.4; opacity: 0.95;">
                                            {{ Str::limit($vendorStore->details, 150) }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div>
                                <a href="{{ route('front.catalog') }}" class="btn btn-light btn-sm font-weight-bold shadow-sm" style="border-radius: 8px; font-size: 12.5px; padding: 8px 16px; color: #0d6efd;">
                                    <i class="fas fa-th-large mr-1"></i> {{ __('View All Products') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <div class="row">
            <div class="col-lg-12">
                <div class="shop-top-filter-wrapper">
                    <div class="row">
                        <div class="col-md-10 gd-text-sm-center">
                            <div class="sptfl">
                                <div class="quickFilter">
                                    <h4 class="quickFilter-title"><i class="fas fa-filter"></i>{{__('Quick filter')}}</h4>
                                    <ul id="quick_filter">
                                        <li><a datahref=""><i class="icon-chevron-right pr-2"></i>{{__('All products')}} </a></li>
                                        <li class=""><a href="javascript:;" data-href="feature"><i class="icon-chevron-right pr-2"></i>{{__('Featured products')}} </a></li>
                                        <li class=""><a href="javascript:;" data-href="best"><i class="icon-chevron-right pr-2"></i>{{__('Best sellers')}} </a></li>
                                        <li class=""><a href="javascript:;" data-href="top"><i class="icon-chevron-right pr-2"></i>{{__('Top rated')}} </a></li>
                                        <li class=""><a href="javascript:;" data-href="new"><i class="icon-chevron-right pr-2"></i>{{__('New Arrival')}} </a></li>
                                    </ul>
                                </div>
                                <div class="shop-sorting">
                                    <label for="sorting">{{__('Sort by')}}:</label>
                                    <select class="form-control" id="sorting">
                                    <option value="">{{__('All Products')}}</option>
                                    <option value="low_to_high" {{request()->input('low_to_high') ? 'selected' : ''}}>{{__('Low - High Price')}}</option>
                                    <option value="high_to_low" {{request()->input('high_to_low') ? 'selected' : ''}}>{{__('High - Low Price')}}</option>
                                    </select><span class="text-muted">{{__('Showing')}}:</span><span>1 - {{$setting->view_product}} {{__('items')}}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 gd-text-sm-center">
                            <div class="shop-view"><a class="list-view {{Session::has('view_catalog') && Session::get('view_catalog') == 'grid' ? 'active' : ''}} " data-step="grid" href="javascript:;" data-href="{{route('front.catalog').'?view_check=grid'}}"><i class="fas fa-th-large"></i></a>
                                <a class="list-view {{Session::has('view_catalog') && Session::get('view_catalog') == 'list' ? 'active' : ''}}" href="javascript:;" data-step="list" data-href="{{route('front.catalog').'?view_check=list'}}"><i class="fas fa-list"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row g-3">

          <div class="col-lg-9 order-lg-2" id="list_view_ajax">
            @if(request()->filled('tag'))
                <div class="alert alert-info d-flex align-items-center justify-content-between p-2 px-3 mb-3 shadow-sm" style="border-radius: 8px; background: #f0f9ff; border: 1px solid #bae6fd; color: #0369a1;">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-tags mr-2" style="font-size: 16px;"></i>
                        <span>{{ __('Showing products for Tag') }}: <strong class="text-primary font-weight-bold ml-1">#{{ request('tag') }}</strong></span>
                    </div>
                    <a href="{{ route('front.catalog') }}" class="btn btn-sm btn-outline-danger py-1 px-2" style="font-size: 12px; border-radius: 6px; text-decoration: none;">
                        <i class="fas fa-times mr-1"></i> {{ __('Clear Tag') }}
                    </a>
                </div>
            @endif
            @include('front.catalog.catalog')
          </div>

          <!-- Sidebar          -->
          <div class="col-lg-3 order-lg-1">
            <div class="sidebar-toggle position-left"><i class="icon-filter"></i></div>
            <aside class="sidebar sidebar-offcanvas position-left"><span class="sidebar-close"><i class="icon-x"></i></span>
              <!-- Widget Categories-->
              <section class="widget widget-categories card rounded p-4">
                <h3 class="widget-title">{{__('Shop Categories')}}</h3>
                <ul id="category_list" class="category-scroll">
                    @foreach ($categories as $getcategory)
                    <li class="has-children  {{isset($category) && $category->id == $getcategory->id ? 'expanded active' : ''}} ">
                      <a class="category_search" href="javascript:;"  data-href="{{$getcategory->slug}}">{{$getcategory->name}}</a>

                        <ul id="subcategory_list">
                            @foreach ($getcategory->subcategory as $getsubcategory)
                            <li class="{{isset($subcategory) && $subcategory->id == $getsubcategory->id ? 'active' : ''}}">
                              <a class="subcategory" href="javascript:;" data-href="{{$getsubcategory->slug}}">{{$getsubcategory->name}}</a>

                              <ul id="childcategory_list">
                                @foreach ($getsubcategory->childcategory as $getchildcategory)
                                <li class="{{isset($childcategory) && $getchildcategory->id == $getchildcategory->id ? 'active' : ''}}">
                                  <a class="childcategory" href="javascript:;" data-href="{{$getchildcategory->slug}}">{{$getchildcategory->name}}</a>

                                </li>
                                @endforeach
                            </ul>
                            </li>
                            @endforeach
                        </ul>
                      </li>
                    @endforeach
                </ul>
              </section>

              @if ($setting->is_range_search == 1)
                   <!-- Widget Price Range-->
              <section class="widget widget-categories card rounded p-4">
                <h3 class="widget-title">{{ __('Filter by Price') }}</h3>
                <form class="price-range-slider" method="post" data-start-min="{{request()->input('minPrice') ? request()->input('minPrice') : '0'}}" data-start-max="{{request()->input('maxPrice') ? request()->input('maxPrice') : $setting->max_price}}" data-min="0" data-max="{{$setting->max_price}}" data-step="5">
                  <div class="ui-range-slider"></div>
                  <footer class="ui-range-slider-footer">
                    <div class="column">
                      <button class="btn btn-primary btn-sm font-weight-bold px-3 shadow-sm" id="price_filter" type="button"><i class="fas fa-check-circle mr-1"></i> <span>{{__('Apply Filters')}}</span></button>
                    </div>
                    <div class="column">
                      <div class="ui-range-values">
                        <div class="ui-range-value-min">{{PriceHelper::setCurrencySign()}}<span class="min_price"></span>
                          <input type="hidden">
                        </div>-
                        <div class="ui-range-value-max">{{PriceHelper::setCurrencySign()}}<span class="max_price"></span>
                          <input type="hidden">
                        </div>
                      </div>
                    </div>
                  </footer>
                </form>
              </section>
              @endif

              <div class="p-3 text-center d-block d-lg-none">
                <button class="btn btn-primary btn-block font-weight-bold py-2 shadow-sm" id="mobile_apply_filters" type="button">
                  <i class="fas fa-check-circle mr-1"></i> {{ __('Apply Filters') }}
                </button>
              </div>

            </aside>
          </div>
        </div>
      </div>



      <form id="search_form" class="d-none" action="{{route('front.catalog')}}" method="GET">

        <input type="text" name="maxPrice" id="maxPrice" value="{{request()->input('maxPrice') ? request()->input('maxPrice') : ''}}">
        <input type="text" name="minPrice" id="minPrice" value="{{request()->input('minPrice') ? request()->input('minPrice') : ''}}">
        <input type="text" name="brand" id="brand" value="{{isset($brand) ? $brand->slug : ''}}">
        <input type="text" name="vendor" id="vendor" value="{{request()->input('vendor') ? request()->input('vendor') : ''}}">
        <input type="text" name="category" id="category" value="{{isset($category) ? $category->slug : ''}}">
        <input type="text" name="quick_filter" id="quick_filter" value="">
        <input type="text" name="childcategory" id="childcategory" value="{{isset($childcategory) ? $childcategory->slug : ''}}">
        <input type="text" name="page" id="page" value="{{isset($page) ? $page : ''}}">
        <input type="text" name="attribute" id="attribute" value="{{isset($attribute) ? $attribute : ''}}">
        <input type="text" name="option" id="option" value="{{isset($option) ? $option : ''}}">
        <input type="text" name="subcategory" id="subcategory" value="{{isset($subcategory) ? $subcategory->slug : ''}}">
        <input type="text" name="sorting" id="sorting" value="{{isset($sorting) ? $sorting : ''}}">
        <input type="text" name="view_check" id="view_check" value="{{isset($view_check) ? $view_check : ''}}">
        <input type="text" name="tag" id="tag" value="{{request()->input('tag') ? request()->input('tag') : ''}}">


        <button type="submit" id="search_button" class="d-none"></button>
    </form>
@endsection

