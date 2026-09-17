<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ __('Order Slip') }} - {{ $order->transaction_number }}</title>
    <link rel="stylesheet" href="{{ asset('assets/back/css/bootstrap.min.css') }}">
    <style>
        body { font-family: sans-serif; background: #fff; padding: 20px; color: #333; }
        .invoice-box { max-width: 800px; margin: auto; padding: 20px; border: 1px solid #eee; }
        @media print {
            .no-print { display: none; }
            .invoice-box { border: 0; }
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <div class="no-print text-right mb-3">
            <button onclick="window.print()" class="btn btn-primary btn-sm"><i class="fas fa-print"></i> {{ __('Print Order') }}</button>
        </div>

        <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
            <div>
                <h3>{{ (Auth::user() && Auth::user()->seller) ? Auth::user()->seller->shop_name : ($order->seller ? $order->seller->shop_name : 'Seller Store') }}</h3>
                <p class="mb-0 text-muted">{{ Auth::user() ? Auth::user()->email : ($order->seller ? $order->seller->email : '') }} {{ (Auth::user() && Auth::user()->phone) ? '| ' . Auth::user()->phone : '' }}</p>
            </div>
            <div class="text-right">
                <h5 class="text-primary font-weight-bold">{{ __('ORDER INVOICE') }}</h5>
                <p class="mb-0"><strong>#{{ $order->transaction_number }}</strong></p>
                <small class="text-muted">{{ $order->created_at->format('M d, Y') }}</small>
            </div>
        </div>

        @php
            $bill = json_decode($order->billing_info, true);
            $ship = json_decode($order->shipping_info, true);
        @endphp

        <div class="row mb-4">
            <div class="col-6">
                <h6><strong>{{ __('Customer / Delivery Address:') }}</strong></h6>
                <p class="mb-1">{{ $bill['bill_first_name'] ?? '' }} {{ $bill['bill_last_name'] ?? '' }}</p>
                <p class="mb-1">{{ $ship['ship_address1'] ?? ($bill['bill_address1'] ?? '') }}</p>
                <p class="mb-1">{{ $ship['ship_city'] ?? ($bill['bill_city'] ?? '') }}, {{ $ship['ship_country'] ?? ($bill['bill_country'] ?? '') }}</p>
                <p class="mb-0">{{ __('Phone:') }} {{ $ship['ship_phone'] ?? ($bill['bill_phone'] ?? '') }}</p>
            </div>
            <div class="col-6 text-right">
                <h6><strong>{{ __('Payment Info:') }}</strong></h6>
                <p class="mb-1">{{ __('Method:') }} <strong>{{ $order->payment_method }}</strong></p>
                <p class="mb-0">{{ __('Status:') }} <strong>{{ $order->payment_status }}</strong></p>
            </div>
        </div>

        <table class="table table-bordered">
            <thead class="thead-light">
                <tr>
                    <th>{{ __('Product') }}</th>
                    <th>{{ __('Qty') }}</th>
                    <th class="text-right">{{ __('Price') }}</th>
                    <th class="text-right">{{ __('Subtotal') }}</th>
                </tr>
            </thead>
            <tbody>
                @php $total = 0; @endphp
                @foreach($sellerCart as $item)
                    @php
                        $price = ($item['main_price'] ?? 0) + ($item['attribute_price'] ?? 0);
                        $qty = $item['qty'] ?? 1;
                        $sub = $price * $qty;
                        $total += $sub;
                    @endphp
                    <tr>
                        <td>{{ $item['name'] }}</td>
                        <td>{{ $qty }}</td>
                        <td class="text-right">{{ PriceHelper::setCurrencyPrice($price) }}</td>
                        <td class="text-right">{{ PriceHelper::setCurrencyPrice($sub) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" class="text-right">{{ __('Subtotal:') }}</th>
                    <th class="text-right">{{ PriceHelper::setCurrencyPrice($total) }}</th>
                </tr>
                @if($order->shipping)
                    @php
                        $shipData = json_decode($order->shipping, true);
                    @endphp
                    @if(isset($shipData['price']) && $shipData['price'] > 0)
                        <tr>
                            <th colspan="3" class="text-right">{{ __('Shipping / Delivery:') }}</th>
                            <th class="text-right">{{ PriceHelper::setCurrencyPrice($shipData['price']) }}</th>
                        </tr>
                    @endif
                @endif
                @if($order->discount)
                    @php
                        $discData = json_decode($order->discount, true);
                    @endphp
                    @if(isset($discData['discount']) && $discData['discount'] > 0)
                        <tr>
                            <th colspan="3" class="text-right">{{ __('Discount:') }}</th>
                            <th class="text-right">-{{ PriceHelper::setCurrencyPrice($discData['discount']) }}</th>
                        </tr>
                    @endif
                @endif
                <tr class="bg-light">
                    <th colspan="3" class="text-right">{{ __('Grand Total:') }}</th>
                    <th class="text-right font-weight-bold text-primary">
                        @if(isset($setting) && $setting->currency_direction == 1)
                            {{ $order->currency_sign }}{{ PriceHelper::OrderTotal($order) }}
                        @else
                            {{ PriceHelper::OrderTotal($order) }}{{ $order->currency_sign }}
                        @endif
                    </th>
                </tr>
            </tfoot>
        </table>
    </div>
</body>
</html>
