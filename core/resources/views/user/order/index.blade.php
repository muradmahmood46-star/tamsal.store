@extends('master.front')
@section('title')
    {{__('Orders')}}
@endsection

@section('content')
    <!-- Page Title-->
<div class="page-title d-none d-lg-block">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <ul class="breadcrumbs">
                    <li><a href="{{route('front.index')}}">{{__('Home')}}</a> </li>
                    <li class="separator"></li>
                    <li>{{__('Orders')}}</li>
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
            <!-- Mobile Header / Top Bar with Back Button -->
            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                <div class="d-flex align-items-center">
                    <a href="{{ route('user.dashboard') }}" class="btn btn-sm btn-light border mr-2 shadow-sm d-lg-none" style="border-radius: 8px; padding: 6px 12px; margin-right: 10px;" title="{{ __('Back to Dashboard') }}">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h5 class="mb-0 font-weight-bold text-dark d-flex align-items-center">
                        <i class="icon-shopping-bag mr-2 text-primary" style="margin-right: 8px;"></i>{{ __('My Orders') }}
                    </h5>
                </div>
                <span class="badge badge-primary font-weight-bold px-2 py-1" style="border-radius: 12px; font-size: 11px;">
                    {{ $orders->count() }} {{ __('Orders') }}
                </span>
            </div>

            @if($orders->count() > 0)
                <!-- Mobile Card View for Orders -->
                <div class="d-md-none">
                    @foreach ($orders as $order)
                    <div class="card mb-3 shadow-sm border" style="border-radius: 12px; overflow: hidden; background: #fff;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <a class="navi-link font-weight-bold text-dark" href="{{route('user.order.invoice',$order->id)}}" style="font-size: 15px;">
                                        #{{$order->transaction_number}}
                                    </a>
                                    <div class="mt-1">
                                        <span class="badge badge-light border text-dark" style="font-size: 11px;">
                                            <i class="fas fa-store text-primary mr-1"></i> {{ $order->store_name }}
                                        </span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="font-weight-bold text-dark" style="font-size: 15px;">
                                        @if ($setting->currency_direction == 1)
                                        {{$order->currency_sign}}{{PriceHelper::OrderTotal($order)}}
                                        @else
                                        {{PriceHelper::OrderTotal($order)}}{{$order->currency_sign}}
                                        @endif
                                    </span>
                                    <small class="text-muted d-block" style="font-size: 11px;">{{$order->created_at->format('d M, Y')}}</small>
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-center flex-wrap gap-1 mb-3 pt-2 border-top" style="gap: 6px;">
                                @if($order->order_status == 'Pending')
                                    <span class="badge badge-warning text-dark">{{__('Pending')}}</span>
                                @elseif($order->order_status == 'Accepted')
                                    <span class="badge badge-primary">{{__('Accepted')}}</span>
                                @elseif($order->order_status == 'Send to Delivery House')
                                    <span class="badge" style="background-color: #6f42c1; color: #fff;">{{__('Send to Delivery House')}}</span>
                                @elseif($order->order_status == 'In Progress')
                                    <span class="badge badge-info">{{__('In Progress')}}</span>
                                @elseif($order->order_status == 'Delivered')
                                    <span class="badge badge-success">{{__('Delivered')}}</span>
                                @else
                                    <span class="badge badge-danger">{{__('Canceled')}}</span>
                                @endif

                                @if($order->payment_status == 'Paid')
                                    <span class="badge badge-success">{{$order->payment_status}}</span>
                                @elseif($order->payment_status == 'Pending')
                                    <span class="badge badge-warning text-dark">{{$order->payment_status}}</span>
                                @else
                                    <span class="badge badge-danger">{{$order->payment_status}}</span>
                                @endif

                                @if($order->checkout_ref)
                                    <span class="text-muted small ml-auto" style="font-size: 10px;">{{ __('Ref:') }} {{ $order->checkout_ref }}</span>
                                @endif
                            </div>

                            <div class="d-flex" style="gap: 8px;">
                                <a href="{{route('user.order.invoice',$order->id)}}" class="btn btn-info btn-sm flex-fill" style="border-radius: 6px; font-size: 12px; padding: 7px 10px;">
                                    <i class="fas fa-eye mr-1"></i> {{__('Details')}}
                                </a>
                                <a href="{{route('front.order.track') . '?order_number=' . $order->transaction_number}}" class="btn btn-outline-primary btn-sm flex-fill" style="border-radius: 6px; font-size: 12px; padding: 7px 10px;">
                                    <i class="fas fa-map-marker-alt mr-1"></i> {{__('Track')}}
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Desktop Table View for Orders -->
                <div class="card shadow-sm border-0 d-none d-md-block" style="border-radius: 12px; overflow: hidden;">
                    <div class="card-body p-0">
                        <div class="u-table-res">
                          <table class="table table-bordered mb-0">
                            <thead>
                              <tr>
                                <th>{{__('Order')}} #</th>
                                <th>{{__('Total')}}</th>
                                <th>{{__('Order Status')}}</th>
                                <th>{{__('Payment Status')}}</th>
                                <th>{{__('Date Purchased')}}</th>
                                <th>{{__('Action')}}</th>
                              </tr>
                            </thead>
                            <tbody>
                             @foreach ($orders as $order)
                             <tr>
                              <td>
                                <a class="navi-link font-weight-bold" href="{{route('user.order.invoice',$order->id)}}">{{$order->transaction_number}}</a>
                                <div class="mt-1">
                                  <span class="badge badge-light border text-dark" style="font-size: 11px;">
                                    <i class="fas fa-store text-primary mr-1"></i> {{ $order->store_name }}
                                  </span>
                                </div>
                                @if($order->checkout_ref)
                                  <small class="text-muted d-block" style="font-size: 10px;">{{ __('Checkout:') }} {{ $order->checkout_ref }}</small>
                                @endif
                              </td>
                              <td>
                                <span class="font-weight-bold text-dark">
                                  @if ($setting->currency_direction == 1)
                                  {{$order->currency_sign}}{{PriceHelper::OrderTotal($order)}}
                                  @else
                                  {{PriceHelper::OrderTotal($order)}}{{$order->currency_sign}}
                                  @endif
                                </span>
                              </td>
                              <td>
                                @if($order->order_status == 'Pending')
                                <span class="badge badge-warning text-dark">{{__('Pending')}}</span>
                                @elseif($order->order_status == 'Accepted')
                                <span class="badge badge-primary">{{__('Accepted')}}</span>
                                @elseif($order->order_status == 'Send to Delivery House')
                                <span class="badge" style="background-color: #6f42c1; color: #fff;">{{__('Send to Delivery House')}}</span>
                                @elseif($order->order_status == 'In Progress')
                                <span class="badge badge-info">{{__('In Progress')}}</span>
                                @elseif($order->order_status == 'Delivered')
                                <span class="badge badge-success">{{__('Delivered')}}</span>
                                @else
                                <span class="badge badge-danger">{{__('Canceled')}}</span>
                                @endif
                              </td>
                              <td>
                                @if($order->payment_status == 'Paid')
                                <span class="badge badge-success">{{$order->payment_status}}</span>
                                @elseif($order->payment_status == 'Pending')
                                <span class="badge badge-warning text-dark">{{$order->payment_status}}</span>
                                @else
                                <span class="badge badge-danger">{{$order->payment_status}}</span>
                                @endif
                              </td>

                              <td><small>{{$order->created_at->format('d M, Y')}}</small></td>
                              <td>
                                <div class="btn-group-vertical btn-group-sm">
                                  <a href="{{route('user.order.invoice',$order->id)}}" class="btn btn-info btn-xs mb-1">
                                    <i class="fas fa-eye mr-1"></i> {{__('Details')}}
                                  </a>
                                  <a href="{{route('front.order.track') . '?order_number=' . $order->transaction_number}}" class="btn btn-outline-primary btn-xs">
                                    <i class="fas fa-map-marker-alt mr-1"></i> {{__('Track')}}
                                  </a>
                                </div>
                              </td>
                            </tr>
                             @endforeach
                            </tbody>
                          </table>
                        </div>
                    </div>
                </div>
            @else
                <!-- Clean Empty State Card -->
                <div class="card shadow-sm border-0" style="border-radius: 14px; background: #ffffff;">
                    <div class="card-body text-center py-5">
                        <div class="mb-3" style="width: 80px; height: 80px; border-radius: 50%; background: #eff6ff; display: inline-flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(0,123,255,0.1);">
                            <i class="icon-shopping-bag text-primary" style="font-size: 36px;"></i>
                        </div>
                        <h5 class="font-weight-bold text-dark mb-2">{{ __('No Orders Found') }}</h5>
                        <p class="text-muted small mb-4" style="max-width: 320px; margin: 0 auto; line-height: 1.5;">
                            {{ __('You haven\'t placed any orders yet. Discover our trending products and start shopping today!') }}
                        </p>
                        <a href="{{ route('front.catalog') }}" class="btn btn-primary px-4 shadow-sm font-weight-bold" style="border-radius: 25px; padding: 10px 24px; font-size: 14px;">
                            <i class="icon-shopping-cart mr-2"></i>{{ __('Start Shopping') }}
                        </a>
                    </div>
                </div>
            @endif

       </div>
    </div>
 </div>


@endsection

