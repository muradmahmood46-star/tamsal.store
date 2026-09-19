@extends('master.front')
@section('meta')
<meta name="keywords" content="{{$setting->meta_keywords}}">
<meta name="description" content="{{$setting->meta_description}}">
@endsection
@section('title')
    {{__('Contact')}}
@endsection

@section('content')
    <section class="store-hero-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="shop-banner-content">
                        <img class="shop-banner-logo" src="https://shofy.botble.com/storage/main/stores/8.png" alt="Old El Paso">
                        <div class="shop-banner-info">
                            <h2 class="shop-banner-name">Old El Paso</h2>
                            <div class="shop-banner-contact">
                                <div class="shop-banner-address d-flex gap-1">
                                    <svg class="icon svg-icon-ti-ti-map-pin" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0"></path>
                                        <path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z">
                                        </path>
                                    </svg> 562 Jakob Manors, East Selenaton, Michigan, TR
                                </div>
                                <div class="shop-banner-phone d-flex gap-1"><svg class="icon svg-icon-ti-ti-phone" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2">
                                        </path>
                                    </svg><a href="tel:+19594255150">+19594255150</a>
                                </div>
                                <div class="shop-banner-address d-flex gap-1"><svg class="icon svg-icon-ti-ti-mail" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z">
                                        </path>
                                        <path d="M3 7l9 6l9 -6"></path>
                                    </svg><a href="mailto:anderson.berta@example.net">anderson.berta@example.net</a>
                                </div>
                            </div>
                            <div class="shop-banner-description ck-content"> Odio nihil quam illo fuga veritatis deserunt
                                praesentium. Expedita omnis blanditiis tempora earum maxime saepe cumque. Quisquam sint ipsa
                                aliquid vel. Quia commodi corrupti ab necessitatibus adipisci nam. Sint assumenda modi eos
                                aliquam. Aut asperiores rem temporibus sunt consectetur iusto cumque. Libero commodi optio
                                eos laudantium velit quasi id perspiciatis. </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
  <!-- Page Content-->
  <div class="container  mt-30">
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
          <section class="widget widget-categories card rounded p-4 mb-3">
            <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
              <h3 class="widget-title mb-0" style="font-size: 16px; font-weight: 700;">{{ __('Filter by Price') }}</h3>
            </div>

            {{-- Manual Price Range (From - To) --}}
            <div class="manual-price-box mb-3 p-2 bg-light rounded border">
              <div class="row g-2">
                <div class="col-6">
                  <label class="form-label text-muted small mb-1" style="font-size: 11px; font-weight: 700; text-transform: uppercase;">{{ __('From Price') }}</label>
                  <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white px-2 text-muted" style="font-size: 11px;">{{ PriceHelper::setCurrencySign() }}</span>
                    <input type="number" class="form-control form-control-sm price-manual-input" id="manual_min_price" placeholder="0" min="0" value="{{ request()->input('minPrice') ? request()->input('minPrice') : '' }}" style="font-size: 12.5px;">
                  </div>
                </div>
                <div class="col-6">
                  <label class="form-label text-muted small mb-1" style="font-size: 11px; font-weight: 700; text-transform: uppercase;">{{ __('To Price') }}</label>
                  <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white px-2 text-muted" style="font-size: 11px;">{{ PriceHelper::setCurrencySign() }}</span>
                    <input type="number" class="form-control form-control-sm price-manual-input" id="manual_max_price" placeholder="{{ $setting->max_price }}" min="0" value="{{ request()->input('maxPrice') ? request()->input('maxPrice') : '' }}" style="font-size: 12.5px;">
                  </div>
                </div>
              </div>
            </div>

            <form class="price-range-slider" method="post" data-start-min="{{request()->input('minPrice') ? request()->input('minPrice') : '0'}}" data-start-max="{{request()->input('maxPrice') ? request()->input('maxPrice') : $setting->max_price}}" data-min="0" data-max="{{$setting->max_price}}" data-step="5">
              <div class="ui-range-slider"></div>
              <footer class="ui-range-slider-footer mt-3">
                <div class="column w-100 mb-2">
                  <div class="ui-range-values d-flex justify-content-between align-items-center text-muted small px-1">
                    <div>{{ __('Min') }}: <strong>{{PriceHelper::setCurrencySign()}}<span class="min_price">{{ request()->input('minPrice') ? request()->input('minPrice') : '0' }}</span></strong>
                      <input type="hidden">
                    </div>
                    <div>{{ __('Max') }}: <strong>{{PriceHelper::setCurrencySign()}}<span class="max_price">{{ request()->input('maxPrice') ? request()->input('maxPrice') : $setting->max_price }}</span></strong>
                      <input type="hidden">
                    </div>
                  </div>
                </div>
                <div class="column w-100">
                  <button class="btn btn-primary btn-block w-100 font-weight-bold py-2 shadow-sm apply-filters-btn" id="price_filter" type="button">
                    <i class="fas fa-check-circle mr-1"></i> <span>{{__('Apply Filters')}}</span>
                  </button>
                </div>
              </footer>
            </form>
          </section>
          @endif

          <div class="p-3 text-center d-block d-lg-none mt-2">
            <button class="btn btn-primary btn-block w-100 font-weight-bold py-2 shadow-sm apply-filters-btn" id="mobile_apply_filters" type="button">
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
    <input type="text" name="category" id="category" value="{{request()->input('category') ? (is_array(request()->input('category')) ? implode(',', request()->input('category')) : request()->input('category')) : (isset($category) && $category ? $category->slug : '')}}">
    <input type="text" name="quick_filter" id="quick_filter" value="">
    <input type="text" name="childcategory" id="childcategory" value="{{isset($childcategory) ? $childcategory->slug : ''}}">
    <input type="text" name="page" id="page" value="{{isset($page) ? $page : ''}}">
    <input type="text" name="attribute" id="attribute" value="{{isset($attribute) ? $attribute : ''}}">
    <input type="text" name="option" id="option" value="{{isset($option) ? $option : ''}}">
    <input type="text" name="subcategory" id="subcategory" value="{{request()->input('subcategory') ? (is_array(request()->input('subcategory')) ? implode(',', request()->input('subcategory')) : request()->input('subcategory')) : (isset($subcategory) && $subcategory ? $subcategory->slug : '')}}">
    <input type="text" name="sorting" id="sorting" value="{{isset($sorting) ? $sorting : ''}}">
    <input type="text" name="view_check" id="view_check" value="{{isset($view_check) ? $view_check : ''}}">


    <button type="submit" id="search_button" class="d-none"></button>
</form>
@endsection
