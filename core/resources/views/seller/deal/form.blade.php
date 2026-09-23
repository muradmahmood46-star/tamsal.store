@extends('master.seller')

@section('content')
<div class="container-fluid">
    <div class="card mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">
            <h3 class="mb-0 bc-title"><b>{{ (isset($deal) && $deal) ? __('Edit Bundle') : __('Create Bundle') }}</b></h3>
            <a class="btn btn-primary btn-sm" href="{{ route('seller.deal.index') }}"><i class="fas fa-chevron-left"></i> {{ __('Back to Deals') }}</a>
        </div>
    </div>
    <div class="card shadow">
        <div class="card-body">
            @include('alerts.alerts')
            <form method="post" action="{{ (isset($deal) && $deal) ? route('seller.deal.update', $deal->id) : route('seller.deal.store') }}" enctype="multipart/form-data">
                @csrf
                @if(isset($deal) && $deal) @method('PUT') @endif

                <div class="row">
                    <div class="col-lg-7">
                        <div class="form-group">
                            <label>{{ __('Bundle Name') }} *</label>
                            <input class="form-control" name="name" required value="{{ old('name', (isset($deal) && $deal) ? $deal->name : '') }}" placeholder="{{ __('e.g. Mega Summer Bundle Deal') }}">
                        </div>
                        <div class="form-group">
                            <label>{{ __('Bundle Description') }}</label>
                            <textarea class="form-control" name="description" rows="3" placeholder="{{ __('Describe what makes this deal special...') }}">{{ old('description', (isset($deal) && $deal) ? $deal->description : '') }}</textarea>
                        </div>
                        <div class="form-group">
                            <label>{{ __('Bundle Image') }}</label>
                            @if(isset($deal) && $deal && $deal->photo)
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
                            <small class="text-muted">{{ __('Optional. This image will appear on the bundle card.') }}</small>
                        </div>
                        <div class="form-group">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="mb-0"><strong>{{ __('Select Products From Your Store') }} *</strong></label>
                                <small class="text-muted"><span id="selected-count">0</span> {{ __('products') }} (<span id="total-qty-count">0</span> {{ __('items') }}) {{ __('selected') }} <span id="min-products-warning" class="text-danger font-weight-bold" style="display:none">— {{ __('(min. 2 items required)') }}</span></small>
                            </div>
                            <input type="text" id="seller-product-search" class="form-control form-control-sm mb-2" placeholder="{{ __('Search products by name or SKU...') }}">
                            <div class="border rounded p-2" id="seller-products-list" style="max-height:380px;overflow-y:auto;background:#fafbfe;">
                                @forelse($items as $item)
                                    @php
                                        $price = $item->discount_price > 0 ? $item->discount_price : $item->previous_price;
                                        $isChecked = in_array($item->id, old('item_ids', $selectedItemIds ?? []));
                                        $itemQty = old('item_quantities.' . $item->id, $selectedQuantities[$item->id] ?? 1);
                                        $thumb = $item->photo ?: $item->thumbnail;
                                        $itemImg = \Illuminate\Support\Str::startsWith($thumb, 'images/')
                                            ? url('/core/public/storage/' . $thumb)
                                            : url('/core/public/storage/images/' . $thumb);
                                    @endphp
                                    <div class="d-flex align-items-center justify-content-between border-bottom py-2 mb-1 seller-product-item flex-wrap" data-name="{{ strtolower($item->name) }}" data-sku="{{ strtolower($item->sku ?? '') }}" style="background:#fff;padding:8px 12px;border-radius:4px;">
                                        <div class="d-flex align-items-center flex-grow-1 mr-2" style="cursor:pointer;" onclick="$(this).find('.deal-product-checkbox').trigger('click');">
                                            <input class="deal-product-checkbox mr-2" type="checkbox" name="item_ids[]" value="{{ $item->id }}" data-price="{{ $price }}" {{ $isChecked ? 'checked' : '' }} style="width:18px;height:18px;" onclick="event.stopPropagation();">
                                            <img src="{{ $itemImg }}" style="width:40px;height:40px;object-fit:cover;border-radius:4px;margin-right:10px;" onerror="this.src='{{ asset('assets/images/placeholder.png') }}'">
                                            <div>
                                                <div class="font-weight-bold text-dark" style="font-size:13px;">{{ Str::limit($item->name, 45) }}</div>
                                                <small class="text-muted">SKU: {{ $item->sku ?? 'N/A' }} | Price: <strong class="text-primary">{{ PriceHelper::setCurrencyPrice($price) }}</strong></small>
                                            </div>
                                        </div>
                                        <div class="deal-qty-container align-items-center {{ $isChecked ? 'd-flex' : 'd-none' }}" style="gap:4px;">
                                            <button type="button" class="btn btn-sm btn-outline-secondary deal-qty-btn deal-qty-minus" style="padding:1px 7px;font-weight:bold;height:28px;line-height:1;">-</button>
                                            <input type="number" name="item_quantities[{{ $item->id }}]" class="form-control form-control-sm text-center deal-qty-input" value="{{ $itemQty }}" min="1" max="99" style="width:48px;height:28px;padding:2px;font-weight:bold;" {{ $isChecked ? '' : 'disabled' }}>
                                            <button type="button" class="btn btn-sm btn-outline-secondary deal-qty-btn deal-qty-plus" style="padding:1px 7px;font-weight:bold;height:28px;line-height:1;">+</button>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-muted mb-0 p-3 text-center">{{ __('No eligible products available in your store.') }}</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="card border-primary mb-3">
                            <div class="card-header bg-primary text-white py-2">
                                <h6 class="mb-0 font-weight-bold"><i class="fas fa-tags"></i> {{ __('Discount & Duration') }}</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>{{ __('Discount Type') }} *</label>
                                    <select class="form-control" id="discount_type" name="discount_type">
                                        <option value="percent" {{ old('discount_type', (isset($deal) && $deal) ? $deal->discount_type : '') !== 'fixed' ? 'selected' : '' }}>{{ __('Percentage (%)') }}</option>
                                        <option value="fixed" {{ old('discount_type', (isset($deal) && $deal) ? $deal->discount_type : '') === 'fixed' ? 'selected' : '' }}>{{ __('Fixed Amount Off (PKR)') }}</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label id="discount_value_label">{{ __('Discount Percentage (%)') }} *</label>
                                    <input class="form-control" type="number" min="0.01" step="0.01" id="discount_value" name="discount_value" required value="{{ old('discount_value', (isset($deal) && $deal && $deal->discount_type === 'fixed') ? PriceHelper::setPrice($deal->discount_value) : ((isset($deal) && $deal && $deal->discount_value) ? $deal->discount_value : 10)) }}">
                                </div>
                                <div class="form-group">
                                    <label>{{ __('Deal Duration (Days)') }} * <span class="badge badge-info">{{ __('Max 20 Days') }}</span></label>
                                    <input class="form-control" type="number" min="1" max="20" name="duration_days" required value="{{ old('duration_days', (isset($deal) && $deal && $deal->duration_days) ? $deal->duration_days : 7) }}">
                                    <small class="form-text text-muted">{{ __('Choose how many days this deal should remain active.') }}</small>
                                </div>
                                <div class="form-group mb-2">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" name="is_free_delivery" value="1" id="is_free_delivery" {{ old('is_free_delivery', (isset($deal) && $deal) ? $deal->is_free_delivery : 0) ? 'checked' : '' }}>
                                        <label class="custom-control-label font-weight-bold" for="is_free_delivery">{{ __('Free Delivery') }}</label>
                                    </div>
                                </div>
                                <div class="form-group" id="delivery-charge-group">
                                    <label>{{ __('Delivery Charges (PKR)') }}</label>
                                    <input class="form-control" type="number" min="0" step="0.01" id="delivery_charge" name="delivery_charge" value="{{ old('delivery_charge', (isset($deal) && $deal) ? PriceHelper::setPrice($deal->delivery_charge) : 0) }}">
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-light border shadow-sm">
                            <h6 class="font-weight-bold text-dark mb-3"><i class="fas fa-calculator text-primary"></i> {{ __('Deal Pricing Summary') }}</h6>
                            <div class="d-flex justify-content-between mb-1"><span>{{ __('Selected total:') }}</span> <strong id="deal-total">PKR 0.00</strong></div>
                            <div class="d-flex justify-content-between mb-1"><span>{{ __('Buyer save:') }}</span> <strong class="text-danger" id="deal-savings">PKR 0.00</strong></div>
                            <div class="d-flex justify-content-between mb-2"><span>{{ __('Final deal price:') }}</span> <strong class="text-success" style="font-size:18px;" id="deal-final">PKR 0.00</strong></div>
                            <div class="d-flex justify-content-between mb-1" id="vendor-delivery-row" style="display:none"><span>{{ __('Delivery Charges:') }}</span> <strong class="text-secondary" id="deal-delivery">PKR 0.00</strong></div>
                            <div class="d-flex justify-content-between mb-1" id="vendor-total-row" style="display:none"><span>{{ __('Final Payment (with delivery):') }}</span> <strong class="text-primary" style="font-size:18px;" id="deal-total-delivery">PKR 0.00</strong></div>
                        </div>

                        <button type="submit" class="btn btn-success btn-block btn-lg font-weight-bold shadow"><i class="fas fa-save"></i> {{ (isset($deal) && $deal) ? __('Update Bundle') : __('Launch Bundle') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
var dealCurrencyValue = {{ json_encode(PriceHelper::setCurrencyValue()) }}, dealCurrencySign = @json(PriceHelper::setCurrencySign());
function dealMoney(value) { return dealCurrencySign + ' ' + (value * dealCurrencyValue).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}); }
function updateDealTotal() {
    var total = 0;
    var count = 0;
    var totalQty = 0;
    document.querySelectorAll('.deal-product-checkbox:checked').forEach(function(input) {
        var row = input.closest('.seller-product-item');
        var qtyInput = row ? row.querySelector('.deal-qty-input') : null;
        var qty = qtyInput ? (parseInt(qtyInput.value) || 1) : 1;
        if (qty < 1) qty = 1;
        var price = parseFloat(input.dataset.price) || 0;
        total += price * qty;
        count++;
        totalQty += qty;
    });
    var countElem = document.getElementById('selected-count');
    if (countElem) countElem.textContent = count;
    var totalQtyElem = document.getElementById('total-qty-count');
    if (totalQtyElem) totalQtyElem.textContent = totalQty;
    var warnElem = document.getElementById('min-products-warning');
    if (warnElem) warnElem.style.display = totalQty < 2 ? '' : 'none';

    var discountType = document.getElementById('discount_type').value;
    var discount = parseFloat(document.getElementById('discount_value').value) || 0;
    var saving = discountType === 'percent' ? (total * discount / 100) : (discount / dealCurrencyValue);
    saving = Math.min(saving, total);
    var finalPrice = Math.max(0, total - saving);
    var isFree = document.getElementById('is_free_delivery').checked;
    var deliveryInput = parseFloat(document.getElementById('delivery_charge').value) || 0;
    var deliveryCharge = isFree ? 0 : (deliveryInput / dealCurrencyValue);

    document.getElementById('deal-total').textContent = dealMoney(total);
    document.getElementById('deal-savings').textContent = dealMoney(saving);
    document.getElementById('deal-final').textContent = dealMoney(finalPrice);
    if (!isFree && deliveryCharge > 0) {
        document.getElementById('deal-delivery').textContent = dealMoney(deliveryCharge);
        document.getElementById('vendor-delivery-row').style.display = 'flex';
        document.getElementById('deal-total-delivery').textContent = dealMoney(finalPrice + deliveryCharge);
        document.getElementById('vendor-total-row').style.display = 'flex';
    } else {
        document.getElementById('vendor-delivery-row').style.display = 'none';
        document.getElementById('vendor-total-row').style.display = 'none';
    }
}

