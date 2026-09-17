@extends('master.seller')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between">
                <div>
                    <h3 class="mb-0 text-dark"><b><i class="fas fa-store-alt text-primary mr-2"></i> {{ __('Store Settings & Profile') }}</b></h3>
                    <p class="text-muted small mb-0">{{ __('Update your shop branding, contact information, location, and description.') }}</p>
                </div>
                <a href="{{ route('seller.dashboard') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-home mr-1"></i> {{ __('Dashboard') }}
                </a>
            </div>
        </div>
    </div>

    @include('alerts.alerts')

    <form action="{{ route('seller.profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <!-- Left Store Details -->
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white py-3">
                        <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-info-circle mr-1"></i> {{ __('Store Details') }}</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="form-group mb-3">
                            <label for="shop_name" class="font-weight-bold">{{ __('Store Name / Shop Name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="shop_name" id="shop_name" class="form-control" value="{{ old('shop_name', $seller->shop_name) }}" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label for="shop_phone" class="font-weight-bold">{{ __('Store Contact Phone') }} <span class="text-danger">*</span></label>
                                <input type="text" name="shop_phone" id="shop_phone" class="form-control" value="{{ old('shop_phone', $seller->shop_phone) }}" required>
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label for="shop_email" class="font-weight-bold">{{ __('Store Email') }} <span class="text-danger">*</span></label>
                                <input type="email" name="shop_email" id="shop_email" class="form-control" value="{{ old('shop_email', $seller->shop_email) }}" required>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="shop_address" class="font-weight-bold">{{ __('Store Address / Location') }} <span class="text-danger">*</span></label>
                            <textarea name="shop_address" id="shop_address" rows="3" class="form-control" required>{{ old('shop_address', $seller->shop_address) }}</textarea>
                        </div>

                        <div class="form-group mb-0">
                            <label for="shop_details" class="font-weight-bold">{{ __('Store Bio / About Store') }}</label>
                            <textarea name="shop_details" id="shop_details" rows="5" class="form-control" placeholder="{{ __('Tell customers about your brand, warranty policies, mission, etc.') }}">{{ old('shop_details', $seller->shop_details) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Branding (Logo & Banner) -->
            <div class="col-lg-4">
                <!-- Shop Logo -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light py-2">
                        <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-image mr-1"></i> {{ __('Store Logo') }}</h6>
                    </div>
                    <div class="card-body text-center p-3">
                        <div class="mb-3">
                            <img src="{{ $user->photoUrl() }}" id="logo_preview" class="img-fluid rounded-circle border shadow-sm p-1" style="width: 110px; height: 110px; object-fit: cover;">
                        </div>
                        <div class="custom-file text-left">
                            <input type="file" name="shop_logo" id="shop_logo" class="custom-file-input" accept="image/*" onchange="previewImg(this, 'logo_preview')">
                            <label class="custom-file-label" for="shop_logo">{{ __('Upload Logo') }}</label>
                        </div>
                        <small class="text-muted d-block mt-1">{{ __('Square format recommended (200x200)') }}</small>
                    </div>
                </div>

                <!-- Shop Banner -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light py-2">
                        <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-panorama mr-1"></i> {{ __('Store Banner') }}</h6>
                    </div>
                    <div class="card-body text-center p-3">
                        <div class="mb-3">
                            <img src="{{ $seller->shop_banner ? asset('storage/images/stores/' . $seller->shop_banner) : asset('storage/images/placeholder.png') }}" id="banner_preview" class="img-fluid rounded border p-1" style="max-height: 120px; width: 100%; object-fit: cover;">
                        </div>
                        <div class="custom-file text-left">
                            <input type="file" name="shop_banner" id="shop_banner" class="custom-file-input" accept="image/*" onchange="previewImg(this, 'banner_preview')">
                            <label class="custom-file-label" for="shop_banner">{{ __('Upload Banner') }}</label>
                        </div>
                        <small class="text-muted d-block mt-1">{{ __('Wide format (1200x400)') }}</small>
                    </div>
                </div>

                <!-- Save Button Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary btn-block py-2 font-weight-bold">
                            <i class="fas fa-save mr-1"></i> {{ __('Save Store Settings') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    function previewImg(input, previewId) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const el = document.getElementById(previewId);
                el.src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
