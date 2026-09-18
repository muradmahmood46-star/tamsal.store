<div class="{{ $column ?? 'col-md-4 mb-4' }}">
    <article class="card h-100 border-0 shadow-sm deal-card" data-deal-end="{{ $deal->end_date->toIso8601String() }}">
        <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center py-2">
            <small class="font-weight-bold"><i class="icon-clock"></i> <span class="deal-countdown">--</span></small>
            <span class="badge badge-warning text-dark">{{ $deal->discount_badge }}</span>
        </div>
        <a href="{{ route('front.deal.details', $deal->slug) }}">
            @if($deal->photo)
                @php
                    $cardImg = \Illuminate\Support\Str::startsWith($deal->photo, 'images/')
                        ? url('/core/public/storage/' . $deal->photo)
                        : url('/core/public/storage/images/' . $deal->photo);
                @endphp
                <img class="card-img-top p-3" style="height:160px;object-fit:contain" src="{{ $cardImg }}" alt="{{ $deal->name }}">
            @else
                @php($firstItem = $deal->dealItems->first()->item ?? null)
                @if($firstItem)
                    @php
                        $firstThumb = $firstItem->photo ?: $firstItem->thumbnail;
                        $firstImg = \Illuminate\Support\Str::startsWith($firstThumb, 'images/')
                            ? url('/core/public/storage/' . $firstThumb)
                            : url('/core/public/storage/images/' . $firstThumb);
                    @endphp
                    <img class="card-img-top p-3" style="height:160px;object-fit:contain" src="{{ $firstImg }}" alt="{{ $deal->name }}">
                @endif
            @endif
        </a>
        <div class="card-body d-flex flex-column">
            <h3 class="h6 font-weight-bold">{{ $deal->name }}</h3>
            <div class="mt-auto">
                <del class="text-muted">{{ PriceHelper::setCurrencyPrice($deal->original_price) }}</del>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <strong class="text-success h5 mb-0">{{ PriceHelper::setCurrencyPrice($deal->discounted_price) }}</strong>
                    @if($deal->is_free_delivery)
                        <span class="badge badge-success" style="font-size:11px;"><i class="fas fa-truck"></i> {{ __('Free Delivery') }}</span>
                    @elseif($deal->delivery_charge > 0)
                        <span class="badge badge-light border text-dark" style="font-size:11px;"><i class="fas fa-truck"></i> {{ PriceHelper::setCurrencyPrice($deal->delivery_charge) }}</span>
                    @endif
                </div>
                <a href="{{ route('front.deal.details', $deal->slug) }}" class="btn btn-outline-primary btn-sm btn-block mb-2">{{ __('View Bundle') }}</a>
                <div class="d-flex" style="gap:6px;">
                    <form action="{{ route('front.deal.add_to_cart', $deal->slug) }}" method="post" class="flex-grow-1 mb-0">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-sm btn-block text-nowrap" title="{{ __('Add Bundle to Cart') }}"><i class="icon-shopping-cart"></i> {{ __('Add to Cart') }}</button>
                    </form>
                    <form action="{{ route('front.deal.add_to_cart', $deal->slug) }}" method="post" class="flex-grow-1 mb-0">
                        @csrf
                        <input type="hidden" name="buy_now" value="1">
                        <button type="submit" class="btn btn-success btn-sm btn-block text-nowrap" title="{{ __('Buy Bundle Now') }}"><i class="fas fa-bolt"></i> {{ __('Buy Now') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </article>
</div>
