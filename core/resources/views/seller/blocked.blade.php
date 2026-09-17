@extends('master.front')

@section('title')
    {{ __('Store Suspended - Unblock Request') }}
@endsection

@section('styles')
<style>
    .account-pay-card {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background: #ffffff;
        transition: all 0.2s ease;
    }
    .account-pay-card:hover {
        border-color: #f59e0b;
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.12);
    }
    .pulse-glow {
        animation: pulseGlow 1.5s ease-in-out infinite;
    }
    @keyframes pulseGlow {
        0%, 100% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.4); }
        50% { box-shadow: 0 0 0 10px rgba(245, 158, 11, 0); }
    }
    .account-card-single {
        animation: fadeInCard 0.3s ease-in-out;
    }
    @keyframes fadeInCard {
        from { opacity: 0; transform: translateY(5px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection

@section('content')
<div class="page-title">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <ul class="breadcrumbs">
                    <li><a href="{{ route('front.index') }}">{{ __('Home') }}</a></li>
                    <li class="separator"></li>
                    <li><a href="{{ route('user.dashboard') }}">{{ __('Dashboard') }}</a></li>
                    <li class="separator"></li>
                    <li>{{ __('Store Status & Unblock Appeal') }}</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="container padding-bottom-3x mb-4 mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-9 col-md-11">
            @include('alerts.alerts')

            <!-- Store Suspension Banner -->
            <div class="card border-danger shadow-sm mb-4" style="border-radius: 12px; overflow: hidden;">
                <div class="card-header bg-danger text-white py-3 d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 text-white font-weight-bold">
                        <i class="fas fa-ban mr-2"></i> {{ __('Seller Account Suspended') }}
                    </h5>
                    @if(isset($unblockRequest) && $unblockRequest)
                        @php
                            $stBadge = 'badge-warning text-dark';
                            if ($unblockRequest->status === 'Unblocked') $stBadge = 'badge-success text-white';
                            elseif ($unblockRequest->status === 'Replied') $stBadge = 'badge-info text-white';
                            elseif ($unblockRequest->status === 'Pending Fine') $stBadge = 'badge-warning text-dark font-weight-bold';
                        @endphp
                        <span class="badge {{ $stBadge }} font-weight-bold px-3 py-1" style="font-size: 12px; border-radius: 20px;">
                            {{ __('Appeal Status: ') }} {{ $unblockRequest->status }}
                        </span>
                    @else
                        <span class="badge badge-light text-danger font-weight-bold px-3 py-1" style="font-size: 12px; border-radius: 20px;">
                            {{ __('Status: Blocked') }}
                        </span>
                    @endif
                </div>
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light border border-danger p-3 mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-store-slash fa-2x text-danger"></i>
                        </span>
                        <h4 class="text-danger font-weight-bold mb-2">{{ __('Your Store has been Blocked by Administration') }}</h4>
                        <p class="text-muted mb-0 lead" style="font-size: 15.5px;">
                            {{ __('Your seller dashboard access and product listings for') }}
                            <strong class="text-dark">"{{ $seller ? $seller->shop_name : Auth::user()->first_name . '\'s Store' }}"</strong>
                            {{ __('are currently deactivated.') }}
                        </p>
                    </div>

                    <!-- Fine Imposition Alert & Pay Fine Action (If Fine Imposed) -->
                    @if(isset($unblockRequest) && $unblockRequest && $unblockRequest->fine_amount > 0 && ($unblockRequest->status === 'Pending Fine' || $unblockRequest->fine_status !== 'paid'))
                        @php
                            $vendorBal = $seller ? (float)$seller->balance : 0;
                            $fineReqAmt = (float)$unblockRequest->fine_amount;
                            $isBalSufficient = ($vendorBal >= $fineReqAmt);
                        @endphp
                        <div class="card border-warning shadow-sm mb-4" style="border-radius: 10px; overflow: hidden; border-left: 5px solid #f59e0b !important; background: #fffdf5;">
                            <div class="card-header bg-white py-2 d-flex align-items-center justify-content-between border-bottom">
                                <h6 class="mb-0 font-weight-bold text-dark" style="font-size: 14px;">
                                    <i class="fas fa-file-invoice-dollar text-warning mr-2"></i> {{ __('Store Unblock Fine Requirement:') }}
                                </h6>
                                @if($unblockRequest->fine_status === 'submitted')
                                    <span class="badge badge-info text-white font-weight-bold px-2 py-1" style="font-size: 11px;">
                                        <i class="fas fa-clock mr-1"></i> {{ __('Payment Proof Submitted') }}
                                    </span>
                                @elseif($unblockRequest->fine_status === 'rejected')
                                    <span class="badge badge-danger text-white font-weight-bold px-2 py-1" style="font-size: 11px;">
                                        <i class="fas fa-times mr-1"></i> {{ __('Proof Rejected') }}
                                    </span>
                                @else
                                    <span class="badge badge-warning text-dark font-weight-bold px-2 py-1" style="font-size: 11px;">
                                        <i class="fas fa-exclamation-circle mr-1"></i> {{ __('Action Required') }}
                                    </span>
                                @endif
                            </div>
                            <div class="card-body p-4 bg-white">
                                <div class="d-sm-flex align-items-center justify-content-between">
                                    <div class="mb-3 mb-sm-0 mr-sm-3">
                                        <p class="text-dark font-weight-bold mb-1" style="font-size: 15px; line-height: 1.5;">
                                            {{ __('To unblock your store, a fine of :curr :amount has been imposed by the system. Please pay the fine to continue operating your store.', [
                                                'curr' => PriceHelper::adminCurrency(),
                                                'amount' => number_format($unblockRequest->fine_amount, 2)
                                            ]) }}
                                        </p>
                                        @if($isBalSufficient)
                                            <div class="mt-2">
                                                <span class="badge badge-success font-weight-bold px-2 py-1" style="font-size: 11.5px;">
                                                    <i class="fas fa-wallet mr-1"></i> {{ __('Sufficient balance available in wallet') }} ({{ PriceHelper::adminCurrency() }} {{ number_format($vendorBal, 2) }})
                                                </span>
                                            </div>
                                        @else
                                            <div class="mt-2">
                                                <span class="badge badge-light border text-danger font-weight-bold px-2 py-1" style="font-size: 11.5px;">
                                                    <i class="fas fa-exclamation-triangle mr-1"></i> {{ __('Wallet balance: :curr :bal (Insufficient)', ['curr' => PriceHelper::adminCurrency(), 'bal' => number_format($vendorBal, 2)]) }}
                                                </span>
                                            </div>
                                        @endif
                                        @if($unblockRequest->fine_status === 'submitted')
                                            <small class="text-info font-weight-bold d-block mt-1">
                                                <i class="fas fa-info-circle mr-1"></i> {{ __('Your payment proof is under review by Administration. Your store will be unblocked upon verification.') }}
                                            </small>
                                        @elseif($unblockRequest->fine_status === 'rejected')
                                            <small class="text-danger font-weight-bold d-block mt-1">
                                                <i class="fas fa-exclamation-triangle mr-1"></i> {{ __('Your previous payment proof was rejected. Please resubmit valid payment proof with correct Transaction ID.') }}
                                            </small>
                                        @endif
                                    </div>
                                    <div class="flex-shrink-0">
                                        @if($unblockRequest->fine_status === 'submitted')
                                            <button type="button" class="btn btn-outline-info font-weight-bold px-3 py-2" onclick="openPayFineModal(event)" data-toggle="modal" data-target="#payFineModal" data-bs-toggle="modal" data-bs-target="#payFineModal">
                                                <i class="fas fa-eye mr-1"></i> {{ __('View / Update Proof') }}
                                            </button>
                                        @elseif($unblockRequest->fine_status === 'rejected')
                                            <button type="button" class="btn btn-warning text-dark font-weight-bold px-4 py-2 shadow-sm pulse-glow" onclick="openPayFineModal(event)" data-toggle="modal" data-target="#payFineModal" data-bs-toggle="modal" data-bs-target="#payFineModal">
                                                <i class="fas fa-redo mr-1"></i> {{ __('Resubmit Pay Fine') }}
                                            </button>
                                        @else
                                            @if($isBalSufficient)
                                                <button type="button" class="btn btn-warning text-dark font-weight-bold px-4 py-2 shadow-sm pulse-glow" style="font-size: 15px; border-radius: 8px;" onclick="openWalletPayModal(event)">
                                                    <i class="fas fa-credit-card mr-1"></i> {{ __('Pay Fine') }}
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-warning text-dark font-weight-bold px-4 py-2 shadow-sm pulse-glow" style="font-size: 15px; border-radius: 8px;" onclick="openPayFineModal(event)">
                                                    <i class="fas fa-credit-card mr-1"></i> {{ __('Pay Fine') }}
                                                </button>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Administration Reason / Message (If Provided) -->
                    @if((isset($unblockRequest) && $unblockRequest->admin_reply) || Auth::user()->chat_blocked_reason)
                        <div class="card border-danger shadow-sm mb-4" style="border-radius: 10px; overflow: hidden; border-left: 5px solid #dc3545 !important;">
                            <div class="card-header bg-white py-2 d-flex align-items-center justify-content-between border-bottom">
                                <h6 class="mb-0 font-weight-bold text-danger" style="font-size: 14px;">
                                    <i class="fas fa-exclamation-circle mr-2"></i> {{ __('Administration Notice / Reason:') }}
                                </h6>
                                @if(isset($unblockRequest) && $unblockRequest->admin_replied_at)
                                    <small class="text-muted font-weight-bold" style="font-size: 11px;">
                                        {{ $unblockRequest->admin_replied_at->format('M d, Y h:i A') }}
                                    </small>
                                @endif
                            </div>
                            <div class="card-body p-3 bg-light">
                                <div class="p-3 bg-white rounded shadow-sm border">
                                    <p class="text-dark mb-0 font-weight-bold" style="font-size: 14.5px; white-space: pre-wrap; line-height: 1.6;">{{ $unblockRequest ? $unblockRequest->admin_reply : Auth::user()->chat_blocked_reason }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="alert alert-warning p-3 mb-0" style="border-radius: 8px;">
                        <div class="d-flex">
                            <i class="fas fa-info-circle fa-lg text-warning mr-3 mt-1"></i>
                            <div>
                                <strong class="text-dark d-block mb-1">{{ __('Important Account Information:') }}</strong>
                                <ul class="mb-0 pl-3 small text-muted">
                                    <li>{{ __('Your sales history, orders, and financial balance remain safe and intact.') }}</li>
                                    <li>{{ __('Your products will become visible on the marketplace as soon as your account is unblocked.') }}</li>
                                    <li>{{ __('Please use the appeal form below to contact support and request store unblocking.') }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- DEDICATED INLINE FINE PAYMENT SECTION (When fine is required) -->
            @if(isset($unblockRequest) && $unblockRequest && $unblockRequest->fine_amount > 0 && ($unblockRequest->status === 'Pending Fine' || $unblockRequest->fine_status !== 'paid'))
                @php
                    $vendorBal = $seller ? (float)$seller->balance : 0;
                    $fineReqAmt = (float)$unblockRequest->fine_amount;
                    $isBalSufficient = ($vendorBal >= $fineReqAmt);
                @endphp
                <div class="card shadow-sm border-warning mb-4" id="payFineInlineCard" style="border-radius: 12px; overflow: hidden; border: 2px solid #f59e0b !important;">
                    <div class="card-header bg-warning text-dark py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 font-weight-bold text-dark">
                            <i class="fas fa-file-invoice-dollar mr-2"></i> {{ __('Pay Fine — Store Unblock') }}
                        </h5>
                        <span class="badge badge-dark text-white font-weight-bold px-3 py-1">
                            {{ PriceHelper::adminCurrency() }} {{ number_format($unblockRequest->fine_amount, 2) }}
                        </span>
                    </div>
                    <div class="card-body p-4 p-md-5 bg-white">

                        <!-- Sufficient vs Insufficient Wallet Balance Alert -->
                        @if($isBalSufficient)
                            <div class="alert alert-success border-success shadow-sm mb-4" style="border-radius: 10px; background: #f0fdf4; border-left: 5px solid #16a34a !important;">
                                <div class="d-md-flex align-items-center justify-content-between">
                                    <div class="mb-3 mb-md-0 mr-md-3">
                                        <h6 class="font-weight-bold text-success mb-1">
                                            <i class="fas fa-wallet mr-1"></i> {{ __('Sufficient amount in balance.') }}
                                        </h6>
                                        <p class="mb-0 text-dark" style="font-size: 14.5px; line-height: 1.5;">
                                            {{ __('Sufficient amount in balance. :curr :fine will be deducted from your balance.', [
                                                'curr' => PriceHelper::adminCurrency(),
                                                'fine' => number_format($fineReqAmt, 2)
                                            ]) }}
                                        </p>
                                        <small class="text-muted font-weight-bold d-block mt-1">
                                            <i class="fas fa-check-circle text-success mr-1"></i> {{ __('Current Available Balance: :curr :bal', ['curr' => PriceHelper::adminCurrency(), 'bal' => number_format($vendorBal, 2)]) }} &bull; {{ __('Store and products will be automatically unblocked immediately upon confirmation.') }}
                                        </small>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <form action="{{ route('seller.fine.pay_wallet') }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to deduct :curr :fine from your wallet balance to instantly unblock your store?', ['curr' => PriceHelper::adminCurrency(), 'fine' => number_format($fineReqAmt, 2)]) }}');">
                                            @csrf
                                            <button type="submit" class="btn btn-success font-weight-bold px-4 py-3 shadow text-white" style="font-size: 15px; border-radius: 8px;">
                                                <i class="fas fa-bolt mr-1 text-warning"></i> {{ __('Confirm & Pay :curr :fine from Balance', ['curr' => PriceHelper::adminCurrency(), 'fine' => number_format($fineReqAmt, 2)]) }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="text-center my-4 position-relative">
                                <hr style="margin: 0;">
                                <span class="bg-white px-3 text-muted font-weight-bold small" style="position: absolute; top: -10px; left: 50%; transform: translateX(-50%);">
                                    {{ __('OR PAY VIA MANUAL PAYMENT TRANSFER BELOW') }}
                                </span>
                            </div>
                        @else
                            <div class="alert alert-danger border-danger shadow-sm mb-4" style="border-radius: 10px; background: #fef2f2; border-left: 5px solid #dc2525 !important;">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-exclamation-triangle fa-2x text-danger mr-3"></i>
                                    <div>
                                        <h6 class="font-weight-bold text-danger mb-1">
                                            {{ __('Insufficient Balance in Wallet') }}
                                        </h6>
                                        <p class="mb-0 text-dark" style="font-size: 14.5px; line-height: 1.5;">
                                            {{ __('Insufficient balance in wallet (Available: :curr :bal, Required: :curr :fine). Please select a payment method below to pay fine.', [
                                                'curr' => PriceHelper::adminCurrency(),
                                                'bal' => number_format($vendorBal, 2),
                                                'fine' => number_format($fineReqAmt, 2)
                                            ]) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Fine Instructions Alert -->
                        <div class="alert alert-warning border-0 shadow-sm mb-4" style="border-radius: 8px; background: #fffbe8;">
                            <h6 class="font-weight-bold text-dark mb-1">
                                <i class="fas fa-info-circle text-warning mr-1"></i> {{ __('Fine Payment Instructions:') }}
                            </h6>
                            <p class="mb-0 small text-dark" style="line-height: 1.5;">
                                {{ __('Transfer exact fine amount of') }} <strong class="text-danger" style="font-size: 15px;">{{ PriceHelper::adminCurrency() }} {{ number_format($unblockRequest->fine_amount, 2) }}</strong> {{ __('to our official Administration receiving account below, then submit your transaction proof.') }}
                            </p>
                        </div>

                        <!-- Previous Submission Status Card (If already submitted) -->
                        @if($unblockRequest->fine_status === 'submitted' && isset($latestFinePayment) && $latestFinePayment)
                            <div class="card border-info mb-4" style="background: #f0fdf4; border-radius: 8px;">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <h6 class="text-success font-weight-bold mb-0">
                                            <i class="fas fa-check-circle mr-1"></i> {{ __('Current Submitted Proof (Under Review):') }}
                                        </h6>
                                        <span class="badge badge-info">{{ __('Pending Approval') }}</span>
                                    </div>
                                    <div class="row text-dark small">
                                        <div class="col-sm-6 mb-1"><strong>{{ __('Txn ID:') }}</strong> <code>{{ $latestFinePayment->txn_id }}</code></div>
                                        <div class="col-sm-6 mb-1"><strong>{{ __('Admin Account:') }}</strong> {{ $latestFinePayment->payment_method }}</div>
                                        <div class="col-sm-6 mb-1"><strong>{{ __('Sender:') }}</strong> {{ $latestFinePayment->bank_name }} ({{ $latestFinePayment->account_name }})</div>
                                        <div class="col-sm-6 mb-1"><strong>{{ __('Submitted At:') }}</strong> {{ $latestFinePayment->created_at->format('M d, Y h:i A') }}</div>
                                    </div>
                                    @if($latestFinePayment->screenshot)
                                        <div class="mt-2 pt-2 border-top">
                                            <a href="{{ asset('core/public/storage/images/fines/' . $latestFinePayment->screenshot) }}" target="_blank" class="btn btn-xs btn-outline-primary py-1 px-2 font-weight-bold">
                                                <i class="fas fa-image mr-1"></i> {{ __('View Uploaded Receipt Image') }}
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Fine Payment Submission Form -->
                        <form action="{{ route('seller.fine.submit') }}" method="POST" enctype="multipart/form-data" id="inlineFineForm">
                            @csrf

                            <h6 class="font-weight-bold text-dark mb-3 border-bottom pb-2">
                                <i class="fas fa-edit text-warning mr-1"></i> {{ __('Select Receiving Account & Enter Payment Details:') }}
                            </h6>

                            <div class="row">
                                <!-- Locked Fine Amount -->
                                <div class="col-md-6 form-group mb-3">
                                    <label class="font-weight-bold text-dark">{{ __('Fine Amount Required (Fixed)') }} <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text font-weight-bold bg-white text-dark">{{ PriceHelper::adminCurrency() }}</span>
                                        </div>
                                        <input type="text" class="form-control font-weight-bold bg-light text-danger" value="{{ number_format($unblockRequest->fine_amount, 2) }}" readonly style="font-size: 16px; font-weight: 700;">
                                    </div>
                                    <small class="text-muted">{{ __('This amount is fixed by Administration.') }}</small>
                                </div>

                                <!-- Payment Method / Admin Receiving Account Dropdown -->
                                <div class="col-md-6 form-group mb-3">
                                    <label for="inline_fine_payment_method" class="font-weight-bold text-dark">
                                        {{ __('Select Admin Account Sent To') }} <span class="text-danger">*</span>
                                    </label>
                                    <select name="payment_method" id="inline_fine_payment_method" class="form-control" onchange="onAdminAccountSelect(this, 'inline')" required style="font-weight: 600;">
                                        <option value="" disabled selected>{{ __('--- Select Admin Account ---') }}</option>
                                        @if(isset($receivingAccounts) && $receivingAccounts->count() > 0)
                                            @foreach($receivingAccounts as $acc)
                                                <option value="{{ $acc->payment_method }} - {{ $acc->account_name }} ({{ $acc->account_number }})" data-id="{{ $acc->id }}" {{ (old('payment_method') == ($acc->payment_method . ' - ' . $acc->account_name . ' (' . $acc->account_number . ')')) ? 'selected' : '' }}>
                                                    {{ $acc->payment_method }} &mdash; {{ $acc->account_name }} ({{ $acc->account_number }})
                                                </option>
                                            @endforeach
                                        @else
                                            <option value="Direct Admin Bank Account">{{ __('Direct Admin Bank / Easypaisa Account') }}</option>
                                        @endif
                                    </select>
                                    <small class="text-muted">{{ __('Select the payment method/account you transferred funds to.') }}</small>
                                </div>
                            </div>

                            <!-- Official Admin Receiving Account Details (SHOW ONLY SELECTED ACCOUNT) -->
                            @if(isset($receivingAccounts) && $receivingAccounts->count() > 0)
                                <div class="mb-4">
                                    <div class="card shadow-sm border-0" style="border-radius: 10px; background: #fffbe8; border: 1px solid #fed7aa !important;">
                                        <div class="card-header bg-white py-2 font-weight-bold small text-dark d-flex align-items-center justify-content-between border-bottom">
                                            <span><i class="fas fa-university mr-1 text-primary"></i> {{ __('Official Receiving Account for Fine Payment:') }}</span>
                                            <span class="badge badge-warning text-dark">{{ __('Transfer Here') }}</span>
                                        </div>
                                        <div class="card-body p-3">
                                            <!-- Placeholder when nothing is selected -->
                                            <div id="acc_placeholder_inline" class="text-center text-muted py-2">
                                                <i class="fas fa-hand-point-up text-warning mr-1"></i>
                                                <span>{{ __('Please select an account from "Select Admin Account Sent To" above to view account title and number.') }}</span>
                                            </div>

                                            <!-- Dynamic Account Cards (Only selected account is displayed) -->
                                            @foreach($receivingAccounts as $acc)
                                                <div id="acc_card_inline_{{ $acc->id }}" class="account-card-single d-none">
                                                    <div class="d-sm-flex align-items-center justify-content-between">
                                                        <div class="mb-2 mb-sm-0">
                                                            <div class="d-flex align-items-center mb-1">
                                                                <span class="badge badge-primary font-weight-bold px-2 py-1 mr-2">{{ $acc->payment_method }}</span>
                                                                <span class="text-muted small">{{ __('Title:') }}</span>
                                                                <strong class="text-dark ml-1" style="font-size: 14.5px;">{{ $acc->account_name }}</strong>
                                                            </div>
                                                            @if($acc->note)
                                                                <small class="text-muted font-italic d-block"><i class="fas fa-info-circle text-info mr-1"></i>{{ $acc->note }}</small>
                                                            @endif
                                                        </div>
                                                        <div class="text-sm-right">
                                                            <span class="text-muted small d-block mb-1">{{ __('Account Number:') }}</span>
                                                            <div class="d-inline-flex align-items-center bg-white px-2 py-1 rounded border shadow-sm">
                                                                <code class="font-weight-bold text-primary mr-2" style="font-size: 15px;">{{ $acc->account_number }}</code>
                                                                <button type="button" class="btn btn-warning btn-sm py-0 px-2 font-weight-bold" onclick="copyAccountNumber('{{ $acc->account_number }}', this)">
                                                                    <i class="fas fa-copy mr-1"></i> {{ __('Copy') }}
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="row">
                                <!-- Sender Bank / Wallet Name -->
                                <div class="col-md-4 form-group mb-3">
                                    <label for="inline_fine_bank_name" class="font-weight-bold text-dark">
                                        {{ __('Sent From (Bank / Wallet)') }} <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="bank_name" id="inline_fine_bank_name" class="form-control" value="{{ old('bank_name', $latestFinePayment ? $latestFinePayment->bank_name : '') }}" placeholder="{{ __('e.g. Easypaisa, JazzCash, HBL') }}" required>
                                </div>

                                <!-- Sender Account Title -->
                                <div class="col-md-4 form-group mb-3">
                                    <label for="inline_fine_account_name" class="font-weight-bold text-dark">
                                        {{ __('Sender Account Title') }} <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="account_name" id="inline_fine_account_name" class="form-control" value="{{ old('account_name', $latestFinePayment ? $latestFinePayment->account_name : Auth::user()->first_name . ' ' . Auth::user()->last_name) }}" placeholder="{{ __('Your Account Title') }}" required>
                                </div>

                                <!-- Sender Account Number -->
                                <div class="col-md-4 form-group mb-3">
                                    <label for="inline_fine_account_number" class="font-weight-bold text-dark">
                                        {{ __('Sender Account Number') }} <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="account_number" id="inline_fine_account_number" class="form-control" value="{{ old('account_number', $latestFinePayment ? $latestFinePayment->account_number : Auth::user()->phone) }}" placeholder="{{ __('e.g. 03001234567') }}" required>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Transaction ID -->
                                <div class="col-md-6 form-group mb-3">
                                    <label for="inline_fine_txn_id" class="font-weight-bold text-dark">
                                        {{ __('Transaction ID / Ref No.') }} <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="txn_id" id="inline_fine_txn_id" class="form-control font-weight-bold text-primary" value="{{ old('txn_id', $latestFinePayment ? $latestFinePayment->txn_id : '') }}" placeholder="{{ __('e.g. TRX982746123') }}" required>
                                </div>

                                <!-- Screenshot Upload with Live Preview -->
                                <div class="col-md-6 form-group mb-3">
                                    <label for="inline_fine_screenshot" class="font-weight-bold text-dark">
                                        {{ __('Payment Proof Screenshot') }} <span class="text-danger">*</span>
                                    </label>
                                    <input type="file" name="screenshot" id="inline_fine_screenshot" class="form-control-file border p-2 rounded bg-light w-100" accept="image/*" onchange="previewScreenshot(this, 'inlineProofPreview', 'inlinePreviewBox')" required>
                                    <small class="text-muted">{{ __('Upload transaction receipt or payment screenshot (JPG, PNG, WebP).') }}</small>
                                </div>
                            </div>

                            <!-- Live Screenshot Preview Area -->
                            <div id="inlinePreviewBox" class="mb-3 d-none text-center p-3 bg-light rounded border">
                                <p class="text-muted small mb-2 font-weight-bold">{{ __('Selected Receipt Preview:') }}</p>
                                <img id="inlineProofPreview" src="" class="img-fluid rounded shadow-sm" style="max-height: 180px; object-fit: contain;">
                            </div>

                            <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                                <button type="reset" class="btn btn-outline-secondary font-weight-bold">
                                    <i class="fas fa-undo mr-1"></i> {{ __('Reset') }}
                                </button>
                                <button type="submit" class="btn btn-warning text-dark font-weight-bold px-5 py-3 shadow" style="font-size: 16px; border-radius: 8px;">
                                    <i class="fas fa-paper-plane mr-2"></i> {{ __('Submit Fine Payment Proof') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Full Chat Stream with Administration (If Messages Exist) -->
            @if(isset($chatMessages) && $chatMessages->count() > 0)
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; overflow: hidden;">
                    <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 font-weight-bold text-white">
                            <i class="fas fa-comments mr-2"></i> {{ __('Official Support Chat & Appeal Messages') }}
                        </h6>
                        <span class="badge badge-light text-primary font-weight-bold">{{ $chatMessages->count() }} {{ __('Messages') }}</span>
                    </div>
                    <div class="card-body p-3 p-md-4" style="background: #efeae2; max-height: 380px; overflow-y: auto; display: flex; flex-direction: column; gap: 12px;">
                        @foreach($chatMessages as $msg)
                            @php
                                $isMe = ($msg->sender_type === 'vendor');
                            @endphp
                            <div style="align-self: {{ $isMe ? 'flex-end' : 'flex-start' }}; background: {{ $isMe ? '#d9fdd3' : '#ffffff' }}; color: #111b21; border-radius: {{ $isMe ? '12px 0 12px 12px' : '0 12px 12px 12px' }}; max-width: 82%; padding: 10px 14px; box-shadow: 0 1px 2px rgba(0,0,0,0.12); border-{{ $isMe ? 'right' : 'left' }}: 3px solid {{ $isMe ? '#008069' : '#0d6efd' }};">
                                <div style="font-size: 11px; font-weight: 700; color: {{ $isMe ? '#166534' : '#0d6efd' }}; margin-bottom: 3px;">
                                    {{ $isMe ? __('You (Store Owner)') : __('Administration & Support') }}
                                </div>
                                <div style="white-space: pre-wrap; font-size: 13.5px; line-height: 1.45;">{{ $msg->message }}</div>
                                <div style="font-size: 10.5px; color: #64748b; margin-top: 4px; display: flex; align-items: center; justify-content: flex-end; gap: 3px;">
                                    <span>{{ $msg->created_at ? $msg->created_at->format('M d, h:i A') : '' }}</span>
                                    @if($isMe)
                                        <span style="color: #53bdeb; font-weight: bold; margin-left: 2px;">✓✓</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Tell Us Your Message / Unblock Request Form -->
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; overflow: hidden;">
                <div class="card-header bg-light py-3 border-bottom">
                    <h5 class="mb-0 text-dark font-weight-bold">
                        <i class="fas fa-envelope-open-text text-primary mr-2"></i> {{ __('Tell Us Your Message :') }}
                    </h5>
                    <small class="text-muted">{{ __('Submit your unblock appeal or message to Administration.') }}</small>
                </div>
                <div class="card-body p-4 p-md-5">
                    <form action="{{ route('seller.unblock.submit') }}" method="POST">
                        @csrf

                        <!-- Store Name -->
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark" style="font-size: 13.5px;">{{ __('Store Name') }}</label>
                            <input type="text" class="form-control bg-light" value="{{ $seller ? $seller->shop_name : (Auth::user()->first_name . '\'s Store') }}" readonly style="font-weight: 600;">
                        </div>

                        <div class="row">
                            <!-- First Name -->
                            <div class="col-md-6 form-group mb-3">
                                <label class="font-weight-bold text-dark" style="font-size: 13.5px;">{{ __('First Name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" class="form-control" placeholder="{{ __('First Name') }}" value="{{ old('first_name', (isset($unblockRequest) && $unblockRequest->first_name) ? $unblockRequest->first_name : Auth::user()->first_name) }}" required>
                            </div>

                            <!-- Last Name -->
                            <div class="col-md-6 form-group mb-3">
                                <label class="font-weight-bold text-dark" style="font-size: 13.5px;">{{ __('Last Name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" class="form-control" placeholder="{{ __('Last Name') }}" value="{{ old('last_name', (isset($unblockRequest) && $unblockRequest->last_name) ? $unblockRequest->last_name : Auth::user()->last_name) }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <!-- E-mail -->
                            <div class="col-md-6 form-group mb-3">
                                <label class="font-weight-bold text-dark" style="font-size: 13.5px;">{{ __('E-mail') }} <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" placeholder="{{ __('E-mail') }}" value="{{ old('email', (isset($unblockRequest) && $unblockRequest->email) ? $unblockRequest->email : Auth::user()->email) }}" required>
                            </div>

                            <!-- Phone -->
                            <div class="col-md-6 form-group mb-3">
                                <label class="font-weight-bold text-dark" style="font-size: 13.5px;">{{ __('Phone') }} <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control" placeholder="{{ __('Phone') }}" value="{{ old('phone', (isset($unblockRequest) && $unblockRequest->phone) ? $unblockRequest->phone : Auth::user()->phone) }}" required>
                            </div>
                        </div>

                        <!-- Message -->
                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-dark" style="font-size: 13.5px;">{{ __('Message') }} <span class="text-danger">*</span></label>
                            <textarea name="message" class="form-control" rows="4" placeholder="{{ __('Write your message here...') }}" required style="font-size: 14px;">{{ old('message') }}</textarea>
                            <small class="text-muted">{{ __('Please provide any context or request details regarding unblocking your store.') }}</small>
                        </div>

                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                            <button type="submit" class="btn btn-primary font-weight-bold px-4 py-2">
                                <i class="fas fa-paper-plane mr-2"></i> {{ __('Send Unblock Request / Message') }}
                            </button>

                            <div class="mt-2 mt-sm-0">
                                <a href="{{ route('user.dashboard') }}" class="btn btn-outline-secondary btn-sm mr-1">
                                    <i class="fas fa-user mr-1"></i> {{ __('Customer Dashboard') }}
                                </a>
                                <a href="{{ route('user.logout') }}" class="btn btn-outline-danger btn-sm">
                                    <i class="fas fa-sign-out-alt mr-1"></i> {{ __('Logout') }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

@if(isset($unblockRequest) && $unblockRequest && $unblockRequest->fine_amount > 0)
<!-- Pay Fine Modal (Reusing Deposit Request UI) -->
<div class="modal fade" id="payFineModal" tabindex="-1" role="dialog" aria-labelledby="payFineModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header bg-warning text-dark py-3">
                <h5 class="modal-title font-weight-bold text-dark d-flex align-items-center" id="payFineModalLabel">
                    <i class="fas fa-file-invoice-dollar mr-2"></i> {{ __('Pay Fine — Store Unblock') }}
                </h5>
                <button type="button" class="close text-dark" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" onclick="closePayFineModal()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('seller.fine.submit') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4 bg-light">
                    <!-- Fine Requirement Info Alert -->
                    <div class="alert alert-warning border-0 shadow-sm mb-4" style="border-radius: 8px;">
                        <h6 class="font-weight-bold text-dark mb-1">
                            <i class="fas fa-info-circle mr-1"></i> {{ __('Fine Payment Instructions:') }}
                        </h6>
                        <p class="mb-0 small text-dark">
                            {{ __('Transfer exact fine amount of') }} <strong class="text-danger" style="font-size: 15px;">{{ PriceHelper::adminCurrency() }} {{ number_format($unblockRequest->fine_amount, 2) }}</strong> {{ __('to our official Administration receiving account, then submit your transaction proof.') }}
                        </p>
                    </div>

                    <div class="row">
                        <!-- Locked Fine Amount (Vendor cannot modify) -->
                        <div class="col-md-6 form-group mb-3">
                            <label class="font-weight-bold text-dark">{{ __('Fine Amount Imposed (Fixed)') }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text font-weight-bold bg-white text-dark">{{ PriceHelper::adminCurrency() }}</span>
                                </div>
                                <input type="text" class="form-control font-weight-bold bg-light text-danger" value="{{ number_format($unblockRequest->fine_amount, 2) }}" readonly style="font-size: 16px; font-weight: 700;">
                            </div>
                            <small class="text-muted">{{ __('This amount is fixed by Administration and cannot be altered.') }}</small>
                        </div>

                        <!-- Payment Method / Admin Receiving Account -->
                        <div class="col-md-6 form-group mb-3">
                            <label for="fine_payment_method" class="font-weight-bold text-dark">
                                {{ __('Select Admin Account Sent To') }} <span class="text-danger">*</span>
                            </label>
                            <select name="payment_method" id="fine_payment_method" class="form-control" onchange="onAdminAccountSelect(this, 'modal')" required>
                                <option value="" disabled selected>{{ __('--- Select Admin Account ---') }}</option>
                                @if(isset($receivingAccounts) && $receivingAccounts->count() > 0)
                                    @foreach($receivingAccounts as $acc)
                                        <option value="{{ $acc->payment_method }} - {{ $acc->account_name }} ({{ $acc->account_number }})" data-id="{{ $acc->id }}">
                                            {{ $acc->payment_method }} &mdash; {{ $acc->account_name }} ({{ $acc->account_number }})
                                        </option>
                                    @endforeach
                                @else
                                    <option value="Direct Admin Bank Account">{{ __('Direct Admin Bank / Easypaisa Account') }}</option>
                                @endif
                            </select>
                        </div>
                    </div>

                    <!-- Receiving Accounts Display in Modal (Only Selected One) -->
                    @if(isset($receivingAccounts) && $receivingAccounts->count() > 0)
                        <div class="card shadow-sm border-0 mb-3" style="border-radius: 8px;">
                            <div class="card-header bg-white py-2 font-weight-bold small text-muted">
                                <i class="fas fa-university mr-1 text-primary"></i> {{ __('Official Receiving Account Details:') }}
                            </div>
                            <div class="card-body p-3 bg-white small">
                                <div id="acc_placeholder_modal" class="text-center text-muted py-2">
                                    <span>{{ __('Please select an Admin Account above to view transfer details.') }}</span>
                                </div>
                                @foreach($receivingAccounts as $acc)
                                    <div id="acc_card_modal_{{ $acc->id }}" class="account-card-single-modal d-none">
                                        <div class="p-2 border rounded bg-light d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong class="text-dark d-block">{{ $acc->payment_method }} &mdash; {{ $acc->account_name }}</strong>
                                                <span class="text-muted">{{ __('A/C No:') }}</span> <code class="font-weight-bold text-primary" style="font-size: 14px;">{{ $acc->account_number }}</code>
                                            </div>
                                            <button type="button" class="btn btn-warning btn-sm py-1 px-2 font-weight-bold" onclick="copyAccountNumber('{{ $acc->account_number }}', this)">
                                                <i class="fas fa-copy mr-1"></i> {{ __('Copy') }}
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="row">
                        <!-- Sender Bank / Wallet Name -->
                        <div class="col-md-4 form-group mb-3">
                            <label for="fine_bank_name" class="font-weight-bold text-dark">
                                {{ __('Sent From (Bank / Wallet)') }} <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="bank_name" id="fine_bank_name" class="form-control" placeholder="{{ __('e.g. Easypaisa, HBL, JazzCash') }}" required>
                        </div>

                        <!-- Sender Account Title -->
                        <div class="col-md-4 form-group mb-3">
                            <label for="fine_account_name" class="font-weight-bold text-dark">
                                {{ __('Sender Account Title') }} <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="account_name" id="fine_account_name" class="form-control" placeholder="{{ __('Your Account Title') }}" required>
                        </div>

                        <!-- Sender Account Number -->
                        <div class="col-md-4 form-group mb-3">
                            <label for="fine_account_number" class="font-weight-bold text-dark">
                                {{ __('Sender Account Number') }} <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="account_number" id="fine_account_number" class="form-control" placeholder="{{ __('e.g. 03001234567') }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Transaction ID -->
                        <div class="col-md-6 form-group mb-3">
                            <label for="fine_txn_id" class="font-weight-bold text-dark">
                                {{ __('Transaction ID / Ref No.') }} <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="txn_id" id="fine_txn_id" class="form-control" placeholder="{{ __('e.g. TRX982746123') }}" required>
                        </div>

                        <!-- Screenshot Upload -->
                        <div class="col-md-6 form-group mb-3">
                            <label for="fine_screenshot" class="font-weight-bold text-dark">
                                {{ __('Payment Proof Screenshot') }} <span class="text-danger">*</span>
                            </label>
                            <input type="file" name="screenshot" id="fine_screenshot" class="form-control-file border p-1 rounded bg-white w-100" accept="image/*" required>
                            <small class="text-muted">{{ __('Upload transaction receipt / slip image (Max: 10MB)') }}</small>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-white py-3 d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal" data-bs-dismiss="modal" onclick="closePayFineModal()">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit" class="btn btn-warning text-dark font-weight-bold px-4 py-2 shadow-sm">
                        <i class="fas fa-paper-plane mr-1"></i> {{ __('Submit Fine Payment Proof') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@if(isset($unblockRequest) && $unblockRequest && $unblockRequest->fine_amount > 0 && isset($isBalSufficient) && $isBalSufficient)
<!-- Wallet Pay Confirmation Modal -->
<div class="modal fade" id="walletPayModal" tabindex="-1" role="dialog" aria-labelledby="walletPayModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
            <div class="modal-header bg-success text-white py-3">
                <h5 class="modal-title font-weight-bold text-white d-flex align-items-center" id="walletPayModalLabel">
                    <i class="fas fa-wallet mr-2"></i> {{ __('Pay Fine — Store Unblock') }}
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" onclick="closeWalletPayModal()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4 p-md-5 text-center">
                <div class="mb-4">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light border border-success p-3 mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-wallet fa-2x text-success"></i>
                    </span>
                </div>

                <h5 class="font-weight-bold text-dark mb-3">{{ __('Sufficient amount in balance') }}</h5>

                <div class="card border-success mb-4 mx-auto" style="border-radius: 10px; max-width: 380px; background: #f0fdf4;">
                    <div class="card-body p-3">
                        <div class="mb-2">
                            <span class="text-muted small d-block">{{ __('Fine Amount') }}</span>
                            <h4 class="font-weight-bold text-danger mb-0">{{ PriceHelper::adminCurrency() }} {{ number_format($fineReqAmt, 2) }}</h4>
                        </div>
                        <hr class="my-2">
                        <div class="mb-2">
                            <span class="text-muted small d-block">{{ __('Your Wallet Balance') }}</span>
                            <h5 class="font-weight-bold text-success mb-0">{{ PriceHelper::adminCurrency() }} {{ number_format($vendorBal, 2) }}</h5>
                        </div>
                        <hr class="my-2">
                        <div>
                            <span class="text-muted small d-block">{{ __('Balance After Deduction') }}</span>
                            <h5 class="font-weight-bold text-dark mb-0">{{ PriceHelper::adminCurrency() }} {{ number_format($vendorBal - $fineReqAmt, 2) }}</h5>
                        </div>
                    </div>
                </div>

                <p class="text-dark font-weight-bold mb-1" style="font-size: 15px;">
                    {{ PriceHelper::adminCurrency() }} {{ number_format($fineReqAmt, 2) }} {{ __('will be deducted from your wallet balance.') }}
                </p>
                <small class="text-muted d-block mb-4">
                    <i class="fas fa-info-circle mr-1"></i> {{ __('Your store and all products will be automatically unblocked immediately.') }}
                </small>

                <form action="{{ route('seller.fine.pay_wallet') }}" method="POST" id="walletPayForm">
                    @csrf
                    <button type="submit" class="btn btn-success font-weight-bold px-5 py-3 shadow text-white" style="font-size: 16px; border-radius: 10px;" id="walletPayContinueBtn">
                        <i class="fas fa-check-circle mr-2"></i> {{ __('Continue') }}
                    </button>
                </form>
            </div>
            <div class="modal-footer bg-light py-2 d-flex justify-content-center border-top">
                <button type="button" class="btn btn-outline-secondary font-weight-bold px-4" data-dismiss="modal" data-bs-dismiss="modal" onclick="closeWalletPayModal()">
                    <i class="fas fa-times mr-1"></i> {{ __('Cancel') }}
                </button>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script>
    function onAdminAccountSelect(selectEl, context) {
        const selectedOption = selectEl.options[selectEl.selectedIndex];
        const accId = selectedOption ? selectedOption.getAttribute('data-id') : null;

        if (context === 'inline') {
            const placeholder = document.getElementById('acc_placeholder_inline');
            const allCards = document.querySelectorAll('.account-card-single');
            allCards.forEach(c => c.classList.add('d-none'));

            if (accId) {
                if (placeholder) placeholder.classList.add('d-none');
                const targetCard = document.getElementById('acc_card_inline_' + accId);
                if (targetCard) targetCard.classList.remove('d-none');
            } else {
                if (placeholder) placeholder.classList.remove('d-none');
            }
        } else if (context === 'modal') {
            const placeholder = document.getElementById('acc_placeholder_modal');
            const allCards = document.querySelectorAll('.account-card-single-modal');
            allCards.forEach(c => c.classList.add('d-none'));

            if (accId) {
                if (placeholder) placeholder.classList.add('d-none');
                const targetCard = document.getElementById('acc_card_modal_' + accId);
                if (targetCard) targetCard.classList.remove('d-none');
            } else {
                if (placeholder) placeholder.classList.remove('d-none');
            }
        }
    }

    // Run on document ready to show selected account if pre-selected
    document.addEventListener('DOMContentLoaded', function() {
        const inlineSelect = document.getElementById('inline_fine_payment_method');
        if (inlineSelect && inlineSelect.value) {
            onAdminAccountSelect(inlineSelect, 'inline');
        }
        const modalSelect = document.getElementById('fine_payment_method');
        if (modalSelect && modalSelect.value) {
            onAdminAccountSelect(modalSelect, 'modal');
        }
    });

    function copyAccountNumber(text, btn) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text).then(function() {
                const oldHtml = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-check text-success"></i> Copied!';
                setTimeout(function() { btn.innerHTML = oldHtml; }, 2000);
            }).catch(function() {
                fallbackCopyText(text, btn);
            });
        } else {
            fallbackCopyText(text, btn);
        }
    }

    function fallbackCopyText(text, btn) {
        const textArea = document.createElement("textarea");
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        try {
            document.execCommand('copy');
            const oldHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check text-success"></i> Copied!';
            setTimeout(function() { btn.innerHTML = oldHtml; }, 2000);
        } catch (err) {
            alert('A/C Number: ' + text);
        }
        document.body.removeChild(textArea);
    }

    function previewScreenshot(input, imgId, wrapId) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById(imgId);
                const wrap = document.getElementById(wrapId);
                if (img) img.src = e.target.result;
                if (wrap) wrap.classList.remove('d-none');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function openPayFineModal(e) {
        if (e) e.preventDefault();

        // 1. Scroll smoothly to the inline fine payment section if present
        const inlineCard = document.getElementById('payFineInlineCard');
        if (inlineCard) {
            inlineCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
            inlineCard.classList.add('pulse-glow');
            setTimeout(function() {
                inlineCard.classList.remove('pulse-glow');
            }, 3000);
            
            // Focus on the first form input
            const firstInput = document.getElementById('inline_fine_payment_method') || document.getElementById('inline_fine_bank_name');
            if (firstInput) {
                firstInput.focus();
            }
            return;
        }

        // 2. Try modal display if inline section not available
        const modalEl = document.getElementById('payFineModal');
        if (!modalEl) return;

        if (window.jQuery && typeof jQuery(modalEl).modal === 'function') {
            jQuery(modalEl).modal('show');
        } else if (window.bootstrap && typeof bootstrap.Modal === 'function') {
            const bModal = new bootstrap.Modal(modalEl);
            bModal.show();
        } else {
            modalEl.classList.add('show');
            modalEl.style.display = 'block';
            modalEl.removeAttribute('aria-hidden');
            modalEl.setAttribute('aria-modal', 'true');
            
            let backdrop = document.getElementById('custom-fine-backdrop');
            if (!backdrop) {
                backdrop = document.createElement('div');
                backdrop.id = 'custom-fine-backdrop';
                backdrop.className = 'modal-backdrop fade show';
                document.body.appendChild(backdrop);
            }
        }
    }

    function closePayFineModal() {
        const modalEl = document.getElementById('payFineModal');
        if (!modalEl) return;

        if (window.jQuery && typeof jQuery(modalEl).modal === 'function') {
            jQuery(modalEl).modal('hide');
        } else if (window.bootstrap && typeof bootstrap.Modal === 'function') {
            const bModal = bootstrap.Modal.getInstance(modalEl);
            if (bModal) bModal.hide();
        } else {
            modalEl.classList.remove('show');
            modalEl.style.display = 'none';
            modalEl.setAttribute('aria-hidden', 'true');
            modalEl.removeAttribute('aria-modal');
            
            const backdrop = document.getElementById('custom-fine-backdrop');
            if (backdrop) backdrop.remove();
        }
    }

    function openWalletPayModal(e) {
        if (e) e.preventDefault();
        const modalEl = document.getElementById('walletPayModal');
        if (!modalEl) return;

        if (window.jQuery && typeof jQuery(modalEl).modal === 'function') {
            jQuery(modalEl).modal('show');
        } else if (window.bootstrap && typeof bootstrap.Modal === 'function') {
            const bModal = new bootstrap.Modal(modalEl);
            bModal.show();
        } else {
            modalEl.classList.add('show');
            modalEl.style.display = 'block';
            modalEl.removeAttribute('aria-hidden');
            modalEl.setAttribute('aria-modal', 'true');
            
            let backdrop = document.getElementById('custom-wallet-backdrop');
            if (!backdrop) {
                backdrop = document.createElement('div');
                backdrop.id = 'custom-wallet-backdrop';
                backdrop.className = 'modal-backdrop fade show';
                document.body.appendChild(backdrop);
            }
        }
    }

    function closeWalletPayModal() {
        const modalEl = document.getElementById('walletPayModal');
        if (!modalEl) return;

        if (window.jQuery && typeof jQuery(modalEl).modal === 'function') {
            jQuery(modalEl).modal('hide');
        } else if (window.bootstrap && typeof bootstrap.Modal === 'function') {
            const bModal = bootstrap.Modal.getInstance(modalEl);
            if (bModal) bModal.hide();
        } else {
            modalEl.classList.remove('show');
            modalEl.style.display = 'none';
            modalEl.setAttribute('aria-hidden', 'true');
            modalEl.removeAttribute('aria-modal');
            
            const backdrop = document.getElementById('custom-wallet-backdrop');
            if (backdrop) backdrop.remove();
        }
    }
</script>
@endsection