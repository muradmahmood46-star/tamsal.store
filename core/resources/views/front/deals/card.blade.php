<div class="{{ $column ?? 'col-6 col-md-4 col-lg-3 mb-3' }}">
    @php
        $dealEndIso = '';
        if (!empty($deal->end_date)) {
            $dealEndIso = ($deal->end_date instanceof \Carbon\Carbon)
                ? $deal->end_date->toIso8601String()
                : \Carbon\Carbon::parse($deal->end_date)->toIso8601String();
        }
    @endphp
    <article class="card h-100 border-0 shadow-sm deal-card" data-deal-end="{{ $dealEndIso }}">
        <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center py-1 px-2">
            <small class="font-weight-bold" style="font-size: 11px;"><i class="icon-clock"></i> <span class="deal-countdown">--</span></small>
            <span class="badge badge-warning text-dark" style="font-size: 10px;">{{ $deal->discount_badge }}</span>
        </div>
        <a href="{{ route('front.deal.details', $deal->slug) }}" class="d-flex align-items-center justify-content-center bg-white" style="overflow: hidden;">
            @if($deal->photo)
                @php
                    $cardImg = \Illuminate\Support\Str::startsWith($deal->photo, 'images/')
                        ? url('/core/public/storage/' . $deal->photo)
                        : url('/core/public/storage/images/' . $deal->photo);
                @endphp
                <img class="card-img-top p-2" style="height:140px;object-fit:contain" src="{{ $cardImg }}" alt="{{ $deal->name }}">
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
                    <img class="card-img-top p-2" style="height:140px;object-fit:contain" src="{{ $firstImg }}" alt="{{ $deal->name }}">
                @endif
            @endif
        </a>
        <div class="card-body d-flex flex-column p-2 p-md-3">
            <h3 class="h6 font-weight-bold deal-title mb-1" style="font-size: 13px; line-height: 1.3;"><a href="{{ route('front.deal.details', $deal->slug) }}" class="text-dark">{{ Str::limit($deal->name, 35) }}</a></h3>
            <div class="mt-auto">
                <del class="text-muted small">{{ PriceHelper::setCurrencyPrice($deal->original_price) }}</del>
                <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap" style="gap:2px;">
                    <strong class="text-success h5 deal-price mb-0" style="font-size: 14px;">{{ PriceHelper::setCurrencyPrice($deal->discounted_price) }}</strong>
                    @if($deal->is_free_delivery)
                        <span class="badge badge-success" style="font-size:10px;"><i class="fas fa-truck"></i> {{ __('Free Delivery') }}</span>
                    @elseif($deal->delivery_charge > 0)
                        <span class="badge badge-light border text-dark" style="font-size:10px;"><i class="fas fa-truck"></i> {{ PriceHelper::setCurrencyPrice($deal->delivery_charge) }}</span>
                    @endif
                </div>
                <a href="{{ route('front.deal.details', $deal->slug) }}" class="btn btn-outline-primary btn-sm btn-block mb-1 py-1" style="font-size: 11.5px;">{{ __('View Bundle') }}</a>
                <div class="d-flex" style="gap:4px;">
                    <form action="{{ route('front.deal.add_to_cart', $deal->slug) }}" method="post" class="flex-grow-1 mb-0">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-sm btn-block text-nowrap px-1 py-1" title="{{ __('Add Bundle to Cart') }}" style="font-size: 11px;"><i class="icon-shopping-cart"></i> <span class="d-none d-sm-inline">{{ __('Add to ') }}</span>{{ __('Cart') }}</button>
                    </form>
                    <form action="{{ route('front.deal.add_to_cart', $deal->slug) }}" method="post" class="flex-grow-1 mb-0">
                        @csrf
                        <input type="hidden" name="buy_now" value="1">
                        <button type="submit" class="btn btn-success btn-sm btn-block text-nowrap px-1 py-1" title="{{ __('Buy Bundle Now') }}" style="font-size: 11px;"><i class="fas fa-bolt"></i> {{ __('Buy') }}<span class="d-none d-sm-inline">{{ __(' Now') }}</span></button>
                    </form>
                </div>
            </div>
        </div>
    </article>
</div>
