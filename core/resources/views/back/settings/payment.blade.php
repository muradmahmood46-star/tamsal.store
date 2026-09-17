@extends('master.back')

@section('content')
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-sm-flex align-items-center justify-content-between">
                    <h3 class=" mb-0"><b>{{ __('Payment Methods') }}</b></h3>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="row">

            <div class="col-xl-12 col-lg-12 col-md-12">

                <div class="card o-hidden border-0 shadow-lg">
                    <div class="card-body ">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="nav flex-column m-3 nav-pills nav-secondary" id="v-pills-tab" role="tablist"
                                    aria-orientation="vertical">

                                    <a class="nav-link active" data-toggle="pill"
                                        href="#cod">{{ $cod->name ?? __('Cash On Delivery') }}</a>
                                    <a class="nav-link" data-toggle="pill" href="#stripe">{{ $stripe->name ?? __('Easypaisa') }}</a>
                                    <a class="nav-link" data-toggle="pill" href="#authorize">{{ $authorize->name ?? __('JazzCash') }}</a>
                                    <a class="nav-link" data-toggle="pill" href="#bank">{{ $bank->name ?? __('Bank Transfer') }}</a>
                                    @if(isset($custom_payments) && count($custom_payments) > 0)
                                        @foreach($custom_payments as $cpay)
                                            <a class="nav-link" data-toggle="pill" href="#custom_{{ $cpay->id }}">{{ $cpay->name }}</a>
                                        @endforeach
                                    @endif

                                    <a class="nav-link text-white bg-primary font-weight-bold mt-3 text-center" data-toggle="pill" href="#add_payment_tab">
                                        <i class="fas fa-plus-circle"></i> {{ __('Add Payment Method') }}
                                    </a>

                                </div>
                            </div>
                            <div class="col-lg-9">
                                <div class="p-5">
                                    <div class="admin-form">

                                        @include('alerts.alerts')

                                        <div class="container pl-0 pr-0 ml-0 mr-0 w-100 mw-100">
                                            <div id="tabs">
                                                <!-- Tab panes -->
                                                <div class="tab-content">
                                                    <div id="cod" class="container tab-pane active"><br>

                                                        <div class="row justify-content-center">

                                                            <div class="col-lg-8">

                                                                <form action="{{ route('back.setting.payment.update') }}"
                                                                    method="POST" enctype="multipart/form-data">

                                                                    @csrf
                                                                    <input type="hidden" name="unique_keyword" value="cod">
                                                                    <div class="form-group">
                                                                        <label class="switch-primary">
                                                                            <input type="checkbox"
                                                                                class="switch switch-bootstrap "
                                                                                name="status" value="1"
                                                                                {{ $cod->status == 1 ? 'checked' : '' }}>
                                                                            <span class="switch-body"></span>
                                                                            <span
                                                                                class="switch-text">{{ __('Display Cash On Delivery') }}</span>
                                                                        </label>
                                                                    </div>

                                                                    <div
                                                                        class="image-show {{ $cod->status == 1 ? '' : 'd-none' }}">

                                                                        <div class="form-group">
                                                                            <label for="name">{{ __('Enter Name') }}
                                                                                *</label>
                                                                            <input type="text" class="form-control"
                                                                                name="name" id="name"
                                                                                value="{{ $cod->name }}">
                                                                        </div>

                                                                        <div class="form-group">
                                                                            <label
                                                                                for="name">{{ __('Current Image') }}</label>
                                                                            <div class="col-lg-12 pb-1">
                                                                                <img class="admin-setting-img"
                                                                                    src="{{ $cod->photo ? url('/core/public/storage/images/' . $cod->photo) : url('/core/public/storage/images/placeholder.png') }}"
                                                                                    alt="No Image Found">
                                                                            </div>
                                                                            <span>{{ __('Image Size Should Be 52 x 35.') }}</span>
                                                                        </div>

                                                                        <div class="form-group position-relative col-xl-12">
                                                                            <label class="file">
                                                                                <input type="file" accept="image/*"
                                                                                    class="upload-photo" name="photo"
                                                                                    id="file"
                                                                                    aria-label="File browser example">
                                                                                <span
                                                                                    class="file-custom text-left">{{ __('Upload Image...') }}</span>
                                                                            </label>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="text">{{ __('Enter Text') }}
                                                                                *</label>
                                                                            <textarea name="text" id="text" class="form-control " rows="5" placeholder="{{ __('Enter Text') }}">{{ $cod->text }}</textarea>
                                                                        </div>

                                                                    </div>

                                                                    <div>

                                                                        <div
                                                                            class="form-group d-flex justify-content-center">
                                                                            <button type="submit"
                                                                                class="btn btn-secondary ">{{ __('Submit') }}</button>
                                                                        </div>

                                                                    </div>

                                                                </form>

                                                            </div>

                                                        </div>

                                                    </div>

                                                    <div id="stripe" class="container tab-pane"><br>

                                                        <div class="row justify-content-center">

                                                            <div class="col-lg-8">

                                                                <form action="{{ route('back.setting.payment.update') }}"
                                                                    method="POST" enctype="multipart/form-data">

                                                                    @csrf
                                                                    <input type="hidden" name="unique_keyword" value="stripe">

                                                                    <div class="form-group">
                                                                        <label class="switch-primary">
                                                                            <input type="checkbox"
                                                                                class="switch switch-bootstrap "
                                                                                name="status" value="1"
                                                                                {{ $stripe->status == 1 ? 'checked' : '' }}>
                                                                            <span class="switch-body"></span>
                                                                            <span
                                                                                class="switch-text">{{ __('Display Easypaisa') }}</span>
                                                                        </label>
                                                                    </div>


                                                                    <div
                                                                        class="image-show {{ $stripe->status == 1 ? '' : 'd-none' }}">

                                                                        <div class="form-group">
                                                                            <label
                                                                                for="name">{{ __('Current Image') }}</label>
                                                                            <div class="col-lg-12 pb-1">
                                                                                <img class="admin-setting-img"
                                                                                    src="{{ $stripe->photo ? url('/core/public/storage/images/' . $stripe->photo) : url('/core/public/storage/images/placeholder.png') }}"
                                                                                    stripe="No Image Found">
                                                                            </div>
                                                                            <span>{{ __('Image Size Should Be 52 x 35.') }}</span>
                                                                        </div>

                                                                        <div
                                                                            class="form-group position-relative col-xl-12">
                                                                            <label class="file">
                                                                                <input type="file" accept="image/*"
                                                                                    class="upload-photo" name="photo"
                                                                                    id="file"
                                                                                    aria-label="File browser example">
                                                                                <span
                                                                                    class="file-custom text-left">{{ __('Upload Image...') }}</span>
                                                                            </label>
                                                                        </div>

                                                                        <div class="form-group">
                                                                            <label for="name">{{ __('Enter Name') }}
                                                                                *</label>
                                                                            <input type="text" class="form-control"
                                                                                name="name" id="name"
                                                                                value="{{ $stripe->name }}">
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="stripe_account_name">{{ __('Account Holder Name') }}</label>
                                                                            <input type="text" class="form-control"
                                                                                id="stripe_account_name"
                                                                                name="pkey[account_name]"
                                                                                placeholder="{{ __('Enter Account Holder Name') }}"
                                                                                value="{{ $stripeData['account_name'] ?? '' }}">
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="stripe_account_number">{{ __('Account Number') }}</label>
                                                                            <input type="text" class="form-control"
                                                                                id="stripe_account_number"
                                                                                name="pkey[account_number]"
                                                                                placeholder="{{ __('Enter Account Number') }}"
                                                                                value="{{ $stripeData['account_number'] ?? '' }}">
                                                                        </div>


                                                                        <div class="form-group">
                                                                            <label for="text">{{ __('Enter Text') }}
                                                                                *</label>
                                                                            <textarea name="text" id="text" class="form-control " rows="5" placeholder="{{ __('Enter Text') }}">{{ $stripe->text }}</textarea>
                                                                        </div>

                                                                    </div>

                                                                    <div>

                                                                        <div
                                                                            class="form-group d-flex justify-content-center">
                                                                            <button type="submit"
                                                                                class="btn btn-secondary ">{{ __('Submit') }}</button>
                                                                        </div>

                                                                    </div>

                                                                </form>

                                                            </div>

                                                        </div>

                                                    </div>

                                                    <div id="paypal" class="container tab-pane"><br>

                                                        <div class="row justify-content-center">

                                                            <div class="col-lg-8">

                                                                <form action="{{ route('back.setting.payment.update') }}"
                                                                    method="POST" enctype="multipart/form-data">

                                                                    @csrf

                                                                    <div class="form-group">
                                                                        <label class="switch-primary">
                                                                            <input type="checkbox"
                                                                                class="switch switch-bootstrap "
                                                                                name="status" value="1"
                                                                                {{ $paypal->status == 1 ? 'checked' : '' }}>
                                                                            <span class="switch-body"></span>
                                                                            <span
                                                                                class="switch-text">{{ __('Display Paypal') }}</span>
                                                                        </label>
                                                                    </div>


                                                                    <div
                                                                        class="image-show {{ $paypal->status == 1 ? '' : 'd-none' }}">

                                                                        <div class="form-group">
                                                                            <label
                                                                                for="name">{{ __('Current Image') }}</label>
                                                                            <div class="col-lg-12 pb-1">
                                                                                <img class="admin-setting-img"
                                                                                    src="{{ $paypal->photo ? url('/core/public/storage/images/' . $paypal->photo) : url('/core/public/storage/images/placeholder.png') }}"
                                                                                    alt="No Image Found">
                                                                            </div>
                                                                            <span>{{ __('Image Size Should Be 52 x 35.') }}</span>
                                                                        </div>

                                                                        <div
                                                                            class="form-group position-relative col-xl-12">
                                                                            <label class="file">
                                                                                <input type="file" accept="image/*"
                                                                                    class="upload-photo" name="photo"
                                                                                    id="file"
                                                                                    aria-label="File browser example">
                                                                                <span
                                                                                    class="file-custom text-left">{{ __('Upload Image...') }}</span>
                                                                            </label>
                                                                        </div>


                                                                        <div class="form-group">
                                                                            <label for="name">{{ __('Enter Name') }}
                                                                                *</label>
                                                                            <input type="text" class="form-control"
                                                                                name="name" id="name"
                                                                                value="{{ $paypal->name }}">
                                                                        </div>

                                                                        @foreach ($paypalData as $pkey => $pdata)
                                                                            @if ($pkey == 'check_sandbox')
                                                                                <div class="form-group  col-xl-4 col-md-6">
                                                                                    <div
                                                                                        class="custom-control custom-checkbox">
                                                                                        <input type="checkbox"
                                                                                            name="pkey[{{ __($pkey) }}]"
                                                                                            class="custom-control-input"
                                                                                            {{ $pdata == 1 ? 'checked' : '' }}
                                                                                            id="{{ $pkey }}">
                                                                                        <label class="custom-control-label"
                                                                                            for="{{ $pkey }}">
                                                                                            {{ __($paypal->name . ' ' . ucwords(str_replace('_', ' ', $pkey))) }}
                                                                                        </label>
                                                                                    </div>
                                                                                </div>
                                                                            @else
                                                                                <div class="form-group">
                                                                                    <label
                                                                                        for="inp-{{ __($pkey) }}">{{ __($paypal->name . ' ' . ucwords(str_replace('_', ' ', $pkey))) }}</label>
                                                                                    <input type="text"
                                                                                        class="form-control"
                                                                                        id="inp-{{ __($pkey) }}"
                                                                                        name="pkey[{{ __($pkey) }}]"
                                                                                        placeholder="{{ __($paypal->name . ' ' . ucwords(str_replace('_', ' ', $pkey))) }}"
                                                                                        value="{{ $pdata }}">
                                                                                </div>
                                                                            @endif
                                                                        @endforeach

                                                                        <div class="form-group">
                                                                            <label for="text">{{ __('Enter Text') }}
                                                                                *</label>
                                                                            <textarea name="text" id="text" class="form-control " rows="5" placeholder="{{ __('Enter Text') }}">{{ $paypal->text }}</textarea>
                                                                        </div>

                                                                        <input type="hidden" name="unique_keyword"
                                                                            value="paypal">

                                                                    </div>

                                                                    <div>

                                                                        <div
                                                                            class="form-group d-flex justify-content-center">
                                                                            <button type="submit"
                                                                                class="btn btn-secondary ">{{ __('Submit') }}</button>
                                                                        </div>

                                                                    </div>

                                                                </form>

                                                            </div>

                                                        </div>

                                                    </div>
                                                    <div id="molly" class="container tab-pane"><br>

                                                        <div class="row justify-content-center">

                                                            <div class="col-lg-8">

                                                                <form action="{{ route('back.setting.payment.update') }}"
                                                                    method="POST" enctype="multipart/form-data">

                                                                    @csrf

                                                                    <div class="form-group">
                                                                        <label class="switch-primary">
                                                                            <input type="checkbox"
                                                                                class="switch switch-bootstrap "
                                                                                name="status" value="1"
                                                                                {{ $molly->status == 1 ? 'checked' : '' }}>
                                                                            <span class="switch-body"></span>
                                                                            <span
                                                                                class="switch-text">{{ __('Display Mollie') }}</span>
                                                                        </label>
                                                                    </div>



                                                                    <div
                                                                        class="image-show {{ $molly->status == 1 ? '' : 'd-none' }}">

                                                                        <div class="form-group">
                                                                            <label
                                                                                for="name">{{ __('Current Image') }}</label>
                                                                            <div class="col-lg-12 pb-1">
                                                                                <img class="admin-setting-img"
                                                                                    src="{{ $molly->photo ? url('/core/public/storage/images/' . $molly->photo) : url('/core/public/storage/images/placeholder.png') }}"
                                                                                    alt="No Image Found">
                                                                            </div>
                                                                            <span>{{ __('Image Size Should Be 52 x 35.') }}</span>
                                                                        </div>

                                                                        <div
                                                                            class="form-group position-relative col-xl-12">
                                                                            <label class="file">
                                                                                <input type="file" accept="image/*"
                                                                                    class="upload-photo" name="photo"
                                                                                    id="file"
                                                                                    aria-label="File browser example">
                                                                                <span
                                                                                    class="file-custom text-left">{{ __('Upload Image...') }}</span>
                                                                            </label>
                                                                        </div>

                                                                        <div class="form-group">
                                                                            <label for="name">{{ __('Enter Name') }}
                                                                                *</label>
                                                                            <input type="text" class="form-control"
                                                                                name="name" id="name"
                                                                                value="{{ $molly->name }}">
                                                                        </div>

                                                                        @foreach ($mollyData as $pkey => $pdata)
                                                                            <div class="form-group">
                                                                                <label
                                                                                    for="inp-{{ __($pkey) }}">{{ __($molly->name . ' ' . ucwords(str_replace('_', ' ', $pkey))) }}</label>
                                                                                <input type="text" class="form-control"
                                                                                    id="inp-{{ __($pkey) }}"
                                                                                    name="pkey[{{ __($pkey) }}]"
                                                                                    placeholder="{{ __($molly->name . ' ' . ucwords(str_replace('_', ' ', $pkey))) }}"
                                                                                    value="{{ $pdata }}">
                                                                            </div>
                                                                        @endforeach

                                                                        <input type="hidden" name="unique_keyword"
                                                                            value="mollie">

                                                                        <div class="form-group">
                                                                            <label for="text">{{ __('Enter Text') }}
                                                                                *</label>
                                                                            <textarea name="text" id="text" class="form-control " rows="5" placeholder="{{ __('Enter Text') }}">{{ $molly->text }}</textarea>
                                                                        </div>

                                                                    </div>

                                                                    <div>

                                                                        <div
                                                                            class="form-group d-flex justify-content-center">
                                                                            <button type="submit"
                                                                                class="btn btn-secondary ">{{ __('Submit') }}</button>
                                                                        </div>

                                                                    </div>

                                                                </form>

                                                            </div>

                                                        </div>

                                                    </div>

                                                    <div id="paytm" class="container tab-pane"><br>

                                                        <div class="row justify-content-center">

                                                            <div class="col-lg-8">

                                                                <form action="{{ route('back.setting.payment.update') }}"
                                                                    method="POST" enctype="multipart/form-data">

                                                                    @csrf

                                                                    <div class="form-group">
                                                                        <label class="switch-primary">
                                                                            <input type="checkbox"
                                                                                class="switch switch-bootstrap "
                                                                                name="status" value="1"
                                                                                {{ $paytm->status == 1 ? 'checked' : '' }}>
                                                                            <span class="switch-body"></span>
                                                                            <span
                                                                                class="switch-text">{{ __('Display Paytm') }}</span>
                                                                        </label>
                                                                    </div>



                                                                    <div
                                                                        class="image-show {{ $paytm->status == 1 ? '' : 'd-none' }}">

                                                                        <div class="form-group col-xl-12">
                                                                            <label
                                                                                for="name">{{ __('Current Image') }}</label>
                                                                            <div class="col-lg-12 pb-1">
                                                                                <img class="admin-setting-img"
                                                                                    src="{{ $paytm->photo ? url('/core/public/storage/images/' . $paytm->photo) : url('/core/public/storage/images/placeholder.png') }}"
                                                                                    stripe="No Image Found">
                                                                            </div>
                                                                            <span>{{ __('Image Size Should Be 52 x 35.') }}</span>
                                                                        </div>

                                                                        <div
                                                                            class="form-group position-relative col-xl-12">
                                                                            <label class="file">
                                                                                <input type="file" class="upload-photo"
                                                                                    name="photo" id="file"
                                                                                    aria-label="File browser example">
                                                                                <span
                                                                                    class="file-custom text-left">{{ __('Upload Image...') }}</span>
                                                                            </label>
                                                                        </div>

                                                                        <div class="form-group">
                                                                            <label for="name">{{ __('Enter Name') }}
                                                                                *</label>
                                                                            <input type="text" class="form-control"
                                                                                name="name" id="name"
                                                                                value="{{ $paytm->name }}">
                                                                        </div>

                                                                        @foreach ($paytmData as $pakey => $paytmDat)
                                                                            @if ($pakey == 'paytm_mode')
                                                                                <div class="form-group  col-xl-4 col-md-6">
                                                                                    <div
                                                                                        class="custom-control custom-checkbox">
                                                                                        <input type="checkbox"
                                                                                            name="pkey[{{ __($pakey) }}]"
                                                                                            class="custom-control-input"
                                                                                            {{ $paytmDat == 1 ? 'checked' : '' }}
                                                                                            id="{{ $pakey }}"
                                                                                            value="1">
                                                                                        <label class="custom-control-label"
                                                                                            for="{{ $pakey }}">
                                                                                            {{ __(ucwords(str_replace('_', ' ', $pakey))) }}
                                                                                        </label>
                                                                                    </div>
                                                                                </div>
                                                                            @else
                                                                                <div class="form-group">
                                                                                    <label
                                                                                        for="inp-{{ __($pakey) }}">{{ __($paytm->name . ' ' . ucwords(str_replace('_', ' ', $pakey))) }}</label>
                                                                                    <input type="text"
                                                                                        class="form-control"
                                                                                        id="inp-{{ __($pakey) }}"
                                                                                        name="pkey[{{ __($pakey) }}]"
                                                                                        placeholder="{{ __($paytm->name . ' ' . ucwords(str_replace('_', ' ', $pakey))) }}"
                                                                                        value="{{ $paytmDat }}">
                                                                                </div>
                                                                            @endif
                                                                        @endforeach

                                                                        <div class="form-group">
                                                                            <label for="text">{{ __('Enter Text') }}
                                                                                *</label>
                                                                            <textarea name="text" id="text" class="form-control " rows="5"
                                                                                placeholder="{{ __('Enter Text') }}">{{ $paytm->text }}</textarea>
                                                                        </div>

                                                                        <input type="hidden" name="unique_keyword"
                                                                            value="paytm">

                                                                    </div>

                                                                    <div>

                                                                        <div
                                                                            class="form-group d-flex justify-content-center">
                                                                            <button type="submit"
                                                                                class="btn btn-secondary btn-block w-50">{{ __('Submit') }}</button>
                                                                        </div>

                                                                    </div>

                                                                </form>

                                                            </div>

                                                        </div>

                                                    </div>

                                                    <div id="sslcommerz" class="container tab-pane"><br>

                                                        <div class="row justify-content-center">

                                                            <div class="col-lg-8">

                                                                <form action="{{ route('back.setting.payment.update') }}"
                                                                    method="POST" enctype="multipart/form-data">

                                                                    @csrf

                                                                    <div class="form-group">
                                                                        <label class="switch-primary">
                                                                            <input type="checkbox"
                                                                                class="switch switch-bootstrap "
                                                                                name="status" value="1"
                                                                                {{ $sslcommerz->status == 1 ? 'checked' : '' }}>
                                                                            <span class="switch-body"></span>
                                                                            <span
                                                                                class="switch-text">{{ __('Display sslcommerz') }}</span>
                                                                        </label>
                                                                    </div>


                                                                    <div
                                                                        class="image-show {{ $sslcommerz->status == 1 ? '' : 'd-none' }}">

                                                                        <div class="form-group col-xl-12">
                                                                            <label
                                                                                for="name">{{ __('Current Image') }}</label>
                                                                            <div class="col-lg-12 pb-1">
                                                                                <img class="admin-setting-img"
                                                                                    src="{{ $sslcommerz->photo ? url('/core/public/storage/images/' . $sslcommerz->photo) : url('/core/public/storage/images/placeholder.png') }}"
                                                                                    stripe="No Image Found">
                                                                            </div>
                                                                            <span>{{ __('Image Size Should Be 52 x 35.') }}</span>
                                                                        </div>

                                                                        <div
                                                                            class="form-group position-relative col-xl-12">
                                                                            <label class="file">
                                                                                <input type="file" class="upload-photo"
                                                                                    name="photo" id="file"
                                                                                    aria-label="File browser example">
                                                                                <span
                                                                                    class="file-custom text-left">{{ __('Upload Image...') }}</span>
                                                                            </label>
                                                                        </div>


                                                                        <div class="form-group">
                                                                            <label for="name">{{ __('Enter Name') }}
                                                                                *</label>
                                                                            <input type="text" class="form-control"
                                                                                name="name" id="name"
                                                                                value="{{ $sslcommerz->name }}">
                                                                        </div>

                                                                        @foreach ($sslcommerzData as $pkey => $sslcommerzData)
                                                                            @if ($pkey == 'check_sandbox')
                                                                                <div class="form-group  col-xl-4 col-md-6">
                                                                                    <div
                                                                                        class="custom-control custom-checkbox">
                                                                                        <input type="checkbox"
                                                                                            name="pkey[{{ __($pkey) }}]"
                                                                                            class="custom-control-input"
                                                                                            {{ $sslcommerzData == 1 ? 'checked' : '' }}
                                                                                            id="ssl{{ $pkey }}">
                                                                                        <label class="custom-control-label"
                                                                                            for="ssl{{ $pkey }}">
                                                                                            {{ __($sslcommerz->name . ' ' . ucwords(str_replace('_', ' ', $pkey))) }}
                                                                                        </label>
                                                                                    </div>
                                                                                </div>
                                                                            @else
                                                                                <div class="form-group col-xl-12">
                                                                                    <label
                                                                                        for="inp-{{ __($pkey) }}">{{ __($sslcommerz->name . ' ' . ucwords(str_replace('_', ' ', $pkey))) }}</label>
                                                                                    <input type="text"
                                                                                        class="form-control"
                                                                                        id="inp-{{ __($pkey) }}"
                                                                                        name="pkey[{{ __($pkey) }}]"
                                                                                        placeholder="{{ __($sslcommerz->name . ' ' . ucwords(str_replace('_', ' ', $pkey))) }}"
                                                                                        value="{{ $sslcommerzData }}"
                                                                                        required>
                                                                                </div>
                                                                            @endif
                                                                        @endforeach

                                                                        <div class="form-group">
                                                                            <label for="text">{{ __('Enter Text') }}
                                                                                *</label>
                                                                            <textarea name="text" id="text" class="form-control " rows="5"
                                                                                placeholder="{{ __('Enter Text') }}">{{ $sslcommerz->text }}</textarea>
                                                                        </div>

                                                                        <input type="hidden" name="unique_keyword"
                                                                            value="sslcommerz">

                                                                    </div>

                                                                    <div>

                                                                        <div
                                                                            class="form-group d-flex justify-content-center">
                                                                            <button type="submit"
                                                                                class="btn btn-secondary btn-block w-50">{{ __('Submit') }}</button>
                                                                        </div>

                                                                    </div>

                                                                </form>

                                                            </div>

                                                        </div>

                                                    </div>

                                                    <div id="mercadopago" class="container tab-pane"><br>

                                                        <div class="row justify-content-center">

                                                            <div class="col-lg-8">

                                                                <form action="{{ route('back.setting.payment.update') }}"
                                                                    method="POST" enctype="multipart/form-data">

                                                                    @csrf

                                                                    <div class="form-group">
                                                                        <label class="switch-primary">
                                                                            <input type="checkbox"
                                                                                class="switch switch-bootstrap "
                                                                                name="status" value="1"
                                                                                {{ $mercadopago->status == 1 ? 'checked' : '' }}>
                                                                            <span class="switch-body"></span>
                                                                            <span
                                                                                class="switch-text">{{ __('Display Mercadopago') }}</span>
                                                                        </label>
                                                                    </div>



                                                                    <div
                                                                        class="image-show {{ $mercadopago->status == 1 ? '' : 'd-none' }}">

                                                                        <div class="form-group col-xl-12">
                                                                            <label
                                                                                for="name">{{ __('Current Image') }}</label>
                                                                            <div class="col-lg-12 pb-1">
                                                                                <img class="admin-setting-img"
                                                                                    src="{{ $mercadopago->photo ? url('/core/public/storage/images/' . $mercadopago->photo) : url('/core/public/storage/images/placeholder.png') }}"
                                                                                    stripe="No Image Found">
                                                                            </div>
                                                                            <span>{{ __('Image Size Should Be 52 x 35.') }}</span>
                                                                        </div>

                                                                        <div
                                                                            class="form-group position-relative col-xl-12">
                                                                            <label class="file">
                                                                                <input type="file" class="upload-photo"
                                                                                    name="photo" id="file"
                                                                                    aria-label="File browser example">
                                                                                <span
                                                                                    class="file-custom text-left">{{ __('Upload Image...') }}</span>
                                                                            </label>
                                                                        </div>

                                                                        <div class="form-group">
                                                                            <label for="name">{{ __('Enter Name') }}
                                                                                *</label>
                                                                            <input type="text" class="form-control"
                                                                                name="name" id="name"
                                                                                value="{{ $mercadopago->name }}">
                                                                        </div>

                                                                        @foreach ($mercadopagoData as $pkey => $mercadopagoData)
                                                                            @if ($pkey == 'check_sandbox')
                                                                                <div class="form-group  col-xl-4 col-md-6">
                                                                                    <div
                                                                                        class="custom-control custom-checkbox">
                                                                                        <input type="checkbox"
                                                                                            name="pkey[{{ __($pkey) }}]"
                                                                                            class="custom-control-input"
                                                                                            {{ $mercadopagoData == 1 ? 'checked' : '' }}
                                                                                            id="authorize{{ $pkey }}">
                                                                                        <label class="custom-control-label"
                                                                                            for="authorize{{ $pkey }}">
                                                                                            {{ __($mercadopago->name . ' ' . ucwords(str_replace('_', ' ', $pkey))) }}
                                                                                        </label>
                                                                                    </div>
                                                                                </div>
                                                                            @else
                                                                                <div class="form-group col-xl-12">
                                                                                    <label
                                                                                        for="inp-{{ __($pkey) }}">{{ __($mercadopago->name . ' ' . ucwords(str_replace('_', ' ', $pkey))) }}</label>
                                                                                    <input type="text"
                                                                                        class="form-control"
                                                                                        id="inp-{{ __($pkey) }}"
                                                                                        name="pkey[{{ __($pkey) }}]"
                                                                                        placeholder="{{ __($mercadopago->name . ' ' . ucwords(str_replace('_', ' ', $pkey))) }}"
                                                                                        value="{{ $mercadopagoData }}"
                                                                                        required>
                                                                                </div>
                                                                            @endif
                                                                        @endforeach

                                                                        <div class="form-group">
                                                                            <label for="text">{{ __('Enter Text') }}
                                                                                *</label>
                                                                            <textarea name="text" id="text" class="form-control " rows="5"
                                                                                placeholder="{{ __('Enter Text') }}">{{ $mercadopago->text }}</textarea>
                                                                        </div>

                                                                        <input type="hidden" name="unique_keyword"
                                                                            value="mercadopago">

                                                                    </div>

                                                                    <div>

                                                                        <div
                                                                            class="form-group d-flex justify-content-center">
                                                                            <button type="submit"
                                                                                class="btn btn-secondary btn-block w-50">{{ __('Submit') }}</button>
                                                                        </div>

                                                                    </div>

                                                                </form>

                                                            </div>

                                                        </div>

                                                    </div>

                                                    <div id="authorize" class="container tab-pane"><br>

                                                        <div class="row justify-content-center">

                                                            <div class="col-lg-8">

                                                                <form action="{{ route('back.setting.payment.update') }}"
                                                                    method="POST" enctype="multipart/form-data">

                                                                    @csrf
                                                                    <input type="hidden" name="unique_keyword" value="authorize">

                                                                    <div class="form-group">
                                                                        <label class="switch-primary">
                                                                            <input type="checkbox"
                                                                                class="switch switch-bootstrap "
                                                                                name="status" value="1"
                                                                                {{ $authorize->status == 1 ? 'checked' : '' }}>
                                                                            <span class="switch-body"></span>
                                                                            <span
                                                                                class="switch-text">{{ __('Display JazzCash') }}</span>
                                                                        </label>
                                                                    </div>


                                                                    <div
                                                                        class="image-show {{ $authorize->status == 1 ? '' : 'd-none' }}">

                                                                        <div class="form-group col-xl-12">
                                                                            <label
                                                                                for="name">{{ __('Current Image') }}</label>
                                                                            <div class="col-lg-12 pb-1">
                                                                                <img class="admin-setting-img"
                                                                                    src="{{ $authorize->photo ? url('/core/public/storage/images/' . $authorize->photo) : url('/core/public/storage/images/placeholder.png') }}"
                                                                                    stripe="No Image Found">
                                                                            </div>
                                                                            <span>{{ __('Image Size Should Be 52 x 35.') }}</span>
                                                                        </div>

                                                                        <div
                                                                            class="form-group position-relative col-xl-12">
                                                                            <label class="file">
                                                                                <input type="file" class="upload-photo"
                                                                                    name="photo" id="file"
                                                                                    aria-label="File browser example">
                                                                                <span
                                                                                    class="file-custom text-left">{{ __('Upload Image...') }}</span>
                                                                            </label>
                                                                        </div>

                                                                        <div class="form-group">
                                                                            <label for="name">{{ __('Enter Name') }}
                                                                                *</label>
                                                                            <input type="text" class="form-control"
                                                                                name="name" id="name"
                                                                                value="{{ $authorize->name }}">
                                                                        </div>

                                                                        <div class="form-group col-xl-12">
                                                                            <label for="auth_account_name">{{ __('Account Holder Name') }}</label>
                                                                            <input type="text" class="form-control"
                                                                                id="auth_account_name"
                                                                                name="pkey[account_name]"
                                                                                placeholder="{{ __('Enter Account Holder Name') }}"
                                                                                value="{{ $authorizeData['account_name'] ?? '' }}">
                                                                        </div>
                                                                        <div class="form-group col-xl-12">
                                                                            <label for="auth_account_number">{{ __('Account Number') }}</label>
                                                                            <input type="text" class="form-control"
                                                                                id="auth_account_number"
                                                                                name="pkey[account_number]"
                                                                                placeholder="{{ __('Enter Account Number') }}"
                                                                                value="{{ $authorizeData['account_number'] ?? '' }}">
                                                                        </div>


                                                                        <div class="form-group">
                                                                            <label for="text">{{ __('Enter Text') }}
                                                                                *</label>
                                                                            <textarea name="text" id="text" class="form-control " rows="5"
                                                                                placeholder="{{ __('Enter Text') }}">{{ $authorize->text }}</textarea>
                                                                        </div>

                                                                    </div>

                                                                    <div>

                                                                        <div
                                                                            class="form-group d-flex justify-content-center">
                                                                            <button type="submit"
                                                                                class="btn btn-secondary btn-block w-50">{{ __('Submit') }}</button>
                                                                        </div>

                                                                    </div>

                                                                </form>

                                                            </div>

                                                        </div>

                                                    </div>

                                                    <div id="paystack" class="container tab-pane"><br>

                                                        <div class="row justify-content-center">

                                                            <div class="col-lg-8">

                                                                <form action="{{ route('back.setting.payment.update') }}"
                                                                    method="POST" enctype="multipart/form-data">

                                                                    @csrf

                                                                    <div class="form-group">
                                                                        <label class="switch-primary">
                                                                            <input type="checkbox"
                                                                                class="switch switch-bootstrap "
                                                                                name="status" value="1"
                                                                                {{ $paystack->status == 1 ? 'checked' : '' }}>
                                                                            <span class="switch-body"></span>
                                                                            <span
                                                                                class="switch-text">{{ __('Display Paystack') }}</span>
                                                                        </label>
                                                                    </div>



                                                                    <div
                                                                        class="image-show {{ $paystack->status == 1 ? '' : 'd-none' }}">

                                                                        <div class="form-group col-xl-12">
                                                                            <label
                                                                                for="name">{{ __('Current Image') }}</label>
                                                                            <div class="col-lg-12 pb-1">
                                                                                <img class="admin-setting-img"
                                                                                    src="{{ $paystack->photo ? url('/core/public/storage/images/' . $paystack->photo) : url('/core/public/storage/images/placeholder.png') }}"
                                                                                    stripe="No Image Found">
                                                                            </div>
                                                                            <span>{{ __('Image Size Should Be 52 x 35.') }}</span>
                                                                        </div>

                                                                        <div
                                                                            class="form-group position-relative col-xl-12">
                                                                            <label class="file">
                                                                                <input type="file" class="upload-photo"
                                                                                    name="photo" id="file"
                                                                                    aria-label="File browser example">
                                                                                <span
                                                                                    class="file-custom text-left">{{ __('Upload Image...') }}</span>
                                                                            </label>
                                                                        </div>

                                                                        <div class="form-group">
                                                                            <label for="name">{{ __('Enter Name') }}
                                                                                *</label>
                                                                            <input type="text" class="form-control"
                                                                                name="name" id="name"
                                                                                value="{{ $paystack->name }}">
                                                                        </div>

                                                                        @foreach ($paystackData as $pkey => $paystackData)
                                                                            @if ($pkey == 'check_sandbox')
                                                                                <div class="form-group  col-xl-4 col-md-6">
                                                                                    <div
                                                                                        class="custom-control custom-checkbox">
                                                                                        <input type="checkbox"
                                                                                            name="pkey[{{ __($pkey) }}]"
                                                                                            class="custom-control-input"
                                                                                            {{ $paystackData->status == 1 ? 'checked' : '' }}
                                                                                            id="mer{{ $pkey }}">
                                                                                        <label class="custom-control-label"
                                                                                            for="mer{{ $pkey }}">
                                                                                            {{ __($paystack->name . ' ' . ucwords(str_replace('_', ' ', $pkey))) }}
                                                                                        </label>
                                                                                    </div>
                                                                                </div>
                                                                            @else
                                                                                <div class="form-group col-xl-12">
                                                                                    <label
                                                                                        for="inp-{{ __($pkey) }}">{{ __($paystack->name . ' ' . ucwords(str_replace('_', ' ', $pkey))) }}</label>
                                                                                    <input type="text"
                                                                                        class="form-control"
                                                                                        id="inp-{{ __($pkey) }}"
                                                                                        name="pkey[{{ __($pkey) }}]"
                                                                                        placeholder="{{ __($paystack->name . ' ' . ucwords(str_replace('_', ' ', $pkey))) }}"
                                                                                        value="{{ $paystackData }}"
                                                                                        required>
                                                                                </div>
                                                                            @endif
                                                                        @endforeach

                                                                        <div class="form-group">
                                                                            <label for="text">{{ __('Enter Text') }}
                                                                                *</label>
                                                                            <textarea name="text" id="text" class="form-control " rows="5"
                                                                                placeholder="{{ __('Enter Text') }}">{{ $paystack->text }}</textarea>
                                                                        </div>

                                                                        <input type="hidden" name="unique_keyword"
                                                                            value="paystack">

                                                                    </div>

                                                                    <div>

                                                                        <div
                                                                            class="form-group d-flex justify-content-center">
                                                                            <button type="submit"
                                                                                class="btn btn-secondary btn-block w-50">{{ __('Submit') }}</button>
                                                                        </div>

                                                                    </div>

                                                                </form>

                                                            </div>

                                                        </div>

                                                    </div>

                                                    <div id="bank" class="container tab-pane"><br>
                                                        <div class="row justify-content-center">
                                                            <div class="col-lg-8">
                                                                <form action="{{ route('back.setting.payment.update') }}"
                                                                    method="POST" enctype="multipart/form-data">
                                                                    @csrf
                                                                    <input type="hidden" name="unique_keyword"
                                                                        value="bank">

                                                                    <div class="form-group">
                                                                        <label class="switch-primary">
                                                                            <input type="checkbox"
                                                                                class="switch switch-bootstrap "
                                                                                name="status" value="1"
                                                                                {{ $bank->status == 1 ? 'checked' : '' }}>
                                                                            <span class="switch-body"></span>
                                                                            <span
                                                                                class="switch-text">{{ __('Display Bank Transfer') }}</span>
                                                                        </label>
                                                                    </div>
                                                                    <div
                                                                        class="image-show {{ $bank->status == 1 ? '' : 'd-none' }}">
                                                                        <div class="form-group col-xl-12">
                                                                            <label
                                                                                for="name">{{ __('Current Image') }}</label>
                                                                            <div class="col-lg-12 pb-1">
                                                                                <img class="admin-setting-img"
                                                                                    src="{{ $bank->photo ? url('/core/public/storage/images/' . $bank->photo) : url('/core/public/storage/images/placeholder.png') }}"
                                                                                    stripe="No Image Found">
                                                                            </div>
                                                                            <span>{{ __('Image Size Should Be 52 x 35.') }}</span>
                                                                        </div>

                                                                        <div
                                                                            class="form-group position-relative col-xl-12">
                                                                            <label class="file">
                                                                                <input type="file" class="upload-photo"
                                                                                    name="photo" id="file"
                                                                                    aria-label="File browser example">
                                                                                <span
                                                                                    class="file-custom text-left">{{ __('Upload Image...') }}</span>
                                                                            </label>
                                                                        </div>

                                                                        <div class="form-group">
                                                                            <label for="name">{{ __('Enter Name') }}
                                                                                *</label>
                                                                            <input type="text" class="form-control"
                                                                                name="name" id="name"
                                                                                value="{{ $bank->name }}">
                                                                        </div>

                                                                        <div class="form-group col-xl-12">
                                                                            <label for="bank_bank_name">{{ __('Bank Name') }}</label>
                                                                            <input type="text" class="form-control"
                                                                                id="bank_bank_name"
                                                                                name="pkey[bank_name]"
                                                                                placeholder="{{ __('Enter Bank Name (e.g., HBL, Meezan, UBL)') }}"
                                                                                value="{{ $bankData['bank_name'] ?? '' }}">
                                                                        </div>
                                                                        <div class="form-group col-xl-12">
                                                                            <label for="bank_account_name">{{ __('Account Holder Name') }}</label>
                                                                            <input type="text" class="form-control"
                                                                                id="bank_account_name"
                                                                                name="pkey[account_name]"
                                                                                placeholder="{{ __('Enter Account Holder Name') }}"
                                                                                value="{{ $bankData['account_name'] ?? '' }}">
                                                                        </div>
                                                                        <div class="form-group col-xl-12">
                                                                            <label for="bank_account_number">{{ __('Account Number / IBAN') }}</label>
                                                                            <input type="text" class="form-control"
                                                                                id="bank_account_number"
                                                                                name="pkey[account_number]"
                                                                                placeholder="{{ __('Enter Account Number / IBAN') }}"
                                                                                value="{{ $bankData['account_number'] ?? '' }}">
                                                                        </div>

                                                                        <div class="form-group">
                                                                            <label for="text">{{ __('Enter Text') }}
                                                                                *</label>
                                                                            <textarea name="text" id="text" class="form-control text-editor" rows="5"
                                                                                placeholder="{{ __('Enter Text') }}">{{ $bank->text }}</textarea>
                                                                        </div>

                                                                    </div>

                                                                    <div>

                                                                        <div
                                                                            class="form-group d-flex justify-content-center">
                                                                            <button type="submit"
                                                                                class="btn btn-secondary btn-block w-50">{{ __('Submit') }}</button>
                                                                        </div>

                                                                    </div>

                                                                </form>

                                                            </div>

                                                        </div>

                                                    </div>

                                                    <div id="razorpay" class="container tab-pane"><br>
                                                        <div class="row justify-content-center">
                                                            <div class="col-lg-8">
                                                                <form action="{{ route('back.setting.payment.update') }}"
                                                                    method="POST" enctype="multipart/form-data">

                                                                    @csrf

                                                                    <div class="form-group">
                                                                        <label class="switch-primary">
                                                                            <input type="checkbox"
                                                                                class="switch switch-bootstrap "
                                                                                name="status" value="1"
                                                                                {{ $razorpay->status == 1 ? 'checked' : '' }}>
                                                                            <span class="switch-body"></span>
                                                                            <span
                                                                                class="switch-text">{{ __('Display Razorpay') }}</span>
                                                                        </label>
                                                                    </div>

                                                                    <div
                                                                        class="image-show {{ $razorpay->status == 1 ? '' : 'd-none' }}">

                                                                        <div class="form-group col-xl-12">
                                                                            <label
                                                                                for="name">{{ __('Current Image') }}</label>
                                                                            <div class="col-lg-12 pb-1">
                                                                                <img class="admin-setting-img"
                                                                                    src="{{ $razorpay->photo ? url('/core/public/storage/images/' . $razorpay->photo) : url('/core/public/storage/images/placeholder.png') }}"
                                                                                    stripe="No Image Found">
                                                                            </div>
                                                                            <span>{{ __('Image Size Should Be 52 x 35.') }}</span>
                                                                        </div>

                                                                        <div
                                                                            class="form-group position-relative col-xl-12">
                                                                            <label class="file">
                                                                                <input type="file" class="upload-photo"
                                                                                    name="photo" id="file"
                                                                                    aria-label="File browser example">
                                                                                <span
                                                                                    class="file-custom text-left">{{ __('Upload Image...') }}</span>
                                                                            </label>
                                                                        </div>

                                                                        <div class="form-group">
                                                                            <label for="name">{{ __('Enter Name') }}
                                                                                *</label>
                                                                            <input type="text" class="form-control"
                                                                                name="name" id="name"
                                                                                value="{{ $razorpay->name }}">
                                                                        </div>

                                                                        @foreach ($razorpayData as $pkey => $razorpayData)
                                                                            @if ($pkey == 'check_sandbox')
                                                                                <div class="form-group  col-xl-4 col-md-6">
                                                                                    <div
                                                                                        class="custom-control custom-checkbox">
                                                                                        <input type="checkbox"
                                                                                            name="pkey[{{ __($pkey) }}]"
                                                                                            class="custom-control-input"
                                                                                            {{ $razorpayData->status == 1 ? 'checked' : '' }}
                                                                                            id="mer{{ $pkey }}">
                                                                                        <label class="custom-control-label"
                                                                                            for="mer{{ $pkey }}">
                                                                                            {{ __($razorpay->name . ' ' . ucwords(str_replace('_', ' ', $pkey))) }}
                                                                                        </label>
                                                                                    </div>
                                                                                </div>
                                                                            @else
                                                                                <div class="form-group col-xl-12">
                                                                                    <label
                                                                                        for="inp-{{ __($pkey) }}">{{ __($razorpay->name . ' ' . ucwords(str_replace('_', ' ', $pkey))) }}</label>
                                                                                    <input type="text"
                                                                                        class="form-control"
                                                                                        id="inp-{{ __($pkey) }}"
                                                                                        name="pkey[{{ __($pkey) }}]"
                                                                                        placeholder="{{ __($razorpay->name . ' ' . ucwords(str_replace('_', ' ', $pkey))) }}"
                                                                                        value="{{ $razorpayData }}"
                                                                                        required>
                                                                                </div>
                                                                            @endif
                                                                        @endforeach
                                                                        <div class="form-group">
                                                                            <label for="text">{{ __('Enter Text') }}
                                                                                *</label>
                                                                            <textarea name="text" id="text" class="form-control " rows="5"
                                                                                placeholder="{{ __('Enter Text') }}">{{ $razorpay->text }}</textarea>
                                                                        </div>
                                                                        <input type="hidden" name="unique_keyword"
                                                                            value="razorpay">
                                                                    </div>
                                                                    <div>
                                                                        <div
                                                                            class="form-group d-flex justify-content-center">
                                                                            <button type="submit"
                                                                                class="btn btn-secondary btn-block w-50">{{ __('Submit') }}</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>


                                                    <div id="flutterwave" class="container tab-pane"><br>
                                                        <div class="row justify-content-center">
                                                            <div class="col-lg-8">
                                                                <form action="{{ route('back.setting.payment.update') }}"
                                                                    method="POST" enctype="multipart/form-data">

                                                                    @csrf

                                                                    <div class="form-group">
                                                                        <label class="switch-primary">
                                                                            <input type="checkbox"
                                                                                class="switch switch-bootstrap "
                                                                                name="status" value="1"
                                                                                {{ $flutterwave->status == 1 ? 'checked' : '' }}>
                                                                            <span class="switch-body"></span>
                                                                            <span
                                                                                class="switch-text">{{ __('Display Flutterwave') }}</span>
                                                                        </label>
                                                                    </div>

                                                                    <div
                                                                        class="image-show {{ $flutterwave->status == 1 ? '' : 'd-none' }}">

                                                                        <div class="form-group col-xl-12">
                                                                            <label
                                                                                for="name">{{ __('Current Image') }}</label>
                                                                            <div class="col-lg-12 pb-1">
                                                                                <img class="admin-setting-img"
                                                                                    src="{{ $flutterwave->photo ? url('/core/public/storage/images/' . $flutterwave->photo) : url('/core/public/storage/images/placeholder.png') }}"
                                                                                    stripe="No Image Found">
                                                                            </div>
                                                                            <span>{{ __('Image Size Should Be 52 x 35.') }}</span>
                                                                        </div>

                                                                        <div
                                                                            class="form-group position-relative col-xl-12">
                                                                            <label class="file">
                                                                                <input type="file" class="upload-photo"
                                                                                    name="photo" id="file"
                                                                                    aria-label="File browser example">
                                                                                <span
                                                                                    class="file-custom text-left">{{ __('Upload Image...') }}</span>
                                                                            </label>
                                                                        </div>

                                                                        <div class="form-group">
                                                                            <label for="name">{{ __('Enter Name') }}
                                                                                *</label>
                                                                            <input type="text" class="form-control"
                                                                                name="name" id="name"
                                                                                value="{{ $flutterwave->name }}">
                                                                        </div>

                                                                        @foreach ($flutterwaveData as $pkey => $flutterwaveData)
                                                                            @if ($pkey == 'check_sandbox')
                                                                                <div class="form-group  col-xl-4 col-md-6">
                                                                                    <div
                                                                                        class="custom-control custom-checkbox">
                                                                                        <input type="checkbox"
                                                                                            name="pkey[{{ __($pkey) }}]"
                                                                                            class="custom-control-input"
                                                                                            {{ $flutterwaveData->status == 1 ? 'checked' : '' }}
                                                                                            id="mer{{ $pkey }}">
                                                                                        <label class="custom-control-label"
                                                                                            for="mer{{ $pkey }}">
                                                                                            {{ __($flutterwave->name . ' ' . ucwords(str_replace('_', ' ', $pkey))) }}
                                                                                        </label>
                                                                                    </div>
                                                                                </div>
                                                                            @else
                                                                                <div class="form-group col-xl-12">
                                                                                    <label
                                                                                        for="inp-{{ __($pkey) }}">{{ __($flutterwave->name . ' ' . ucwords(str_replace('_', ' ', $pkey))) }}</label>
                                                                                    <input type="text"
                                                                                        class="form-control"
                                                                                        id="inp-{{ __($pkey) }}"
                                                                                        name="pkey[{{ __($pkey) }}]"
                                                                                        placeholder="{{ __($flutterwave->name . ' ' . ucwords(str_replace('_', ' ', $pkey))) }}"
                                                                                        value="{{ $flutterwaveData }}"
                                                                                        required>
                                                                                </div>
                                                                            @endif
                                                                        @endforeach
                                                                        <div class="form-group">
                                                                            <label for="text">{{ __('Enter Text') }}
                                                                                *</label>
                                                                            <textarea name="text" id="text" class="form-control " rows="5"
                                                                                placeholder="{{ __('Enter Text') }}">{{ $flutterwave->text }}</textarea>
                                                                        </div>
                                                                        <input type="hidden" name="unique_keyword"
                                                                            value="flutterwave">
                                                                    </div>
                                                                    <div>
                                                                        <div
                                                                            class="form-group d-flex justify-content-center">
                                                                            <button type="submit"
                                                                                class="btn btn-secondary btn-block w-50">{{ __('Submit') }}</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>



                                                    <div id="paytabs" class="container tab-pane"><br>
                                                        <div class="row justify-content-center">
                                                            <div class="col-lg-8">
                                                                <form action="{{ route('back.setting.payment.update') }}"
                                                                    method="POST" enctype="multipart/form-data">

                                                                    @csrf

                                                                    <div class="form-group">
                                                                        <label class="switch-primary">
                                                                            <input type="checkbox"
                                                                                class="switch switch-bootstrap "
                                                                                name="status" value="1"
                                                                                {{ $paytabs->status == 1 ? 'checked' : '' }}>
                                                                            <span class="switch-body"></span>
                                                                            <span
                                                                                class="switch-text">{{ __('Display Paytabs') }}</span>
                                                                        </label>
                                                                    </div>

                                                                    <div
                                                                        class="image-show {{ $paytabs->status == 1 ? '' : 'd-none' }}">

                                                                        <div class="form-group col-xl-12">
                                                                            <label
                                                                                for="name">{{ __('Current Image') }}</label>
                                                                            <div class="col-lg-12 pb-1">
                                                                                <img class="admin-setting-img"
                                                                                    src="{{ $paytabs->photo ? url('/core/public/storage/images/' . $flutterwave->photo) : url('/core/public/storage/images/placeholder.png') }}"
                                                                                    stripe="No Image Found">
                                                                            </div>
                                                                            <span>{{ __('Image Size Should Be 52 x 35.') }}</span>
                                                                        </div>

                                                                        <div
                                                                            class="form-group position-relative col-xl-12">
                                                                            <label class="file">
                                                                                <input type="file" class="upload-photo"
                                                                                    name="photo" id="file"
                                                                                    aria-label="File browser example">
                                                                                <span
                                                                                    class="file-custom text-left">{{ __('Upload Image...') }}</span>
                                                                            </label>
                                                                        </div>

                                                                        <div class="form-group">
                                                                            <label for="name">{{ __('Enter Name') }}
                                                                                *</label>
                                                                            <input type="text" class="form-control"
                                                                                name="name" id="name"
                                                                                value="{{ $paytabs->name }}">
                                                                        </div>

                                                                        @foreach ($paytabsData as $pakey => $paytabsData)
                                                                            @if ($pakey == 'check_sandbox')
                                                                                <div class="form-group  col-xl-4 col-md-6">
                                                                                    <div
                                                                                        class="custom-control custom-checkbox">
                                                                                        <input type="checkbox"
                                                                                            name="pkey[{{ __($pakey) }}]"
                                                                                            class="custom-control-input"
                                                                                            {{ @$paytabsData == 1 ? 'checked' : '' }}
                                                                                            id="pay{{ $pakey }}">
                                                                                        <label class="custom-control-label"
                                                                                            for="pay{{ $pakey }}">
                                                                                            {{ __($paytabs->name . ' ' . ucwords(str_replace('_', ' ', $pakey))) }}
                                                                                        </label>
                                                                                    </div>
                                                                                </div>
                                                                            @else
                                                                                <div class="form-group col-xl-12">
                                                                                    <label
                                                                                        for="inp-{{ __($pakey) }}">{{ __($paytabs->name . ' ' . ucwords(str_replace('_', ' ', $pakey))) }}</label>
                                                                                    <input type="text"
                                                                                        class="form-control"
                                                                                        id="inp-{{ __($pakey) }}"
                                                                                        name="pkey[{{ __($pakey) }}]"
                                                                                        placeholder="{{ __($paytabs->name . ' ' . ucwords(str_replace('_', ' ', $pakey))) }}"
                                                                                        value="{{ $paytabsData }}"
                                                                                        required>
                                                                                </div>
                                                                            @endif
                                                                        @endforeach
                                                                        <div class="form-group">
                                                                            <label for="text">{{ __('Enter Text') }}
                                                                                *</label>
                                                                            <textarea name="text" id="text" class="form-control " rows="5"
                                                                                placeholder="{{ __('Enter Text') }}">{{ $paytabs->text }}</textarea>
                                                                        </div>
                                                                        <input type="hidden" name="unique_keyword"
                                                                            value="paytabs">
                                                                    </div>
                                                                    <div>
                                                                        <div
                                                                            class="form-group d-flex justify-content-center">
                                                                            <button type="submit"
                                                                                class="btn btn-secondary btn-block w-50">{{ __('Submit') }}</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    @if(isset($custom_payments) && count($custom_payments) > 0)
                                                        @foreach($custom_payments as $cpay)
                                                            @php
                                                                $cpay_info = json_decode($cpay->information, true) ?? [];
                                                            @endphp
                                                            <div id="custom_{{ $cpay->id }}" class="container tab-pane"><br>
                                                                <div class="row justify-content-center">
                                                                    <div class="col-lg-8">
                                                                        <form action="{{ route('back.setting.payment.custom.update', $cpay->id) }}" method="POST" enctype="multipart/form-data">
                                                                            @csrf
                                                                            <div class="form-group d-flex justify-content-between align-items-center">
                                                                                <label class="switch-primary mb-0">
                                                                                    <input type="checkbox" class="switch switch-bootstrap" name="status" value="1" {{ $cpay->status == 1 ? 'checked' : '' }}>
                                                                                    <span class="switch-body"></span>
                                                                                    <span class="switch-text">{{ __('Display') }} {{ $cpay->name }}</span>
                                                                                </label>
                                                                                <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#delete_custom_{{ $cpay->id }}">
                                                                                    <i class="fas fa-trash"></i> {{ __('Delete') }}
                                                                                </button>
                                                                            </div>
                                                                            <div class="image-show {{ $cpay->status == 1 ? '' : 'd-none' }}">
                                                                                <div class="form-group col-xl-12">
                                                                                    <label for="name">{{ __('Current Image') }}</label>
                                                                                    <div class="col-lg-12 pb-1">
                                                                                        <img class="admin-setting-img" src="{{ $cpay->photo ? url('/core/public/storage/images/' . $cpay->photo) : url('/core/public/storage/images/placeholder.png') }}" alt="{{ $cpay->name }}">
                                                                                    </div>
                                                                                    <span>{{ __('Image Size Should Be 52 x 35.') }}</span>
                                                                                </div>
                                                                                <div class="form-group position-relative col-xl-12">
                                                                                    <label class="file">
                                                                                        <input type="file" class="upload-photo" name="photo" id="file_{{ $cpay->id }}" aria-label="File browser example">
                                                                                        <span class="file-custom text-left">{{ __('Upload Image...') }}</span>
                                                                                    </label>
                                                                                </div>
                                                                                <div class="form-group col-xl-12">
                                                                                    <label for="cname_{{ $cpay->id }}">{{ __('Enter Name') }} *</label>
                                                                                    <input type="text" class="form-control" id="cname_{{ $cpay->id }}" name="name" value="{{ $cpay->name }}" required>
                                                                                </div>
                                                                                <div class="form-group col-xl-12">
                                                                                    <label for="bank_name_{{ $cpay->id }}">{{ __('Bank / Provider Name') }}</label>
                                                                                    <input type="text" class="form-control" id="bank_name_{{ $cpay->id }}" name="bank_name" placeholder="{{ __('e.g., SadaPay, NayaPay, UPaisa') }}" value="{{ $cpay_info['bank_name'] ?? '' }}">
                                                                                </div>
                                                                                <div class="form-group col-xl-12">
                                                                                    <label for="account_name_{{ $cpay->id }}">{{ __('Account Holder Name') }}</label>
                                                                                    <input type="text" class="form-control" id="account_name_{{ $cpay->id }}" name="account_name" placeholder="{{ __('Enter Account Holder Name') }}" value="{{ $cpay_info['account_name'] ?? '' }}">
                                                                                </div>
                                                                                <div class="form-group col-xl-12">
                                                                                    <label for="account_number_{{ $cpay->id }}">{{ __('Account Number') }}</label>
                                                                                    <input type="text" class="form-control" id="account_number_{{ $cpay->id }}" name="account_number" placeholder="{{ __('Enter Account Number') }}" value="{{ $cpay_info['account_number'] ?? '' }}">
                                                                                </div>
                                                                                <div class="form-group col-xl-12">
                                                                                    <label for="text_{{ $cpay->id }}">{{ __('Enter Text') }} *</label>
                                                                                    <textarea name="text" id="text_{{ $cpay->id }}" class="form-control" rows="5" placeholder="{{ __('Enter Text / Instructions') }}">{{ $cpay->text }}</textarea>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group d-flex justify-content-center mt-3">
                                                                                <button type="submit" class="btn btn-secondary btn-block w-50">{{ __('Submit') }}</button>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            {{-- Delete Modal --}}
                                                            <div class="modal fade" id="delete_custom_{{ $cpay->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                                                <div class="modal-dialog" role="document">
                                                                    <div class="modal-content">
                                                                        <form action="{{ route('back.setting.payment.custom.delete', $cpay->id) }}" method="POST">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <div class="modal-header">
                                                                                <h5 class="modal-title">{{ __('Delete Payment Method') }}</h5>
                                                                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                                                                    <span aria-hidden="true">&times;</span>
                                                                                </button>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                {{ __('Are you sure you want to delete this payment method?') }} <strong>({{ $cpay->name }})</strong>
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                                                                                <button type="submit" class="btn btn-danger">{{ __('Delete') }}</button>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    @endif

                                                    {{-- ADD PAYMENT METHOD TAB PANE --}}
                                                    <div id="add_payment_tab" class="container tab-pane"><br>
                                                        <div class="row justify-content-center">
                                                            <div class="col-lg-8">
                                                                <div class="card p-4 border shadow-sm">
                                                                    <h4 class="font-weight-bold text-primary mb-3"><i class="fas fa-plus-circle"></i> {{ __('Add New Payment Method') }}</h4>
                                                                    <form action="{{ route('back.setting.payment.custom.store') }}" method="POST" enctype="multipart/form-data">
                                                                        @csrf
                                                                        <div class="form-group">
                                                                            <label class="switch-primary">
                                                                                <input type="checkbox" class="switch switch-bootstrap" name="status" value="1" checked>
                                                                                <span class="switch-body"></span>
                                                                                <span class="switch-text">{{ __('Display This Payment Method') }}</span>
                                                                            </label>
                                                                        </div>

                                                                        <div class="form-group">
                                                                            <label for="new_name">{{ __('Payment Option Name *') }}</label>
                                                                            <input type="text" class="form-control" name="name" id="new_name" placeholder="{{ __('e.g. SadaPay, UPaisa, NayaPay') }}" required>
                                                                        </div>

                                                                        <div class="form-group">
                                                                            <label>{{ __('Payment Method Logo / Image') }}</label>
                                                                            <div class="position-relative">
                                                                                <input type="file" class="form-control-file" name="photo" id="new_photo" accept="image/*">
                                                                            </div>
                                                                            <small class="text-muted">{{ __('Image Size Should Be 52 x 35.') }}</small>
                                                                        </div>

                                                                        <div class="form-group">
                                                                            <label for="new_bank_name">{{ __('Bank / Provider Name (Optional)') }}</label>
                                                                            <input type="text" class="form-control" id="new_bank_name" name="bank_name" placeholder="{{ __('e.g., SadaPay MFB, Microfinance, Bank Name') }}">
                                                                        </div>

                                                                        <div class="form-group">
                                                                            <label for="new_account_name">{{ __('Account Holder Name *') }}</label>
                                                                            <input type="text" class="form-control" id="new_account_name" name="account_name" placeholder="{{ __('Enter Account Holder Name') }}" required>
                                                                        </div>

                                                                        <div class="form-group">
                                                                            <label for="new_account_number">{{ __('Account Number *') }}</label>
                                                                            <input type="text" class="form-control" id="new_account_number" name="account_number" placeholder="{{ __('Enter Account Number / Mobile Account') }}" required>
                                                                        </div>

                                                                        <div class="form-group">
                                                                            <label for="new_text">{{ __('Enter Text / Instructions *') }}</label>
                                                                            <textarea name="text" id="new_text" class="form-control" rows="4" placeholder="{{ __('Enter payment instructions for the user...') }}"></textarea>
                                                                        </div>

                                                                        <div class="form-group d-flex justify-content-center mt-4">
                                                                            <button type="submit" class="btn btn-success btn-block w-50 font-weight-bold">{{ __('Save Payment Method') }}</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>



                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    @endsection
