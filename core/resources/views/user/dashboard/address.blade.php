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
                <div class="card mb-4 shadow-sm" style="border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden;">
                    <div class="card-header bg-white py-3 px-3 px-sm-4 d-flex flex-wrap align-items-center justify-content-between" style="border-bottom: 1px solid #f1f5f9;">
                        <div class="d-flex align-items-center mb-2 mb-sm-0">
                            <div class="header-icon-box mr-3">
                                <i class="fas fa-map-marked-alt"></i>
                            </div>
                            <div>
                                <div class="d-flex align-items-center flex-wrap">
                                    <h5 class="mb-0 text-dark font-weight-bold" style="font-size: 17px; letter-spacing: -0.2px;">{{ __('Saved Addresses') }}</h5>
                                    <span class="badge badge-purple-soft ml-2">{{ $addresses->count() }} {{ __('Saved') }}</span>
                                </div>
                                <small class="text-muted" style="font-size: 12.5px;">{{ __('Manage your delivery locations for fast 1-click checkout.') }}</small>
                            </div>
                        </div>
                        <a href="#new-address-section" class="btn btn-primary btn-sm font-weight-bold px-3 py-2" style="border-radius: 8px; font-size: 12.5px; box-shadow: 0 2px 8px rgba(121, 82, 179, 0.25);">
                            <i class="fas fa-plus mr-1"></i> {{ __('Add New Address') }}
                        </a>
                    </div>
                    <div class="card-body p-3 p-md-4">
                        @if($addresses->count() > 0)
                            <div class="row">
                                @foreach($addresses as $addr)
                                    <div class="col-md-6 mb-3">
                                        <div class="user-address-card h-100 {{ $addr->is_default ? 'is-default-card' : '' }}">
                                            <div>
                                                <div class="card-top-header d-flex align-items-center justify-content-between mb-2">
                                                    <div class="d-flex align-items-center text-truncate mr-2">
                                                        <div class="user-avatar-icon mr-2">
                                                            <i class="fas fa-user text-primary"></i>
                                                        </div>
                                                        <span class="recipient-title font-weight-bold text-dark text-truncate" title="{{ $addr->full_name }}">
                                                            {{ $addr->full_name }}
                                                        </span>
                                                    </div>
                                                    @if($addr->is_default)
                                                        <span class="badge-default-active">
                                                            <i class="fas fa-check-circle mr-1"></i> {{ __('Default') }}
                                                        </span>
                                                    @endif
                                                </div>

                                                <div class="contact-pill-row mb-2">
                                                    <span class="contact-badge"><i class="fas fa-phone-alt mr-1 text-primary"></i> <strong>{{ $addr->phone }}</strong></span>
                                                    @if($addr->email)
                                                        <span class="contact-badge"><i class="fas fa-envelope mr-1 text-muted"></i> {{ $addr->email }}</span>
                                                    @endif
                                                </div>

                                                <div class="address-details-box">
                                                    <div class="address-street">
                                                        <i class="fas fa-home text-muted mr-1"></i> {{ $addr->address1 }}
                                                    </div>
                                                    <div class="address-sub text-muted">
                                                        <i class="fas fa-map-marker-alt text-primary mr-1"></i>
                                                        @if($addr->locality)
                                                            <span>{{ $addr->locality }}, </span>
                                                        @endif
                                                        <span>{{ $addr->area }}, {{ $addr->city }}</span>
                                                    </div>
                                                    <div class="address-region font-weight-bold text-secondary">
                                                        <i class="fas fa-flag text-muted mr-1"></i> {{ $addr->province }}, Pakistan
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="card-actions-footer d-flex align-items-center justify-content-between pt-3 mt-3 border-top">
                                                <div>
                                                    @if(!$addr->is_default)
                                                        <form action="{{ route('user.address.default', $addr->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-outline-primary btn-sm py-1 px-2" style="font-size: 11.5px; border-radius: 6px; font-weight: 600;">
                                                                <i class="fas fa-star mr-1"></i> {{ __('Set as Default') }}
                                                            </button>
                                                        </form>
                                                    @else
                                                        <span class="default-status-text">
                                                            <i class="fas fa-check-double mr-1 text-success"></i> {{ __('Primary Delivery') }}
                                                        </span>
                                                    @endif
                                                </div>

                                                <div>
                                                    <form action="{{ route('user.address.delete', $addr->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this saved address?') }}');">
                                                        @csrf
                                                        <button type="submit" class="btn btn-outline-danger btn-sm py-1 px-2" style="font-size: 11.5px; border-radius: 6px;" title="{{ __('Delete Address') }}">
                                                            <i class="fas fa-trash-alt mr-1"></i> {{ __('Delete') }}
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5 my-2">
                                <div class="rounded-circle d-inline-flex align-items-center justify-content-center bg-light text-primary mb-3" style="width: 65px; height: 65px; font-size: 28px;">
                                    <i class="fas fa-map-marked-alt"></i>
                                </div>
                                <h6 class="text-dark font-weight-bold mb-1">{{ __('No Saved Addresses Yet') }}</h6>
                                <p class="text-muted small mb-3" style="max-width: 420px; margin: 0 auto;">{{ __('Add your delivery addresses here for lightning-fast 1-click checkout whenever you place an order.') }}</p>
                                <a href="#new-address-section" class="btn btn-primary btn-sm font-weight-bold px-3 py-2" style="border-radius: 8px;">
                                    <i class="fas fa-plus mr-1"></i> {{ __('Add Your First Address') }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Add New Address Form Section -->
                <div class="card shadow-sm" id="new-address-section" style="border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden;">
                    <div class="card-header bg-white py-3 px-3 px-sm-4 d-flex align-items-center justify-content-between" style="border-bottom: 1px solid #f1f5f9;">
                        <div class="d-flex align-items-center">
                            <div class="header-icon-box mr-3" style="background: rgba(37, 99, 235, 0.1); color: #2563eb;">
                                <i class="fas fa-plus"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 text-dark font-weight-bold" style="font-size: 16.5px;">
                                    {{ __('Add New Address') }}
                                </h5>
                                <small class="text-muted" style="font-size: 12px;">{{ __('Save a permanent address to your address book.') }}</small>
                            </div>
                        </div>
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
                                    <label class="custom-control-label font-weight-bold text-dark" for="set_default_address" style="font-size: 13.5px; cursor: pointer;">
                                        {{ __('Set as default delivery address') }}
                                    </label>
                                </div>
                                <button type="submit" class="btn btn-primary px-4 py-2 font-weight-bold" style="border-radius: 8px; box-shadow: 0 2px 8px rgba(121, 82, 179, 0.25);">
                                    <i class="fas fa-save mr-1"></i> {{ __('Save Address') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <style>
        .header-icon-box {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            background: rgba(121, 82, 179, 0.1);
            color: #7952b3;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }
        .badge-purple-soft {
            background: #f3e8ff;
            color: #7952b3;
            font-weight: 700;
            font-size: 11.5px;
            padding: 4px 10px;
            border-radius: 20px;
            border: 1px solid rgba(121, 82, 179, 0.2);
        }
        .user-address-card {
            padding: 16px;
            border-radius: 12px;
            border: 2px solid #e2e8f0;
            background: #ffffff;
            transition: all 0.25s ease;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
        }
        .user-address-card:hover {
            border-color: #7952b3;
            box-shadow: 0 6px 18px rgba(121, 82, 179, 0.1);
            transform: translateY(-2px);
        }
        .user-address-card.is-default-card {
            border-color: #7952b3;
            background: #fdfcff;
            box-shadow: 0 4px 14px rgba(121, 82, 179, 0.08);
        }
        .user-avatar-icon {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #f3f0fa;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
        }
        .recipient-title {
            font-size: 15.5px;
            color: #0f172a;
        }
        .badge-default-active {
            background: #ecfdf5;
            color: #059669;
            border: 1px solid #a7f3d0;
            font-weight: 700;
            font-size: 11px;
            padding: 4px 9px;
            border-radius: 20px;
            white-space: nowrap;
        }
        .contact-pill-row {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }
        .contact-badge {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 12px;
            color: #475569;
        }
        .address-details-box {
            background: #f8fafc;
            border: 1px solid #edf2f7;
            border-radius: 8px;
            padding: 10px 12px;
            margin-top: 8px;
        }
        .address-street {
            font-size: 13.5px;
            font-weight: 600;
            color: #1e293b;
            line-height: 1.45;
            margin-bottom: 4px;
        }
        .address-sub {
            font-size: 12.5px;
            line-height: 1.4;
            color: #475569;
        }
        .address-region {
            font-size: 12px;
            margin-top: 4px;
        }
        .default-status-text {
            font-size: 11.5px;
            font-weight: 700;
            color: #16a34a;
        }
    </style>

    @include('includes.checkout_location_script')

@endsection
