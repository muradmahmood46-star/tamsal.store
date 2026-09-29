@extends('master.back')
@section('styles')
	<link rel="stylesheet" href="{{asset('assets/back/css/datepicker.css')}}">
@endsection
@section('content')

<div class="container-fluid">

<!-- Page Heading -->
<div class="card mb-4 shadow-sm">
    <div class="card-body">
        <div class="d-sm-flex align-items-center justify-content-between">
            <h3 class="mb-0 bc-title font-weight-bold"><b>{{ __('Highlight Bundle Deal') }}</b></h3>
            <a class="btn btn-primary btn-sm" href="{{route('back.deal.index')}}"><i class="fas fa-chevron-left"></i> {{ __('Back') }}</a>
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

<!-- Bundle Info Header -->
<div class="card mb-4 shadow-sm border-0">
    <div class="card-body py-3">
        <div class="d-flex align-items-center">
            <img src="{{ $bundleImg }}" 
                 alt="{{ $deal->name }}" 
                 style="width: 55px; height: 55px; object-fit: cover; border-radius: 6px; border: 1px solid #e2e8f0;" class="mr-3">
            <div>
                <h5 class="mb-1 font-weight-bold text-dark" style="font-size: 16px;">{{ $deal->name }}</h5>
                <div class="d-flex align-items-center flex-wrap" style="gap: 8px; font-size: 13px;">
                    @if($deal->sku)
                        <span class="badge badge-light border text-muted"><i class="fas fa-barcode mr-1"></i>{{ $deal->sku }}</span>
                    @endif
                    <span class="badge badge-secondary">{{ $deal->dealItems->count() }} {{ __('Items') }}</span>
                    <strong class="text-success">{{ PriceHelper::adminCurrencyPrice($deal->discounted_price) }}</strong>
                    <span class="badge badge-warning text-dark font-weight-bold">{{ $deal->discount_badge }}</span>
                    @if($deal->isPromotionActive())
                        <span class="badge badge-warning text-dark font-weight-bold" style="background:#fef08a; border:1px solid #facc15;">
                            <i class="fas fa-crown text-warning mr-1"></i> {{ $deal->promotion_tag }} ({{ $deal->promotion_expires_at ? \Carbon\Carbon::parse($deal->promotion_expires_at)->diffForHumans() : __('Permanent') }})
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Highlight Settings Form -->
<div class="row">
    <div class="col-lg-8 col-md-10">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form class="admin-form" action="{{ route('back.deal.highlight.update', $deal->id) }}" method="POST">
                    @csrf
                    @include('alerts.alerts')

                    <!-- 1. Select Highlight Tag -->
                    <div class="form-group mb-4">
                        <label for="promotion_tag_id" class="font-weight-bold text-dark" style="font-size: 15px;">
                            <i class="fas fa-crown text-warning mr-1"></i> {{ __('Select Highlight Badge') }}
                        </label>
                        <select name="promotion_tag_id" id="promotion_tag_id" class="form-control form-control-lg">
                            <option value="none" {{ (!$deal->isPromotionActive() && empty($deal->promotion_tag)) ? 'selected' : '' }}>
                                {{ __('-- No Highlight Badge (Remove) --') }}
                            </option>
                            @foreach($tags as $tag)
                                <option value="{{ $tag->id }}" 
                                    {{ ($deal->promotion_tag_id == $tag->id || ($deal->isPromotionActive() && $deal->promotion_tag == $tag->name)) ? 'selected' : '' }}>
                                    ⭐ {{ $tag->name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">{{ __('These badges are managed in Stores Rate & Setting > Promotion Settings.') }}</small>
                    </div>

                    <!-- 2. Select Duration -->
                    <div class="form-group mb-4" id="duration-section">
                        <label class="font-weight-bold text-dark d-block mb-2" style="font-size: 15px;">
                            <i class="fas fa-clock text-primary mr-1"></i> {{ __('Promotion Duration / Validity') }}
                        </label>
                        
                        <div class="border rounded p-3 bg-light">
                            <!-- Radio Option 1: Permanent -->
                            <div class="custom-control custom-radio mb-3">
                                <input type="radio" id="mode_permanent" name="promotion_duration_mode" value="permanent" class="custom-control-input" 
                                    {{ (empty($deal->promotion_expires_at) || !$deal->isPromotionActive()) ? 'checked' : '' }}>
                                <label class="custom-control-label font-weight-bold text-dark" for="mode_permanent" style="font-size: 14.5px; cursor: pointer;">
                                    <i class="fas fa-infinity text-primary mr-1"></i> {{ __('Permanent / Unlimited') }} 
                                    <span class="text-muted font-weight-normal d-block small">{{ __('(Badge will stay on bundle forever until manually removed)') }}</span>
                                </label>
                            </div>

                            <hr class="my-3">

                            <!-- Radio Option 2: Number of Days -->
                            <div class="custom-control custom-radio mb-2">
                                <input type="radio" id="mode_days" name="promotion_duration_mode" value="days" class="custom-control-input"
                                    {{ ($deal->promotion_expires_at && $deal->isPromotionActive()) ? 'checked' : '' }}>
                                <label class="custom-control-label font-weight-bold text-dark" for="mode_days" style="font-size: 14.5px; cursor: pointer;">
                                    <i class="fas fa-hourglass-half text-warning mr-1"></i> {{ __('Expire After Days') }}
                                    <span class="text-muted font-weight-normal d-block small">{{ __('(Badge will automatically be removed after the specified number of days)') }}</span>
                                </label>
                            </div>

                            <!-- ALWAYS VISIBLE DAYS INPUT BOX -->
                            <div class="mt-2 ml-4 p-3 bg-white rounded border" style="max-width: 320px;">
                                <label for="promotion_days" class="font-weight-bold text-dark mb-1 d-block" style="font-size: 13px;">
                                    <i class="fas fa-calendar-alt text-success mr-1"></i> {{ __('Enter Number of Days:') }}
                                </label>
                                <div class="input-group">
                                    <input type="number" name="promotion_days" id="promotion_days" class="form-control font-weight-bold text-center" 
                                           min="1" max="3650" value="{{ $deal->promotion_days ?: 30 }}" placeholder="30" style="font-size: 16px; background-color: #f8fafc;">
                                    <div class="input-group-append">
                                        <span class="input-group-text font-weight-bold bg-light">{{ __('Days') }}</span>
                                    </div>
                                </div>
                                <small class="text-muted d-block mt-2" style="font-size: 11.5px; line-height: 1.4;">
                                    {{ __('Example: Write 7, 15, 30 or 60 days.') }}
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-secondary btn-lg px-4 font-weight-bold shadow-sm">
                            <i class="fas fa-save mr-1"></i> {{ __('Save Changes') }}
                        </button>
                        <a href="{{ route('back.deal.index') }}" class="btn btn-light btn-lg px-3 ml-2 border">
                            {{ __('Cancel') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // When clicking or typing into days input box, auto-select the "Expire After Days" radio
    $('#promotion_days').on('focus input change', function() {
        $('#mode_days').prop('checked', true);
    });

    // Toggle duration section if "none" is selected
    $('#promotion_tag_id').on('change', function() {
        if ($(this).val() === 'none') {
            $('#duration-section').slideUp(150);
        } else {
            $('#duration-section').slideDown(150);
        }
    });

    if ($('#promotion_tag_id').val() === 'none') {
        $('#duration-section').hide();
    }
});
</script>
@endsection
