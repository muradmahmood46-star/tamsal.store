@extends('master.back')

@section('content')

<!-- Start of Main Content -->
<div class="container-fluid">

	<!-- Page Heading -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between">
                <h3 class=" mb-0">{{ __('Order Invoice') }} </h3>
                <div>
                    <a class="btn btn-primary btn-sm" href="{{route('back.order.index')}}"><i class="fas fa-chevron-left"></i> {{ __('Back') }}</a>
                    <a class="btn btn-info btn-sm" href="javascript:;" data-toggle="modal" data-target="#sendEmailModal"><i class="fas fa-envelope"></i> {{ __('Send Email to Customer') }}</a>
                    <a class="btn btn-primary btn-sm" href="{{ route('back.order.print',$order->id) }}" target="_blank"><i class="fas fa-print"></i> {{ __('print') }}</a>
                </div>
            </div>
        </div>
    </div>
@php
    if($order->state){
        $state = json_decode($order->state,true);
    }else{
        $state = [];
    }
@endphp

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                            <div class="row">
                                <div class="col text-center">

                                <!-- Logo -->
                                <img class="img-fluid mb-5 mh-70" width="180" alt="Logo" src="{{url('/core/public/storage/images/'.$setting->logo)}}">

                            </div>
                            </div> <!-- / .row -->
                            @php
                                $bill = json_decode($order->billing_info, true);
                                $customerName = '';
                                if (isset($bill['bill_first_name'])) {
                                    $customerName = trim($bill['bill_first_name'] . ' ' . ($bill['bill_last_name'] ?? ''));
                                } elseif ($order->user && $order->user->name) {
                                    $customerName = $order->user->name;
                                }

                                $checkoutRef = $order->checkout_ref ?? null;
                                $adminSiblingOrders = $checkoutRef ? \App\Models\Order::where('checkout_ref', $checkoutRef)->get() : collect([$order]);
                            @endphp

                            @if($adminSiblingOrders->count() > 1)
                                <div class="alert alert-info p-3 mb-4 rounded shadow-sm border-0">
                                    <h6 class="font-weight-bold mb-2 text-dark"><i class="fas fa-boxes text-primary mr-1"></i> {{ __('Linked Multi-Vendor Sub-Orders (Checkout Ref: :ref):', ['ref' => $checkoutRef]) }}</h6>
                                    <p class="small text-muted mb-2">{{ __('This customer checkout contained products from multiple stores. Each store order is managed and fulfilled separately:') }}</p>
                                    <div class="d-flex flex-wrap" style="gap: 8px;">
                                        @foreach($adminSiblingOrders as $admOrd)
                                            <a href="{{ route('back.order.invoice', $admOrd->id) }}" class="btn btn-xs {{ $admOrd->id == $order->id ? 'btn-primary' : 'btn-outline-primary bg-white' }} py-1 px-2" style="border-radius: 6px;">
                                                <i class="fas fa-store mr-1"></i> {{ $admOrd->store_name }} ({{ $admOrd->transaction_number }}) - <strong>{{ __($admOrd->order_status) }}</strong>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <div class="row">
                                <div class="col-12">
                                    <h5><b>{{__('Order Details :')}}</b></h5>

                                    <span class="text-muted">{{__('Customer Name :')}}</span> <strong>{{ $customerName ?: ($order->account_name ?? 'N/A') }}</strong><br>
                                    <span class="text-muted">{{__('Store / Vendor :')}}</span> <strong class="badge badge-primary px-2 py-1">{{ $order->store_name }}</strong><br>
                                    <span class="text-muted">{{__('Order Id :')}}</span> <strong>{{$order->transaction_number}}</strong><br>
                                    @if($order->checkout_ref)
                                        <span class="text-muted">{{__('Checkout Reference :')}}</span> <strong>{{$order->checkout_ref}}</strong><br>
                                    @endif
                                    <span class="text-muted">{{__('Order Date :')}}</span> <strong>{{$order->created_at->format('M d, Y')}}</strong><br>
                                    <span class="text-muted">{{__('Total Amount :')}}</span> <strong class="text-primary">
                                        @if ($setting->currency_direction == 1)
                                            {{$order->currency_sign}}{{PriceHelper::OrderTotal($order)}}
                                        @else
                                            {{PriceHelper::OrderTotal($order)}}{{$order->currency_sign}}
                                        @endif
                                    </strong><br>
                                    <span class="text-muted">{{__('Payment Status :')}}</span>
                                    @php
                                        $isCod = in_array(strtolower(trim($order->payment_method)), ['cash on delivery', 'cod']);
                                    @endphp
                                    @if($order->payment_status == 'Paid')
                                    <div class="badge badge-success">
                                        {{ $isCod ? __('Cash Received') : __('Paid') }}
                                    </div>
                                    @elseif($order->payment_status == 'Pending')
                                    <div class="badge badge-warning">
                                        {{__('Pending')}}
                                    </div>
                                    @else
                                    <div class="badge badge-danger">
                                        {{ $isCod ? __("Cash Hasn't Received") : __('Unpaid') }}
                                    </div>
                                    @endif
                                    <br>

                                    <span class="text-muted">{{__('Order Status :')}}</span>
                                    @php
                                        $wasCanceled = App\Models\TrackOrder::where('order_id', $order->id)->where('title', 'Canceled')->exists();
                                    @endphp
                                    @if($order->order_status == 'Delivered')
                                        <div class="badge badge-success">
                                            {{ $wasCanceled ? __('Delivered (After Re-attempt)') : __('Delivered') }}
                                        </div>
                                    @elseif($order->order_status == 'In Progress')
                                        <div class="badge badge-info">
                                            {{ $wasCanceled ? __('Delivery in Progress (Re-attempt)') : __('Delivery in Progress') }}
                                        </div>
                                    @elseif($order->order_status == 'Send to Delivery House')
                                        <div class="badge" style="background-color: #6f42c1; color: #fff;">
                                            {{ __('Send to Delivery House') }}
                                        </div>
                                    @elseif($order->order_status == 'Accepted')
                                        <div class="badge badge-primary">
                                            {{ __('Accepted') }}
                                        </div>
                                    @elseif($order->order_status == 'Canceled')
                                        <div class="badge badge-danger">
                                            {{__('Canceled')}}
                                        </div>
                                    @else
                                        <div class="badge badge-warning">
                                            {{ $wasCanceled ? __('Re-attempting Delivery (New Order)') : __('Pending (New Order)') }}
                                        </div>
                                    @endif
                                    <br>

                                    <span class="text-muted">{{__('Payment Method :')}}</span> <strong>{{$order->payment_method }}</strong><br>

                                    @if($order->payment_method != 'Cash On Delivery' && ($order->account_name || $order->account_number || $order->payment_screenshot || $order->bank_name || in_array($order->payment_method, ['Easypaisa', 'JazzCash', 'Bank Transfer'])))
                                        @if($order->bank_name)
                                            <span class="text-muted">{{__('Bank Name :')}}</span> <strong>{{$order->bank_name }}</strong><br>
                                        @endif
                                        @if($order->account_name)
                                            <span class="text-muted">{{__('Account Name :')}}</span> <strong>{{$order->account_name }}</strong><br>
                                        @endif
                                        @if($order->account_number)
                                            <span class="text-muted">{{__('Account Number :')}}</span> <strong>{{$order->account_number }}</strong><br>
                                        @endif
                                        @if($order->txnid)
                                            <span class="text-muted">{{__('Transaction ID :')}}</span> <strong>{{$order->txnid }}</strong><br>
                                        @endif
                                        <span class="text-muted">{{__('Payment Screenshot :')}}</span>
                                        @if($order->payment_screenshot)
                                            <br>
                                            <a href="{{ asset('core/public/storage/receipts/'.$order->payment_screenshot) }}" target="_blank">
                                                <img src="{{ asset('core/public/storage/receipts/'.$order->payment_screenshot) }}" style="max-width: 220px; max-height: 250px; object-fit: contain; border-radius: 6px; border: 2px solid #007bff; margin-top: 6px; box-shadow: 0 4px 10px rgba(0,0,0,0.15);" alt="Receipt">
                                            </a>
                                            <br>
                                        @else
                                            <span class="text-danger font-italic">{{ __('No screenshot uploaded') }}</span><br>
                                        @endif

                                        @if($order->order_status == 'Pending')
                                        <div class="mt-3 p-3 bg-light border rounded" style="max-width: 500px;">
                                            <h6 class="font-weight-bold mb-2 text-dark"><i class="fas fa-tasks text-primary"></i> {{ __('Payment Verification Action :') }}</h6>
                                            <div class="d-flex align-items-center flex-wrap">
                                                @if($order->payment_status != 'Paid')
                                                    <a class="btn btn-success btn-sm mr-2 mb-2" href="javascript:;" data-toggle="modal" data-target="#statusModal" data-href="{{ route('back.order.status', [$order->id, 'payment_status', 'Paid']) }}">
                                                        <i class="fas fa-check-circle"></i> {{ __('Accept Payment (Mark as Paid)') }}
                                                    </a>
                                                @else
                                                    <span class="badge badge-success p-2 mr-2 mb-2"><i class="fas fa-check-circle"></i> {{ __('Payment Accepted (Paid)') }}</span>
                                                @endif

                                                @if($order->payment_status != 'Unpaid')
                                                    <a class="btn btn-danger btn-sm mr-2 mb-2" href="javascript:;" data-toggle="modal" data-target="#statusModal" data-href="{{ route('back.order.status', [$order->id, 'payment_status', 'Unpaid']) }}">
                                                        <i class="fas fa-times-circle"></i> {{ __('Reject Payment (Mark as Unpaid)') }}
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                        @endif
                                    @else
                                        @if($order->txnid)
                                            <span class="text-muted">{{__('Transaction Id :')}}</span> <strong>{{$order->txnid}}</strong><br>
                                        @endif
                                    @endif

                                    @if($isCod && $order->order_status == 'Delivered')
                                        <div class="mt-3 p-3 bg-light border rounded" style="max-width: 500px;">
                                            <h6 class="font-weight-bold mb-2 text-dark"><i class="fas fa-money-bill-wave text-success"></i> {{ __('Cash on Delivery Payment Action :') }}</h6>
                                            <div class="d-flex align-items-center flex-wrap">
                                                @if($order->payment_status != 'Paid')
                                                    <a class="btn btn-success btn-sm mr-2 mb-2 font-weight-bold" href="javascript:;" data-toggle="modal" data-target="#statusModal" data-href="{{ route('back.order.status', [$order->id, 'payment_status', 'Paid']) }}">
                                                        <i class="fas fa-check-circle"></i> {{ __('Cash Received') }}
                                                    </a>
                                                    <span class="badge badge-danger p-2 mr-2 mb-2"><i class="fas fa-exclamation-circle"></i> {{ __("Cash Hasn't Received") }}</span>
                                                @else
                                                    <span class="badge badge-success p-2 mr-2 mb-2"><i class="fas fa-check-circle"></i> {{ __('Cash Received') }}</span>
                                                    <a class="btn btn-danger btn-sm mr-2 mb-2" href="javascript:;" data-toggle="modal" data-target="#statusModal" data-href="{{ route('back.order.status', [$order->id, 'payment_status', 'Unpaid']) }}">
                                                        <i class="fas fa-times-circle"></i> {{ __("Mark as Cash Hasn't Received") }}
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    <div class="mt-3 p-3 bg-light border rounded" style="max-width: 520px;">
                                        <h6 class="font-weight-bold mb-2 text-dark"><i class="fas fa-truck text-info"></i> {{ __('Order Fulfillment Action :') }}</h6>
                                        <div class="d-flex align-items-center flex-wrap">
                                            @if($order->order_status == 'Pending')
                                                <a class="btn btn-primary btn-sm mr-2 mb-2 font-weight-bold" href="javascript:;" data-toggle="modal" data-target="#statusModal" data-href="{{ route('back.order.status', [$order->id, 'order_status', 'Accepted']) }}">
                                                    <i class="fas fa-check"></i> {{ __('Accept Order') }}
                                                </a>
                                                <a class="btn btn-danger btn-sm mr-2 mb-2" href="javascript:;" data-toggle="modal" data-target="#statusModal" data-href="{{ route('back.order.status', [$order->id, 'order_status', 'Canceled']) }}">
                                                    <i class="fas fa-times-circle"></i> {{ __('Cancel Order') }}
                                                </a>
                                            @elseif($order->order_status == 'Accepted')
                                                <a class="btn btn-sm mr-2 mb-2 font-weight-bold" style="background-color: #6f42c1; color: #fff;" href="javascript:;" data-toggle="modal" data-target="#statusModal" data-href="{{ route('back.order.status', [$order->id, 'order_status', 'Send to Delivery House']) }}">
                                                    <i class="fas fa-warehouse"></i> {{ __('Send to Delivery House') }}
                                                </a>
                                                <a class="btn btn-danger btn-sm mr-2 mb-2" href="javascript:;" data-toggle="modal" data-target="#statusModal" data-href="{{ route('back.order.status', [$order->id, 'order_status', 'Canceled']) }}">
                                                    <i class="fas fa-times-circle"></i> {{ __('Cancel Order') }}
                                                </a>
                                            @elseif($order->order_status == 'Send to Delivery House')
                                                <a class="btn btn-info btn-sm mr-2 mb-2 font-weight-bold" href="javascript:;" data-toggle="modal" data-target="#statusModal" data-href="{{ route('back.order.status', [$order->id, 'order_status', 'In Progress']) }}">
                                                    <i class="fas fa-truck-loading"></i> {{ __('Mark as In Progress (Dispatched)') }}
                                                </a>
                                                <a class="btn btn-danger btn-sm mr-2 mb-2" href="javascript:;" data-toggle="modal" data-target="#statusModal" data-href="{{ route('back.order.status', [$order->id, 'order_status', 'Canceled']) }}">
                                                    <i class="fas fa-times-circle"></i> {{ __('Cancel Order') }}
                                                </a>
                                            @elseif($order->order_status == 'In Progress')
                                                <a class="btn btn-success btn-sm mr-2 mb-2 font-weight-bold" href="javascript:;" data-toggle="modal" data-target="#statusModal" data-href="{{ route('back.order.status', [$order->id, 'order_status', 'Delivered']) }}">
                                                    <i class="fas fa-check-circle"></i> {{ __('Add to Delivered') }}
                                                </a>
                                                <a class="btn btn-danger btn-sm mr-2 mb-2 font-weight-bold" href="javascript:;" data-toggle="modal" data-target="#statusModal" data-href="{{ route('back.order.status', [$order->id, 'order_status', 'Canceled']) }}">
                                                    <i class="fas fa-times-circle"></i> {{ __('Order Not Received by Customer') }}
                                                </a>
                                            @elseif($order->order_status == 'Delivered')
                                                <span class="badge badge-success p-2 mr-2 mb-2"><i class="fas fa-check-circle"></i> {{ __('Order Delivered') }}</span>
                                                <a class="btn btn-outline-info btn-sm mr-2 mb-2" href="javascript:;" data-toggle="modal" data-target="#statusModal" data-href="{{ route('back.order.status', [$order->id, 'order_status', 'In Progress']) }}">
                                                    <i class="fas fa-undo"></i> {{ __('Move back to In Progress') }}
                                                </a>
                                            @elseif($order->order_status == 'Canceled')
                                                <span class="badge badge-danger p-2 mr-2 mb-2"><i class="fas fa-ban"></i> {{ __('Order Canceled') }}</span>
                                                <a class="btn btn-warning btn-sm mr-2 mb-2 font-weight-bold" href="javascript:;" data-toggle="modal" data-target="#statusModal" data-href="{{ route('back.order.status', [$order->id, 'order_status', 'Pending']) }}">
                                                    <i class="fas fa-redo-alt"></i> {{ __('Add to Order Again') }}
                                                </a>
                                            @endif
                                        </div>
                                    </div>

                                    <br>
                                    <br>
                                    </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                      <h5>{{__('Billing Address :')}}</h5>
                                          @php
                                              $bill = json_decode($order->billing_info,true) ?: [];

                                          @endphp

                                          <span class="text-muted">{{__('Name')}}: </span>{{$bill['bill_first_name'] ?? ($order->user->first_name ?? '')}} {{$bill['bill_last_name'] ?? ($order->user->last_name ?? '')}}<br>
                                          <span class="text-muted">{{__('Email')}}: </span>{{$bill['bill_email'] ?? ($order->user->email ?? '')}}<br>
                                          <span class="text-muted">{{__('Phone')}}: </span>{{$bill['bill_phone'] ?? ($order->user->phone ?? '')}}<br>
                                          @if (!empty($bill['bill_address1']))
                                          <span class="text-muted">{{__('Address')}}: </span>{{$bill['bill_address1']}}, {{isset($bill['bill_address2']) ? $bill['bill_address2'] : ''}}<br>
                                          @endif
                                          @if (!empty($bill['bill_country']))
                                          <span class="text-muted">{{__('Country')}}: </span>{{$bill['bill_country']}}<br>
                                          @endif
                                          @if (!empty($bill['bill_city']))
                                          <span class="text-muted">{{__('City')}}: </span>{{$bill['bill_city']}}<br>
                                          @endif
                                          @if (!empty($state['name']))
                                          <span class="text-muted">{{__('State')}}: </span>{{$state['name']}}<br>
                                          @endif
                                          @if (!empty($bill['bill_zip']))
                                          <span class="text-muted">{{__('Zip')}}: </span>{{$bill['bill_zip']}}<br>
                                          @endif
                                          @if (!empty($bill['bill_company']))
                                          <span class="text-muted">{{__('Company')}}: </span>{{$bill['bill_company']}}<br>
                                          @endif


                                </div>
                                <div class="col-12 col-md-6">
                                  <h5>{{__('Shipping Address :')}}</h5>
                                      @php
                                          $ship = json_decode($order->shipping_info,true) ?: [];
                                      @endphp
                                          <span class="text-muted">{{__('Name')}}: </span>{{$ship['ship_first_name'] ?? ($bill['bill_first_name'] ?? '')}} {{$ship['ship_last_name'] ?? ($bill['bill_last_name'] ?? '')}} <br>
                                          <span class="text-muted">{{__('Email')}}: </span>{{$ship['ship_email'] ?? ($bill['bill_email'] ?? ($order->user->email ?? ''))}}<br>
                                          <span class="text-muted">{{__('Phone')}}: </span>{{$ship['ship_phone'] ?? ($bill['bill_phone'] ?? ($order->user->phone ?? ''))}}<br>
                                          @if (!empty($ship['ship_address1']))
                                          <span class="text-muted">{{__('Address')}}: </span>{{$ship['ship_address1']}}, {{isset($ship['ship_address2']) ? $ship['ship_address2'] : ''}}<br>
                                          @endif
                                          @if (!empty($ship['ship_country']))
                                          <span class="text-muted">{{__('Country')}}: </span>{{$ship['ship_country']}}<br>
                                          @endif
                                          @if (!empty($ship['ship_city']))
                                          <span class="text-muted">{{__('City')}}: </span>{{$ship['ship_city']}}<br>
                                          @endif
                                          @if (!empty($state['name']))
                                          <span class="text-muted">{{__('State')}}: </span>{{$state['name']}}<br>
                                          @endif
                                          @if (!empty($ship['ship_zip']))
                                          <span class="text-muted">{{__('Zip')}}: </span>{{$ship['ship_zip']}}<br>
                                          @endif
                                          @if (!empty($ship['ship_company']))
                                          <span class="text-muted">{{__('Company')}}: </span>{{$ship['ship_company']}}<br>
                                          @endif

                                </div>
                              </div>
                            <div class="row">
                                <div class="col-12">

                                <!-- Table -->
                                <div class="gd-responsive-table">
                                    <table class="table my-4">
                                    <thead>
                                        <tr>
                                        <th width="35%" class="px-0 bg-transparent border-top-0">
                                            <span class="h6">{{__('Products')}}</span>
                                        </th>
                                        <th class="px-0 bg-transparent border-top-0">
                                            <span class="h6">{{__('Attribute')}}</span>
                                        </th>
                                        <th class="px-0 bg-transparent border-top-0">
                                            <span class="h6">{{__('Quantity')}}</span>
                                        </th>
                                        <th class="px-0 bg-transparent border-top-0 text-right">
                                            <span class="h6">{{__('Price')}}</span>
                                        </th>
                                        <th class="px-0 bg-transparent border-top-0 text-right">
                                            <span class="h6">{{__('Advance Discount')}}</span>
                                        </th>
                                        <th class="px-0 bg-transparent border-top-0 text-right">
                                            <span class="h6">{{__('Delivery Charges')}}</span>
                                        </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $option_price = 0;
                                            $total = 0;
                                            $isCod = in_array(strtolower(trim($order->payment_method ?? '')), ['cash on delivery', 'cod']);
                                        @endphp
                                    @foreach (json_decode($order->cart,true) as $key => $item)
                                    @php
                                        $total += $item['main_price'] * $item['qty'];
                                        $option_price += ($item['attribute_price'] ?? 0);
                                        $grandSubtotal = $total + $option_price;

                                        $itemId = explode('-', $key)[0];
                                        $productModel = \App\Models\Item::find($itemId);
                                        $itemAdvDiscount = 0;
                                        if (!$isCod && $productModel && $productModel->advance_payment_amount > 0) {
                                            $item_total_price = ($item['main_price'] + ($item['attribute_price'] ?? 0)) * $item['qty'];
                                            if ($productModel->advance_payment_type == 'percentage') {
                                                $itemAdvDiscount = ($item_total_price * $productModel->advance_payment_amount) / 100;
                                            } else {
                                                $curr_val = $order->currency_value;
                                                $itemAdvDiscount = ($curr_val > 0 ? ($productModel->advance_payment_amount / $curr_val) : $productModel->advance_payment_amount) * $item['qty'];
                                            }
                                        }

                                        $itemDeliveryFee = 0;
                                        $isItemFreeDelivery = true;
                                        if ($productModel) {
                                            if ($productModel->is_free_delivery == 1) {
                                                $isItemFreeDelivery = true;
                                            } elseif ($productModel->delivery_fee > 0) {
                                                $isItemFreeDelivery = false;
                                                $itemDeliveryFee = $productModel->delivery_fee;
                                            } else {
                                                $isItemFreeDelivery = true;
                                            }
                                        }
                                    @endphp
                                    <tr>
                                        <td class="px-0">
                                            {{$item['name']}}
                                        </td>
                                        <td class="px-0">
                                            @if(!empty($item['attribute']['option_name']))
                                            @foreach ($item['attribute']['option_name'] as $optionkey => $option_name)
                                            <span class="entry-meta"><b>{{$option_name}}</b> :
                                                @php
                                                    $optPrice = $item['attribute']['option_price'][$optionkey] ?? 0;
                                                @endphp
                                                @if ($setting->currency_direction == 1)
                                                {{$order->currency_sign}}{{round($optPrice * $order->currency_value, 2)}}
                                                @else
                                                {{round($optPrice * $order->currency_value, 2)}}{{$order->currency_sign}}
                                                @endif

                                            </span>
                                            @endforeach
                                            @else
                                            --
                                            @endif
                                        </td>
                                        <td class="px-0">
                                            {{$item['qty']}}
                                        </td>

                                        <td class="px-0 text-right">
                                            @if ($setting->currency_direction == 1)
                                                {{$order->currency_sign}}{{round($item['main_price']*$order->currency_value,2)}}
                                            @else
                                                {{round($item['main_price']*$order->currency_value,2)}}{{$order->currency_sign}}
                                            @endif
                                        </td>
                                        <td class="px-0 text-right">
                                            @if($itemAdvDiscount > 0)
                                                <span class="text-danger font-weight-bold">
                                                    @if ($setting->currency_direction == 1)
                                                        -{{$order->currency_sign}}{{round($itemAdvDiscount * $order->currency_value, 2)}}
                                                    @else
                                                        -{{round($itemAdvDiscount * $order->currency_value, 2)}}{{$order->currency_sign}}
                                                    @endif
                                                </span>
                                            @else
                                                <span class="text-muted">--</span>
                                            @endif
                                        </td>
                                        <td class="px-0 text-right">
                                            @if($isItemFreeDelivery || $itemDeliveryFee == 0)
                                                <span class="badge badge-success px-2 py-1" style="font-size: 11px;">{{ __('Free Delivery') }}</span>
                                            @else
                                                <span class="text-dark font-weight-bold">
                                                    @if ($setting->currency_direction == 1)
                                                        {{$order->currency_sign}}{{round($itemDeliveryFee, 2)}}
                                                    @else
                                                        {{round($itemDeliveryFee, 2)}}{{$order->currency_sign}}
                                                    @endif
                                                </span>
                                            @endif
                                        </td>
                                        </tr>
                                    @endforeach
                                        <tr>
                                        <td class="padding-top-2x" colspan="6">
                                        </td>
                                        </tr>
                                        @if($order->tax!=0)
                                        <tr>
                                        <td class="px-0 border-top border-top-2">
                                        <span class="text-muted">{{__('Tax')}}</span>
                                        </td>
                                        <td class="px-0 text-right border-top border-top-2" colspan="5">
                                            <span>
                                             @if ($setting->currency_direction == 1)
                                                {{$order->currency_sign}}{{round($order->tax*$order->currency_value,2)}}
                                            @else
                                            {{round($order->tax*$order->currency_value,2)}}{{$order->currency_sign}}
                                            @endif
                                            </span>
                                        </td>
                                        </tr>
                                        @endif
                                        @if(json_decode($order->discount,true))
                                        @php
                                            $discount = json_decode($order->discount,true);
                                        @endphp
                                        <tr>
                                        <td class="px-0 border-top border-top-2">
                                        <span class="text-muted">{{__('Coupon discount')}} ({{$discount['code']['code_name']}})</span>
                                        </td>
                                        <td class="px-0 text-right border-top border-top-2" colspan="5">
                                            <span class="text-danger">
                                            @if ($setting->currency_direction == 1)
                                                -{{$order->currency_sign}}{{round($discount['discount'] * $order->currency_value,2)}}
                                            @else
                                                -{{round($discount['discount'] * $order->currency_value,2)}}{{$order->currency_sign}}
                                            @endif
                                            </span>
                                        </td>
                                        </tr>
                                        @endif
                                        @if(json_decode($order->shipping,true))
                                        @php
                                            $shipping = json_decode($order->shipping,true);
                                        @endphp
                                        <tr>
                                        <td class="px-0 border-top border-top-2">
                                        <span class="text-muted">{{__('Delivery Charges')}}</span>
                                        </td>
                                        <td class="px-0 text-right border-top border-top-2" colspan="5">
                                            <span >
                                            @if(($shipping['price'] ?? 0) == 0)
                                                <span class="text-success font-weight-bold">{{ __('Free Delivery') }}</span>
                                            @else
                                                @if ($setting->currency_direction == 1)
                                                    {{$order->currency_sign}}{{round($shipping['price']*$order->currency_value,2)}}
                                                @else
                                                    {{round($shipping['price']*$order->currency_value,2)}}{{$order->currency_sign}}
                                                @endif
                                            @endif

                                            </span>
                                        </td>
                                        </tr>
                                        @endif
                                        @if(json_decode($order->state_price,true))
                                        <tr>
                                        <td class="px-0 border-top border-top-2">
                                        <span class="text-muted">{{__('State Tax')}}</span>
                                        </td>
                                        <td class="px-0 text-right border-top border-top-2" colspan="5">
                                            <span >
                                            @if ($setting->currency_direction == 1)
                                            {{isset($state['type']) && $state['type'] == 'percentage' ?  ' ('.$state['price'].'%) ' : ''}}  {{$order->currency_sign}}{{round($order['state_price']*$order->currency_value,2)}}
                                            @else
                                            {{isset($state['type']) &&  $state['type'] == 'percentage' ?  ' ('.$state['price'].'%) ' : ''}}  {{round($order['state_price']*$order->currency_value,2)}}{{$order->currency_sign}}
                                            @endif

                                            </span>
                                        </td>
                                        </tr>
                                        @endif
                                        @php
                                            $advDiscount = !$isCod ? PriceHelper::getAdvancePaymentDiscount(json_decode($order->cart, true)) : 0;
                                        @endphp
                                        @if($advDiscount > 0)
                                        <tr>
                                        <td class="px-0 border-top border-top-2">
                                        <span class="text-danger font-weight-bold"><i class="fas fa-gift mr-1"></i> {{__('Advance Payment Offer Discount')}}</span>
                                        </td>
                                        <td class="px-0 text-right border-top border-top-2" colspan="5">
                                            <span class="text-danger font-weight-bold">
                                            @if ($setting->currency_direction == 1)
                                                -{{$order->currency_sign}}{{round($advDiscount * $order->currency_value, 2)}}
                                            @else
                                                -{{round($advDiscount * $order->currency_value, 2)}}{{$order->currency_sign}}
                                            @endif
                                            </span>
                                        </td>
                                        </tr>
                                        @endif
                                        <tr>
                                        <td class="px-0 border-top border-top-2">

                                        @if ($order->payment_method == 'Cash On Delivery')
                                        <strong>{{__('Total amount')}}</strong>
                                        @else
                                        <strong>{{__('Total amount due')}}</strong>
                                        @endif
                                        </td>
                                        <td class="px-0 text-right border-top border-top-2" colspan="5">
                                            <span class="h3">
                                                @if ($setting->currency_direction == 1)
                                                {{$order->currency_sign}}{{PriceHelper::OrderTotal($order)}}
                                                @else
                                                {{PriceHelper::OrderTotal($order)}}{{$order->currency_sign}}
                                                @endif
                                            </span>
                                        </td>
                                        </tr>
                                    </tbody>
                                    </table>
                                </div>
                                </div>
                            </div> <!-- / .row -->
                    </div>
                </div>
            </div>
        </div>


