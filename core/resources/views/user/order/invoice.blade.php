@extends('master.front')
@section('title')
    {{ __('Invoice') }}
@endsection
@section('content')

    <!-- Page Title-->
    <div class="page-title">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <ul class="breadcrumbs">
                        <li><a href="{{ route('user.order.index') }}">{{ __('Orders') }}</a> </li>
                        <li class="separator"></li>
                        <li>{{ __('Order Invoice') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @php
        if ($order->state) {
            $state = json_decode($order->state, true);
        } else {
            $state = [];
        }
    @endphp
    <!-- Page Content-->
    <div class="container  padding-bottom-3x mb-1 print_invoice">
        <div class="card card-body p-5">
            <div class="row mb-3">
                <div class="col-lg-12 d-flex flex-wrap justify-content-between align-items-center">
                    <div>
                        <a href="{{ route('user.order.index') }}"
                            class="btn btn-sm btn-outline-secondary d-inline-block mr-2"><i class="fas fa-arrow-left mr-1"></i> <span>{{ __('Back to Orders') }}</span></a>
                        <a href="{{ route('front.order.track') . '?order_number=' . $order->transaction_number }}"
                            class="btn btn-sm btn-primary d-inline-block mr-2"><i class="fas fa-map-marker-alt mr-1"></i> <span>{{ __('Track This Order') }}</span></a>
                    </div>
                    <div class="mt-2 mt-sm-0">
                        <a href="{{ route('user.order.print', $order->id) }}" target="_blank"
                            class="btn btn-sm btn-outline-primary invoice_price d-inline-block"><i class="fas fa-print mr-1"></i> <span>{{ __('Print Invoice') }}</span></a>
                    </div>
                </div>
            </div> <!-- / .row -->

            @php
                $checkoutRef = $order->checkout_ref ?? null;
                $siblingOrders = $checkoutRef ? \App\Models\Order::where('checkout_ref', $checkoutRef)->get() : collect([$order]);
            @endphp

            @if($siblingOrders->count() > 1)
                <div class="alert alert-info p-3 mb-4 rounded-lg shadow-sm border-0">
                    <h6 class="font-weight-bold mb-2 text-dark"><i class="fas fa-boxes text-primary mr-1"></i> {{ __('Multi-Store Purchase Orders:') }}</h6>
                    <p class="small text-muted mb-2">{{ __('Your checkout contained items from multiple stores. Each store order is fulfilled independently:') }}</p>
                    <div class="d-flex flex-wrap gap-2" style="gap: 8px;">
                        @foreach($siblingOrders as $sOrd)
                            <a href="{{ route('user.order.invoice', $sOrd->id) }}" class="btn btn-xs {{ $sOrd->id == $order->id ? 'btn-primary' : 'btn-outline-primary bg-white' }} py-1 px-2" style="border-radius: 6px;">
                                <i class="fas fa-store mr-1"></i> {{ $sOrd->store_name }} ({{ $sOrd->transaction_number }}) - <strong>{{ __($sOrd->order_status) }}</strong>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="row">
                <div class="col text-center">

                    <!-- Logo -->
                    <img class="img-fluid mb-5 mh-70" alt="Logo"
                        src="{{ url('/core/public/storage/images/' . $setting->logo) }}">

                </div>
            </div> <!-- / .row -->
            <div class="row">
                <div class="col-12">
                    <h5><b>{{ __('Order Details :') }}</b></h5>

                    @if(!in_array($order->payment_method, ['Easypaisa', 'JazzCash', 'Bank Transfer']))
                        <span class="text-muted">{{ __('Transaction Id :') }}</span>{{ $order->txnid }}<br>
                    @endif
                    <span class="text-muted">{{ __('Order Id :') }}</span><strong>{{ $order->transaction_number }}</strong><br>
                    <span class="text-muted">{{ __('Store / Seller :') }}</span>
                    <span class="badge badge-primary py-1 px-2 ml-1" style="font-size: 12px;"><i class="fas fa-store mr-1"></i> {{ $order->store_name }}</span><br>
                    <span class="text-muted">{{ __('Order Status :') }}</span>
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
                    <br>
                    <span class="text-muted">{{ __('Order Date :') }}</span>{{ $order->created_at->format('M d, Y') }}<br>
                    <span class="text-muted">{{ __('Payment Status :') }}</span>
                    @php
                        $isCod = in_array(strtolower(trim($order->payment_method)), ['cash on delivery', 'cod']);
                    @endphp
                    @if ($order->payment_status == 'Paid')
                        <div class="badge badge-success">
                            {{ $isCod ? __('Cash Received') : __('Paid') }}
                        </div>
                    @elseif ($order->payment_status == 'Pending')
                        <div class="badge badge-warning text-dark">
                            {{ __('Pending') }}
                        </div>
                    @else
                        <div class="badge badge-danger">
                            {{ $isCod ? __("Cash Hasn't Received") : __('Unpaid') }}
                        </div>
                    @endif
                    <br>
                    <span class="text-muted">{{ __('Payment Method :') }}</span>{{ $order->payment_method }}<br>

                    @if($order->payment_method != 'Cash On Delivery' && ($order->account_name || $order->account_number || $order->payment_screenshot || $order->bank_name || in_array($order->payment_method, ['Easypaisa', 'JazzCash', 'Bank Transfer'])))
                        @if($order->bank_name)
                            <span class="text-muted">{{__('Bank Name :')}}</span>{{$order->bank_name }}<br>
                        @endif
                        @if($order->account_name)
                            <span class="text-muted">{{__('Account Name :')}}</span>{{$order->account_name }}<br>
                        @endif
                        @if($order->account_number)
                            <span class="text-muted">{{__('Account Number :')}}</span>{{$order->account_number }}<br>
                        @endif
                        @if($order->txnid)
                            <span class="text-muted">{{__('Transaction ID :')}}</span>{{$order->txnid }}<br>
                        @endif
                        @if($order->payment_screenshot)
                            <span class="text-muted">{{__('Screenshot :')}}</span><br>
                            <a href="{{ asset('core/public/storage/receipts/'.$order->payment_screenshot) }}" target="_blank">
                                <img src="{{ asset('core/public/storage/receipts/'.$order->payment_screenshot) }}" style="max-width: 150px; border: 1px solid #ccc; margin-top: 5px;" alt="Receipt">
                            </a>
                            <br>
                        @endif
                    @endif

                    <br>
                    <br>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-6">
                    <h5>{{ __('Billing Address :') }}</h5>
                    @php
                        $bill = json_decode($order->billing_info, true) ?: [];

                    @endphp

                    <span class="text-muted">{{ __('Name') }}: </span>{{ $bill['bill_first_name'] ?? ($order->user->first_name ?? '') }}
                    {{ $bill['bill_last_name'] ?? ($order->user->last_name ?? '') }}<br>
                    <span class="text-muted">{{ __('Email') }}: </span>{{ $bill['bill_email'] ?? ($order->user->email ?? '') }}<br>
                    <span class="text-muted">{{ __('Phone') }}: </span>{{ $bill['bill_phone'] ?? ($order->user->phone ?? '') }}<br>
                    @if (!empty($bill['bill_address1']))
                        <span class="text-muted">{{ __('Address') }}: </span>{{ $bill['bill_address1'] }},
                        {{ isset($bill['bill_address2']) ? $bill['bill_address2'] : '' }}<br>
                    @endif
                    @if (!empty($bill['bill_country']))
                        <span class="text-muted">{{ __('Country') }}: </span>{{ $bill['bill_country'] }}<br>
                    @endif
                    @if (!empty($bill['bill_city']))
                        <span class="text-muted">{{ __('City') }}: </span>{{ $bill['bill_city'] }}<br>
                    @endif
                    @if (!empty($state['name']))
                        <span class="text-muted">{{ __('State') }}: </span>{{ $state['name'] }}<br>
                    @endif
                    @if (!empty($bill['bill_zip']))
                        <span class="text-muted">{{ __('Zip') }}: </span>{{ $bill['bill_zip'] }}<br>
                    @endif
                    @if (!empty($bill['bill_company']))
                        <span class="text-muted">{{ __('Company') }}: </span>{{ $bill['bill_company'] }}<br>
                    @endif


                </div>
                <div class="col-12 col-md-6">
                    <h5>{{ __('Shipping Address :') }}</h5>
                    @php
                        $ship = json_decode($order->shipping_info, true) ?: [];
                    @endphp
                    <span class="text-muted">{{ __('Name') }}: </span>{{ $ship['ship_first_name'] ?? ($bill['bill_first_name'] ?? '') }}
                    {{ $ship['ship_last_name'] ?? ($bill['bill_last_name'] ?? '') }} <br>
                    <span class="text-muted">{{ __('Email') }}: </span>{{ $ship['ship_email'] ?? ($bill['bill_email'] ?? ($order->user->email ?? '')) }}<br>
                    <span class="text-muted">{{ __('Phone') }}: </span>{{ $ship['ship_phone'] ?? ($bill['bill_phone'] ?? ($order->user->phone ?? '')) }}<br>
                    @if (!empty($ship['ship_address1']))
                        <span class="text-muted">{{ __('Address') }}: </span>{{ $ship['ship_address1'] }},
                        {{ isset($ship['ship_address2']) ? $ship['ship_address2'] : '' }}<br>
                    @endif
                    @if (!empty($ship['ship_country']))
                        <span class="text-muted">{{ __('Country') }}: </span>{{ $ship['ship_country'] }}<br>
                    @endif
                    @if (!empty($ship['ship_city']))
                        <span class="text-muted">{{ __('City') }}: </span>{{ $ship['ship_city'] }}<br>
                    @endif
                    @if (!empty($state['name']))
                        <span class="text-muted">{{ __('State') }}: </span>{{ $state['name'] }}<br>
                    @endif
                    @if (!empty($ship['ship_zip']))
                        <span class="text-muted">{{ __('Zip') }}: </span>{{ $ship['ship_zip'] }}<br>
                    @endif
                    @if (!empty($ship['ship_company']))
                        <span class="text-muted">{{ __('Company') }}: </span>{{ $ship['ship_company'] }}<br>
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
                                        <span class="h6">{{ __('Products') }}</span>
                                    </th>
                                    <th class="px-0 bg-transparent border-top-0">
                                        <span class="h6">{{ __('Attribute') }}</span>
                                    </th>
                                    <th class="px-0 bg-transparent border-top-0">
                                        <span class="h6">{{ __('Quantity') }}</span>
                                    </th>
                                    <th class="px-0 bg-transparent border-top-0 text-right">
                                        <span class="h6">{{ __('Price') }}</span>
                                    </th>
                                    <th class="px-0 bg-transparent border-top-0 text-right">
                                        <span class="h6">{{ __('Advance Discount') }}</span>
                                    </th>
                                    <th class="px-0 bg-transparent border-top-0 text-right">
                                        <span class="h6">{{ __('Delivery Charges') }}</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $option_price = 0;
                                    $total = 0;
                                    $isCod = in_array(strtolower(trim($order->payment_method ?? '')), ['cash on delivery', 'cod']);
                                @endphp
                                @foreach (json_decode($order->cart, true) as $key => $item)
                                    @php
                                        $total += $item['main_price'] * $item['qty'];
                                        $option_price += ($item['attribute_price'] ?? 0);
                                        $grandSubtotal = $total + $option_price;
                                        $itemId = explode('-', $key)[0];
                                        if (App\Models\Item::where('id', $itemId)->exists()) {
                                            $main_item = App\Models\Item::find($itemId);
                                        } else {
                                            $main_item = null;
                                        }

                                        $itemAdvDiscount = 0;
                                        if (!$isCod && $main_item && $main_item->advance_payment_amount > 0) {
                                            $item_total_price = ($item['main_price'] + ($item['attribute_price'] ?? 0)) * $item['qty'];
                                            if ($main_item->advance_payment_type == 'percentage') {
                                                $itemAdvDiscount = ($item_total_price * $main_item->advance_payment_amount) / 100;
                                            } else {
                                                $curr_val = $order->currency_value;
                                                $itemAdvDiscount = ($curr_val > 0 ? ($main_item->advance_payment_amount / $curr_val) : $main_item->advance_payment_amount) * $item['qty'];
                                            }
                                        }

                                        $itemDeliveryFee = 0;
                                        $isItemFreeDelivery = true;
                                        if ($main_item) {
                                            if ($main_item->is_free_delivery == 1) {
                                                $isItemFreeDelivery = true;
                                            } elseif ($main_item->delivery_fee > 0) {
                                                $isItemFreeDelivery = false;
                                                $itemDeliveryFee = $main_item->delivery_fee;
                                            } else {
                                                $isItemFreeDelivery = true;
                                            }
                                        }
                                    @endphp
                                    <tr>
                                        <td class="">
                                            {{ $item['name'] }}
                                            <p>
                                                @if ($main_item)
                                                    @if (($item['item_type'] ?? '') == 'digital')
                                                        @if ($order->payment_status == 'Paid')
                                                            @if ($main_item['file_type'] == 'link')
                                                                <a href="{{ $main_item->link }}" target="_blank"
                                                                    class="btn btn-sm btn-success">{{ __('Click Here') }}</a>
                                                            @else
                                                                <a href="{{ asset('assets/files/' . $main_item->file) }}"
                                                                    class="btn btn-sm btn-success">{{ __('Download') }}</a>
                                                            @endif
                                                        @endif
                                                    @endif

                                                    @if (($item['item_type'] ?? '') == 'license')
                                                        @if ($order->payment_status == 'Paid')
                                                            @if ($main_item['file_type'] == 'link')
                                                                <a href="{{ $main_item->link }}" target="_blank"
                                                                    class="btn btn-sm my-2 btn-success">{{ __('Click Here') }}</a>
                                                                <p class="py-2">{{ __('License Information') }} :
                                                                    {{ $item['item_l_n'] ?? '' }} : {{ $item['item_l_k'] ?? '' }}</p>
                                                            @else
                                                                <a href="{{ asset('assets/files/' . $main_item->file) }}"
                                                                    class="btn my-2 btn-sm btn-success">{{ __('Download') }}</a>
                                                                <p class="py-2">{{ __('License Information') }} :
                                                                    {{ $item['item_l_n'] ?? '' }} : {{ $item['item_l_k'] ?? '' }}</p>
                                                            @endif
                                                        @endif
                                                    @endif
                                                @endif
                                            </p>
                                        </td>
                                        <td class="px-0">
                                            @if (!empty($item['attribute']['option_name']))
                                                @foreach ($item['attribute']['option_name'] as $optionkey => $option_name)
                                                    <span class="entry-meta"><b>{{ $option_name }}</b> :
                                                        @php
                                                            $optPrice = $item['attribute']['option_price'][$optionkey] ?? 0;
                                                        @endphp
                                                        @if ($setting->currency_direction == 1)
                                                            {{ $order->currency_sign }}{{ round($optPrice * $order->currency_value, 2) }}
                                                        @else
                                                            {{ round($optPrice * $order->currency_value, 2) }}{{ $order->currency_sign }}
                                                        @endif

                                                    </span>
                                                @endforeach
                                            @else
                                                --
                                            @endif
                                        </td>
                                        <td class="px-0">
                                            {{ $item['qty'] }}
                                        </td>

                                        <td class="px-0 text-right">
                                            @if ($setting->currency_direction == 1)
                                                {{ $order->currency_sign }}{{ round($item['main_price'] * $order->currency_value, 2) }}
                                            @else
                                                {{ round($item['main_price'] * $order->currency_value, 2) }}{{ $order->currency_sign }}
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
                                @if ($order->tax != 0)
                                    <tr>
                                        <td class="px-0 border-top border-top-2">
                                            <span class="text-muted">{{ __('Tax') }}</span>
                                        </td>
                                        <td class="px-0 text-right border-top border-top-2" colspan="5">
                                            <span>
                                                @if ($setting->currency_direction == 1)
                                                    {{ $order->currency_sign }}{{ round($order->tax * $order->currency_value, 2) }}
                                                @else
                                                    {{ round($order->tax * $order->currency_value, 2) }}{{ $order->currency_sign }}
                                                @endif
                                            </span>
                                        </td>
                                    </tr>
                                @endif
                                @if (json_decode($order->discount, true))
                                    @php
                                        $discount = json_decode($order->discount, true);
                                    @endphp
                                    <tr>
                                        <td class="px-0 border-top border-top-2">
                                            <span class="text-muted">{{ __('Coupon discount') }}
                                                ({{ $discount['code']['code_name'] }})</span>
                                        </td>
                                        <td class="px-0 text-right border-top border-top-2" colspan="5">
                                            <span class="text-danger">
                                                @if ($setting->currency_direction == 1)
                                                    -{{ $order->currency_sign }}{{ round($discount['discount'] * $order->currency_value, 2) }}
                                                @else
                                                    -{{ round($discount['discount'] * $order->currency_value, 2) }}{{ $order->currency_sign }}
                                                @endif
                                            </span>
                                        </td>
                                    </tr>
                                @endif
                                @if (json_decode($order->shipping, true))
                                    @php
                                        $shipping = json_decode($order->shipping, true);
                                    @endphp
                                    <tr>
                                        <td class="px-0 border-top border-top-2">
                                            <span class="text-muted">{{ __('Delivery Charges') }}</span>
                                        </td>
                                        <td class="px-0 text-right border-top border-top-2" colspan="5">
                                            <span>
                                                @if (($shipping['price'] ?? 0) == 0)
                                                    <span class="text-success font-weight-bold">{{ __('Free Delivery') }}</span>
                                                @else
                                                    @if ($setting->currency_direction == 1)
                                                        {{ $order->currency_sign }}{{ round($shipping['price'] * $order->currency_value, 2) }}
                                                    @else
                                                        {{ round($shipping['price'] * $order->currency_value, 2) }}{{ $order->currency_sign }}
                                                    @endif
                                                @endif

                                            </span>
                                        </td>
                                    </tr>
                                @endif
                                @if (json_decode($order->state_price, true))
                                    <tr>
                                        <td class="px-0 border-top border-top-2">
                                            <span class="text-muted">{{ __('State Tax') }}</span>
                                        </td>
                                        <td class="px-0 text-right border-top border-top-2" colspan="5">
                                            <span>
                                                @if ($setting->currency_direction == 1)
                                                    {{ isset($state['type']) && $state['type'] == 'percentage' ? ' (' . $state['price'] . '%) ' : '' }}
                                                    {{ $order->currency_sign }}{{ round($order['state_price'] * $order->currency_value, 2) }}
                                                @else
                                                    {{ isset($state['type']) && $state['type'] == 'percentage' ? ' (' . $state['price'] . '%) ' : '' }}
                                                    {{ round($order['state_price'] * $order->currency_value, 2) }}{{ $order->currency_sign }}
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
                                            <strong>{{ __('Total amount') }}</strong>
                                        @else
                                            <strong>{{ __('Total amount due') }}</strong>
                                        @endif
                                    </td>
                                    <td class="px-0 text-right border-top border-top-2" colspan="5">
                                        <span class="h3">
                                            @if ($setting->currency_direction == 1)
                                                {{ $order->currency_sign }}{{ PriceHelper::OrderTotal($order) }}
                                            @else
                                                {{ PriceHelper::OrderTotal($order) }}{{ $order->currency_sign }}
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

@endsection
