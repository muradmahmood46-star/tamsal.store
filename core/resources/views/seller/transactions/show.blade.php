@extends('master.seller')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between">
                <h3 class="mb-0 text-dark"><b><i class="fas fa-receipt text-primary mr-2"></i> {{ __('Transaction Details') }}</b></h3>
                <a class="btn btn-primary btn-sm" href="{{ route('seller.transaction.index') }}"><i class="fas fa-chevron-left mr-1"></i> {{ __('Back to Transactions') }}</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white py-3">
                    <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-info-circle mr-1"></i> {{ __('Transaction Information') }}</h6>
                </div>
                <div class="card-body p-4">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>{{ __('Transaction ID / TRX:') }}</strong> <span class="badge badge-dark">{{ $transaction->txn_id }}</span></p>
                            <p class="mb-1"><strong>{{ __('Order Number:') }}</strong> #{{ $order->transaction_number }}</p>
                            <p class="mb-1"><strong>{{ __('Transaction Date:') }}</strong> {{ $transaction->created_at ? $transaction->created_at->format('M d, Y h:i A') : '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>{{ __('Payment Method:') }}</strong> {{ $order->payment_method }}</p>
                            <p class="mb-1"><strong>{{ __('Customer Email:') }}</strong> {{ $transaction->email ?: 'N/A' }}</p>
                            <p class="mb-1"><strong>{{ __('Order Status:') }}</strong> <span class="badge badge-success">{{ $order->order_status }}</span></p>
                        </div>
                    </div>

                    <h6 class="font-weight-bold text-dark border-bottom pb-2 mb-3">{{ __('Your Store Items in this Transaction:') }}</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>{{ __('Product') }}</th>
                                    <th class="text-center">{{ __('Qty') }}</th>
                                    <th class="text-right">{{ __('Price') }}</th>
                                    <th class="text-right">{{ __('Subtotal') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sellerCart as $item)
                                    @php
                                        $price = ($item['main_price'] ?? 0) + ($item['attribute_price'] ?? 0);
                                        $qty = $item['qty'] ?? 1;
                                    @endphp
                                    <tr>
                                        <td><strong>{{ $item['name'] }}</strong></td>
                                        <td class="text-center">{{ $qty }}</td>
                                        <td class="text-right">{{ PriceHelper::setCurrencyPrice($price) }}</td>
                                        <td class="text-right font-weight-bold">{{ PriceHelper::setCurrencyPrice($price * $qty) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-right">{{ __('Your Store Subtotal:') }}</th>
                                    <th class="text-right text-success h5">{{ PriceHelper::setCurrencyPrice($sellerSubtotal) }}</th>
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
