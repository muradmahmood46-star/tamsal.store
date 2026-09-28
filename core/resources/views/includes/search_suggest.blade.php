
<div class="s-r-inner">
    @if(isset($deals) && $deals->count() > 0)
        <div class="search-section-header px-3 py-1 bg-light border-bottom d-flex align-items-center justify-content-between">
            <span class="badge badge-success font-weight-bold px-2 py-1" style="font-size: 11px;"><i class="fas fa-boxes mr-1"></i> {{ __('BUNDLE OFFERS') }}</span>
            <small class="text-muted font-weight-bold">{{ $deals->count() }} {{ __('found') }}</small>
        </div>
        @foreach ($deals as $deal)
            @php
                $dealUrl = route('front.deal.details', !empty($deal->sku) ? $deal->sku : $deal->slug);
                $dealImgUrl = '';
                if ($deal->photo) {
                    $dealImgUrl = \Illuminate\Support\Str::startsWith($deal->photo, 'images/')
                        ? url('/core/public/storage/' . $deal->photo)
                        : url('/core/public/storage/images/' . $deal->photo);
                } else {
                    $fItem = $deal->dealItems->first()->item ?? null;
                    if ($fItem) {
                        $fThumb = $fItem->photo ?: $fItem->thumbnail;
                        $dealImgUrl = \Illuminate\Support\Str::startsWith($fThumb, 'images/')
                            ? url('/core/public/storage/' . $fThumb)
                            : url('/core/public/storage/images/' . $fThumb);
                    }
                }
            @endphp
            <div class="product-card p-col bundle-search-item" style="background: #f0fdf4; border-left: 3px solid #10b981;">
                <a class="product-thumb" href="{{ $dealUrl }}">
                    <img class="lazy" alt="{{ $deal->name }}" src="{{ $dealImgUrl ?: asset('assets/images/placeholder.png') }}">
                </a>
                <div class="product-card-body">
                    <div class="d-flex align-items-center flex-wrap mb-1" style="gap: 3px;">
                        <span class="badge badge-danger px-1 py-0 font-weight-bold" style="font-size: 10px;">{{ $deal->discount_badge }}</span>
                        @if(!empty($deal->sku))
                            <span class="badge badge-light border text-dark px-1 py-0 font-weight-bold" style="font-size: 10px;"><i class="fas fa-barcode"></i> {{ $deal->sku }}</span>
                        @endif
                    </div>
                    <h3 class="product-title mb-1"><a href="{{ $dealUrl }}">
                        {{ Str::limit($deal->name, 38) }}
                    </a></h3>
                    <div class="rating-stars">
                        {!! Helper::renderStarRating($deal->rating) !!}
                        @if($deal->rating > 0)
                            <span class="text-muted ml-1" style="font-size: 11px; font-weight: 600;">({{ number_format($deal->rating, 1) }})</span>
                        @endif
                    </div>
                    <h4 class="product-price text-success">
                        {{ PriceHelper::setCurrencyPrice($deal->discounted_price) }}
                        @if($deal->original_price > $deal->discounted_price)
                            <del class="text-muted ml-1" style="font-size: 11.5px; font-weight: normal;">{{ PriceHelper::setCurrencyPrice($deal->original_price) }}</del>
                        @endif
                    </h4>
                </div>
            </div>
        @endforeach
        @if($items->count() > 0)
            <div class="search-section-header px-3 py-1 bg-light border-top border-bottom">
                <span class="badge badge-primary font-weight-bold px-2 py-1" style="font-size: 11px;"><i class="fas fa-box mr-1"></i> {{ __('PRODUCTS') }}</span>
            </div>
        @endif
    @endif

    @foreach ($items as $item)
    <div class="product-card p-col">
        <a class="product-thumb" href="{{ route('front.product', !empty($item->sku) ? $item->sku : $item->slug) }}">
            <img class="lazy" alt="Product" src="{{ url('/core/public/storage/images/'.$item->thumbnail) }}">
        </a>
        <div class="product-card-body">
            <h3 class="product-title mb-1"><a href="{{ route('front.product', !empty($item->sku) ? $item->sku : $item->slug) }}">
                {{ Str::limit($item->name, 35) }}
            </a></h3>
            @if(!empty($item->sku))
                <div class="mb-1">
                    <span class="badge badge-light border text-primary px-1 py-0 font-weight-bold" style="font-size: 10.5px;">SKU: {{ $item->sku }}</span>
                </div>
            @endif
            <div class="rating-stars">
                {!! Helper::renderStarRating($item) !!}
                @if($item->rating > 0)
                    <span class="text-muted ml-1" style="font-size: 11.5px; font-weight: 600;">({{ number_format($item->rating, 1) }})</span>
                @endif
            </div>
            <h4 class="product-price">
                {{ PriceHelper::grandCurrencyPrice($item) }}
            </h4>
        </div>
    </div>
    @endforeach
    
</div>
<div class="bottom-area">
    <a id="view_all_search_" href="javascript:;">{{ __('View all result') }}</a>
</div>