@extends('master.seller')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between">
                <div>
                    <h3 class="mb-0 text-dark"><b><i class="fas fa-receipt text-primary mr-2"></i> {{ __('Transaction Details') }}</b></h3>
                    <small class="text-muted">{{ __('Reference #') }}: VT-{{ $vTxn->id }}</small>
                </div>
                <a class="btn btn-primary btn-sm" href="{{ route('seller.transaction.index') }}">
                    <i class="fas fa-chevron-left mr-1"></i> {{ __('Back to Transactions') }}
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="{{ $vTxn->depositRequest ? 'col-lg-7' : 'col-12' }}">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-info-circle mr-1"></i> {{ __('Transaction Information') }}</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th style="width: 35%;">{{ __('Transaction Type') }}</th>
                                <td>
                                    @if($vTxn->type === 'deposit')
                                        <span class="badge badge-success px-2 py-1"><i class="fas fa-plus-circle mr-1"></i> {{ __('Add Balance (Deposit)') }}</span>
                                    @elseif($vTxn->type === 'commission_deduction')
                                        <span class="badge badge-danger px-2 py-1"><i class="fas fa-minus-circle mr-1"></i> {{ __('Commission Deduction') }}</span>
                                    @else
                                        <span class="badge badge-secondary px-2 py-1">{{ ucfirst($vTxn->type) }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>{{ __('Amount') }}</th>
                                <td>
                                    @if($vTxn->amount > 0)
                                        <span class="font-weight-bold text-success h5 mb-0">+ {{ PriceHelper::formatPrice($vTxn->amount) }}</span>
                                    @else
                                        <span class="font-weight-bold text-danger h5 mb-0">- {{ PriceHelper::formatPrice(abs($vTxn->amount)) }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>{{ __('Wallet Balance After') }}</th>
                                <td class="font-weight-bold text-dark h6 mb-0">
                                    {{ PriceHelper::formatPrice($vTxn->balance_after) }}
                                </td>
                            </tr>
                            <tr>
                                <th>{{ __('Status') }}</th>
                                <td>
                                    @if($vTxn->status === 'completed' || $vTxn->status === 'approved')
                                        <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i> {{ __('Completed / Approved') }}</span>
                                    @elseif($vTxn->status === 'pending')
                                        <span class="badge badge-warning text-dark px-2 py-1"><i class="fas fa-clock mr-1"></i> {{ __('Pending') }}</span>
                                    @elseif($vTxn->status === 'rejected')
                                        <span class="badge badge-danger px-2 py-1"><i class="fas fa-times-circle mr-1"></i> {{ __('Rejected') }}</span>
                                    @else
                                        <span class="badge badge-secondary px-2 py-1">{{ ucfirst($vTxn->status) }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>{{ __('Details / Description') }}</th>
                                <td>{{ $vTxn->details }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Date & Time') }}</th>
                                <td>{{ $vTxn->created_at ? $vTxn->created_at->format('M d, Y h:i:s A') : '-' }}</td>
                            </tr>
                        </table>
                    </div>

                    @if($vTxn->order)
                        <div class="mt-4">
                            <h6 class="font-weight-bold text-dark">{{ __('Related Order Details') }}</h6>
                            <p class="mb-1"><strong>{{ __('Order #:') }}</strong> {{ $vTxn->order->transaction_number }}</p>
                            <p class="mb-1"><strong>{{ __('Payment Status:') }}</strong> {{ $vTxn->order->payment_status }}</p>
                            <p class="mb-2"><strong>{{ __('Order Status:') }}</strong> {{ $vTxn->order->order_status }}</p>
                            <a href="{{ route('seller.order.invoice', $vTxn->order->id) }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-file-invoice mr-1"></i> {{ __('View Full Order Invoice') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        @if($vTxn->depositRequest)
            <div class="col-lg-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-paperclip mr-1"></i> {{ __('Deposit Details & Proof') }}</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-1"><strong>{{ __('Payment Method:') }}</strong> {{ $vTxn->depositRequest->payment_method }}</p>
                        @if($vTxn->depositRequest->bank_name)
                            <p class="mb-1"><strong>{{ __('Sender Bank / Wallet:') }}</strong> <span class="badge badge-info">{{ $vTxn->depositRequest->bank_name }}</span></p>
                        @endif
                        <p class="mb-1"><strong>{{ __('Account Name:') }}</strong> {{ $vTxn->depositRequest->account_name }}</p>
                        <p class="mb-1"><strong>{{ __('Account Number:') }}</strong> {{ $vTxn->depositRequest->account_number }}</p>
                        <p class="mb-1"><strong>{{ __('Txn ID / Ref:') }}</strong> <code>{{ $vTxn->depositRequest->txn_id }}</code></p>
                        
                        @if($vTxn->depositRequest->admin_note)
                            <div class="alert alert-info mt-3 py-2 px-3">
                                <small><strong>{{ __('Admin Note:') }}</strong> {{ $vTxn->depositRequest->admin_note }}</small>
                            </div>
                        @endif

                        @if($vTxn->depositRequest->screenshot)
                            <div class="mt-3">
                                <strong>{{ __('Deposit Proof:') }}</strong>
                                <div class="border rounded p-2 bg-light mt-1 text-center">
                                    <img src="{{ asset('storage/images/deposits/' . $vTxn->depositRequest->screenshot) }}" class="img-fluid rounded" alt="Deposit Proof" style="max-height: 250px;">
                                    <div class="mt-2">
                                        <a href="{{ asset('storage/images/deposits/' . $vTxn->depositRequest->screenshot) }}" target="_blank" download class="btn btn-sm btn-info btn-block">
                                            <i class="fas fa-download mr-1"></i> {{ __('Download Screenshot') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
