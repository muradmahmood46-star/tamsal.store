@extends('master.front')
@section('title')
    {{__('Dashboard')}}
@endsection
@section('content')

<!-- Page Title-->
<div class="page-title d-none d-lg-block">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <ul class="breadcrumbs">
                    <li><a href="{{ route('front.index')}}">{{__('Home')}}</a> </li>
                    <li class="separator"></li>
                    <li>{{__('Welcome Back')}}, {{$user->first_name}}</li>
                  </ul>
            </div>
        </div>
    </div>
  </div>
  <!-- Page Content-->
  <div class="container padding-bottom-3x mb-1 mt-3 mt-lg-0">
  <div class="row">
         @include('includes.user_sitebar')
          <div class="col-12 col-lg-8">
            <!-- Mobile Header with Back Button -->
            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                <div class="d-flex align-items-center">
                    <a href="{{ route('user.dashboard') }}" class="btn btn-sm btn-light border mr-2 shadow-sm d-lg-none" style="border-radius: 8px; padding: 6px 12px; margin-right: 10px;" title="{{ __('Back to Dashboard') }}">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h5 class="mb-0 font-weight-bold text-dark d-flex align-items-center">
                        <i class="icon-user mr-2 text-primary" style="margin-right: 8px;"></i>{{ __('Edit Profile') }}
                    </h5>
                </div>
            </div>
            <div class="card shadow-sm border-0" style="border-radius: 12px;">
                <div class="card-body">
                        <form  class="row" action="{{route('user.profile.update')}}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <!-- Profile Picture Upload Section -->
                            <div class="col-12 mb-3">
                                <div class="p-3 bg-light rounded-lg border d-flex flex-column flex-sm-row align-items-center" style="border-radius: 12px; gap: 18px;">
                                    <div class="position-relative" style="width: 100px; height: 100px; flex-shrink: 0;">
                                        <img id="profile_avatar_preview" 
                                             src="{{ $user->photoUrl() }}" 
                                             alt="{{ $user->first_name }}" 
                                             style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%; border: 3px solid #008069; box-shadow: 0 4px 10px rgba(0,0,0,0.12);">
                                        
                                        <!-- Camera badge on avatar -->
                                        <label for="avater" 
                                               title="{{ __('Upload via Camera or Gallery') }}" 
                                               style="position: absolute; bottom: 0; right: 0; width: 34px; height: 34px; background: #008069; color: #ffffff; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; border: 2px solid #ffffff; box-shadow: 0 2px 6px rgba(0,0,0,0.25); margin: 0; transition: transform 0.15s ease;"
                                               onmouseover="this.style.transform='scale(1.1)'"
                                               onmouseout="this.style.transform='scale(1)'">
                                            <i class="fas fa-camera" style="font-size: 14px;"></i>
                                        </label>

                                        <!-- Uploading Spinner -->
                                        <div id="avatar_spinner" style="display: none; position: absolute; top: 0; left: 0; width: 100px; height: 100px; border-radius: 50%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; color: #ffffff;">
                                            <div class="spinner-border spinner-border-sm text-light" role="status"></div>
                                        </div>
                                    </div>

                                    <div class="text-center text-sm-left flex-grow-1">
                                        <label for="avater" class="form-label font-weight-bold text-dark mb-1 d-block" style="font-size: 15px; cursor: pointer;">
                                            <i class="fas fa-user-circle mr-1 text-success"></i> {{ __('Profile Picture') }}
                                        </label>
                                        <p class="text-muted small mb-2" style="font-size: 12px;">
                                            {{ __('Choose from Camera or Photo Gallery. The new picture will immediately appear on your dashboard!') }}
                                        </p>
                                        <div class="d-flex flex-wrap justify-content-center justify-content-sm-start align-items-center" style="gap: 8px;">
                                            <label for="avater" class="btn btn-sm btn-primary px-3 shadow-sm mb-0" style="border-radius: 20px; font-size: 12.5px; cursor: pointer;">
                                                <i class="fas fa-camera mr-1"></i> {{ __('Take Photo / Choose Gallery') }}
                                            </label>
                                            <span id="avatar_success_badge" class="badge badge-success py-2 px-2" style="display: none; font-size: 11px;">
                                                <i class="fas fa-check-circle mr-1"></i> {{ __('Updated Successfully!') }}
                                            </span>
                                        </div>
                                        <input class="d-none" type="file" name="photo" id="avater" accept="image/*" onchange="handleProfilePhotoSelect(this)">
                                        @error('photo')
                                            <p class="text-danger small mt-1 mb-0">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                <label for="account-fn">{{__('First Name')}}</label>
                                <input class="form-control" name="first_name" type="text" id="account-fn" value="{{$user->first_name}}">
                                @error('first_name')
                                <p class="text-danger">{{$message}}</p>
                                @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                <label for="account-ln">{{__('Last Name')}}</label>
                                <input class="form-control" type="text" name="last_name" id="account-ln" value="{{$user->last_name}}">
                                @error('last_name')
                                <p class="text-danger">{{$message}}</p>
                                @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                <label for="account-email">{{__('E-mail Address')}}</label>
                                <input class="form-control" name="email" type="email" id="account-email" value="{{$user->email}}" >
                                @error('email')
                                <p class="text-danger">{{$message}}</p>
                                @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                <label for="account-phone">{{__('Phone Number')}}</label>
                                <input class="form-control" name="phone" type="text" id="account-phone" value="{{$user->phone}}">
                                @error('phone')
                                    <p class="text-danger">{{$message}}</p>
                                @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                <label for="account-pass">{{__('New Password')}}</label>
                                <input class="form-control" name="password"  type="text" id="account-pass" placeholder="{{__('Change your password')}}">
                                @error('password')
                                <p class="text-danger">{{$message}}</p>
                                @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <hr class="mt-2 mb-3">
                                <div class="d-flex flex-wrap justify-content-between align-items-center">
                                <div class="custom-control custom-checkbox d-block">
                                    <input class="custom-control-input" name="newsletter" type="checkbox" id="subscribe_me" {{$check_newsletter ? 'checked' : ''}}>
                                    <label class="custom-control-label" for="subscribe_me">{{__('Subscribe')}}</label>
                                </div>
                                <button class="btn btn-primary margin-right-none" type="submit"><span>{{__('Update Profile')}}</span></button>
                                </div>
                            </div>
                        </form>
                </div>
            </div>
          </div>
        </div>
  </div>

