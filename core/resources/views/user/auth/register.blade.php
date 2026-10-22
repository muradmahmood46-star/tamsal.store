@extends('master.front')
@section('title')
    {{__('Register')}}
@endsection
@section('no_footer', true)
@section('no_topbar', true)

@section('content')
<div class="register-page-wrapper">
    <!-- Page Title (Hidden for clean, centered, locked register screen) -->
    <div class="page-title register-page-title d-none">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <ul class="breadcrumbs">
                        <li><a href="{{route('front.index')}}">{{__('Home')}}</a></li>
                        <li class="separator"></li>
                        <li>{{__('Register')}}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Page Content-->
    <div class="container register-page-container">
        <div class="row justify-content-center align-items-center w-100 m-0">
            <div class="col-12 p-0">
                <div class="card register-area auth-form-card shadow-sm">
                    <div class="card-body">
                        <div class="text-center mb-2">
                            <a href="{{ route('front.index') }}" title="{{ $setting->title }}">
                                <img src="{{ url('/core/public/storage/images/' . $setting->logo) }}" alt="{{ $setting->title }}" style="max-height: 36px; width: auto; object-fit: contain;">
                            </a>
                        </div>
                        <h4 class="margin-bottom-1x text-center font-weight-bold" style="color: #1e293b; font-size: 20px;">{{__('Register')}}</h4>

                        <form class="row g-2" action="{{route('user.register.submit')}}" method="POST">
                            @csrf
                            <div class="col-6 mb-2">
                                <div class="form-group mb-0">
                                    <label class="form-label text-muted small mb-1" for="reg-fn">{{__('First Name')}}*</label>
                                    <input class="form-control" type="text" name="first_name" placeholder="{{__('First Name')}}" id="reg-fn" value="{{old('first_name')}}">
                                    @error('first_name')
                                        <p class="text-danger small mb-0">{{$message}}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6 mb-2">
                                <div class="form-group mb-0">
                                    <label class="form-label text-muted small mb-1" for="reg-ln">{{__('Last Name')}}*</label>
                                    <input class="form-control" type="text" name="last_name" placeholder="{{__('Last Name')}}" id="reg-ln" value="{{old('last_name')}}">
                                    @error('last_name')
                                        <p class="text-danger small mb-0">{{$message}}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6 mb-2">
                                <div class="form-group mb-0">
                                    <label class="form-label text-muted small mb-1" for="reg-email">{{__('E-mail')}}*</label>
                                    <input class="form-control" type="email" name="email" placeholder="{{__('E-mail')}}" id="reg-email" value="{{old('email')}}">
                                    @error('email')
                                        <p class="text-danger small mb-0">{{$message}}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6 mb-2">
                                <div class="form-group mb-0">
                                    <label class="form-label text-muted small mb-1" for="reg-phone">{{__('Phone')}}*</label>
                                    <input class="form-control" name="phone" type="text" placeholder="{{__('Phone')}}" id="reg-phone" value="{{old('phone')}}">
                                    @error('phone')
                                        <p class="text-danger small mb-0">{{$message}}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6 mb-2">
                                <div class="form-group mb-0">
                                    <label class="form-label text-muted small mb-1" for="reg-pass">{{__('Password')}}*</label>
                                    <input class="form-control" type="password" name="password" placeholder="{{__('Password')}}" id="reg-pass">
                                    @error('password')
                                        <p class="text-danger small mb-0">{{$message}}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6 mb-2">
                                <div class="form-group mb-0">
                                    <label class="form-label text-muted small mb-1" for="reg-pass-confirm">{{__('Confirm')}}*</label>
                                    <input class="form-control" type="password" name="password_confirmation" placeholder="{{__('Confirm Password')}}" id="reg-pass-confirm">
                                </div>
                            </div>

                            <input type="text" name="honeypot" id="honeypot" value="" style="display:none;">

                            @if ($setting->recaptcha == 1)
                            <div class="col-12 mb-2">
                                {!! NoCaptcha::renderJs() !!}
                                {!! NoCaptcha::display() !!}
                                @if ($errors->has('g-recaptcha-response'))
                                    @php
                                        $errmsg = $errors->first('g-recaptcha-response');
                                    @endphp
                                    <p class="text-danger small mb-0">{{__("$errmsg")}}</p>
                                @endif
                            </div>
                            @endif

                            <div class="col-12 mb-3 text-center">
                                <p class="mb-0" style="font-size: 13px;">{{ __("Already have an account ?") }} <a class="text-base-color font-weight-bold" href="{{ route("user.login") }}">{{ __('Login now') }}</a></p>
                            </div>

                            <div class="col-12 text-center">
                                <button class="btn btn-primary w-100 margin-bottom-none" type="submit"><span>{{__('Register')}}</span></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* 1. Hide search box topbar and header on register page */
    .site-header,
    .site-header .topbar,
    .topbar,
    .site-footer {
        display: none !important;
    }
    .scroll-to-top-btn,
    .register-page-title {
        display: none !important;
    }

    .register-page-container {
        width: 100%;
        max-width: 550px;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 0 15px !important;
        margin: 0 auto !important;
    }
    .auth-form-card {
        border-radius: 14px !important;
        border: 1px solid #eef2f6 !important;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.07) !important;
        background: #ffffff !important;
        width: 100%;
    }
    .auth-form-card .card-body {
        padding: 20px 22px !important;
    }
    .auth-form-card input,
    .auth-form-card button,
    .auth-form-card a {
        touch-action: manipulation !important;
    }
    .auth-form-card .form-control {
        height: 38px !important;
        font-size: 13px !important;
        padding: 6px 10px !important;
    }
    .auth-form-card label {
        font-size: 11.5px !important;
        font-weight: 600 !important;
        color: #475569 !important;
        margin-bottom: 3px !important;
    }

    /* Mobile specific: locked & centered screen */
    @media (max-width: 991.98px) {
        html, body {
            overflow: hidden !important;
            height: 100% !important;
            max-height: 100vh !important;
            max-height: 100dvh !important;
            overscroll-behavior: none !important;
            margin: 0 !important;
        }
        body {
            padding-top: 0 !important;
            padding-bottom: calc(56px + env(safe-area-inset-bottom, 0px)) !important;
        }
        .register-page-wrapper {
            width: 100%;
            height: calc(100vh - 56px - env(safe-area-inset-bottom, 0px));
            height: calc(100dvh - 56px - env(safe-area-inset-bottom, 0px));
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 8px 16px !important;
            box-sizing: border-box !important;
            overflow: hidden !important;
            touch-action: none !important;
        }
        .auth-form-card {
            max-height: calc(100vh - 65px - env(safe-area-inset-bottom, 0px));
            max-height: calc(100dvh - 65px - env(safe-area-inset-bottom, 0px));
            overflow-y: auto !important;
            -webkit-overflow-scrolling: touch;
        }
        .auth-form-card::-webkit-scrollbar {
            display: none;
        }
    }

    /* Laptop / PC: Responsive, Spacious, Centered Card */
    @media (min-width: 992px) {
        html, body {
            height: auto !important;
            min-height: 100vh !important;
            overflow-y: auto !important;
            margin: 0 !important;
        }
        body {
            padding-top: 0 !important;
            padding-bottom: 0 !important;
            background-color: #f8fafc !important;
        }
        .register-page-wrapper {
            width: 100%;
            min-height: 100vh;
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 40px 20px !important;
            box-sizing: border-box !important;
        }
        .register-page-container {
            max-width: 550px !important;
            width: 100% !important;
        }
        .auth-form-card {
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08) !important;
            max-height: none !important;
            overflow-y: visible !important;
        }
        .auth-form-card .card-body {
            padding: 28px 28px !important;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if ('ontouchstart' in window || navigator.maxTouchPoints > 0) {
            document.addEventListener('touchmove', function(e) {
                if (!e.target.closest('input, textarea, select, button, a, .card-body')) {
                    e.preventDefault();
                }
            }, { passive: false });
        }
    });
</script>
@endsection
