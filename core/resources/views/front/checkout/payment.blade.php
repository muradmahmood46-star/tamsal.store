@extends('master.front')
@section('title')
    {{ __('Payment') }}
@endsection
@section('content')
    <!-- Page Title-->
    <div class="page-title">
        <div class="container">
            <div class="column">
                <ul class="breadcrumbs">
                    <li><a href="{{ route('front.index') }}">{{ __('Home') }}</a> </li>
                    <li class="separator"></li>
                    <li>{{ __('Review your order and pay') }}</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- Page Content-->
    <div class="container padding-bottom-3x mb-1 checkut-page">
        <div class="row">
            <!-- Payment Methode-->
            <div class="col-xl-9 col-lg-8">
                <div class="steps flex-sm-nowrap mb-5"> <a class="step" href="{{ route('front.checkout.billing') }}">
                        <h4 class="step-title"><i class="icon-check-circle"></i>1. {{ __('Invoice to') }}:</h4>
                    </a> <a class="step" href="{{ route('front.checkout.shipping') }}">
                        <h4 class="step-title"><i class="icon-check-circle"></i>2. {{ __('Ship to') }}:</h4>
                    </a> <a class="step active" href="{{ route('front.checkout.payment') }}">
                        <h4 class="step-title">3. {{ __('Review and pay') }}</h4>
                    </a>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h6 class="pb-2 widget-title2">{{ __('Review Your Order') }} :</h6>
                        
                        <div class="row">
                            <div class="col-sm-6 mb-4">
                                <h6 class="fz-16-bold">{{ __('Invoice address') }} :</h6>
                                @php

                                    $ship = Session::get('shipping_address');
                                    $bill = Session::get('billing_address');
                                @endphp
                                <ul class="list-unstyled">
                                    <li><span class="text-muted pay-label">{{ __('Name') }}:
                                        </span>{{ $ship['ship_first_name'] ?? ($bill['bill_first_name'] ?? '') }} {{ $ship['ship_last_name'] ?? ($bill['bill_last_name'] ?? '') }}</li>
                                    @if (PriceHelper::CheckDigital())
                                        <li><span class="text-muted pay-label">{{ __('Address') }}:
                                            </span>{{ $ship['ship_address1'] ?? ($bill['bill_address1'] ?? '') }} {{ $ship['ship_address2'] ?? ($bill['bill_address2'] ?? '') }}</li>
                                    @endif
                                    <li><span class="text-muted pay-label">{{ __('Phone') }}: </span>{{ $ship['ship_phone'] ?? ($bill['bill_phone'] ?? '') }}
                                    </li>
                                </ul>
                            </div>
                            <div class="col-sm-6  mb-4">
                                <h6 class="fz-16-bold">{{ __('Shipping address') }} :</h6>
                                <ul class="list-unstyled">
                                    <li><span class="text-muted pay-label">{{ __('Name') }}:
                                        </span>{{ $bill['bill_first_name'] ?? ($ship['ship_first_name'] ?? '') }} {{ $bill['bill_last_name'] ?? ($ship['ship_last_name'] ?? '') }}</li>
                                    @if (PriceHelper::CheckDigital())
                                        <li><span class="text-muted pay-label">{{ __('Address') }}:
                                            </span>{{ $ship['ship_address1'] ?? ($bill['bill_address1'] ?? '') }} {{ $ship['ship_address2'] ?? ($bill['bill_address2'] ?? '') }}</li>
                                    @endif
                                    <li><span class="text-muted pay-label">{{ __('Phone') }}: </span>{{ $bill['bill_phone'] ?? ($ship['ship_phone'] ?? '') }}
                                    </li>
                                </ul>

                              
                               
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 mb-4">
                                @if (PriceHelper::CheckDigital() == true)
                                    @php
                                        $freeShippingOffer = DB::table('shipping_services')->where('id', 1)->first() ?? DB::table('shipping_services')->where('is_condition', 1)->first();
                                        $isFreeDeliveryByThreshold = ($freeShippingOffer && ($freeShippingOffer->is_condition == 1 || $freeShippingOffer->status == 1) && $freeShippingOffer->minimum_price > 0 && $cart_total >= $freeShippingOffer->minimum_price);
                                        $actual_fee_calc = isset($actual_delivery_fee) ? $actual_delivery_fee : PriceHelper::getDeliveryFee($cart, $cart_total, false);
                                    @endphp
                                    <input type="hidden" name="shipping_id" id="shipping_id_select" value="1">
                                    <div id="top_delivery_fee_card_wrap" class="p-3 rounded border" style="background: #f8fafc; border-color: #e2e8f0;">
                                        <div id="top_delivery_fee_display_box" class="d-flex align-items-center">
                                            @if($actual_fee_calc == 0)
                                                <i class="fas fa-truck text-success mr-2" style="font-size: 18px;"></i>
                                                <div>
                                                    <span class="badge badge-success px-2 py-1" style="font-size: 13px;">{{ __('Free Delivery') }}</span>
                                                    <span class="text-muted ml-1" style="font-size: 13px;">({{ PriceHelper::setCurrencyPrice(0) }})</span>
                                                </div>
                                            @else
                                                <i class="fas fa-truck text-primary mr-2" style="font-size: 18px;"></i>
                                                <div>
                                                    <strong class="text-dark" style="font-size: 14px;">{{ __('Delivery Fee') }}:</strong>
                                                    <span class="text-primary font-weight-bold ml-1" style="font-size: 15px;">{{ PriceHelper::setCurrencyPrice($actual_fee_calc) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="col-sm-6 mb-4">
                                @if (PriceHelper::CheckDigital() == true)
                                    @if (DB::table('states')->whereStatus(1)->count() > 0)
                                        <select name="state_id" class="form-control" id="state_id_select" required>
                                            <option value="" selected disabled>{{ __('Select Shipping State') }}</option>
                                            @foreach (DB::table('states')->whereStatus(1)->get() as $state)
                                                <option value="{{ $state->id }}"
                                                    data-href="{{ route('front.state.setup') }}"
                                                    {{ Auth::check() && Auth::user()->state_id == $state->id ? 'selected' : '' }}>
                                                    {{ $state->name }}
                                                    @if ($state->type == 'fixed')
                                                        ({{ PriceHelper::setCurrencyPrice($state->price) }})
                                                    @else
                                                        ({{ $state->price }}%)
                                                    @endif

                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="text-primary state_message">{{ __('Please select shipping state') }}</small>
                                        @error('state_id')
                                            <p class="text-danger state_message">{{ $message }}</p>
                                        @enderror
                                    @endif
                                @endif
                            </div>
                        </div>

                        @if (PriceHelper::CheckDigital() == true && $isFreeDeliveryByThreshold && $actual_fee_calc > 0)
                        <div class="card mb-4" id="free_delivery_offer_card" style="border: 2px solid #22c55e; transition: all 0.3s ease;">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-1" style="color: #15803d;"><strong><i class="fas fa-truck mr-2"></i>{{ __('Free Delivery Offer') }}</strong></h5>
                                    <p class="mb-0 text-muted" style="font-size: 13px;">{{ __('Turn ON to apply Free Delivery because your order is more than') }} <strong class="text-dark">{{ PriceHelper::setCurrencyPrice($freeShippingOffer->minimum_price) }}</strong>.</p>
                                    <div id="free_delivery_success_msg" class="mt-2 text-success font-weight-bold d-none" style="display: none; font-size: 14px;">
                                        <i class="fas fa-check-circle mr-1"></i> {{ __('Free Delivery Applied!') }} {{ __('(You saved') }} {{ PriceHelper::setCurrencyPrice($actual_fee_calc) }})
                                    </div>
                                </div>
                                <label class="switch-primary mb-0" style="transform: scale(1.2);">
                                    <input type="checkbox" class="switch switch-bootstrap status radio-check" id="free_delivery_toggle" onchange="toggleFreeDelivery(this)">
                                    <span class="switch-body"></span>
                                </label>
                            </div>
                        </div>
                        @endif

                        @php
                            $total_advance_discount = 0;
                            $advance_discount_details = [];
                            $has_advance_payment = false;
                            $default_currency = \App\Models\Currency::where('is_default', 1)->first();
                            $curr_val = PriceHelper::setCurrencyValue();

                            if($cart) {
                                $processedDeals = [];
                                foreach($cart as $key => $item) {
                                    if (!empty($item['deal_id'])) {
                                        $dealId = $item['deal_id'];
                                        if (!isset($processedDeals[$dealId])) {
                                            $dealAdv = (float)($item['deal_advance_discount'] ?? 0);
                                            if ($dealAdv <= 0 && class_exists(\App\Models\Deal::class)) {
                                                $d = \App\Models\Deal::find($dealId);
                                                if ($d && $d->advance_discount > 0) {
                                                    $dealAdv = (float)$d->advance_discount;
                                                }
                                            }
                                            $processedDeals[$dealId] = [
                                                'name' => $item['deal_name'] ?? __('Bundle Deal'),
                                                'total_price' => 0,
                                                'advance_discount' => $dealAdv
                                            ];
                                        }
                                        $item_total_price = ($item['main_price'] + ($item['attribute_price'] ?? 0)) * $item['qty'];
                                        $processedDeals[$dealId]['total_price'] += $item_total_price;
                                        continue;
                                    }

                                    $itemId = explode('-', $key)[0];
                                    $product = \App\Models\Item::find($itemId);
                                    $item_total_price = ($item['main_price'] + ($item['attribute_price'] ?? 0)) * $item['qty'];
                                    if ($product && $product->advance_payment_amount > 0) {
                                        $has_advance_payment = true;
                                        if ($product->advance_payment_type == 'percentage') {
                                            $adv_discount = ($item_total_price * $product->advance_payment_amount) / 100;
                                        } else {
                                            $adv_discount = ($curr_val > 0 ? ($product->advance_payment_amount / $curr_val) : $product->advance_payment_amount) * $item['qty'];
                                        }
                                        $total_advance_discount += $adv_discount;
                                        $advance_discount_details[] = [
                                            'name' => $item['name'] ?? ($product->name ?? 'Product'),
                                            'price' => $item_total_price,
                                            'qty' => $item['qty'],
                                            'discount' => $adv_discount
                                        ];
                                    }
                                }

                                foreach ($processedDeals as $dealId => $dInfo) {
                                    if ($dInfo['advance_discount'] > 0) {
                                        $has_advance_payment = true;
                                        $adv_discount = ($curr_val > 0 ? ($dInfo['advance_discount'] / $curr_val) : $dInfo['advance_discount']);
                                        $total_advance_discount += $adv_discount;
                                        $advance_discount_details[] = [
                                            'name' => $dInfo['name'],
                                            'price' => $dInfo['total_price'],
                                            'qty' => 1,
                                            'discount' => $adv_discount
                                        ];
                                    }
                                }
                            }
                        @endphp
                        
                        @php
                            $isVendorCart = PriceHelper::isCartHasVendorProducts($cart);
                        @endphp

                        @if(!$isVendorCart && $has_advance_payment)
                        <div class="card mb-4" id="advance_payment_offer_card" style="border: 2px solid #0d6efd; transition: all 0.3s ease;">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-1" style="color: #0d6efd;"><strong><i class="fas fa-gift mr-2"></i>{{ __('Advance Payment Offer') }}</strong></h5>
                                    <p class="mb-0 text-muted" style="font-size: 13px;">{{ __('Turn ON to apply special discounts for paid payment methods (Cannot be used with Cash on Delivery).') }}</p>
                                    <div id="adv_discount_success_msg" class="mt-2 text-success font-weight-bold d-none" style="display: none; font-size: 14px;">
                                        <i class="fas fa-check-circle mr-1"></i> {{ __('Discount Applied') }}: -{{ PriceHelper::setCurrencyPrice($total_advance_discount) }}
                                    </div>
                                    <div id="adv_toggle_alert_msg" class="mt-2 text-danger font-weight-bold d-none" style="display: none; font-size: 13px;">
                                        <i class="fas fa-hand-point-right mr-1"></i> {{ __('Please turn ON this switch to accept your discount benefits!') }}
                                    </div>
                                </div>
                                <label class="switch-primary mb-0" style="transform: scale(1.2);">
                                    <input type="checkbox" class="switch switch-bootstrap status radio-check" id="advance_payment_toggle" onchange="toggleAdvancePayment(this)">
                                    <span class="switch-body"></span>
                                </label>
                            </div>
                        </div>
                        @endif

                        <h6 class="pb-2 widget-title2">{{ __('Pay') }} (<span class="pay_amount_display" style="color: purple; font-weight: 700;">{{ PriceHelper::setCurrencyPrice($grand_total) }}</span>) {{ __('With') }} :</h6>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="payment-methods">
                                    @php
                                        $gateways = PriceHelper::getCheckoutPaymentGateways($cart);
                                        $isSingleGateway = ($gateways->count() === 1);
                                    @endphp
                                     @foreach ($gateways as $gateway)
                                         @if($gateway->unique_keyword == 'cod')
                                             @if($isSingleGateway)
                                                 {{-- Sole payment method: Featured Animated & Highlighted COD Card --}}
                                                 <div class="single-payment-method single-cod-method" id="gateway_wrap_cod">
                                                     <a class="text-decoration-none cod-animated-card" href="javascript:void(0);" onclick="handlePaymentGatewayClick('cod')">
                                                         <div class="cod-glow-badge">
                                                             <span class="cod-badge-text"><i class="fas fa-hand-pointer mr-1 cod-hand-pulse"></i> {{ __('Click to Confirm Order') }}</span>
                                                         </div>
                                                         <div class="cod-card-body">
                                                             <div class="cod-icon-wrap">
                                                                 <img src="{{ url('/core/public/storage/images/' . $gateway->photo) }}" alt="{{ $gateway->name }}" title="{{ $gateway->name }}">
                                                             </div>
                                                             <h5 class="cod-title mb-1">{{ $gateway->name }}</h5>
                                                             <p class="cod-subtext mb-2 text-muted">{{ __('Pay cash at your doorstep when delivery arrives') }}</p>
                                                             <div class="cod-action-btn">
                                                                 <i class="fas fa-check-circle mr-1"></i> {{ __('Place Order (COD)') }} <i class="fas fa-arrow-right ml-1 cod-arrow-bounce"></i>
                                                             </div>
                                                         </div>
                                                     </a>
                                                 </div>
                                             @else
                                                 {{-- Multiple payment methods: Simple Plain COD matching other gateways (No coloring / animation) --}}
                                                 <div class="single-payment-method" id="gateway_wrap_cod">
                                                     <a class="text-decoration-none" href="javascript:void(0);" onclick="handlePaymentGatewayClick('cod')">
                                                         <img class=""
                                                             src="{{ url('/core/public/storage/images/' . $gateway->photo) }}"
                                                             alt="{{ $gateway->name }}" title="{{ $gateway->name }}">
                                                         <p>{{ $gateway->name }}</p>
                                                     </a>
                                                 </div>
                                             @endif
                                         @else
                                             <div class="single-payment-method" id="gateway_wrap_{{ $gateway->unique_keyword }}">
                                                 <a class="text-decoration-none" href="javascript:void(0);" onclick="handlePaymentGatewayClick('{{ $gateway->unique_keyword }}')">
                                                     <img class=""
                                                         src="{{ url('/core/public/storage/images/' . $gateway->photo) }}"
                                                         alt="{{ $gateway->name }}" title="{{ $gateway->name }}">
                                                     <p>{{ $gateway->name }}</p>
                                                 </a>
                                             </div>
                                         @endif
                                     @endforeach

                                 </div>
                             </div>
                         </div>

                         <style>
                             /* -------------------------------------------------------------
                                Interactive & Animated Cash On Delivery Box
                                ------------------------------------------------------------- */
                             #gateway_wrap_cod {
                                 transition: all 0.3s ease;
                             }

                             #gateway_wrap_cod.single-cod-method {
                                 width: 100% !important;
                                 max-width: 460px;
                                 margin: 10px auto 20px auto;
                                 padding: 0;
                             }

                             .cod-animated-card {
                                 display: block !important;
                                 position: relative !important;
                                 background: linear-gradient(135deg, #ffffff 0%, #f0fdf4 100%) !important;
                                 border: 2.5px solid #16a34a !important;
                                 border-radius: 14px !important;
                                 padding: 20px 16px 18px 16px !important;
                                 text-align: center !important;
                                 box-shadow: 0 4px 20px rgba(22, 163, 74, 0.22) !important;
                                 cursor: pointer !important;
                                 overflow: hidden !important;
                                 animation: codPulseGlow 2.2s infinite ease-in-out;
                                 transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275) !important;
                             }

                             .cod-animated-card:hover {
                                 transform: translateY(-5px) scale(1.025) !important;
                                 border-color: #15803d !important;
                                 background: linear-gradient(135deg, #ffffff 0%, #dcfce7 100%) !important;
                                 box-shadow: 0 8px 30px rgba(22, 163, 74, 0.4) !important;
                             }

                             .cod-glow-badge {
                                 position: absolute;
                                 top: 8px;
                                 right: 8px;
                                 z-index: 2;
                             }

                             .cod-badge-text {
                                 display: inline-block;
                                 background: #16a34a;
                                 color: #ffffff !important;
                                 font-size: 11px;
                                 font-weight: 700;
                                 padding: 3px 9px;
                                 border-radius: 20px;
                                 box-shadow: 0 2px 6px rgba(22, 163, 74, 0.35);
                                 animation: codBadgePulse 1.8s infinite;
                                 letter-spacing: 0.2px;
                             }

                             .cod-hand-pulse {
                                 display: inline-block;
                                 animation: codHandTap 1.2s infinite ease-in-out;
                             }

                             .cod-icon-wrap img {
                                 height: 52px !important;
                                 object-fit: contain;
                                 transition: transform 0.3s ease;
                                 margin-bottom: 8px;
                             }

                             .cod-animated-card:hover .cod-icon-wrap img {
                                 transform: scale(1.08);
                             }

                             .cod-title {
                                 color: #15803d !important;
                                 font-weight: 700 !important;
                                 font-size: 16px !important;
                                 letter-spacing: -0.2px;
                             }

                             .cod-subtext {
                                 font-size: 12px !important;
                                 line-height: 1.3;
                                 color: #4b5563 !important;
                             }

                             .cod-action-btn {
                                 display: inline-block;
                                 background: #16a34a;
                                 color: #ffffff !important;
                                 font-size: 13px;
                                 font-weight: 700;
                                 padding: 7px 18px;
                                 border-radius: 8px;
                                 box-shadow: 0 3px 8px rgba(22, 163, 74, 0.35);
                                 transition: all 0.25s ease;
                             }

                             .cod-animated-card:hover .cod-action-btn {
                                 background: #15803d;
                                 box-shadow: 0 4px 14px rgba(22, 163, 74, 0.5);
                             }

                             .cod-arrow-bounce {
                                 display: inline-block;
                                 animation: codArrowBounce 1.4s infinite;
                             }

                             @keyframes codPulseGlow {
                                 0% {
                                     box-shadow: 0 0 0 0 rgba(22, 163, 74, 0.45), 0 4px 15px rgba(22, 163, 74, 0.18);
                                     border-color: #16a34a;
                                 }
                                 50% {
                                     box-shadow: 0 0 0 9px rgba(22, 163, 74, 0), 0 6px 24px rgba(22, 163, 74, 0.32);
                                     border-color: #22c55e;
                                 }
                                 100% {
                                     box-shadow: 0 0 0 0 rgba(22, 163, 74, 0), 0 4px 15px rgba(22, 163, 74, 0.18);
                                     border-color: #16a34a;
                                 }
                             }

                             @keyframes codBadgePulse {
                                 0%, 100% {
                                     transform: scale(1);
                                 }
                                 50% {
                                     transform: scale(1.06);
                                 }
                             }

                             @keyframes codHandTap {
                                 0%, 100% {
                                     transform: translateY(0) rotate(0deg);
                                 }
                                 50% {
                                     transform: translateY(2px) rotate(-12deg);
                                 }
                             }

                             @keyframes codArrowBounce {
                                 0%, 100% {
                                     transform: translateX(0);
                                 }
                                 50% {
                                     transform: translateX(4px);
                                 }
                             }
                         </style>
                     </div>
                 </div>

                 @include('includes.checkout_modal')

             </div>
             <!-- Sidebar  -->
             <div class="col-xl-3 col-lg-4">
                 @include('includes.checkout_sitebar',$cart)
             </div>
         </div>
     </div>

     <script>
         function openModalById(modalId) {
             var modalEl = document.getElementById(modalId);
             if (modalEl) {
                 if (window.bootstrap && bootstrap.Modal) {
                     var modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
                     modalInstance.show();
                 } else if (window.jQuery && $(modalEl).modal) {
                     $(modalEl).modal('show');
                 }
             }
         }

         window.handlePaymentGatewayClick = function(gatewayKey) {
             var advToggle = document.getElementById('advance_payment_toggle');
             
             // 1. If Cash on Delivery clicked
             if (gatewayKey === 'cod') {
                 if (advToggle && advToggle.checked) {
                     if (typeof DangerNotification === 'function') {
                         DangerNotification("Cash on Delivery cannot be used with Advance Payment Offer. Please select a paid payment method.");
                     } else {
                         alert("Cash on Delivery cannot be used with Advance Payment Offer.");
                     }
                     return false;
                 }
                 openModalById('cod');
                 return false;
             }

             // 2. If Paid Payment Method clicked and toggle is OFF
             if (advToggle && !advToggle.checked) {
                 var offerCard = document.getElementById('advance_payment_offer_card');
                 var inlineAlert = document.getElementById('adv_toggle_alert_msg');
                 if (inlineAlert) {
                     inlineAlert.classList.remove('d-none');
                     inlineAlert.style.display = 'block';
                     setTimeout(function() {
                         inlineAlert.classList.add('d-none');
                         inlineAlert.style.display = 'none';
                     }, 6000);
                 }
                 if (offerCard) {
                     offerCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
                     offerCard.style.boxShadow = '0 0 25px rgba(220, 53, 69, 0.85)';
                     offerCard.style.borderColor = '#dc3545';
                     offerCard.style.transform = 'scale(1.02)';
                     setTimeout(function() {
                         offerCard.style.boxShadow = '0 0 15px rgba(13, 110, 253, 0.4)';
                         offerCard.style.borderColor = '#0d6efd';
                         offerCard.style.transform = 'scale(1)';
                     }, 2500);
                 }
                 return false;
             }

             // 3. Toggle is ON (or no advance offer on product): Open Modal directly
             openModalById(gatewayKey);
             return false;
         };

         document.addEventListener("DOMContentLoaded", function() {
             var baseGrandTotal = {!! json_encode((float)$grand_total) !!};
             var actualDeliveryFee = {!! json_encode((float)$actual_fee_calc) !!};
             var advanceDiscount = {!! json_encode((float)$total_advance_discount) !!};
             var currVal = {!! json_encode((float)PriceHelper::setCurrencyValue()) !!};
             var currSign = {!! json_encode(PriceHelper::setCurrencySign()) !!};
             var currDir = {!! json_encode($setting->currency_direction ?? (\App\Models\Setting::first()->currency_direction ?? 1)) !!};

             function formatPrice(amount) {
                 let converted = Math.round(amount * currVal * 100) / 100;
                 let formatted = converted.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                 return currDir == 1 ? (currSign + formatted) : (formatted + currSign);
             }

             function updateGrandTotalDisplay() {
                 let isFreeDeliveryChecked = document.getElementById('free_delivery_toggle') ? document.getElementById('free_delivery_toggle').checked : false;
                 let isAdvancePaymentChecked = document.getElementById('advance_payment_toggle') ? document.getElementById('advance_payment_toggle').checked : false;

                 let total = baseGrandTotal;
                 if (isFreeDeliveryChecked) {
                     total -= actualDeliveryFee;
                 }
                 if (isAdvancePaymentChecked) {
                     total -= advanceDiscount;
                 }

                 let formattedTotal = formatPrice(total);
                 document.querySelectorAll('.pay_amount_display, .cart-total-display, .modal_pay_amount_display').forEach(function(el) {
                     el.innerText = formattedTotal;
                 });
             }

             window.toggleFreeDelivery = function(checkbox) {
                 let isChecked = checkbox.checked;
                 
                 // 1. Update Free Delivery success message
                 let successMsg = document.getElementById('free_delivery_success_msg');
                 if (successMsg) {
                     if (isChecked) {
                         successMsg.classList.remove('d-none');
                         successMsg.style.display = 'block';
                     } else {
                         successMsg.classList.add('d-none');
                         successMsg.style.display = 'none';
                     }
                 }

                 // 2. Update Top Delivery Fee Box
                 let topDisplayBox = document.getElementById('top_delivery_fee_display_box');
                 if (topDisplayBox) {
                     if (isChecked) {
                         topDisplayBox.innerHTML = '<i class="fas fa-truck text-success mr-2" style="font-size: 18px;"></i>' +
                             '<div>' +
                             '<span class="badge badge-success px-2 py-1" style="font-size: 13px;">{{ __("Free Delivery") }}</span>' +
                             '<span class="text-muted ml-1" style="font-size: 13px;">(' + {!! json_encode(PriceHelper::setCurrencyPrice(0)) !!} + ')</span>' +
                             '</div>';
                     } else {
                         @if($actual_fee_calc == 0)
                             topDisplayBox.innerHTML = '<i class="fas fa-truck text-success mr-2" style="font-size: 18px;"></i>' +
                                 '<div>' +
                                 '<span class="badge badge-success px-2 py-1" style="font-size: 13px;">{{ __("Free Delivery") }}</span>' +
                                 '<span class="text-muted ml-1" style="font-size: 13px;">(' + {!! json_encode(PriceHelper::setCurrencyPrice(0)) !!} + ')</span>' +
                                 '</div>';
                         @else
                             topDisplayBox.innerHTML = '<i class="fas fa-truck text-primary mr-2" style="font-size: 18px;"></i>' +
                                 '<div>' +
                                 '<strong class="text-dark" style="font-size: 14px;">{{ __("Delivery Fee") }}:</strong>' +
                                 '<span class="text-primary font-weight-bold ml-1" style="font-size: 15px;">' + {!! json_encode(PriceHelper::setCurrencyPrice($actual_fee_calc)) !!} + '</span>' +
                                 '</div>';
                         @endif
                     }
                 }

                 // 3. Update Sidebar Product-wise Delivery fees and Total
                 document.querySelectorAll('.sidebar-item-delivery-val').forEach(function(el) {
                     let isItemFree = el.getAttribute('data-is-free') === '1';
                     if (isChecked) {
                         el.innerHTML = '<span class="badge badge-success px-2 py-0" style="font-size: 10.5px;">{{ __("Free Delivery") }}</span>';
                     } else {
                         if (isItemFree) {
                             el.innerHTML = '<span class="badge badge-success px-2 py-0" style="font-size: 10.5px;">{{ __("Free Delivery") }}</span>';
                         } else {
                             let orig = el.getAttribute('data-orig-text');
                             el.innerHTML = '<span class="text-dark font-weight-bold">' + orig + '</span>';
                         }
                     }
                 });

                 let totalDelEl = document.getElementById('sidebar_total_delivery_fee_display');
                 if (totalDelEl) {
                     if (isChecked) {
                         totalDelEl.innerHTML = '<span class="badge badge-success px-2 py-1" style="font-size: 11.5px;">{{ __("Free Delivery") }}</span>';
                         totalDelEl.className = 'font-weight-bold text-success';
                     } else {
                         let origTotal = totalDelEl.getAttribute('data-orig-text');
                         if (origTotal) {
                             totalDelEl.innerHTML = origTotal;
                             totalDelEl.className = 'font-weight-bold text-dark';
                         } else {
                             totalDelEl.innerHTML = '<span class="badge badge-success px-2 py-1" style="font-size: 11.5px;">{{ __("Free Delivery") }}</span>';
                             totalDelEl.className = 'font-weight-bold text-success';
                         }
                     }
                 }

                 // 3. Update hidden inputs in all checkout forms
                 let forms = document.querySelectorAll('form[action*="checkout/submit"], form[action*="checkout"]');
                 forms.forEach(function(form) {
                     let hiddenInput = form.querySelector('.free-delivery-claimed-input');
                     if(!hiddenInput) {
                         hiddenInput = document.createElement('input');
                         hiddenInput.type = 'hidden';
                         hiddenInput.name = 'is_free_delivery_claimed';
                         hiddenInput.classList.add('free-delivery-claimed-input');
                         form.appendChild(hiddenInput);
                     }
                     hiddenInput.value = isChecked ? '1' : '0';
                 });

                 // 4. Update overall totals
                 updateGrandTotalDisplay();
             };

             window.toggleAdvancePayment = function(checkbox) {
                 let isChecked = checkbox.checked;
                 
                 // Show success message inside the box
                 let successMsg = document.getElementById('adv_discount_success_msg');
                 let inlineAlert = document.getElementById('adv_toggle_alert_msg');
                 if(successMsg) {
                     if(isChecked) {
                         successMsg.classList.remove('d-none');
                         successMsg.style.display = 'block';
                     } else {
                         successMsg.classList.add('d-none');
                         successMsg.style.display = 'none';
                     }
                 }
                 if(inlineAlert) {
                     inlineAlert.classList.add('d-none');
                     inlineAlert.style.display = 'none';
                 }

                 // Dim/Enable COD Box
                 let codWrap = document.getElementById('gateway_wrap_cod');
                 if(codWrap) {
                     if(isChecked) {
                         codWrap.style.opacity = '0.5';
                         codWrap.style.cursor = 'not-allowed';
                         codWrap.title = 'Not available with Advance Payment Offer';
                     } else {
                         codWrap.style.opacity = '1';
                         codWrap.style.cursor = 'pointer';
                         codWrap.title = 'Cash On Delivery';
                     }
                 }

                 // Show/Hide Details in Sidebar
                 let discountRow = document.getElementById('advance_discount_row');
                 if(discountRow) {
                     discountRow.style.display = isChecked ? '' : 'none';
                 }

                 // Update hidden inputs for backend forms
                 let forms = document.querySelectorAll('form[action*="checkout/submit"], form[action*="checkout"]');
                 forms.forEach(function(form) {
                     let hiddenInput = form.querySelector('.adv-payment-input');
                     if(!hiddenInput) {
                         hiddenInput = document.createElement('input');
                         hiddenInput.type = 'hidden';
                         hiddenInput.name = 'is_advance_payment';
                         hiddenInput.classList.add('adv-payment-input');
                         form.appendChild(hiddenInput);
                     }
                     hiddenInput.value = isChecked ? '1' : '0';
                 });

                 // Update overall totals
                 updateGrandTotalDisplay();
             };
         });
     </script>
@endsection
