<div class="{{ $column ?? 'col-6 col-md-4 col-lg-3 mb-3' }}">
    @php
        $dealEndIso = '';
        if (!empty($deal->end_date)) {
            $dealEndIso = ($deal->end_date instanceof \Carbon\Carbon)
                ? $deal->end_date->toIso8601String()
                : \Carbon\Carbon::parse($deal->end_date)->toIso8601String();
        }
    @endphp
    <article class="card h-100 border-0 shadow-sm deal-card {{ $deal->isPromotionActive() ? 'promoted-card' : '' }}" data-deal-end="{{ $dealEndIso }}">
        @if($deal->isPromotionActive())
            <div class="promoted-tag-badge">
                <i class="fas fa-crown mr-1"></i> {{ $deal->promotion_tag }}
            </div>
        @endif
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center py-1 px-1 px-sm-2" style="white-space: nowrap; overflow: hidden; min-height: 26px; gap: 4px;">
            <small class="font-weight-bold d-inline-flex align-items-center text-nowrap" style="font-size: 9.5px; line-height: 1;"><i class="icon-clock" style="font-size: 9px; margin-right: 5px;"></i><span class="deal-countdown">--</span></small>
            <span class="badge badge-warning text-dark ml-1 font-weight-bold text-nowrap flex-shrink-0" style="font-size: 8.5px; padding: 2px 4px; line-height: 1;">{{ $deal->discount_badge }}</span>
        </div>
        @php
            $dealUrl = route('front.deal.details', !empty($deal->sku) ? $deal->sku : $deal->slug);
        @endphp
        <a href="{{ $dealUrl }}" class="deal-thumb d-block bg-white" style="overflow: hidden;">
            @if($deal->photo)
                @php
                    $cardImg = \Illuminate\Support\Str::startsWith($deal->photo, 'images/')
                        ? url('/core/public/storage/' . $deal->photo)
                        : url('/core/public/storage/images/' . $deal->photo);
                @endphp
                <img class="card-img-top lazy" src="{{ $cardImg }}" data-src="{{ $cardImg }}" alt="{{ $deal->name }}" loading="lazy" decoding="async">
            @else
                @php
                    $firstItem = $deal->dealItems->first()->item ?? null;
                @endphp
                @if($firstItem)
                    @php
                        $firstThumb = $firstItem->photo ?: $firstItem->thumbnail;
                        $firstImg = \Illuminate\Support\Str::startsWith($firstThumb, 'images/')
                            ? url('/core/public/storage/' . $firstThumb)
                            : url('/core/public/storage/images/' . $firstThumb);
                    @endphp
                    <img class="card-img-top lazy" src="{{ $firstImg }}" data-src="{{ $firstImg }}" alt="{{ $deal->name }}" loading="lazy" decoding="async">
                @endif
            @endif
        </a>
        <div class="card-body d-flex flex-column p-2 p-md-3">
            <h3 class="h6 font-weight-bold deal-title mb-1" style="font-size: 12.5px; line-height: 1.3;"><a href="{{ $dealUrl }}" class="text-dark">{{ Str::limit($deal->name, 75) }}</a></h3>
            <div class="mt-auto">
                <div class="d-flex justify-content-between align-items-center mb-1 text-nowrap">
                    <del class="text-muted small" style="font-size: 10.5px;">{{ PriceHelper::setCurrencyPrice($deal->original_price) }}</del>
                    @php
                        $savedPrice = (float)$deal->original_price - (float)$deal->discounted_price;
                    @endphp
                    @if($savedPrice > 0)
                        <span class="font-weight-bold text-nowrap deal-save-badge">
                            {{ __('Save') }} <span class="d-none d-md-inline">{{ PriceHelper::setCurrencyPrice($savedPrice) }}</span><span class="d-inline d-md-none">{{ PriceHelper::setIntegerCurrencyPrice($savedPrice) }}</span>
                        </span>
                    @endif
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2 text-nowrap" style="gap:2px;">
                    <strong class="text-success h5 deal-price mb-0 text-nowrap font-weight-bold" style="font-size: 13.5px; line-height: 1.2;">{{ PriceHelper::setCurrencyPrice($deal->discounted_price) }}</strong>
                    @if($deal->is_free_delivery)
                        <span class="badge badge-success text-nowrap font-weight-bold" style="font-size:9px; padding: 2px 4px;"><i class="fas fa-truck mr-1"></i><span class="d-none d-sm-inline">{{ __('Free Delivery') }}</span><span class="d-inline d-sm-none">{{ __('Free') }}</span></span>
                    @elseif($deal->delivery_charge > 0)
                        <span class="badge badge-light border text-dark text-nowrap" style="font-size:9px; padding: 2px 4px;"><i class="fas fa-truck mr-1"></i>{{ PriceHelper::setCurrencyPrice($deal->delivery_charge) }}</span>
                    @endif
                </div>
                <a href="{{ $dealUrl }}" class="btn btn-outline-primary btn-sm btn-block mb-1 py-1" style="font-size: 11px; line-height: 1.2;">{{ __('View Bundle') }}</a>
                <div class="d-flex" style="gap:4px;">
                    <form action="{{ route('front.deal.add_to_cart', !empty($deal->sku) ? $deal->sku : $deal->slug) }}" method="post" class="flex-grow-1 mb-0">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-sm btn-block text-nowrap px-1 py-1" title="{{ __('Add Bundle to Cart') }}" style="font-size: 10.5px; line-height: 1.2;"><i class="icon-shopping-cart"></i> <span class="d-none d-sm-inline">{{ __('Add to ') }}</span>{{ __('Cart') }}</button>
                    </form>
                    <form action="{{ route('front.deal.add_to_cart', !empty($deal->sku) ? $deal->sku : $deal->slug) }}" method="post" class="flex-grow-1 mb-0">
                        @csrf
                        <input type="hidden" name="buy_now" value="1">
                        <button type="submit" class="btn btn-success btn-sm btn-block text-nowrap px-1 py-1" title="{{ __('Buy Bundle Now') }}" style="font-size: 10.5px; line-height: 1.2;"><i class="fas fa-bolt"></i> {{ __('Buy') }}<span class="d-none d-sm-inline">{{ __(' Now') }}</span></button>
                    </form>
                </div>
            </div>
        </div>
    </article>
</div>
