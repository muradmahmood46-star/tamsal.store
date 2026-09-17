@extends('master.seller')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between">
                <h3 class="mb-0 text-dark"><b><i class="fab fa-first-order text-primary mr-2"></i> {{ __('Manage Store Orders') }}</b></h3>
            </div>
        </div>
    </div>

    @include('alerts.alerts')

    <!-- Order Status Filter Tabs -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="btn-group flex-wrap" role="group">
                <a href="{{ route('seller.order.index') }}" class="btn btn-sm {{ !request('type') ? 'btn-primary' : 'btn-outline-primary' }}">
                    {{ __('All Orders') }}
                </a>
                <a href="{{ route('seller.order.index', ['type' => 'Pending']) }}" class="btn btn-sm {{ request('type') == 'Pending' ? 'btn-warning text-dark font-weight-bold' : 'btn-outline-warning text-dark' }}">
                    <i class="fas fa-clock mr-1"></i> {{ __('New Orders') }}
                </a>
                <a href="{{ route('seller.order.index', ['type' => 'Accepted']) }}" class="btn btn-sm {{ request('type') == 'Accepted' ? 'btn-primary' : 'btn-outline-primary' }}">
                    <i class="fas fa-check mr-1"></i> {{ __('Accepted') }}
                </a>
                <a href="{{ route('seller.order.index', ['type' => 'Send to Delivery House']) }}" class="btn btn-sm {{ request('type') == 'Send to Delivery House' ? 'text-white' : '' }}" style="{{ request('type') == 'Send to Delivery House' ? 'background-color: #6f42c1; border-color: #6f42c1;' : 'border-color: #6f42c1; color: #6f42c1;' }}">
                    <i class="fas fa-warehouse mr-1"></i> {{ __('Send to Delivery House') }}
                </a>
                <a href="{{ route('seller.order.index', ['type' => 'In Progress']) }}" class="btn btn-sm {{ request('type') == 'In Progress' ? 'btn-info' : 'btn-outline-info' }}">
                    <i class="fas fa-truck mr-1"></i> {{ __('Delivery in Progress') }}
                </a>
                <a href="{{ route('seller.order.index', ['type' => 'Delivered']) }}" class="btn btn-sm {{ request('type') == 'Delivered' ? 'btn-success' : 'btn-outline-success' }}">
                    <i class="fas fa-check-circle mr-1"></i> {{ __('Delivered Orders') }}
                </a>
                <a href="{{ route('seller.order.index', ['type' => 'Canceled']) }}" class="btn btn-sm {{ request('type') == 'Canceled' ? 'btn-danger' : 'btn-outline-danger' }}">
                    <i class="fas fa-times mr-1"></i> {{ __('Canceled Orders') }}
                </a>
            </div>
        </div>
    </div>

    <!-- Orders Table Card -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="gd-responsive-table">
                <table class="table table-bordered table-striped table-hover" style="min-width: 820px;" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>{{ __('Order ID') }}</th>
                            <th>{{ __('Customer') }}</th>
                            <th>{{ __('Total Amount') }}</th>
                            <th>{{ __('Payment') }}</th>
                            <th>{{ __('Items') }}</th>
                            <th>{{ __('Order Status') }}</th>
                            <th>{{ __('Payment Status') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($datas as $order)
                            @php
                                $bill = json_decode($order->billing_info, true);
                                $cart = json_decode($order->cart, true);
                                $sellerItemsCount = 0;
                                if (is_array($cart)) {
                                    foreach ($cart as $k => $it) {
                                        $sellerItemsCount += ($it['qty'] ?? 1);
                                    }
                                }
                                $sellerProfile = Auth::user() ? Auth::user()->seller : null;
                            @endphp

                            @if($order->is_locked == 1)
                                {{-- LOCKED ORDER ROW (INSUFFICIENT BALANCE) --}}
                                <tr class="table-warning border-warning" style="background: #fffbeb !important; border-left: 5px solid #dc3545 !important;">
                                    <td colspan="9" class="p-3">
                                        <div class="d-flex flex-wrap align-items-center justify-content-between">
                                            <div class="d-flex align-items-center mb-2 mb-md-0">
                                                <span class="badge badge-danger p-2 mr-3 font-weight-bold shadow-sm" style="font-size: 12px; border-radius: 6px;">
                                                    <i class="fas fa-lock mr-1"></i> {{ __('LOCKED') }} ({{ $order->transaction_number }})
                                                </span>
                                                <span class="font-weight-bold text-dark" style="font-size: 14.5px;">
                                                    <i class="fas fa-exclamation-triangle text-warning mr-1"></i>
                                                    {{ __('You have received an order but have insufficient balance.') }}
                                                </span>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <a href="{{ route('seller.wallet.index') }}" class="btn btn-success btn-sm font-weight-bold shadow-sm mr-2" style="border-radius: 6px; padding: 6px 14px;">
                                                    <i class="fas fa-plus-circle mr-1"></i> {{ __('Add Balance') }}
                                                </a>
                                                <button type="button" class="btn btn-outline-dark btn-sm font-weight-bold" style="border-radius: 6px; padding: 6px 14px;" onclick="openLockedOrderModal('{{ $order->transaction_number }}', '{{ number_format($order->commission_amount, 2) }}', '{{ number_format($sellerProfile->balance ?? 0, 2) }}', '{{ route('seller.order.invoice', $order->id) }}')">
                                                    <i class="fas fa-eye mr-1"></i> {{ __('View Order') }}
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @else
                                {{-- NORMAL UNLOCKED ORDER ROW --}}
                                <tr>
                                    <td><strong>{{ $order->transaction_number }}</strong></td>
                                    <td>
                                        <strong>{{ $bill['bill_first_name'] ?? ($order->user->first_name ?? 'Customer') }} {{ $bill['bill_last_name'] ?? '' }}</strong>
                                        <div class="small text-muted">{{ $bill['bill_phone'] ?? ($order->user->phone ?? '') }}</div>
                                    </td>
                                    <td>
                                        <strong class="text-primary font-weight-bold">
                                            @if(isset($setting) && $setting->currency_direction == 1)
                                                {{ $order->currency_sign }}{{ PriceHelper::OrderTotal($order) }}
                                            @else
                                                {{ PriceHelper::OrderTotal($order) }}{{ $order->currency_sign }}
                                            @endif
                                        </strong>
                                    </td>
                                    <td>
                                        <span class="badge badge-light border">{{ $order->payment_method }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $sellerItemsCount }} {{ __('item(s)') }}</span>
                                    </td>
                                    <td>
                                        @if($order->order_status == 'Delivered')
                                            <span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i>{{ __('Delivered') }}</span>
                                        @elseif($order->order_status == 'In Progress')
                                            <span class="badge badge-info"><i class="fas fa-truck mr-1"></i>{{ __('In Progress') }}</span>
                                        @elseif($order->order_status == 'Send to Delivery House')
                                            <span class="badge" style="background-color: #6f42c1; color: #fff;"><i class="fas fa-warehouse mr-1"></i>{{ __('Send to Delivery House') }}</span>
                                        @elseif($order->order_status == 'Accepted')
                                            <span class="badge badge-primary"><i class="fas fa-check mr-1"></i>{{ __('Accepted') }}</span>
                                        @elseif($order->order_status == 'Canceled')
                                            <span class="badge badge-danger"><i class="fas fa-ban mr-1"></i>{{ __('Canceled') }}</span>
                                        @else
                                            <span class="badge badge-warning text-dark"><i class="fas fa-clock mr-1"></i>{{ __('Pending') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($order->payment_status == 'Paid')
                                            <span class="badge badge-success">{{ __('Paid') }}</span>
                                        @elseif($order->payment_status == 'Pending')
                                            <span class="badge badge-warning text-dark">{{ __('Pending') }}</span>
                                        @else
                                            <span class="badge badge-danger">{{ __('Unpaid') }}</span>
                                        @endif
                                    </td>
                                    <td><small>{{ $order->created_at->format('M d, Y h:i A') }}</small></td>
                                    <td>
                                        <a href="{{ route('seller.order.invoice', $order->id) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye mr-1"></i> {{ __('View') }}
                                        </a>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    <i class="fas fa-shopping-bag fa-3x mb-2 d-block"></i>
                                    {{ __('No orders found.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center justify-content-md-end mt-3 flex-wrap">
                {{ $datas->links() }}
            </div>
        </div>
    </div>
</div>

<!-- LOCKED ORDER MODAL -->
<div class="modal fade" id="lockedOrderModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0" style="border-radius: 12px;">
            <div class="modal-header bg-danger text-white py-3">
                <h5 class="modal-title text-white font-weight-bold"><i class="fas fa-lock mr-2"></i> {{ __('Order Locked (Insufficient Balance)') }}</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center bg-light text-danger mb-3 shadow-sm" style="width: 70px; height: 70px; font-size: 32px;">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h5 class="font-weight-bold text-dark mb-2" id="lockedModalTxn"></h5>
                <p class="text-muted small mb-3">
                    {{ __('This order is currently locked because your wallet balance is insufficient to cover the platform commission deduction.') }}
                </p>

                <div class="p-3 bg-light rounded border mb-3 text-left">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">{{ __('Required Commission:') }}</span>
                        <strong class="text-danger" id="lockedModalCommission"></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-0">
                        <span class="text-muted">{{ __('Your Current Balance:') }}</span>
                        <strong class="text-dark" id="lockedModalBalance"></strong>
                    </div>
                </div>

                <p class="small text-info mb-0 font-weight-bold">
                    <i class="fas fa-magic mr-1"></i> {{ __('Once you top up your wallet balance, this order will automatically unlock and full customer details will become accessible.') }}
                </p>
            </div>
            <div class="modal-footer bg-light d-flex justify-content-between">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">{{ __('Close') }}</button>
                <div>
                    <a id="lockedModalInvoiceBtn" href="" class="btn btn-outline-info btn-sm mr-1"><i class="fas fa-file-invoice mr-1"></i> {{ __('Invoice Preview') }}</a>
                    <a href="{{ route('seller.wallet.index') }}" class="btn btn-success btn-sm font-weight-bold"><i class="fas fa-plus-circle mr-1"></i> {{ __('Add Balance Now') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function openLockedOrderModal(txnNo, commission, balance, invoiceUrl) {
        document.getElementById('lockedModalTxn').innerText = 'Order: ' + txnNo;
        document.getElementById('lockedModalCommission').innerText = '{{ PriceHelper::adminCurrency() }} ' + commission;
        document.getElementById('lockedModalBalance').innerText = '{{ PriceHelper::adminCurrency() }} ' + balance;
        document.getElementById('lockedModalInvoiceBtn').href = invoiceUrl;
        $('#lockedOrderModal').modal('show');
    }
</script>
@endsection
