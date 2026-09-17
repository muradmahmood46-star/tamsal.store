@extends('master.seller')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between">
                <h3 class="mb-0 text-dark"><b><i class="fas fa-file-invoice text-primary mr-2"></i> {{ __('Order Details & Invoice') }}</b></h3>
                <div>
                    <a class="btn btn-primary btn-sm" href="{{ route('seller.order.index') }}"><i class="fas fa-chevron-left mr-1"></i> {{ __('Back to Orders') }}</a>
                    <a class="btn btn-secondary btn-sm" href="{{ route('seller.order.print', $order->id) }}" target="_blank"><i class="fas fa-print mr-1"></i> {{ __('Print Slip') }}</a>
                </div>
            </div>
        </div>
    </div>

    @include('alerts.alerts')

    @php
        $bill = json_decode($order->billing_info, true);
        $ship = json_decode($order->shipping_info, true);
        $customerName = trim(($bill['bill_first_name'] ?? ($order->user->first_name ?? '')) . ' ' . ($bill['bill_last_name'] ?? ($order->user->last_name ?? '')));
    @endphp

    @if($order->is_locked == 1)
        <div class="alert alert-danger shadow-sm mb-4 border-left border-danger" style="border-left-width: 5px !important; border-radius: 8px;">
            <div class="d-flex flex-wrap align-items-center justify-content-between">
                <div class="d-flex align-items-center mb-2 mb-md-0">
                    <i class="fas fa-lock fa-2x mr-3 text-danger"></i>
                    <div>
                        <h6 class="mb-1 font-weight-bold text-danger">{{ __('This Order is Locked (Insufficient Balance)') }}</h6>
                        <p class="mb-0 text-dark small">
                            {{ __('Required Commission:') }} <strong class="text-danger">{{ PriceHelper::adminCurrency() }} {{ number_format($order->commission_amount, 2) }}</strong>.
                            {{ __('Order processing actions are locked until you top up your wallet balance. Full customer details and fulfillment actions will be unlocked automatically upon deposit approval.') }}
                        </p>
                    </div>
                </div>
                <a href="{{ route('seller.wallet.index') }}" class="btn btn-success btn-sm font-weight-bold shadow-sm px-3 py-2">
                    <i class="fas fa-plus-circle mr-1"></i> {{ __('Add Balance to Unlock') }}
                </a>
            </div>
        </div>
    @endif

    <div class="row">
        <!-- Order & Customer Summary -->
        <div class="col-12 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="m-0 font-weight-bold text-dark"><i class="fas fa-file-invoice text-primary mr-2"></i> {{ __('Order Summary & Customer Information') }}</h5>
                </div>
                <div class="card-body p-3 p-md-4">
                    <div class="row">
                        <!-- Order Details Box -->
                        <div class="col-12 col-md-6 mb-3 mb-md-0">
                            <div class="p-3 bg-light rounded border h-100">
                                <h6 class="font-weight-bold text-primary border-bottom pb-2 mb-3">
                                    <i class="fas fa-info-circle mr-1"></i> {{ __('Order Information') }}
                                </h6>
                                <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                    <span class="text-muted">{{ __('Order Number:') }}</span>
                                    <strong class="text-dark">{{ $order->transaction_number }}</strong>
                                </div>
                                <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                    <span class="text-muted">{{ __('Order Date:') }}</span>
                                    <strong>{{ $order->created_at->format('M d, Y h:i A') }}</strong>
                                </div>
                                <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                    <span class="text-muted">{{ __('Payment Method:') }}</span>
                                    <span class="badge badge-light border font-weight-bold">{{ $order->payment_method }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                    <span class="text-muted">{{ __('Order Status:') }}</span>
                                    @if($order->is_locked == 1)
                                        <span class="badge badge-danger"><i class="fas fa-lock mr-1"></i>{{ __('Locked') }}</span>
                                    @elseif($order->order_status == 'Delivered')
                                        <span class="badge badge-success">{{ __('Delivered') }}</span>
                                    @elseif($order->order_status == 'In Progress')
                                        <span class="badge badge-info">{{ __('In Progress') }}</span>
                                    @elseif($order->order_status == 'Send to Delivery House')
                                        <span class="badge badge-purple" style="background-color: #6f42c1; color: #fff;">{{ __('Send to Delivery House') }}</span>
                                    @elseif($order->order_status == 'Accepted')
                                        <span class="badge badge-primary">{{ __('Accepted') }}</span>
                                    @elseif($order->order_status == 'Canceled')
                                        <span class="badge badge-danger">{{ __('Canceled') }}</span>
                                    @else
                                        <span class="badge badge-warning text-dark">{{ __('Pending') }}</span>
                                    @endif
                                </div>
                                <div class="d-flex justify-content-between align-items-center pt-1">
                                    <span class="text-muted">{{ __('Payment Status:') }}</span>
                                    @if($order->payment_status == 'Paid')
                                        <span class="badge badge-success">{{ __('Paid') }}</span>
                                    @else
                                        <span class="badge badge-warning text-dark">{{ $order->payment_status }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Customer Shipping / Contact Info Box -->
                        <div class="col-12 col-md-6">
                            <div class="p-3 bg-light rounded border h-100">
                                <h6 class="font-weight-bold text-primary border-bottom pb-2 mb-3">
                                    <i class="fas fa-map-marker-alt mr-1"></i> {{ __('Delivery / Customer Address') }}
                                </h6>
                                @if($order->is_locked == 1)
                                    <div class="p-3 bg-white rounded text-muted border text-center my-3">
                                        <i class="fas fa-lock mr-1 text-danger"></i> <em>{{ __('Customer address and contact details are masked until balance is topped up.') }}</em>
                                    </div>
                                @else
                                    <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                        <span class="text-muted">{{ __('Customer Name:') }}</span>
                                        <strong class="text-dark">{{ $customerName ?: 'Customer' }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                        <span class="text-muted">{{ __('Phone:') }}</span>
                                        <strong><i class="fas fa-phone mr-1 text-muted"></i> {{ $ship['ship_phone'] ?? ($bill['bill_phone'] ?? 'N/A') }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                        <span class="text-muted">{{ __('Email:') }}</span>
                                        <span>{{ $ship['ship_email'] ?? ($bill['bill_email'] ?? 'N/A') }}</span>
                                    </div>
                                    <div class="pt-2">
                                        <span class="text-muted d-block small mb-1">{{ __('Full Address:') }}</span>
                                        <strong class="text-dark">
                                            <i class="fas fa-home mr-1 text-muted"></i>
                                            {{ $ship['ship_address1'] ?? ($bill['bill_address1'] ?? '') }},
                                            {{ $ship['ship_city'] ?? ($bill['bill_city'] ?? '') }},
                                            {{ $ship['ship_country'] ?? ($bill['bill_country'] ?? '') }}
                                        </strong>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Status Update Actions for Seller -->
                    <div class="border-top pt-3 mt-3 bg-white p-3 rounded border">
                        <h6 class="font-weight-bold text-dark mb-2"><i class="fas fa-tasks text-primary mr-1"></i> {{ __('Update Order Status:') }}</h6>
                        @if($order->is_locked == 1)
                            <div class="d-flex align-items-center text-danger font-weight-bold py-2">
                                <i class="fas fa-lock mr-2"></i> {{ __('Actions are disabled because this order is locked due to insufficient wallet balance.') }}
                                <a href="{{ route('seller.wallet.index') }}" class="btn btn-success btn-sm ml-3 font-weight-bold"><i class="fas fa-plus-circle mr-1"></i> {{ __('Add Balance') }}</a>
                            </div>
                        @else
                            <div class="btn-group flex-wrap">
                            @if($order->order_status == 'Pending')
                                <a href="{{ route('seller.order.status', [$order->id, 'order_status', 'Accepted']) }}" class="btn btn-primary btn-sm mr-2 mb-2 font-weight-bold">
                                    <i class="fas fa-check mr-1"></i> {{ __('Accept Order') }}
                                </a>
                                <a href="{{ route('seller.order.status', [$order->id, 'order_status', 'Canceled']) }}" class="btn btn-danger btn-sm mb-2" onclick="return confirm('Are you sure you want to cancel this order?')">
                                    <i class="fas fa-times-circle mr-1"></i> {{ __('Mark as Canceled') }}
                                </a>
                            @elseif($order->order_status == 'Accepted')
                                <a href="{{ route('seller.order.status', [$order->id, 'order_status', 'Send to Delivery House']) }}" class="btn btn-sm mr-2 mb-2 font-weight-bold" style="background-color: #6f42c1; color: #fff;">
                                    <i class="fas fa-warehouse mr-1"></i> {{ __('Send to Delivery House') }}
                                </a>
                                <a href="{{ route('seller.order.status', [$order->id, 'order_status', 'Canceled']) }}" class="btn btn-danger btn-sm mb-2" onclick="return confirm('Are you sure you want to cancel this order?')">
                                    <i class="fas fa-times-circle mr-1"></i> {{ __('Mark as Canceled') }}
                                </a>
                            @elseif($order->order_status == 'Send to Delivery House')
                                <a href="{{ route('seller.order.status', [$order->id, 'order_status', 'In Progress']) }}" class="btn btn-info btn-sm mr-2 mb-2 font-weight-bold">
                                    <i class="fas fa-truck mr-1"></i> {{ __('Mark as In Progress (Dispatched)') }}
                                </a>
                                <a href="{{ route('seller.order.status', [$order->id, 'order_status', 'Canceled']) }}" class="btn btn-danger btn-sm mb-2" onclick="return confirm('Are you sure you want to cancel this order?')">
                                    <i class="fas fa-times-circle mr-1"></i> {{ __('Mark as Canceled') }}
                                </a>
                            @elseif($order->order_status == 'In Progress')
                                <a href="{{ route('seller.order.status', [$order->id, 'order_status', 'Delivered']) }}" class="btn btn-success btn-sm mr-2 mb-2 font-weight-bold">
                                    <i class="fas fa-check-circle mr-1"></i> {{ __('Mark as Delivered') }}
                                </a>
                                <a href="{{ route('seller.order.status', [$order->id, 'order_status', 'Canceled']) }}" class="btn btn-danger btn-sm mb-2" onclick="return confirm('Are you sure you want to cancel this order?')">
                                    <i class="fas fa-times-circle mr-1"></i> {{ __('Mark as Canceled') }}
                                </a>
                            @elseif($order->order_status == 'Delivered')
                                <span class="badge badge-success p-2 mr-2 mb-2"><i class="fas fa-check-circle mr-1"></i> {{ __('Order Delivered') }}</span>
                                <a href="{{ route('seller.order.status', [$order->id, 'order_status', 'In Progress']) }}" class="btn btn-outline-info btn-sm mb-2">
                                    <i class="fas fa-undo mr-1"></i> {{ __('Move back to In Progress') }}
                                </a>
                            @elseif($order->order_status == 'Canceled')
                                <span class="badge badge-danger p-2 mr-2 mb-2"><i class="fas fa-ban mr-1"></i> {{ __('Order Canceled') }}</span>
                                <a href="{{ route('seller.order.status', [$order->id, 'order_status', 'Pending']) }}" class="btn btn-warning btn-sm mb-2 font-weight-bold">
                                    <i class="fas fa-redo-alt mr-1"></i> {{ __('Add to Order Again') }}
                                </a>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Seller Products Table -->
        <div class="col-lg-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light py-3">
                    <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-shopping-cart mr-1"></i> {{ __('Your Store Products in this Order') }}</h6>
                </div>
                <div class="card-body p-0">
                    <div class="gd-responsive-table">
                        <table class="table table-bordered table-hover mb-0" style="min-width: 850px;" width="100%">
                            <thead class="thead-light">
                                <tr>
                                    <th>{{ __('Product') }}</th>
                                    <th>{{ __('Attributes / Options') }}</th>
                                    <th class="text-center">{{ __('Quantity') }}</th>
                                    <th class="text-right">{{ __('Unit Price') }}</th>
                                    <th class="text-right">{{ __('Subtotal') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $runningTotal = 0;
                                @endphp
                                @foreach($sellerCart as $key => $item)
                                    @php
                                        $price = $item['main_price'] ?? 0;
                                        $attrPrice = $item['attribute_price'] ?? 0;
                                        $qty = $item['qty'] ?? 1;
                                        $lineSubtotal = ($price + $attrPrice) * $qty;
                                        $runningTotal += $lineSubtotal;
                                    @endphp
                                    <tr>
                                        <td>
                                            <strong>{{ $item['name'] }}</strong>
                                        </td>
                                        <td>
                                            @if(!empty($item['attribute']['option_name']))
                                                @foreach($item['attribute']['option_name'] as $k => $opName)
                                                    <span class="badge badge-secondary mr-1">{{ $opName }}</span>
                                                @endforeach
                                            @else
                                                <span class="text-muted small">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center font-weight-bold">{{ $qty }}</td>
                                        <td class="text-right">{{ PriceHelper::setCurrencyPrice($price + $attrPrice) }}</td>
                                        <td class="text-right font-weight-bold text-primary">{{ PriceHelper::setCurrencyPrice($lineSubtotal) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-right font-weight-bold">{{ __('Items Subtotal:') }}</td>
                                    <td class="text-right font-weight-bold">{{ PriceHelper::setCurrencyPrice($runningTotal) }}</td>
                                </tr>
                                @if($order->shipping)
                                    @php
                                        $shipData = json_decode($order->shipping, true);
                                    @endphp
                                    @if(isset($shipData['price']) && $shipData['price'] > 0)
                                        <tr>
                                            <td colspan="4" class="text-right">{{ __('Delivery / Shipping Fee:') }}</td>
                                            <td class="text-right font-weight-bold">{{ PriceHelper::setCurrencyPrice($shipData['price']) }}</td>
                                        </tr>
                                    @endif
                                @endif
                                @if($order->discount)
                                    @php
                                        $discData = json_decode($order->discount, true);
                                    @endphp
                                    @if(isset($discData['discount']) && $discData['discount'] > 0)
                                        <tr>
                                            <td colspan="4" class="text-right text-danger">{{ __('Discount:') }}</td>
                                            <td class="text-right font-weight-bold text-danger">-{{ PriceHelper::setCurrencyPrice($discData['discount']) }}</td>
                                        </tr>
                                    @endif
                                @endif
                                <tr class="bg-light">
                                    <td colspan="4" class="text-right font-weight-bold h5 mb-0">{{ __('Order Total:') }}</td>
                                    <td class="text-right font-weight-bold text-success h5 mb-0">
                                        @if(isset($setting) && $setting->currency_direction == 1)
                                            {{ $order->currency_sign }}{{ PriceHelper::OrderTotal($order) }}
                                        @else
                                            {{ PriceHelper::OrderTotal($order) }}{{ $order->currency_sign }}
                                        @endif
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
