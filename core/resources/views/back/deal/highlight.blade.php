@extends('master.back')
@section('styles')
	<link rel="stylesheet" href="{{asset('assets/back/css/datepicker.css')}}">
    <style>
        .promoted-tag-badge-preview {
            background: linear-gradient(135deg, #15803d 0%, #16a34a 100%);
            color: #ffffff;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            padding: 4px 14px;
            border-radius: 6px;
            box-shadow: 0 3px 10px rgba(22, 163, 74, 0.35);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            line-height: 1.2;
        }
        .promoted-tag-badge-preview i {
            color: #fef08a;
            font-size: 10px;
            margin-right: 5px;
        }
        .promo-status-badge {
            font-size: 12px;
            padding: 4px 10px;
            border-radius: 6px;
            font-weight: 600;
        }
    </style>
@endsection
@section('content')

<div class="container-fluid">

<!-- Page Heading -->
<div class="card mb-4">
    <div class="card-body">
        <div class="d-sm-flex align-items-center justify-content-between">
            <h3 class="mb-0 bc-title"><b>{{ __('Highlight Bundle Deal') }}</b></h3>
            <a class="btn btn-primary btn-sm" href="{{route('back.deal.index')}}"><i class="fas fa-chevron-left"></i> {{ __('Back to Bundles') }}</a>
        </div>
    </div>
</div>

@php
    $bundleImg = '';
    if ($deal->photo) {
        $bundleImg = \Illuminate\Support\Str::startsWith($deal->photo, 'images/')
            ? url('/core/public/storage/' . $deal->photo)
            : url('/core/public/storage/images/' . $deal->photo);
    } else {
        $firstItem = $deal->dealItems->first()->item ?? null;
        if ($firstItem) {
            $firstThumb = $firstItem->photo ?: $firstItem->thumbnail;
            $bundleImg = \Illuminate\Support\Str::startsWith($firstThumb, 'images/')
                ? url('/core/public/storage/' . $firstThumb)
                : url('/core/public/storage/images/' . $firstThumb);
        } else {
            $bundleImg = url('/core/public/storage/images/placeholder.png');
        }
    }
@endphp

<!-- Bundle Quick Overview -->
<div class="card mb-4 shadow-sm border-0">
    <div class="card-body py-3">
        <div class="row align-items-center">
            <div class="col-auto">
                <img src="{{ $bundleImg }}" 
                     alt="{{ $deal->name }}" 
                     style="width: 70px; height: 70px; object-fit: cover; border-radius: 8px; border: 2px solid #e2e8f0;">
            </div>
            <div class="col">
                <h5 class="mb-1 font-weight-bold text-dark">{{ $deal->name }}</h5>
                <div class="d-flex flex-wrap align-items-center" style="gap: 8px; font-size: 13px;">
                    @if($deal->sku)
                        <span class="badge badge-light border text-muted"><i class="fas fa-barcode mr-1"></i>{{ $deal->sku }}</span>
                    @endif
                    <span class="badge badge-secondary">{{ $deal->dealItems->count() }} {{ __('Items') }}</span>
                    <span class="text-success font-weight-bold">{{ PriceHelper::adminCurrencyPrice($deal->discounted_price) }}</span>
                    <span class="badge badge-warning text-dark font-weight-bold">{{ $deal->discount_badge }}</span>
                    @if($deal->vendor_id > 0 && $deal->vendor)
                        <span class="badge badge-info"><i class="fas fa-store mr-1"></i>{{ $deal->vendor->shop_name ?: $deal->vendor->first_name }}</span>
                    @else
                        <span class="badge badge-primary"><i class="fas fa-user-shield mr-1"></i>{{ __('Admin Bundle') }}</span>
                    @endif
                </div>
            </div>
            <div class="col-12 col-md-auto mt-2 mt-md-0">
                @if($deal->isPromotionActive())
                    <div class="alert alert-success mb-0 py-2 px-3 d-inline-flex align-items-center" style="border-radius: 8px; font-size: 13px;">
                        <i class="fas fa-crown text-warning mr-2" style="font-size: 16px;"></i>
                        <div>
                            <strong class="d-block text-success">{{ __('Active Highlight Badge:') }} {{ $deal->promotion_tag }}</strong>
                            <small class="text-muted">
                                @if($deal->promotion_expires_at)
                                    {{ __('Expires:') }} {{ \Carbon\Carbon::parse($deal->promotion_expires_at)->format('d M, Y') }} ({{ \Carbon\Carbon::parse($deal->promotion_expires_at)->diffForHumans() }})
                                @else
                                    {{ __('Unlimited / Permanent') }}
                                @endif
                            </small>
                        </div>
                    </div>
                @else
                    <span class="badge badge-secondary promo-status-badge"><i class="fas fa-info-circle mr-1"></i>{{ __('No Active Highlight Badge') }}</span>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Form -->
<div class="row">
    <div class="col-xl-8 col-lg-8 col-md-12">
        <div class="card o-hidden border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="m-0 font-weight-bold text-primary"><i class="fas fa-crown text-warning mr-2"></i>{{ __('Highlight Badge & Promotion Settings') }}</h5>
                <small class="text-muted">{{ __('This badge displays on the bundle card and bundle detail page with a highlighted green border.') }}</small>
            </div>
            <div class="card-body">
                <form class="admin-form" action="{{ route('back.deal.highlight.update', $deal->id) }}" method="POST">
                    @csrf
                    @include('alerts.alerts')

                    <!-- Highlight Tag Selection -->
                    <div class="form-group mb-3">
                        <label for="promotion_tag_id" class="font-weight-bold text-dark">{{ __('Select Highlight Tag') }} *</label>
                        <select name="promotion_tag_id" id="promotion_tag_id" class="form-control select2">
                            <option value="none" {{ (!$deal->isPromotionActive() && empty($deal->promotion_tag)) ? 'selected' : '' }}>
                                {{ __('-- No Highlight Badge (Remove) --') }}
                            </option>
                            @foreach($tags as $tag)
                                <option value="{{ $tag->id }}" 
                                    data-name="{{ $tag->name }}"
                                    {{ ($deal->promotion_tag_id == $tag->id || ($deal->isPromotionActive() && $deal->promotion_tag == $tag->name)) ? 'selected' : '' }}>
                                    ⭐ {{ $tag->name }}
                                </option>
                            @endforeach
                            <option value="custom" 
                                data-name="{{ ($deal->isPromotionActive() && !$deal->promotion_tag_id) ? $deal->promotion_tag : '' }}"
                                {{ ($deal->isPromotionActive() && !$deal->promotion_tag_id && !empty($deal->promotion_tag)) ? 'selected' : '' }}>
                                ✍️ {{ __('+ Custom Tag Name...') }}
                            </option>
                        </select>
                    </div>

                    <!-- Custom Tag Input -->
                    <div class="form-group mb-3" id="custom-tag-group" style="{{ ($deal->isPromotionActive() && !$deal->promotion_tag_id && !empty($deal->promotion_tag)) ? '' : 'display:none;' }}">
                        <label for="custom_promotion_tag" class="font-weight-bold text-dark">{{ __('Enter Custom Tag Name') }} *</label>
                        <input type="text" name="custom_promotion_tag" id="custom_promotion_tag" class="form-control" 
                               placeholder="{{ __('e.g. Mega Deal, Best Combo, Super Bundle') }}"
                               value="{{ ($deal->isPromotionActive() && !$deal->promotion_tag_id) ? $deal->promotion_tag : '' }}">
                        <small class="form-text text-muted">{{ __('Keep it short (max 20 characters) for optimal mobile display.') }}</small>
                    </div>

                    <!-- Duration Section -->
                    <div id="duration-settings-container">
                        <label class="font-weight-bold text-dark mt-2 mb-2">{{ __('Promotion Duration / Validity') }} *</label>
                        
                        <div class="row g-2 mb-3">
                            <div class="col-md-6 mb-2">
                                <div class="custom-control custom-radio border rounded p-3 h-100 bg-light">
                                    <input type="radio" id="mode_permanent" name="promotion_duration_mode" value="permanent" class="custom-control-input duration-mode-radio" 
                                        {{ (empty($deal->promotion_expires_at) || !$deal->isPromotionActive()) ? 'checked' : '' }}>
                                    <label class="custom-control-label font-weight-bold text-dark" for="mode_permanent">
                                        <i class="fas fa-infinity text-primary mr-1"></i> {{ __('Unlimited / Permanent') }}
                                        <small class="d-block text-muted font-weight-normal mt-1">{{ __('Badge stays active until manually removed or deal ends.') }}</small>
                                    </label>
                                </div>
                            </div>
                            @if($plans && $plans->count() > 0)
                            <div class="col-md-6 mb-2">
                                <div class="custom-control custom-radio border rounded p-3 h-100 bg-light">
                                    <input type="radio" id="mode_plan" name="promotion_duration_mode" value="plan" class="custom-control-input duration-mode-radio"
                                        {{ ($deal->promotion_expires_at && $deal->promotion_days && $plans->where('days', $deal->promotion_days)->count() > 0) ? 'checked' : '' }}>
                                    <label class="custom-control-label font-weight-bold text-dark" for="mode_plan">
                                        <i class="fas fa-calendar-check text-success mr-1"></i> {{ __('Standard Plan Duration') }}
                                        <small class="d-block text-muted font-weight-normal mt-1">{{ __('Choose from predefined store plans.') }}</small>
                                    </label>
                                </div>
                            </div>
                            @endif
                            <div class="col-md-6 mb-2">
                                <div class="custom-control custom-radio border rounded p-3 h-100 bg-light">
                                    <input type="radio" id="mode_days" name="promotion_duration_mode" value="days" class="custom-control-input duration-mode-radio">
                                    <label class="custom-control-label font-weight-bold text-dark" for="mode_days">
                                        <i class="fas fa-hourglass-half text-warning mr-1"></i> {{ __('Custom Number of Days') }}
                                        <small class="d-block text-muted font-weight-normal mt-1">{{ __('Set exact number of days from today.') }}</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="custom-control custom-radio border rounded p-3 h-100 bg-light">
                                    <input type="radio" id="mode_date" name="promotion_duration_mode" value="date" class="custom-control-input duration-mode-radio">
                                    <label class="custom-control-label font-weight-bold text-dark" for="mode_date">
                                        <i class="fas fa-calendar-alt text-info mr-1"></i> {{ __('Specific Expiry Date') }}
                                        <small class="d-block text-muted font-weight-normal mt-1">{{ __('Choose an exact calendar date.') }}</small>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Plan Dropdown -->
                        @if($plans && $plans->count() > 0)
                        <div class="form-group mb-3 duration-input-group" id="plan-input-group" style="display: none;">
                            <label for="promotion_plan_id" class="font-weight-bold text-dark">{{ __('Select Plan') }}</label>
                            <select name="promotion_plan_id" id="promotion_plan_id" class="form-control">
                                @foreach($plans as $plan)
                                    <option value="{{ $plan->id }}" {{ ($deal->promotion_days == $plan->days) ? 'selected' : '' }}>
                                        {{ $plan->days }} {{ __('Days') }} ({{ PriceHelper::adminCurrencyPrice($plan->price) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        <!-- Custom Days Input -->
                        <div class="form-group mb-3 duration-input-group" id="days-input-group" style="display: none;">
                            <label for="promotion_custom_days" class="font-weight-bold text-dark">{{ __('Number of Days') }}</label>
                            <input type="number" name="promotion_custom_days" id="promotion_custom_days" class="form-control" 
                                   min="1" max="3650" value="{{ $deal->promotion_days ?: 30 }}" placeholder="{{ __('e.g. 15, 30, 60') }}">
                        </div>

                        <!-- Specific Date Input -->
                        <div class="form-group mb-3 duration-input-group" id="date-input-group" style="display: none;">
                            <label for="promotion_expires_at" class="font-weight-bold text-dark">{{ __('Select Expiry Date') }}</label>
                            <input type="text" name="promotion_expires_at" id="promotion_expires_at" class="form-control datepicker" 
                                   placeholder="{{ __('YYYY-MM-DD') }}" 
                                   value="{{ $deal->promotion_expires_at ? \Carbon\Carbon::parse($deal->promotion_expires_at)->format('Y-m-d') : '' }}">
                        </div>
                    </div>

                    <div class="form-group mt-4 mb-2">
                        <button type="submit" class="btn btn-success btn-lg px-4 font-weight-bold shadow-sm">
                            <i class="fas fa-check-circle mr-1"></i> {{ __('Save & Apply Highlight') }}
                        </button>
                        <a href="{{ route('back.deal.index') }}" class="btn btn-light btn-lg px-3 ml-2 border">
                            {{ __('Cancel') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Live Preview Sidebar -->
    <div class="col-xl-4 col-lg-4 col-md-12">
        <div class="card shadow-sm border-0 mb-4 sticky-top" style="top: 20px;">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-eye text-primary mr-2"></i>{{ __('Live Store Bundle Preview') }}</h6>
            </div>
            <div class="card-body text-center p-4">
                <div class="border rounded p-3 bg-white position-relative shadow-sm" style="max-width: 260px; margin: 0 auto; border-radius: 12px !important; border: 2px solid #16a34a !important;">
                    <!-- Badge Preview Element -->
                    <div id="preview-badge-wrapper" style="{{ ($deal->isPromotionActive() || old('promotion_tag_id')) ? '' : 'display:none;' }}">
                        <div class="promoted-tag-badge-preview mb-2">
                            <i class="fas fa-crown"></i>
                            <span id="preview-badge-text">{{ $deal->promotion_tag ?: 'Best Product' }}</span>
                        </div>
                    </div>

                    <div id="no-badge-preview" class="py-2 text-muted" style="{{ ($deal->isPromotionActive() || old('promotion_tag_id')) ? 'display:none;' : '' }}">
                        <i class="fas fa-tag fa-2x mb-1 text-muted"></i>
                        <p class="small mb-0">{{ __('Select a highlight tag on the left to preview.') }}</p>
                    </div>

                    <!-- Mock Bundle Header -->
                    <div class="bg-success text-white py-1 px-2 rounded mb-2 d-flex justify-content-between align-items-center" style="font-size: 9.5px;">
                        <span><i class="fas fa-clock mr-1"></i> 2d 14h left</span>
                        <span class="badge badge-warning text-dark">{{ $deal->discount_badge }}</span>
                    </div>

                    <!-- Mock Thumbnail -->
                    <img src="{{ $bundleImg }}" 
                         alt="{{ $deal->name }}" 
                         class="img-fluid rounded mb-2" 
                         style="width: 130px; height: 130px; object-fit: cover; border: 1px solid #e2e8f0;">

                    <h6 class="font-weight-bold text-truncate mb-1" style="font-size: 13px;">{{ $deal->name }}</h6>
                    <div class="text-success font-weight-bold mb-2" style="font-size: 14px;">{{ PriceHelper::adminCurrencyPrice($deal->discounted_price) }}</div>
                    
                    <button class="btn btn-outline-success btn-sm btn-block disabled" style="font-size: 11px; border-radius: 20px;">
                        <i class="fas fa-shopping-bag mr-1"></i> {{ __('View Bundle') }}
                    </button>
                </div>

                <div class="mt-4 p-3 border rounded bg-white text-left" style="font-size: 12.5px; line-height: 1.6;">
                    <strong class="d-block text-dark mb-1"><i class="fas fa-info-circle text-primary mr-1"></i> {{ __('Store Visibility') }}</strong>
                    <p class="text-muted mb-0">
                        {{ __('Once saved, this bundle will display the glowing green border and crown badge on the homepage Flash Deals & Bundle carousel, Deals Catalog, and Bundle Details page.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

</div>

@endsection

@section('scripts')
<script src="{{asset('assets/back/js/bootstrap-datepicker.js')}}"></script>
<script>
$(document).ready(function() {
    // Datepicker
    $('.datepicker').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true
    });

    // Update Live Preview & Custom Tag visibility
    function updateBadgePreview() {
        var selectedVal = $('#promotion_tag_id').val();
        var badgeText = '';

        if (selectedVal === 'none' || selectedVal === '') {
            $('#preview-badge-wrapper').hide();
            $('#no-badge-preview').show();
            $('#custom-tag-group').hide();
            $('#duration-settings-container').slideUp(200);
        } else {
            $('#duration-settings-container').slideDown(200);
            if (selectedVal === 'custom') {
                $('#custom-tag-group').show();
                badgeText = $('#custom_promotion_tag').val().trim() || 'Custom Tag';
            } else {
                $('#custom-tag-group').hide();
                badgeText = $('#promotion_tag_id option:selected').data('name') || $('#promotion_tag_id option:selected').text().replace('⭐ ', '').trim();
            }

            $('#preview-badge-text').text(badgeText);
            $('#preview-badge-wrapper').show();
            $('#no-badge-preview').hide();
        }
    }

    // Handle Duration mode visibility
    function updateDurationInputs() {
        var mode = $('input[name="promotion_duration_mode"]:checked').val();
        $('.duration-input-group').hide();

        if (mode === 'plan') {
            $('#plan-input-group').show();
        } else if (mode === 'days') {
            $('#days-input-group').show();
        } else if (mode === 'date') {
            $('#date-input-group').show();
        }
    }

    $('#promotion_tag_id').on('change', updateBadgePreview);
    $('#custom_promotion_tag').on('input keyup', updateBadgePreview);
    $('input[name="promotion_duration_mode"]').on('change', updateDurationInputs);

    // Initial trigger
    updateBadgePreview();
    updateDurationInputs();
});
</script>
@endsection
