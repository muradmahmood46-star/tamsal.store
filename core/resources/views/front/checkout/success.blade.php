@extends('master.front')

@section('title')
    {{ __('Order Success') }}
@endsection

@section('content')
    <!-- Page Content-->
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="card text-center shadow-sm border-0" style="border-radius: 14px; overflow: hidden; background: #fff;">
                    <div class="card-body p-4 p-md-5">

                        <!-- NMZ Order Success Box -->
                        <div class="nmz-order-success-box text-start">
                            <div class="nmz-success-icon">✓</div>
                            <div class="nmz-success-content text-left">
                                @php
                                    $isCod = in_array(strtolower(trim($order->payment_method ?? '')), ['cash on delivery', 'cod']);
                                @endphp

                                @if(!$isCod && ($order->payment_screenshot || in_array($order->payment_method, ['Stripe', 'Authorize.Net', 'Bank', 'Easypaisa', 'JazzCash', 'Bank Transfer'])))
                                    <h2>{{ __('Your Order Has Been Received!') }}</h2>
                                    <p class="nmz-greeting mb-2 font-weight-bold" style="font-size: 15px; color: #222;">
                                        {{ __('Dear Customer,') }}
                                    </p>
                                    <p class="mb-3 text-muted" style="font-size: 14.5px;">
                                        {{ __('Your order and payment proof have been received. We will verify your transaction details shortly.') }}
                                    </p>
                                @else
                                    <h2>{{ __('Thank You For Your Order!') }}</h2>
                                    <p class="nmz-greeting mb-2 font-weight-bold" style="font-size: 15px; color: #222;">
                                        {{ __('Dear Customer,') }}
                                    </p>
                                    <p class="mb-3 text-muted" style="font-size: 14.5px;">
                                        {{ __('Your order has been placed successfully and will be processed soon.') }}
                                    </p>
                                @endif

                                @php
                                    $checkoutRef = $order->checkout_ref ?? null;
                                    $allOrders = $checkoutRef ? \App\Models\Order::where('checkout_ref', $checkoutRef)->get() : collect([$order]);
                                @endphp

                                @if($allOrders->count() > 1)
                                    <div class="p-3 my-3 rounded" style="background: #ffffff; border: 1px dashed #8CCF00;">
                                        <p class="mb-2 text-dark font-weight-bold" style="font-size: 15px;">
                                            <i class="fas fa-boxes text-success mr-1"></i> {{ __('Your purchase has been split into :count store orders:', ['count' => $allOrders->count()]) }}
                                        </p>
                                        <div class="list-group">
                                            @foreach($allOrders as $ord)
                                                @php
                                                    $ordCart = json_decode($ord->cart, true) ?: [];
                                                    $itemsCount = 0;
                                                    foreach($ordCart as $it) { $itemsCount += ($it['qty'] ?? 1); }
                                                @endphp
                                                <div class="list-group-item d-flex flex-wrap justify-content-between align-items-center mb-2 rounded border" style="background: #f8fafc;">
                                                    <div>
                                                        <span class="badge badge-primary py-1 px-2" style="font-size: 13px;">{{ $ord->transaction_number }}</span>
                                                        <strong class="ml-2 text-dark"><i class="fas fa-store text-muted mr-1"></i> {{ $ord->store_name }}</strong>
                                                        <span class="text-muted small ml-2">({{ $itemsCount }} {{ __('item(s)') }})</span>
                                                    </div>
                                                    <div class="mt-2 mt-sm-0">
                                                        <span class="font-weight-bold text-success mr-3">
                                                            @if($setting->currency_direction == 1)
                                                                {{ $ord->currency_sign }}{{ PriceHelper::OrderTotal($ord) }}
                                                            @else
                                                                {{ PriceHelper::OrderTotal($ord) }}{{ $ord->currency_sign }}
                                                            @endif
                                                        </span>
                                                        <a href="{{ route('front.order.track') . '?order_number=' . $ord->transaction_number }}" class="btn btn-xs btn-outline-primary">
                                                            <i class="fas fa-map-marker-alt mr-1"></i> {{ __('Track') }}
                                                        </a>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <div class="p-3 my-3 rounded" style="background: #ffffff; border: 1px dashed #8CCF00;">
                                        <p class="mb-1 text-dark font-weight-bold" style="font-size: 15px;">
                                            {{ __('Make sure you make note of your order number, which is:') }} 
                                            <span class="badge badge-primary text-white py-1 px-2 ml-1" style="font-size: 14px; letter-spacing: 0.5px;">{{ $order->transaction_number }}</span>
                                        </p>
                                        <div class="mt-2">
                                            <small class="text-muted"><i class="fas fa-store mr-1"></i> {{ __('Store:') }} <strong>{{ $order->store_name }}</strong></small>
                                            <a href="{{ route('front.order.track') . '?order_number=' . $order->transaction_number }}" class="btn btn-xs btn-outline-primary ml-2">
                                                <i class="fas fa-map-marker-alt mr-1"></i> {{ __('Track Order') }}
                                            </a>
                                        </div>
                                    </div>
                                @endif

                                <p style="font-size: 14px; line-height: 1.6; margin-bottom: 8px;">
                                    {{ __('You will be receiving an email / SMS / call shortly with confirmation of your order.') }}
                                </p>

                                <p style="font-size: 14px; line-height: 1.6; margin-bottom: 12px;">
                                    <strong>{{ __('After your confirmation, each store will process and dispatch their respective products for delivery.') }}</strong>
                                </p>

                                <div class="nmz-thank-you text-center">
                                    {{ __('Thank you for shopping with us!') }} ❤️
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <a class="btn btn-primary px-4 py-2 font-weight-bold" href="{{ route('front.catalog') }}">
                                <i class="icon-package pr-2"></i> {{ __('View our products again') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
    /* NMZ Order Success Box */
    .nmz-order-success-box {
        width: 100%;
        box-sizing: border-box;
        display: flex;
        align-items: flex-start;
        gap: 15px;
        margin: 10px 0 20px 0;
        padding: 24px;
        background: #F1F4EE;
        border: 1px solid #8CCF00;
        border-left: 5px solid #8CCF00;
        border-radius: 12px;
        color: #222222;
        font-family: Arial, sans-serif;
        text-align: left;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
    }

    .nmz-success-icon {
        width: 44px;
        height: 44px;
        min-width: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #8CCF00;
        color: #ffffff;
        border-radius: 50%;
        font-size: 24px;
        font-weight: bold;
        margin-top: 2px;
    }

    .nmz-success-content {
        flex: 1;
    }

    .nmz-success-content h2 {
        margin: 0 0 8px 0;
        color: #222222;
        font-size: 21px;
        font-weight: 700;
    }

    .nmz-success-content p {
        margin: 0 0 9px 0;
        color: #333333;
        font-size: 14px;
        line-height: 1.6;
    }

    .nmz-success-content strong {
        color: #4a7700;
    }

    .nmz-thank-you {
        margin-top: 15px;
        padding-top: 12px;
        border-top: 1px solid rgba(140, 207, 0, 0.35);
        color: #4a7700;
        font-size: 15px;
        font-weight: 700;
        text-align: center;
    }

    @media (max-width: 576px) {
        .nmz-order-success-box {
            gap: 12px;
            padding: 16px;
            border-left-width: 4px;
        }

        .nmz-success-icon {
            width: 34px;
            height: 34px;
            min-width: 34px;
            font-size: 18px;
        }

        .nmz-success-content h2 {
            font-size: 18px;
        }

        .nmz-success-content p {
            font-size: 13px;
            line-height: 1.5;
        }

        .nmz-thank-you {
            font-size: 13.5px;
        }
    }
    </style>
@endsection