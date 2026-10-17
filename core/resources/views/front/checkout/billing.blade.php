@extends('master.front')

@section('title')
    {{ __('Address') }}
@endsection

@section('content')
    <!-- Page Title-->
    <div class="page-title">
        <div class="container">
            <div class="column">
                <ul class="breadcrumbs">
                    <li><a href="{{ route('front.index') }}">{{ __('Home') }}</a> </li>
                    <li class="separator"></li>
                    <li>{{ __('Address') }}</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Page Content-->
    <div class="container padding-bottom-3x mb-1 checkut-page">
        <div class="row">
            <!-- Address-->
            <div class="col-xl-9 col-lg-8">
                <div class="steps flex-sm-nowrap mb-5">
                    <a class="step active" href="{{ route('front.checkout.billing') }}">
                        <h4 class="step-title">1. {{ __('Address') }}:</h4>
                    </a>
                    <a class="step" href="{{ route('front.checkout.payment') }}">
                        <h4 class="step-title">2. {{ __('Review and pay') }}</h4>
                    </a>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h6>{{ __('Address') }}</h6>

                        @php
                            $bill = Session::get('billing_address');
                            $selectedProvince = old('bill_province', (isset($bill['bill_province']) ? $bill['bill_province'] : (isset($user) ? $user->bill_state : '')));
                        @endphp

                        <form id="checkoutBilling" action="{{ route('front.checkout.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="checkout-fn">{{ __('First Name') }}*</label>
                                        <input class="form-control {{ $errors->has('bill_first_name') ? 'requireInput' : '' }}" name="bill_first_name" type="text" 
                                            id="checkout-fn" value="{{ old('bill_first_name', (isset($bill['bill_first_name']) ? $bill['bill_first_name'] : (isset($user) ? $user->first_name : ''))) }}">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="checkout-ln">{{ __('Last Name') }}*</label>
                                        <input class="form-control {{ $errors->has('bill_last_name') ? 'requireInput' : '' }}" name="bill_last_name" type="text" 
                                            id="checkout-ln" value="{{ old('bill_last_name', (isset($bill['bill_last_name']) ? $bill['bill_last_name'] : (isset($user) ? $user->last_name : ''))) }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="checkout_email_billing">{{ __('E-mail Address') }}*</label>
                                        <input class="form-control {{ $errors->has('bill_email') ? 'requireInput' : '' }}" name="bill_email" type="email" 
                                            id="checkout_email_billing" value="{{ old('bill_email', (isset($bill['bill_email']) ? $bill['bill_email'] : (isset($user) ? $user->email : ''))) }}">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="checkout-phone">{{ __('Phone Number') }}*</label>
                                        <input class="form-control {{ $errors->has('bill_phone') ? 'requireInput' : '' }}" name="bill_phone" type="text" id="checkout-phone"
                                             value="{{ old('bill_phone', (isset($bill['bill_phone']) ? $bill['bill_phone'] : (isset($user) ? $user->phone : ''))) }}">
                                    </div>
                                </div>
                            </div>
                            @if (PriceHelper::CheckDigital())
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="checkout-province">{{ __('Province') }}*</label>
                                            <select class="form-control {{ $errors->has('bill_province') ? 'requireInput' : '' }}" name="bill_province" id="checkout-province" required>
                                                <option value="" disabled {{ empty($selectedProvince) ? 'selected' : '' }}>{{ __('Select Province') }}</option>
                                                <option value="Azad Kashmir" {{ $selectedProvince == 'Azad Kashmir' ? 'selected' : '' }}>Azad Kashmir</option>
                                                <option value="Balochistan" {{ $selectedProvince == 'Balochistan' ? 'selected' : '' }}>Balochistan</option>
                                                <option value="Federally Administered Tribal Areas" {{ $selectedProvince == 'Federally Administered Tribal Areas' ? 'selected' : '' }}>Federally Administered Tribal Areas</option>
                                                <option value="Gilgit-Baltistan" {{ $selectedProvince == 'Gilgit-Baltistan' ? 'selected' : '' }}>Gilgit-Baltistan</option>
                                                <option value="Islamabad" {{ $selectedProvince == 'Islamabad' ? 'selected' : '' }}>Islamabad</option>
                                                <option value="Khyber Pakhtunkhwa" {{ $selectedProvince == 'Khyber Pakhtunkhwa' ? 'selected' : '' }}>Khyber Pakhtunkhwa</option>
                                                <option value="Punjab" {{ $selectedProvince == 'Punjab' ? 'selected' : '' }}>Punjab</option>
                                                <option value="Sindh" {{ $selectedProvince == 'Sindh' ? 'selected' : '' }}>Sindh</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="checkout-city">{{ __('City') }}*</label>
                                            <input class="form-control {{ $errors->has('bill_city') ? 'requireInput' : '' }}" name="bill_city" type="text" 
                                                id="checkout-city" placeholder="{{ __('Enter your city') }}" value="{{ old('bill_city', (isset($bill['bill_city']) ? $bill['bill_city'] : (isset($user) ? $user->bill_city : ''))) }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="checkout-area">{{ __('Area') }}*</label>
                                            <input class="form-control {{ $errors->has('bill_area') ? 'requireInput' : '' }}" name="bill_area" type="text" 
                                                id="checkout-area" placeholder="{{ __('Enter your area or neighborhood') }}" value="{{ old('bill_area', (isset($bill['bill_area']) ? $bill['bill_area'] : '')) }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="checkout-locality">{{ __('Colony / Suburb / Locality / Landmark') }}</label>
                                            <input class="form-control {{ $errors->has('bill_locality') ? 'requireInput' : '' }}" name="bill_locality" type="text" 
                                                id="checkout-locality" placeholder="{{ __('Enter colony, sector, street area or nearby landmark') }}" value="{{ old('bill_locality', (isset($bill['bill_locality']) ? $bill['bill_locality'] : (isset($bill['bill_address2']) ? $bill['bill_address2'] : ''))) }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="checkout-address1">{{ __('Complete Address') }}*</label>
                                            <textarea class="form-control {{ $errors->has('bill_address1') ? 'requireInput' : '' }}" name="bill_address1" rows="3"
                                                id="checkout-address1" placeholder="{{ __('House No. 123, Street 5, Sector G-11, near [Landmark]') }}">{{ old('bill_address1', (isset($bill['bill_address1']) ? $bill['bill_address1'] : (isset($user) ? $user->bill_address1 : ''))) }}</textarea>
                                            <small class="form-text text-muted">{{ __('Enter your house or flat number, street number, building name and any details that will help the courier find your location.') }}</small>
                                        </div>
                                    </div>
                                </div>

                                {{-- Preserved field for checkout compatibility --}}
                                <input type="hidden" name="bill_zip" value="{{ old('bill_zip', (isset($bill['bill_zip']) ? $bill['bill_zip'] : (isset($user) && $user->bill_zip ? $user->bill_zip : 'N/A'))) }}">
                            @endif

                            @if ($setting->is_privacy_trams == 1)
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input" type="checkbox" id="trams__condition">
                                        <label class="custom-control-label" for="trams__condition">This site is protected
                                            by reCAPTCHA and the <a href="{{ $setting->policy_link }}"
                                                target="_blank">Privacy Policy</a> and <a
                                                href="{{ $setting->terms_link }}" target="_blank">Terms of Service</a>
                                            apply.</label>
                                    </div>
                                </div>
                            @endif

                            <div class="d-flex justify-content-between paddin-top-1x mt-4">
                                <a class="btn btn-primary btn-sm" href="{{ route('front.cart') }}"><span
                                        class="hidden-xs-down"><i
                                            class="icon-arrow-left"></i>{{ __('Back To Cart') }}</span></a>
                                @if ($setting->is_privacy_trams == 1)
                                    <button disabled id="continue__button" class="btn btn-primary  btn-sm"
                                        type="button"><span class="hidden-xs-down">{{ __('Continue') }}</span><i
                                            class="icon-arrow-right"></i></button>
                                @else
                                    <button class="btn btn-primary btn-sm" type="submit"><span
                                            class="hidden-xs-down">{{ __('Continue') }}</span><i
                                            class="icon-arrow-right"></i></button>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Sidebar  -->
            <div class="col-xl-3 col-lg-4">
                @include('includes.checkout_sitebar', ['cart' => $cart, 'hideOrderSummary' => true])
            </div>
        </div>
    </div>

    @php $checkoutMsg = DB::table('checkout_messages')->where('is_active', 1)->first(); @endphp
    @if($checkoutMsg)
    <div id="checkout-popup-overlay" style="display:flex;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.6);z-index:99999;align-items:center;justify-content:center;">
        <div style="background:#fff;border-radius:10px;padding:30px;max-width:500px;width:90%;position:relative;box-shadow:0 10px 40px rgba(0,0,0,0.3);">
            <button onclick="document.getElementById('checkout-popup-overlay').style.display='none'" style="position:absolute;top:10px;right:15px;background:none;border:none;font-size:22px;cursor:pointer;color:#333;">&times;</button>
            <h5 style="margin-bottom:15px;color:#333;"><i class="fas fa-info-circle" style="color:#7952b3;"></i> Notice</h5>
            <div id="msg-short" style="color:#555;line-height:1.7;max-height:80px;overflow:hidden;">{{ $checkoutMsg->message }}</div>
            <div id="msg-full" style="color:#555;line-height:1.7;display:none;">{{ $checkoutMsg->message }}</div>
            @if(strlen($checkoutMsg->message) > 150)
            <a href="javascript:;" id="read-more-btn" onclick="document.getElementById('msg-short').style.display='none';document.getElementById('msg-full').style.display='block';this.style.display='none';" style="color:#7952b3;font-size:13px;">Read more...</a>
            @endif
            <button onclick="document.getElementById('checkout-popup-overlay').style.display='none'" class="btn btn-primary mt-3" style="width:100%;">OK, Got it</button>
        </div>
    </div>
    @endif
@endsection
