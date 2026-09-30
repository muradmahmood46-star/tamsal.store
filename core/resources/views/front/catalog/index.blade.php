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
            <!-- Store Hero Header Banner (Responsive: Spacious on PC, Compact on Mobile) -->
            <style>
                .store-hero-card {
                    border-radius: 14px;
                    background: {{ !empty($vendorStore->banner_url) ? 'url(' . $vendorStore->banner_url . ') center/cover no-repeat' : 'linear-gradient(135deg, #0d6efd 0%, #063970 100%)' }};
                    position: relative;
                }
                .store-hero-card-inner {
                    background: {{ !empty($vendorStore->banner_url) ? 'rgba(15, 23, 42, 0.82)' : 'transparent' }};
                    padding: 24px 28px;
                }
                .store-hero-avatar-img,
                .store-hero-avatar-fallback {
                    width: 76px;
                    height: 76px;
                    font-size: 32px;
                    border: 3px solid rgba(255,255,255,0.95) !important;
                }
                .store-hero-name {
                    font-size: 22px;
                    letter-spacing: -0.2px;
                    line-height: 1.2;
                }
                .store-hero-badge {
                    font-size: 12px;
                    padding: 3px 8px;
                    border-radius: 6px;
                }
                .store-hero-meta {
                    font-size: 13.5px;
                    margin-top: 6px;
                    display: flex;
                    align-items: center;
                    gap: 12px;
                }
                .store-hero-details {
                    display: block;
                    max-width: 650px;
                    line-height: 1.4;
                    opacity: 0.9;
                    font-size: 12.5px;
                    margin-top: 6px;
                }
                .store-hero-actions {
                    display: flex;
                    align-items: center;
                    justify-content: flex-end;
                    flex-wrap: wrap;
                    gap: 10px;
                }
                .store-share-pill {
                    display: inline-flex;
                    align-items: center;
                    background: #ffffff;
                    border-radius: 20px;
                    padding: 2px 3px 2px 12px;
                    border: 1px solid rgba(255,255,255,0.75);
                    box-sizing: border-box;
                    height: 38px;
                }
                .store-share-pill .fa-link {
                    color: #0d6efd;
                    font-size: 13px;
                    margin-right: 6px;
                    flex-shrink: 0;
                }
                .store-share-input {
                    border: none !important;
                    outline: none !important;
                    box-shadow: none !important;
                    background: transparent !important;
                    color: #1e293b !important;
                    font-size: 12.5px !important;
                    font-weight: 600 !important;
                    padding: 0 6px !important;
                    margin: 0 !important;
                    width: 250px !important;
                    height: 32px !important;
                    line-height: 32px !important;
                    cursor: text;
                    font-family: inherit;
                }
                .store-copy-btn {
                    border-radius: 16px !important;
                    font-size: 12px !important;
                    padding: 0 12px !important;
                    white-space: nowrap;
                    flex-shrink: 0;
                    display: inline-flex;
                    align-items: center;
                    gap: 4px;
                    height: 30px !important;
                    box-shadow: 0 1px 3px rgba(13,110,253,0.25);
                    border: none;
                }
                .store-all-btn {
                    border-radius: 20px !important;
                    font-size: 13px !important;
                    height: 38px !important;
                    padding: 0 16px !important;
                    color: #0d6efd !important;
                    white-space: nowrap;
                    border: 1px solid rgba(255,255,255,0.5) !important;
                    display: inline-flex;
                    align-items: center;
                }

                /* Mobile View Specifics */
                @media (max-width: 767.98px) {
                    .store-hero-card-inner {
                        padding: 16px 14px !important;
                    }
                    .store-hero-avatar-img,
                    .store-hero-avatar-fallback {
                        width: 52px !important;
                        height: 52px !important;
                        font-size: 22px !important;
                        border: 2px solid rgba(255,255,255,0.95) !important;
                    }
                    .store-hero-name {
                        font-size: 18px !important;
                    }
                    .store-hero-badge {
                        font-size: 10.5px !important;
                        padding: 2px 6px !important;
                        border-radius: 4px !important;
                    }
                    .store-hero-meta {
                        font-size: 12px !important;
                        margin-top: 4px !important;
                        gap: 8px !important;
                        white-space: nowrap !important;
                        overflow: hidden !important;
                        min-width: 0 !important;
                    }
                    .store-hero-details {
                        display: none !important;
                    }
                    .store-hero-actions {
                        width: auto !important;
                        margin-left: auto !important;
                        justify-content: flex-end !important;
                        gap: 6px !important;
                        margin-top: 6px !important;
                    }
                    .store-share-pill {
                        height: 25px !important;
                        padding: 1px 2px 1px 8px !important;
                        border-radius: 16px !important;
                    }
                    .store-share-pill .fa-link {
                        font-size: 10px !important;
                        margin-right: 4px !important;
                    }
                    .store-share-input {
                        width: 170px !important;
                        font-size: 11px !important;
                        height: 21px !important;
                        line-height: 21px !important;
                        padding: 0 4px !important;
                    }
                    .store-copy-btn {
                        height: 21px !important;
                        padding: 0 8px !important;
                        font-size: 9.5px !important;
                        border-radius: 12px !important;
                        gap: 3px !important;
                    }
                    .store-copy-btn i {
                        font-size: 9px !important;
                    }
                    .store-all-btn {
                        height: 25px !important;
                        padding: 0 10px !important;
                        font-size: 10px !important;
                        border-radius: 14px !important;
                    }
                    .store-all-btn i {
                        font-size: 9.5px !important;
                    }
                }
            </style>
            <div class="store-hero-banner mb-3">
                <div class="card border-0 shadow-sm overflow-hidden store-hero-card">
                    <div class="store-hero-card-inner">
                        <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between" style="gap: 12px;">
                            
                            {{-- Left: Store Logo & Details --}}
                            <div class="d-flex align-items-center flex-grow-1 min-w-0" style="gap: 14px; width: 100%; max-width: 100%;">
                                {{-- Store Logo / Avatar --}}
                                <div class="store-hero-avatar flex-shrink-0">
                                    @if(!empty($vendorStore->logo_url))
                                        <img src="{{ $vendorStore->logo_url }}" alt="{{ $vendorStore->name }}" class="rounded-circle border bg-white shadow-sm store-hero-avatar-img" style="object-fit: cover;">
                                    @else
                                        <div class="rounded-circle d-flex align-items-center justify-content-center text-primary bg-white shadow-sm store-hero-avatar-fallback">
                                            <i class="fas fa-store"></i>
                                        </div>
                                    @endif
                                </div>

                                {{-- Store Text Info --}}
                                <div class="store-hero-info text-white flex-grow-1 min-w-0" style="overflow: hidden;">
                                    <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                                        <h3 class="store-hero-name mb-0 text-white font-weight-bold">
                                            {{ $vendorStore->name }}
                                        </h3>
                                        <span class="badge {{ $vendorStore->is_admin ? 'badge-warning text-dark' : 'badge-success text-white' }} font-weight-bold store-hero-badge">
                                            <i class="fas fa-check-circle mr-1"></i> {{ $vendorStore->type }}
                                        </span>
                                    </div>

                                    <div class="store-hero-meta text-white-50">
                                        <span class="flex-shrink-0"><i class="fas fa-boxes text-warning mr-1"></i> <strong class="text-white">{{ $vendorStore->products_count }}</strong> {{ __('Products') }}</span>
                                        @if(!empty($vendorStore->address))
                                            <span class="text-white-50 text-truncate" style="opacity: 0.95; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; min-width: 0;" title="{{ $vendorStore->address }}"><i class="fas fa-map-marker-alt text-danger mr-1"></i> {{ $vendorStore->address }}</span>
                                        @endif
                                    </div>

                                    @if(!empty($vendorStore->details))
                                        <p class="text-white-50 small mb-0 store-hero-details text-truncate">
                                            {{ Str::limit($vendorStore->details, 110) }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            {{-- Right: Share Link & View All --}}
                            <div class="store-hero-actions">
                                <div class="store-share-pill shadow-sm">
                                    <i class="fas fa-link"></i>
                                    <input type="text" id="frontStoreShareLink" class="store-share-input" value="{{ $vendorStore->store_url ?? url('/c/' . ($vendorStore->store_code ?? ($vendorStore->vendor_id ?: 'admin'))) }}" readonly>
                                    <button type="button" class="btn btn-primary btn-sm font-weight-bold store-copy-btn" id="copyFrontStoreBtn" onclick="copyFrontStoreLink()">
                                        <i class="fas fa-copy" id="frontCopyIcon"></i> <span id="frontCopyText">{{ __('Copy') }}</span>
                                    </button>
                                </div>
                                <a href="{{ route('front.catalog') }}" class="btn btn-light btn-sm font-weight-bold shadow-sm store-all-btn">
                                    <i class="fas fa-th-large mr-1"></i> {{ __('All Products') }}
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
              <section class="widget widget-categories card rounded p-4 mb-3">
                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                  <h3 class="widget-title mb-0" style="font-size: 16px; font-weight: 700;">{{__('Shop Categories')}}</h3>
                </div>
                <ul id="category_list" class="category-scroll" style="max-height: 280px; overflow-y: auto;">
                    @foreach ($categories as $getcategory)
                    @php
                        $isCatActive = in_array($getcategory->slug, $selected_categories ?? []) || (isset($category) && $category && $category->id == $getcategory->id);
                    @endphp
                    <li class="has-children {{ $isCatActive ? 'expanded active' : '' }} mb-1">
                      <div class="d-flex align-items-center justify-content-between py-1">
                        <div class="custom-control custom-checkbox d-flex align-items-center flex-grow-1 mr-2" style="cursor: pointer;">
                          <input type="checkbox" class="custom-control-input category-checkbox" id="cat_chk_{{$getcategory->id}}" value="{{$getcategory->slug}}" {{ $isCatActive ? 'checked' : '' }}>
                          <label class="custom-control-label font-weight-500 text-dark mb-0" for="cat_chk_{{$getcategory->id}}" style="cursor: pointer; font-size: 13.5px;">
                            {{$getcategory->name}}
                          </label>
                        </div>
                        @if ($getcategory->subcategory->count() > 0)
                          <span class="subcat-toggle-btn text-muted px-2 py-1 cursor-pointer" style="font-size: 11px;" title="{{ __('Toggle subcategories') }}">
                            <i class="fas {{ $isCatActive ? 'fa-chevron-up' : 'fa-chevron-down' }}"></i>
                          </span>
                        @endif
                      </div>

                      @if ($getcategory->subcategory->count() > 0)
                        <ul id="subcategory_list" class="pl-4 mt-1 border-left ml-2" style="list-style: none; display: {{ $isCatActive ? 'block' : 'none' }};">
                            @foreach ($getcategory->subcategory as $getsubcategory)
                            @php
                                $isSubActive = in_array($getsubcategory->slug, $selected_subcategories ?? []) || (isset($subcategory) && $subcategory && $subcategory->id == $getsubcategory->id);
                            @endphp
                            <li class="{{ $isSubActive ? 'active' : '' }} py-1">
                              <div class="custom-control custom-checkbox d-flex align-items-center">
                                <input type="checkbox" class="custom-control-input subcategory-checkbox" id="subcat_chk_{{$getsubcategory->id}}" value="{{$getsubcategory->slug}}" {{ $isSubActive ? 'checked' : '' }}>
                                <label class="custom-control-label text-muted mb-0" for="subcat_chk_{{$getsubcategory->id}}" style="cursor: pointer; font-size: 12.5px;">
                                  {{$getsubcategory->name}}
                                </label>
                              </div>
                            </li>
                            @endforeach
                        </ul>
                      @endif
                    </li>
                    @endforeach
                </ul>
              </section>

              @if ($setting->is_range_search == 1)
                <!-- Widget Price Range-->
                <section class="widget widget-categories card rounded p-3 p-md-4 mb-3" style="box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: 1px solid #e2e8f0;">
                  <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                    <h3 class="widget-title mb-0" style="font-size: 15px; font-weight: 700; color: #1e293b;"><i class="fas fa-sliders-h mr-1 text-primary"></i> {{ __('Filter by Price') }}</h3>
                  </div>

                  {{-- Manual Price Range (From - To) --}}
                  <div class="manual-price-box mb-3 p-2 bg-light rounded border" style="border-color: #e2e8f0 !important;">
                    <div class="row g-2">
                      <div class="col-6">
                        <label class="form-label text-muted mb-1" style="font-size: 10.5px; font-weight: 700; text-transform: uppercase;">{{ __('From Price') }}</label>
                        <div class="input-group input-group-sm">
                          <span class="input-group-text bg-white px-2 text-muted" style="font-size: 11px;">{{ PriceHelper::setCurrencySign() }}</span>
                          <input type="number" class="form-control form-control-sm price-manual-input" id="manual_min_price" placeholder="0" min="0" value="{{ request()->input('minPrice') ? request()->input('minPrice') : '' }}" style="font-size: 12px; height: 32px;">
                        </div>
                      </div>
                      <div class="col-6">
                        <label class="form-label text-muted mb-1" style="font-size: 10.5px; font-weight: 700; text-transform: uppercase;">{{ __('To Price') }}</label>
                        <div class="input-group input-group-sm">
                          <span class="input-group-text bg-white px-2 text-muted" style="font-size: 11px;">{{ PriceHelper::setCurrencySign() }}</span>
                          <input type="number" class="form-control form-control-sm price-manual-input" id="manual_max_price" placeholder="{{ $setting->max_price }}" min="0" value="{{ request()->input('maxPrice') ? request()->input('maxPrice') : '' }}" style="font-size: 12px; height: 32px;">
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="price-range-slider" data-start-min="{{request()->input('minPrice') ? request()->input('minPrice') : '0'}}" data-start-max="{{request()->input('maxPrice') ? request()->input('maxPrice') : $setting->max_price}}" data-min="0" data-max="{{$setting->max_price}}" data-step="5">
                    <div class="ui-range-slider"></div>
                    <div class="ui-range-values d-flex justify-content-between align-items-center text-muted small mt-2 px-1" style="font-size: 11.5px;">
                      <div class="ui-range-value-min">{{ __('Min') }}: <strong>{{PriceHelper::setCurrencySign()}}<span class="min_price">{{ request()->input('minPrice') ? request()->input('minPrice') : '0' }}</span></strong>
                        <input type="hidden">
                      </div>
                      <div class="ui-range-value-max">{{ __('Max') }}: <strong>{{PriceHelper::setCurrencySign()}}<span class="max_price">{{ request()->input('maxPrice') ? request()->input('maxPrice') : $setting->max_price }}</span></strong>
                        <input type="hidden">
                      </div>
                    </div>
                  </div>

                  {{-- Single Clean Apply Filters Button --}}
                  <div class="mt-3 pt-2">
                    <button class="btn btn-primary btn-block w-100 font-weight-bold py-2 shadow-sm apply-filters-btn" id="price_filter" type="button" style="border-radius: 6px; font-size: 13.5px; min-height: 40px; display: flex; align-items: center; justify-content: center; width: 100% !important; box-sizing: border-box !important;">
                      <i class="fas fa-check-circle mr-1"></i> <span>{{__('Apply Filters')}}</span>
                    </button>
                  </div>
                </section>
              @else
                <div class="p-2 text-center mb-3">
                  <button class="btn btn-primary btn-block w-100 font-weight-bold py-2 shadow-sm apply-filters-btn" id="price_filter" type="button" style="border-radius: 6px; font-size: 13.5px; min-height: 40px; display: flex; align-items: center; justify-content: center; width: 100% !important; box-sizing: border-box !important;">
                    <i class="fas fa-check-circle mr-1"></i> <span>{{ __('Apply Filters') }}</span>
                  </button>
                </div>
              @endif

            </aside>
          </div>
        </div>
      </div>



      <form id="search_form" class="d-none" action="{{route('front.catalog')}}" method="GET">

        <input type="text" name="maxPrice" id="maxPrice" value="{{request()->input('maxPrice') ? request()->input('maxPrice') : ''}}">
        <input type="text" name="minPrice" id="minPrice" value="{{request()->input('minPrice') ? request()->input('minPrice') : ''}}">
        <input type="text" name="brand" id="brand" value="{{isset($brand) ? $brand->slug : ''}}">
        <input type="text" name="vendor" id="vendor" value="{{request()->input('vendor') ? request()->input('vendor') : ''}}">
        <input type="text" name="category" id="category" value="{{request()->input('category') ? (is_array(request()->input('category')) ? implode(',', request()->input('category')) : request()->input('category')) : (isset($category) && $category ? $category->slug : '')}}">
        <input type="text" name="quick_filter" id="quick_filter" value="">
        <input type="text" name="childcategory" id="childcategory" value="{{isset($childcategory) ? $childcategory->slug : ''}}">
        <input type="text" name="page" id="page" value="{{isset($page) ? $page : ''}}">
        <input type="text" name="attribute" id="attribute" value="{{isset($attribute) ? $attribute : ''}}">
        <input type="text" name="option" id="option" value="{{isset($option) ? $option : ''}}">
        <input type="text" name="subcategory" id="subcategory" value="{{request()->input('subcategory') ? (is_array(request()->input('subcategory')) ? implode(',', request()->input('subcategory')) : request()->input('subcategory')) : (isset($subcategory) && $subcategory ? $subcategory->slug : '')}}">
        <input type="text" name="sorting" id="sorting" value="{{isset($sorting) ? $sorting : ''}}">
        <input type="text" name="view_check" id="view_check" value="{{isset($view_check) ? $view_check : ''}}">
        <input type="text" name="tag" id="tag" value="{{request()->input('tag') ? request()->input('tag') : ''}}">


        <button type="submit" id="search_button" class="d-none"></button>
    </form>

<script>
(function(){
    var _loading = false;
    var _observer = null;

    function _loadMore(){
        var nextUrl = window._infiniteNextUrl;
        if(_loading || !nextUrl) return;
        _loading = true;
        var loader = document.getElementById('infinite-scroll-loader');
        var endMsg = document.getElementById('infinite-scroll-end');
        if(loader) loader.style.display = 'block';

        fetch(nextUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(function(r){ return r.text(); })
        .then(function(html){
            var parser = new DOMParser();
            var doc = parser.parseFromString(html, 'text/html');

            var newMain = doc.getElementById('main_div');
            var existingMain = document.getElementById('main_div');
            if(newMain && existingMain){
                Array.from(newMain.children).forEach(function(child){
                    var catId = child.dataset && child.dataset.categoryId;
                    if(catId){
                        var existing = existingMain.querySelector('.catalog-category-block[data-category-id="' + catId + '"]');
                        if(existing){
                            // Merge products into the existing category block
                            var existingRow = existing.querySelector('.row.g-3.gx-2');
                            var newRow = child.querySelector('.row.g-3.gx-2');
                            if(existingRow && newRow){
                                Array.from(newRow.children).forEach(function(product){
                                    existingRow.appendChild(product.cloneNode(true));
                                });
                                // Update product count badge
                                var badge = existing.querySelector('.catalog-cat-count-badge');
                                if(badge){
                                    var total = existingRow.children.length;
                                    badge.textContent = badge.textContent.replace(/\d+/, total);
                                }
                                return;
                            }
                        }
                    }
                    existingMain.appendChild(child.cloneNode(true));
                });
            }

            // Get next URL from injected script tag
            var m = html.match(/window\._infiniteNextUrl = '([^']*)'/);
            window._infiniteNextUrl = (m && m[1]) ? m[1] : '';

            _loading = false;
            if(loader) loader.style.display = 'none';
            if(!window._infiniteNextUrl){
                if(endMsg) endMsg.style.display = 'block';
                if(_observer){
                    var s = document.getElementById('infinite-scroll-sentinel');
                    if(s) _observer.unobserve(s);
                }
            }
            // Re-init lazy images if available
            if(typeof $ !== 'undefined') $('img.lazy').each(function(){ if($(this).data('src')) $(this).attr('src', $(this).data('src')); });
        })
        .catch(function(){
            _loading = false;
            if(loader) loader.style.display = 'none';
        });
    }

    window._infiniteInit = function(){
        // Reset state on filter reload
        _loading = false;
        if(_observer) _observer.disconnect();
        var endMsg = document.getElementById('infinite-scroll-end');
        if(endMsg) endMsg.style.display = 'none';

        var sentinel = document.getElementById('infinite-scroll-sentinel');
        if(!sentinel) return;

        if(!window._infiniteNextUrl){
            if(endMsg) endMsg.style.display = 'block';
            return;
        }

        if('IntersectionObserver' in window){
            _observer = new IntersectionObserver(function(entries){
                if(entries[0].isIntersecting) _loadMore();
            }, { rootMargin: '400px' });
            _observer.observe(sentinel);
        } else {
            window.addEventListener('scroll', function(){
                var rect = sentinel.getBoundingClientRect();
                if(rect.top <= window.innerHeight + 400) _loadMore();
            });
        }
    };

    // Init on first page load
    window._infiniteInit();
})();

function copyFrontStoreLink() {
    var input = document.getElementById('frontStoreShareLink');
    if (!input) return;
    input.select();
    input.setSelectionRange(0, 99999);
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(input.value).then(handleCopied).catch(fallbackCopy);
    } else {
        fallbackCopy();
    }
    function fallbackCopy() {
        try {
            document.execCommand('copy');
            handleCopied();
        } catch (err) {
            alert('Failed to copy link.');
        }
    }
    function handleCopied() {
        var btn = document.getElementById('copyFrontStoreBtn');
        var icon = document.getElementById('frontCopyIcon');
        var text = document.getElementById('frontCopyText');
        if (btn && icon && text) {
            var originalText = text.innerText;
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-success');
            if (icon) icon.className = 'fas fa-check';
            text.innerText = 'Copied!';
            setTimeout(function() {
                btn.classList.remove('btn-success');
                btn.classList.add('btn-primary');
                if (icon) icon.className = 'fas fa-copy';
                text.innerText = originalText;
            }, 2500);
        }
    }
}
</script>
@endsection