</div>

{{-- STATUS MODAL --}}
<div class="modal fade" id="statusModal" tabindex="-1" role="dialog" aria-labelledby="statusModalModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">{{ __('Update Status?') }}</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
		</div>
        <div class="modal-body">
            {{ __('You are going to update the status.') }} {{ __('Do you want to proceed?') }}
		</div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
            <a href="" class="btn btn-ok btn-success">{{ __('Update') }}</a>
		</div>
      </div>
    </div>
</div>

{{-- SEND CUSTOM EMAIL MODAL --}}
@php
    $billing_for_email = json_decode($order->billing_info, true) ?: [];
    $shipping_for_email = json_decode($order->shipping_info, true) ?: [];
    $cust_email = $order->user->email ?? ($billing_for_email['bill_email'] ?? ($shipping_for_email['ship_email'] ?? ''));
@endphp
<div class="modal fade" id="sendEmailModal" tabindex="-1" role="dialog" aria-labelledby="sendEmailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <form action="{{ route('back.order.send.email', $order->id) }}" method="POST">
        @csrf
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold" id="sendEmailModalLabel"><i class="fas fa-paper-plane mr-2"></i>{{ __('Send Email to Customer') }}</h5>
                <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div class="form-group mb-3">
                    <label class="font-weight-bold text-dark">{{ __('Recipient Customer Email') }} <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" value="{{ $cust_email }}" required>
                </div>
                <div class="form-group mb-3">
                    <label class="font-weight-bold text-dark">{{ __('Subject') }} <span class="text-danger">*</span></label>
                    <input type="text" name="subject" class="form-control" value="Update regarding Order #{{ $order->transaction_number }} - {{ $setting->title }}" required>
                </div>
                <div class="form-group mb-0">
                    <label class="font-weight-bold text-dark">{{ __('Message / Custom Update') }} <span class="text-danger">*</span></label>
                    <textarea name="message" class="form-control" rows="6" placeholder="{{ __('Type message here... e.g. Courier tracking ID (TCS / Trax / Leopards), estimated delivery date, order confirmation details, etc.') }}" required></textarea>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-primary font-weight-bold shadow-sm"><i class="fas fa-paper-plane mr-1"></i> {{ __('Send Email Now') }}</button>
            </div>
        </div>
      </form>
    </div>
</div>

@endsection
