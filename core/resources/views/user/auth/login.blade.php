@extends('master.front')
@section('title')
    {{__('Login')}}
@endsection
@section('no_footer', true)

@section('content')
<div class="login-page-wrapper">
    <!-- Page Title-->
    <div class="page-title login-page-title">
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
        <div class="row justify-content-center w-100 m-0">
            <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5 p-0">
                <form class="card auth-form-card shadow-sm" method="post" action="{{route('user.login.submit')}}">
                    @csrf
                    <div class="card-body">
                        <h4 class="margin-bottom-1x text-center">{{__('Login')}}</h4>

                        <div class="form-group input-group">
                            <input class="form-control" type="email" name="login_email" placeholder="{{ __('Email') }}" value="{{old('login_email')}}"><span class="input-group-addon"><i class="icon-mail"></i></span>
                        </div>
                        @error('login_email')
                            <p class="text-danger">{{$message}}</p>
                        @enderror

                        <div class="form-group input-group">
                            <input class="form-control" type="password" name="login_password" placeholder="{{ __('Password') }}"><span class="input-group-addon"><i class="icon-lock"></i></span>
                        </div>
                        @error('login_password')
                            <p class="text-danger">{{$message}}</p>
                        @enderror

                        <div class="d-flex flex-wrap justify-content-between padding-bottom-1x">
                            <div class="custom-control custom-checkbox">
                            </div>
                            <a class="navi-link" href="{{route('user.forgot')}}">{{__('Forgot password?')}}</a>
                        </div>
                        <div class="col-lg-12 mb-4 p-0">
                            <p class="mb-0">{{ __('Not registered ?') }}  <a class="text-base-color font-weight-bold" href="{{ route("user.register") }}">{{ __('Register now') }}</a></p>
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
    .login-page-wrapper {
        min-height: calc(100vh - 170px);
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
    }
    .login-page-title {
        margin-bottom: 20px !important;
        padding: 8px 0 !important;
    }
    .login-page-container {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding-top: 5px;
        padding-bottom: 30px;
    }
    @media (max-width: 991.98px) {
        .login-page-wrapper {
            min-height: calc(100vh - 110px - env(safe-area-inset-bottom, 0px));
        }
        .login-page-title {
            margin-bottom: 12px !important;
            padding: 4px 0 !important;
        }
        .login-page-container {
            align-items: flex-start;
            padding-top: 8px;
            padding-bottom: 20px;
        }
    }
</style>
@endsection
