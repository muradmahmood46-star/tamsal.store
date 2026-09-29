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
            <h3 class="mb-0 bc-title font-weight-bold"><b>{{ __('Highlight Product') }}</b></h3>
            <a class="btn btn-primary btn-sm" href="{{route('back.item.index')}}"><i class="fas fa-chevron-left"></i> {{ __('Back') }}</a>
        </div>
    </div>
</div>

<!-- Product Info Header -->
<div class="card mb-4 shadow-sm border-0">
    <div class="card-body py-3">
        <div class="d-flex align-items-center">
            <img src="{{ $item->thumbnail ? url('/core/public/storage/images/'.$item->thumbnail) : url('/core/public/storage/images/placeholder.png') }}" 
                 alt="{{ $item->name }}" 
                 style="width: 55px; height: 55px; object-fit: cover; border-radius: 6px; border: 1px solid #e2e8f0;" class="mr-3">
            <div>
                <h5 class="mb-1 font-weight-bold text-dark" style="font-size: 16px;">{{ $item->name }}</h5>
                <div class="d-flex align-items-center flex-wrap" style="gap: 8px; font-size: 13px;">
                    @if($item->sku)
                        <span class="badge badge-light border text-muted"><i class="fas fa-barcode mr-1"></i>{{ $item->sku }}</span>
                    @endif
                    <strong class="text-success">{{ PriceHelper::adminCurrencyPrice($item->discount_price) }}</strong>
                    @if($item->isPromotionActive())
                        <span class="badge badge-warning text-dark font-weight-bold" style="background:#fef08a; border:1px solid #facc15;">
                            <i class="fas fa-crown text-warning mr-1"></i> {{ $item->promotion_tag }} ({{ $item->promotion_expires_at ? \Carbon\Carbon::parse($item->promotion_expires_at)->diffForHumans() : __('Permanent') }})
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
                <form class="admin-form" action="{{ route('back.item.highlight.update', $item->id) }}" method="POST">
                    @csrf
                    @include('alerts.alerts')

                    <!-- 1. Select Highlight Tag -->
                    <div class="form-group mb-4">
                        <label for="promotion_tag_id" class="font-weight-bold text-dark" style="font-size: 15px;">
                            <i class="fas fa-crown text-warning mr-1"></i> {{ __('Select Highlight Badge') }}
                        </label>
                        <select name="promotion_tag_id" id="promotion_tag_id" class="form-control form-control-lg">
                            <option value="none" {{ (!$item->isPromotionActive() && empty($item->promotion_tag)) ? 'selected' : '' }}>
                                {{ __('-- No Highlight Badge (Remove) --') }}
                            </option>
                            @foreach($tags as $tag)
                                <option value="{{ $tag->id }}" 
                                    {{ ($item->promotion_tag_id == $tag->id || ($item->isPromotionActive() && $item->promotion_tag == $tag->name)) ? 'selected' : '' }}>
                                    ⭐ {{ $tag->name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">{{ __('These badges are managed in Stores Rate & Setting > Promotion Settings.') }}</small>
                    </div>

                    <!-- 2. Select Duration -->
                    <div class="form-group mb-4" id="duration-section">
                        <label class="font-weight-bold text-dark d-block" style="font-size: 15px;">
                            <i class="fas fa-clock text-primary mr-1"></i> {{ __('Promotion Duration') }}
                        </label>
                        
                        <div class="border rounded p-3 bg-light">
                            <div class="form-check mb-3">
                                <input class="form-check-input duration-mode-input" type="radio" name="promotion_duration_mode" id="mode_permanent" value="permanent" 
                                    {{ (empty($item->promotion_expires_at) || !$item->isPromotionActive()) ? 'checked' : '' }} 
                                    style="cursor:pointer; width: 18px; height: 18px; margin-top: 2px;">
                                <label class="form-check-label font-weight-bold text-dark ml-2" for="mode_permanent" style="cursor:pointer; font-size: 14.5px;">
                                    {{ __('Permanent / Unlimited') }} <span class="text-muted font-weight-normal">({{ __('Badge will stay active forever until removed') }})</span>
                                </label>
                            </div>

                            <div class="form-check mb-2">
                                <input class="form-check-input duration-mode-input" type="radio" name="promotion_duration_mode" id="mode_days" value="days" 
                                    {{ ($item->promotion_expires_at && $item->isPromotionActive()) ? 'checked' : '' }} 
                                    style="cursor:pointer; width: 18px; height: 18px; margin-top: 2px;">
                                <label class="form-check-label font-weight-bold text-dark ml-2" for="mode_days" style="cursor:pointer; font-size: 14.5px;">
                                    {{ __('Set Duration in Days') }} <span class="text-muted font-weight-normal">({{ __('Badge will automatically expire after specified days') }})</span>
                                </label>
                            </div>

                            <!-- Enter Number of Days Input Box -->
                            <div id="days-input-wrapper" class="mt-2 ml-4 p-3 bg-white rounded border shadow-sm" style="max-width: 320px; {{ ($item->promotion_expires_at && $item->isPromotionActive()) ? '' : 'display:none;' }}">
                                <label for="promotion_days" class="small font-weight-bold text-dark mb-1 d-block">
                                    <i class="fas fa-calendar-day text-primary mr-1"></i> {{ __('Enter Number of Days:') }}
                                </label>
                                <div class="input-group">
                                    <input type="number" name="promotion_days" id="promotion_days" class="form-control font-weight-bold text-center" 
                                           min="1" max="3650" value="{{ $item->promotion_days ?: 30 }}" placeholder="30" style="font-size: 16px;">
                                    <div class="input-group-append">
                                        <span class="input-group-text font-weight-bold bg-light">{{ __('Days') }}</span>
                                    </div>
                                </div>
                                <small class="text-muted d-block mt-2" style="font-size: 12px; line-height: 1.4;">
                                    <i class="fas fa-info-circle text-info mr-1"></i>{{ __('Enter days (e.g. 15, 30). After this time, the badge will be automatically removed.') }}
                                </small>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- 3. Legacy Section Category (is_type) -->
                    <div class="form-group mb-3">
                        <label for="is_type" class="font-weight-bold text-dark">{{ __('Homepage Section Category') }}</label>
                        <select name="is_type" id="is_type" class="form-control">
                            <option value="undefine" {{ $item->is_type == 'undefine' ? 'selected' : '' }}>{{ __('Undefine / Default') }}</option>
                            <option value="new" {{ $item->is_type == 'new' ? 'selected' : '' }}>{{ __('New Arrival') }}</option>
                            <option value="feature" {{ $item->is_type == 'feature' ? 'selected' : '' }}>{{ __('Feature Product') }}</option>
                            <option value="top" {{ $item->is_type == 'top' ? 'selected' : '' }}>{{ __('Top Product') }}</option>
                            <option value="best" {{ $item->is_type == 'best' ? 'selected' : '' }}>{{ __('Best Product') }}</option>
                            <option value="flash_deal" {{ $item->is_type == 'flash_deal' ? 'selected' : '' }}>{{ __('Flash Deal Product') }}</option>
                        </select>
                    </div>

                    <div class="form-group show-datepicker {{ $item->is_type == 'flash_deal' ? '' : 'd-none' }} mb-4">
                        <label for="datepicker" class="font-weight-bold text-dark">{{ __('Flash Deal Date') }} *</label>
                        <input type="text" name="date" class="form-control datepicker" id="datepicker"
                            placeholder="{{ __('Enter Date') }}" value="{{ $item->date }}">
                    </div>

                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-secondary btn-lg px-4 font-weight-bold shadow-sm">
                            <i class="fas fa-save mr-1"></i> {{ __('Save Changes') }}
                        </button>
                        <a href="{{ route('back.item.index') }}" class="btn btn-light btn-lg px-3 ml-2 border">
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
<script src="{{asset('assets/back/js/bootstrap-datepicker.js')}}"></script>
<script>
$(document).ready(function() {
    $('.datepicker').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true
    });

    // Handle is_type Flash Deal toggle
    $('#is_type').on('change', function() {
        if ($(this).val() == 'flash_deal') {
            $('.show-datepicker').removeClass('d-none');
        } else {
            $('.show-datepicker').addClass('d-none');
        }
    });

    // Toggle Days input
    function toggleDaysInput() {
        if ($('#mode_days').is(':checked')) {
            $('#days-input-wrapper').slideDown(150);
            $('#promotion_days').focus();
        } else {
            $('#days-input-wrapper').slideUp(150);
        }
    }

    $(document).on('change click', 'input[name="promotion_duration_mode"], .form-check-label', function() {
        setTimeout(toggleDaysInput, 50);
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

    toggleDaysInput();
});
</script>
@endsection
