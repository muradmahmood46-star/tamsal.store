@extends('master.seller')

@section('styles')
<style>
    .promo-item-row {
        transition: background-color 0.2s ease, border-color 0.2s ease;
    }
    .promo-item-row.selected-row {
        background-color: #f0fdf4 !important;
        border-color: #86efac !important;
    }
    .promo-thumb-img {
        width: 48px;
        height: 48px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
    }
    .badge-promo-tag {
        background: linear-gradient(135deg, #15803d, #16a34a);
        color: #ffffff;
        font-weight: 700;
        letter-spacing: 0.3px;
    }
    .summary-card-sticky {
        position: sticky;
        top: 20px;
        z-index: 10;
    }
    .promo-type-badge {
        font-size: 10px;
    }
    @media (max-width: 767.98px) {
        .promo-item-name {
            font-size: 12px !important;
            line-height: 1.25 !important;
            max-width: 160px !important;
        }
        .promo-thumb-img {
            width: 36px !important;
            height: 36px !important;
            margin-right: 5px !important;
        }
        .promo-item-meta {
            gap: 3px !important;
            margin-top: 1px !important;
            margin-left: 0 !important;
            padding-left: 0 !important;
            justify-content: flex-start !important;
        }
        .promo-type-badge {
            font-size: 8.5px !important;
            padding: 1px 4px !important;
            line-height: 1.1 !important;
            font-weight: 600 !important;
            margin-left: 0 !important;
        }
        .promo-sku-badge {
            font-size: 8.5px !important;
            padding: 1px 3px !important;
        }
        .promo-price-text {
            font-size: 10.5px !important;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between flex-wrap">
                <div class="mb-2 mb-sm-0">
                    <h3 class="mb-0 text-dark"><b><i class="fas fa-bullhorn text-success mr-2"></i> {{ __('Product & Bundle Promotion') }}</b></h3>
                    <p class="text-muted small mb-0">{{ __('Promote your products and bundles with premium highlight badges (e.g. Best Product, Trending) to boost visibility and sales.') }}</p>
                </div>
                <div class="d-flex align-items-center flex-wrap" style="gap: 10px;">
                    <div class="bg-light p-2 px-3 rounded border text-right">
                        <small class="text-muted font-weight-bold d-block" style="font-size: 11px;">{{ __('Current Wallet Balance') }}</small>
                        <strong class="text-success h6 mb-0 font-weight-bold" style="font-size: 15px;">
                            {{ PriceHelper::formatPrice($seller->balance ?? 0) }}
                        </strong>
                    </div>
                    <a href="{{ route('seller.wallet.index') }}" class="btn btn-primary btn-sm font-weight-bold shadow-sm">
                        <i class="fas fa-plus-circle mr-1"></i> {{ __('Add Balance') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    @include('alerts.alerts')

    <!-- Insufficient Balance Notification Alert if triggered -->
    @if(session('insufficient_balance'))
        @php
            $insData = session('insufficient_balance');
        @endphp
        <div class="alert alert-danger shadow-sm border-danger mb-4 p-4" style="border-radius: 10px; border-left: 5px solid #dc3545 !important;">
            <div class="d-flex align-items-start">
                <i class="fas fa-exclamation-triangle fa-2x text-danger mr-3 mt-1"></i>
                <div class="flex-grow-1">
                    <h5 class="font-weight-bold text-danger mb-1">{{ $insData['message'] ?? __('Insufficient amount. Deposit first.') }}</h5>
                    <p class="mb-2 text-dark">
                        {{ __('Your wallet balance is less than the required amount to purchase the selected promotion badges.') }}
                    </p>
                    <div class="d-flex flex-wrap align-items-center mb-3 text-dark" style="gap: 15px; font-size: 13px;">
                        <div><strong>{{ __('Required Amount:') }}</strong> <span class="text-danger font-weight-bold">{{ PriceHelper::formatPrice($insData['required'] ?? 0) }}</span></div>
                        <div><strong>{{ __('Available Balance:') }}</strong> <span class="text-success font-weight-bold">{{ PriceHelper::formatPrice($insData['available'] ?? 0) }}</span></div>
                        <div><strong>{{ __('Shortage Amount:') }}</strong> <span class="text-danger font-weight-bold">{{ PriceHelper::formatPrice($insData['shortage'] ?? 0) }}</span></div>
                    </div>
                    <a href="{{ $insData['deposit_url'] ?? route('seller.wallet.index') }}" class="btn btn-danger font-weight-bold shadow-sm">
                        <i class="fas fa-wallet mr-1"></i> {{ __('Deposit / Add Balance to Wallet Now') }}
                    </a>
                </div>
            </div>
        </div>
    @endif

    <div class="row">
        <!-- Main Form: Product & Bundle Selection -->
        <div class="col-xl-8 col-lg-7 mb-4">
            <form action="{{ route('seller.promotion.purchase') }}" method="POST" id="promotionForm">
                @csrf

                <div class="card shadow-sm border-0 mb-4" style="border-radius: 10px;">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap border-bottom">
                        <div>
                            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-check-square mr-1"></i> {{ __('Select Items to Promote') }}</h6>
                            <small class="text-muted">{{ __('Select one or multiple items, choose a badge tag and duration for each.') }}</small>
                        </div>
                        <div class="mt-2 mt-sm-0">
                            <button type="button" class="btn btn-outline-secondary btn-xs mr-1" onclick="toggleSelectAll(true)">{{ __('Select All') }}</button>
                            <button type="button" class="btn btn-outline-secondary btn-xs" onclick="toggleSelectAll(false)">{{ __('Deselect All') }}</button>
                        </div>
                    </div>

                    <!-- Filter Tabs -->
                    <div class="card-body p-3 bg-light border-bottom">
                        <ul class="nav nav-pills" id="itemFilterPills" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active font-weight-bold py-1 px-3" id="all-tab" data-toggle="pill" href="#tab-all" role="tab">{{ __('All Items') }} <span class="badge badge-light ml-1">{{ $products->count() + $bundles->count() }}</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link font-weight-bold py-1 px-3" id="products-tab" data-toggle="pill" href="#tab-products" role="tab">{{ __('Products Only') }} <span class="badge badge-light ml-1">{{ $products->count() }}</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link font-weight-bold py-1 px-3" id="bundles-tab" data-toggle="pill" href="#tab-bundles" role="tab">{{ __('Bundles Only') }} <span class="badge badge-light ml-1">{{ $bundles->count() }}</span></a>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body p-0">
                        @php
                            $allItems = collect();
                            foreach ($products as $p) {
                                $allItems->push((object)[
                                    'type' => 'product',
                                    'id' => $p->id,
                                    'name' => $p->name,
                                    'sku' => $p->sku,
                                    'price' => $p->discount_price ?: $p->previous_price,
                                    'photo' => $p->photo ?: $p->thumbnail,
                                    'is_promoted' => $p->isPromotionActive(),
                                    'promotion_tag' => $p->promotion_tag,
                                    'promotion_expires_at' => $p->promotion_expires_at,
                                    'created_at' => $p->created_at,
                                ]);
                            }
                            foreach ($bundles as $b) {
                                $allItems->push((object)[
                                    'type' => 'bundle',
                                    'id' => $b->id,
                                    'name' => $b->name,
                                    'sku' => $b->sku,
                                    'price' => $b->discounted_price ?: $b->original_price,
                                    'photo' => $b->photo,
                                    'is_promoted' => $b->isPromotionActive(),
                                    'promotion_tag' => $b->promotion_tag,
                                    'promotion_expires_at' => $b->promotion_expires_at,
                                    'created_at' => $b->created_at,
                                ]);
                            }
                        @endphp

                        @if($allItems->isEmpty())
                            <div class="text-center py-5 text-muted">
                                <i class="fas fa-box-open fa-3x mb-3 text-muted"></i>
                                <h5>{{ __('No products or bundles found in your store.') }}</h5>
                                <p class="text-muted small">{{ __('Please add some products or create bundles first before applying promotions.') }}</p>
                                <a href="{{ route('seller.item.create') }}" class="btn btn-primary btn-sm mr-2"><i class="fas fa-plus mr-1"></i> {{ __('Add Product') }}</a>
                                <a href="{{ route('seller.deal.index') }}" class="btn btn-outline-primary btn-sm"><i class="fas fa-layer-group mr-1"></i> {{ __('Manage Bundles') }}</a>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover mb-0" id="itemsTable">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="width: 40px;" class="text-center">#</th>
                                            <th style="min-width: 170px;">{{ __('Item / Details') }}</th>
                                            <th style="min-width: 140px; width: 150px;">{{ __('Highlight Tag') }} <span class="text-danger">*</span></th>
                                            <th style="min-width: 150px; width: 160px;">{{ __('Duration & Price') }} <span class="text-danger">*</span></th>
                                            <th style="min-width: 90px; width: 100px;" class="text-right">{{ __('Cost') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($allItems as $index => $item)
                                            @php
                                                $imgSrc = $item->photo
                                                    ? (\Illuminate\Support\Str::startsWith($item->photo, 'images/')
                                                        ? url('/core/public/storage/' . $item->photo)
                                                        : url('/core/public/storage/images/' . $item->photo))
                                                    : asset('storage/images/placeholder.png');
                                            @endphp
                                            <tr class="promo-item-row item-type-{{ $item->type }}" id="itemRow{{ $index }}">
                                                <td class="text-center align-middle">
                                                    <input type="hidden" name="promotions[{{ $index }}][type]" value="{{ $item->type }}">
                                                    <input type="hidden" name="promotions[{{ $index }}][id]" value="{{ $item->id }}">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input item-checkbox" id="checkItem{{ $index }}" name="promotions[{{ $index }}][selected]" value="1" onchange="onItemSelectionChanged({{ $index }})">
                                                        <label class="custom-control-label cursor-pointer" for="checkItem{{ $index }}"></label>
                                                    </div>
                                                </td>
                                                <td class="align-middle">
                                                    <div class="d-flex align-items-center">
                                                        <img src="{{ $imgSrc }}" alt="{{ $item->name }}" class="promo-thumb-img mr-2 shadow-sm flex-shrink-0">
                                                        <div class="overflow-hidden">
                                                            <div class="font-weight-bold text-dark text-truncate promo-item-name" style="max-width: 260px;" title="{{ $item->name }}">
                                                                {{ Str::limit($item->name, 30) }}
                                                            </div>
                                                            <div class="d-flex align-items-center flex-wrap promo-item-meta" style="gap: 5px;">
                                                                @if($item->type === 'bundle')
                                                                    <span class="badge badge-info promo-type-badge px-1 py-0">{{ __('Bundle') }}</span>
                                                                @else
                                                                    <span class="badge badge-secondary promo-type-badge px-1 py-0">{{ __('Product') }}</span>
                                                                @endif
                                                                @if($item->sku)
                                                                    <span class="badge badge-light border text-muted promo-sku-badge px-1 py-0 font-weight-normal">SKU: {{ $item->sku }}</span>
                                                                @endif
                                                                <span class="text-success font-weight-bold small promo-price-text">{{ PriceHelper::setCurrencyPrice($item->price) }}</span>
                                                            </div>
                                                            @if($item->is_promoted)
                                                                <div class="mt-1">
                                                                    <span class="badge badge-promo-tag px-2 py-0" style="font-size: 10px;">
                                                                        <i class="fas fa-certificate mr-1"></i> {{ $item->promotion_tag }}
                                                                    </span>
                                                                    <small class="text-muted ml-1" style="font-size: 11px;">
                                                                        {{ __('Active until:') }} {{ $item->promotion_expires_at ? \Carbon\Carbon::parse($item->promotion_expires_at)->format('M d, Y') : '' }}
                                                                    </small>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="align-middle">
                                                    <select name="promotions[{{ $index }}][tag_id]" id="tagSelect{{ $index }}" class="form-control form-control-sm item-tag-select" disabled onchange="calculatePromotionTotals()">
                                                        @foreach($tags as $t)
                                                            <option value="{{ $t->id }}" {{ $loop->first ? 'selected' : '' }}>{{ $t->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td class="align-middle">
                                                    <select name="promotions[{{ $index }}][plan_id]" id="planSelect{{ $index }}" class="form-control form-control-sm item-plan-select" disabled onchange="onPlanChanged({{ $index }})">
                                                        @foreach($plans as $p)
                                                            <option value="{{ $p->id }}" data-price="{{ $p->price }}" data-days="{{ $p->days }}" {{ $loop->first ? 'selected' : '' }}>
                                                                {{ $p->days }} {{ __('Days') }} — {{ PriceHelper::adminCurrency() }} {{ number_format($p->price, 0) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td class="align-middle text-right font-weight-bold text-success" id="itemCostDisplay{{ $index }}">
                                                    -
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <!-- Right Side: Order Summary / Checkout Card -->
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="card shadow-sm border-0 summary-card-sticky" style="border-radius: 12px; overflow: hidden;">
                <div class="card-header bg-dark text-white py-3">
                    <h5 class="m-0 font-weight-bold text-white"><i class="fas fa-receipt text-warning mr-2"></i> {{ __('Promotion Summary') }}</h5>
                </div>
                <div class="card-body p-4 bg-white">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <span class="text-muted">{{ __('Selected Items:') }}</span>
                        <strong class="h6 mb-0 font-weight-bold text-dark" id="summarySelectedCount">0 {{ __('items') }}</strong>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <span class="text-muted">{{ __('Wallet Balance:') }}</span>
                        <strong class="h6 mb-0 font-weight-bold text-success" id="summaryWalletBalance">{{ PriceHelper::formatPrice($seller->balance ?? 0) }}</strong>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                        <span class="font-weight-bold text-dark h6 mb-0">{{ __('Total Cost:') }}</span>
                        <strong class="h4 mb-0 font-weight-bold text-primary" id="summaryTotalCost">{{ PriceHelper::adminCurrency() }} 0.00</strong>
                    </div>

                    <div id="balanceStatusAlert" class="alert alert-info py-2 px-3 small mb-3">
                        <i class="fas fa-info-circle mr-1"></i> {{ __('Tick the checkbox next to any product or bundle to configure its promotion badge.') }}
                    </div>

                    <!-- "Get Badge" Purchase Button -->
                    <button type="button" class="btn btn-success btn-block btn-lg font-weight-bold shadow" id="btnGetBadge" onclick="submitPromotionPurchase()" disabled>
                        <i class="fas fa-certificate mr-2"></i> {{ __('Get Badge') }}
                    </button>

                    <div class="text-center mt-3">
                        <small class="text-muted">
                            <i class="fas fa-shield-alt text-success mr-1"></i> {{ __('Instant Activation • Auto-Expires after duration') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PROMOTION HISTORY TABLE -->
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 10px;">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap">
            <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-history text-info mr-1"></i> {{ __('Promotion History & Active Badges') }}</h6>
            <small class="text-muted">{{ __('Complete log of all promotion badges purchased for your store items.') }}</small>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>{{ __('Item') }}</th>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Badge Tag') }}</th>
                            <th>{{ __('Duration') }}</th>
                            <th>{{ __('Cost Paid') }}</th>
                            <th>{{ __('Dates') }}</th>
                            <th class="text-center">{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($promotionsHistory as $history)
                            @php
                                $isActive = $history->isCurrentlyActive();
                            @endphp
                            <tr>
                                <td class="font-weight-bold">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $history->item_photo }}" alt="Thumb" class="promo-thumb-img mr-2 flex-shrink-0">
                                        <div class="font-weight-bold text-dark" style="max-width: 250px;">
                                            {{ $history->item_title }}
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($history->item_type === 'bundle' || $history->item_type === 'deal')
                                        <span class="badge badge-info">{{ __('Bundle') }}</span>
                                    @else
                                        <span class="badge badge-secondary">{{ __('Product') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-promo-tag px-2 py-1 font-weight-bold" style="font-size: 11px;">
                                        <i class="fas fa-certificate mr-1"></i> {{ $history->tag_name }}
                                    </span>
                                </td>
                                <td>
                                    <span class="font-weight-bold text-dark">{{ $history->days }} {{ __('Days') }}</span>
                                </td>
                                <td>
                                    <strong class="text-danger font-weight-bold">- {{ PriceHelper::adminCurrency() }} {{ number_format($history->price, 2) }}</strong>
                                </td>
                                <td>
                                    <div class="small">
                                        <div><span class="text-muted">{{ __('Started:') }}</span> {{ $history->starts_at ? $history->starts_at->format('d M Y, h:i A') : '-' }}</div>
                                        <div><span class="text-muted">{{ __('Expires:') }}</span> {{ $history->expires_at ? $history->expires_at->format('d M Y, h:i A') : '-' }}</div>
                                    </div>
                                </td>
                                <td class="text-center align-middle">
                                    @if($isActive)
                                        <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i> {{ __('Active') }}</span>
                                        <div class="small text-success font-weight-bold mt-1">
                                            {{ $history->expires_at ? $history->expires_at->diffForHumans() : '' }}
                                        </div>
                                    @else
                                        <span class="badge badge-secondary px-2 py-1"><i class="fas fa-clock mr-1"></i> {{ __('Expired') }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    {{ __('No promotions purchased yet. Select products above to get your first highlight badge!') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($promotionsHistory->hasPages())
                <div class="card-footer py-2">
                    {{ $promotionsHistory->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const sellerBalance = {{ (float)($seller->balance ?? 0) }};
    const currency = "{{ PriceHelper::adminCurrency() }} ";

    function onItemSelectionChanged(index) {
        const checkbox = $('#checkItem' + index);
        const isChecked = checkbox.is(':checked');
        const row = $('#itemRow' + index);
        const tagSelect = $('#tagSelect' + index);
        const planSelect = $('#planSelect' + index);

        if (isChecked) {
            row.addClass('selected-row');
            tagSelect.prop('disabled', false);
            planSelect.prop('disabled', false);
            updateItemCostDisplay(index);
        } else {
            row.removeClass('selected-row');
            tagSelect.prop('disabled', true);
            planSelect.prop('disabled', true);
            $('#itemCostDisplay' + index).text('-');
        }

        calculatePromotionTotals();
    }

    function onPlanChanged(index) {
        updateItemCostDisplay(index);
        calculatePromotionTotals();
    }

    function updateItemCostDisplay(index) {
        const planSelect = $('#planSelect' + index);
        const selectedOption = planSelect.find('option:selected');
        const price = parseFloat(selectedOption.data('price')) || 0;
        $('#itemCostDisplay' + index).text(currency + price.toFixed(2));
    }

    function calculatePromotionTotals() {
        let totalCount = 0;
        let totalCost = 0.0;

        $('.item-checkbox:checked').each(function() {
            const index = $(this).attr('id').replace('checkItem', '');
            const planSelect = $('#planSelect' + index);
            const price = parseFloat(planSelect.find('option:selected').data('price')) || 0;
            totalCost += price;
            totalCount++;
        });

        $('#summarySelectedCount').text(totalCount + ' ' + (totalCount === 1 ? 'item' : 'items'));
        $('#summaryTotalCost').text(currency + totalCost.toFixed(2));

        const alertBox = $('#balanceStatusAlert');
        const btn = $('#btnGetBadge');

        if (totalCount === 0) {
            alertBox.attr('class', 'alert alert-info py-2 px-3 small mb-3')
                .html('<i class="fas fa-info-circle mr-1"></i> {{ __("Tick the checkbox next to any product or bundle to configure its promotion badge.") }}');
            btn.prop('disabled', true);
            return;
        }

        if (sellerBalance >= totalCost) {
            const remaining = sellerBalance - totalCost;
            alertBox.attr('class', 'alert alert-success py-2 px-3 small mb-3')
                .html('<i class="fas fa-check-circle mr-1"></i> <strong>{{ __("Sufficient Balance!") }}</strong> {{ __("Remaining balance after purchase:") }} <strong>' + currency + remaining.toFixed(2) + '</strong>');
            btn.prop('disabled', false);
        } else {
            const shortage = totalCost - sellerBalance;
            alertBox.attr('class', 'alert alert-danger py-2 px-3 small mb-3')
                .html('<i class="fas fa-exclamation-triangle mr-1"></i> <strong>{{ __("Insufficient amount. Deposit first.") }}</strong><br>{{ __("Shortage:") }} <strong class="text-danger">' + currency + shortage.toFixed(2) + '</strong> <a href="{{ route("seller.wallet.index") }}" class="btn btn-danger btn-xs ml-2 font-weight-bold">{{ __("Add Balance") }}</a>');
            btn.prop('disabled', false); // Allow clicking to show proper warning / redirection flow
        }
    }

    function toggleSelectAll(select) {
        $('.item-checkbox').each(function() {
            const index = $(this).attr('id').replace('checkItem', '');
            // Only toggle visible items based on current tab
            const row = $('#itemRow' + index);
            if (row.is(':visible')) {
                $(this).prop('checked', select);
                onItemSelectionChanged(index);
            }
        });
    }

    function submitPromotionPurchase() {
        const count = $('.item-checkbox:checked').length;
        if (count === 0) {
            alert('{{ __("Please select at least one item to promote.") }}');
            return;
        }

        let totalCost = 0.0;
        $('.item-checkbox:checked').each(function() {
            const index = $(this).attr('id').replace('checkItem', '');
            const price = parseFloat($('#planSelect' + index).find('option:selected').data('price')) || 0;
            totalCost += price;
        });

        if (sellerBalance < totalCost) {
            if (confirm('{{ __("Insufficient wallet balance. Do you want to go to Wallet & Balance to add funds now?") }}')) {
                window.location.href = "{{ route('seller.wallet.index') }}";
            }
            return;
        }

        if (confirm('{{ __("Confirm purchasing promotion badges for ") }}' + count + ' {{ __("item(s) for a total of ") }}' + currency + totalCost.toFixed(2) + '? {{ __("Amount will be deducted from your wallet balance.") }}')) {
            $('#promotionForm').submit();
        }
    }

    // Filter Tabs
    $(document).ready(function() {
        $('#all-tab').on('click', function() {
            $('.promo-item-row').show();
        });
        $('#products-tab').on('click', function() {
            $('.promo-item-row.item-type-product').show();
            $('.promo-item-row.item-type-bundle').hide();
        });
        $('#bundles-tab').on('click', function() {
            $('.promo-item-row.item-type-bundle').show();
            $('.promo-item-row.item-type-product').hide();
        });
    });
</script>
@endsection
