@extends('master.front')
@section('title')
    {{__('Dashboard')}}
@endsection
@section('content')

<!-- Page Title-->
<div class="page-title">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <ul class="breadcrumbs">
                    <li><a href="{{route('front.index')}}">{{__('Home')}}</a> </li>
                    <li class="separator"></li>
                    <li>{{__('Welcome Back')}} </li>
                  </ul>
            </div>
        </div>
    </div>
  </div>

  <!-- Page Content-->
  <div class="container  padding-bottom-3x mb-1">
  <div class="row">
         @include('includes.user_sitebar')
          <div class="col-lg-8">
            <div class="padding-top-2x mt-2 hidden-lg-up"></div>
            <!-- Welcome Header Card with Circular Avatar -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px; background: linear-gradient(135deg, #008069 0%, #0d9488 100%); color: #ffffff;">
                <div class="card-body p-3 p-sm-4 d-flex align-items-center justify-content-between flex-wrap" style="gap: 15px;">
                    <div class="d-flex align-items-center" style="gap: 16px;">
                        <a href="{{ route('user.profile') }}" title="{{ __('Change Profile Picture') }}" class="position-relative d-inline-block">
                            <img src="{{ Auth::user()->photoUrl() }}" alt="{{ Auth::user()->first_name }}" style="width: 64px; height: 64px; object-fit: cover; border-radius: 50%; border: 3px solid rgba(255,255,255,0.9); box-shadow: 0 4px 10px rgba(0,0,0,0.2);">
                            <span style="position: absolute; bottom: 0; right: 0; width: 22px; height: 22px; background: #ffffff; color: #008069; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.3);">
                                <i class="fas fa-camera"></i>
                            </span>
                        </a>
                        <div>
                            <h5 class="mb-1 text-white font-weight-bold" style="font-size: 18px;">
                                {{ __('Hello') }}, {{ Auth::user()->first_name }}! 👋
                            </h5>
                            <p class="mb-0 text-white-50 small" style="font-size: 12px;">
                                {{ __('Welcome to your personal dashboard') }}
                            </p>
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('user.profile') }}" class="btn btn-sm btn-light font-weight-bold shadow-sm" style="border-radius: 20px; font-size: 12px; color: #008069; padding: 6px 14px;">
                            <i class="icon-user mr-1"></i> {{ __('Edit Profile') }}
                        </a>
                    </div>
                </div>
            </div>
            <div class="row u-d-d">
                    <div class="col-md-6 mb-4">
                        <a href="{{ route('user.order.index') }}" style="text-decoration: none !important; color: inherit; display: block;">
                            <div class="card round shadow-sm" style="transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                                <div class="card-body text-center">
                                    <i class="icon-shopping-bag"></i>
                                    <p class="mt-3">{{__('All Order')}}</p>
                                    <h4><b>{{$allorders}}</b></h4>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6 mb-4">
                        <a href="{{ route('user.order.index') }}" style="text-decoration: none !important; color: inherit; display: block;">
                            <div class="card round shadow-sm" style="transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                                <div class="card-body text-center">
                                    <i class="icon-shopping-bag"></i>
                                    <p class="mt-3">{{__('Completed Order')}}</p>
                                    <h4><b>{{$delivered}}</b></h4>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6 mb-4">
                        <a href="{{ route('user.order.index') }}" style="text-decoration: none !important; color: inherit; display: block;">
                            <div class="card round shadow-sm" style="transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                                <div class="card-body text-center">
                                    <i class="icon-shopping-bag"></i>
                                    <p class="mt-3">{{__('Processing Order')}}</p>
                                    <h4><b>{{$progress}}</b></h4>
                                </div>
                            </div>
                        </a>
                    </div>


                    <div class="col-md-6 mb-4">
                        <a href="{{ route('user.order.index') }}" style="text-decoration: none !important; color: inherit; display: block;">
                            <div class="card round shadow-sm" style="transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                                <div class="card-body text-center">
                                    <i class="icon-shopping-bag"></i>
                                    <p class="mt-3">{{__('Canceled Order')}}</p>
                                    <h4><b>{{$canceled}}</b></h4>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6 mb-4">
                        <a href="{{ route('user.order.index') }}" style="text-decoration: none !important; color: inherit; display: block;">
                            <div class="card round shadow-sm" style="transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                                <div class="card-body text-center">
                                    <i class="icon-shopping-bag"></i>
                                    <p class="mt-3">{{__('Pending Order')}}</p>
                                    <h4><b>{{$pending}}</b></h4>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6 mb-4">
                        <a href="{{ route('user.message.index') }}" style="text-decoration: none !important;">
                            <div class="card round shadow-sm" style="border: 2px solid #008069 !important; background: #f0fdf4; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                                <div class="card-body text-center">
                                    <i class="icon-message-square" style="color: #008069 !important; font-size: 26px;"></i>
                                    <p class="mt-3 font-weight-bold text-dark">{{__('My Chats & Messages')}}</p>
                                    <h4 style="color: #008069 !important;">
                                        <b>{{ $totalChats ?? 0 }}</b>
                                        @if(($unreadChats ?? 0) > 0)
                                            <span class="badge badge-danger ml-1" style="font-size: 11px; vertical-align: middle;">{{ $unreadChats }} {{ __('New') }}</span>
                                        @endif
                                    </h4>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
          </div>
        </div>
  </div>
@endsection