<script>
function handleProfilePhotoSelect(input) {
    if (!input.files || !input.files[0]) return;
    const file = input.files[0];

    // 1. Instant local preview in circle
    const reader = new FileReader();
    reader.onload = function(e) {
        const previewImg = document.getElementById('profile_avatar_preview');
        if (previewImg) previewImg.src = e.target.result;
        const sidebarImg = document.getElementById('avater_photo_view');
        if (sidebarImg) sidebarImg.src = e.target.result;
    };
    reader.readAsDataURL(file);

    // 2. Instant background upload so it immediately saves
    const spinner = document.getElementById('avatar_spinner');
    const badge = document.getElementById('avatar_success_badge');
    if (spinner) spinner.style.display = 'flex';
    if (badge) badge.style.display = 'none';

    const formData = new FormData();
    formData.append('photo', file);
    formData.append('_token', '{{ csrf_token() }}');

    fetch("{{ route('user.profile.photo.update') }}", {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (spinner) spinner.style.display = 'none';
        if (data.success) {
            if (badge) {
                badge.style.display = 'inline-block';
                setTimeout(() => { badge.style.display = 'none'; }, 4000);
            }
            if (data.photo_url) {
                const previewImg = document.getElementById('profile_avatar_preview');
                if (previewImg) previewImg.src = data.photo_url;
                const sidebarImg = document.getElementById('avater_photo_view');
                if (sidebarImg) sidebarImg.src = data.photo_url;
            }
        } else {
            alert(data.message || 'Error uploading profile picture');
        }
    })
    .catch(err => {
        if (spinner) spinner.style.display = 'none';
        console.error('Photo upload error:', err);
    });
}
</script>
@endsection
