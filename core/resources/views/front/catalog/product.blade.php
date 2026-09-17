@extends('master.front')

@section('title')
    {{ $item->name }}
@endsection


@section('meta')
    <meta name="tile" content="{{ $item->title }}">
    <meta name="keywords" content="{{ $item->meta_keywords }}">
    <meta name="description" content="{{ $item->meta_description }}">

    <meta name="twitter:title" content="{{ $item->title }}">
    <meta name="twitter:image" content="{{ url('/core/public/storage/images/' . $item->photo) }}">
    <meta name="twitter:description" content="{{ $item->meta_description }}">

    <meta name="og:title" content="{{ $item->title }}">
    <meta name="og:image" content="{{ url('/core/public/storage/images/' . $item->photo) }}">
    <meta name="og:description" content="{{ $item->meta_description }}">
@endsection



@section('content')
    <div class="page-title">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <ul class="breadcrumbs">
                        <li><a href="{{ route('front.index') }}">{{ __('Home') }}</a>
                        </li>
                        <li class="separator"></li>
                        <li><a href="{{ route('front.catalog') }}">{{ __('Shop') }}</a>
                        </li>
                        <li class="separator"></li>
                        <li>{{ $item->name }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- Page Content-->
    <div class="container padding-bottom-1x mb-1">
        <div class="row">
            <!-- Poduct Gallery-->
            <div class="col-xxl-5 col-lg-6 col-md-6">
                <div class="product-gallery">
                    @if ($item->video)
                        <div class="gallery-wrapper">
                            <div class="gallery-item video-btn text-center">
                                <a href="{{ $item->video }}" title="Watch video"></a>
                            </div>
                        </div>
                    @endif
                    @if ($item->is_stock())
                        <span
                            class="product-badge
                        @if ($item->is_type == 'feature') bg-warning
                        @elseif($item->is_type == 'new')
                        bg-success
                        @elseif($item->is_type == 'top')
                        bg-info
                        @elseif($item->is_type == 'best')
                        bg-dark
                        @elseif($item->is_type == 'flash_deal')
                            bg-success @endif
                        ">{{ __($item->is_type != 'undefine' ? ucfirst(str_replace('_', ' ', $item->is_type)) : '') }}</span>
                    @else
                        <span class="product-badge bg-secondary border-default text-body">{{ __('out of stock') }}</span>
                    @endif

                    @if ($item->previous_price && $item->previous_price != 0)
                        <div class="product-badge bg-goldenrod  ppp-t"> -{{ PriceHelper::DiscountPercentage($item) }}</div>
                    @endif

                    <div class="product-thumbnails insize">
                        <div class="product-details-slider owl-carousel">
                            <div class="item"><img src="{{ url('/core/public/storage/images/' . $item->photo) }}"
                                    alt="zoom" />
                            </div>
                            @foreach ($galleries as $key => $gallery)
                                <div class="item"><img src="{{ url('/core/public/storage/images/' . $gallery->photo) }}"
                                        alt="zoom" /></div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <!-- Product Info-->
            <div class="col-xxl-7 col-lg-6 col-md-6">
                <div class="details-page-top-right-content d-flex align-items-center">
                    <div class="div w-100">
                        <input type="hidden" id="item_id" value="{{ $item->id }}">
                        <input type="hidden" id="demo_price"
                            value="{{ PriceHelper::setConvertPrice($item->discount_price) }}">
                        <input type="hidden" value="{{ PriceHelper::setCurrencySign() }}" id="set_currency">
                        <input type="hidden" value="{{ PriceHelper::setCurrencyValue() }}" id="set_currency_val">
                        <!-- Category Tag Above Title -->
                        @if ($item->category)
                            <div class="product-category-breadcrumb mb-2">
                                <span class="d-inline-flex align-items-center py-1 px-2 rounded" style="background: #eff6ff; font-size: 12.5px; font-weight: 600; color: #1d4ed8; border: 1px solid #dbeafe;">
                                    <i class="fas fa-folder-open mr-1 text-primary"></i>
                                    <a href="{{ route('front.catalog') . '?category=' . $item->category->slug }}" style="color: #1d4ed8; text-decoration: none;">{{ $item->category->name }}</a>
                                    @if ($item->subcategory && $item->subcategory->name)
                                        <span class="mx-1 text-muted">/</span>
                                        <a href="{{ route('front.catalog') . '?subcategory=' . $item->subcategory->slug }}" style="color: #3b82f6; text-decoration: none;">{{ $item->subcategory->name }}</a>
                                    @endif
                                    @if ($item->childcategory && $item->childcategory->name)
                                        <span class="mx-1 text-muted">/</span>
                                        <a href="{{ route('front.catalog') . '?childcategory=' . $item->childcategory->slug }}" style="color: #60a5fa; text-decoration: none;">{{ $item->childcategory->name }}</a>
                                    @endif
                                </span>
                            </div>
                        @endif

                        <h4 class="mb-2 p-title-main">{{ $item->name }}</h4>
                        <div class="mb-3" id="product_stock_badge">
                            @php
                                $displayRating = $item->rating;
                                $displayCount = $item->rating_count;
                            @endphp
                            <div class="rating-stars d-inline-block gmr-3">
                                {!! Helper::renderStarRating($item) !!}
                                @if ($displayRating > 0 || $displayCount > 0)
                                    <span class="text-dark font-weight-bold ml-1" style="font-size: 13.5px;">{{ number_format($displayRating, 1) }}</span>
                                    <span class="text-muted" style="font-size: 13px;">({{ $displayCount }} {{ __('Reviews') }})</span>
                                @endif
                            </div>
                            @if ($item->is_stock())
                                <span class="text-success d-inline-block">{{ __('In Stock') }} <b>({{ $item->stock }}
                                        @lang('items'))</b></span>
                            @else
                                <span class="text-danger d-inline-block">{{ __('Out of stock') }}</span>
                            @endif
                        </div>


                        @if ($item->is_type == 'flash_deal')
                            @if (date('d-m-y') != \Carbon\Carbon::parse($item->date)->format('d-m-y'))
                                <div class="countdown countdown-alt mb-3" data-date-time="{{ $item->date }}">
                                </div>
                            @endif
                        @endif

                        <span class="h3 d-block price-area">
                            @if ($item->previous_price != 0)
                                <small
                                    class="d-inline-block"><del>{{ PriceHelper::setPreviousPrice($item->previous_price) }}</del></small>
                            @endif
                            <span id="main_price" class="main-price">{{ PriceHelper::grandCurrencyPrice($item) }}</span>
                        </span>

                        <p class="text-muted">{{ $item->sort_details }} <a href="#details"
                                class="scroll-to">{{ __('Read more') }}</a></p>

                        <style>
                            /* Product Gallery Frame & Sizing */
                            .product-gallery {
                                background: #ffffff;
                                border: 1px solid #e2e8f0;
                                border-radius: 16px;
                                padding: 16px;
                                box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
                                position: relative;
                            }
                            .product-gallery .product-thumbnails {
                                margin-bottom: 8px;
                            }
                            .product-details-slider {
                                border-radius: 12px;
                                overflow: hidden;
                                background: #ffffff;
                            }
                            .product-details-slider .item {
                                height: 450px !important;
                                max-height: 450px !important;
                                display: flex !important;
                                align-items: center !important;
                                justify-content: center !important;
                                background: #ffffff;
                                border-radius: 12px;
                                overflow: hidden;
                                position: relative;
                            }
                            .product-details-slider .item img {
                                max-height: 420px !important;
                                max-width: 100% !important;
                                width: auto !important;
                                height: auto !important;
                                object-fit: contain !important;
                                margin: auto !important;
                                display: block !important;
                            }
                            .product-thumbnails .owl-thumbs {
                                display: flex !important;
                                flex-wrap: wrap !important;
                                justify-content: center !important;
                                gap: 8px !important;
                                margin-top: 14px !important;
                                padding-top: 12px !important;
                                border-top: 1px solid #f1f5f9 !important;
                            }
                            .product-thumbnails .owl-thumbs .owl-thumb-item {
                                width: 68px !important;
                                height: 68px !important;
                                border-radius: 8px !important;
                                border: 2px solid #e2e8f0 !important;
                                overflow: hidden !important;
                                background: #ffffff !important;
                                cursor: pointer !important;
                                transition: all 0.2s ease !important;
                                display: inline-flex !important;
                                align-items: center !important;
                                justify-content: center !important;
                                padding: 4px !important;
                                opacity: 0.75;
                            }
                            .product-thumbnails .owl-thumbs .owl-thumb-item:hover {
                                opacity: 1;
                                border-color: #94a3b8 !important;
                            }
                            .product-thumbnails .owl-thumbs .owl-thumb-item.active {
                                opacity: 1 !important;
                                border-color: #0d6efd !important;
                                box-shadow: 0 2px 8px rgba(13, 110, 253, 0.3) !important;
                            }
                            .product-thumbnails .owl-thumbs .owl-thumb-item img {
                                width: 100% !important;
                                height: 100% !important;
                                object-fit: contain !important;
                            }
                            @media (max-width: 767px) {
                                .product-details-slider .item {
                                    height: 330px !important;
                                    max-height: 330px !important;
                                }
                                .product-details-slider .item img {
                                    max-height: 310px !important;
                                }
                                .product-thumbnails .owl-thumbs .owl-thumb-item {
                                    width: 56px !important;
                                    height: 56px !important;
                                }
                            }

                            .variant-option-group {
                                margin-bottom: 16px;
                            }
                            .variant-pills-container {
                                display: flex;
                                flex-wrap: wrap;
                                gap: 8px;
                                margin-top: 6px;
                            }
                            .variant-pill-btn {
                                display: inline-flex;
                                align-items: center;
                                padding: 6px 14px;
                                border: 2px solid #cbd5e1;
                                border-radius: 6px;
                                cursor: pointer;
                                background: #ffffff;
                                color: #1e293b;
                                font-weight: 600;
                                font-size: 13.5px;
                                transition: all 0.2s ease;
                                user-select: none;
                                position: relative;
                            }
                            .variant-pill-btn:hover:not(.out-of-stock) {
                                border-color: #0d6efd;
                                background: #eff6ff;
                                color: #0d6efd;
                            }
                            .variant-pill-btn.active {
                                border-color: #0d6efd;
                                background: #0d6efd;
                                color: #ffffff;
                                box-shadow: 0 2px 6px rgba(13, 110, 253, 0.35);
                            }
                            .variant-pill-btn.out-of-stock {
                                opacity: 0.55;
                                background: #f8fafc;
                                border-color: #e2e8f0;
                                color: #94a3b8;
                                position: relative;
                            }
                            .variant-pill-btn.out-of-stock .pill-text {
                                text-decoration: line-through;
                                text-decoration-color: #dc2626;
                                text-decoration-thickness: 2px;
                            }
                            .variant-pill-btn.out-of-stock .stock-cut-badge {
                                font-size: 10px;
                                font-weight: 700;
                                background: #fee2e2;
                                color: #dc2626;
                                padding: 1px 5px;
                                border-radius: 4px;
                                margin-left: 5px;
                                text-decoration: none;
                                display: inline-block;
                            }
                        </style>

                        @php
                            $variantsData = !empty($item->item_variants) ? json_decode($item->item_variants, true) : [];
                        @endphp

                        <div class="row margin-top-1x" id="product_variants_wrapper">
                            @foreach ($attributes as $attribute)
                                @if ($attribute->options->count() != 0)
                                    @php
                                        $attrName = $attribute->name;
                                        $attrKey = strtolower(trim($attribute->name));
                                    @endphp
                                    <div class="col-12 variant-option-group" data-attr-name="{{ $attrKey }}">
                                        <label class="font-weight-bold mb-1" style="font-size: 14px;">
                                            {{ $attrName }}: <span class="selected-variant-label text-primary font-weight-bold" id="selected_{{ $attrKey }}_label"></span>
                                        </label>

                                        <!-- Interactive Visual Pill Buttons with Cut Marks -->
                                        <div class="variant-pills-container" id="pills_container_{{ $attrKey }}">
                                            @foreach ($attribute->options as $index => $option)
                                                @php
                                                    $optStock = ($option->stock === 'unlimited') ? 9999 : (int)$option->stock;
                                                    $isOutOfStock = ($optStock <= 0);
                                                @endphp
                                                <div class="variant-pill-btn {{ $index == 0 ? 'active' : '' }} {{ $isOutOfStock ? 'out-of-stock' : '' }}"
                                                     data-attr="{{ $attrKey }}"
                                                     data-val="{{ $option->name }}"
                                                     data-option-id="{{ $option->id }}"
                                                     data-stock="{{ $optStock }}"
                                                     onclick="selectVariantPill(this, '{{ $attrKey }}', '{{ $option->name }}', '{{ $option->id }}')">
                                                    <span class="pill-text">{{ $option->name }}</span>
                                                    @if($isOutOfStock)
                                                        <span class="stock-cut-badge">{{ __('Out of Stock') }}</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>

                                        <!-- Hidden Synced Select for standard cart AJAX -->
                                        <select class="form-control attribute_option d-none" id="{{ $attribute->name }}" data-attr-name="{{ $attrKey }}">
                                            @foreach ($attribute->options as $index => $option)
                                                <option value="{{ $option->name }}" 
                                                        data-type="{{ $attribute->id }}"
                                                        data-href="{{ $option->id }}"
                                                        data-stock="{{ $option->stock }}"
                                                        data-target="{{ PriceHelper::setConvertPrice($option->price) }}"
                                                        {{ $index == 0 ? 'selected' : '' }}>
                                                    {{ $option->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        <div class="row align-items-end pb-4">
                            <div class="col-sm-12">
                                @if ($item->item_type == 'normal')
                                    <div class="qtySelector product-quantity">
                                        <span class="decreaseQty subclick"><i class="fas fa-minus "></i></span>
                                        <input type="text" class="qtyValue cart-amount" value="1">
                                        <span class="increaseQty addclick"><i class="fas fa-plus"></i></span>
                                        <input type="hidden" value="3333" id="current_stock">
                                    </div>
                                @endif
                                <div class="p-action-button">
                                    @if ($item->item_type != 'affiliate')
                                        @if ($item->is_stock())
                                            <button class="btn btn-primary m-0 a-t-c-mr" id="add_to_cart"><i
                                                    class="icon-bag"></i><span>{{ __('Add to Cart') }}</span></button>
                                            <button class="btn btn-primary m-0" id="but_to_cart"><i
                                                    class="icon-bag"></i><span>{{ __('Buy Now') }}</span></button>
                                        @else
                                            <button class="btn btn-primary m-0 disabled" disabled id="add_to_cart"><i
                                                    class="icon-bag"></i><span>{{ __('Out of stock') }}</span></button>
                                        @endif
                                    @else
                                        <a href="{{ $item->affiliate_link }}" target="_blank"
                                            class="btn btn-primary m-0"><span><i
                                                    class="icon-bag"></i>{{ __('Buy Now') }}</span></a>
                                    @endif

                                </div>
                                <div id="variant_stock_status" class="mt-2 mb-2 font-weight-bold" style="display:none; font-size: 13.5px;"></div>
                            </div>
                        </div>

                        <div class="div">
                            <div class="product-specs-box p-3 rounded mt-3 mb-2" style="background: #f8fafc; border: 1px solid #e2e8f0; font-size: 13.5px;">
                                <div class="row g-2">
                                    @if ($item->category)
                                        <div class="col-sm-6 mb-1">
                                            <span class="text-muted font-weight-500"><i class="fas fa-folder text-primary mr-1"></i> {{ __('Category') }}:</span>
                                            <a class="font-weight-600 text-dark ml-1" href="{{ route('front.catalog') . '?category=' . $item->category->slug }}">{{ $item->category->name }}</a>
                                            @if ($item->subcategory && $item->subcategory->name)
                                                <span class="text-muted">/</span> <a class="text-dark" href="{{ route('front.catalog') . '?subcategory=' . $item->subcategory->slug }}">{{ $item->subcategory->name }}</a>
                                            @endif
                                        </div>
                                    @endif
                                    @if ($item->brand_id && $item->brand)
                                        <div class="col-sm-6 mb-1">
                                            <span class="text-muted font-weight-500"><i class="fas fa-bookmark text-info mr-1"></i> {{ __('Brand') }}:</span>
                                            <a class="font-weight-600 text-dark ml-1" href="{{ route('front.catalog') . '?brand=' . $item->brand->slug }}">{{ $item->brand->name }}</a>
                                        </div>
                                    @endif
                                    @if ($item->item_type == 'normal' && $item->sku)
                                        <div class="col-sm-6 mb-1">
                                            <span class="text-muted font-weight-500"><i class="fas fa-barcode text-secondary mr-1"></i> {{ __('SKU') }}:</span>
                                            <span class="font-weight-600 text-dark ml-1">#{{ $item->sku }}</span>
                                        </div>
                                    @endif
                                    <div class="col-sm-6 mb-1">
                                        <span class="text-muted font-weight-500"><i class="fas fa-store text-primary mr-1"></i> {{ __('Sold By') }}:</span>
                                        <a class="font-weight-600 text-primary ml-1" href="{{ route('front.catalog') . '?vendor=' . ($item->vendor_id ?: 'admin') }}">
                                            {{ $item->store_name }}
                                        </a>
                                    </div>
                                    @if ($item->is_returnable == 1)
                                        <div class="col-sm-6 mb-1">
                                            <span class="text-muted font-weight-500"><i class="fas fa-undo-alt text-success mr-1"></i> {{ __('Return Policy') }}:</span>
                                            <span class="font-weight-600 text-success ml-1">
                                                <i class="fas fa-shield-alt mr-1"></i>{{ $item->return_days ?? 14 }} {{ ($item->return_days == 1) ? __('Day') : __('Days') }} {{ __('Easy Return') }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            @if ($item->is_returnable == 1)
                                <div class="return-policy-box d-flex align-items-center mt-2 mb-2 p-2 px-3 rounded" style="background: #f0fdf4; border: 1px solid #86efac;">
                                    <div class="mr-3 text-success" style="font-size: 22px;">
                                        <i class="fas fa-shield-alt"></i>
                                    </div>
                                    <div>
                                        <div class="font-weight-bold text-success" style="font-size: 13.5px; line-height: 1.3;">
                                            {{ $item->return_days ?? 14 }} {{ ($item->return_days == 1) ? __('Day') : __('Days') }} {{ __('Easy Return & Replacement Guarantee') }}
                                        </div>
                                        <div class="text-muted" style="font-size: 11.5px; color: #166534 !important;">
                                            {{ __('Hassle-free return policy if product is damaged, defective, or not as described.') }}
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="mt-4 p-d-f-area">
                                <div class="left d-flex flex-wrap align-items-center gap-2">
                                    <a class="btn btn-primary btn-sm wishlist_store wishlist_text"
                                        href="{{ route('user.wishlist.store', $item->id) }}"><span><i
                                                class="icon-heart"></i></span>
                                        @if (Auth::check() &&
                                                App\Models\Wishlist::where('user_id', Auth::user()->id)->where('item_id', $item->id)->exists())
                                            <span>{{ __('Added To Wishlist') }}</span>
                                        @else
                                            <span class="wishlist1">{{ __('Wishlist') }}</span>
                                            <span class="wishlist2 d-none">{{ __('Added To Wishlist') }}</span>
                                        @endif
                                    </a>

                                    <!-- Chat with Seller Button on Desktop -->
                                    <button type="button" class="btn btn-sm" onclick="openWhatsAppChat()" style="background: #008069; color: #ffffff; border: none; font-weight: 600; padding: 6px 14px; border-radius: 4px; box-shadow: 0 2px 6px rgba(0, 128, 105, 0.35);">
                                        <span><i class="fab fa-whatsapp mr-1" style="font-size: 15px;"></i> {{ __('Chat with Seller') }}</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Message Seller Callout Banner -->
                            <div class="mt-3 p-2 px-3 rounded d-flex align-items-center justify-content-between" style="background: #f0fdf4; border: 1px solid #bbf7d0; cursor: pointer; transition: all 0.2s;" onclick="openWhatsAppChat()">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; background: #008069; color: #fff; margin-right: 10px; font-size: 15px; flex-shrink: 0;">
                                        <i class="fas fa-comment-dots"></i>
                                    </div>
                                    <div>
                                        <span class="font-weight-bold text-dark d-block" style="font-size: 13px;">{{ __('Message Seller about this product') }}</span>
                                        <small class="text-muted" style="font-size: 11.5px;">{{ __('Inquire about size, stock, fast delivery, or discounts directly with :store', ['store' => $item->store_name]) }}</small>
                                    </div>
                                </div>
                                <span class="badge badge-success px-2 py-1 font-weight-bold" style="background: #008069; font-size: 11px;">
                                    <i class="fas fa-paper-plane mr-1"></i> {{ __('Chat Now') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class=" padding-top-3x mb-3" id="details">
                <div class="col-lg-12">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="description-tab" data-bs-toggle="tab"
                                data-bs-target="#description" type="button" role="tab" aria-controls="description"
                                aria-selected="true"><i class="fas fa-file-alt mr-1"></i> {{ __('Product Details') }}</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="specification-tab" data-bs-toggle="tab"
                                data-bs-target="#specification" type="button" role="tab"
                                aria-controls="specification" aria-selected="false"><i class="fas fa-list-ul mr-1"></i> {{ __('Specifications') }}</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="vendor-tab" data-bs-toggle="tab"
                                data-bs-target="#vendor_tab_content" type="button" role="tab"
                                aria-controls="vendor_tab_content" aria-selected="false"><i class="fas fa-store mr-1"></i> {{ __('Store Info') }}</a>
                        </li>
                    </ul>
                    <div class="tab-content card">
                        <div class="tab-pane fade show active" id="description" role="tabpanel"
                            aria-labelledby="description-tab">
                            {!! $item->details !!}
                        </div>
                        <div class="tab-pane fade show" id="specification" role="tabpanel"
                            aria-labelledby="specification-tab">
                            <!-- Mobile Responsive Specifications -->
                            <style>
                                #specification .spec-table {
                                    width: 100%;
                                    border-collapse: collapse;
                                    table-layout: fixed;
                                }
                                #specification .spec-table th,
                                #specification .spec-table td {
                                    padding: 10px 14px;
                                    border: 1px solid #e2e8f0;
                                    word-wrap: break-word;
                                    word-break: break-word;
                                    overflow-wrap: break-word;
                                    vertical-align: middle;
                                    font-size: 14px;
                                }
                                #specification .spec-table th {
                                    width: 40%;
                                    background: #f8fafc;
                                    font-weight: 600;
                                    color: #334155;
                                }
                                #specification .spec-table td {
                                    width: 60%;
                                    color: #475569;
                                }
                                #specification .spec-table .spec-header-row th,
                                #specification .spec-table .spec-header-row td {
                                    background: #64748b;
                                    color: #ffffff;
                                    font-weight: 700;
                                    text-transform: uppercase;
                                    font-size: 13px;
                                }
                                /* Override any global min-width rule from styles.min.css */
                                #details .comparison-table,
                                #specification .comparison-table {
                                    overflow: visible !important;
                                }
                                #details .comparison-table table,
                                #specification .comparison-table table {
                                    width: 100% !important;
                                    min-width: unset !important;
                                }
                                @media (max-width: 575px) {
                                    #specification .spec-table th {
                                        width: 45%;
                                        font-size: 13px;
                                        padding: 8px 10px;
                                    }
                                    #specification .spec-table td {
                                        width: 55%;
                                        font-size: 13px;
                                        padding: 8px 10px;
                                    }
                                }
                            </style>
                            <div style="overflow: visible !important;">
                                <table class="spec-table">
                                    <tbody>
                                        <tr class="spec-header-row">
                                            <th>{{ __('Specification') }}</th>
                                            <td>{{ __('Details') }}</td>
                                        </tr>
                                        @if ($sec_name)
                                            @foreach (array_combine($sec_name, $sec_details) as $sname => $sdetail)
                                                <tr>
                                                    <th>{{ $sname }}</th>
                                                    <td>{{ $sdetail }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr class="text-center">
                                                <td colspan="2" style="text-align:center; color:#94a3b8; padding:24px;">
                                                    <i class="fas fa-info-circle mr-2"></i>{{ __('No Specifications Added') }}
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Store / Seller Details Tab Content -->
                        <div class="tab-pane fade show" id="vendor_tab_content" role="tabpanel"
                            aria-labelledby="vendor-tab">
                            @php
                                $seller = $item->seller;
                                $isVendor = ($item->vendor_id && $item->vendor_id > 0);
                                $storeProductsCount = \App\Models\Item::where('vendor_id', $item->vendor_id)->where('status', 1)->count();
                            @endphp
                            <div class="p-3 p-md-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary text-white mr-3" style="width: 52px; height: 52px; font-size: 22px; margin-right: 15px; box-shadow: 0 3px 8px rgba(13,110,253,0.3);">
                                        <i class="fas fa-store"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-1 font-weight-bold text-dark d-flex align-items-center">
                                            {{ $item->store_name }}
                                            <span class="badge badge-success ml-2 py-1 px-2 font-weight-normal" style="font-size: 11px; margin-left: 8px;">
                                                <i class="fas fa-check-circle mr-1"></i>{{ $isVendor ? __('Verified Seller') : __('Official Store') }}
                                            </span>
                                        </h5>
                                        <small class="text-muted"><i class="fas fa-box-open text-primary mr-1"></i> {{ $storeProductsCount }} {{ __('Product(s) available in this store') }}</small>
                                    </div>
                                </div>
                                <div class="row mt-3 pt-3 border-top">
                                    @if($isVendor && $seller)
                                        @if($seller->shop_address)
                                            <div class="col-12 mb-2">
                                                <span class="text-muted d-block small"><i class="fas fa-map-marker-alt text-danger mr-1"></i> {{ __('Store Location:') }}</span>
                                                <strong class="text-dark">{{ $seller->shop_address }}</strong>
                                            </div>
                                        @endif
                                    @else
                                        <div class="col-12 mb-2">
                                            <p class="text-muted mb-0"><i class="fas fa-shield-alt text-success mr-1"></i> {{ __('This product is sold and fulfilled directly by the Platform.') }}</p>
                                        </div>
                                    @endif
                                </div>
                                <div class="mt-3">
                                    <a href="{{ route('front.catalog') . '?vendor=' . ($item->vendor_id ?: 'admin') }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-store mr-1"></i> {{ __('View More from') }} {{ $item->store_name }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Store / Vendor Banner Card Right After Product Details & Specifications -->
            <div class="col-12 mt-4 mb-2">
                <div class="card shadow-sm border" style="border-radius: 12px; background: #ffffff; border-left: 5px solid #0d6efd !important;">
                    <div class="card-body p-3 p-md-4">
                        <div class="d-flex flex-wrap align-items-center justify-content-between">
                            <div class="d-flex align-items-center mb-2 mb-md-0">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white mr-3" style="width: 52px; height: 52px; background: linear-gradient(135deg, #0d6efd, #0b5ed7); font-size: 22px; margin-right: 15px; box-shadow: 0 4px 10px rgba(13, 110, 253, 0.25);">
                                    <i class="fas fa-store"></i>
                                </div>
                                <div>
                                    <div class="text-uppercase text-muted" style="font-size: 11px; font-weight: 700; letter-spacing: 0.5px;">{{ __('Sold By Store') }}</div>
                                    <h5 class="mb-1 font-weight-bold text-dark d-flex align-items-center">
                                        {{ $item->store_name }}
                                        <span class="badge badge-success ml-2 py-1 px-2" style="font-size: 11px; font-weight: 600; margin-left: 8px;">
                                            <i class="fas fa-check-circle mr-1"></i>{{ ($item->vendor_id && $item->vendor_id > 0) ? __('Verified Store') : __('Official Store') }}
                                        </span>
                                    </h5>
                                    @php
                                        $storeBannerCount = \App\Models\Item::where('vendor_id', $item->vendor_id)->where('status', 1)->count();
                                    @endphp
                                    <small class="text-muted">
                                        <i class="fas fa-box-open text-primary mr-1"></i> {{ $storeBannerCount }} {{ __('Product(s) listed') }}
                                        @if($item->seller && $item->seller->shop_address)
                                            <span class="mx-2 text-muted">•</span> <i class="fas fa-map-marker-alt text-danger mr-1"></i> {{ $item->seller->shop_address }}
                                        @endif
                                    </small>
                                </div>
                            </div>
                            <div>
                                <a href="{{ route('front.catalog') . '?vendor=' . ($item->vendor_id ?: 'admin') }}" class="btn btn-primary btn-sm px-4 py-2 shadow-sm" style="border-radius: 8px; font-weight: 600;">
                                    <i class="fas fa-store-alt mr-1"></i> {{ __('Visit Store') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Reviews Section -->
    <div class="container review-area my-5" id="reviews_section">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-title mb-4">
                    <h2 class="h3 font-weight-bold text-dark"><i class="fas fa-comments text-primary mr-2"></i>{{ __('Customer Reviews & Ratings') }}</h2>
                    <p class="text-muted" style="font-size: 14px;">{{ __('Verified feedback and honest ratings from genuine buyers.') }}</p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8 col-md-7">
                @forelse ($reviews as $review)
                    <div class="card border-0 mb-3 modern-review-card shadow-sm" style="border-radius: 14px; background: #ffffff; border: 1px solid #edf2f7 !important; transition: all 0.2s ease;">
                        <div class="card-body p-3 p-md-4">
                            <div class="d-flex align-items-start">
                                <!-- WhatsApp Style User Avatar -->
                                <div class="mr-3 mr-md-4" style="margin-right: 16px;">
                                    @if ($review->user && $review->user->photo && file_exists('assets/images/' . $review->user->photo))
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
                                                <i class="fas fa-check-circle mr-1"></i> {{ __('Verified Purchase') }}
                                            </span>
                                        </div>
                                        <span class="text-muted small">
                                            <i class="far fa-calendar-alt mr-1"></i> {{ $review->created_at ? $review->created_at->format('M d, Y') : date('M d, Y') }}
                                        </span>
                                    </div>

                                    <!-- Star Ratings -->
                                    <div class="mb-2" style="color: #f59e0b; font-size: 13.5px; letter-spacing: 1px;">
                                        @for ($i = 0; $i < $review->rating; $i++)
                                            <i class="fas fa-star text-warning"></i>
                                        @endfor
                                        @for ($i = $review->rating; $i < 5; $i++)
                                            <i class="far fa-star text-muted" style="opacity: 0.35;"></i>
                                        @endfor
                                    </div>

                                    @if (!empty($review->subject))
                                        <h5 class="font-weight-bold text-dark mb-1" style="font-size: 15px; line-height: 1.3;">{{ $review->subject }}</h5>
                                    @endif

                                    <p class="text-secondary mb-0" style="font-size: 14px; line-height: 1.6; color: #475569 !important;">
                                        {{ $review->review }}
                                    </p>
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
                            <p class="text-muted small mb-3">{{ __('Be the first one to share your feedback and experience with this product.') }}</p>
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
                                $avgRatingScore = $item->rating;
                                $totalRatingCount = $item->rating_count;
                            @endphp
                            <div class="d-flex align-items-center justify-content-center">
                                <span class="font-weight-bold text-dark" style="font-size: 46px; line-height: 1;">{{ number_format($avgRatingScore, 1) }}</span>
                                <span class="text-muted ml-2 font-weight-bold" style="font-size: 18px;">/ 5.0</span>
                            </div>
                            <div class="my-2" style="font-size: 18px;">
                                {!! Helper::renderStarRating($item) !!}
                            </div>
                            <span class="text-muted small font-weight-bold">
                                {{ __('Based on') }} {{ $totalRatingCount }} {{ __('customer ratings') }}
                            </span>
                        </div>

                        <!-- 5 Star Breakdown Bars -->
                        <div class="rating-breakdown-bars">
                            @php
                                $totalReviewsReal = $item->reviews->where('status', 1)->count();
                            @endphp

                            @for ($star = 5; $star >= 1; $star--)
                                @php
                                    $starCount = $item->reviews->where('status', 1)->where('rating', $star)->count();
                                    $starPercent = ($totalReviewsReal > 0) ? round(($starCount / $totalReviewsReal) * 100) : ($star == 5 && $item->is_custom_rating == 1 ? 85 : ($star == 4 && $item->is_custom_rating == 1 ? 15 : 0));
                                @endphp
                                <div class="d-flex align-items-center mb-2" style="font-size: 13px;">
                                    <span class="text-dark font-weight-bold" style="width: 45px;">{{ $star }} <i class="fas fa-star text-warning" style="font-size: 11px;"></i></span>
                                    <div class="progress flex-grow-1 mx-2" style="height: 7px; border-radius: 10px; background-color: #e2e8f0;">
                                        <div class="progress-bar" role="progressbar" style="width: {{ $starPercent }}%; background: linear-gradient(90deg, #f59e0b, #fbbf24); border-radius: 10px;" aria-valuenow="{{ $starPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <span class="text-muted text-right" style="width: 35px;">{{ $starCount }}</span>
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

    @if (count($related_items) > 0)
        <div class="relatedproduct-section container padding-bottom-3x mb-1 s-pt-30">
            <!-- Related Products Carousel-->
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title">
                        <h2 class="h3">{{ __('You May Also Like') }}</h2>
                    </div>
                </div>
            </div>
            <!-- Carousel-->
            <div class="row">
                <div class="col-lg-12">
                    <div class="relatedproductslider owl-carousel">
                        @foreach ($related_items as $related)
                            <div class="slider-item">
                                <div class="product-card">

                                    @if ($related->is_stock())
                                        @if ($related->is_type == 'new')
                                        @else
                                            <div
                                                class="product-badge
                                    @if ($related->is_type == 'feature') bg-warning

                                    @elseif($related->is_type == 'top')
                                    bg-info
                                    @elseif($related->is_type == 'best')
                                    bg-dark
                                    @elseif($related->is_type == 'flash_deal')
                                    bg-success @endif
                                    ">
                                                {{ $related->is_type != 'undefine' ? ucfirst(str_replace('_', ' ', $related->is_type)) : '' }}
                                            </div>
                                        @endif
                                    @else
                                        <div
                                            class="product-badge bg-secondary border-default text-body
                                    ">
                                            {{ __('out of stock') }}</div>
                                    @endif
                                    @if ($related->previous_price && $related->previous_price != 0)
                                        <div class="product-badge product-badge2 bg-info">
                                            -{{ PriceHelper::DiscountPercentage($related) }}</div>
                                    @endif

                                    @if ($related->previous_price && $related->previous_price != 0)
                                        <div class="product-badge product-badge2 bg-info">
                                            -{{ PriceHelper::DiscountPercentage($related) }}</div>
                                    @endif
                                    <div class="product-thumb">
                                        <img class="lazy"
                                            data-src="{{ url('/core/public/storage/images/' . $related->thumbnail) }}"
                                            alt="Product">
                                        <div class="product-button-group">
                                            <a class="product-button wishlist_store"
                                                href="{{ route('user.wishlist.store', $related->id) }}"
                                                title="{{ __('Wishlist') }}"><i class="icon-heart"></i></a>
                                            <a class="product-button product_compare" href="javascript:;"
                                                data-target="{{ route('fornt.compare.product', $related->id) }}"
                                                title="{{ __('Compare') }}"><i class="icon-repeat"></i></a>
                                            @include('includes.item_footer', ['sitem' => $related])
                                        </div>
                                    </div>
                                    <div class="product-card-body">
                                        <div class="product-category"><a
                                                href="{{ route('front.catalog') . '?category=' . $related->category->slug }}">{{ $related->category->name }}</a>
                                        </div>
                                        <h3 class="product-title"><a
                                                href="{{ route('front.product', $related->slug) }}">
                                                {{ Str::limit($related->name, 35) }}
                                            </a></h3>
                                        <h4 class="product-price">
                                            @if ($related->previous_price != 0)
                                                <del>{{ PriceHelper::setPreviousPrice($related->previous_price) }}</del>
                                            @endif
                                            {{ PriceHelper::grandCurrencyPrice($related) }}
                                        </h4>
                                    </div>

                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif




    @auth
        <form class="modal fade ratingForm" action="{{ route('front.review.submit') }}" method="post" id="leaveReview"
            tabindex="-1">
            @csrf
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">{{ __('Leave a Review') }}</h4>
                        <button class="close modal_close" type="button" data-bs-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        @php
                            $user = Auth::user();
                        @endphp
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="review-name">{{ __('Your Name') }}</label>
                                    <input class="form-control" type="text" id="review-name"
                                        value="{{ $user->first_name }}" required>
                                </div>
                            </div>
                            <input type="hidden" name="item_id" value="{{ $item->id }}">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="review-email">{{ __('Your Email') }}</label>
                                    <input class="form-control" type="email" id="review-email"
                                        value="{{ $user->email }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="review-subject">{{ __('Subject') }}</label>
                                    <input class="form-control" type="text" name="subject" id="review-subject" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="review-rating">{{ __('Rating') }}</label>
                                    <select name="rating" class="form-control" id="review-rating">
                                        <option value="5">5 {{ __('Stars') }}</option>
                                        <option value="4">4 {{ __('Stars') }}</option>
                                        <option value="3">3 {{ __('Stars') }}</option>
                                        <option value="2">2 {{ __('Stars') }}</option>
                                        <option value="1">1 {{ __('Star') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="review-message">{{ __('Review') }}</label>
                            <textarea class="form-control" name="review" id="review-message" rows="8" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit"><span>{{ __('Submit Review') }}</span></button>
                    </div>
                </div>
            </div>
        </form>
    @endauth

    <script>
        const itemVariantsMatrix = {!! !empty($variantsData) ? json_encode($variantsData) : '[]' !!};

        function selectVariantPill(btn, attrKey, val, optionId) {
            const $group = $(btn).closest('.variant-option-group');
            $group.find('.variant-pill-btn').removeClass('active');
            $(btn).addClass('active');

            $('#selected_' + attrKey + '_label').text(val);

            const $select = $group.find('select.attribute_option');
            if ($select.length) {
                $select.val(val);
                $select.find('option').prop('selected', false);
                $select.find('option[value="' + val + '"]').prop('selected', true);
                $select.trigger('change');
            }

            updateVariantStockAndCutMarks();
        }

        function updateVariantStockAndCutMarks() {
            if (!itemVariantsMatrix || itemVariantsMatrix.length === 0) {
                // Legacy / Standard options stock check
                let anyOutOfStock = false;
                $('.variant-option-group').each(function() {
                    const $activePill = $(this).find('.variant-pill-btn.active');
                    if ($activePill.length) {
                        const stock = parseInt($activePill.attr('data-stock'));
                        if (!isNaN(stock) && stock <= 0) {
                            anyOutOfStock = true;
                        }
                    }
                });

                if (anyOutOfStock) {
                    disableAddToCart('{{ __("Out of stock") }}');
                } else {
                    enableAddToCart();
                }
                return;
            }

            // We have itemVariantsMatrix
            let activeColor = '';
            let activeSize = '';

            $('.variant-option-group').each(function() {
                const attrKey = $(this).attr('data-attr-name') || '';
                const activeVal = $(this).find('.variant-pill-btn.active').attr('data-val') || '';
                if (attrKey.indexOf('color') !== -1 || attrKey.indexOf('colour') !== -1) {
                    activeColor = activeVal.trim();
                } else if (attrKey.indexOf('size') !== -1) {
                    activeSize = activeVal.trim();
                }
            });

            // 1. If Color is selected, update Size pills out-of-stock styling & cut mark
            if (activeColor !== '') {
                const $sizeGroup = $('.variant-option-group').filter(function() {
                    const k = $(this).attr('data-attr-name') || '';
                    return k.indexOf('size') !== -1;
                });

                if ($sizeGroup.length) {
                    $sizeGroup.find('.variant-pill-btn').each(function() {
                        const sizeVal = ($(this).attr('data-val') || '').trim();
                        const variant = itemVariantsMatrix.find(function(v) {
                            return (v.color || '').trim().toLowerCase() === activeColor.toLowerCase() &&
                                   (v.size || '').trim().toLowerCase() === sizeVal.toLowerCase();
                        });

                        const stock = variant ? parseInt(variant.stock) : 0;
                        if (isNaN(stock) || stock <= 0) {
                            $(this).addClass('out-of-stock');
                            if ($(this).find('.stock-cut-badge').length === 0) {
                                $(this).append('<span class="stock-cut-badge">{{ __("Out of Stock") }}</span>');
                            }
                        } else {
                            $(this).removeClass('out-of-stock');
                            $(this).find('.stock-cut-badge').remove();
                        }
                    });
                }
            }

            // 2. If Size is selected without Color
            if (activeColor === '' && activeSize !== '') {
                const $sizeGroup = $('.variant-option-group').filter(function() {
                    const k = $(this).attr('data-attr-name') || '';
                    return k.indexOf('size') !== -1;
                });
                $sizeGroup.find('.variant-pill-btn').each(function() {
                    const sizeVal = ($(this).attr('data-val') || '').trim();
                    const variant = itemVariantsMatrix.find(function(v) {
                        return (v.size || '').trim().toLowerCase() === sizeVal.toLowerCase();
                    });
                    const stock = variant ? parseInt(variant.stock) : 0;
                    if (isNaN(stock) || stock <= 0) {
                        $(this).addClass('out-of-stock');
                        if ($(this).find('.stock-cut-badge').length === 0) {
                            $(this).append('<span class="stock-cut-badge">{{ __("Out of Stock") }}</span>');
                        }
                    } else {
                        $(this).removeClass('out-of-stock');
                        $(this).find('.stock-cut-badge').remove();
                    }
                });
            }

            // 3. Find combination stock for currently active combination
            let currentVariant = null;
            if (activeColor !== '' && activeSize !== '') {
                currentVariant = itemVariantsMatrix.find(function(v) {
                    return (v.color || '').trim().toLowerCase() === activeColor.toLowerCase() &&
                           (v.size || '').trim().toLowerCase() === activeSize.toLowerCase();
                });
            } else if (activeColor !== '') {
                currentVariant = itemVariantsMatrix.find(function(v) {
                    return (v.color || '').trim().toLowerCase() === activeColor.toLowerCase();
                });
            } else if (activeSize !== '') {
                currentVariant = itemVariantsMatrix.find(function(v) {
                    return (v.size || '').trim().toLowerCase() === activeSize.toLowerCase();
                });
            }

            let variantStock = currentVariant ? parseInt(currentVariant.stock) : 0;
            if (isNaN(variantStock)) variantStock = 0;

            if (currentVariant === null || variantStock <= 0) {
                disableAddToCart('{{ __("Out of stock") }}');
                let combName = (activeColor ? activeColor + ' ' : '') + (activeSize ? activeSize + ' ' : '');
                $('#variant_stock_status').html('<span class="text-danger"><i class="fas fa-times-circle"></i> ' + (combName ? combName : '') + '{{ __("is Out of Stock") }}</span>').show();
            } else {
                enableAddToCart();
                $('#current_stock').val(variantStock);
                $('#variant_stock_status').html('<span class="text-success"><i class="fas fa-check-circle"></i> In Stock (' + variantStock + ' pcs available)</span>').show();
            }
        }

        function disableAddToCart(label) {
            $('#add_to_cart, #but_to_cart').prop('disabled', true).addClass('disabled');
            $('#add_to_cart span').text(label || '{{ __("Out of stock") }}');
            $('#but_to_cart span').text(label || '{{ __("Out of stock") }}');
        }

        function enableAddToCart() {
            $('#add_to_cart, #but_to_cart').prop('disabled', false).removeClass('disabled');
            $('#add_to_cart span').text('{{ __("Add to Cart") }}');
            $('#but_to_cart span').text('{{ __("Buy Now") }}');
        }

        $(document).ready(function() {
            $('.variant-option-group').each(function() {
                const attrKey = $(this).attr('data-attr-name');
                const activeVal = $(this).find('.variant-pill-btn.active').attr('data-val');
                if (activeVal) {
                    $('#selected_' + attrKey + '_label').text(activeVal);
                }
            });

            updateVariantStockAndCutMarks();
        });
    </script>

    @include('front.catalog.inc.whatsapp_chatbox')

@endsection
