@extends('master.front')

@section('title')
    {{ __('Address') }}
@endsection

@section('content')
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

    <div class="container padding-bottom-3x mb-1 checkut-page">

        @if(isset($extra_settings) && $extra_settings->is_checkout_notice == 1)
            <div class="nmz-single-notice-box">
                <div class="nmz-notice-icon-wrapper">
                    <i class="icon-info"></i>
                </div>
                <div class="nmz-notice-body">
                    @if(!empty($extra_settings->checkout_notice_title))
                        <h4 class="nmz-notice-heading">{{ $extra_settings->checkout_notice_title }}</h4>
                    @endif
                    
                    <div class="nmz-notice-text">
                        @if(!empty($extra_settings->checkout_notice_en))
                            {!! nl2br(e($extra_settings->checkout_notice_en)) !!}
                        @endif
                        
                        @if(!empty($extra_settings->checkout_notice_ur))
                            <div class="nmz-urdu-text mt-2" dir="rtl" style="text-align: right; font-size: 15px;">
                                {!! nl2br(e($extra_settings->checkout_notice_ur)) !!}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <style>
                .nmz-single-notice-box {
                    width: 100%;
                    max-width: 100%;
                    box-sizing: border-box;
                    display: flex;
                    align-items: flex-start;
                    gap: 15px;
                    margin: 0 0 25px 0;
                    padding: 18px 20px;
                    background-color: #fcfaff;
                    border: 1px solid #7952b3;
                    border-left: 5px solid #7952b3;
                    border-radius: 10px;
                    box-shadow: 0 4px 12px rgba(121, 82, 179, 0.08);
                    font-family: inherit;
                }

                .nmz-notice-icon-wrapper {
                    width: 38px;
                    height: 38px;
                    min-width: 38px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    background: #7952b3;
                    color: #ffffff;
                    border-radius: 50%;
                    font-size: 18px;
                    box-shadow: 0 2px 6px rgba(121, 82, 179, 0.3);
                }

                .nmz-notice-body {
                    flex: 1;
                    min-width: 0;
                }

                .nmz-notice-heading {
                    margin: 0 0 8px 0;
                    color: #4a2c7a;
                    font-size: 18px;
                    font-weight: 700;
                    line-height: 1.3;
                }

                .nmz-notice-text {
                    color: #333333;
                    font-size: 14px;
                    line-height: 1.7;
                    word-wrap: break-word;
                    overflow-wrap: break-word;
                }

                .nmz-notice-text a {
                    color: #7952b3;
                    text-decoration: underline;
                    font-weight: 600;
                }

                @media (max-width: 576px) {
                    .nmz-single-notice-box {
                        padding: 12px 14px;
                        gap: 10px;
                        margin-bottom: 18px;
                        border-left-width: 4px;
                        border-radius: 8px;
                    }

                    .nmz-notice-icon-wrapper {
                        width: 30px;
                        height: 30px;
                        min-width: 30px;
                        font-size: 15px;
                    }

                    .nmz-notice-heading {
                        font-size: 15px;
                        margin-bottom: 5px;
                    }

                    .nmz-notice-text {
                        font-size: 13px;
                        line-height: 1.6;
                    }
                }
            </style>
        @endif

        <div class="row">
            <div class="col-xl-8 col-lg-8">
                <div class="row">
                    <div class="col-12">
                        <section class="card widget widget-featured-posts widget-featured-products p-4">
                            <h3 class="widget-title">{{ __('Items In Your Cart') }}</h3>
                            @foreach ($cart as $key => $item)
                                <div class="entry">
                                    <div class="entry-thumb"><a href="{{ route('front.product', $item['slug']) }}"><img
                                                src="{{ url('/core/public/storage/images/' . $item['photo']) }}" alt="Product"></a>
                                    </div>
                                    <div class="entry-content">
                                        <h4 class="entry-title"><a href="{{ route('front.product', $item['slug']) }}">
                                                {{ Str::limit($item['name'], 45) }}

                                            </a></h4>
                                        <span class="entry-meta">{{ $item['qty'] }} x
                                            {{ PriceHelper::setCurrencyPrice($item['main_price']) }}.</span>

                                        @foreach ($item['attribute']['option_name'] as $optionkey => $option_name)
                                            <span class="entry-meta"><b>{{ $option_name }}</b> :
                                                {{ PriceHelper::setCurrencySign() }}{{ $item['attribute']['option_price'][$optionkey] }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </section>
                        <div class="card">
                            <div class="card-body">
                                <h6>{{ __('Address') }}</h6>
                                <form id="checkoutBilling" action="{{ route('front.checkout.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="single_page_checkout" value="1">
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
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="checkout_email_billing">{{ __('E-mail Address') }}*</label>
                                                <input class="form-control {{ $errors->has('bill_email') ? 'requireInput' : '' }}" name="bill_email" type="email" 
                                                    id="checkout_email_billing"
                                                    value="{{ isset($user) ? $user->email : '' }}">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="checkout-phone">{{ __('Phone Number') }}*</label>
                                                <input class="form-control {{ $errors->has('bill_phone') ? 'requireInput' : '' }}" name="bill_phone" type="text"
                                                    id="checkout-phone" 
                                                    value="{{ isset($user) ? $user->phone : '' }}">
                                            </div>
                                        </div>
                                    </div>
                                    @php
                                        $bill = Session::get('billing_address');
                                        $selectedProvince = old('bill_province', (isset($bill['bill_province']) ? $bill['bill_province'] : (isset($user) ? $user->bill_state : '')));
                                    @endphp
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
                                                        id="checkout-city" placeholder="{{ __('Enter your city') }}"
                                                        value="{{ old('bill_city', (isset($bill['bill_city']) ? $bill['bill_city'] : (isset($user) ? $user->bill_city : ''))) }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="checkout-area">{{ __('Area') }}*</label>
                                                    <input class="form-control {{ $errors->has('bill_area') ? 'requireInput' : '' }}" name="bill_area" type="text" 
                                                        id="checkout-area" placeholder="{{ __('Enter your area or neighborhood') }}"
                                                        value="{{ old('bill_area', (isset($bill['bill_area']) ? $bill['bill_area'] : '')) }}">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="checkout-locality">{{ __('Colony / Suburb / Locality / Landmark') }}</label>
                                                    <input class="form-control {{ $errors->has('bill_locality') ? 'requireInput' : '' }}" name="bill_locality" type="text" 
                                                        id="checkout-locality" placeholder="{{ __('Enter colony, sector, street area or nearby landmark') }}"
                                                        value="{{ old('bill_locality', (isset($bill['bill_locality']) ? $bill['bill_locality'] : (isset($bill['bill_address2']) ? $bill['bill_address2'] : ''))) }}">
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
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-4">
                @include('includes.single_checkout_sidebar', $cart)
                @include('includes.single_checkout_modal')
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