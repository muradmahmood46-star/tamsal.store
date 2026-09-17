@extends('master.front')
@section('title')
    {{__('Orders')}}
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
                    <li>{{__('Orders')}}</li>
                 </ul>
            </div>
        </div>
    </div>
 </div>
 <!-- Page Content-->
 <div class="container   padding-bottom-3x mb-1">
    <div class="row">
       @include('includes.user_sitebar')
       <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
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

      </div>
    </div>
 </div>


@endsection

