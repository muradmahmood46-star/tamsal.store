@extends('master.front')
@section('title')
    {{__('Wishlist')}}
@endsection

@section('content')

<div class="page-title d-none d-lg-block">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <ul class="breadcrumbs">
                    <li><a href="{{route('front.index')}}">{{__('Home')}}</a> </li>
                    <li class="separator"></li>
                    <li>{{__('Wishlist')}}</li>
                  </ul>
            </div>
        </div>
    </div>
  </div>
  <!-- Page Content-->
  <div class="container padding-bottom-3x mb-1 mt-3 mt-lg-0">
  <div class="row">
         @include('includes.user_sitebar')
          <div class="col-12 col-lg-8">
            <!-- Mobile Header with Back Button -->
            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                <div class="d-flex align-items-center">
                    <a href="{{ route('user.dashboard') }}" class="btn btn-sm btn-light border mr-2 shadow-sm d-lg-none" style="border-radius: 8px; padding: 6px 12px; margin-right: 10px;" title="{{ __('Back to Dashboard') }}">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h5 class="mb-0 font-weight-bold text-dark d-flex align-items-center">
                        <i class="icon-heart mr-2 text-danger" style="margin-right: 8px;"></i>{{ __('My Wishlist') }}
                    </h5>
                </div>
                <span class="badge badge-primary font-weight-bold px-2 py-1" style="border-radius: 12px; font-size: 11px;">
                    {{ $wishlist_items->count() }} {{ __('Items') }}
                </span>
            </div>

            @if ($wishlist_items->count() > 0)
                <div class="card shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
                    <div class="card-body p-0">
                        <!-- Wishlist Table-->
                        <div class="u-table-res wishlist-table mb-0">
                          <table class="table table-bordered mb-0">
                            <thead>
                              <tr>
                                <th>{{__('Wishlist Product')}}</th>
                                <th class="text-center"><a class="btn btn-sm btn-primary" href="{{route('user.wishlist.delete.all')}}"><span>{{__('Clear Wishlist')}}</span></a></th>
                              </tr>
                            </thead>
                            <tbody>
                            @foreach ($wishlist_items as $product)
                            <tr>
                                <td>
                                  <div class="product-item"><a class="product-thumb" href="{{route('front.product',$product->slug)}}"><img src="{{url('/core/public/storage/images/'.$product->photo)}}" alt="Product"></a>
                                    <div class="product-info">
                                      <h4 class="product-title"><a href="{{route('front.product',$product->slug)}}">{{$product->name}}</a></h4>
                                      <div class="text-lg mb-1">{{PriceHelper::grandCurrencyPrice($product)}}</div>
                                      <div class="text-sm">{{__('Availability')}}:
                                        <div class="d-inline text-{{$product->stock == 0 ? 'danger' : 'success'}}">{{$product->stock == 0 ? __('Out of stock') : __('In Stock')}}</div>
                                      </div>
                                    </div>

                                  </div>
                                  @if ($product->is_stock())
                                  @if ($product->item_type != 'affiliate')
                                  <a class="product-button btn btn-primary btn-sm add_to_single_cart" href="javascript:;" data-target="{{$product->id}}"><i class="icon-shopping-cart"></i>
                                    <span> {{__('Add To Cart')}}</span>
                                  </a> 
                                  @endif
                               
                                  @else
                                  <a class="product-button btn btn-primary btn-sm" href="{{route('front.product',$product->slug)}}"><i class="icon-arrow-right"></i><span>{{__('Details')}}</span></a>
                                @endif
                                </td>
                                <td class="text-center"><a class="remove-from-cart" href="{{route('user.wishlist.delete',$product->getWishlistItemId())}}" data-toggle="tooltip" title="Remove item"><i class="icon-x"></i></a></td>
                              </tr>
                            @endforeach
                            </tbody>
                          </table>
                        </div>
                    </div>
                </div>
            @else
                <div class="card shadow-sm border-0" style="border-radius: 14px; background: #ffffff;">
                    <div class="card-body text-center py-5">
                        <div class="mb-3" style="width: 80px; height: 80px; border-radius: 50%; background: #fee2e2; display: inline-flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(239,68,68,0.1);">
                            <i class="icon-heart text-danger" style="font-size: 36px;"></i>
                        </div>
                        <h5 class="font-weight-bold text-dark mb-2">{{ __('Your Wishlist is Empty') }}</h5>
                        <p class="text-muted small mb-4" style="max-width: 320px; margin: 0 auto; line-height: 1.5;">
                            {{ __('Explore our trending products and save your favorite items here for easy access!') }}
                        </p>
                        <a href="{{ route('front.catalog') }}" class="btn btn-primary px-4 shadow-sm font-weight-bold" style="border-radius: 25px; padding: 10px 24px; font-size: 14px;">
                            <i class="icon-shopping-cart mr-2"></i>{{ __('Explore Products') }}
                        </a>
                    </div>
                </div>
            @endif

          </div>
        </div>
  </div>
@endsection
