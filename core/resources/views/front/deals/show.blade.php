@extends('master.front')
@section('title', $deal->name)
@section('content')
@php
    $dealEndIso = '';
    if (!empty($deal->end_date)) {
        $dealEndIso = ($deal->end_date instanceof \Carbon\Carbon)
            ? $deal->end_date->toIso8601String()
            : \Carbon\Carbon::parse($deal->end_date)->toIso8601String();
    }
    $setting = \App\Models\Setting::find(1);
    $vendorId = $deal->vendor_id ? (int)$deal->vendor_id : 0;
    $isVendor = ($vendorId > 0);
    $storeName = $deal->store_name;
@endphp
<div class="container padding-bottom-3x mb-2 mt-4" data-deal-end="{{ $dealEndIso }}">
    <a class="small font-weight-bold text-muted mb-3 d-inline-block" href="{{ route('front.deal.index') }}">
        <i class="icon-arrow-left mr-1"></i> {{ __('All Bundles & Deals') }}
    </a>

    <!-- Main Bundle Header Card -->
    <div class="card border-0 shadow-sm mt-2 mb-4" style="border-radius: 14px; overflow: hidden; border: 1px solid #e2e8f0 !important;">
        <div class="card-body p-3 p-md-4">
            @if($deal->photo)
                @php
                    $dealBanner = \Illuminate\Support\Str::startsWith($deal->photo, 'images/')
                        ? url('/core/public/storage/' . $deal->photo)
                        : url('/core/public/storage/images/' . $deal->photo);
                @endphp
                <div class="position-relative mb-3 text-center" style="background:#f8fafc; border-radius:10px; overflow:hidden;">
                    <img src="{{ $dealBanner }}" alt="{{ $deal->name }}" style="max-height:340px; width:100%; object-fit:cover; border-radius:10px;" class="img-fluid">
                    <span class="badge badge-warning text-dark font-weight-bold position-absolute" style="top: 15px; right: 15px; font-size: 13px; padding: 6px 12px; border-radius: 20px; box-shadow: 0 4px 10px rgba(0,0,0,0.15);">
                        <i class="fas fa-bolt mr-1 text-danger"></i> {{ $deal->discount_badge }}
                    </span>
                </div>
            @endif

            <div class="d-flex flex-wrap justify-content-between align-items-center mb-2">
                <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                    <h1 class="h3 font-weight-bold text-dark mb-0 d-inline-block">{{ $deal->name }}</h1>
                    @if(!empty($deal->sku))
                        <span class="d-inline-flex align-items-center px-2.5 py-1 font-weight-bold" style="font-size: 13px; color: #1e3a8a; background: #e0f2fe; border: 1.5px solid #38bdf8; border-radius: 6px; letter-spacing: 0.5px; box-shadow: 0 2px 6px rgba(56, 189, 248, 0.25);">
                            <i class="fas fa-barcode mr-1.5 text-primary" style="font-size: 14px;"></i>SKU:&nbsp;<span style="color: #0369a1; font-weight: 800; font-family: monospace, sans-serif; letter-spacing: 1px;">{{ $deal->sku }}</span>
                        </span>
                    @endif
                </div>
                <div class="d-flex align-items-center" style="gap: 6px;">
                    <div style="font-size: 15px;">
                        {!! Helper::renderStarRating($deal) !!}
                    </div>
                    <span class="text-muted small font-weight-bold">({{ $deal->rating_count }})</span>
                </div>
            </div>

            @if($deal->description)
                <p class="text-muted mb-3" style="font-size: 14.5px; line-height: 1.6;">{{ $deal->description }}</p>
            @endif

            <!-- Deal Price, Timer & Delivery Bar -->
            <div class="alert alert-success mb-3 p-3 d-flex justify-content-between align-items-center flex-wrap" style="gap:12px; background-color: #f0fdf4; border-color: #bbf7d0; color: #166534; border-radius: 10px;">
                <div class="d-flex align-items-center flex-wrap" style="gap: 12px;">
                    <div>
                        <span class="text-muted small d-block font-weight-bold" style="color: #15803d !important;">{{ __('Offer Expires In:') }}</span>
                        <strong class="deal-countdown font-weight-bold" style="font-size: 17px; letter-spacing: 0.5px;">--</strong>
                    </div>
                    <div class="border-left pl-3 ml-2" style="border-color: #bbf7d0 !important;">
                        <span class="text-muted small d-block">{{ __('Special Bundle Price') }}</span>
                        <del class="text-muted mr-1" style="font-size: 14px;">{{ PriceHelper::setCurrencyPrice($deal->original_price) }}</del>
                        <strong class="text-success font-weight-bold" style="font-size: 20px;">{{ PriceHelper::setCurrencyPrice($deal->discounted_price) }}</strong>
                        @php
                            $savedPrice = (float)$deal->original_price - (float)$deal->discounted_price;
                        @endphp
                        @if($savedPrice > 0)
                            <span class="badge font-weight-bold ml-2" style="font-size: 12px; color: #15803d; background: #dcfce7; border: 1px solid #bbf7d0; padding: 4px 8px; border-radius: 6px;">
                                <i class="fas fa-piggy-bank mr-1"></i>{{ __('Save') }} {{ PriceHelper::setCurrencyPrice($savedPrice) }}
                            </span>
                        @endif
                    </div>
                </div>
                <div>
                    @if($deal->is_free_delivery)
                        <span class="badge badge-success px-3 py-2 font-weight-bold" style="font-size: 12px; background: #16a34a;">
                            <i class="fas fa-truck mr-1"></i> {{ __('Free Delivery') }}
                        </span>
                    @elseif($deal->delivery_charge > 0)
                        <span class="badge badge-light border text-dark px-3 py-2 font-weight-bold" style="font-size: 12px;">
                            <i class="fas fa-truck text-primary mr-1"></i> {{ __('Delivery') }}: {{ PriceHelper::setCurrencyPrice($deal->delivery_charge) }}
                        </span>
                    @endif
                </div>
            </div>

            <!-- Action Buttons Area -->
            <div class="mt-3 pt-2 d-flex align-items-center flex-wrap" style="gap:10px;">
                <form action="{{ route('front.deal.add_to_cart', !empty($deal->sku) ? $deal->sku : $deal->slug) }}" method="post" class="d-inline">
                    @csrf
                    <button type="submit" id="bundle_add_to_cart_btn" class="btn btn-primary btn-md px-4 font-weight-bold shadow-sm" style="border-radius: 8px;">
                        <i class="icon-shopping-cart mr-1"></i> {{ __('Add Bundle to Cart') }}
                    </button>
                </form>
                <form action="{{ route('front.deal.add_to_cart', !empty($deal->sku) ? $deal->sku : $deal->slug) }}" method="post" class="d-inline">
                    @csrf
                    <input type="hidden" name="buy_now" value="1">
                    <button type="submit" id="bundle_buy_now_btn" class="btn btn-success btn-md px-4 font-weight-bold shadow-sm" style="border-radius: 8px; background: #16a34a; border-color: #16a34a;">
                        <i class="fas fa-bolt mr-1"></i> {{ __('Buy Bundle Now') }}
                    </button>
                </form>

                <!-- WhatsApp Chat with Seller Trigger Button -->
                <button type="button" class="btn btn-md font-weight-bold shadow-sm text-white" onclick="openWhatsAppChat()" style="background: #008069; border: none; border-radius: 8px; padding: 8px 16px;">
                    <i class="fab fa-whatsapp mr-1" style="font-size: 16px;"></i> {{ __('Chat with Seller') }}
                </button>
            </div>

            <!-- Message Seller Callout Banner -->
            <div class="mt-3 p-2 px-3 rounded d-flex align-items-center justify-content-between" style="background: #f0fdf4; border: 1px solid #bbf7d0; cursor: pointer; transition: all 0.2s;" onclick="openWhatsAppChat()">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background: #008069; color: #fff; margin-right: 12px; font-size: 16px; flex-shrink: 0; box-shadow: 0 2px 5px rgba(0, 128, 105, 0.3);">
                        <i class="fas fa-comment-dots"></i>
                    </div>
                    <div>
                        <span class="font-weight-bold text-dark d-block" style="font-size: 13.5px;">{{ __('Message Seller about this bundle') }}</span>
                        <small class="text-muted" style="font-size: 12px;">{{ __('Inquire about items, stock, fast delivery, or bundle customization directly with :store', ['store' => $storeName]) }}</small>
                    </div>
                </div>
                <span class="badge badge-success px-3 py-1 font-weight-bold" style="background: #008069; font-size: 11.5px; border-radius: 20px;">
                    <i class="fas fa-paper-plane mr-1"></i> {{ __('Chat Now') }}
                </span>
            </div>

            <div class="mt-3">
                @include('includes.copy_share_link', [
                    'shareInputId' => 'bundle-share-link-' . $deal->id,
                    'shareLabel' => __('Bundle Link'),
                    'shareUrl' => route('front.deal.details', !empty($deal->sku) ? $deal->sku : $deal->slug),
                ])
            </div>
        </div>
    </div>

    <!-- Included Products List -->
    <div class="mb-4">
        <h2 class="h4 font-weight-bold text-dark mb-3"><i class="fas fa-cubes text-primary mr-2"></i>{{ __('Included Products in this Bundle') }}</h2>
        <div class="row">
            @foreach($deal->dealItems as $dealItem)
                @php
                    $item = $dealItem->item ?? null;
                @endphp
                @if($item)
                <div class="col-md-6 mb-3">
                    <div class="card h-100 border shadow-sm" style="border-radius: 12px; overflow: hidden; border-color: #e2e8f0 !important;">
                        <div class="row no-gutters h-100">
                            <div class="col-4 p-2 d-flex align-items-center justify-content-center" style="background: #f8fafc;">
                                <img class="img-fluid" style="max-height:130px; width:100%; object-fit:contain"
                                    src="{{ url('/core/public/storage/images/' . ($item->photo ?: $item->thumbnail)) }}"
                                    alt="{{ $item->name }}">
                            </div>
                            <div class="col-8">
                                <div class="card-body p-3 d-flex flex-column justify-content-between h-100">
                                    <div>
                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                            <h3 class="h6 font-weight-bold mb-1 text-dark text-truncate" title="{{ $item->name }}">{{ $item->name }}</h3>
                                            @if(($dealItem->quantity ?? 1) > 1)
                                                <span class="badge badge-primary ml-2 px-2 py-1 font-weight-bold text-nowrap" style="font-size:11.5px;"><i class="fas fa-cubes mr-1"></i>{{ __('Qty: ') }}{{ $dealItem->quantity }}</span>
                                            @endif
                                        </div>
                                        <div class="mb-2">
                                            <del class="small text-muted mr-1">{{ PriceHelper::setCurrencyPrice($dealItem->original_price) }}</del>
                                            <strong class="text-success font-weight-bold" style="font-size: 15px;">{{ PriceHelper::setCurrencyPrice($dealItem->discounted_price) }}</strong>
                                            @if(($dealItem->quantity ?? 1) > 1)
                                                <small class="text-muted font-weight-normal">({{ __('each') }})</small>
                                            @endif
                                        </div>
                                    </div>
                                    <div>
                                        <a href="{{ route('front.product', $item->slug) }}" class="btn btn-outline-primary btn-sm px-3 font-weight-bold" target="_blank" style="border-radius: 6px; font-size: 12px;">
                                            <i class="icon-eye mr-1"></i> {{ __('View Product') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            @endforeach
        </div>
    </div>

    <!-- Store / Vendor Information Card -->
    <div class="card shadow-sm border mb-4" style="border-radius: 12px; background: #ffffff; border-left: 5px solid #0d6efd !important; border-color: #e2e8f0 !important;">
        <div class="card-body p-3 p-md-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between">
                <div class="d-flex align-items-center mb-2 mb-md-0">
                    @if(!$isVendor && isset($setting->brand_logo) && $setting->brand_logo)
                        <div class="d-flex align-items-center justify-content-center mr-3" style="width: 52px; height: 52px; margin-right: 15px; flex-shrink: 0;">
                            <img src="{{ url('/core/public/storage/images/' . $setting->brand_logo) }}" alt="{{ $storeName }}" style="max-width: 52px; max-height: 52px; object-fit: contain;">
                        </div>
                    @else
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white mr-3" style="width: 52px; height: 52px; background: linear-gradient(135deg, #0d6efd, #0b5ed7); font-size: 22px; margin-right: 15px; box-shadow: 0 4px 10px rgba(13, 110, 253, 0.25);">
                            <i class="fas fa-store"></i>
                        </div>
                    @endif
                    <div>
                        <div class="text-uppercase text-muted" style="font-size: 11px; font-weight: 700; letter-spacing: 0.5px;">{{ __('Sold By Store') }}</div>
                        <h5 class="mb-1 font-weight-bold text-dark d-flex align-items-center">
                            {{ $storeName }}
                            <span class="badge badge-success ml-2 py-1 px-2" style="font-size: 11px; font-weight: 600; margin-left: 8px;">
                                <i class="fas fa-check-circle mr-1"></i>{{ $isVendor ? __('Verified Store') : __('Official Store') }}
                            </span>
                        </h5>
                        @php
                            $storeProductsCount = \App\Models\Item::where('vendor_id', $vendorId)->where('status', 1)->count();
                        @endphp
                        <small class="text-muted">
                            <i class="fas fa-box-open text-primary mr-1"></i> {{ $storeProductsCount }} {{ __('Product(s) listed') }}
                            @if($deal->vendor && $deal->vendor->seller && $deal->vendor->seller->shop_address)
                                <span class="mx-2 text-muted">•</span> <i class="fas fa-map-marker-alt text-danger mr-1"></i> {{ $deal->vendor->seller->shop_address }}
                            @endif
                        </small>
                    </div>
                </div>
                <div class="d-flex align-items-center" style="gap: 8px;">
                    <button type="button" class="btn btn-outline-success btn-sm px-3 py-2 font-weight-bold shadow-sm" onclick="openWhatsAppChat()" style="border-radius: 8px;">
                        <i class="fab fa-whatsapp mr-1"></i> {{ __('Chat') }}
                    </button>
                    <a href="{{ $deal->getStoreUrl() }}" class="btn btn-primary btn-sm px-4 py-2 font-weight-bold shadow-sm" style="border-radius: 8px;">
                        <i class="fas fa-store-alt mr-1"></i> {{ __('Visit Store') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Ratings & Customer Reviews Section -->
    <div class="review-area my-5" id="reviews_section">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-title mb-4">
                    <h2 class="h3 font-weight-bold text-dark"><i class="fas fa-comments text-primary mr-2"></i>{{ __('Customer Reviews & Ratings') }}</h2>
                    <p class="text-muted" style="font-size: 14px;">{{ __('Verified feedback and honest ratings from genuine buyers.') }}</p>
                </div>
            </div>
        </div>
        <div class="row">
            <!-- Left Column: Reviews List -->
            <div class="col-lg-8 col-md-7">
                @forelse ($reviews as $review)
                    <div class="card border-0 mb-3 modern-review-card shadow-sm" style="border-radius: 14px; background: #ffffff; border: 1px solid #edf2f7 !important; transition: all 0.2s ease;">
                        <div class="card-body p-3 p-md-4">
                            <div class="d-flex align-items-start">
                                <!-- User Avatar -->
                                <div class="mr-3 mr-md-4" style="margin-right: 16px;">
                                    @if ($review->user && $review->user->photo)
                                        <img src="{{ url('/core/public/storage/images/' . $review->user->photo) }}" class="rounded-circle shadow-sm" style="width: 48px; height: 48px; min-width: 48px; object-fit: cover; border: 2px solid #ffffff;" alt="{{ $review->reviewer_name }}">
                                    @else
                                        <div class="whatsapp-no-dp-avatar" style="width: 48px; height: 48px; min-width: 48px; border-radius: 50%; background: #e2e8f0; display: flex; align-items: center; justify-content: center; border: 2px solid #ffffff; box-shadow: 0 2px 6px rgba(0,0,0,0.06);">
                                            <svg viewBox="0 0 24 24" width="28" height="28" fill="#64748b">
                                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                <!-- Review Body -->
                                <div class="flex-grow-1">
                                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-1">
                                        <div class="d-flex align-items-center flex-wrap">
                                            <h6 class="font-weight-bold text-dark mb-0 mr-2" style="font-size: 15.5px;">{{ $review->reviewer_name }}</h6>
                                            <span class="badge badge-pill badge-success mr-2" style="background-color: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; font-size: 11px; padding: 3px 8px; font-weight: 600;">
                                                <i class="fas fa-check-circle mr-1"></i> {{ __('Verified Buyer') }}
                                            </span>
                                        </div>
                                        <span class="text-muted small">
                                            <i class="far fa-calendar-alt mr-1"></i> {{ $review->created_at ? $review->created_at->format('M d, Y') : date('M d, Y') }}
                                        </span>
                                    </div>

                                    <!-- Star Ratings -->
                                    <div class="mb-2" style="color: #f59e0b; font-size: 13.5px; letter-spacing: 1px;">
                                        @for ($i = 0; $i < $review->rating; $i++)
                                            <i class="fas fa-star text-warning" style="color: #f59e0b !important;"></i>
                                        @endfor
                                        @for ($i = $review->rating; $i < 5; $i++)
                                            <i class="far fa-star text-muted" style="color: #cbd5e1 !important;"></i>
                                        @endfor
                                    </div>

                                    @if (!empty($review->subject))
                                        <h5 class="font-weight-bold text-dark mb-1" style="font-size: 15px; line-height: 1.3;">{{ $review->subject }}</h5>
                                    @endif

                                    <p class="text-secondary mb-2" style="font-size: 14px; line-height: 1.6; color: #475569 !important;">
                                        {{ $review->review }}
                                    </p>

                                    @if (!empty($review->review_photos))
                                        <div class="review-photos-container d-flex flex-wrap mt-2 pt-1" style="gap: 10px;">
                                            @foreach ($review->review_photos as $rPhoto)
                                                <a href="{{ url('/core/public/storage/images/' . $rPhoto) }}" target="_blank" class="d-inline-block position-relative" title="{{ __('Click to view full image') }}">
                                                    <img src="{{ url('/core/public/storage/images/' . $rPhoto) }}" alt="{{ __('Customer Review Photo') }}" class="img-thumbnail rounded shadow-sm" style="height: 85px; width: 85px; object-fit: cover; border-radius: 8px; cursor: pointer; border: 1px solid #e2e8f0; transition: transform 0.2s ease, box-shadow 0.2s ease;" onmouseover="this.style.transform='scale(1.06)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.15)';" onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='none';">
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="card border-0 text-center p-5 shadow-sm" style="border-radius: 14px; background: #ffffff; border: 1px dashed #cbd5e1 !important;">
                        <div class="py-3">
                            <div class="mb-3">
                                <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 70px; height: 70px; background: #eff6ff; color: #2563eb; font-size: 30px;">
                                    <i class="far fa-comment-dots"></i>
                                </span>
                            </div>
                            <h5 class="font-weight-bold text-dark mb-1">{{ __('No Reviews Yet') }}</h5>
                            <p class="text-muted small mb-3">{{ __('Be the first one to share your feedback and experience with this bundle.') }}</p>
                            @if (Auth::user())
                                <button type="button" class="btn btn-primary btn-sm px-4 font-weight-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#leaveReview">
                                    <i class="fas fa-pencil-alt mr-1"></i> {{ __('Write the First Review') }}
                                </button>
                            @else
                                <a href="{{ route('user.login') }}" class="btn btn-primary btn-sm px-4 font-weight-bold shadow-sm">
                                    <i class="fas fa-sign-in-alt mr-1"></i> {{ __('Login to Write a Review') }}
                                </a>
                            @endif
                        </div>
                    </div>
                @endforelse

                @if ($reviews->hasPages())
                    <div class="row mt-4">
                        <div class="col-lg-12 text-center">
                            {{ $reviews->links() }}
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Column: Rating Summary Breakdown Card -->
            <div class="col-lg-4 col-md-5 mb-4">
                <div class="card border-0 shadow-sm sticky-top" style="border-radius: 16px; background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%); border: 1px solid #e2e8f0 !important; top: 100px;">
                    <div class="card-body p-4">
                        <h6 class="font-weight-bold text-dark mb-3" style="font-size: 15px;"><i class="fas fa-chart-bar text-primary mr-1"></i> {{ __('Rating Summary') }}</h6>
                        
                        <div class="text-center pb-3 border-bottom mb-3">
                            @php
                                $avgRatingScore = $deal->rating;
                                $totalRatingCount = $deal->rating_count;
                            @endphp
                            <div class="d-flex align-items-center justify-content-center">
                                <span class="font-weight-bold text-dark" style="font-size: 46px; line-height: 1;">{{ number_format($avgRatingScore, 1) }}</span>
                                <span class="text-muted ml-2 font-weight-bold" style="font-size: 18px;">/ 5.0</span>
                            </div>
                            <div class="my-2" style="font-size: 18px;">
                                {!! Helper::renderStarRating($deal) !!}
                            </div>
                            <span class="text-muted small font-weight-bold">
                                {{ __('Based on') }} {{ $totalRatingCount }} {{ __('customer ratings') }}
                            </span>
                        </div>

                        <!-- 5 Star Breakdown Bars -->
                        <div class="rating-breakdown-bars">
                            @for ($star = 5; $star >= 1; $star--)
                                @php
                                    $realStarCount = $deal->reviews()->where('status', 1)->where('rating', $star)->count();
                                    if ($realStarCount == 0 && $totalRatingCount > 0) {
                                        $itemIds = $deal->dealItems->pluck('item_id')->toArray();
                                        if (!empty($itemIds)) {
                                            $realStarCount = \App\Models\Review::whereIn('item_id', $itemIds)->where('status', 1)->where('rating', $star)->count();
                                        }
                                    }
                                    $starPercent = ($totalRatingCount > 0) ? round(($realStarCount / $totalRatingCount) * 100) : 0;
                                @endphp
                                <div class="d-flex align-items-center mb-2" style="font-size: 13px;">
                                    <span class="text-dark font-weight-bold" style="width: 45px;">{{ $star }} <i class="fas fa-star text-warning" style="font-size: 11px; color: #f59e0b !important;"></i></span>
                                    <div class="progress flex-grow-1 mx-2" style="height: 7px; border-radius: 10px; background-color: #e2e8f0;">
                                        <div class="progress-bar" role="progressbar" style="width: {{ $starPercent }}%; background: linear-gradient(90deg, #f59e0b, #fbbf24); border-radius: 10px;" aria-valuenow="{{ $starPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <span class="text-muted text-right" style="width: 35px;">{{ $realStarCount }}</span>
                                </div>
                            @endfor
                        </div>

                        <hr class="my-3">

                        <!-- Review Action Button -->
                        @if (Auth::user())
                            <button type="button" class="btn btn-primary btn-block w-100 font-weight-bold py-2 shadow-sm" style="border-radius: 8px;" data-bs-toggle="modal" data-bs-target="#leaveReview">
                                <i class="fas fa-pencil-alt mr-1"></i> {{ __('Write a Review') }}
                            </button>
                        @else
                            <a href="{{ route('user.login') }}" class="btn btn-primary btn-block w-100 font-weight-bold py-2 shadow-sm" style="border-radius: 8px;">
                                <i class="fas fa-sign-in-alt mr-1"></i> {{ __('Login to Write a Review') }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom CTA Bar -->
    <div class="text-center mt-4 pt-3 border-top d-flex justify-content-center align-items-center flex-wrap" style="gap:15px;">
        <form action="{{ route('front.deal.add_to_cart', $deal->slug) }}" method="post" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-primary btn-lg px-4 shadow-sm font-weight-bold" style="border-radius: 8px;"><i class="icon-shopping-cart mr-1"></i> {{ __('Add Bundle to Cart') }}</button>
        </form>
        <form action="{{ route('front.deal.add_to_cart', $deal->slug) }}" method="post" class="d-inline">
            @csrf
            <input type="hidden" name="buy_now" value="1">
            <button type="submit" class="btn btn-success btn-lg px-4 shadow-sm font-weight-bold" style="border-radius: 8px; background: #16a34a; border-color: #16a34a;"><i class="fas fa-bolt mr-1"></i> {{ __('Buy Bundle Now') }}</button>
        </form>
        <button type="button" class="btn btn-lg font-weight-bold shadow-sm text-white" onclick="openWhatsAppChat()" style="background: #008069; border: none; border-radius: 8px; padding: 10px 22px;">
            <i class="fab fa-whatsapp mr-1" style="font-size: 18px;"></i> {{ __('Chat with Seller') }}
        </button>
    </div>
</div>

<!-- Leave Review Modal -->
@auth
    <form class="modal fade ratingForm" action="{{ route('front.review.submit') }}" method="post" id="leaveReview"
        tabindex="-1" enctype="multipart/form-data">
        @csrf
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                <div class="modal-header bg-light py-3 px-4" style="border-bottom: 1px solid #e2e8f0;">
                    <h4 class="modal-title font-weight-bold text-dark mb-0" style="font-size: 18px;">
                        <i class="fas fa-star text-warning mr-2" style="color: #f59e0b !important;"></i> {{ __('Leave a Bundle Review') }}
                    </h4>
                    <button class="close modal_close" type="button" data-bs-dismiss="modal" aria-label="Close" style="font-size: 24px; outline: none;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    @php
                        $user = Auth::user();
                    @endphp
                    <input type="hidden" name="deal_id" value="{{ $deal->id }}">

                    <div class="row mb-3">
                        <div class="col-sm-6 mb-2 mb-sm-0">
                            <div class="form-group mb-0">
                                <label class="font-weight-bold text-dark small mb-1" for="review-name">{{ __('Your Name') }}</label>
                                <input class="form-control" type="text" id="review-name" value="{{ trim($user->first_name . ' ' . $user->last_name) }}" readonly style="background-color: #f8fafc; border-radius: 8px;">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group mb-0">
                                <label class="font-weight-bold text-dark small mb-1" for="review-email">{{ __('Your Email') }}</label>
                                <input class="form-control" type="email" id="review-email" value="{{ $user->email }}" readonly style="background-color: #f8fafc; border-radius: 8px;">
                            </div>
                        </div>
                    </div>

                    <!-- Interactive Star Rating Selector -->
                    <div class="card p-3 mb-3 border-0" style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border-radius: 12px; border: 1px solid #fde68a !important;">
                        <label class="font-weight-bold text-dark mb-2 d-flex align-items-center justify-content-between">
                            <span><i class="fas fa-star text-warning mr-1" style="color: #f59e0b !important;"></i> {{ __('Your Rating') }} <span class="text-danger">*</span></span>
                            <span class="badge badge-warning text-dark font-weight-bold px-3 py-1" id="review-rating-label" style="font-size: 13px; border-radius: 20px; background-color: #fbbf24;">
                                5.0 - {{ __('Excellent') }}
                            </span>
                        </label>

                        <div class="d-flex align-items-center flex-wrap">
                            <div class="interactive-star-rating d-inline-flex align-items-center" id="interactive-star-picker" style="font-size: 32px; cursor: pointer; user-select: none;">
                                <i class="fas fa-star star-option active" data-score="1" style="color: #f59e0b; margin-right: 6px; transition: transform 0.15s ease;"></i>
                                <i class="fas fa-star star-option active" data-score="2" style="color: #f59e0b; margin-right: 6px; transition: transform 0.15s ease;"></i>
                                <i class="fas fa-star star-option active" data-score="3" style="color: #f59e0b; margin-right: 6px; transition: transform 0.15s ease;"></i>
                                <i class="fas fa-star star-option active" data-score="4" style="color: #f59e0b; margin-right: 6px; transition: transform 0.15s ease;"></i>
                                <i class="fas fa-star star-option active" data-score="5" style="color: #f59e0b; margin-right: 6px; transition: transform 0.15s ease;"></i>
                            </div>
                            <input type="hidden" name="rating" id="review-rating-input" value="5">
                            <small class="text-muted ml-3 d-none d-md-inline" style="font-size: 12px;">({{ __('Click on stars to rate') }})</small>
                        </div>
                    </div>

                    <!-- Review Subject -->
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark small mb-1" for="review-subject">{{ __('Review Title / Subject') }} <span class="text-muted">({{ __('Optional') }})</span></label>
                        <input class="form-control" type="text" name="subject" id="review-subject" placeholder="{{ __('e.g. Incredible bundle value and fast delivery!') }}" style="border-radius: 8px;">
                    </div>

                    <!-- Review Text Message -->
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark small mb-1" for="review-message">{{ __('Review Details') }} <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="review" id="review-message" rows="4" placeholder="{{ __('Share your genuine experience with this bundle package, products quality, packing, and delivery...') }}" required style="border-radius: 8px;"></textarea>
                    </div>

                    <!-- Optional Photo Upload with Instant Multi-Photo Preview (Max 3) -->
                    <div class="form-group mb-2">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="font-weight-bold text-dark small mb-0">
                                <i class="fas fa-camera text-primary mr-1"></i> {{ __('Add Photos (Optional)') }}
                            </label>
                            <span class="text-muted small" id="review-photo-count-badge" style="font-size: 11.5px;">(Max 3 pictures)</span>
                        </div>
                        
                        <div class="review-upload-box p-3 border text-center position-relative" id="review-drop-zone" style="background: #f8fafc; border: 2px dashed #cbd5e1 !important; border-radius: 12px; transition: all 0.2s ease;">
                            <input type="file" name="photos[]" id="review-photo-input" class="d-none" accept="image/jpeg,image/png,image/webp,image/jpg,image/gif" multiple onchange="handleReviewPhotosSelect(this)">
                            
                            <div id="review-upload-prompt" style="cursor: pointer;" onclick="document.getElementById('review-photo-input').click();">
                                <div class="mb-1 text-primary" style="font-size: 28px;">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                </div>
                                <p class="font-weight-bold text-dark mb-0 small">{{ __('Click to browse & select up to 3 pictures') }}</p>
                                <span class="text-muted" style="font-size: 11.5px;">{{ __('Supports JPG, PNG, WebP or GIF (Max 8MB each)') }}</span>
                            </div>

                            <div id="review-images-grid" class="d-none mt-2 d-flex flex-wrap justify-content-center align-items-center" style="gap: 12px;">
                                <!-- Dynamic Preview Thumbnails rendered by JS -->
                            </div>

                            <div id="review-add-more-container" class="d-none mt-2">
                                <button type="button" class="btn btn-outline-primary btn-sm px-3" style="border-radius: 20px; font-size: 12px;" onclick="document.getElementById('review-photo-input').click();">
                                    <i class="fas fa-plus mr-1"></i> {{ __('Add Another Picture') }}
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer bg-light py-3 px-4 d-flex justify-content-between" style="border-top: 1px solid #e2e8f0;">
                    <button class="btn btn-outline-secondary btn-sm px-4" type="button" data-bs-dismiss="modal" style="border-radius: 8px;">
                        {{ __('Cancel') }}
                    </button>
                    <button class="btn btn-primary px-4 font-weight-bold shadow-sm" type="submit" style="border-radius: 8px;">
                        <i class="fas fa-paper-plane mr-1"></i> <span>{{ __('Submit Review') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </form>
@endauth

@include('front.catalog.inc.whatsapp_chatbox')

@endsection

@section('scripts')
@include('front.deals.countdown-script')

<script>
    // Rating star labels map
    const ratingLabels = {
        1: "1.0 - {{ __('Very Poor') }}",
        2: "2.0 - {{ __('Poor') }}",
        3: "3.0 - {{ __('Average') }}",
        4: "4.0 - {{ __('Good') }}",
        5: "5.0 - {{ __('Excellent') }}"
    };

    function setInteractiveRating(score) {
        score = parseInt(score) || 5;
        $('#review-rating-input').val(score);
        $('#review-rating-label').text(ratingLabels[score] || (score + '.0'));

        $('#interactive-star-picker .star-option').each(function() {
            const starScore = parseInt($(this).attr('data-score'));
            if (starScore <= score) {
                $(this).removeClass('far').addClass('fas').css('color', '#f59e0b');
            } else {
                $(this).removeClass('fas').addClass('far').css('color', '#cbd5e1');
            }
        });
    }

    $(document).on('mouseenter', '#interactive-star-picker .star-option', function() {
        const hoverScore = parseInt($(this).attr('data-score'));
        $('#interactive-star-picker .star-option').each(function() {
            const starScore = parseInt($(this).attr('data-score'));
            if (starScore <= hoverScore) {
                $(this).removeClass('far').addClass('fas').css('color', '#fbbf24');
            } else {
                $(this).removeClass('fas').addClass('far').css('color', '#e2e8f0');
            }
        });
        $('#review-rating-label').text(ratingLabels[hoverScore] || (hoverScore + '.0'));
    });

    $(document).on('mouseleave', '#interactive-star-picker', function() {
        const currentScore = parseInt($('#review-rating-input').val()) || 5;
        setInteractiveRating(currentScore);
    });

    $(document).on('click', '#interactive-star-picker .star-option', function() {
        const clickedScore = parseInt($(this).attr('data-score'));
        setInteractiveRating(clickedScore);
    });

    // Review Multi-Photo Upload & Preview Handler (Max 3)
    let reviewSelectedFiles = [];

    function handleReviewPhotosSelect(input) {
        if (input.files && input.files.length > 0) {
            const newFiles = Array.from(input.files);
            
            for (let i = 0; i < newFiles.length; i++) {
                if (reviewSelectedFiles.length >= 3) {
                    dangerNotification("{{ __('You can upload a maximum of 3 pictures.') }}");
                    break;
                }
                if (newFiles[i].type.startsWith('image/')) {
                    reviewSelectedFiles.push(newFiles[i]);
                }
            }
            
            syncReviewFileInput();
            renderReviewThumbnails();
        }
    }

    function removeReviewPhoto(index) {
        if (index >= 0 && index < reviewSelectedFiles.length) {
            reviewSelectedFiles.splice(index, 1);
            syncReviewFileInput();
            renderReviewThumbnails();
        }
    }

    function syncReviewFileInput() {
        const input = document.getElementById('review-photo-input');
        if (!input) return;
        
        try {
            const dt = new DataTransfer();
            reviewSelectedFiles.forEach(file => dt.items.add(file));
            input.files = dt.files;
        } catch (e) {
            console.error("DataTransfer error: ", e);
        }
    }

    function renderReviewThumbnails() {
        const $grid = $('#review-images-grid');
        const $prompt = $('#review-upload-prompt');
        const $addMore = $('#review-add-more-container');
        const $countBadge = $('#review-photo-count-badge');

        $grid.empty();

        if (reviewSelectedFiles.length === 0) {
            $grid.addClass('d-none');
            $addMore.addClass('d-none');
            $prompt.removeClass('d-none');
            $countBadge.text('(Max 3 pictures)').removeClass('text-primary font-weight-bold');
            return;
        }

        $prompt.addClass('d-none');
        $grid.removeClass('d-none');
        $countBadge.text('(' + reviewSelectedFiles.length + '/3 pictures selected)').addClass('text-primary font-weight-bold');

        if (reviewSelectedFiles.length < 3) {
            $addMore.removeClass('d-none');
        } else {
            $addMore.addClass('d-none');
        }

        reviewSelectedFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const thumbHtml = `
                    <div class="review-thumb-item position-relative text-center" style="width: 95px; margin: 4px;">
                        <div style="width: 95px; height: 95px; border-radius: 10px; overflow: hidden; border: 2px solid #e2e8f0; background: #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.06); display: flex; align-items: center; justify-content: center; padding: 4px;">
                            <img src="${e.target.result}" alt="Preview" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                        </div>
                        <button type="button" class="btn btn-danger btn-sm rounded-circle position-absolute shadow" style="top: -6px; right: -6px; width: 22px; height: 22px; padding: 0; line-height: 20px; font-size: 12px; z-index: 5;" onclick="removeReviewPhoto(${index})" title="{{ __('Remove photo') }}">
                            &times;
                        </button>
                        <div class="text-truncate mt-1 small text-muted" style="font-size: 11px; max-width: 95px;" title="${file.name}">
                            ${(file.size / (1024 * 1024)).toFixed(1)} MB
                        </div>
                    </div>
                `;
                $grid.append(thumbHtml);
            };
            reader.readAsDataURL(file);
        });
    }

    $(document).on('hidden.bs.modal', '#leaveReview', function () {
        reviewSelectedFiles = [];
        syncReviewFileInput();
        renderReviewThumbnails();
        setInteractiveRating(5);
    });
</script>
@include('front.deals.countdown-script')
@endsection
