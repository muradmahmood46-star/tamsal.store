@extends('master.front')

@section('title')
    {{ __('Store Application Status') }}
@endsection

@section('content')
<!-- Page Title-->
<div class="page-title">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <ul class="breadcrumbs">
                    <li><a href="{{ route('front.index') }}">{{ __('Home') }}</a></li>
                    <li class="separator"></li>
                    <li><a href="{{ route('user.dashboard') }}">{{ __('Dashboard') }}</a></li>
                    <li class="separator"></li>
                    <li>{{ __('Store Application Status') }}</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Page Content-->
<div class="container padding-bottom-3x mb-2">
    <div class="row">
        @include('includes.user_sitebar')

        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 text-white"><i class="icon-clock mr-2"></i> {{ __('Store Application Status') }}</h5>
                    @if($latestRequest->status == 'Pending')
                        <span class="badge badge-warning text-dark px-3 py-2 font-weight-bold">
                            <i class="icon-clock"></i> {{ __('PENDING APPROVAL') }}
                        </span>
                    @elseif($latestRequest->status == 'Rejected')
                        <span class="badge badge-danger px-3 py-2 font-weight-bold">
                            <i class="icon-x-circle"></i> {{ __('APPLICATION REJECTED') }}
                        </span>
                    @elseif($latestRequest->status == 'Approved')
                        <span class="badge badge-success px-3 py-2 font-weight-bold">
                            <i class="icon-check-circle"></i> {{ __('APPROVED') }}
                        </span>
                    @endif
                </div>

                <div class="card-body p-4">
                    @include('alerts.alerts')

                    @if($latestRequest->status == 'Pending')
                        <div class="text-center py-4">
                            <div class="mb-3">
                                <span class="d-inline-flex align-items-center justify-content-center bg-warning text-white rounded-circle" style="width: 80px; height: 80px; font-size: 38px;">
                                    <i class="icon-clock"></i>
                                </span>
                            </div>
                            <h4 class="font-weight-bold text-dark mb-2">{{ __('Your Store Application is Under Review') }}</h4>
                            <p class="text-muted mx-auto" style="max-width: 550px;">
                                {{ __('Thank you for applying to open a store with us! Our administration team is currently reviewing your personal information, identification documents, and store details. You will be notified as soon as your shop is approved.') }}
                            </p>
                        </div>

                    @elseif($latestRequest->status == 'Rejected')
                        <div class="text-center py-4">
                            <div class="mb-3">
                                <span class="d-inline-flex align-items-center justify-content-center bg-danger text-white rounded-circle" style="width: 80px; height: 80px; font-size: 38px;">
                                    <i class="icon-x"></i>
                                </span>
                            </div>
                            <h4 class="font-weight-bold text-danger mb-2">{{ __('Store Application Not Approved') }}</h4>
                            <p class="text-muted mx-auto" style="max-width: 550px;">
                                {{ __('Unfortunately, your store application could not be approved at this time. Please review the reason below and submit an updated application.') }}
                            </p>

                            @if($latestRequest->reject_reason)
                                <div class="alert alert-danger text-left mx-auto my-3" style="max-width: 600px;">
                                    <h6 class="font-weight-bold mb-1"><i class="icon-alert-triangle"></i> {{ __('Admin Rejection Note / Reason:') }}</h6>
                                    <p class="mb-0">{{ $latestRequest->reject_reason }}</p>
                                </div>
                            @endif

                            <div class="mt-4">
                                <a href="{{ route('user.store.apply', ['reapply' => 1]) }}" class="btn btn-primary px-4 py-2 font-weight-bold">
                                    <i class="icon-refresh-cw"></i> {{ __('Re-Apply / Update Store Application') }}
                                </a>
                            </div>
                        </div>

                    @elseif($latestRequest->status == 'Approved')
                        <div class="text-center py-4">
                            <div class="mb-3">
                                <span class="d-inline-flex align-items-center justify-content-center bg-success text-white rounded-circle" style="width: 80px; height: 80px; font-size: 38px;">
                                    <i class="icon-check"></i>
                                </span>
                            </div>
                            <h4 class="font-weight-bold text-success mb-2">{{ __('Congratulations! Your Store is Approved') }}</h4>
                            <p class="text-muted mx-auto" style="max-width: 550px;">
                                {{ __('Your seller account is active. You can now manage your store, add products, view orders, and manage categories.') }}
                            </p>
                            <div class="mt-4">
                                <a href="{{ route('seller.dashboard') }}" class="btn btn-success px-4 py-2 font-weight-bold">
                                    <i class="icon-layout"></i> {{ __('Go to Seller Dashboard') }}
                                </a>
                            </div>
                        </div>
                    @endif

                    <!-- Application Details Summary -->
                    <div class="border rounded p-3 mt-4 bg-light">
                        <h6 class="font-weight-bold text-dark border-bottom pb-2 mb-3">
                            <i class="icon-list mr-1"></i> {{ __('Submitted Application Summary') }}
                        </h6>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <span class="text-muted">{{ __('Store Name:') }}</span> <strong>{{ $latestRequest->shop_name }}</strong>
                            </div>
                            <div class="col-md-6 mb-2">
                                <span class="text-muted">{{ __('Applicant Name:') }}</span> <strong>{{ $latestRequest->first_name }} {{ $latestRequest->last_name }}</strong>
                            </div>
                            <div class="col-md-6 mb-2">
                                <span class="text-muted">{{ __('Phone:') }}</span> <strong>{{ $latestRequest->phone }}</strong>
                            </div>
                            <div class="col-md-6 mb-2">
                                <span class="text-muted">{{ __('Email:') }}</span> <strong>{{ $latestRequest->email }}</strong>
                            </div>
                            <div class="col-md-6 mb-2">
                                <span class="text-muted">{{ __('CNIC / ID:') }}</span> <strong>{{ $latestRequest->cnic }}</strong>
                            </div>
                            <div class="col-md-6 mb-2">
                                <span class="text-muted">{{ __('Application Date:') }}</span> <strong>{{ $latestRequest->created_at ? $latestRequest->created_at->format('M d, Y h:i A') : __('Just now') }}</strong>
                            </div>
                            <div class="col-md-12 mb-2">
                                <span class="text-muted">{{ __('Store Location:') }}</span> <strong>{{ $latestRequest->shop_address }}</strong>
                            </div>

                            @if($latestRequest->is_free == 0)
                                <div class="col-md-12 mt-2 pt-2 border-top">
                                    <h6 class="font-weight-bold text-dark mb-2">{{ __('Payment Proof Details:') }}</h6>
                                    <div class="row">
                                        <div class="col-md-6 mb-1">
                                            <span class="text-muted">{{ __('Payment Method:') }}</span> <strong>{{ $latestRequest->account_type }}</strong>
                                        </div>
                                        <div class="col-md-6 mb-1">
                                            <span class="text-muted">{{ __('Sender Account:') }}</span> <strong>{{ $latestRequest->account_name }} ({{ $latestRequest->account_number }})</strong>
                                        </div>
                                        <div class="col-md-6 mb-1">
                                            <span class="text-muted">{{ __('Transaction ID:') }}</span> <strong>{{ $latestRequest->transaction_id }}</strong>
                                        </div>
                                        <div class="col-md-6 mb-1">
                                            <span class="text-muted">{{ __('Store Fee:') }}</span> <strong>{{ PriceHelper::storeOpeningFee($latestRequest->store_fee) }}</strong>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