// Checkbox toggle handles qty input
$(document).on('change', '.deal-product-checkbox', function() {
    var row = $(this).closest('.seller-product-item');
    var qtyContainer = row.find('.deal-qty-container');
    var qtyInput = row.find('.deal-qty-input');
    if (this.checked) {
        qtyContainer.removeClass('d-none').addClass('d-flex');
        qtyInput.prop('disabled', false);
    } else {
        qtyContainer.removeClass('d-flex').addClass('d-none');
        qtyInput.prop('disabled', true);
    }
    updateDealTotal();
});

// Stepper plus/minus
$(document).on('click', '.deal-qty-plus', function(e) {
    e.stopPropagation();
    var input = $(this).siblings('.deal-qty-input');
    var val = parseInt(input.val()) || 1;
    input.val(val + 1).trigger('input');
});

$(document).on('click', '.deal-qty-minus', function(e) {
    e.stopPropagation();
    var input = $(this).siblings('.deal-qty-input');
    var val = parseInt(input.val()) || 1;
    if (val > 1) {
        input.val(val - 1).trigger('input');
    }
});

$(document).on('input change', '.deal-qty-input', function() {
    var val = parseInt($(this).val()) || 1;
    if (val < 1) $(this).val(1);
    updateDealTotal();
});

document.querySelectorAll('#discount_type,#discount_value,#delivery_charge').forEach(function(input) {
    input.addEventListener('change', updateDealTotal);
    input.addEventListener('input', updateDealTotal);
});

