@if($deals->isNotEmpty())
<section class="container mt-5 mb-5 flash-deals-widget">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h2 class="h3 mb-0"><i class="icon-zap text-warning"></i> {{ __('Buy Bundles and Get Discount') }}</h2>
        <a class="btn btn-outline-primary btn-sm" href="{{ route('front.deal.index') }}">{{ __('View All Deals') }}</a>
    </div>
    <div class="row">
        @foreach($deals as $deal)
            @include('front.deals.card', ['deal' => $deal, 'column' => 'col-lg-3 col-md-4 col-sm-6 mb-4'])
        @endforeach
    </div>
    <div class="text-center">
        <a class="btn btn-primary" href="{{ route('front.deal.index') }}">{{ __('View All Deals') }}</a>
    </div>
</section>
@include('front.deals.countdown-script')
@endif
