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
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 text-white"><i class="icon-shopping-bag mr-2"></i> {{ __('Open Shop / Seller Application') }}</h5>
                    @if(isset($setting) && $setting->is_store_opening_free == 1)
                        <span class="badge badge-success px-3 py-2"><i class="icon-check-circle"></i> {{ __('FREE STORE OPENING') }}</span>
                    @else
                        <span class="badge badge-warning text-dark px-3 py-2 font-weight-bold">
                            {{ __('Fee:') }} {{ PriceHelper::storeOpeningFee($setting->store_opening_fee ?? 0) }}
                        </span>
                    @endif
                </div>

                <div class="card-body p-4">
                    @include('alerts.alerts')

                    @php
                        $isFree = (isset($setting) && $setting->is_store_opening_free == 1);
                        $totalSteps = $isFree ? 3 : 4;
                    @endphp

                    @if($isFree)
                        <div class="alert alert-success d-flex align-items-center mb-4">
                            <i class="icon-gift fa-2x mr-3 text-success"></i>
                            <div>
                                <h6 class="mb-1 font-weight-bold text-success">{{ __('Free Store Opening Offer is Active!') }}</h6>
                                <p class="mb-0 font-size-sm">{{ __('Open your store without any fees. Complete the 3-step application below to get your shop approved.') }}</p>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info d-flex align-items-center mb-4">
                            <i class="icon-info fa-2x mr-3 text-primary"></i>
                            <div>
                                <h6 class="mb-1 font-weight-bold text-primary">{{ __('Open Shop / Seller Application') }}</h6>
                                <p class="mb-0 font-size-sm">
                                    {{ __('Complete steps 1-4 or apni application bhejein apna store open krny k liye or products list krny k liye.') }}
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

                                        <div class="d-flex gap-2" style="gap: 8px;">
                                            <label class="btn btn-outline-primary btn-sm flex-fill mb-0 cursor-pointer" for="id_card_front">
                                                <i class="icon-upload"></i> {{ __('Upload File') }}
                                            </label>
                                            <input type="file" name="id_card_front" id="id_card_front" class="d-none" accept="image/*" onchange="handleFileSelected(this, 'id_card_front')">
                                            
                                            <button type="button" class="btn btn-primary btn-sm" onclick="openCamModal('id_card_front')">
                                                <i class="icon-camera"></i> {{ __('Camera') }}
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

                                        <div class="d-flex gap-2" style="gap: 8px;">
                                            <label class="btn btn-outline-primary btn-sm flex-fill mb-0 cursor-pointer" for="selfie_with_id">
                                                <i class="icon-upload"></i> {{ __('Upload File') }}
                                            </label>
                                            <input type="file" name="selfie_with_id" id="selfie_with_id" class="d-none" accept="image/*" onchange="handleFileSelected(this, 'selfie_with_id')">
                                            
                                            <button type="button" class="btn btn-primary btn-sm" onclick="openCamModal('selfie_with_id')">
                                                <i class="icon-camera"></i> {{ __('Camera') }}
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
                                <div class="col-md-12 mb-3">
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

                                        <div class="d-flex gap-2" style="gap: 8px;">
                                            <label class="btn btn-outline-primary btn-sm flex-fill mb-0 cursor-pointer" for="store_documents">
                                                <i class="icon-upload"></i> {{ __('Upload Document / Image / PDF') }}
                                            </label>
                                            <input type="file" name="store_documents" id="store_documents" class="d-none" accept="image/*,application/pdf" onchange="handleFileSelected(this, 'store_documents')">
                                            
                                            <button type="button" class="btn btn-primary btn-sm" onclick="openCamModal('store_documents')">
                                                <i class="icon-camera"></i> {{ __('Camera') }}
                                            </button>
                                        </div>
                                        <input type="hidden" name="store_documents_cam" id="store_documents_cam">
                                        <input type="hidden" id="has_existing_store_documents" value="{{ !empty($latestRequest->store_documents) ? '1' : '0' }}">
                                        <div class="text-danger font-size-sm mt-2 d-none" id="error_store_documents">
                                            <i class="icon-alert-circle mr-1"></i> {{ __('Please provide a store document (upload file or capture photo).') }}
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

                                <!-- Receiving Accounts Callout -->
                                <div class="bg-light p-3 rounded border mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="font-weight-bold text-dark mb-0">
                                            <i class="icon-info text-primary mr-1"></i> {{ __('Store Opening Fee:') }} 
                                            <span class="text-primary font-weight-bold">{{ PriceHelper::storeOpeningFee($setting->store_opening_fee ?? 0) }}</span>
                                        </h6>
                                    </div>
                                    <p class="text-muted font-size-sm mb-3">{{ __('Please transfer the fee to one of our official accounts below, then provide your transaction details and receipt.') }}</p>
                                    
                                    <div class="row">
                                        @forelse($receivingAccounts as $acc)
                                            <div class="col-md-6 mb-3">
                                                <div class="p-3 bg-white border rounded shadow-sm account-card">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <span class="badge badge-primary font-weight-bold px-2 py-1">{{ $acc->payment_method }}</span>
                                                        <small class="text-muted"><i class="icon-check-circle text-success"></i> {{ __('Verified Account') }}</small>
                                                    </div>
                                                    <div class="mb-1 font-size-sm"><span class="text-muted">{{ __('Account Name:') }}</span> <strong>{{ $acc->account_name }}</strong></div>
                                                    <div class="d-flex justify-content-between align-items-center font-size-sm">
                                                        <div><span class="text-muted">{{ __('Account No:') }}</span> <strong class="text-primary">{{ $acc->account_number }}</strong></div>
                                                        <button type="button" class="btn btn-outline-secondary btn-xs py-0 px-2" onclick="navigator.clipboard.writeText('{{ $acc->account_number }}'); alert('Account number copied to clipboard!');">{{ __('Copy') }}</button>
                                                    </div>
                                                    @if($acc->note)
                                                        <small class="text-muted d-block mt-2 border-top pt-1"><i class="icon-info"></i> {{ $acc->note }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        @empty
                                            <div class="col-12">
                                                <p class="text-muted mb-0 font-italic font-size-sm">{{ __('Transfer methods: Easypaisa / JazzCash / Bank Transfer.') }}</p>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>

                                <!-- Sender Details Form -->
                                <h6 class="font-weight-bold mb-3 text-dark"><i class="icon-edit mr-1"></i> {{ __('Enter Your Payment Details:') }}</h6>
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label for="account_type">{{ __('Sent Via (Payment Method)') }} <span class="text-danger">*</span></label>
                                        <select name="account_type" id="account_type" class="form-control" required>
                                            <option value="">{{ __('-- Select Payment Method --') }}</option>
                                            @foreach($receivingAccounts as $acc)
                                                <option value="{{ $acc->payment_method }}" {{ old('account_type') == $acc->payment_method ? 'selected' : '' }}>{{ $acc->payment_method }}</option>
                                            @endforeach
                                            <option value="Easypaisa" {{ old('account_type') == 'Easypaisa' ? 'selected' : '' }}>Easypaisa</option>
                                            <option value="JazzCash" {{ old('account_type') == 'JazzCash' ? 'selected' : '' }}>JazzCash</option>
                                            <option value="HBL / Bank" {{ old('account_type') == 'HBL / Bank' ? 'selected' : '' }}>HBL / Bank Transfer</option>
                                            <option value="Other" {{ old('account_type') == 'Other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                        <div class="invalid-feedback">{{ __('Please select a payment method.') }}</div>
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label for="account_name">{{ __('Sender Account Holder Name') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="account_name" id="account_name" class="form-control" value="{{ old('account_name') }}" placeholder="{{ __('Name on your sending account') }}" required>
                                        <div class="invalid-feedback">{{ __('Please enter sender account name.') }}</div>
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label for="account_number">{{ __('Sender Account / Mobile Number') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="account_number" id="account_number" class="form-control" value="{{ old('account_number') }}" placeholder="{{ __('Account number transferred from') }}" required>
                                        <div class="invalid-feedback">{{ __('Please enter sender account number.') }}</div>
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label for="transaction_id">{{ __('Transaction ID (TRX ID)') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="transaction_id" id="transaction_id" class="form-control" value="{{ old('transaction_id') }}" placeholder="{{ __('e.g. 19283746501') }}" required>
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

                                            <div class="d-flex gap-2" style="gap: 8px;">
                                                <label class="btn btn-outline-primary btn-sm flex-fill mb-0 cursor-pointer" for="payment_screenshot">
                                                    <i class="icon-upload"></i> {{ __('Upload Screenshot') }}
                                                </label>
                                                <input type="file" name="payment_screenshot" id="payment_screenshot" class="d-none" accept="image/*" onchange="handleFileSelected(this, 'payment_screenshot')">
                                                
                                                <button type="button" class="btn btn-primary btn-sm" onclick="openCamModal('payment_screenshot')">
                                                    <i class="icon-camera"></i> {{ __('Camera') }}
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
                <div id="cameraLiveWrap" class="position-relative bg-black rounded overflow-hidden shadow-inner" style="height: 320px;">
                    <video id="cameraVideo" autoplay playsinline style="width: 100%; height: 100%; object-fit: cover;"></video>
                    <canvas id="cameraCanvas" class="d-none"></canvas>
                </div>

                <!-- Snapshot Preview View (Shown after capture) -->
                <div id="cameraSnapWrap" class="d-none position-relative bg-white rounded p-2 border" style="height: 320px; display: flex; align-items: center; justify-content: center;">
                    <img id="cameraSnapImg" src="" class="img-fluid rounded" style="max-height: 300px; object-fit: contain;">
                </div>

                <div id="cameraError" class="alert alert-danger mt-3 mb-0 d-none text-left font-size-sm"></div>
            </div>
            <div class="modal-footer justify-content-between py-2">
                <button type="button" class="btn btn-secondary btn-sm" onclick="closeCameraModal()">{{ __('Cancel') }}</button>
                
                <div id="cameraLiveControls">
                    <button type="button" class="btn btn-success font-weight-bold px-4" id="captureBtn" onclick="takeSnapshot()">
                        <i class="icon-camera mr-1"></i> {{ __('Snap Photo') }}
                    </button>
                </div>

                <div id="cameraSnapControls" class="d-none" style="gap: 8px;">
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="retakePhoto()">
                        <i class="icon-refresh-cw mr-1"></i> {{ __('Retake') }}
                    </button>
                    <button type="button" class="btn btn-primary btn-sm font-weight-bold px-3" onclick="confirmCapturedPhoto()">
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
        const shopAddress = document.getElementById('shop_address');

        if (!shopName || !shopName.value.trim()) {
            if (shopName) shopName.classList.add('is-invalid');
            isValid = false;
            if (!firstInvalid && shopName) firstInvalid = shopName;
        } else {
            shopName.classList.remove('is-invalid');
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

        if (!accType || !accType.value) {
            if (accType) accType.classList.add('is-invalid');
            isValid = false;
            if (!firstInvalid && accType) firstInvalid = accType;
        } else {
            accType.classList.remove('is-invalid');
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
            firstInvalid.focus();
        }

        return isValid;
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
    ['first_name', 'last_name', 'phone', 'email', 'cnic', 'shop_name', 'shop_address', 'account_type', 'account_name', 'account_number', 'transaction_id'].forEach(id => {
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
    // WebRTC Camera Capture Integration
    // -------------------------------------------------------------
    window.openCamModal = function(targetInputName) {
        activeCameraTarget = targetInputName;
        capturedTempDataUrl = null;

        const modal = $('#cameraModal');
        const video = document.getElementById('cameraVideo');
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
            'payment_screenshot': '{{ __("Capture Payment Receipt") }}'
        };
        const modalTitle = document.getElementById('cameraModalTitle');
        if (modalTitle) modalTitle.innerText = titles[targetInputName] || '{{ __("Take Photo with Camera") }}';

        modal.modal('show');

        // Camera facing: 'user' for selfie, 'environment' for documents
        const facing = (targetInputName === 'selfie_with_id') ? 'user' : 'environment';

        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({
                video: { facingMode: facing, width: { ideal: 1280 }, height: { ideal: 720 } },
                audio: false
            })
            .then(function(stream) {
                cameraStream = stream;
                video.srcObject = stream;
                video.play();
            })
            .catch(function(err) {
                // Fallback without facingMode constraint
                navigator.mediaDevices.getUserMedia({ video: true, audio: false })
                .then(function(stream) {
                    cameraStream = stream;
                    video.srcObject = stream;
                    video.play();
                })
                .catch(function(e) {
                    errDiv.innerText = "{{ __('Unable to access camera. Please allow camera permissions or upload file directly from your device.') }}";
                    errDiv.classList.remove('d-none');
                });
            });
        } else {
            errDiv.innerText = "{{ __('Camera API is not supported on this browser. Please choose a file.') }}";
            errDiv.classList.remove('d-none');
        }
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
    });
})();
</script>
@endsection
