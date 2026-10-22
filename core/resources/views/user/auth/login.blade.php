@extends('master.front')
@section('title')
    {{__('Login')}}
@endsection
@section('no_footer', true)
@section('no_topbar', true)

@section('content')
<div class="login-page-wrapper">
    <!-- Page Title (Hidden for clean, centered, locked login screen) -->
    <div class="page-title login-page-title d-none">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <ul class="breadcrumbs">
                        <li><a href="{{route('front.index')}}">{{__('Home')}}</a></li>
                        <li class="separator"></li>
                        <li>{{__('Login')}}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Page Content-->
    <div class="container login-page-container">
        <div class="row justify-content-center align-items-center w-100 m-0">
            <div class="col-12 p-0">
                <form class="card auth-form-card shadow-sm" method="post" action="{{route('user.login.submit')}}">
                    @csrf
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <a href="{{ route('front.index') }}" title="{{ $setting->title }}">
                                <img src="{{ url('/core/public/storage/images/' . $setting->logo) }}" alt="{{ $setting->title }}" style="max-height: 38px; width: auto; object-fit: contain;">
                            </a>
                        </div>
                        <h4 class="margin-bottom-1x text-center font-weight-bold" style="color: #1e293b; font-size: 20px;">{{__('Login')}}</h4>

                        <div class="form-group input-group">
                            <input class="form-control" type="email" name="login_email" placeholder="{{ __('Email') }}" value="{{old('login_email')}}"><span class="input-group-addon"><i class="icon-mail"></i></span>
                        </div>
                        @error('login_email')
                            <p class="text-danger small mb-1">{{$message}}</p>
                        @enderror

                        <div class="form-group input-group">
                            <input class="form-control" type="password" name="login_password" placeholder="{{ __('Password') }}"><span class="input-group-addon"><i class="icon-lock"></i></span>
                        </div>
                        @error('login_password')
                            <p class="text-danger small mb-1">{{$message}}</p>
                        @enderror

                        <div class="d-flex flex-wrap justify-content-between padding-bottom-1x">
                            <div class="custom-control custom-checkbox">
                            </div>
                            <a class="navi-link" href="{{route('user.forgot')}}">{{__('Forgot password?')}}</a>
                        </div>
                        <div class="col-lg-12 mb-3 p-0">
                            <p class="mb-0" style="font-size: 13.5px;">{{ __('Not registered ?') }}  <a class="text-base-color font-weight-bold" href="{{ route("user.register") }}">{{ __('Register now') }}</a></p>
                        </div>
                        <div class="text-center">
                            <button class="btn btn-primary margin-bottom-none w-100" type="submit"><span>{{ __('Login') }}</span></button>
                        </div>
                        <div class="row">
                            <div class="col-lg-12 text-center mt-3">
                                @if($setting->facebook_check == 1)
                                <a class="facebook-btn mr-2" href="{{route('social.provider','facebook')}}">{{ __('Facebook login') }}</a>
                                @endif
                                @if($setting->google_check == 1)
                                <a class="google-btn" href="{{route('social.provider','google')}}">{{ __('Google login') }}</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    /* 1. Hide search box topbar and header on login page */
    .site-header,
    .site-header .topbar,
    .topbar {
        display: none !important;
    }
    .scroll-to-top-btn,
    .login-page-title {
        display: none !important;
    }

    .login-page-container {
        width: 100%;
        max-width: 440px;
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
        padding: 24px 22px !important;
    }
    .auth-form-card input,
    .auth-form-card button,
    .auth-form-card a {
        touch-action: manipulation !important;
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
        .login-page-wrapper {
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
        .login-page-wrapper {
            width: 100%;
            min-height: 100vh;
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 40px 20px !important;
            box-sizing: border-box !important;
        }
        .login-page-container {
            max-width: 440px !important;
            width: 100% !important;
        }
        .auth-form-card {
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08) !important;
        }
        .auth-form-card .card-body {
            padding: 32px 30px !important;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if ('ontouchstart' in window || navigator.maxTouchPoints > 0) {
            document.addEventListener('touchmove', function(e) {
                if (!e.target.closest('input, textarea, select, button, a')) {
                    e.preventDefault();
                }
            }, { passive: false });
        }
    });
</script>
@endsection
