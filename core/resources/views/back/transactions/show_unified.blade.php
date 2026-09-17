@extends('master.back')

@section('content')

<div class="container-fluid">

    <!-- Page Heading -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between">
                <div>
                    <h3 class="mb-0 text-dark">
                        <b><i class="{{ $item->type_icon }} text-primary mr-2"></i> {{ $item->type_label }} {{ __('Details') }}</b>
                    </h3>
                    <p class="text-muted small mb-0">{{ __('Reference:') }} <strong>{{ $item->reference }}</strong> &bull; {{ __('Transaction ID:') }} <code>{{ $item->txn_id }}</code></p>
                </div>
                <div class="mt-2 mt-sm-0">
                    <a href="{{ route('back.transaction.index') }}" class="btn btn-primary btn-sm"><i class="fas fa-chevron-left mr-1"></i> {{ __('Back to Transactions') }}</a>
                    @if($item->direct_url)
                        <a href="{{ $item->direct_url }}" class="btn btn-outline-info btn-sm"><i class="fas fa-external-link-alt mr-1"></i> {{ $item->direct_label }}</a>
                    @endif
                    <a href="javascript:window.print()" class="btn btn-secondary btn-sm"><i class="fas fa-print mr-1"></i> {{ __('Print') }}</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Transaction Summary Card -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header py-3 bg-primary text-white d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold"><i class="fas fa-receipt mr-1"></i> {{ __('Transaction Overview') }}</h6>
                    <span class="badge badge-light text-dark font-weight-bold">{{ $item->type_label }}</span>
                </div>
                <div class="card-body">
                    <table class="table table-bordered mb-0">
                        <tr>
                            <th class="bg-light" style="width: 40%;">{{ __('Transaction Type :') }}</th>
                            <td>
                                <span class="badge {{ $item->type_badge }} px-2 py-1">
                                    <i class="{{ $item->type_icon }} mr-1"></i> {{ $item->type_label }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th class="bg-light">{{ __('Transaction ID / Ref :') }}</th>
                            <td>
                                <strong class="text-primary font-weight-bold" style="font-size: 15px;">
                                    {{ $item->txn_id }}
                                </strong>
                            </td>
                        </tr>
                        <tr>
                            <th class="bg-light">{{ __('System Reference :') }}</th>
                            <td><strong>{{ $item->reference }}</strong></td>
                        </tr>
                        <tr>
                            <th class="bg-light">{{ __('Transaction Date :') }}</th>
                            <td>{{ $item->created_at ? $item->created_at->format('M d, Y - h:i A') : '-' }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">{{ __('Amount :') }}</th>
                            <td>
                                <strong class="text-success" style="font-size: 18px;">
                                    {{ $item->currency_sign }} {{ number_format($item->amount, 2) }}
                                </strong>
                            </td>
                        </tr>
                        <tr>
                            <th class="bg-light">{{ __('Payment Method :') }}</th>
                            <td>
                                <span class="badge badge-secondary px-2 py-1">{{ $item->payment_method }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th class="bg-light">{{ __('Transaction Status :') }}</th>
                            <td>
                                @if(in_array(strtolower($item->status), ['paid', 'approved', 'completed', 'active']))
                                    <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i> {{ $item->status }}</span>
                                @elseif(in_array(strtolower($item->status), ['pending', 'pending fine']))
                                    <span class="badge badge-warning text-dark px-2 py-1"><i class="fas fa-clock mr-1"></i> {{ $item->status }}</span>
                                @elseif(in_array(strtolower($item->status), ['rejected', 'canceled', 'unpaid']))
                                    <span class="badge badge-danger px-2 py-1"><i class="fas fa-times-circle mr-1"></i> {{ $item->status }}</span>
                                @else
                                    <span class="badge badge-secondary px-2 py-1">{{ $item->status }}</span>
                                @endif
                            </td>
                        </tr>
                        @if($item->details)
                        <tr>
                            <th class="bg-light">{{ __('Details / Note :') }}</th>
                            <td><span class="text-muted">{{ $item->details }}</span></td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <!-- Store & Sender Details Card -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header py-3 bg-dark text-white d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold"><i class="fas fa-store mr-1"></i> {{ __('Store & Sender Information') }}</h6>
                    @if($item->store_url)
                        <a href="{{ $item->store_url }}" target="_blank" class="btn btn-xs btn-outline-light text-white py-0 px-2" style="font-size: 11px;">
                            <i class="fas fa-external-link-alt mr-1"></i> {{ __('View Store') }}
                        </a>
                    @endif
                </div>
                <div class="card-body">
                    <table class="table table-bordered mb-0">
                        <tr>
                            <th class="bg-light" style="width: 40%;">{{ __('Store Name :') }}</th>
                            <td>
                                <strong class="text-dark">{{ $item->store_name ?: '-' }}</strong>
                            </td>
                        </tr>
                        <tr>
                            <th class="bg-light">{{ __('Applicant / Sender :') }}</th>
                            <td><strong>{{ $item->name ?: '-' }}</strong></td>
                        </tr>
                        <tr>
                            <th class="bg-light">{{ __('Email Address :') }}</th>
                            <td>{{ $item->email ?: '-' }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">{{ __('Phone Number :') }}</th>
                            <td>{{ $item->phone ?: '-' }}</td>
                        </tr>
                        @if($item->account_name || $item->account_number || $item->bank_name)
                        <tr>
                            <th class="bg-light">{{ __('Sender Account Info :') }}</th>
                            <td>
                                @if($item->bank_name)
                                    <div><span class="text-muted small">{{ __('Bank:') }}</span> <strong>{{ $item->bank_name }}</strong></div>
                                @endif
                                @if($item->account_name)
                                    <div><span class="text-muted small">{{ __('Title:') }}</span> <strong>{{ $item->account_name }}</strong></div>
                                @endif
                                @if($item->account_number)
                                    <div><span class="text-muted small">{{ __('A/C No:') }}</span> <code class="font-weight-bold text-primary">{{ $item->account_number }}</code></div>
                                @endif
                            </td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Screenshot & Proof Preview -->
    @if($item->screenshot_url)
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header py-3 bg-light d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-image text-info mr-1"></i> {{ __('Payment Proof Screenshot') }}</h6>
                    <a href="{{ $item->screenshot_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-external-link-alt mr-1"></i> {{ __('Open Original Image') }}
                    </a>
                </div>
                <div class="card-body text-center bg-white p-4">
                    <a href="{{ $item->screenshot_url }}" target="_blank">
                        <img src="{{ $item->screenshot_url }}" alt="Payment Proof" class="img-fluid rounded shadow-sm border" style="max-height: 450px; object-fit: contain;">
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>

@endsection