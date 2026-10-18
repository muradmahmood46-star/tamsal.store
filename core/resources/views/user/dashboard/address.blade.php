@extends('master.front')
@section('title')
    {{ __('Address Book') }}
@endsection
@section('content')

    <!-- Page Title-->
    <div class="page-title">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <ul class="breadcrumbs">
                        <li><a href="{{ route('front.index') }}">{{ __('Home') }}</a> </li>
                        <li class="separator"></li>
                        <li>{{ __('My Addresses') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Page Content-->
    <div class="container padding-bottom-3x mb-1">
        <div class="row">
            @include('includes.user_sitebar')
            <div class="col-lg-8">
                @include('alerts.alerts')

                <!-- Saved Addresses Section -->
                <div class="card mb-4 shadow-sm" style="border-radius: 10px; border: 1px solid #e2e8f0;">
                    <div class="card-header bg-white py-3 d-flex flex-wrap align-items-center justify-content-between" style="border-bottom: 1px solid #edf2f7;">
                        <div>
                            <h5 class="mb-0 text-dark font-weight-bold" style="font-size: 17px;">
                                <i class="icon-map-pin text-primary mr-2"></i>{{ __('Saved Addresses') }}
                                <span class="badge badge-primary ml-2" style="font-size: 11px;">{{ $addresses->count() }}</span>
                            </h5>
                            <small class="text-muted">{{ __('Manage your delivery locations for fast 1-click checkout.') }}</small>
                        </div>
                        <a href="#new-address-section" class="btn btn-primary btn-sm mt-2 mt-sm-0 font-weight-bold" style="border-radius: 6px;">
                            <i class="icon-plus mr-1"></i> {{ __('Add New Address') }}
                        </a>
                    </div>
                    <div class="card-body p-3 p-md-4">
                        @if($addresses->count() > 0)
                            <div class="row">
                                @foreach($addresses as $addr)
                                    <div class="col-md-6 mb-3">
                                        <div class="p-3 rounded h-100 position-relative d-flex flex-column justify-content-between" 
                                             style="border: 1.5px solid {{ $addr->is_default ? '#2563eb' : '#e2e8f0' }}; background: {{ $addr->is_default ? '#f8fafc' : '#ffffff' }}; border-radius: 8px;">
                                            <div>
                                                <div class="d-flex align-items-center justify-content-between mb-2">
                                                    <span class="font-weight-bold text-dark" style="font-size: 15px;">
                                                        <i class="icon-user text-muted mr-1"></i> {{ $addr->full_name }}
                                                    </span>
                                                    @if($addr->is_default)
                                                        <span class="badge badge-success px-2 py-1 font-weight-bold" style="font-size: 10.5px; border-radius: 4px;">
                                                            <i class="icon-check mr-1"></i> {{ __('Default') }}
                                                        </span>
                                                    @endif
                                                </div>

                                                <div class="text-muted small mb-1" style="font-size: 12px;">
                                                    <i class="icon-phone mr-1 text-primary"></i> <strong>{{ $addr->phone }}</strong>
                                                    @if($addr->email)
                                                        <span class="mx-1">•</span> <i class="icon-mail mr-1"></i> {{ $addr->email }}
                                                    @endif
                                                </div>

                                                <div class="text-dark my-2 font-weight-bold" style="font-size: 13px; line-height: 1.45;">
                                                    {{ $addr->address1 }}
                                                </div>

                                                <div class="text-muted small" style="font-size: 12px; line-height: 1.4;">
                                                    @if($addr->locality)
                                                        <span>{{ $addr->locality }}, </span>
                                                    @endif
                                                    <span>{{ $addr->area }}, {{ $addr->city }}</span>
                                                    <span class="d-block text-secondary font-weight-bold mt-1">
                                                        <i class="icon-map-pin mr-1 text-info"></i> {{ $addr->province }}, Pakistan
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="d-flex align-items-center justify-content-between pt-3 mt-3 border-top" style="border-color: #e2e8f0 !important;">
                                                <div>
                                                    @if(!$addr->is_default)
                                                        <form action="{{ route('user.address.default', $addr->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-outline-primary btn-xs py-1 px-2" style="font-size: 11px;">
                                                                {{ __('Set as Default') }}
                                                            </button>
                                                        </form>
                                                    @else
                                                        <span class="small text-success font-weight-bold" style="font-size: 11px;">
                                                            <i class="icon-check-circle mr-1"></i> {{ __('Primary Delivery') }}
                                                        </span>
                                                    @endif
                                                </div>

                                                <div>
                                                    <form action="{{ route('user.address.delete', $addr->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this saved address?') }}');">
                                                        @csrf
                                                        <button type="submit" class="btn btn-outline-danger btn-xs py-1 px-2" style="font-size: 11px;" title="{{ __('Delete Address') }}">
                                                            <i class="icon-trash-2 mr-1"></i> {{ __('Delete') }}
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4 my-2">
                                <div class="rounded-circle d-inline-flex align-items-center justify-content-center bg-light text-primary mb-3" style="width: 60px; height: 60px; font-size: 26px;">
                                    <i class="icon-map-pin"></i>
                                </div>
                                <h6 class="text-dark font-weight-bold mb-1">{{ __('No Saved Addresses') }}</h6>
                                <p class="text-muted small mb-0">{{ __('You have not saved any delivery address yet. Add your address below to enable instant 1-click checkout!') }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Add New Address Form Section -->
                <div class="card shadow-sm" id="new-address-section" style="border-radius: 10px; border: 1px solid #e2e8f0;">
                    <div class="card-header bg-white py-3" style="border-bottom: 1px solid #edf2f7;">
                        <h5 class="mb-0 text-dark font-weight-bold" style="font-size: 16px;">
                            <i class="icon-plus text-primary mr-1"></i> {{ __('Add New Address') }}
                        </h5>
                    </div>
                    <div class="card-body p-3 p-md-4">
                        <form action="{{ route('user.address.store') }}" method="POST" id="checkoutBilling">
                            @csrf
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="checkout-fn">{{ __('First Name') }}*</label>
                                        <input class="form-control" name="first_name" type="text" id="checkout-fn" 
                                            value="{{ old('first_name', $user->first_name) }}" required placeholder="{{ __('e.g. Murad') }}">
                                        @error('first_name')
                                            <p class="text-danger small mb-0">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="checkout-ln">{{ __('Last Name') }}*</label>
                                        <input class="form-control" name="last_name" type="text" id="checkout-ln" 
                                            value="{{ old('last_name', $user->last_name) }}" required placeholder="{{ __('e.g. Mahmood') }}">
                                        @error('last_name')
                                            <p class="text-danger small mb-0">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="checkout-email">{{ __('E-mail Address') }}*</label>
                                        <input class="form-control" name="email" type="email" id="checkout-email" 
                                            value="{{ old('email', $user->email) }}" required placeholder="{{ __('name@example.com') }}">
                                        @error('email')
                                            <p class="text-danger small mb-0">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="checkout-phone">{{ __('Phone Number') }}*</label>
                                        <input class="form-control" name="phone" type="text" id="checkout-phone" 
                                            value="{{ old('phone', $user->phone) }}" required placeholder="{{ __('03001234567') }}">
                                        @error('phone')
                                            <p class="text-danger small mb-0">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="checkout-province">{{ __('Province') }}*</label>
                                        <select class="form-control" name="province" id="checkout-province" required>
                                            <option value="" disabled selected>{{ __('Select Province') }}</option>
                                            <option value="Azad Kashmir" {{ old('province') == 'Azad Kashmir' ? 'selected' : '' }}>Azad Kashmir</option>
                                            <option value="Balochistan" {{ old('province') == 'Balochistan' ? 'selected' : '' }}>Balochistan</option>
                                            <option value="Federally Administered Tribal Areas" {{ old('province') == 'Federally Administered Tribal Areas' ? 'selected' : '' }}>Federally Administered Tribal Areas</option>
                                            <option value="Gilgit-Baltistan" {{ old('province') == 'Gilgit-Baltistan' ? 'selected' : '' }}>Gilgit-Baltistan</option>
                                            <option value="Islamabad" {{ old('province') == 'Islamabad' ? 'selected' : '' }}>Islamabad</option>
                                            <option value="Khyber Pakhtunkhwa" {{ old('province') == 'Khyber Pakhtunkhwa' ? 'selected' : '' }}>Khyber Pakhtunkhwa</option>
                                            <option value="Punjab" {{ old('province') == 'Punjab' ? 'selected' : '' }}>Punjab</option>
                                            <option value="Sindh" {{ old('province') == 'Sindh' ? 'selected' : '' }}>Sindh</option>
                                        </select>
                                        @error('province')
                                            <p class="text-danger small mb-0">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label for="checkout-city-select" class="mb-1">{{ __('City') }}*</label>
                                            <a href="javascript:void(0);" id="checkout-city-back-link" class="checkout-back-link" style="display: none;">{{ __('Select from list') }}</a>
                                        </div>
                                        <input type="hidden" name="city" id="checkout-city" value="{{ old('city') }}" required>
                                        <select class="form-control" id="checkout-city-select" disabled>
                                            <option value="" disabled selected>{{ __('Select Province First') }}</option>
                                        </select>
                                        <input class="form-control" type="text" id="checkout-city-custom" placeholder="{{ __('Enter your city name') }}" style="display: none;">
                                        @error('city')
                                            <p class="text-danger small mb-0">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label for="checkout-area-select" class="mb-1">{{ __('Area') }}*</label>
                                            <a href="javascript:void(0);" id="checkout-area-back-link" class="checkout-back-link" style="display: none;">{{ __('Select from list') }}</a>
                                        </div>
                                        <input type="hidden" name="area" id="checkout-area" value="{{ old('area') }}" required>
                                        <select class="form-control" id="checkout-area-select" disabled>
                                            <option value="" disabled selected>{{ __('Select City First') }}</option>
                                        </select>
                                        <input class="form-control" type="text" id="checkout-area-custom" placeholder="{{ __('Enter your area or neighborhood') }}" style="display: none;">
                                        @error('area')
                                            <p class="text-danger small mb-0">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="checkout-locality">{{ __('Colony / Suburb / Locality / Landmark') }}</label>
                                        <input class="form-control" name="locality" type="text" id="checkout-locality" 
                                            value="{{ old('locality') }}" placeholder="{{ __('Enter colony, sector, street area or nearby landmark') }}">
                                        @error('locality')
                                            <p class="text-danger small mb-0">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="checkout-address1">{{ __('Complete Address') }}*</label>
                                        <textarea class="form-control" name="address1" rows="3" id="checkout-address1" required
                                            placeholder="{{ __('House No. 123, Street 5, Sector G-11, near [Landmark]') }}">{{ old('address1') }}</textarea>
                                        <small class="d-block text-muted mt-1">{{ __('Enter your house or flat number, street number, building name and any details that will help the courier find your location.') }}</small>
                                        @error('address1')
                                            <p class="text-danger small mb-0">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap align-items-center justify-content-between mt-3 pt-2">
                                <div class="custom-control custom-checkbox mb-2 mb-sm-0">
                                    <input type="checkbox" class="custom-control-input" id="set_default_address" name="is_default" value="1" {{ $addresses->count() == 0 ? 'checked' : '' }}>
                                    <label class="custom-control-label font-weight-bold text-dark" for="set_default_address" style="font-size: 13.5px;">
                                        {{ __('Set as default delivery address') }}
                                    </label>
                                </div>
                                <button type="submit" class="btn btn-primary px-4 py-2 font-weight-bold" style="border-radius: 6px;">
                                    <i class="icon-save mr-1"></i> {{ __('Save Address') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @include('includes.checkout_location_script')

@endsection
