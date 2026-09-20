@extends('master.front')

@section('title')
    {{ __('Billing') }}
@endsection

@section('content')
    <!-- Page Title-->
    <div class="page-title">
        <div class="container">
            <div class="column">
                <ul class="breadcrumbs">
                    <li><a href="{{ route('front.index') }}">{{ __('Home') }}</a> </li>
                    <li class="separator"></li>
                    <li>{{ __('Billing address') }}</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Page Content-->
    <div class="container padding-bottom-3x mb-1 checkut-page">
        <div class="row">
            <!-- Billing Adress-->
            <div class="col-xl-9 col-lg-8">
                <div class="steps flex-sm-nowrap mb-5">
                    <a class="step active" href="{{ route('front.checkout.billing') }}">
                        <h4 class="step-title">1. {{ __('Billing Address') }}:</h4>
                    </a>
                    <a class="step" href="javascript:;">
                        <h4 class="step-title">2. {{ __('Shipping Address') }}:</h4>
                    </a>
                    <a class="step" href="{{ route('front.checkout.payment') }}">
                        <h4 class="step-title">3. {{ __('Review and pay') }}</h4>
                    </a>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h6>{{ __('Billing Address') }}</h6>

                        <form id="checkoutBilling" action="{{ route('front.checkout.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="checkout-fn">{{ __('First Name') }}*</label>
                                        <input class="form-control {{ $errors->has('bill_first_name') ? 'requireInput' : '' }}" name="bill_first_name" type="text" 
                                            id="checkout-fn" value="{{ isset($user) ? $user->first_name : '' }}">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="checkout-ln">{{ __('Last Name') }}*</label>
                                        <input class="form-control {{ $errors->has('bill_last_name') ? 'requireInput' : '' }}" name="bill_last_name" type="text" 
                                            id="checkout-ln" value="{{ isset($user) ? $user->last_name : '' }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="checkout_email_billing">{{ __('E-mail Address') }}*</label>
                                        <input class="form-control {{ $errors->has('bill_email') ? 'requireInput' : '' }}" name="bill_email" type="email" 
                                            id="checkout_email_billing" value="{{ isset($user) ? $user->email : '' }}">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="checkout-phone">{{ __('Phone Number') }}*</label>
                                        <input class="form-control {{ $errors->has('bill_phone') ? 'requireInput' : '' }}" name="bill_phone" type="text" id="checkout-phone"
                                             value="{{ isset($user) ? $user->phone : '' }}">
                                    </div>
                                </div>
                            </div>
                            @if (PriceHelper::CheckDigital())
                                
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="checkout-address1">{{ __('Address') }}*</label>
                                            <input class="form-control {{ $errors->has('bill_address1') ? 'requireInput' : '' }}" name="bill_address1"  type="text"
                                                id="checkout-address1"
                                                value="{{ isset($user) ? $user->bill_address1 : '' }}">
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="checkout-zip">{{ __('Zip Code') }}*</label>
                                            <input class="form-control {{ $errors->has('bill_zip') ? 'requireInput' : '' }}" name="bill_zip" type="text" id="checkout-zip"
                                                value="{{ isset($user) ? $user->bill_zip : '' }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="checkout-city">{{ __('City') }}*</label>
                                            <input class="form-control {{ $errors->has('bill_city') ? 'requireInput' : '' }}" name="bill_city" type="text" 
                                                id="checkout-city" value="{{ isset($user) ? $user->bill_city : '' }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="checkout-country">{{ __('Country') }}</label>
                                            <select class="form-control {{ $errors->has('bill_country') ? 'requireInput' : '' }}"  name="bill_country"
                                                id="billing-country">
                                                <option selected>{{ __('Choose Country') }}</option>
                                                @foreach (DB::table('countries')->get() as $country)
                                                    <option value="{{ $country->name }}"
                                                        {{ isset($user) && $user->bill_country == $country->name ? 'selected' : '' }}>
                                                        {{ $country->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" type="checkbox" id="same_address"
                                        name="same_ship_address" {{ Session::has('shipping_address') ? 'checked' : '' }}>
                                    <label class="custom-control-label"
                                        for="same_address">{{ __('Same as billing address') }}</label>
                                </div>
                            </div>

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
