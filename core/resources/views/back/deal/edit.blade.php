@extends('master.back')

@section('styles')
    <style>
        .product-select-card {
            max-height: 380px;
            overflow-y: auto;
            border: 1px solid #e3e6f0;
            border-radius: 6px;
            padding: 10px;
            background: #fafbfe;
        }
        .product-checkbox-item {
            display: flex;
            align-items: center;
            padding: 8px 12px;
            border-bottom: 1px solid #edf2f9;
            background: #fff;
            border-radius: 4px;
            margin-bottom: 6px;
            transition: all 0.2s;
            cursor: pointer;
        }
        .product-checkbox-item:hover {
            background: #f1f5f9;
        }
        .product-checkbox-item input[type="checkbox"] {
            width: 18px;
            height: 18px;
            margin-right: 12px;
            cursor: pointer;
        }
        .product-thumb-sm {
            width: 45px;
            height: 45px;
            object-fit: cover;
            border-radius: 4px;
            margin-right: 12px;
            border: 1px solid #e2e8f0;
        }
        .deal-summary-box {
            background: #f8fafc;
            border: 2px dashed #cbd5e1;
            border-radius: 8px;
            padding: 18px;
        }
    </style>
@endsection

@section('content')

<div class="container-fluid">

	<!-- Page Heading -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between">
                <h3 class="mb-0 bc-title"><b>{{ __('Edit Deal') }}</b></h3>
                <a class="btn btn-primary btn-sm" href="{{ route('back.deal.index') }}"><i class="fas fa-chevron-left"></i> {{ __('Back to Deals') }}</a>
            </div>
        </div>
    </div>

	<!-- Form Card -->
	<div class="card shadow mb-4">
		<div class="card-body">
            @include('alerts.alerts')

            <form action="{{ route('back.deal.update', $deal->id) }}" method="POST" id="deal-form" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <!-- Left Column: Deal Details & Product Selection -->
                    <div class="col-lg-7">
                        <div class="form-group">
                            <label for="name">{{ __('Deal Name') }} *</label>
                            <input type="text" required class="form-control" name="name" id="name" value="{{ old('name', $deal->name) }}" placeholder="{{ __('e.g. Mega Summer Bundle Deal') }}">
                        </div>

                        <div class="form-group">
                            <label for="description">{{ __('Deal Description') }}</label>
                            <textarea class="form-control" name="description" id="description" rows="3" placeholder="{{ __('Describe what makes this deal special...') }}">{{ old('description', $deal->description) }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="photo">{{ __('Bundle Image') }}</label>
                            @if($deal->photo)
                                <div class="mb-2">
                                    @php
                                        $photoUrl = \Illuminate\Support\Str::startsWith($deal->photo, 'images/')
                                            ? url('/core/public/storage/' . $deal->photo)
                                            : url('/core/public/storage/images/' . $deal->photo);
                                    @endphp
                                    <img src="{{ $photoUrl }}" style="height:80px;object-fit:cover;border-radius:4px;" alt="bundle">
                                    <small class="d-block text-muted mt-1">{{ __('Current image. Upload new to replace.') }}</small>
                                </div>
                            @endif
                            <input type="file" class="form-control-file" name="photo" id="photo" accept="image/*">
                        </div>

                        <div class="form-group">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="mb-0"><strong>{{ __('Select Products for this Deal') }} *</strong></label>
                                <small class="text-muted"><span id="selected-count">0</span> {{ __('products selected') }} <span id="min-products-warning" class="text-danger font-weight-bold" style="display:none">— {{ __('min. 2 required') }}</span></small>
                            </div>

                            <input type="text" id="product-search-input" class="form-control form-control-sm mb-2" placeholder="{{ __('Search products by name or SKU...') }}">

                            <div class="product-select-card" id="product-list-container">
                                @forelse ($items as $item)
                                    @php
                                        $price = $item->discount_price > 0 ? $item->discount_price : $item->previous_price;
                                        $isChecked = in_array($item->id, old('item_ids', $selectedItemIds));
                                        $thumb = $item->photo ?: $item->thumbnail;
                                        $itemImg = \Illuminate\Support\Str::startsWith($thumb, 'images/')
                                            ? url('/core/public/storage/' . $thumb)
                                            : url('/core/public/storage/images/' . $thumb);
                                    @endphp
                                    <label class="product-checkbox-item" data-name="{{ strtolower($item->name) }}" data-sku="{{ strtolower($item->sku ?? '') }}">
                                        <input type="checkbox" name="item_ids[]" value="{{ $item->id }}" data-price="{{ $price }}" data-name="{{ $item->name }}" class="deal-product-checkbox" {{ $isChecked ? 'checked' : '' }}>
                                        <img src="{{ $itemImg }}" class="product-thumb-sm" alt="{{ $item->name }}" onerror="this.src='{{ asset('assets/images/placeholder.png') }}'">
                                        <div class="flex-grow-1">
                                            <div class="font-weight-bold text-dark" style="font-size: 13.5px;">{{ Str::limit($item->name, 50) }}</div>
                                            <small class="text-muted">SKU: {{ $item->sku ?? 'N/A' }} | Price: <strong class="text-primary">{{ PriceHelper::setCurrencyPrice($price) }}</strong></small>
                                        </div>
                                    </label>
                                @empty
                                    <div class="p-3 text-center text-muted">
                                        {{ __('No products available.') }}
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Pricing, Duration & Summary -->
                    <div class="col-lg-5">
                        <div class="card border-primary mb-4">
                            <div class="card-header bg-primary text-white py-2">
                                <h6 class="mb-0 font-weight-bold"><i class="fas fa-tags"></i> {{ __('Discount & Duration') }}</h6>
                            </div>
                            <div class="card-body">

                                <div class="form-group">
                                    <label for="discount_type">{{ __('Discount Type') }} *</label>
                                    <select name="discount_type" id="discount_type" class="form-control" required>
                                        <option value="percent" {{ old('discount_type', $deal->discount_type) == 'percent' ? 'selected' : '' }}>{{ __('Percentage Discount (%)') }}</option>
                                        <option value="fixed" {{ old('discount_type', $deal->discount_type) == 'fixed' ? 'selected' : '' }}>{{ __('Fixed Amount Off (PKR)') }}</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="discount_value" id="discount_value_label">{{ __('Discount Percentage (%)') }} *</label>
                                    <input type="number" step="0.01" min="0.01" class="form-control" name="discount_value" id="discount_value" value="{{ old('discount_value', $deal->discount_type === 'fixed' ? PriceHelper::setPrice($deal->discount_value) : $deal->discount_value) }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="duration_days">{{ __('Deal Duration (Days)') }} * <span class="badge badge-info">{{ __('Max 20 Days') }}</span></label>
                                    <input type="number" min="1" max="20" class="form-control" name="duration_days" id="duration_days" value="{{ old('duration_days', $deal->duration_days) }}" required>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-clock text-warning"></i> {{ __('Choose how many days this deal should remain active.') }}
                                    </small>
                                </div>

                                <div class="form-group mb-2">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" name="is_free_delivery" value="1" id="is_free_delivery" {{ old('is_free_delivery', $deal->is_free_delivery) ? 'checked' : '' }}>
                                        <label class="custom-control-label font-weight-bold" for="is_free_delivery">{{ __('Free Delivery') }}</label>
                                    </div>
                                </div>
                                <div class="form-group" id="delivery-charge-group">
                                    <label for="delivery_charge">{{ __('Delivery Charges (PKR)') }}</label>
                                    <input type="number" min="0" step="0.01" class="form-control" name="delivery_charge" id="delivery_charge" value="{{ old('delivery_charge', PriceHelper::setPrice($deal->delivery_charge)) }}">
                                </div>

                            </div>
                        </div>

                        <!-- Real-time Deal Calculation Summary Box -->
                        <div class="deal-summary-box mb-4">
                            <h6 class="font-weight-bold text-dark mb-3"><i class="fas fa-calculator text-primary"></i> {{ __('Deal Pricing Summary') }}</h6>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">{{ __('Total Original Price:') }}</span>
                                <strong id="summary-original-price">PKR 0.00</strong>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">{{ __('Discount Applied:') }}</span>
                                <span class="badge badge-warning text-dark font-weight-bold" id="summary-discount-badge">0% OFF</span>
                            </div>

                            <div class="d-flex justify-content-between mb-2 text-danger">
                                <span>{{ __('Buyer Save:') }}</span>
                                <strong id="summary-savings">PKR 0.00</strong>
                            </div>

                            <hr>

                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="font-weight-bold text-dark" style="font-size: 16px;">{{ __('Final Deal Price:') }}</span>
                                <span class="font-weight-bold text-success" style="font-size: 20px;" id="summary-final-price">PKR 0.00</span>
                            </div>

                            <div class="d-flex justify-content-between mb-2 text-secondary" id="summary-delivery-row" style="display:none!important">
                                <span>{{ __('Delivery Charges:') }}</span>
                                <strong id="summary-delivery">PKR 0.00</strong>
                            </div>

                            <div class="d-flex justify-content-between align-items-center" id="summary-total-row" style="display:none!important">
                                <span class="font-weight-bold text-dark" style="font-size: 16px;">{{ __('Final Payment (with delivery):') }}</span>
                                <span class="font-weight-bold text-primary" style="font-size: 20px;" id="summary-total-with-delivery">PKR 0.00</span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block btn-lg font-weight-bold shadow">
                            <i class="fas fa-save"></i> {{ __('Update Deal') }}
                        </button>
                    </div>
                </div>

            </form>
		</div>
	</div>

</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    const dealCurrencyValue = {{ json_encode(PriceHelper::setCurrencyValue()) }};
    const dealCurrencySign = @json(PriceHelper::setCurrencySign());
    function formatDealPrice(basePrice) {
        return dealCurrencySign + ' ' + (basePrice * dealCurrencyValue).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }
    function calculateDeal() {
        let totalOriginal = 0;
        let count = 0;

        $('.deal-product-checkbox:checked').each(function() {
            totalOriginal += parseFloat($(this).data('price')) || 0;
            count++;
        });

        $('#selected-count').text(count);
        if (count < 2) {
            $('#min-products-warning').show();
        } else {
            $('#min-products-warning').hide();
        }

        let discountType = $('#discount_type').val();
        let discountValue = parseFloat($('#discount_value').val()) || 0;
        let savings = 0;
        let finalPrice = 0;
        let badgeText = '';

        if (discountType === 'percent') {
            savings = (totalOriginal * discountValue) / 100;
            badgeText = discountValue + '% OFF';
        } else {
            savings = discountValue / dealCurrencyValue;
            badgeText = '-' + formatDealPrice(savings) + ' OFF';
        }

        savings = Math.min(savings, totalOriginal);
        finalPrice = Math.max(0, totalOriginal - savings);

        let isFreeDelivery = $('#is_free_delivery').is(':checked');
        let deliveryCharge = isFreeDelivery ? 0 : (parseFloat($('#delivery_charge').val()) || 0) / dealCurrencyValue;
        let totalWithDelivery = finalPrice + deliveryCharge;

        $('#summary-original-price').text(formatDealPrice(totalOriginal));
        $('#summary-discount-badge').text(badgeText);
        $('#summary-savings').text(formatDealPrice(savings));
        $('#summary-final-price').text(formatDealPrice(finalPrice));

        if (!isFreeDelivery && deliveryCharge > 0) {
            $('#summary-delivery').text(formatDealPrice(deliveryCharge));
            $('#summary-delivery-row').css('display', 'flex');
            $('#summary-total-with-delivery').text(formatDealPrice(totalWithDelivery));
            $('#summary-total-row').css('display', 'flex');
        } else {
            $('#summary-delivery-row').css('display', 'none');
            $('#summary-total-row').css('display', 'none');
        }
    }

    $('#discount_type').on('change', function() {
        if ($(this).val() === 'percent') {
            $('#discount_value_label').text('{{ __("Discount Percentage (%)") }} *');
            $('#discount_value').attr('placeholder', 'e.g. 20').attr('max', '99');
        } else {
            $('#discount_value_label').text('{{ __("Fixed Amount Off (PKR)") }} *');
            $('#discount_value').attr('placeholder', 'e.g. 500').removeAttr('max');
        }
        calculateDeal();
    });

    $(document).on('change', '.deal-product-checkbox', calculateDeal);
    $('#discount_value').on('input', calculateDeal);
    $('#delivery_charge').on('input', calculateDeal);

    $('#is_free_delivery').on('change', function() {
        $('#delivery-charge-group').toggle(!this.checked);
        $('#delivery_charge').prop('disabled', this.checked);
        calculateDeal();
    }).trigger('change');

    $('#product-search-input').on('keyup', function() {
        let query = $(this).val().toLowerCase();
        $('.product-checkbox-item').each(function() {
            let name = $(this).data('name');
            let sku = $(this).data('sku');
            if (name.indexOf(query) > -1 || sku.indexOf(query) > -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    calculateDeal();
});
</script>
@endsection
