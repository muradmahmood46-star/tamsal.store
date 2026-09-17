@extends('master.seller')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between">
                <div>
                    <h3 class="mb-0 text-dark"><b><i class="fas fa-receipt text-primary mr-2"></i> {{ __('Wallet Transactions & Commission History') }}</b></h3>
                    <small class="text-muted">{{ __('View complete log of your wallet deposits, commission deductions, and running balance.') }}</small>
                </div>
                <div class="mt-2 mt-sm-0">
                    <a href="{{ route('seller.wallet.index') }}" class="btn btn-primary btn-sm shadow-sm font-weight-bold">
                        <i class="fas fa-plus-circle mr-1"></i> {{ __('Add Balance') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    @include('alerts.alerts')

    <!-- Stat Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">{{ __('Current Wallet Balance') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ PriceHelper::formatPrice($seller->balance ?? 0) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-wallet fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">{{ __('Total Balance Added') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-success">
                                + {{ PriceHelper::formatPrice($totalDeposits) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-down fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">{{ __('Total Commission Paid') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-danger">
                                - {{ PriceHelper::formatPrice($totalCommissionDeducted) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">{{ __('Pending Deposit Requests') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-warning">
                                {{ $pendingDepositsCount }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Buttons & Transactions Table -->
    <div class="card shadow mb-4">
        <div class="card-header bg-white py-3 d-flex flex-wrap align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-list mr-1"></i> {{ __('Transaction Log') }}</h6>
            <div class="btn-group btn-group-sm mt-2 mt-sm-0" role="group">
                <a href="{{ route('seller.transaction.index') }}" class="btn {{ empty($filterType) ? 'btn-primary font-weight-bold' : 'btn-outline-secondary' }}">
                    {{ __('All') }}
                </a>
                <a href="{{ route('seller.transaction.index', ['type' => 'deposit']) }}" class="btn {{ $filterType === 'deposit' ? 'btn-success font-weight-bold' : 'btn-outline-secondary' }}">
                    <i class="fas fa-plus-circle mr-1"></i> {{ __('Deposits (Add Balance)') }}
                </a>
                <a href="{{ route('seller.transaction.index', ['type' => 'fine_payment']) }}" class="btn {{ $filterType === 'fine_payment' ? 'btn-warning text-dark font-weight-bold' : 'btn-outline-secondary' }}">
                    <i class="fas fa-file-invoice-dollar mr-1"></i> {{ __('Fine Payments') }}
                </a>
                <a href="{{ route('seller.transaction.index', ['type' => 'commission_deduction']) }}" class="btn {{ $filterType === 'commission_deduction' ? 'btn-danger font-weight-bold' : 'btn-outline-secondary' }}">
                    <i class="fas fa-minus-circle mr-1"></i> {{ __('Commission Deductions') }}
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="gd-responsive-table">
                <table class="table table-bordered table-hover align-middle" style="min-width: 950px;" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Details / Reference') }}</th>
                            <th class="text-right">{{ __('Amount') }}</th>
                            <th class="text-right">{{ __('Balance After') }}</th>
                            <th class="text-center">{{ __('Status') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th class="text-center">{{ __('Proof / Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($datas as $txn)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    @if($txn->type === 'deposit')
                                        <span class="badge badge-success px-2 py-1 font-weight-normal">
                                            <i class="fas fa-plus-circle mr-1"></i> {{ __('Add Balance') }}
                                        </span>
                                    @elseif($txn->type === 'fine_payment')
                                        <span class="badge badge-warning text-dark px-2 py-1 font-weight-normal">
                                            <i class="fas fa-file-invoice-dollar mr-1 text-danger"></i> {{ __('Fine Payment') }}
                                        </span>
                                    @elseif($txn->type === 'commission_deduction')
                                        <span class="badge badge-danger px-2 py-1 font-weight-normal">
                                            <i class="fas fa-minus-circle mr-1"></i> {{ __('Commission Cut') }}
                                        </span>
                                    @else
                                        <span class="badge badge-secondary px-2 py-1 font-weight-normal">
                                            {{ ucfirst(str_replace('_', ' ', $txn->type)) }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($txn->type === 'deposit' && $txn->depositRequest)
                                        <div><strong>{{ $txn->depositRequest->payment_method }}</strong></div>
                                        @if($txn->depositRequest->bank_name)
                                            <small class="text-primary font-weight-bold d-block">{{ __('From:') }} {{ $txn->depositRequest->bank_name }}</small>
                                        @endif
                                        <small class="text-muted d-block">
                                            {{ __('A/C:') }} {{ $txn->depositRequest->account_name }} ({{ $txn->depositRequest->account_number }})
                                        </small>
                                        <small class="text-dark font-weight-bold">
                                            {{ __('Txn ID:') }} <code>{{ $txn->depositRequest->txn_id }}</code>
                                        </small>
                                        @if($txn->depositRequest->admin_note && $txn->depositRequest->status === 'rejected')
                                            <div class="small text-danger mt-1">
                                                <strong>{{ __('Note:') }}</strong> {{ $txn->depositRequest->admin_note }}
                                            </div>
                                        @endif
                                    @elseif($txn->type === 'fine_payment' && $txn->finePayment)
                                        <div><strong>{{ $txn->finePayment->payment_method }}</strong></div>
                                        @if($txn->finePayment->bank_name)
                                            <small class="text-warning font-weight-bold d-block">{{ __('From:') }} {{ $txn->finePayment->bank_name }}</small>
                                        @endif
                                        <small class="text-muted d-block">
                                            {{ __('A/C:') }} {{ $txn->finePayment->account_name }} ({{ $txn->finePayment->account_number }})
                                        </small>
                                        <small class="text-dark font-weight-bold">
                                            {{ __('Txn ID:') }} <code>{{ $txn->finePayment->txn_id }}</code>
                                        </small>
                                        @if($txn->finePayment->admin_note && $txn->finePayment->status === 'rejected')
                                            <div class="small text-danger mt-1">
                                                <strong>{{ __('Rejection Reason:') }}</strong> {{ $txn->finePayment->admin_note }}
                                            </div>
                                        @endif
                                    @elseif($txn->type === 'commission_deduction' && $txn->order)
                                        <div>
                                            <strong>{{ __('Order #:') }}</strong>
                                            <a href="{{ route('seller.order.invoice', $txn->order->id) }}" class="font-weight-bold text-primary">
                                                {{ $txn->order->transaction_number }}
                                            </a>
                                        </div>
                                        <small class="text-muted">{{ $txn->details }}</small>
                                    @else
                                        <span>{{ $txn->details ?: 'N/A' }}</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    @if($txn->type === 'deposit')
                                        <span class="font-weight-bold text-success h6 mb-0">+ {{ PriceHelper::formatPrice($txn->amount) }}</span>
                                    @else
                                        <span class="font-weight-bold text-danger h6 mb-0">- {{ PriceHelper::formatPrice(abs($txn->amount)) }}</span>
                                    @endif
                                </td>
                                <td class="text-right font-weight-bold text-dark">
                                    {{ PriceHelper::formatPrice($txn->balance_after ?? 0) }}
                                </td>
                                <td class="text-center">
                                    @if($txn->status === 'completed' || $txn->status === 'approved')
                                        <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i> {{ __('Completed') }}</span>
                                    @elseif($txn->status === 'pending')
                                        <span class="badge badge-warning text-dark px-2 py-1"><i class="fas fa-clock mr-1"></i> {{ __('Pending') }}</span>
                                    @elseif($txn->status === 'rejected')
                                        <span class="badge badge-danger px-2 py-1"><i class="fas fa-times-circle mr-1"></i> {{ __('Rejected') }}</span>
                                    @else
                                        <span class="badge badge-secondary px-2 py-1">{{ ucfirst($txn->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="font-weight-bold text-dark">{{ $txn->created_at ? $txn->created_at->format('M d, Y') : '' }}</small><br>
                                    <small class="text-muted">{{ $txn->created_at ? $txn->created_at->format('h:i A') : '' }}</small>
                                </td>
                                <td class="text-center">
                                    @if($txn->type === 'deposit' && $txn->depositRequest && $txn->depositRequest->screenshot)
                                        <button type="button" class="btn btn-outline-info btn-sm view-screenshot-btn" 
                                            data-src="{{ asset('storage/images/deposits/' . $txn->depositRequest->screenshot) }}"
                                            data-title="{{ $txn->depositRequest->payment_method }} - {{ PriceHelper::formatPrice($txn->amount) }}"
                                            data-txnid="{{ $txn->depositRequest->txn_id }}"
                                            data-toggle="modal" data-target="#screenshotModal">
                                            <i class="fas fa-image mr-1"></i> {{ __('Proof') }}
                                        </button>
                                    @elseif($txn->type === 'fine_payment' && $txn->finePayment && $txn->finePayment->screenshot)
                                        <button type="button" class="btn btn-outline-warning text-dark btn-sm view-screenshot-btn" 
                                            data-src="{{ asset('storage/images/fines/' . $txn->finePayment->screenshot) }}"
                                            data-title="Fine Payment - {{ PriceHelper::formatPrice($txn->amount) }}"
                                            data-txnid="{{ $txn->finePayment->txn_id }}"
                                            data-toggle="modal" data-target="#screenshotModal">
                                            <i class="fas fa-image mr-1"></i> {{ __('Proof') }}
                                        </button>
                                    @elseif($txn->order_id)
                                        <a href="{{ route('seller.order.invoice', $txn->order_id) }}" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye mr-1"></i> {{ __('Order') }}
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="fas fa-wallet fa-3x mb-3 text-gray-300 d-block"></i>
                                    <h5>{{ __('No Transactions Found') }}</h5>
                                    <p class="mb-3">{{ __('You have not made any deposits or had any commission deductions yet.') }}</p>
                                    <a href="{{ route('seller.wallet.index') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus-circle mr-1"></i> {{ __('Add Balance Now') }}
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($datas->hasPages())
                <div class="d-flex justify-content-end mt-3">
                    {{ $datas->appends(request()->input())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Screenshot Preview Modal -->
<div class="modal fade" id="screenshotModal" tabindex="-1" role="dialog" aria-labelledby="screenshotModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="screenshotModalLabel"><i class="fas fa-receipt mr-2"></i> {{ __('Deposit Proof / Screenshot') }}</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center p-3">
                <p id="modalProofTitle" class="font-weight-bold text-primary mb-2"></p>
                <div class="border rounded p-2 bg-light">
                    <img id="modalScreenshotImg" src="" alt="Payment Proof" class="img-fluid rounded" style="max-height: 500px; object-fit: contain;">
                </div>
            </div>
            <div class="modal-footer">
                <a id="modalDownloadBtn" href="" target="_blank" download class="btn btn-info btn-sm">
                    <i class="fas fa-download mr-1"></i> {{ __('Download / Full Size') }}
                </a>
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).on('click', '.view-screenshot-btn', function() {
        var src = $(this).data('src');
        var title = $(this).data('title');
        var txnid = $(this).data('txnid');
        $('#modalScreenshotImg').attr('src', src);
        $('#modalDownloadBtn').attr('href', src);
        $('#modalProofTitle').text(title + ' (Txn ID: ' + txnid + ')');
    });
</script>
@endsection
