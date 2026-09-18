<aside class="sidebar">
    <div class="padding-top-2x hidden-lg-up"></div>
    <!-- Items in Cart Widget-->


    <section class="card widget widget-featured-posts widget-order-summary p-4">
        <h3 class="widget-title">{{ __('Order Summary') }}</h3>
        @php
            $free_shipping = DB::table('shipping_services')->whereStatus(1)->whereIsCondition(1)->first();
        @endphp

        @if ($free_shipping)
            @if ($free_shipping->minimum_price > 0 && $free_shipping->minimum_price > $cart_total)
                <p class="free-shippin-aa mb-2" style="font-size: 13px; color: #15803d;"><i class="fas fa-truck mr-1"></i><em>{{ __('Free Delivery on orders above') }}
                        {{ PriceHelper::setCurrencyPrice($free_shipping->minimum_price) }}</em></p>
            @endif
        @endif

        <table class="table">
            <tr>
                <td>{{ __('Cart subtotal') }}:</td>
                <td class="text-gray-dark">{{ PriceHelper::setCurrencyPrice($cart_total) }}</td>
            </tr>

            @if ($tax != 0)
                <tr>
                    <td>{{ __('Estimated tax') }}:</td>
                    <td class="text-gray-dark">{{ PriceHelper::setCurrencyPrice($tax) }}</td>
                </tr>
            @endif

            @if (DB::table('states')->count() > 0)
                <tr class="{{ Auth::check() && Auth::user()->state_id ? '' : 'd-none' }} set__state_price_tr">
                    <td>{{ __('State tax') }}:</td>
                    <td class="text-gray-dark set__state_price">
                        {{ PriceHelper::setCurrencyPrice(Auth::check() && Auth::user()->state_id ? ($cart_total * Auth::user()->state->price) / 100 : 0) }}
                    </td>
                </tr>
            @endif

            @if ($discount)
                <tr>
                    <td>{{ __('Coupon discount') }}:</td>
                    <td class="text-danger">-
                        {{ PriceHelper::setCurrencyPrice($discount ? $discount['discount'] : 0) }}</td>
                </tr>
            @endif

            @if (PriceHelper::CheckDigital() == true)
                @php
                    $curr_cart = $cart ?? Session::get('cart');
                    $calcDeliveryFee = isset($delivery_fee) ? $delivery_fee : PriceHelper::getDeliveryFee($curr_cart, $cart_total ?? null, false);
                    
                    $delivery_fee_details = [];
                    if (!empty($curr_cart) && is_array($curr_cart)) {
                        $curr_val = PriceHelper::setCurrencyValue();
                        $processedDealIds = [];
                        foreach ($curr_cart as $key => $cItem) {
                            $itemId = explode('-', $key)[0];
                            $product = \App\Models\Item::find($itemId);
                            $itemFee = 0;
                            $isFree = false;

                            // Deal item - use bundle's own delivery setting
                            if (!empty($cItem['deal_id'])) {
                                $dealId = (int)$cItem['deal_id'];
                                if (in_array($dealId, $processedDealIds)) continue;
                                $processedDealIds[] = $dealId;
                                $isFree = !empty($cItem['deal_free_delivery']);
                                $itemFee = $isFree ? 0 : (float)($cItem['deal_delivery_charge'] ?? 0);
                                $bundleName = !empty($cItem['deal_name']) ? $cItem['deal_name'] : ($cItem['name'] ?? 'Bundle');
                                $delivery_fee_details[] = [
                                    'name' => __('Bundle') . ': ' . $bundleName,
                                    'fee'  => $itemFee,
                                    'is_free' => ($isFree || $itemFee == 0),
                                ];
                                continue;
                            }

                            if ($product) {
                                if ($product->is_free_delivery == 1) {
                                    $isFree = true;
                                } elseif ($product->delivery_fee > 0) {
                                    $itemFee = $curr_val > 0 ? ($product->delivery_fee / $curr_val) : $product->delivery_fee;
                                } else {
                                    $isFree = true;
                                }
                            }
                            $delivery_fee_details[] = [
                                'name' => $cItem['name'] ?? ($product->name ?? 'Product'),
                                'fee' => $itemFee,
                                'is_free' => $isFree
                            ];
                        }
                    }
                @endphp
                <tr>
                    <td colspan="2" class="p-0 border-0">
                        <div class="p-2 my-2 rounded" style="background-color: #f0fdf4; border: 1px dashed #22c55e;">
                            <div class="d-flex justify-content-between align-items-center mb-1 pb-1" style="border-bottom: 1px solid #bbf7d0;">
                                <span class="font-weight-bold text-success" style="font-size: 12px;">
                                    <i class="fas fa-truck mr-1"></i> {{ __('Delivery Charges') }}
                                </span>
                            </div>
                            @if(count($delivery_fee_details) > 0)
                                @foreach($delivery_fee_details as $dDetail)
                                    <div class="d-flex justify-content-between align-items-center py-1" style="border-bottom: 1px dotted #bbf7d0; text-align: left;">
                                        <div style="text-align: left; flex: 1;">
                                            <div class="font-weight-bold text-dark text-left" style="font-size: 12px; text-align: left !important; margin: 0; padding: 0;">• {{ \Illuminate\Support\Str::limit($dDetail['name'], 22) }}</div>
                                        </div>
                                        <div class="sidebar-item-delivery-val text-right" style="font-size: 12px; white-space: nowrap; text-align: right;" data-is-free="{{ ($dDetail['is_free'] || $dDetail['fee'] == 0) ? '1' : '0' }}" data-orig-text="{{ ($dDetail['is_free'] || $dDetail['fee'] == 0) ? '' : PriceHelper::setCurrencyPrice($dDetail['fee']) }}">
                                            @if($dDetail['is_free'] || $dDetail['fee'] == 0)
                                                <span class="badge badge-success px-2 py-0" style="font-size: 10.5px;">{{ __('Free Delivery') }}</span>
                                            @else
                                                <span class="text-dark font-weight-bold">{{ PriceHelper::setCurrencyPrice($dDetail['fee']) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                            <div class="d-flex justify-content-between align-items-center pt-2" style="font-size: 12px;">
                                <span class="font-weight-bold text-dark text-left" style="text-align: left !important;">{{ __('Total Delivery Charges') }}:</span>
                                <span id="sidebar_total_delivery_fee_display" class="font-weight-bold {{ $calcDeliveryFee == 0 ? 'text-success' : 'text-dark' }}" style="font-size: 13px;" data-orig-text="{{ $calcDeliveryFee == 0 ? '' : PriceHelper::setCurrencyPrice($calcDeliveryFee) }}">
                                    @if($calcDeliveryFee == 0)
                                        <span class="badge badge-success px-2 py-1" style="font-size: 11.5px;">{{ __('Free Delivery') }}</span>
                                    @else
                                        {{ PriceHelper::setCurrencyPrice($calcDeliveryFee) }}
                                    @endif
                                </span>
                            </div>
                        </div>
                    </td>
                </tr>
            @endif

            @if(isset($has_advance_payment) && $has_advance_payment)
                <tr id="advance_discount_row" style="display: none;">
                    <td colspan="2" class="p-0 border-0">
                        <div class="p-2 my-2 rounded" style="background-color: #fff5f5; border: 1px dashed #e53e3e; text-align: left;">
                            <div class="d-flex justify-content-between align-items-center mb-1 pb-1" style="border-bottom: 1px solid #fed7d7; text-align: left;">
                                <span class="font-weight-bold text-danger text-left" style="font-size: 12px; text-align: left !important;">
                                    <i class="fas fa-gift mr-1"></i> {{ __('Advance Discount Offer') }}
                                </span>
                            </div>
                            @if(count($advance_discount_details) > 0)
                                @foreach($advance_discount_details as $detail)
                                    @if($detail['discount'] > 0)
                                        <div class="d-flex justify-content-between align-items-start py-1" style="border-bottom: 1px dotted #fed7d7; text-align: left;">
                                            <div style="text-align: left; flex: 1; padding-right: 6px;">
                                                <div class="font-weight-bold text-dark text-left" style="font-size: 12px; text-align: left !important; margin: 0; padding: 0;">• {{ \Illuminate\Support\Str::limit($detail['name'], 30) }}</div>
                                                <div class="text-muted text-left" style="font-size: 11px; text-align: left !important; margin: 0; padding: 0;">{{ __('Price') }}: {{ PriceHelper::setCurrencyPrice($detail['price']) }}</div>
                                            </div>
                                            <div class="text-danger font-weight-bold text-right" style="font-size: 12px; white-space: nowrap; text-align: right;">
                                                -{{ PriceHelper::setCurrencyPrice($detail['discount']) }}
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                            <div class="d-flex justify-content-between align-items-center pt-2" style="font-size: 12px;">
                                <span class="font-weight-bold text-dark text-left" style="text-align: left !important;">{{ __('Total Advance Discount') }}:</span>
                                <span class="font-weight-bold text-danger" style="font-size: 13px;">-{{ PriceHelper::setCurrencyPrice($total_advance_discount) }}</span>
                            </div>
                        </div>
                    </td>
                </tr>
            @endif

            <tr>
                <td class="text-lg text-primary">{{ __('Order total') }}</td>
                <td class="text-lg text-primary grand_total_set cart-total-display">{{ PriceHelper::setCurrencyPrice($grand_total) }}
                </td>
            </tr>
        </table>
    </section>


    <section class="card widget widget-featured-posts widget-featured-products p-4">
        <h3 class="widget-title">{{ __('Items In Your Cart') }}</h3>
        @php
            $bundleGroups = [];
            $standaloneItems = [];
            foreach ($cart as $key => $item) {
                if (!empty($item['deal_id'])) {
                    $dealId = $item['deal_id'];
                    $bundleGroups[$dealId]['deal_name'] = $item['deal_name'] ?? __('Bundle Deal');
                    $bundleGroups[$dealId]['items'][$key] = $item;
                } else {
                    $standaloneItems[$key] = $item;
                }
            }
        @endphp

        @foreach($bundleGroups as $dealId => $group)
            <div class="mb-3 p-2 rounded" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                <div class="d-flex align-items-center mb-2 pb-1" style="border-bottom: 2px solid #007bff;">
                    <span class="badge badge-primary mr-2" style="font-size: 11px;"><i class="fas fa-layer-group"></i> {{ __('Bundle') }}</span>
                    <strong class="text-dark" style="font-size: 13px;">{{ $group['deal_name'] }}</strong>
                </div>
                @foreach($group['items'] as $key => $item)
                    <div class="entry pl-2 mb-2 pb-2" style="border-bottom: 1px dotted #e2e8f0;">
                        <div class="entry-thumb"><a href="{{ route('front.product', $item['slug']) }}"><img
                                    src="{{ url('/core/public/storage/images/' . $item['photo']) }}" alt="Product"></a>
                        </div>
                        <div class="entry-content">
                            <h4 class="entry-title"><a href="{{ route('front.product', $item['slug']) }}">
                                    {{ Str::limit($item['name'], 45) }}
                                </a></h4>
                            <span class="entry-meta">{{ $item['qty'] }} x
                                {{ PriceHelper::setCurrencyPrice($item['main_price']) }}</span>

                            @if(isset($item['attribute']['option_name']))
                                @foreach ($item['attribute']['option_name'] as $optionkey => $option_name)
                                    <span class="entry-meta"><b>{{ $option_name }}</b> :
                                        {{ PriceHelper::setCurrencySign() }}{{ $item['attribute']['option_price'][$optionkey] ?? '' }}</span>
                                @endforeach
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach

        @foreach ($standaloneItems as $key => $item)
            <div class="entry">
                <div class="entry-thumb"><a href="{{ route('front.product', $item['slug']) }}"><img
                            src="{{ url('/core/public/storage/images/' . $item['photo']) }}" alt="Product"></a>
                </div>
                <div class="entry-content">
                    <h4 class="entry-title"><a href="{{ route('front.product', $item['slug']) }}">
                            {{ Str::limit($item['name'], 45) }}
                        </a></h4>
                    <span class="entry-meta">{{ $item['qty'] }} x
                        {{ PriceHelper::setCurrencyPrice($item['main_price']) }}</span>

                    @if(isset($item['attribute']['option_name']))
                        @foreach ($item['attribute']['option_name'] as $optionkey => $option_name)
                            <span class="entry-meta"><b>{{ $option_name }}</b> :
                                {{ PriceHelper::setCurrencySign() }}{{ $item['attribute']['option_price'][$optionkey] ?? '' }}</span>
                        @endforeach
                    @endif
                </div>
            </div>
        @endforeach
    </section>

</aside>
