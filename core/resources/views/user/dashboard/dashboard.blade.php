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
                <div class="row u-d-d">
                    <div class="col-md-6 mb-4">
                        <div class="card round">
                            <div class="card-body text-center">
                                <i class="icon-shopping-bag"></i>
                                <p class="mt-3">{{__('All Order')}}</p>
                                <h4><b>{{$allorders}}</b></h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card round">
                            <div class="card-body text-center">
                                <i class="icon-shopping-bag"></i>
                                <p class="mt-3">{{__('Completed Order')}}</p>
                                <h4><b>{{$delivered}}</b></h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card round">
                            <div class="card-body text-center">
                                <i class="icon-shopping-bag"></i>
                                <p class="mt-3">{{__('Processing Order')}}</p>
                                <h4><b>{{$progress}}</b></h4>
                            </div>
                        </div>
                    </div>


                    <div class="col-md-6 mb-4">
                        <div class="card round">
                            <div class="card-body text-center">
                                <i class="icon-shopping-bag"></i>
                                <p class="mt-3">{{__('Canceled Order')}}</p>
                                <h4><b>{{$canceled}}</b></h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card round">
                            <div class="card-body text-center">
                                <i class="icon-shopping-bag"></i>
                                <p class="mt-3">{{__('Pending Order')}}</p>
                                <h4><b>{{$pending}}</b></h4>
                            </div>
                        </div>
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