document.getElementById('discount_type').addEventListener('change', function() {
    var lbl = document.getElementById('discount_value_label');
    if (this.value === 'percent') {
        if (lbl) lbl.textContent = '{{ __("Discount Percentage (%)") }} *';
        document.getElementById('discount_value').setAttribute('max', '99');
    } else {
        if (lbl) lbl.textContent = '{{ __("Fixed Amount Off (PKR)") }} *';
        document.getElementById('discount_value').removeAttribute('max');
    }
    updateDealTotal();
});

document.getElementById('is_free_delivery').addEventListener('change', function() {
    document.getElementById('delivery-charge-group').style.display = this.checked ? 'none' : '';
    document.getElementById('delivery_charge').disabled = this.checked;
    updateDealTotal();
});

var searchInput = document.getElementById('seller-product-search');
if (searchInput) {
    searchInput.addEventListener('keyup', function() {
        var query = this.value.toLowerCase();
        document.querySelectorAll('.seller-product-item').forEach(function(el) {
            var name = el.getAttribute('data-name') || '';
            var sku = el.getAttribute('data-sku') || '';
            el.style.display = (name.indexOf(query) > -1 || sku.indexOf(query) > -1) ? '' : 'none';
        });
    });
}

document.getElementById('is_free_delivery').dispatchEvent(new Event('change'));
document.getElementById('discount_type').dispatchEvent(new Event('change'));
updateDealTotal();
</script>
@endsection

