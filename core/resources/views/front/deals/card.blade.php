<div class="{{ $column ?? 'col-md-4 mb-4' }}">
    <article class="card h-100 border-0 shadow-sm deal-card" data-deal-end="{{ $deal->end_date->toIso8601String() }}">
        <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center py-2">
            <small class="font-weight-bold"><i class="icon-clock"></i> <span class="deal-countdown">--</span></small>
            <span class="badge badge-warning text-dark">{{ $deal->discount_badge }}</span>
        </div>
        <a href="{{ route('front.deal.details', $deal->slug) }}">
            @if($deal->photo)
                <img class="card-img-top p-3" style="height:160px;object-fit:contain" src="{{ url('/core/public/storage/' . $deal->photo) }}" alt="{{ $deal->name }}">
            @else
                @php($firstItem = $deal->dealItems->first()->item ?? null)
                @if($firstItem)
                    <img class="card-img-top p-3" style="height:160px;object-fit:contain" src="{{ url('/core/public/storage/images/' . ($firstItem->photo ?: $firstItem->thumbnail)) }}" alt="{{ $deal->name }}">
                @endif
            @endif
        </a>
        <div class="card-body d-flex flex-column">
            <h3 class="h6 font-weight-bold">{{ $deal->name }}</h3>
            <div class="mt-auto">
                <del class="text-muted">{{ PriceHelper::setCurrencyPrice($deal->original_price) }}</del>
                <strong class="d-block text-success h5 mb-2">{{ PriceHelper::setCurrencyPrice($deal->discounted_price) }}</strong>
                <a href="{{ route('front.deal.details', $deal->slug) }}" class="btn btn-outline-primary btn-sm btn-block mb-1">{{ __('View Bundle') }}</a>
                <form action="{{ route('front.deal.add_to_cart', $deal->slug) }}" method="post">
                    @csrf
                    <button class="btn btn-primary btn-sm btn-block"><i class="icon-shopping-cart"></i> {{ __('Add Bundle to Cart') }}</button>
                </form>
            </div>
        </div>
    </article>
</div>
