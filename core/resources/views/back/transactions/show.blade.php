@extends('master.back')

@section('content')

<div class="container-fluid">

    <!-- Page Heading -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between">
                <h3 class="mb-0 bc-title"><b>{{ __('Transaction Details') }}</b></h3>
                <div>
                    <a href="{{ route('back.transaction.index') }}" class="btn btn-primary btn-sm"><i class="fas fa-chevron-left"></i> {{ __('Back to Transactions') }}</a>
                    <a href="javascript:window.print()" class="btn btn-secondary btn-sm"><i class="fas fa-print"></i> {{ __('Print') }}</a>
                </div>
            </div>
        </div>
    </div>

    @php
        $bill = json_decode($order->billing_info, true);
        $ship = json_decode($order->shipping_info, true);
        $isCod = in_array(strtolower(trim($order->payment_method)), ['cash on delivery', 'cod']);
        $wasCanceled = App\Models\TrackOrder::where('order_id', $order->id)->where('title', 'Canceled')->exists();
    @endphp

    <div class="row">
        <!-- Transaction Summary Card -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold"><i class="fas fa-receipt mr-1"></i> {{ __('Transaction & Order Overview') }}</h6>
                </div>
                <div class="card-body">
                    <table class="table table-bordered mb-0">
                        <tr>
                            <th class="bg-light" style="width: 40%;">{{ __('Transaction ID / Ref :') }}</th>
                            <td>
                                <strong class="text-primary font-weight-bold">
                                    {{ $order->txnid ? $order->txnid : $transaction->txn_id }}
                                </strong>
                            </td>
                        </tr>
                        <tr>
                            <th class="bg-light">{{ __('Order ID :') }}</th>
                            <td><strong>{{ $order->transaction_number }}</strong></td>
                        </tr>
                        <tr>
                            <th class="bg-light">{{ __('Transaction Date :') }}</th>
                            <td>{{ $transaction->created_at->format('M d, Y - h:i A') }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">{{ __('Total Amount :') }}</th>
                            <td>
                                <strong class="text-success" style="font-size: 16px;">
                                    @if ($setting->currency_direction == 1)
                                        {{ $transaction->currency_sign }}{{ round($transaction->amount * $transaction->currency_value, 2) }}
                                    @else
                                        {{ round($transaction->amount * $transaction->currency_value, 2) }}{{ $transaction->currency_sign }}
                                    @endif
                                </strong>
                            </td>
                        </tr>
                        <tr>
                            <th class="bg-light">{{ __('Order Status :') }}</th>
                            <td>
                                @if ($order->order_status == 'Pending')
                                    <span class="badge badge-warning text-white">{{ $wasCanceled ? __('Re-attempt (New Order)') : __('New Order') }}</span>
                                @elseif ($order->order_status == 'In Progress')
                                    <span class="badge badge-info text-white">{{ $wasCanceled ? __('Delivery in Progress (Re-attempt)') : __('Delivery in Progress') }}</span>
                                @elseif ($order->order_status == 'Delivered')
                                    <span class="badge badge-success text-white">{{ $wasCanceled ? __('Delivered (After Re-attempt)') : __('Delivered') }}</span>
                                @elseif ($order->order_status == 'Canceled')
                                    <span class="badge badge-danger text-white">{{ __('Canceled') }}</span>
                                @else
                                    <span class="badge badge-secondary text-white">{{ $order->order_status }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="bg-light">{{ __('Payment Status :') }}</th>
                            <td>
                                @if ($isCod)
                                    @if ($order->payment_status == 'Paid')
                                        <span class="badge badge-success text-white">{{ __('Cash Received') }}</span>
                                    @else
                                        <span class="badge badge-danger text-white">{{ __("Cash Hasn't Received") }}</span>
                                    @endif
                                @else
                                    @if ($order->payment_status == 'Paid')
                                        <span class="badge badge-success text-white">{{ __('Paid') }}</span>
                                    @elseif ($order->payment_status == 'Pending')
                                        <span class="badge badge-warning text-white">{{ __('Pending') }}</span>
                                    @else
                                        <span class="badge badge-danger text-white">{{ __('Unpaid') }}</span>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Payment Details & Screenshot Card -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 bg-info text-white">
                    <h6 class="m-0 font-weight-bold"><i class="fas fa-money-check-alt mr-1"></i> {{ __('Payment Details & Proof') }}</h6>
                </div>
                <div class="card-body">
                    <table class="table table-bordered mb-3">
                        <tr>
                            <th class="bg-light" style="width: 40%;">{{ __('Payment Method :') }}</th>
                            <td><strong>{{ $order->payment_method }}</strong></td>
                        </tr>
                        @if($order->bank_name)
                        <tr>
                            <th class="bg-light">{{ __('Bank / Provider Name :') }}</th>
                            <td><strong>{{ $order->bank_name }}</strong></td>
                        </tr>
                        @endif
                        @if($order->account_name)
                        <tr>
                            <th class="bg-light">{{ __('Account Holder Name :') }}</th>
                            <td><strong>{{ $order->account_name }}</strong></td>
                        </tr>
                        @endif
                        @if($order->account_number)
                        <tr>
                            <th class="bg-light">{{ __('Account Number / IBAN :') }}</th>
                            <td><strong>{{ $order->account_number }}</strong></td>
                        </tr>
                        @endif
                        @if($order->txnid)
                        <tr>
                            <th class="bg-light">{{ __('Sender Txn / Ref ID :') }}</th>
                            <td><strong>{{ $order->txnid }}</strong></td>
                        </tr>
                        @endif
                    </table>

                    <div>
                        <h6 class="font-weight-bold text-muted mb-2">{{ __('Payment Screenshot / Proof :') }}</h6>
                        @if($order->payment_screenshot)
                            <div class="text-center p-2 border rounded bg-light">
                                <a href="{{ asset('core/public/storage/receipts/'.$order->payment_screenshot) }}" target="_blank">
                                    <img src="{{ asset('core/public/storage/receipts/'.$order->payment_screenshot) }}" style="max-height: 220px; max-width: 100%; object-fit: contain; border-radius: 6px; border: 2px solid #007bff; box-shadow: 0 4px 10px rgba(0,0,0,0.12);" alt="Payment Screenshot">
                                </a>
                                <div class="mt-2">
                                    <a href="{{ asset('core/public/storage/receipts/'.$order->payment_screenshot) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-expand"></i> {{ __('Click to View Full Image') }}
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-secondary py-2 mb-0">
                                <i class="fas fa-info-circle"></i> {{ __('No payment screenshot required/uploaded for this transaction.') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Information Row -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 bg-secondary text-white">
                    <h6 class="m-0 font-weight-bold"><i class="fas fa-user mr-1"></i> {{ __('Customer & Billing Details') }}</h6>
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>{{ __('Name :') }}</strong> {{ $bill['bill_first_name'] ?? '' }} {{ $bill['bill_last_name'] ?? '' }}</p>
                    <p class="mb-1"><strong>{{ __('Email :') }}</strong> {{ $bill['bill_email'] ?? '' }}</p>
                    <p class="mb-1"><strong>{{ __('Phone :') }}</strong> {{ $bill['bill_phone'] ?? '' }}</p>
                    <p class="mb-1"><strong>{{ __('Address :') }}</strong> {{ $bill['bill_address1'] ?? '' }} {{ $bill['bill_address2'] ?? '' }}</p>
                    <p class="mb-0"><strong>{{ __('City / Zip / Country :') }}</strong> {{ $bill['bill_city'] ?? '' }}, {{ $bill['bill_zip'] ?? '' }}, {{ $bill['bill_country'] ?? '' }}</p>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 bg-secondary text-white">
                    <h6 class="m-0 font-weight-bold"><i class="fas fa-shipping-fast mr-1"></i> {{ __('Shipping Details') }}</h6>
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>{{ __('Name :') }}</strong> {{ $ship['ship_first_name'] ?? ($bill['bill_first_name'] ?? '') }} {{ $ship['ship_last_name'] ?? ($bill['bill_last_name'] ?? '') }}</p>
                    <p class="mb-1"><strong>{{ __('Email :') }}</strong> {{ $ship['ship_email'] ?? ($bill['bill_email'] ?? '') }}</p>
                    <p class="mb-1"><strong>{{ __('Phone :') }}</strong> {{ $ship['ship_phone'] ?? ($bill['bill_phone'] ?? '') }}</p>
                    <p class="mb-1"><strong>{{ __('Address :') }}</strong> {{ $ship['ship_address1'] ?? ($bill['bill_address1'] ?? '') }} {{ $ship['ship_address2'] ?? '' }}</p>
                    <p class="mb-0"><strong>{{ __('City / Zip / Country :') }}</strong> {{ $ship['ship_city'] ?? ($bill['bill_city'] ?? '') }}, {{ $ship['ship_zip'] ?? ($bill['bill_zip'] ?? '') }}, {{ $ship['ship_country'] ?? ($bill['bill_country'] ?? '') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Ordered Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-dark text-white">
            <h6 class="m-0 font-weight-bold"><i class="fas fa-boxes mr-1"></i> {{ __('Products Ordered') }}</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>{{ __('Product') }}</th>
                            <th>{{ __('Attributes') }}</th>
                            <th>{{ __('Quantity') }}</th>
                            <th>{{ __('Price') }}</th>
                            <th>{{ __('Total') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(is_array($cart))
                            @foreach ($cart as $key => $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if(isset($item['photo']))
                                                <img src="{{ asset('core/public/storage/images/'.$item['photo']) }}" alt="{{ $item['name'] }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px; margin-right: 12px;">
                                            @endif
                                            <span><strong>{{ $item['name'] }}</strong></span>
                                        </div>
                                    </td>
                                    <td>
                                        @if(isset($item['attribute']['names']) && is_array($item['attribute']['names']) && count($item['attribute']['names']) > 0)
                                            @foreach($item['attribute']['names'] as $a_index => $a_name)
                                                <span class="badge badge-light border">{{ $a_name }}: {{ $item['attribute']['option_name'][$a_index] ?? '' }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $item['qty'] }}</td>
                                    <td>
                                        @if ($setting->currency_direction == 1)
                                            {{ $order->currency_sign }}{{ round((float)$item['main_price'] * (float)$order->currency_value, 2) }}
                                        @else
                                            {{ round((float)$item['main_price'] * (float)$order->currency_value, 2) }}{{ $order->currency_sign }}
                                        @endif
                                    </td>
                                    <td>
                                        <strong>
                                            @if ($setting->currency_direction == 1)
                                                {{ $order->currency_sign }}{{ round((float)$item['price'] * (float)$item['qty'] * (float)$order->currency_value, 2) }}
                                            @else
                                                {{ round((float)$item['price'] * (float)$item['qty'] * (float)$order->currency_value, 2) }}{{ $order->currency_sign }}
                                            @endif
                                        </strong>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                    <tfoot class="bg-light">
                        @if(json_decode($order->shipping, true))
                            @php $shipping_data = json_decode($order->shipping, true); @endphp
                            @if(isset($shipping_data['price']) && (float)$shipping_data['price'] > 0)
                            <tr>
                                <td colspan="4" class="text-right"><strong>{{ __('Shipping Fee :') }}</strong></td>
                                <td>
                                    <strong>
                                        @if ($setting->currency_direction == 1)
                                            {{ $order->currency_sign }}{{ round((float)$shipping_data['price'] * (float)$order->currency_value, 2) }}
                                        @else
                                            {{ round((float)$shipping_data['price'] * (float)$order->currency_value, 2) }}{{ $order->currency_sign }}
                                        @endif
                                    </strong>
                                </td>
                            </tr>
                            @endif
                        @endif

                        @if((float)$order->tax > 0)
                        <tr>
                            <td colspan="4" class="text-right"><strong>{{ __('Tax :') }}</strong></td>
                            <td>
                                <strong>
                                    @if ($setting->currency_direction == 1)
                                        {{ $order->currency_sign }}{{ round((float)$order->tax * (float)$order->currency_value, 2) }}
                                    @else
                                        {{ round((float)$order->tax * (float)$order->currency_value, 2) }}{{ $order->currency_sign }}
                                    @endif
                                </strong>
                            </td>
                        </tr>
                        @endif

                        @if(json_decode($order->state_price, true))
                        <tr>
                            <td colspan="4" class="text-right"><strong>{{ __('State Tax :') }}</strong></td>
                            <td>
                                <strong>
                                    @if ($setting->currency_direction == 1)
                                        {{ $order->currency_sign }}{{ round((float)$order->state_price * (float)$order->currency_value, 2) }}
                                    @else
                                        {{ round((float)$order->state_price * (float)$order->currency_value, 2) }}{{ $order->currency_sign }}
                                    @endif
                                </strong>
                            </td>
                        </tr>
                        @endif

                        @if(json_decode($order->discount, true))
                            @php $discount_data = json_decode($order->discount, true); @endphp
                            @if(isset($discount_data['discount']) && (float)$discount_data['discount'] > 0)
                            <tr>
                                <td colspan="4" class="text-right text-danger"><strong>{{ __('Discount :') }}</strong></td>
                                <td class="text-danger">
                                    <strong>
                                        -@if ($setting->currency_direction == 1)
                                            {{ $order->currency_sign }}{{ round((float)$discount_data['discount'] * (float)$order->currency_value, 2) }}
                                        @else
                                            {{ round((float)$discount_data['discount'] * (float)$order->currency_value, 2) }}{{ $order->currency_sign }}
                                        @endif
                                    </strong>
                                </td>
                            </tr>
                            @endif
                        @endif

                        <tr>
                            <td colspan="4" class="text-right font-weight-bold" style="font-size: 16px;">{{ __('Grand Total :') }}</td>
                            <td class="font-weight-bold text-success" style="font-size: 16px;">
                                @if ($setting->currency_direction == 1)
                                    {{ $order->currency_sign }}{{ round((float)$order->pay_amount * (float)$order->currency_value, 2) }}
                                @else
                                    {{ round((float)$order->pay_amount * (float)$order->currency_value, 2) }}{{ $order->currency_sign }}
                                @endif
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

</div>

@endsection
