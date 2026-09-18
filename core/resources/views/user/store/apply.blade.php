@extends('master.front')

@section('title')
    {{ __('Open Shop / List Product') }}
@endsection

@section('styles')
<style>
    /* Wizard Step Content - Strictly 1 step visible at a time */
    .wizard-step-content {
        display: none !important;
    }
    .wizard-step-content.active {
        display: block !important;
        animation: wizardFadeIn 0.25s ease-in-out;
    }
    @keyframes wizardFadeIn {
        from { opacity: 0; transform: translateY(6px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .doc-upload-card {
        border: 2px dashed #ced4da;
        border-radius: 8px;
        transition: border-color 0.2s, background-color 0.2s;
        background: #fdfdfd;
    }
    .doc-upload-card.has-file {
        border-color: #28a745 !important;
        background: #f8fff9 !important;
    }
    .doc-upload-card.border-danger {
        border-color: #dc3545 !important;
        background: #fff8f8 !important;
    }

    .form-control.is-invalid, .custom-select.is-invalid {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.15rem rgba(220, 53, 69, 0.25) !important;
    }

    .account-card {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .account-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    }

    /* Wizard Navigation Buttons - Mobile Responsive */
    .wizard-nav-btns {
        margin-top: 1.75rem !important;
        padding-top: 1.25rem !important;
        border-top: 1px solid #e9ecef;
        position: relative;
        z-index: 5;
    }

    @media (max-width: 767.98px) {
        .wizard-nav-btns {
            display: flex !important;
            flex-direction: column-reverse !important;
            gap: 12px !important;
            width: 100% !important;
        }

        .wizard-nav-btns .btn,
        .wizard-nav-btns a.btn {
            width: 100% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 13px 20px !important;
            font-size: 15px !important;
            font-weight: 600 !important;
            border-radius: 8px !important;
            margin: 0 !important;
            text-align: center !important;
            box-sizing: border-box !important;
            min-height: 48px !important;
        }

        .wizard-nav-btns .wizard-btn-next {
            order: 1 !important;
            font-weight: 700 !important;
            font-size: 16px !important;
            box-shadow: 0 4px 14px rgba(13, 110, 253, 0.3) !important;
        }

        .wizard-nav-btns .wizard-btn-prev {
            order: 2 !important;
            background-color: #f8f9fa !important;
            color: #495057 !important;
            border: 1px solid #ced4da !important;
        }

        .wizard-nav-btns .btn-success.wizard-btn-next {
            box-shadow: 0 4px 14px rgba(40, 167, 69, 0.35) !important;
        }
    }

    /* Document & Camera Upload Action Buttons */
    .doc-btn-group {
        display: flex !important;
        gap: 8px !important;
        align-items: center !important;
        width: 100% !important;
    }
    .doc-btn-group .btn-upload-file {
        flex: 1 1 0 !important;
        min-width: 0 !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        padding-left: 6px !important;
        padding-right: 6px !important;
        text-align: center !important;
    }
    .doc-btn-group .btn-cam-snap {
        flex: 0 0 auto !important;
        white-space: nowrap !important;
        padding-left: 10px !important;
        padding-right: 10px !important;
    }

    /* Enhanced Animated Hero Banner */
    .store-hero-banner {
        display: flex !important;
        align-items: center !important;
        padding: 16px 20px !important;
        border-radius: 12px !important;
        margin-bottom: 1.5rem !important;
        background: linear-gradient(135deg, #f0f7ff 0%, #ffffff 100%) !important;
        border: 1px solid #bfdbfe !important;
        border-left: 5px solid #0d6efd !important;
        box-shadow: 0 4px 18px rgba(13, 110, 253, 0.07) !important;
        position: relative !important;
        overflow: hidden !important;
        transition: transform 0.25s ease, box-shadow 0.25s ease !important;
    }
    .store-hero-banner:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 22px rgba(13, 110, 253, 0.12) !important;
    }
    .store-hero-banner.is-free-banner {
        background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%) !important;
        border-color: #bbf7d0 !important;
        border-left-color: #16a34a !important;
        box-shadow: 0 4px 18px rgba(22, 163, 74, 0.07) !important;
    }
    .store-hero-icon-wrap {
        width: 52px !important;
        height: 52px !important;
        min-width: 52px !important;
        max-width: 52px !important;
        border-radius: 50% !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        flex-shrink: 0 !important;
        margin-right: 18px !important;
        background: linear-gradient(135deg, #0d6efd 0%, #2563eb 100%) !important;
        box-shadow: 0 4px 14px rgba(13, 110, 253, 0.35) !important;
        animation: pulseIconGlow 2.5s infinite ease-in-out !important;
    }
    .is-free-banner .store-hero-icon-wrap {
        background: linear-gradient(135deg, #16a34a 0%, #15803d 100%) !important;
        box-shadow: 0 4px 14px rgba(22, 163, 74, 0.35) !important;
        animation: pulseFreeIconGlow 2.5s infinite ease-in-out !important;
    }
    .store-hero-icon-wrap i {
        color: #ffffff !important;
        font-size: 22px !important;
        line-height: 1 !important;
    }
    .store-hero-content {
        flex: 1 1 auto !important;
        min-width: 0 !important;
    }
    .store-hero-title {
        font-size: 16px !important;
        font-weight: 700 !important;
        color: #1e293b !important;
        margin-bottom: 4px !important;
        letter-spacing: -0.2px !important;
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
        flex-wrap: wrap !important;
    }
    .store-hero-subtitle {
        font-size: 13.5px !important;
        color: #475569 !important;
        line-height: 1.45 !important;
        margin-bottom: 0 !important;
    }
    .store-hero-badge {
        font-size: 11px !important;
        font-weight: 700 !important;
        padding: 3px 8px !important;
        border-radius: 20px !important;
        text-transform: uppercase !important;
        letter-spacing: 0.4px !important;
        background: rgba(13, 110, 253, 0.12) !important;
        color: #0d6efd !important;
    }
    .is-free-banner .store-hero-badge {
        background: rgba(22, 163, 74, 0.12) !important;
        color: #16a34a !important;
    }

    @keyframes pulseIconGlow {
        0%, 100% {
            transform: scale(1);
            box-shadow: 0 4px 14px rgba(13, 110, 253, 0.35);
        }
        50% {
            transform: scale(1.06);
            box-shadow: 0 6px 20px rgba(13, 110, 253, 0.55);
        }
    }
    @keyframes pulseFreeIconGlow {
        0%, 100% {
            transform: scale(1);
            box-shadow: 0 4px 14px rgba(22, 163, 74, 0.35);
        }
        50% {
            transform: scale(1.06);
            box-shadow: 0 6px 20px rgba(22, 163, 74, 0.55);
        }
    }

    @media (max-width: 575.98px) {
        .store-hero-banner {
            padding: 14px 14px !important;
            align-items: flex-start !important;
        }
        .store-hero-icon-wrap {
            width: 44px !important;
            height: 44px !important;
            min-width: 44px !important;
            max-width: 44px !important;
            margin-right: 12px !important;
            margin-top: 2px !important;
        }
        .store-hero-icon-wrap i {
            font-size: 18px !important;
        }
        .store-hero-title {
            font-size: 15px !important;
        }
        .store-hero-subtitle {
            font-size: 12.5px !important;
        }
    }

    /* Camera Modal Responsive Styles */
    .cam-modal-footer {
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        padding: 12px 16px !important;
        background: #f8fafc !important;
        border-top: 1px solid #e2e8f0 !important;
        width: 100% !important;
        box-sizing: border-box !important;
    }
    .cam-live-controls, .cam-snap-controls {
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
    }
    .cam-live-controls.d-none, .cam-snap-controls.d-none {
        display: none !important;
    }

    @media (max-width: 575.98px) {
        #cameraModal .modal-dialog {
            margin: 8px !important;
            max-width: calc(100% - 16px) !important;
        }
        #cameraModal .modal-body {
            padding: 10px !important;
        }
        #cameraLiveWrap, #cameraSnapWrap {
            height: 260px !important;
        }
        .cam-modal-footer {
            flex-direction: column-reverse !important;
            gap: 10px !important;
            padding: 12px 14px !important;
        }
        .cam-modal-footer .btn-cam-cancel {
            width: 100% !important;
            order: 3 !important;
            margin: 0 !important;
            padding: 10px !important;
            font-size: 14px !important;
        }
        .cam-live-controls, .cam-snap-controls {
            width: 100% !important;
            flex-direction: column !important;
            gap: 8px !important;
        }
        .cam-live-controls .btn,
        .cam-snap-controls .btn {
            width: 100% !important;
            margin: 0 !important;
            padding: 12px 14px !important;
            font-size: 15px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            box-sizing: border-box !important;
        }
        .cam-live-controls .btn-snap-photo {
            order: 1 !important;
            padding: 13px !important;
            font-size: 16px !important;
            font-weight: 700 !important;
            box-shadow: 0 4px 14px rgba(40, 167, 69, 0.4) !important;
        }
        .cam-live-controls .btn-switch-cam {
            order: 2 !important;
            background: #ffffff !important;
        }
        .cam-snap-controls .btn-confirm-photo {
            order: 1 !important;
            padding: 13px !important;
            font-size: 16px !important;
            font-weight: 700 !important;
            box-shadow: 0 4px 14px rgba(13, 110, 253, 0.4) !important;
        }
        .cam-snap-controls .btn-retake-photo {
            order: 2 !important;
        }
    }

    /* Payment Method Selector Grid & Compact Sleek Cards */
    .payment-methods-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(96px, 1fr));
        gap: 8px;
        margin-bottom: 12px;
    }
    .payment-method-card {
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        padding: 7px 4px;
        text-align: center;
        background: #ffffff;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
        position: relative;
        user-select: none;
    }
    .payment-method-card:hover {
        border-color: #0d6efd;
        transform: translateY(-2px);
        box-shadow: 0 3px 10px rgba(13, 110, 253, 0.12);
    }
    .payment-method-card.selected {
        border-color: #0d6efd !important;
        background: #f0f7ff !important;
        box-shadow: 0 3px 12px rgba(13, 110, 253, 0.22) !important;
    }
    .payment-method-card .method-icon-wrap {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: #eef2f6;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 3px;
        color: #0d6efd;
        font-size: 13px;
        transition: all 0.2s ease;
    }
    .payment-method-card.selected .method-icon-wrap {
        background: #0d6efd;
        color: #ffffff;
    }
    .payment-method-card .method-name {
        font-size: 11.5px;
        font-weight: 700;
        color: #1e293b;
        display: block;
        line-height: 1.15;
        margin-bottom: 1px;
    }
    .payment-method-card .method-tag {
        font-size: 9px;
        color: #64748b;
        display: block;
        line-height: 1.1;
    }
    .payment-method-card .method-check {
        position: absolute;
        top: -4px;
        right: -4px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: #0d6efd;
        color: #ffffff;
        display: none;
        align-items: center;
        justify-content: center;
        font-size: 8px;
        box-shadow: 0 2px 5px rgba(13, 110, 253, 0.4);
    }
    .payment-method-card.selected .method-check {
        display: flex !important;
    }

    .selected-admin-account-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border: 2px solid #0d6efd;
        border-radius: 12px;
        padding: 14px 16px;
        box-shadow: 0 4px 16px rgba(13, 110, 253, 0.1);
        animation: fadeInCard 0.25s ease-in-out;
    }
    @keyframes fadeInCard {
        from { opacity: 0; transform: translateY(-6px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Enhanced Account Title Badge with Vibrant Colors */
    .account-title-badge {
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        border: 1.5px solid #93c5fd;
        border-radius: 8px;
        padding: 6px 12px;
        display: inline-flex;
        align-items: center;
        box-shadow: 0 2px 6px rgba(37, 99, 235, 0.1);
        max-width: 100%;
    }
    .account-title-text {
        color: #1e40af !important;
        font-size: 14.5px !important;
        font-weight: 700 !important;
        letter-spacing: -0.2px;
        word-break: break-word;
    }

    /* Single Line Label on Mobile Viewport */
    .acc-single-line-heading {
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        font-size: 11.5px !important;
        letter-spacing: -0.1px !important;
    }

    /* Refined Instruction Note Card & Circular Icon */
    .admin-instruction-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-left: 4px solid #0d6efd;
        border-radius: 8px;
        padding: 10px 12px;
        display: flex;
        align-items: flex-start;
        gap: 10px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.03);
    }
    .instruction-icon-circle {
        width: 26px;
        height: 26px;
        min-width: 26px;
        max-width: 26px;
        border-radius: 50%;
        background: rgba(13, 110, 253, 0.12);
        border: 1px solid rgba(13, 110, 253, 0.25);
        color: #0d6efd;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        flex-shrink: 0;
        margin-top: 1px;
    }
    .instruction-content {
        flex: 1 1 auto;
        min-width: 0;
    }
    .instruction-title {
        font-size: 12px;
        font-weight: 700;
        color: #1e293b;
        display: block;
        margin-bottom: 2px;
    }
    .instruction-text {
        font-size: 12px;
        color: #475569;
        line-height: 1.45;
        margin-bottom: 0;
    }
</style>
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
                    <li>{{ __('Open Shop / List Product') }}</li>
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
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0 text-white"><i class="icon-shopping-bag mr-2"></i> {{ __('Open Shop / Seller Application') }}</h5>
                </div>

                <div class="card-body p-4">
                    @include('alerts.alerts')

                    @php
                        $isFree = (isset($setting) && $setting->is_store_opening_free == 1);
                        $totalSteps = $isFree ? 3 : 4;
                    @endphp

                    @if($isFree)
                        <div class="store-hero-banner is-free-banner">
                            <div class="store-hero-icon-wrap">
                                <i class="fas fa-gift"></i>
                            </div>
                            <div class="store-hero-content">
                                <div class="store-hero-title">
                                    {{ __('Free Store Opening Offer is Active!') }}
                                    <span class="store-hero-badge">{{ __('Free') }}</span>
                                </div>
                                <p class="store-hero-subtitle">
                                    {{ __('Open your store without any fees. Complete the 3-step application below to get your shop approved.') }}
                                </p>
                            </div>
                        </div>
                    @else
                        <div class="store-hero-banner">
                            <div class="store-hero-icon-wrap">
                                <i class="fas fa-store"></i>
                            </div>
                            <div class="store-hero-content">
                                <div class="store-hero-title">
                                    {{ __('Open Shop / Seller Application') }}
                                    <span class="store-hero-badge">{{ __('Easy Steps') }}</span>
                                </div>
                                <p class="store-hero-subtitle">
                                    {{ __('Complete steps 1-4 and submit your application to open your store and list your products.') }}
                                </p>
                            </div>
                        </div>
                    @endif



                    <!-- MULTI-STEP FORM -->
                    <form action="{{ route('user.store.submit') }}" method="POST" enctype="multipart/form-data" id="storeApplyForm" novalidate>
                        @csrf

                        <!-- ============================================== -->
                        <!-- STEP 1: PERSONAL INFORMATION                   -->
                        <!-- ============================================== -->
                        <div class="wizard-step-content active" id="stepContent1" style="display: block;">
                            <div class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-3">
                                <h6 class="text-primary font-weight-bold mb-0">
                                    <i class="icon-user mr-1"></i> {{ __('Step 1: Personal Information') }}
                                </h6>
                                <span class="badge badge-light border text-muted">{{ __('1 of ') . $totalSteps }}</span>
                            </div>
                            <p class="text-muted font-size-sm mb-4">{{ __('Please enter your basic identity and contact details. All fields are required.') }}</p>

                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label for="first_name">{{ __('First Name') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="first_name" id="first_name" class="form-control" value="{{ old('first_name', $user->first_name) }}" placeholder="{{ __('Enter First Name') }}" required>
                                    <div class="invalid-feedback">{{ __('Please enter your first name.') }}</div>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="last_name">{{ __('Last Name') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="last_name" id="last_name" class="form-control" value="{{ old('last_name', $user->last_name) }}" placeholder="{{ __('Enter Last Name') }}" required>
                                    <div class="invalid-feedback">{{ __('Please enter your last name.') }}</div>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="phone">{{ __('Phone Number') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $user->phone) }}" placeholder="{{ __('e.g. 03001234567') }}" required>
                                    <div class="invalid-feedback">{{ __('Please enter a valid phone number (at least 7 digits).') }}</div>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="email">{{ __('Email Address') }} <span class="text-danger">*</span></label>
                                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}" placeholder="{{ __('Enter Email Address') }}" required>
                                    <div class="invalid-feedback">{{ __('Please enter a valid email address.') }}</div>
                                </div>
                                <div class="col-md-12 form-group">
                                    <label for="cnic">{{ __('ID Card Number (CNIC / National ID)') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="cnic" id="cnic" class="form-control" value="{{ old('cnic', $latestRequest->cnic ?? '') }}" placeholder="{{ __('e.g. 35201-1234567-1') }}" required>
                                    <div class="invalid-feedback">{{ __('Please enter your ID card / CNIC number.') }}</div>
                                </div>
                            </div>

                            <div class="wizard-nav-btns d-flex justify-content-between align-items-center pt-3 border-top mt-4">
                                <a href="{{ route('user.dashboard') }}" class="btn btn-outline-secondary wizard-btn-prev">
                                    <i class="icon-x mr-1"></i> {{ __('Cancel') }}
                                </a>
                                <button type="button" class="btn btn-primary px-4 font-weight-bold wizard-btn-next" id="step1NextBtn" onclick="nextStep(1)">
                                    {{ __('Next: Store Info') }} <i class="icon-arrow-right ml-1"></i>
                                </button>
                            </div>
                        </div>

                        <!-- ============================================== -->
                        <!-- STEP 2: STORE INFORMATION                      -->
                        <!-- ============================================== -->
                        <div class="wizard-step-content" id="stepContent2" style="display: none;">
                            <div class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-3">
                                <h6 class="text-primary font-weight-bold mb-0">
                                    <i class="icon-shopping-bag mr-1"></i> {{ __('Step 2: Store Information') }}
                                </h6>
                                <span class="badge badge-light border text-muted">{{ __('2 of ') . $totalSteps }}</span>
                            </div>
                            <p class="text-muted font-size-sm mb-4">{{ __('Provide your shop name and business physical location or address.') }}</p>

                            <div class="row">
                                <div class="col-md-12 form-group">
                                    <label for="shop_name">{{ __('Store Name / Shop Name') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="shop_name" id="shop_name" class="form-control" value="{{ old('shop_name', $latestRequest->shop_name ?? '') }}" placeholder="{{ __('e.g. Al-Madina Super Store') }}" required>
                                    <div class="invalid-feedback">{{ __('Please enter your store / shop name.') }}</div>
                                    <small class="text-muted">{{ __('This name will be displayed publicly on your store profile and products.') }}</small>
                                </div>
                                <div class="col-md-12 form-group">
                                    <label for="product_types">{{ __('Product Types / Categories') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="product_types" id="product_types" class="form-control" value="{{ old('product_types', $latestRequest->product_types ?? '') }}" placeholder="{{ __('e.g. Electronics, Men\'s Clothing, Fashion, Groceries') }}" required>
                                    <div class="invalid-feedback">{{ __('Please specify your product types (e.g. Electronics, Men\'s Clothing).') }}</div>
                                    <small class="text-muted">{{ __('Suggested: Electronics, Men\'s Clothing, Shoes, Accessories, etc.') }}</small>
                                </div>
                                <div class="col-md-12 form-group">
                                    <label for="courier_company">{{ __('Which courier company will you use for delivery?') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="courier_company" id="courier_company" class="form-control" value="{{ old('courier_company', $latestRequest->courier_company ?? '') }}" placeholder="{{ __('e.g. TCS, Leopard, TRAX') }}" required>
                                    <div class="invalid-feedback">{{ __('Please specify the courier company you will use for delivery.') }}</div>
                                    <small class="text-muted">{{ __('Suggested: TCS, Leopard, TRAX, PostEx, M&P, Call Courier, etc.') }}</small>
                                </div>
                                <div class="col-md-12 form-group">
                                    <label for="shop_address">{{ __('Store Location / Complete Address') }} <span class="text-danger">*</span></label>
                                    <textarea name="shop_address" id="shop_address" rows="3" class="form-control" placeholder="{{ __('Shop #, Street, Plaza/Market, City, Province') }}" required>{{ old('shop_address', $latestRequest->shop_address ?? '') }}</textarea>
                                    <div class="invalid-feedback">{{ __('Please enter your complete shop / store address.') }}</div>
                                </div>
                            </div>

                            <div class="wizard-nav-btns d-flex justify-content-between align-items-center pt-3 border-top mt-4">
                                <button type="button" class="btn btn-outline-secondary wizard-btn-prev" onclick="prevStep(2)">
                                    <i class="icon-arrow-left mr-1"></i> {{ __('Previous: Personal Info') }}
                                </button>
                                <button type="button" class="btn btn-primary px-4 font-weight-bold wizard-btn-next" id="step2NextBtn" onclick="nextStep(2)">
                                    {{ __('Next: Documents') }} <i class="icon-arrow-right ml-1"></i>
                                </button>
                            </div>
                        </div>

                        <!-- ============================================== -->
                        <!-- STEP 3: VERIFICATION DOCUMENTS                 -->
                        <!-- ============================================== -->
                        <div class="wizard-step-content" id="stepContent3" style="display: none;">
                            <div class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-3">
                                <h6 class="text-primary font-weight-bold mb-0">
                                    <i class="icon-file-text mr-1"></i> {{ __('Step 3: Verification Documents') }}
                                </h6>
                                <span class="badge badge-light border text-muted">{{ __('3 of ') . $totalSteps }}</span>
                            </div>
                            <p class="text-muted font-size-sm mb-4">{{ __('Upload clear photos or use your camera to capture verification documents. All 3 items are required.') }}</p>

                            <div class="row">
                                <!-- ID Card Picture -->
                                <div class="col-md-6 mb-4">
                                    <div class="p-3 doc-upload-card h-100" id="card_id_card_front">
                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                            <label class="font-weight-bold mb-0 text-dark">{{ __('ID Card Picture') }} <span class="text-danger">*</span></label>
                                            <span class="badge badge-secondary doc-status-badge" id="badge_id_card_front">{{ __('Required') }}</span>
                                        </div>
                                        <small class="text-muted d-block mb-2">{{ __('Clear photo of your ID Card / CNIC (Front)') }}</small>
                                        
                                        <div class="preview-box mb-2 text-center p-2 bg-white rounded border" style="min-height: 120px; display: flex; align-items: center; justify-content: center;">
                                            @if(!empty($latestRequest->id_card_front))
                                                <img src="{{ asset('core/public/storage/images/stores/' . $latestRequest->id_card_front) }}" class="img-fluid rounded" style="max-height: 110px;" id="preview_id_card_front">
                                                <span class="text-muted font-italic d-none" id="no_img_id_card_front">{{ __('No image selected') }}</span>
                                            @else
                                                <span class="text-muted font-italic" id="no_img_id_card_front">{{ __('No image selected') }}</span>
                                                <img src="" class="img-fluid rounded d-none" style="max-height: 110px;" id="preview_id_card_front">
                                            @endif
                                        </div>

                                        <div class="doc-btn-group">
                                            <label class="btn btn-outline-primary btn-sm btn-upload-file mb-0 cursor-pointer" for="id_card_front">
                                                <i class="icon-upload mr-1"></i> {{ __('Upload File') }}
                                            </label>
                                            <input type="file" name="id_card_front" id="id_card_front" class="d-none" accept="image/*" onchange="handleFileSelected(this, 'id_card_front')">
                                            
                                            <button type="button" class="btn btn-primary btn-sm btn-cam-snap" onclick="openCamModal('id_card_front')">
                                                <i class="icon-camera mr-1"></i> {{ __('Camera') }}
                                            </button>
                                        </div>
                                        <input type="hidden" name="id_card_front_cam" id="id_card_front_cam">
                                        <input type="hidden" id="has_existing_id_card_front" value="{{ !empty($latestRequest->id_card_front) ? '1' : '0' }}">
                                        <div class="text-danger font-size-sm mt-2 d-none" id="error_id_card_front">
                                            <i class="icon-alert-circle mr-1"></i> {{ __('Please upload or take a photo of your ID Card.') }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Selfie with ID Card -->
                                <div class="col-md-6 mb-4">
                                    <div class="p-3 doc-upload-card h-100" id="card_selfie_with_id">
                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                            <label class="font-weight-bold mb-0 text-dark">{{ __('Selfie with ID Card') }} <span class="text-danger">*</span></label>
                                            <span class="badge badge-secondary doc-status-badge" id="badge_selfie_with_id">{{ __('Required') }}</span>
                                        </div>
                                        <small class="text-muted d-block mb-2">{{ __('Hold your ID card next to your face') }}</small>
                                        
                                        <div class="preview-box mb-2 text-center p-2 bg-white rounded border" style="min-height: 120px; display: flex; align-items: center; justify-content: center;">
                                            @if(!empty($latestRequest->selfie_with_id))
                                                <img src="{{ asset('core/public/storage/images/stores/' . $latestRequest->selfie_with_id) }}" class="img-fluid rounded" style="max-height: 110px;" id="preview_selfie_with_id">
                                                <span class="text-muted font-italic d-none" id="no_img_selfie_with_id">{{ __('No image selected') }}</span>
                                            @else
                                                <span class="text-muted font-italic" id="no_img_selfie_with_id">{{ __('No image selected') }}</span>
                                                <img src="" class="img-fluid rounded d-none" style="max-height: 110px;" id="preview_selfie_with_id">
                                            @endif
                                        </div>

                                        <div class="doc-btn-group">
                                            <label class="btn btn-outline-primary btn-sm btn-upload-file mb-0 cursor-pointer" for="selfie_with_id">
                                                <i class="icon-upload mr-1"></i> {{ __('Upload File') }}
                                            </label>
                                            <input type="file" name="selfie_with_id" id="selfie_with_id" class="d-none" accept="image/*" onchange="handleFileSelected(this, 'selfie_with_id')">
                                            
                                            <button type="button" class="btn btn-primary btn-sm btn-cam-snap" onclick="openCamModal('selfie_with_id')">
                                                <i class="icon-camera mr-1"></i> {{ __('Camera') }}
                                            </button>
                                        </div>
                                        <input type="hidden" name="selfie_with_id_cam" id="selfie_with_id_cam">
                                        <input type="hidden" id="has_existing_selfie_with_id" value="{{ !empty($latestRequest->selfie_with_id) ? '1' : '0' }}">
                                        <div class="text-danger font-size-sm mt-2 d-none" id="error_selfie_with_id">
                                            <i class="icon-alert-circle mr-1"></i> {{ __('Please upload or take a selfie with your ID Card.') }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Store Documents (Image / PDF) -->
                                <div class="col-md-12 mb-4">
                                    <div class="p-3 doc-upload-card" id="card_store_documents">
                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                            <label class="font-weight-bold mb-0 text-dark">{{ __('Store Documents (Image or PDF)') }} <span class="text-danger">*</span></label>
                                            <span class="badge badge-secondary doc-status-badge" id="badge_store_documents">{{ __('Required') }}</span>
                                        </div>
                                        <small class="text-muted d-block mb-2">{{ __('Business license, utility bill, rent deed, or shop storefront photo (JPG, PNG, or PDF)') }}</small>
                                        
                                        <div class="preview-box mb-2 text-center p-2 bg-white rounded border" style="min-height: 90px; display: flex; align-items: center; justify-content: center;">
                                            @if(!empty($latestRequest->store_documents))
                                                <span class="badge badge-info p-2" id="preview_store_documents_text"><i class="icon-file"></i> {{ $latestRequest->store_documents }}</span>
                                                <img src="" class="img-fluid rounded d-none" style="max-height: 110px;" id="preview_store_documents_img">
                                                <span class="text-muted font-italic d-none" id="no_img_store_documents">{{ __('No document selected') }}</span>
                                            @else
                                                <span class="text-muted font-italic" id="no_img_store_documents">{{ __('No document selected') }}</span>
                                                <span class="badge badge-info p-2 d-none" id="preview_store_documents_text"></span>
                                                <img src="" class="img-fluid rounded d-none" style="max-height: 110px;" id="preview_store_documents_img">
                                            @endif
                                        </div>

                                        <div class="doc-btn-group">
                                            <label class="btn btn-outline-primary btn-sm btn-upload-file mb-0 cursor-pointer" for="store_documents">
                                                <i class="icon-upload mr-1"></i> {{ __('Upload File / PDF') }}
                                            </label>
                                            <input type="file" name="store_documents" id="store_documents" class="d-none" accept="image/*,application/pdf" onchange="handleFileSelected(this, 'store_documents')">
                                            
                                            <button type="button" class="btn btn-primary btn-sm btn-cam-snap" onclick="openCamModal('store_documents')">
                                                <i class="icon-camera mr-1"></i> {{ __('Camera') }}
                                            </button>
                                        </div>
                                        <input type="hidden" name="store_documents_cam" id="store_documents_cam">
                                        <input type="hidden" id="has_existing_store_documents" value="{{ !empty($latestRequest->store_documents) ? '1' : '0' }}">
                                        <div class="text-danger font-size-sm mt-2 d-none" id="error_store_documents">
                                            <i class="icon-alert-circle mr-1"></i> {{ __('Please provide a store document (upload file or capture photo).') }}
                                        </div>
                                    </div>
                                </div>

                                <!-- SAMPLE PRODUCTS VERIFICATION (AT LEAST 3 PRODUCTS) -->
                                <div class="col-md-12 mt-2 mb-2">
                                    <div class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-3">
                                        <h6 class="text-primary font-weight-bold mb-0">
                                            <i class="icon-box mr-1"></i> {{ __('Sample Products Verification (At least 3 Products)') }}
                                        </h6>
                                        <span class="badge badge-warning text-dark font-weight-bold">{{ __('3 Items Required') }}</span>
                                    </div>
                                    <p class="text-muted font-size-sm mb-3">
                                        {{ __('Please provide details and clear photos of at least 3 products you will sell. Enter product name and upload photo or snap with camera.') }}
                                    </p>
                                </div>

                                <!-- Product 1 -->
                                <div class="col-md-4 mb-4">
                                    <div class="p-3 doc-upload-card h-100" id="card_sample_product_1">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <label class="font-weight-bold mb-0 text-dark">
                                                <i class="icon-package text-primary mr-1"></i> {{ __('Product 1') }} <span class="text-danger">*</span>
                                            </label>
                                            <span class="badge badge-secondary doc-status-badge" id="badge_sample_product_1">{{ __('Required') }}</span>
                                        </div>
                                        <div class="form-group mb-2">
                                            <label for="sample_product_1_name" class="font-size-xs text-muted mb-1">{{ __('Product 1 Name') }} <span class="text-danger">*</span></label>
                                            <input type="text" name="sample_product_1_name" id="sample_product_1_name" class="form-control form-control-sm" placeholder="{{ __('e.g. Wireless Earbuds') }}" value="{{ old('sample_product_1_name', $latestRequest->sample_product_1_name ?? '') }}" required>
                                            <div class="invalid-feedback font-size-xs">{{ __('Please enter Product 1 Name.') }}</div>
                                        </div>
                                        
                                        <div class="preview-box mb-2 text-center p-2 bg-white rounded border" style="min-height: 100px; display: flex; align-items: center; justify-content: center;">
                                            @if(!empty($latestRequest->sample_product_1_image))
                                                <img src="{{ asset('core/public/storage/images/stores/' . $latestRequest->sample_product_1_image) }}" class="img-fluid rounded" style="max-height: 95px;" id="preview_sample_product_1">
                                                <span class="text-muted font-italic d-none" id="no_img_sample_product_1">{{ __('No photo selected') }}</span>
                                            @else
                                                <span class="text-muted font-italic font-size-xs" id="no_img_sample_product_1">{{ __('No photo selected') }}</span>
                                                <img src="" class="img-fluid rounded d-none" style="max-height: 95px;" id="preview_sample_product_1">
                                            @endif
                                        </div>

                                        <div class="doc-btn-group">
                                            <label class="btn btn-outline-primary btn-sm btn-upload-file mb-0 cursor-pointer" for="sample_product_1">
                                                <i class="icon-upload mr-1"></i> {{ __('Upload') }}
                                            </label>
                                            <input type="file" name="sample_product_1" id="sample_product_1" class="d-none" accept="image/*" onchange="handleFileSelected(this, 'sample_product_1')">
                                            
                                            <button type="button" class="btn btn-primary btn-sm btn-cam-snap" onclick="openCamModal('sample_product_1')">
                                                <i class="icon-camera mr-1"></i> {{ __('Camera') }}
                                            </button>
                                        </div>
                                        <input type="hidden" name="sample_product_1_cam" id="sample_product_1_cam">
                                        <input type="hidden" id="has_existing_sample_product_1" value="{{ !empty($latestRequest->sample_product_1_image) ? '1' : '0' }}">
                                        <div class="text-danger font-size-xs mt-1 d-none" id="error_sample_product_1">
                                            <i class="icon-alert-circle mr-1"></i> {{ __('Please upload or snap a photo of Product 1.') }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Product 2 -->
                                <div class="col-md-4 mb-4">
                                    <div class="p-3 doc-upload-card h-100" id="card_sample_product_2">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <label class="font-weight-bold mb-0 text-dark">
                                                <i class="icon-package text-primary mr-1"></i> {{ __('Product 2') }} <span class="text-danger">*</span>
                                            </label>
                                            <span class="badge badge-secondary doc-status-badge" id="badge_sample_product_2">{{ __('Required') }}</span>
                                        </div>
                                        <div class="form-group mb-2">
                                            <label for="sample_product_2_name" class="font-size-xs text-muted mb-1">{{ __('Product 2 Name') }} <span class="text-danger">*</span></label>
                                            <input type="text" name="sample_product_2_name" id="sample_product_2_name" class="form-control form-control-sm" placeholder="{{ __('e.g. Cotton Polo Shirt') }}" value="{{ old('sample_product_2_name', $latestRequest->sample_product_2_name ?? '') }}" required>
                                            <div class="invalid-feedback font-size-xs">{{ __('Please enter Product 2 Name.') }}</div>
                                        </div>
                                        
                                        <div class="preview-box mb-2 text-center p-2 bg-white rounded border" style="min-height: 100px; display: flex; align-items: center; justify-content: center;">
                                            @if(!empty($latestRequest->sample_product_2_image))
                                                <img src="{{ asset('core/public/storage/images/stores/' . $latestRequest->sample_product_2_image) }}" class="img-fluid rounded" style="max-height: 95px;" id="preview_sample_product_2">
                                                <span class="text-muted font-italic d-none" id="no_img_sample_product_2">{{ __('No photo selected') }}</span>
                                            @else
                                                <span class="text-muted font-italic font-size-xs" id="no_img_sample_product_2">{{ __('No photo selected') }}</span>
                                                <img src="" class="img-fluid rounded d-none" style="max-height: 95px;" id="preview_sample_product_2">
                                            @endif
                                        </div>

                                        <div class="doc-btn-group">
                                            <label class="btn btn-outline-primary btn-sm btn-upload-file mb-0 cursor-pointer" for="sample_product_2">
                                                <i class="icon-upload mr-1"></i> {{ __('Upload') }}
                                            </label>
                                            <input type="file" name="sample_product_2" id="sample_product_2" class="d-none" accept="image/*" onchange="handleFileSelected(this, 'sample_product_2')">
                                            
                                            <button type="button" class="btn btn-primary btn-sm btn-cam-snap" onclick="openCamModal('sample_product_2')">
                                                <i class="icon-camera mr-1"></i> {{ __('Camera') }}
                                            </button>
                                        </div>
                                        <input type="hidden" name="sample_product_2_cam" id="sample_product_2_cam">
                                        <input type="hidden" id="has_existing_sample_product_2" value="{{ !empty($latestRequest->sample_product_2_image) ? '1' : '0' }}">
                                        <div class="text-danger font-size-xs mt-1 d-none" id="error_sample_product_2">
                                            <i class="icon-alert-circle mr-1"></i> {{ __('Please upload or snap a photo of Product 2.') }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Product 3 -->
                                <div class="col-md-4 mb-4">
                                    <div class="p-3 doc-upload-card h-100" id="card_sample_product_3">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <label class="font-weight-bold mb-0 text-dark">
                                                <i class="icon-package text-primary mr-1"></i> {{ __('Product 3') }} <span class="text-danger">*</span>
                                            </label>
                                            <span class="badge badge-secondary doc-status-badge" id="badge_sample_product_3">{{ __('Required') }}</span>
                                        </div>
                                        <div class="form-group mb-2">
                                            <label for="sample_product_3_name" class="font-size-xs text-muted mb-1">{{ __('Product 3 Name') }} <span class="text-danger">*</span></label>
                                            <input type="text" name="sample_product_3_name" id="sample_product_3_name" class="form-control form-control-sm" placeholder="{{ __('e.g. Leather Wallet') }}" value="{{ old('sample_product_3_name', $latestRequest->sample_product_3_name ?? '') }}" required>
                                            <div class="invalid-feedback font-size-xs">{{ __('Please enter Product 3 Name.') }}</div>
                                        </div>
                                        
                                        <div class="preview-box mb-2 text-center p-2 bg-white rounded border" style="min-height: 100px; display: flex; align-items: center; justify-content: center;">
                                            @if(!empty($latestRequest->sample_product_3_image))
                                                <img src="{{ asset('core/public/storage/images/stores/' . $latestRequest->sample_product_3_image) }}" class="img-fluid rounded" style="max-height: 95px;" id="preview_sample_product_3">
                                                <span class="text-muted font-italic d-none" id="no_img_sample_product_3">{{ __('No photo selected') }}</span>
                                            @else
                                                <span class="text-muted font-italic font-size-xs" id="no_img_sample_product_3">{{ __('No photo selected') }}</span>
                                                <img src="" class="img-fluid rounded d-none" style="max-height: 95px;" id="preview_sample_product_3">
                                            @endif
                                        </div>

                                        <div class="doc-btn-group">
                                            <label class="btn btn-outline-primary btn-sm btn-upload-file mb-0 cursor-pointer" for="sample_product_3">
                                                <i class="icon-upload mr-1"></i> {{ __('Upload') }}
                                            </label>
                                            <input type="file" name="sample_product_3" id="sample_product_3" class="d-none" accept="image/*" onchange="handleFileSelected(this, 'sample_product_3')">
                                            
                                            <button type="button" class="btn btn-primary btn-sm btn-cam-snap" onclick="openCamModal('sample_product_3')">
                                                <i class="icon-camera mr-1"></i> {{ __('Camera') }}
                                            </button>
                                        </div>
                                        <input type="hidden" name="sample_product_3_cam" id="sample_product_3_cam">
                                        <input type="hidden" id="has_existing_sample_product_3" value="{{ !empty($latestRequest->sample_product_3_image) ? '1' : '0' }}">
                                        <div class="text-danger font-size-xs mt-1 d-none" id="error_sample_product_3">
                                            <i class="icon-alert-circle mr-1"></i> {{ __('Please upload or snap a photo of Product 3.') }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="wizard-nav-btns d-flex justify-content-between align-items-center pt-3 border-top mt-4">
                                <button type="button" class="btn btn-outline-secondary wizard-btn-prev" onclick="prevStep(3)">
                                    <i class="icon-arrow-left mr-1"></i> {{ __('Previous: Store Info') }}
                                </button>
                                @if($isFree)
                                    <button type="submit" class="btn btn-success px-4 font-weight-bold wizard-btn-next" id="step3SubmitBtn">
                                        <i class="icon-check mr-1"></i> {{ __('Submit Store Application') }}
                                    </button>
                                @else
                                    <button type="button" class="btn btn-primary px-4 font-weight-bold wizard-btn-next" id="step3NextBtn" onclick="nextStep(3)">
                                        {{ __('Next: Payment Proof') }} <i class="icon-arrow-right ml-1"></i>
                                    </button>
                                @endif
                            </div>
                        </div>

                        <!-- ============================================== -->
                        <!-- STEP 4: PAYMENT PROOF (ONLY IF FEE REQUIRED)   -->
                        <!-- ============================================== -->
                        @if(!$isFree)
                            <div class="wizard-step-content" id="stepContent4" style="display: none;">
                                <div class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-3">
                                    <h6 class="text-primary font-weight-bold mb-0">
                                        <i class="icon-credit-card mr-1"></i> {{ __('Step 4: Payment Details & Proof') }}
                                    </h6>
                                    <span class="badge badge-light border text-muted">{{ __('4 of 4') }}</span>
                                </div>

                                <!-- Receiving Accounts Section -->
                                <div class="bg-light p-3 rounded border mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="font-weight-bold text-dark mb-0">
                                            <i class="icon-credit-card text-primary mr-1"></i> {{ __('Store Opening Fee:') }} 
                                            <span class="text-primary font-weight-bold">{{ PriceHelper::storeOpeningFee($setting->store_opening_fee ?? 0) }}</span>
                                        </h6>
                                    </div>
                                    <p class="text-muted font-size-sm mb-3">{{ __('Select your payment method below to view our official account details:') }}</p>

                                    <!-- Step 4.1: Interactive Payment Method Selection Grid -->
                                    <label class="font-weight-bold text-dark mb-2 d-block">
                                        <i class="icon-layers text-primary mr-1"></i> {{ __('1. Choose Payment Method:') }} <span class="text-danger">*</span>
                                    </label>
                                    <div class="payment-methods-grid" id="paymentMethodsGrid">
                                        @forelse($receivingAccounts as $acc)
                                            @php
                                                $methodLower = strtolower($acc->payment_method);
                                                $iconClass = 'icon-credit-card';
                                                if (str_contains($methodLower, 'jazz') || str_contains($methodLower, 'easy') || str_contains($methodLower, 'sada') || str_contains($methodLower, 'naya') || str_contains($methodLower, 'wallet') || str_contains($methodLower, 'mobile')) {
                                                    $iconClass = 'icon-smartphone';
                                                } elseif (str_contains($methodLower, 'bank') || str_contains($methodLower, 'hbl') || str_contains($methodLower, 'meezan') || str_contains($methodLower, 'alflah') || str_contains($methodLower, 'mcb') || str_contains($methodLower, 'ubl') || str_contains($methodLower, 'allied')) {
                                                    $iconClass = 'icon-briefcase';
                                                }
                                            @endphp
                                            <div class="payment-method-card" id="method_card_{{ $acc->id }}" onclick="selectPaymentMethod('{{ $acc->id }}', '{{ addslashes($acc->payment_method) }}')">
                                                <div class="method-check"><i class="icon-check"></i></div>
                                                <div class="method-icon-wrap">
                                                    <i class="{{ $iconClass }}"></i>
                                                </div>
                                                <span class="method-name">{{ $acc->payment_method }}</span>
                                                <span class="method-tag">{{ __('Tap to select') }}</span>
                                            </div>
                                        @empty
                                            <div class="payment-method-card" id="method_card_easypaisa" onclick="selectPaymentMethod('easypaisa', 'Easypaisa')">
                                                <div class="method-check"><i class="icon-check"></i></div>
                                                <div class="method-icon-wrap"><i class="icon-smartphone"></i></div>
                                                <span class="method-name">Easypaisa</span>
                                                <span class="method-tag">{{ __('Tap to select') }}</span>
                                            </div>
                                            <div class="payment-method-card" id="method_card_jazzcash" onclick="selectPaymentMethod('jazzcash', 'JazzCash')">
                                                <div class="method-check"><i class="icon-check"></i></div>
                                                <div class="method-icon-wrap"><i class="icon-smartphone"></i></div>
                                                <span class="method-name">JazzCash</span>
                                                <span class="method-tag">{{ __('Tap to select') }}</span>
                                            </div>
                                            <div class="payment-method-card" id="method_card_bank" onclick="selectPaymentMethod('bank', 'Bank Transfer')">
                                                <div class="method-check"><i class="icon-check"></i></div>
                                                <div class="method-icon-wrap"><i class="icon-briefcase"></i></div>
                                                <span class="method-name">Bank Transfer</span>
                                                <span class="method-tag">{{ __('Tap to select') }}</span>
                                            </div>
                                        @endforelse
                                    </div>
                                    <div class="text-danger font-size-xs d-none mb-3" id="error_payment_method_select">
                                        <i class="icon-alert-circle mr-1"></i> {{ __('Please choose a payment method from above.') }}
                                    </div>

                                    <!-- Step 4.2: Selected Admin Receiving Account Details Box (Shown ONLY when selected) -->
                                    <div class="mt-2" id="adminAccountDisplaySection">
                                        <!-- Placeholder when none selected -->
                                        <div class="p-4 text-center bg-white rounded border text-muted" id="noAccountSelectedPrompt" style="border-style: dashed !important; border-width: 2px !important;">
                                            <i class="icon-credit-card text-primary mb-2" style="font-size: 32px; display: inline-block;"></i>
                                            <h6 class="font-weight-bold text-dark mb-1">{{ __('Please select a payment method above') }}</h6>
                                            <p class="font-size-xs text-muted mb-0">{{ __('Official receiving account details and payment instructions will appear here.') }}</p>
                                        </div>

                                        <!-- Dedicated Details Card for each account -->
                                        @foreach($receivingAccounts as $acc)
                                            <div class="selected-admin-account-card d-none" id="admin_acc_box_{{ $acc->id }}">
                                                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                                    <div class="d-flex align-items-center flex-wrap" style="gap: 6px;">
                                                        <span class="badge badge-primary font-weight-bold px-3 py-1 font-size-sm">{{ $acc->payment_method }}</span>
                                                        <span class="badge badge-success text-white font-size-xs"><i class="icon-check-circle mr-1"></i>{{ __('Verified Account') }}</span>
                                                    </div>
                                                    <small class="text-muted"><i class="icon-shield text-primary mr-1"></i>{{ __('Official Account') }}</small>
                                                </div>
                                                
                                                <div class="row align-items-center mb-1">
                                                    <div class="col-sm-5 mb-2 mb-sm-0">
                                                        <span class="text-muted font-size-xs d-block mb-1 font-weight-semibold text-uppercase" style="letter-spacing: 0.3px;"><i class="icon-user text-primary mr-1"></i>{{ __('Account Title / Name:') }}</span>
                                                        <div class="account-title-badge">
                                                            <strong class="account-title-text">{{ $acc->account_name }}</strong>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-7">
                                                        <span class="text-muted font-size-xs d-block mb-1 font-weight-semibold acc-single-line-heading"><i class="icon-credit-card text-primary mr-1"></i>{{ __('Account / Mobile / IBAN:') }}</span>
                                                        <div class="d-flex align-items-center justify-content-between bg-white p-2 rounded border" style="box-shadow: 0 1px 4px rgba(0,0,0,0.04);">
                                                            <strong class="text-primary font-size-md font-weight-bold tracking-wide" id="acc_num_text_{{ $acc->id }}">{{ $acc->account_number }}</strong>
                                                            <button type="button" class="btn btn-outline-primary btn-xs py-1 px-2 copy-btn" onclick="copyAccountNumber('{{ $acc->account_number }}', this)">
                                                                <i class="icon-copy mr-1"></i> <span class="copy-text">{{ __('Copy') }}</span>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>

                                                @if($acc->note)
                                                    <div class="admin-instruction-card mt-3">
                                                        <div class="instruction-icon-circle">
                                                            <i class="fas fa-info text-primary"></i>
                                                        </div>
                                                        <div class="instruction-content">
                                                            <strong class="instruction-title"><i class="icon-help-circle mr-1 text-primary"></i>{{ __('Instructions / Note:') }}</strong>
                                                            <div class="instruction-text">{{ $acc->note }}</div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Sender Details Form -->
                                <h6 class="font-weight-bold mb-3 text-dark"><i class="icon-edit mr-1"></i> {{ __('2. Enter Your Payment Transfer Details:') }}</h6>
                                <input type="hidden" name="account_type" id="account_type" value="{{ old('account_type') }}">
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label for="account_name">{{ __('Sender Account Holder Name') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="account_name" id="account_name" class="form-control" value="{{ old('account_name') }}" placeholder="{{ __('e.g. Ahmad Khan') }}" required>
                                        <div class="invalid-feedback">{{ __('Please enter sender account name.') }}</div>
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label for="account_number">{{ __('Sender Account / Mobile Number') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="account_number" id="account_number" class="form-control" value="{{ old('account_number') }}" placeholder="{{ __('Account number transferred from') }}" required>
                                        <div class="invalid-feedback">{{ __('Please enter sender account number.') }}</div>
                                    </div>

                                    <div class="col-md-12 form-group">
                                        <label for="transaction_id">{{ __('Transaction ID (TRX ID)') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="transaction_id" id="transaction_id" class="form-control" value="{{ old('transaction_id') }}" placeholder="{{ __('e.g. 19283746501 or TID') }}" required>
                                        <div class="invalid-feedback">{{ __('Please enter the transaction ID.') }}</div>
                                    </div>

                                    <!-- Payment Screenshot -->
                                    <div class="col-md-12 mb-3">
                                        <div class="p-3 doc-upload-card" id="card_payment_screenshot">
                                            <div class="d-flex justify-content-between align-items-start mb-1">
                                                <label class="font-weight-bold mb-0 text-dark">{{ __('Payment Receipt / Screenshot') }} <span class="text-danger">*</span></label>
                                                <span class="badge badge-secondary doc-status-badge" id="badge_payment_screenshot">{{ __('Required') }}</span>
                                            </div>
                                            <small class="text-muted d-block mb-2">{{ __('Upload screenshot of successful transfer or photograph paper receipt') }}</small>
                                            
                                            <div class="preview-box mb-2 text-center p-2 bg-white rounded border" style="min-height: 100px; display: flex; align-items: center; justify-content: center;">
                                                <span class="text-muted font-italic" id="no_img_payment_screenshot">{{ __('No screenshot selected') }}</span>
                                                <img src="" class="img-fluid rounded d-none" style="max-height: 120px;" id="preview_payment_screenshot">
                                            </div>

                                            <div class="doc-btn-group">
                                                <label class="btn btn-outline-primary btn-sm btn-upload-file mb-0 cursor-pointer" for="payment_screenshot">
                                                    <i class="icon-upload mr-1"></i> {{ __('Upload Screenshot') }}
                                                </label>
                                                <input type="file" name="payment_screenshot" id="payment_screenshot" class="d-none" accept="image/*" onchange="handleFileSelected(this, 'payment_screenshot')">
                                                
                                                <button type="button" class="btn btn-primary btn-sm btn-cam-snap" onclick="openCamModal('payment_screenshot')">
                                                    <i class="icon-camera mr-1"></i> {{ __('Camera') }}
                                                </button>
                                            </div>
                                            <input type="hidden" name="payment_screenshot_cam" id="payment_screenshot_cam">
                                            <div class="text-danger font-size-sm mt-2 d-none" id="error_payment_screenshot">
                                                <i class="icon-alert-circle mr-1"></i> {{ __('Please upload or take a photo of your payment receipt/screenshot.') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="wizard-nav-btns d-flex justify-content-between align-items-center pt-3 border-top mt-4">
                                    <button type="button" class="btn btn-outline-secondary wizard-btn-prev" onclick="prevStep(4)">
                                        <i class="icon-arrow-left mr-1"></i> {{ __('Previous: Documents') }}
                                    </button>
                                    <button type="submit" class="btn btn-success px-4 font-weight-bold wizard-btn-next" id="step4SubmitBtn">
                                        <i class="icon-check mr-1"></i> {{ __('Submit Store Application') }}
                                    </button>
                                </div>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================== -->
<!-- CAMERA CAPTURE MODAL                           -->
<!-- ============================================== -->
<div class="modal fade" id="cameraModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white py-3">
                <h6 class="modal-title text-white mb-0 font-weight-bold">
                    <i class="icon-camera mr-2 text-primary"></i> <span id="cameraModalTitle">{{ __('Take Photo with Camera') }}</span>
                </h6>
                <button type="button" class="close text-white" onclick="closeCameraModal()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center p-3 bg-light">
                <!-- Video Stream View -->
                <div id="cameraLiveWrap" class="position-relative bg-black rounded overflow-hidden shadow-inner" style="height: 320px; width: 100%;">
                    <video id="cameraVideo" autoplay playsinline style="width: 100%; height: 100%; object-fit: cover;"></video>
                    
                    <!-- Quick Floating Flip Camera Button on Video Overlay -->
                    <button type="button" class="btn btn-dark btn-sm rounded-circle position-absolute" style="top: 12px; right: 12px; z-index: 10; width: 42px; height: 42px; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.65); border: 1px solid rgba(255,255,255,0.4);" onclick="toggleCameraFacing()" title="{{ __('Flip / Switch Camera') }}">
                        <i class="fas fa-sync-alt text-white" style="font-size: 16px;"></i>
                    </button>
                    
                    <canvas id="cameraCanvas" class="d-none"></canvas>
                </div>

                <!-- Snapshot Preview View (Shown after capture) -->
                <div id="cameraSnapWrap" class="d-none position-relative bg-white rounded p-2 border" style="height: 320px; width: 100%; display: flex; align-items: center; justify-content: center;">
                    <img id="cameraSnapImg" src="" class="img-fluid rounded" style="max-height: 300px; max-width: 100%; object-fit: contain;">
                </div>

                <div id="cameraError" class="alert alert-danger mt-3 mb-0 d-none text-left font-size-sm"></div>
            </div>
            <div class="modal-footer cam-modal-footer">
                <button type="button" class="btn btn-secondary btn-sm btn-cam-cancel" onclick="closeCameraModal()">{{ __('Cancel') }}</button>
                
                <div id="cameraLiveControls" class="cam-live-controls">
                    <button type="button" class="btn btn-outline-info btn-sm font-weight-bold btn-switch-cam" id="switchCamBtn" onclick="toggleCameraFacing()" title="{{ __('Flip / Switch Front and Back Camera') }}">
                        <i class="fas fa-sync-alt mr-1"></i> <span id="switchCamText">{{ __('Flip Camera') }}</span>
                    </button>
                    <button type="button" class="btn btn-success font-weight-bold px-4 btn-snap-photo" id="captureBtn" onclick="takeSnapshot()">
                        <i class="icon-camera mr-1"></i> {{ __('Snap Photo') }}
                    </button>
                </div>

                <div id="cameraSnapControls" class="d-none cam-snap-controls">
                    <button type="button" class="btn btn-outline-secondary btn-sm btn-retake-photo" onclick="retakePhoto()">
                        <i class="icon-refresh-cw mr-1"></i> {{ __('Retake') }}
                    </button>
                    <button type="button" class="btn btn-primary btn-sm font-weight-bold px-3 btn-confirm-photo" onclick="confirmCapturedPhoto()">
                        <i class="icon-check mr-1"></i> {{ __('Use Photo') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function() {
    const isFree = {{ $isFree ? 'true' : 'false' }};
    const totalSteps = isFree ? 3 : 4;
    let currentStep = 1;
    let capturedTempDataUrl = null;
    let activeCameraTarget = null;
    let cameraStream = null;
    let currentFacingMode = 'environment';

    // -------------------------------------------------------------
    // Validation Helpers
    // -------------------------------------------------------------
    function hasDoc(fieldId) {
        const fileInput = document.getElementById(fieldId);
        const camInput = document.getElementById(fieldId + '_cam');
        const existingInput = document.getElementById('has_existing_' + fieldId);

        const hasFile = fileInput && fileInput.files && fileInput.files.length > 0;
        const hasCam = camInput && camInput.value && camInput.value.length > 0;
        const hasExisting = existingInput && existingInput.value === '1';

        return !!(hasFile || hasCam || hasExisting);
    }

    function validateStep1() {
        let isValid = true;
        let firstInvalid = null;

        const fn = document.getElementById('first_name');
        const ln = document.getElementById('last_name');
        const phone = document.getElementById('phone');
        const email = document.getElementById('email');
        const cnic = document.getElementById('cnic');

        // First Name
        if (!fn || !fn.value.trim()) {
            if (fn) fn.classList.add('is-invalid');
            isValid = false;
            if (!firstInvalid && fn) firstInvalid = fn;
        } else {
            fn.classList.remove('is-invalid');
        }

        // Last Name
        if (!ln || !ln.value.trim()) {
            if (ln) ln.classList.add('is-invalid');
            isValid = false;
            if (!firstInvalid && ln) firstInvalid = ln;
        } else {
            ln.classList.remove('is-invalid');
        }

        // Phone
        if (!phone || !phone.value.trim() || phone.value.trim().length < 7) {
            if (phone) phone.classList.add('is-invalid');
            isValid = false;
            if (!firstInvalid && phone) firstInvalid = phone;
        } else {
            phone.classList.remove('is-invalid');
        }

        // Email
        const emailVal = email ? email.value.trim() : '';
        const isEmailOk = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailVal);
        if (!email || !emailVal || !isEmailOk) {
            if (email) email.classList.add('is-invalid');
            isValid = false;
            if (!firstInvalid && email) firstInvalid = email;
        } else {
            email.classList.remove('is-invalid');
        }

        // CNIC
        const cnicVal = cnic ? cnic.value.trim() : '';
        if (!cnic || !cnicVal || cnicVal.length < 5) {
            if (cnic) cnic.classList.add('is-invalid');
            isValid = false;
            if (!firstInvalid && cnic) firstInvalid = cnic;
        } else {
            cnic.classList.remove('is-invalid');
        }

        if (!isValid && firstInvalid) {
            firstInvalid.focus();
        }

        return isValid;
    }

    function validateStep2() {
        let isValid = true;
        let firstInvalid = null;

        const shopName = document.getElementById('shop_name');
        const productTypes = document.getElementById('product_types');
        const courierCompany = document.getElementById('courier_company');
        const shopAddress = document.getElementById('shop_address');

        if (!shopName || !shopName.value.trim()) {
            if (shopName) shopName.classList.add('is-invalid');
            isValid = false;
            if (!firstInvalid && shopName) firstInvalid = shopName;
        } else {
            shopName.classList.remove('is-invalid');
        }

        if (!productTypes || !productTypes.value.trim()) {
            if (productTypes) productTypes.classList.add('is-invalid');
            isValid = false;
            if (!firstInvalid && productTypes) firstInvalid = productTypes;
        } else {
            productTypes.classList.remove('is-invalid');
        }

        if (!courierCompany || !courierCompany.value.trim()) {
            if (courierCompany) courierCompany.classList.add('is-invalid');
            isValid = false;
            if (!firstInvalid && courierCompany) firstInvalid = courierCompany;
        } else {
            courierCompany.classList.remove('is-invalid');
        }

        if (!shopAddress || !shopAddress.value.trim()) {
            if (shopAddress) shopAddress.classList.add('is-invalid');
            isValid = false;
            if (!firstInvalid && shopAddress) firstInvalid = shopAddress;
        } else {
            shopAddress.classList.remove('is-invalid');
        }

        if (!isValid && firstInvalid) {
            firstInvalid.focus();
        }

        return isValid;
    }

    function validateStep3() {
        let isValid = true;
        let firstInvalid = null;
        const docs = ['id_card_front', 'selfie_with_id', 'store_documents'];

        docs.forEach(docId => {
            const card = document.getElementById('card_' + docId);
            const err = document.getElementById('error_' + docId);
            if (!hasDoc(docId)) {
                isValid = false;
                if (card) card.classList.add('border-danger');
                if (err) err.classList.remove('d-none');
            } else {
                if (card) card.classList.remove('border-danger');
                if (err) err.classList.add('d-none');
            }
        });

        // 3 Sample Products Validation
        for (let p = 1; p <= 3; p++) {
            const nameInput = document.getElementById('sample_product_' + p + '_name');
            const card = document.getElementById('card_sample_product_' + p);
            const err = document.getElementById('error_sample_product_' + p);

            if (!nameInput || !nameInput.value.trim()) {
                if (nameInput) nameInput.classList.add('is-invalid');
                isValid = false;
                if (!firstInvalid && nameInput) firstInvalid = nameInput;
            } else {
                if (nameInput) nameInput.classList.remove('is-invalid');
            }

            if (!hasDoc('sample_product_' + p)) {
                isValid = false;
                if (card) card.classList.add('border-danger');
                if (err) err.classList.remove('d-none');
            } else {
                if (card) card.classList.remove('border-danger');
                if (err) err.classList.add('d-none');
            }
        }

        if (!isValid && firstInvalid) {
            firstInvalid.focus();
        }

        return isValid;
    }

    function validateStep4() {
        if (isFree) return true;
        let isValid = true;
        let firstInvalid = null;

        const accType = document.getElementById('account_type');
        const accName = document.getElementById('account_name');
        const accNumber = document.getElementById('account_number');
        const trxId = document.getElementById('transaction_id');
        const card = document.getElementById('card_payment_screenshot');
        const err = document.getElementById('error_payment_screenshot');
        const errMethod = document.getElementById('error_payment_method_select');

        if (!accType || !accType.value || !accType.value.trim()) {
            if (errMethod) errMethod.classList.remove('d-none');
            const grid = document.getElementById('paymentMethodsGrid');
            if (grid) grid.scrollIntoView({ behavior: 'smooth', block: 'center' });
            isValid = false;
            if (!firstInvalid && accType) firstInvalid = accType;
        } else {
            if (errMethod) errMethod.classList.add('d-none');
        }

        if (!accName || !accName.value.trim()) {
            if (accName) accName.classList.add('is-invalid');
            isValid = false;
            if (!firstInvalid && accName) firstInvalid = accName;
        } else {
            accName.classList.remove('is-invalid');
        }

        if (!accNumber || !accNumber.value.trim()) {
            if (accNumber) accNumber.classList.add('is-invalid');
            isValid = false;
            if (!firstInvalid && accNumber) firstInvalid = accNumber;
        } else {
            accNumber.classList.remove('is-invalid');
        }

        if (!trxId || !trxId.value.trim()) {
            if (trxId) trxId.classList.add('is-invalid');
            isValid = false;
            if (!firstInvalid && trxId) firstInvalid = trxId;
        } else {
            trxId.classList.remove('is-invalid');
        }

        if (!hasDoc('payment_screenshot')) {
            isValid = false;
            if (card) card.classList.add('border-danger');
            if (err) err.classList.remove('d-none');
        } else {
            if (card) card.classList.remove('border-danger');
            if (err) err.classList.add('d-none');
        }

        if (!isValid && firstInvalid) {
            if (typeof firstInvalid.focus === 'function') firstInvalid.focus();
        }

        return isValid;
    }

    // -------------------------------------------------------------
    // Dynamic Payment Method Selection & Copy Helpers
    // -------------------------------------------------------------
    window.selectPaymentMethod = function(accId, methodName) {
        // Update hidden input
        const accTypeInput = document.getElementById('account_type');
        if (accTypeInput) {
            accTypeInput.value = methodName;
            accTypeInput.classList.remove('is-invalid');
        }

        const errMethod = document.getElementById('error_payment_method_select');
        if (errMethod) errMethod.classList.add('d-none');

        // Toggle active card
        document.querySelectorAll('.payment-method-card').forEach(card => {
            card.classList.remove('selected');
        });
        const activeCard = document.getElementById('method_card_' + accId);
        if (activeCard) {
            activeCard.classList.add('selected');
        }

        // Hide prompt placeholder
        const prompt = document.getElementById('noAccountSelectedPrompt');
        if (prompt) prompt.classList.add('d-none');

        // Hide all admin account boxes
        document.querySelectorAll('.selected-admin-account-card').forEach(box => {
            box.classList.add('d-none');
        });

        // Show selected account box
        const targetBox = document.getElementById('admin_acc_box_' + accId);
        if (targetBox) {
            targetBox.classList.remove('d-none');
        }
    };

    window.copyAccountNumber = function(accNumber, btn) {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(accNumber).then(function() {
                showCopiedFeedback(btn);
            }).catch(function() {
                fallbackCopy(accNumber, btn);
            });
        } else {
            fallbackCopy(accNumber, btn);
        }
    };

    function showCopiedFeedback(btn) {
        if (!btn) return;
        const origHtml = btn.innerHTML;
        btn.classList.remove('btn-outline-primary');
        btn.classList.add('btn-success');
        btn.innerHTML = '<i class="icon-check mr-1"></i> {{ __("Copied!") }}';
        setTimeout(function() {
            btn.classList.remove('btn-success');
            btn.classList.add('btn-outline-primary');
            btn.innerHTML = origHtml;
        }, 2000);
    }

    function fallbackCopy(text, btn) {
        const dummy = document.createElement("textarea");
        document.body.appendChild(dummy);
        dummy.value = text;
        dummy.select();
        document.execCommand("copy");
        document.body.removeChild(dummy);
        showCopiedFeedback(btn);
    }

    // -------------------------------------------------------------
    // Step Navigation - Strictly hides other steps and displays only current
    // -------------------------------------------------------------
    window.nextStep = function(fromStep) {
        if (fromStep === 1) {
            if (!validateStep1()) return;
            goToStep(2);
        } else if (fromStep === 2) {
            if (!validateStep2()) return;
            goToStep(3);
        } else if (fromStep === 3) {
            if (!validateStep3()) return;
            if (isFree) {
                document.getElementById('storeApplyForm').submit();
            } else {
                goToStep(4);
            }
        }
    };

    window.prevStep = function(fromStep) {
        if (fromStep > 1) {
            goToStep(fromStep - 1);
        }
    };

    function goToStep(step) {
        if (step < 1 || step > totalSteps) return;

        // Explicitly hide all step contents
        for (let i = 1; i <= 4; i++) {
            const content = document.getElementById('stepContent' + i);
            if (content) {
                content.classList.remove('active');
                content.style.setProperty('display', 'none', 'important');
            }
        }

        // Explicitly show only target step content
        const targetContent = document.getElementById('stepContent' + step);
        if (targetContent) {
            targetContent.classList.add('active');
            targetContent.style.setProperty('display', 'block', 'important');
        }

        currentStep = step;

        // Scroll to wizard top smoothly
        const formCard = document.getElementById('storeApplyForm');
        if (formCard) {
            formCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    // Realtime clear invalid state on typing
    ['first_name', 'last_name', 'phone', 'email', 'cnic', 'shop_name', 'product_types', 'shop_address', 'account_type', 'account_name', 'account_number', 'transaction_id', 'sample_product_1_name', 'sample_product_2_name', 'sample_product_3_name'].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('input', function() {
                if (this.value && this.value.trim().length > 0) {
                    this.classList.remove('is-invalid');
                }
            });
            el.addEventListener('change', function() {
                if (this.value && this.value.trim().length > 0) {
                    this.classList.remove('is-invalid');
                }
            });
        }
    });

    // Form submit validation guard
    const applyForm = document.getElementById('storeApplyForm');
    if (applyForm) {
        applyForm.addEventListener('submit', function(e) {
            if (!validateStep1()) {
                e.preventDefault();
                goToStep(1);
                return false;
            }
            if (!validateStep2()) {
                e.preventDefault();
                goToStep(2);
                return false;
            }
            if (!validateStep3()) {
                e.preventDefault();
                goToStep(3);
                return false;
            }
            if (!isFree && !validateStep4()) {
                e.preventDefault();
                goToStep(4);
                return false;
            }
        });
    }

    // -------------------------------------------------------------
    // File & Camera Document Upload Handlers
    // -------------------------------------------------------------
    window.handleFileSelected = function(input, targetField) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const card = document.getElementById('card_' + targetField);
            const badge = document.getElementById('badge_' + targetField);
            const err = document.getElementById('error_' + targetField);
            const noImg = document.getElementById('no_img_' + targetField);
            const imgPreview = document.getElementById('preview_' + targetField) || document.getElementById('preview_' + targetField + '_img');
            const textPreview = document.getElementById('preview_' + targetField + '_text');

            // Clear camera hidden input if file is chosen
            const camInput = document.getElementById(targetField + '_cam');
            if (camInput) camInput.value = '';

            if (noImg) noImg.classList.add('d-none');
            if (card) {
                card.classList.add('has-file');
                card.classList.remove('border-danger');
            }
            if (err) err.classList.add('d-none');
            if (badge) {
                badge.className = 'badge badge-success doc-status-badge';
                badge.innerHTML = '<i class="icon-check"></i> ' + (file.type === 'application/pdf' ? 'PDF Attached' : 'Image Attached');
            }

            if (file.type === 'application/pdf') {
                if (imgPreview) imgPreview.classList.add('d-none');
                if (textPreview) {
                    textPreview.innerHTML = '<i class="icon-file-text mr-1"></i> ' + file.name;
                    textPreview.classList.remove('d-none');
                }
            } else {
                const reader = new FileReader();
                reader.onload = function(e) {
                    if (textPreview) textPreview.classList.add('d-none');
                    if (imgPreview) {
                        imgPreview.src = e.target.result;
                        imgPreview.classList.remove('d-none');
                    }
                };
                reader.readAsDataURL(file);
            }
        }
    };

    // -------------------------------------------------------------
    // WebRTC Camera Capture & Front/Back Swap Integration
    // -------------------------------------------------------------
    function startCameraStream(facing) {
        if (cameraStream) {
            cameraStream.getTracks().forEach(track => track.stop());
            cameraStream = null;
        }
        currentFacingMode = facing;
        const video = document.getElementById('cameraVideo');
        const errDiv = document.getElementById('cameraError');
        const switchText = document.getElementById('switchCamText');
        if (errDiv) errDiv.classList.add('d-none');
        if (switchText) {
            switchText.innerText = (facing === 'user') ? '{{ __("Switch to Back") }}' : '{{ __("Switch to Front") }}';
        }

        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            if (errDiv) {
                errDiv.innerText = "{{ __('Camera API is not supported on this browser. Please choose a file.') }}";
                errDiv.classList.remove('d-none');
            }
            return;
        }

        // Try with ideal facing constraint first
        navigator.mediaDevices.getUserMedia({
            video: {
                facingMode: { ideal: facing },
                width: { ideal: 1280 },
                height: { ideal: 720 }
            },
            audio: false
        })
        .then(function(stream) {
            cameraStream = stream;
            if (video) {
                video.srcObject = stream;
                video.play();
            }
        })
        .catch(function(err) {
            // Fallback: simple string facingMode
            navigator.mediaDevices.getUserMedia({
                video: { facingMode: facing },
                audio: false
            })
            .then(function(stream) {
                cameraStream = stream;
                if (video) {
                    video.srcObject = stream;
                    video.play();
                }
            })
            .catch(function(err2) {
                // Final fallback: any video device
                navigator.mediaDevices.getUserMedia({ video: true, audio: false })
                .then(function(stream) {
                    cameraStream = stream;
                    if (video) {
                        video.srcObject = stream;
                        video.play();
                    }
                })
                .catch(function(e) {
                    if (errDiv) {
                        errDiv.innerText = "{{ __('Unable to access camera. Please allow camera permissions or upload file directly from your device.') }}";
                        errDiv.classList.remove('d-none');
                    }
                });
            });
        });
    }

    window.toggleCameraFacing = function() {
        const nextFacing = (currentFacingMode === 'user') ? 'environment' : 'user';
        startCameraStream(nextFacing);
    };

    window.openCamModal = function(targetInputName) {
        activeCameraTarget = targetInputName;
        capturedTempDataUrl = null;

        const modal = $('#cameraModal');
        const errDiv = document.getElementById('cameraError');
        const liveWrap = document.getElementById('cameraLiveWrap');
        const snapWrap = document.getElementById('cameraSnapWrap');
        const liveControls = document.getElementById('cameraLiveControls');
        const snapControls = document.getElementById('cameraSnapControls');

        // Reset state
        errDiv.classList.add('d-none');
        liveWrap.classList.remove('d-none');
        snapWrap.classList.add('d-none');
        liveControls.classList.remove('d-none');
        snapControls.classList.add('d-none');

        // Set title
        const titles = {
            'id_card_front': '{{ __("Capture ID Card Picture") }}',
            'selfie_with_id': '{{ __("Capture Selfie with ID Card") }}',
            'store_documents': '{{ __("Capture Store Document / Photo") }}',
            'sample_product_1': '{{ __("Capture Sample Product 1 Photo") }}',
            'sample_product_2': '{{ __("Capture Sample Product 2 Photo") }}',
            'sample_product_3': '{{ __("Capture Sample Product 3 Photo") }}',
            'payment_screenshot': '{{ __("Capture Payment Receipt") }}'
        };
        const modalTitle = document.getElementById('cameraModalTitle');
        if (modalTitle) modalTitle.innerText = titles[targetInputName] || '{{ __("Take Photo with Camera") }}';

        modal.modal('show');

        // Camera facing: 'user' for selfie, 'environment' for documents and products
        const initialFacing = (targetInputName === 'selfie_with_id') ? 'user' : 'environment';
        startCameraStream(initialFacing);
    };

    window.takeSnapshot = function() {
        const video = document.getElementById('cameraVideo');
        const canvas = document.getElementById('cameraCanvas');
        if (!video || !cameraStream) return;

        canvas.width = video.videoWidth || 640;
        canvas.height = video.videoHeight || 480;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        capturedTempDataUrl = canvas.toDataURL('image/jpeg', 0.92);

        // Show snapshot preview
        document.getElementById('cameraSnapImg').src = capturedTempDataUrl;
        document.getElementById('cameraLiveWrap').classList.add('d-none');
        document.getElementById('cameraSnapWrap').classList.remove('d-none');
        document.getElementById('cameraLiveControls').classList.add('d-none');
        document.getElementById('cameraSnapControls').classList.remove('d-none');
    };

    window.retakePhoto = function() {
        capturedTempDataUrl = null;
        document.getElementById('cameraSnapWrap').classList.add('d-none');
        document.getElementById('cameraLiveWrap').classList.remove('d-none');
        document.getElementById('cameraSnapControls').classList.add('d-none');
        document.getElementById('cameraLiveControls').classList.remove('d-none');
    };

    window.confirmCapturedPhoto = function() {
        if (!activeCameraTarget || !capturedTempDataUrl) return;

        // Set base64 value in hidden input
        const hiddenCamInput = document.getElementById(activeCameraTarget + '_cam');
        if (hiddenCamInput) hiddenCamInput.value = capturedTempDataUrl;

        // Clear regular file input
        const fileInput = document.getElementById(activeCameraTarget);
        if (fileInput) fileInput.value = '';

        // Update preview thumbnail & UI badge
        const card = document.getElementById('card_' + activeCameraTarget);
        const badge = document.getElementById('badge_' + activeCameraTarget);
        const err = document.getElementById('error_' + activeCameraTarget);
        const noImg = document.getElementById('no_img_' + activeCameraTarget);
        const imgPreview = document.getElementById('preview_' + activeCameraTarget) || document.getElementById('preview_' + activeCameraTarget + '_img');
        const textPreview = document.getElementById('preview_' + activeCameraTarget + '_text');

        if (noImg) noImg.classList.add('d-none');
        if (textPreview) textPreview.classList.add('d-none');
        if (imgPreview) {
            imgPreview.src = capturedTempDataUrl;
            imgPreview.classList.remove('d-none');
        }
        if (card) {
            card.classList.add('has-file');
            card.classList.remove('border-danger');
        }
        if (err) err.classList.add('d-none');
        if (badge) {
            badge.className = 'badge badge-success doc-status-badge';
            badge.innerHTML = '<i class="icon-camera"></i> Photo Captured';
        }

        closeCameraModal();
    };

    window.closeCameraModal = function() {
        if (cameraStream) {
            cameraStream.getTracks().forEach(track => track.stop());
            cameraStream = null;
        }
        $('#cameraModal').modal('hide');
    };

    $('#cameraModal').on('hidden.bs.modal', function() {
        if (cameraStream) {
            cameraStream.getTracks().forEach(track => track.stop());
            cameraStream = null;
        }
    });

    // Initial validation check & setup on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Set initial step visibility (Step 1 visible, all others hidden)
        goToStep(1);

        // Initialize existing documents check
        ['id_card_front', 'selfie_with_id', 'store_documents'].forEach(field => {
            const hasExisting = document.getElementById('has_existing_' + field);
            if (hasExisting && hasExisting.value === '1') {
                const card = document.getElementById('card_' + field);
                const badge = document.getElementById('badge_' + field);
                if (card) card.classList.add('has-file');
                if (badge) {
                    badge.className = 'badge badge-success doc-status-badge';
                    badge.innerHTML = '<i class="icon-check"></i> ' + '{{ __("Existing Document") }}';
                }
            }
        });

        // Initialize payment method selection if prefilled
        const accTypeInput = document.getElementById('account_type');
        if (accTypeInput && accTypeInput.value) {
            let matched = false;
            document.querySelectorAll('.payment-method-card').forEach(card => {
                const nameEl = card.querySelector('.method-name');
                if (nameEl && nameEl.innerText.trim().toLowerCase() === accTypeInput.value.trim().toLowerCase()) {
                    card.click();
                    matched = true;
                }
            });
            if (!matched) {
                const firstCard = document.querySelector('.payment-method-card');
                if (firstCard) firstCard.click();
            }
        }
    });
})();
</script>
@endsection
