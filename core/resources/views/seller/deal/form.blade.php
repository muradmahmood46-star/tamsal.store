@extends('master.seller')

@section('content')
<div class="container-fluid">
    <div class="card mb-4"><div class="card-body d-flex justify-content-between"><h3 class="mb-0 bc-title"><b>{{ $deal ? __('Edit Deal') : __('Create Deal') }}</b></h3><a class="btn btn-primary btn-sm" href="{{ route('seller.deal.index') }}">{{ __('Back to Deals') }}</a></div></div>
    <div class="card shadow"><div class="card-body">
        @include('alerts.alerts')
        <form method="post" action="{{ $deal ? route('seller.deal.update', $deal->id) : route('seller.deal.store') }}">
            @csrf @if($deal) @method('PUT') @endif
            <div class="row"><div class="col-lg-7">
                <div class="form-group"><label>{{ __('Deal Name') }} *</label><input class="form-control" name="name" required value="{{ old('name', optional($deal)->name) }}"></div>
                <div class="form-group"><label>{{ __('Deal Description') }}</label><textarea class="form-control" name="description" rows="3">{{ old('description', optional($deal)->description) }}</textarea></div>
                <div class="form-group"><label><strong>{{ __('Select Products From Your Store') }} *</strong></label><div class="border rounded p-2" style="max-height:420px;overflow:auto">
                    @forelse($items as $item)
                        @php($price = $item->discount_price > 0 ? $item->discount_price : $item->previous_price)
                        <label class="d-flex align-items-center border-bottom py-2 mb-0"><input class="deal-product-checkbox mr-2" type="checkbox" name="item_ids[]" value="{{ $item->id }}" data-price="{{ $price }}" {{ in_array($item->id, old('item_ids', $selectedItemIds ?? [])) ? 'checked' : '' }}><span class="flex-grow-1">{{ $item->name }} <small class="text-muted">({{ PriceHelper::setCurrencyPrice($price) }})</small></span></label>
                    @empty <p class="text-muted mb-0">{{ __('No eligible products available.') }}</p>
                    @endforelse
                </div></div>
            </div><div class="col-lg-5">
                <div class="form-group"><label>{{ __('Discount Type') }} *</label><select class="form-control" id="discount_type" name="discount_type"><option value="percent" {{ old('discount_type', optional($deal)->discount_type) !== 'fixed' ? 'selected' : '' }}>{{ __('Percentage (%)') }}</option><option value="fixed" {{ old('discount_type', optional($deal)->discount_type) === 'fixed' ? 'selected' : '' }}>{{ __('Fixed Amount Off (PKR)') }}</option></select></div>
                <div class="form-group"><label>{{ __('Discount Value') }} *</label><input class="form-control" type="number" min="0.01" step="0.01" id="discount_value" name="discount_value" required value="{{ old('discount_value', $deal && $deal->discount_type === 'fixed' ? PriceHelper::setPrice($deal->discount_value) : (optional($deal)->discount_value ?: 10)) }}"></div>
                <div class="form-group"><label>{{ __('Deal Duration (Days)') }} * <span class="badge badge-info">{{ __('Max 20 Days') }}</span></label><input class="form-control" type="number" min="1" max="20" name="duration_days" required value="{{ old('duration_days', optional($deal)->duration_days ?: 7) }}"><small class="form-text text-muted">{{ __('Choose how many days this deal should remain active.') }}</small></div>
                <div class="form-group mb-2"><div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" name="is_free_delivery" value="1" id="is_free_delivery" {{ old('is_free_delivery', optional($deal)->is_free_delivery) ? 'checked' : '' }}><label class="custom-control-label font-weight-bold" for="is_free_delivery">{{ __('Free Delivery') }}</label></div></div>
                <div class="form-group" id="delivery-charge-group"><label>{{ __('Delivery Charges (PKR)') }}</label><input class="form-control" type="number" min="0" step="0.01" id="delivery_charge" name="delivery_charge" value="{{ old('delivery_charge', $deal ? PriceHelper::setPrice($deal->delivery_charge) : 0) }}"></div>
                <div class="alert alert-light border"><div>{{ __('Selected total:') }} <strong id="deal-total">PKR 0.00</strong></div><div>{{ __('Buyer save:') }} <strong class="text-danger" id="deal-savings">PKR 0.00</strong></div><div>{{ __('Final deal price:') }} <strong class="text-success" id="deal-final">PKR 0.00</strong></div><div id="vendor-delivery-row" style="display:none">{{ __('Delivery Charges:') }} <strong class="text-secondary" id="deal-delivery">PKR 0.00</strong></div><div id="vendor-total-row" style="display:none">{{ __('Final Payment (with delivery):') }} <strong class="text-primary" id="deal-total-delivery">PKR 0.00</strong></div></div>
                <button class="btn btn-success btn-block btn-lg"><i class="fas fa-bolt"></i> {{ $deal ? __('Apply Deal') : __('Launch Deal') }}</button>
            </div></div>
        </form>
    </div></div>
</div>
@endsection

@section('scripts')
<script>
var dealCurrencyValue = {{ json_encode(PriceHelper::setCurrencyValue()) }}, dealCurrencySign = @json(PriceHelper::setCurrencySign());
function dealMoney(value) { return dealCurrencySign + ' ' + (value * dealCurrencyValue).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}); }
function updateDealTotal() { var total = 0; document.querySelectorAll('.deal-product-checkbox:checked').forEach(function(input) { total += parseFloat(input.dataset.price) || 0; }); var discount = parseFloat(document.getElementById('discount_value').value) || 0; var saving = document.getElementById('discount_type').value === 'percent' ? total * discount / 100 : discount / dealCurrencyValue; saving = Math.min(saving, total); var finalPrice = Math.max(0, total - saving); var isFree = document.getElementById('is_free_delivery').checked; var deliveryInput = parseFloat(document.getElementById('delivery_charge').value) || 0; var deliveryCharge = isFree ? 0 : deliveryInput / dealCurrencyValue; document.getElementById('deal-total').textContent = dealMoney(total); document.getElementById('deal-savings').textContent = dealMoney(saving); document.getElementById('deal-final').textContent = dealMoney(finalPrice); if (!isFree && deliveryCharge > 0) { document.getElementById('deal-delivery').textContent = dealMoney(deliveryCharge); document.getElementById('vendor-delivery-row').style.display = ''; document.getElementById('deal-total-delivery').textContent = dealMoney(finalPrice + deliveryCharge); document.getElementById('vendor-total-row').style.display = ''; } else { document.getElementById('vendor-delivery-row').style.display = 'none'; document.getElementById('vendor-total-row').style.display = 'none'; } }
document.querySelectorAll('.deal-product-checkbox,#discount_type,#discount_value,#delivery_charge').forEach(function(input) { input.addEventListener('change', updateDealTotal); input.addEventListener('input', updateDealTotal); }); document.getElementById('is_free_delivery').addEventListener('change', function() { document.getElementById('delivery-charge-group').style.display = this.checked ? 'none' : ''; document.getElementById('delivery_charge').disabled = this.checked; updateDealTotal(); }); document.getElementById('is_free_delivery').dispatchEvent(new Event('change')); updateDealTotal();
</script>
@endsection
