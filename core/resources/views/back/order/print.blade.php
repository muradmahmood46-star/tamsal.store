<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <link rel="icon"  type="image/x-icon" href="{{ url('/core/public/storage/images/'.$setting->favicon) }}?v={{ !empty($setting->favicon) ? md5($setting->favicon) : time() }}"/>

  <title>{{ $setting->title }}</title>
<!-- Bootstrap -->
<link href="{{ asset('assets/front/css/bootstrap.min.css') }}" rel="stylesheet">

<link href="{{ asset('assets/front/css/fontawesome.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/front/css/magnific-popup.css') }}" rel="stylesheet">
<link href="{{ asset('assets/front/css/jquery-ui.css') }}" rel="stylesheet">

<link href="{{ asset('assets/front/css/animate.css') }}" rel="stylesheet">
<link href="{{ asset('assets/front/css/owl.carousel.min.css') }}" rel="stylesheet">

@yield('css')

<!-- Main css -->
<link href="{{ asset('assets/front/css/main.css') }}" rel="stylesheet">
</head>

<body id="invoice-print" onload="window.print()" id="page-top">

  <!-- Page Wrapper -->
  <div id="wrapper">


    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

      <!-- Main Content -->
      <div id="content">

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
                            @endphp
                            <div class="row">
                                <div class="col-12">
                                    <h5><b>{{__('Order Details :')}}</b></h5>

                                    <span class="text-muted">{{__('Customer Name :')}}</span> <strong>{{ $customerName ?: ($order->account_name ?? 'N/A') }}</strong><br>
                                    <span class="text-muted">{{__('Order Id :')}}</span> <strong>{{$order->transaction_number}}</strong><br>
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
                                        @if($order->payment_screenshot)
                                            <span class="text-muted">{{__('Payment Screenshot :')}}</span><br>
                                            <a href="{{ asset('core/public/storage/receipts/'.$order->payment_screenshot) }}" target="_blank">
                                                <img src="{{ asset('core/public/storage/receipts/'.$order->payment_screenshot) }}" style="max-width: 180px; border-radius: 6px; border: 1px solid #ccc; margin-top: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);" alt="Receipt">
                                            </a>
                                            <br>
                                        @endif
                                    @else
                                        @if($order->txnid)
                                            <span class="text-muted">{{__('Transaction Id :')}}</span> <strong>{{$order->txnid}}</strong><br>
                                        @endif
                                    @endif

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
      <!-- End of Main Content -->

    </div>
    <!-- End of Content Wrapper -->

  </div>
  <!-- End of Page Wrapper -->

  <!-- Scroll to Top Button-->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>

</body>

</html>

