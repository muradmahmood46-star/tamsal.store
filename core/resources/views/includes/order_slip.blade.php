<!DOCTYPE html>
<html lang="en">
<head>
    @php $slipSetting = $setting ?? \App\Models\Setting::find(1); @endphp
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Order Slip') }} - {{ $order->transaction_number }}</title>
    @if(!empty($slipSetting->favicon))
        <link rel="icon" type="image/x-icon" href="{{ url('/core/public/storage/images/' . $slipSetting->favicon) }}?v={{ md5($slipSetting->favicon) }}">
    @endif
    <link rel="stylesheet" href="{{ asset('assets/back/css/bootstrap.min.css') }}">
    <style>
        body { font-family: sans-serif; background: #fff; padding: 20px; color: #333; }
        .invoice-box { max-width: 800px; margin: auto; padding: 20px; border: 1px solid #eee; }
        @media print { .no-print { display: none; } .invoice-box { border: 0; } }
    </style>
</head>
<body>
    @php
        $bill = json_decode($order->billing_info, true) ?: [];
        $ship = json_decode($order->shipping_info, true) ?: [];
        $deliveryName = trim(($ship['ship_first_name'] ?? $bill['bill_first_name'] ?? '') . ' ' . ($ship['ship_last_name'] ?? $bill['bill_last_name'] ?? ''));
        $deliveryAddress = $ship['ship_address1'] ?? $bill['bill_address1'] ?? '';
        $deliveryCity = $ship['ship_city'] ?? $bill['bill_city'] ?? '';
        $deliveryCountry = $ship['ship_country'] ?? $bill['bill_country'] ?? '';
        $deliveryPhone = $ship['ship_phone'] ?? $bill['bill_phone'] ?? '';
        $deliveryZip = $ship['ship_zip'] ?? $bill['bill_zip'] ?? '';
        $total = 0;
    @endphp
    <div class="invoice-box">
        <div class="no-print text-right mb-3"><button onclick="window.print()" class="btn btn-primary btn-sm">{{ __('Print Order') }}</button></div>
        <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
            <div>
                <h3>{{ $storeName }}</h3>
                @if($storeEmail || $storePhone)<p class="mb-0 text-muted">{{ $storeEmail }}{{ $storeEmail && $storePhone ? ' | ' : '' }}{{ $storePhone }}</p>@endif
            </div>
            <div class="text-right"><h5 class="text-primary font-weight-bold">{{ __('ORDER INVOICE') }}</h5><p class="mb-0"><strong>#{{ $order->transaction_number }}</strong></p><small class="text-muted">{{ $order->created_at->format('M d, Y') }}</small></div>
        </div>
        <div class="row mb-4">
            <div class="col-6">
                <h6><strong>{{ __('Customer / Delivery Address:') }}</strong></h6>
                <p class="mb-1">{{ $deliveryName ?: 'N/A' }}</p>
                <p class="mb-1">{{ $deliveryAddress ?: 'N/A' }}</p>
                <p class="mb-1">{{ trim($deliveryCity . ($deliveryCity && $deliveryCountry ? ', ' : '') . $deliveryCountry) ?: 'N/A' }}</p>
                <p class="mb-1">{{ __('ZIP / Postal Code:') }} {{ $deliveryZip ?: 'N/A' }}</p>
                <p class="mb-0">{{ __('Phone:') }} {{ $deliveryPhone ?: 'N/A' }}</p>
            </div>
            <div class="col-6 text-right"><h6><strong>{{ __('Payment Info:') }}</strong></h6><p class="mb-1">{{ __('Method:') }} <strong>{{ $order->payment_method }}</strong></p><p class="mb-0">{{ __('Status:') }} <strong>{{ $order->payment_status }}</strong></p></div>
        </div>
        <table class="table table-bordered">
            <thead class="thead-light"><tr><th>{{ __('Product') }}</th><th>{{ __('Qty') }}</th><th class="text-right">{{ __('Price') }}</th><th class="text-right">{{ __('Subtotal') }}</th></tr></thead>
            <tbody>
                @foreach($slipCart as $item)
                    @php $price = ($item['main_price'] ?? 0) + ($item['attribute_price'] ?? 0); $qty = $item['qty'] ?? 1; $sub = $price * $qty; $total += $sub; @endphp
                    <tr><td>{{ $item['name'] ?? __('Product') }}</td><td>{{ $qty }}</td><td class="text-right">{{ PriceHelper::setCurrencyPrice($price) }}</td><td class="text-right">{{ PriceHelper::setCurrencyPrice($sub) }}</td></tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr><th colspan="3" class="text-right">{{ __('Subtotal:') }}</th><th class="text-right">{{ PriceHelper::setCurrencyPrice($total) }}</th></tr>
                @php $shipData = json_decode($order->shipping, true) ?: []; $discData = json_decode($order->discount, true) ?: []; @endphp
                @if(($shipData['price'] ?? 0) > 0)<tr><th colspan="3" class="text-right">{{ __('Shipping / Delivery:') }}</th><th class="text-right">{{ PriceHelper::setCurrencyPrice($shipData['price']) }}</th></tr>@endif
                @if(($discData['discount'] ?? 0) > 0)<tr><th colspan="3" class="text-right">{{ __('Discount:') }}</th><th class="text-right">-{{ PriceHelper::setCurrencyPrice($discData['discount']) }}</th></tr>@endif
                <tr class="bg-light"><th colspan="3" class="text-right">{{ __('Grand Total:') }}</th><th class="text-right font-weight-bold text-primary">@if(isset($setting) && $setting->currency_direction == 1){{ $order->currency_sign }}{{ PriceHelper::OrderTotal($order) }}@else{{ PriceHelper::OrderTotal($order) }}{{ $order->currency_sign }}@endif</th></tr>
            </tfoot>
        </table>
    </div>
</body>
</html>
