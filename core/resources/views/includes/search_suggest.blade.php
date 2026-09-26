
<div class="s-r-inner">
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