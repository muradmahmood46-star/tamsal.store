    <!-- Modal Cash on Delivery-->
    <div class="modal fade" id="cod" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h6 class="modal-title font-weight-bold"><i class="fas fa-hand-holding-usd text-success mr-2"></i> {{ __('Cash On Delivery Confirmation') }}</h6>
                    <button class="close" type="button" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <form action="{{ route('front.checkout.submit') }}" method="POST">
                    @csrf
                    <input type="hidden" name="payment_method" value="Cash On Delivery">
                    <input type="hidden" name="state_id" value="{{ auth()->check() && auth()->user()->state_id ? auth()->user()->state_id : '' }}" class="state_id_setup">
                    <input type="hidden" name="shipping_id" value="{{ $shipping ? $shipping->id : '' }}" class="shipping_id_setup">
                    
                    <div class="modal-body p-4">
                        <div class="alert alert-success d-flex align-items-center mb-3">
                            <i class="fas fa-truck-moving fa-2x mr-3 text-success"></i>
                            <div>
                                <strong>{{ __('Pay with Cash on Delivery') }}</strong>
                                <p class="mb-0 text-muted" style="font-size: 13px;">{{ __('You can pay cash directly to the delivery rider when your order arrives at your address.') }}</p>
                            </div>
                        </div>

                        {{-- Order Items Summary --}}
                        <h6 class="font-weight-bold mb-2"><i class="fas fa-shopping-bag text-primary"></i> {{ __('Order Summary & Items :') }}</h6>
                        <div class="table-responsive mb-3 border rounded" style="max-height: 180px; overflow-y: auto;">
                            <table class="table table-sm table-striped mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>{{ __('Product') }}</th>
                                        <th class="text-center">{{ __('Qty') }}</th>
                                        <th class="text-right">{{ __('Price') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($cart) && count($cart) > 0)
                                        @foreach($cart as $citem)
                                            <tr>
                                                <td>
                                                    <span class="font-weight-bold">{{ Str::limit($citem['name'], 35) }}</span>
                                                    @if(!empty($citem['attribute']['names']))
                                                        <small class="text-muted d-block">({{ implode(', ', $citem['attribute']['names']) }})</small>
                                                    @endif
                                                </td>
                                                <td class="text-center">{{ $citem['qty'] }}</td>
                                                <td class="text-right font-weight-bold">{{ PriceHelper::setCurrencyPrice(($citem['main_price'] + ($citem['attribute_price'] ?? 0)) * $citem['qty']) }}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        {{-- Cost Breakdown --}}
                        <div class="p-3 bg-light rounded border mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">{{ __('Cart Subtotal :') }}</span>
                                <strong>{{ PriceHelper::setCurrencyPrice($cart_total) }}</strong>
                            </div>
                            @if(isset($tax) && $tax > 0)
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">{{ __('Estimated Tax :') }}</span>
                                    <strong>{{ PriceHelper::setCurrencyPrice($tax) }}</strong>
                                </div>
                            @endif
                            @if(isset($shipping) && $shipping)
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">{{ __('Shipping :') }}</span>
                                    <strong class="shipping_price_set">{{ PriceHelper::setCurrencyPrice($shipping->price) }}</strong>
                                </div>
                            @endif
                            @if(isset($discount) && !empty($discount))
                                <div class="d-flex justify-content-between mb-1 text-danger">
                                    <span>{{ __('Coupon Discount :') }}</span>
                                    <strong>- {{ PriceHelper::setCurrencyPrice($discount['discount'] ?? 0) }}</strong>
                                </div>
                            @endif
                            <hr class="my-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="font-weight-bold mb-0 text-dark">{{ __('Total Amount to Pay on Delivery :') }}</h6>
                                <h5 class="font-weight-bold mb-0 text-primary modal_pay_amount_display">{{ PriceHelper::setCurrencyPrice($grand_total) }}</h5>
                            </div>
                        </div>

                        @if(!empty(PriceHelper::GatewayText('cod')))
                            <p class="text-muted mb-0" style="font-size: 13px;">{{ PriceHelper::GatewayText('cod') }}</p>
                        @endif
                    </div>

                    <div class="modal-footer bg-light">
                        <button class="btn btn-secondary btn-sm" type="button" data-bs-dismiss="modal"><span>{{ __('Cancel') }}</span></button>
                        <button class="btn btn-primary btn-sm font-weight-bold" type="submit"><i class="fas fa-check-circle"></i> <span>{{ __('Confirm Cash On Delivery Order') }}</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
     <!-- Modal MOLLIE -->
     <div class="modal fade" id="mollie" tabindex="-1" aria-hidden="true">
         <div class="modal-dialog">
             <div class="modal-content">
                 <div class="modal-header">
                     <h6 class="modal-title">{{ __('Transactions via Mollie') }}</h6>
                     <button class="close" type="button" data-bs-dismiss="modal" aria-label="Close"><span
                             aria-hidden="true">&times;</span></button>
                 </div>
                 <div class="modal-body">
                     <p>{{ PriceHelper::GatewayText('mollie') }}</p>
                 </div>
                 <div class="modal-footer">
                     <form action="{{ route('front.checkout.submit') }}" method="POST">
                         @csrf
                         <input type="hidden" name="payment_method" value="Mollie">
                         <input type="hidden" name="shipping_id" value="" class="shipping_id_setup">
                         <input type="hidden" name="state_id"
                             value="{{ auth()->check() && auth()->user()->state_id ? auth()->user()->state_id : '' }}"
                             class="state_id_setup">
                         <button class="btn btn-primary btn-sm" type="button"
                             data-bs-dismiss="modal"><span>{{ __('Cancel') }}</span></button>
                         <button class="btn btn-primary btn-sm"
                             type="submit"><span>{{ __('Checkout With Mollie') }}</span></button>
                     </form>
                 </div>
             </div>
         </div>
     </div>
     <!-- Modal PayPal -->
     <div class="modal fade" id="paypal" tabindex="-1" aria-hidden="true">
         <form class="interactive-credit-card row" action="{{ route('front.checkout.submit') }}" method="POST">
             @csrf
             <div class="modal-dialog">

                 <div class="modal-content">
                     <div class="modal-header">
                         <h6 class="modal-title">{{ __('Transactions via PayPal') }}</h6>
                         <button class="close" type="button" data-bs-dismiss="modal" aria-label="Close"><span
                                 aria-hidden="true">&times;</span></button>
                     </div>
                     <div class="modal-body">
                         <div class="card-body">
                             <p>{{ PriceHelper::GatewayText('paypal') }}</p>
                         </div>
                     </div>
                     <input type="hidden" name="payment_method" value="Paypal">
                     <input type="hidden" name="shipping_id" value="" class="shipping_id_setup">
                     <input type="hidden" name="state_id"
                         value="{{ auth()->check() && auth()->user()->state_id ? auth()->user()->state_id : '' }}"
                         class="state_id_setup">
                     <div class="modal-footer">
                         <button class="btn btn-primary btn-sm" type="button"
                             data-bs-dismiss="modal"><span>{{ __('Cancel') }}</span></button>
                         <button class="btn btn-primary btn-sm"
                             type="submit"><span>{{ __('Checkout With PayPal') }}</span></button>
                     </div>
                 </div>

             </div>
         </form>
     </div>

    <!-- Modal Stripe -->
    <div class="modal fade" id="stripe" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">{{ __('Transactions via Easypaisa') }}</h6>
                    <button class="close" type="button" data-bs-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="card-body">

                        <form class="interactive-credit-card row" action="{{ route('front.checkout.submit') }}"
                            method="POST" enctype="multipart/form-data">
                            @php
                                $easypaisaData = PriceHelper::GatewayData('stripe');
                            @endphp

                            @if(!empty($easypaisaData['account_name']) || !empty($easypaisaData['account_number']))
                                <div class="col-sm-12 mb-3">
                                    <div class="p-3 border rounded" style="background-color: #f8f9fa;">
                                        <h6 class="mb-2 font-weight-bold text-dark"><i class="fas fa-university"></i> {{ __('Send Payment to:') }}</h6>
                                        @if(!empty($easypaisaData['account_name']))
                                            <p class="mb-1"><strong>{{ __('Account Holder Name :') }}</strong> <span class="text-primary">{{ $easypaisaData['account_name'] }}</span></p>
                                        @endif
                                        @if(!empty($easypaisaData['account_number']))
                                            <p class="mb-0"><strong>{{ __('Account Number :') }}</strong> <span class="text-primary font-weight-bold">{{ $easypaisaData['account_number'] }}</span></p>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <div class="col-sm-12 mb-3 text-center">
                                <h5 style="color: purple; font-weight: 700;">{{ __('Total Amount to Pay :') }} <span class="modal_pay_amount_display">{{ PriceHelper::setCurrencyPrice($grand_total) }}</span></h5>
                            </div>
                            @csrf

                            <input type="hidden" name="payment_method" value="Stripe">
                            <input type="hidden" name="shipping_id" value="{{ $shipping ? $shipping->id : '' }}" class="shipping_id_setup">
                            <input type="hidden" name="state_id"
                                value="{{ auth()->check() && auth()->user()->state_id ? auth()->user()->state_id : '' }}"
                                class="state_id_setup">

                            <div class="form-group col-sm-12">
                                <input class="form-control" type="text" name="account_name"
                                    placeholder="{{ __('Account Holder Name') }}" required>
                            </div>
                            <div class="form-group col-sm-12">
                                <input class="form-control" type="text" name="account_number"
                                    placeholder="{{ __('Account Number') }}" required>
                            </div>
                            <div class="form-group col-sm-12">
                                <input class="form-control check-txn-input" type="text" name="txn_id"
                                    placeholder="{{ __('Transaction ID') }}" required autocomplete="off">
                                <div class="txn-feedback mt-1" style="font-size: 12.5px; display: none;"></div>
                            </div>
                            <div class="form-group col-sm-12 mb-3">
                                <label>{{ __('Payment Screenshot') }}</label>
                                <input class="form-control" type="file" name="payment_screenshot" accept="image/*" required>
                            </div>

                            @if(!empty(PriceHelper::GatewayText('stripe')))
                                <p class="p-3">{{ PriceHelper::GatewayText('stripe') }}</p>
                            @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary btn-sm" type="button"
                        data-bs-dismiss="modal"><span>{{ __('Cancel') }}</span></button>
                    <button class="btn btn-primary btn-sm"
                        type="submit"><span>{{ __('Checkout With Easypaisa') }}</span></button>
                </div>
                </form>
            </div>
        </div>
    </div>

<!-- Modal Authorize -->
    <div class="modal fade" id="authorize" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">{{ __('Transactions via JazzCash') }}</h6>
                    <button class="close" type="button" data-bs-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <form class="interactive-credit-card row" action="{{ route('front.checkout.submit') }}"
                            method="POST" enctype="multipart/form-data">
                            @php
                                $jazzcashData = PriceHelper::GatewayData('authorize');
                            @endphp

                            @if(!empty($jazzcashData['account_name']) || !empty($jazzcashData['account_number']))
                                <div class="col-sm-12 mb-3">
                                    <div class="p-3 border rounded" style="background-color: #f8f9fa;">
                                        <h6 class="mb-2 font-weight-bold text-dark"><i class="fas fa-university"></i> {{ __('Send Payment to:') }}</h6>
                                        @if(!empty($jazzcashData['account_name']))
                                            <p class="mb-1"><strong>{{ __('Account Holder Name :') }}</strong> <span class="text-primary">{{ $jazzcashData['account_name'] }}</span></p>
                                        @endif
                                        @if(!empty($jazzcashData['account_number']))
                                            <p class="mb-0"><strong>{{ __('Account Number :') }}</strong> <span class="text-primary font-weight-bold">{{ $jazzcashData['account_number'] }}</span></p>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <div class="col-sm-12 mb-3 text-center">
                                <h5 style="color: purple; font-weight: 700;">{{ __('Total Amount to Pay :') }} <span class="modal_pay_amount_display">{{ PriceHelper::setCurrencyPrice($grand_total) }}</span></h5>
                            </div>
                            @csrf
                            
                            <input type="hidden" name="payment_method" value="Authorize.Net">
                            <input type="hidden" name="shipping_id" value="{{ $shipping ? $shipping->id : '' }}" class="shipping_id_setup">
                            <input type="hidden" name="state_id"
                                value="{{ auth()->check() && auth()->user()->state_id ? auth()->user()->state_id : '' }}"
                                class="state_id_setup">

                            <div class="form-group col-sm-12">
                                <input class="form-control" type="text" name="account_name"
                                    placeholder="{{ __('Account Holder Name') }}" required>
                            </div>
                            <div class="form-group col-sm-12">
                                <input class="form-control" type="text" name="account_number"
                                    placeholder="{{ __('Account Number') }}" required>
                            </div>
                            <div class="form-group col-sm-12">
                                <input class="form-control check-txn-input" type="text" name="txn_id"
                                    placeholder="{{ __('Transaction ID') }}" required autocomplete="off">
                                <div class="txn-feedback mt-1" style="font-size: 12.5px; display: none;"></div>
                            </div>
                            <div class="form-group col-sm-12 mb-3">
                                <label>{{ __('Payment Screenshot') }}</label>
                                <input class="form-control" type="file" name="payment_screenshot" accept="image/*" required>
                            </div>

                            @if(!empty(PriceHelper::GatewayText('authorize')))
                                <p class="p-3">{{ PriceHelper::GatewayText('authorize') }}</p>
                            @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary btn-sm" type="button"
                        data-bs-dismiss="modal"><span>{{ __('Cancel') }}</span></button>
                    <button class="btn btn-primary btn-sm"
                        type="submit"><span>{{ __('Checkout With JazzCash') }}</span></button>
                </div>
                </form>
            </div>
        </div>
    </div>


     {{-- PAYPAL --}}
     <div class="modal fade" id="paypal" tabindex="-1" aria-hidden="true">
         <div class="modal-dialog">
             <form class="interactive-credit-card row" action="{{ route('front.checkout.submit') }}" method="POST">
                 @csrf
                 <div class="modal-content">
                     <div class="modal-header">
                         <h6 class="modal-title">{{ __('Transactions via PayPal') }}</h6>
                         <button class="close" type="button" data-bs-dismiss="modal" aria-label="Close"><span
                                 aria-hidden="true">&times;</span></button>
                     </div>
                     <div class="modal-body">
                         <div class="card-body">
                             <p>{{ PriceHelper::GatewayText('paypal') }}</p>
                         </div>
                     </div>
                     <input type="hidden" name="payment_method" value="Paypal">
                     <input type="hidden" name="shipping_id" value="" class="shipping_id_setup">
                     <input type="hidden" name="state_id"
                         value="{{ auth()->check() && auth()->user()->state_id ? auth()->user()->state_id : '' }}"
                         class="state_id_setup">
                     <div class="modal-footer">
                         <button class="btn btn-primary btn-sm" type="button"
                             data-bs-dismiss="modal"><span>{{ __('Cancel') }}</span></button>
                         <button class="btn btn-primary btn-sm"
                             type="submit"><span>{{ __('Checkout With PayPal') }}</span></button>
                     </div>
                 </div>
             </form>
         </div>
     </div>


     {{-- REZORPAY --}}
     <div class="modal fade" id="razorpay" tabindex="-1" aria-hidden="true">
         <form class="interactive-credit-card row" action="{{ route('front.razorpay.submit') }}" method="POST">
             @csrf
             <div class="modal-dialog">

                 <div class="modal-content">
                     <div class="modal-header">
                         <h6 class="modal-title">{{ __('Transactions via Razorpay') }}</h6>
                         <button class="close" type="button" data-bs-dismiss="modal" aria-label="Close"><span
                                 aria-hidden="true">&times;</span></button>
                     </div>
                     <div class="modal-body">
                         <div class="card-body">
                             <p>{{ PriceHelper::GatewayText('razorpay') }}</p>
                         </div>
                     </div>
                     <input type="hidden" name="payment_method" value="Rezorpay">
                     <input type="hidden" name="shipping_id" value="" class="shipping_id_setup">
                     <input type="hidden" name="state_id"
                         value="{{ auth()->check() && auth()->user()->state_id ? auth()->user()->state_id : '' }}"
                         class="state_id_setup">
                     <div class="modal-footer">
                         <button class="btn btn-primary btn-sm" type="button"
                             data-bs-dismiss="modal"><span>{{ __('Cancel') }}</span></button>
                         <button class="btn btn-primary btn-sm"
                             type="submit"><span>{{ __('Checkout With Razorpay') }}</span></button>
                     </div>
                 </div>
             </div>
         </form>
     </div>

     {{-- Flutterwave --}}
     <div class="modal fade" id="flutterwave" tabindex="-1" aria-hidden="true">
         <form class="interactive-credit-card row" action="{{ route('front.flutterwave.submit') }}" method="POST">
             @csrf
             <div class="modal-dialog">

                 <div class="modal-content">
                     <div class="modal-header">
                         <h6 class="modal-title">{{ __('Transactions via Flutterwave') }}</h6>
                         <button class="close" type="button" data-bs-dismiss="modal" aria-label="Close"><span
                                 aria-hidden="true">&times;</span></button>
                     </div>
                     <div class="modal-body">
                         <div class="card-body">
                             <p>{{ PriceHelper::GatewayText('flutterwave') }}</p>
                         </div>
                     </div>
                     <input type="hidden" name="payment_method" value="Flutterwave">
                     <input type="hidden" name="shipping_id" value="" class="shipping_id_setup">
                     <input type="hidden" name="state_id"
                         value="{{ auth()->check() && auth()->user()->state_id ? auth()->user()->state_id : '' }}"
                         class="state_id_setup">
                     <div class="modal-footer">
                         <button class="btn btn-primary btn-sm" type="button"
                             data-bs-dismiss="modal"><span>{{ __('Cancel') }}</span></button>
                         <button class="btn btn-primary btn-sm"
                             type="submit"><span>{{ __('Checkout With Flutterwave') }}</span></button>
                     </div>
                 </div>
             </div>
         </form>
     </div>

     {{-- PAYTM --}}
     <div class="modal fade" id="paytm" tabindex="-1" aria-hidden="true">
         <form class="interactive-credit-card row" action="{{ route('front.paytm.submit') }}" method="POST">
             @csrf
             <div class="modal-dialog">
                 <div class="modal-content">
                     <div class="modal-header">
                         <h6 class="modal-title">{{ __('Transactions via Paytm') }}</h6>
                         <button class="close" type="button" data-bs-dismiss="modal" aria-label="Close"><span
                                 aria-hidden="true">&times;</span></button>
                     </div>
                     <div class="modal-body">
                         <div class="card-body">
                             <p>{{ PriceHelper::GatewayText('paytm') }}</p>
                         </div>
                     </div>
                     <input type="hidden" name="payment_method" value="Paytm">
                     <input type="hidden" name="shipping_id" value="" class="shipping_id_setup">
                     <input type="hidden" name="state_id"
                         value="{{ auth()->check() && auth()->user()->state_id ? auth()->user()->state_id : '' }}"
                         class="state_id_setup">
                     <div class="modal-footer">
                         <button class="btn btn-primary btn-sm" type="button"
                             data-bs-dismiss="modal"><span>{{ __('Cancel') }}</span></button>
                         <button class="btn btn-primary btn-sm"
                             type="submit"><span>{{ __('Checkout With Paytm') }}</span></button>
                     </div>
                 </div>
             </div>
         </form>
     </div>

     {{-- SSL COMMERZ --}}
     <div class="modal fade" id="sslcommerz" tabindex="-1" aria-hidden="true">
         <form class="interactive-credit-card row" action="{{ route('front.sslcommerz.submit') }}" method="POST">
             @csrf
             <div class="modal-dialog">
                 <div class="modal-content">
                     <div class="modal-header">
                         <h6 class="modal-title">{{ __('Transactions via SSLCommerz') }}</h6>
                         <button class="close" type="button" data-bs-dismiss="modal" aria-label="Close"><span
                                 aria-hidden="true">&times;</span></button>
                     </div>
                     <div class="modal-body">
                         <div class="card-body">
                             <p>{{ PriceHelper::GatewayText('sslcommerz') }}</p>
                         </div>
                     </div>
                     <input type="hidden" name="payment_method" value="SSLCommerz">
                     <input type="hidden" name="shipping_id" value="" class="shipping_id_setup">
                     <input type="hidden" name="state_id"
                         value="{{ auth()->check() && auth()->user()->state_id ? auth()->user()->state_id : '' }}"
                         class="state_id_setup">
                     <div class="modal-footer">
                         <button class="btn btn-primary btn-sm" type="button"
                             data-bs-dismiss="modal"><span>{{ __('Cancel') }}</span></button>
                         <button class="btn btn-primary btn-sm"
                             type="submit"><span>{{ __('Checkout With SSLCommerz') }}</span></button>
                     </div>
                 </div>

             </div>
         </form>
     </div>







     @php
         $paymentData = App\Models\PaymentSetting::where('unique_keyword', 'mercadopago')->first();
         $paydata = $paymentData->convertJsonData();
     @endphp

     @if ($paymentData->status == 1)
         {{-- MERCADOPAGO --}}
         <div class="modal fade" id="mercadopago" tabindex="-1" aria-hidden="true">
             <form class="interactive-credit-card row" id="mercadopagofrom"
                 action="{{ route('front.mercadopago.submit') }}" method="POST">
                 @csrf
                 <div class="modal-dialog">
                     <div class="modal-content">
                         <div class="modal-header">
                             <h6 class="modal-title">{{ __('Transactions via Mercadapago') }}</h6>
                             <button class="close" type="button" data-bs-dismiss="modal" aria-label="Close"><span
                                     aria-hidden="true">&times;</span></button>
                         </div>
                         <div class="modal-body">
                             <div class="card-body">


                                 <div class="col-lg-12 form-group">
                                     <div id="cardNumber"></div>
                                 </div>
                                 <div class="col-lg-12 form-group">
                                     <div id="securityCode"> </div>
                                 </div>

                                 <div id="expirationDate"></div>

                                 <div class="col-lg-12 form-group">
                                     <input class="form-control" type="text" id="cardholderName"
                                         data-checkout="cardholderName" placeholder="{{ __('Card Holder Name') }}"
                                         required />
                                 </div>
                                 <div class="col-lg-12 form-group">
                                     <input class="form-control" type="text" id="docNumber"
                                         data-checkout="docNumber" placeholder="{{ __('Document Number') }}"
                                         required />
                                 </div>
                                 <div class="col-lg-12 form-group">
                                     <label for="docType" class="col-lg-3 pl-0"
                                         id="dc-label">{{ __('Document type') }}</label>
                                     <select id="docType" class="form-control" name="docType"
                                         data-checkout="docType"></select>
                                 </div>

                                 <p>{{ PriceHelper::GatewayText('mercadopago') }}</p>
                             </div>
                         </div>
                         <input type="hidden" name="payment_method" value="Mercadopago">
                         <input type="hidden" name="shipping_id" value="" class="shipping_id_setup">
                         <input type="hidden" name="state_id"
                             value="{{ auth()->check() && auth()->user()->state_id ? auth()->user()->state_id : '' }}"
                             class="state_id_setup">
                         <div class="modal-footer">
                             <button class="btn btn-primary btn-sm" type="button"
                                 data-bs-dismiss="modal"><span>{{ __('Cancel') }}</span></button>
                             <button class="btn btn-primary btn-sm"
                                 type="submit"><span>{{ __('Checkout With Mercadopago') }}</span></button>
                         </div>
                     </div>

                 </div>
                 <input type="hidden" id="installments" value="1" />
                 <input type="hidden" name="amount" id="transactionAmount" />
                 <input type="hidden" name="description" />
                 <input type="hidden" name="paymentMethodId" />
             </form>
             <script src="https://sdk.mercadopago.com/js/v2"></script>
             <script>
                 const mp = new MercadoPago("{{ $paydata['public_key'] }}");

                 const cardNumberElement = mp.fields.create('cardNumber', {
                     placeholder: "Card Number"
                 }).mount('cardNumber');

                 const expirationDateElement = mp.fields.create('expirationDate', {
                     placeholder: "MM/YY",
                 }).mount('expirationDate');

                 const securityCodeElement = mp.fields.create('securityCode', {
                     placeholder: "Security Code"
                 }).mount('securityCode');


                 (async function getIdentificationTypes() {
                     try {
                         const identificationTypes = await mp.getIdentificationTypes();

                         const identificationTypeElement = document.getElementById('docType');

                         createSelectOptions(identificationTypeElement, identificationTypes);

                     } catch (e) {
                         return console.error('Error getting identificationTypes: ', e);
                     }
                 })();

                 function createSelectOptions(elem, options, labelsAndKeys = {
                     label: "name",
                     value: "id"
                 }) {

                     const {
                         label,
                         value
                     } = labelsAndKeys;

                     //heem.options.length = 0;

                     const tempOptions = document.createDocumentFragment();

                     options.forEach(option => {
                         const optValue = option[value];
                         const optLabel = option[label];

                         const opt = document.createElement('option');
                         opt.value = optValue;
                         opt.textContent = optLabel;


                         tempOptions.appendChild(opt);
                     });

                     elem.appendChild(tempOptions);
                 }
                 cardNumberElement.on('binChange', getPaymentMethods);
                 async function getPaymentMethods(data) {
                     const {
                         bin
                     } = data
                     const {
                         results
                     } = await mp.getPaymentMethods({
                         bin
                     });
                     console.log(results);
                     return results[0];
                 }

                 async function getIssuers(paymentMethodId, bin) {
                     const issuears = await mp.getIssuers({
                         paymentMethodId,
                         bin
                     });
                     console.log(issuers)
                     return issuers;
                 };

                 async function getInstallments(paymentMethodId, bin) {
                     const installments = await mp.getInstallments({
                         amount: document.getElementById('transactionAmount').value,
                         bin,
                         paymentTypeId: 'credit_card'
                     });

                 };

                 async function createCardToken() {
                     const token = await mp.fields.createCardToken({
                         cardholderName,
                         identificationType,
                         identificationNumber,
                     });

                 }
                 doSubmit = false;
                 document.getElementById('mercadopagofrom').addEventListener('submit', getCardToken);

                 async function getCardToken(event) {
                     event.preventDefault();
                     if (!doSubmit) {
                         let $form = document.getElementById('mercadopagofrom');
                         const token = await mp.fields.createCardToken({
                             cardholderName: document.getElementById('cardholderName').value,
                             identificationType: document.getElementById('docType').value,
                             identificationNumber: document.getElementById('docNumber').value,
                         })
                         setCardTokenAndPay(token.id)
                     }
                 };

                 function setCardTokenAndPay(token) {
                     let form = document.getElementById('mercadopagofrom');
                     let card = document.createElement('input');
                     card.setAttribute('name', 'token');
                     card.setAttribute('type', 'hidden');
                     card.setAttribute('value', token);
                     form.appendChild(card);
                     doSubmit = true;
                     form.submit();
                 };
             </script>


         </div>
     @endif


     {{-- Paystack --}}
     <div class="modal fade" id="paystack" tabindex="-1" aria-hidden="true">

         <form class="interactive-credit-card row" action="{{ route('front.checkout.submit') }}" method="POST"
             id="paystack_form">
             @csrf
             <input type="hidden" name="ref_id" id="ref_id" value="">
             <div class="modal-dialog">
                 <div class="modal-content">
                     <div class="modal-header">
                         <h6 class="modal-title">{{ __('Transactions via Paystack') }}</h6>
                         <button class="close" type="button" data-bs-dismiss="modal" aria-label="Close"><span
                                 aria-hidden="true">&times;</span></button>
                     </div>
                     <div class="modal-body">
                         <div class="card-body">
                             <p>{{ PriceHelper::GatewayText('paystack') }}</p>
                         </div>
                     </div>
                     <input type="hidden" name="payment_method" value="Paystack">
                     <input type="hidden" name="shipping_id" value="" class="shipping_id_setup">
                     <input type="hidden" name="state_id"
                         value="{{ auth()->check() && auth()->user()->state_id ? auth()->user()->state_id : '' }}"
                         class="state_id_setup">
                     <div class="modal-footer">
                         <button class="btn btn-primary btn-sm" type="button"
                             data-bs-dismiss="modal"><span>{{ __('Cancel') }}</span></button>
                         <button class="btn btn-primary btn-sm final-btn" id="final-btn"
                             type="submit"><span>{{ __('Checkout With Paystack') }}</span></button>
                     </div>
                 </div>
             </div>
         </form>


         @php
             $data = App\Models\PaymentSetting::whereUniqueKeyword('paystack')->first();
             $paydata = $data->convertJsonData();
             $billing = Session::get('billing_address');
         @endphp
         @section('script')
             <script src="https://js.paystack.co/v1/inline.js"></script>
             <script>
                 $(document).on('submit', '#paystack_form', function(e) {
                     e.preventDefault();
                     var total = $(".grand_total_set").text();
                     var currencyString = total;
                     var cleanedString = currencyString.replace(/[^0-9.-]+/g, '');
                     total = parseInt(cleanedString).toFixed(2);

                     let email = $('#checkout_email_billing').val();
                     alert(email)
                     var handler = PaystackPop.setup({
                         key: '{{ $paydata['key'] }}',
                         email: email,
                         amount: parseFloat(total).toFixed(2) * 100,
                         currency: '{{ PriceHelper::setCurrencyName() }}',
                         ref: '' + Math.floor((Math.random() * 1000000000) + 1),
                         callback: function(response) {
                             $('#ref_id').val(response.reference);
                             $('#paystack_form').removeAttr('id');
                             $('.final-btn').click();
                         },
                         onClose: function() {
                             window.location.reload();
                         }
                     });
                     handler.openIframe();
                     return false;
                 });
             </script>
         @endsection
     </div>




    <!-- Modal bank -->
    <div class="modal fade" id="bank" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">{{ __('Transactions via Bank Transfer') }}</h6>
                    <button class="close" type="button" data-bs-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                <form action="{{ route('front.checkout.submit') }}" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        @php
                            $bankTransferData = PriceHelper::GatewayData('bank');
                        @endphp

                        @if(!empty($bankTransferData['bank_name']) || !empty($bankTransferData['account_name']) || !empty($bankTransferData['account_number']))
                            <div class="col-lg-12 mb-3">
                                <div class="p-3 border rounded" style="background-color: #f8f9fa;">
                                    <h6 class="mb-2 font-weight-bold text-dark"><i class="fas fa-university"></i> {{ __('Bank Details for Transfer:') }}</h6>
                                    @if(!empty($bankTransferData['bank_name']))
                                        <p class="mb-1"><strong>{{ __('Bank Name :') }}</strong> <span class="text-primary">{{ $bankTransferData['bank_name'] }}</span></p>
                                    @endif
                                    @if(!empty($bankTransferData['account_name']))
                                        <p class="mb-1"><strong>{{ __('Account Holder Name :') }}</strong> <span class="text-primary">{{ $bankTransferData['account_name'] }}</span></p>
                                    @endif
                                    @if(!empty($bankTransferData['account_number']))
                                        <p class="mb-0"><strong>{{ __('Account Number / IBAN :') }}</strong> <span class="text-primary font-weight-bold">{{ $bankTransferData['account_number'] }}</span></p>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <div class="col-lg-12 mb-3 text-center">
                            <h5 style="color: purple; font-weight: 700;">{{ __('Total Amount to Pay :') }} <span class="modal_pay_amount_display">{{ PriceHelper::setCurrencyPrice($grand_total) }}</span></h5>
                        </div>
                        <div class="col-lg-12 form-group">
                            <label for="bank_name">{{ __('Bank Name') }}</label>
                            <input class="form-control" type="text" name="bank_name" id="bank_name"
                                placeholder="{{ __('e.g., HBL, UBL, MEEZAN') }}" required>
                        </div>
                        <div class="col-lg-12 form-group">
                            <label for="account_name">{{ __('Account Holder Name') }}</label>
                            <input class="form-control" type="text" name="account_name" id="account_name"
                                placeholder="{{ __('Account Holder Name') }}" required>
                        </div>
                        <div class="col-lg-12 form-group">
                            <label for="account_number">{{ __('Account Number') }}</label>
                            <input class="form-control" type="text" name="account_number" id="account_number"
                                placeholder="{{ __('Account Number') }}" required>
                        </div>
                        <div class="col-lg-12 form-group">
                            <label for="transaction">{{ __('Transaction Number') }}</label>
                            <input class="form-control check-txn-input" name="txn_id" id="transaction"
                                placeholder="{{ __('Enter Your Transaction Number') }}" required autocomplete="off" />
                            <div class="txn-feedback mt-1" style="font-size: 12.5px; display: none;"></div>
                        </div>
                        <div class="col-lg-12 form-group">
                            <label>{{ __('Payment Screenshot') }}</label>
                            <input class="form-control" type="file" name="payment_screenshot" accept="image/*" required>
                        </div>
                        @if(!empty(PriceHelper::GatewayText('bank')))
                            <p>{!! PriceHelper::GatewayText('bank') !!}</p>
                        @endif
                    </div>
                    <div class="modal-footer">
                        @csrf
                        <input type="hidden" name="payment_method" value="Bank">
                        <input type="hidden" name="shipping_id" value="{{ $shipping ? $shipping->id : '' }}" class="shipping_id_setup">
                        <input type="hidden" name="state_id"
                            value="{{ auth()->check() && auth()->user()->state_id ? auth()->user()->state_id : '' }}"
                            class="state_id_setup">
                        <button class="btn btn-primary btn-sm" type="button"
                            data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button class="btn btn-primary btn-sm"
                            type="submit"><span>{{ __('Checkout With Bank Transfer') }}</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>




      <!-- Modal Paytabs -->
      <div class="modal fade" id="paytabs" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">{{ __('Transactions via Paytabs') }}</h6>
                    <button class="close" type="button" data-bs-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="card-body">

                        <form class="interactive-credit-card row" action="{{ route('front.paytab.submit') }}"
                            method="POST">
                            @csrf

                            <input type="hidden" name="payment_method" value="Paytabs">
                            <input type="hidden" name="shipping_id" value="" class="shipping_id_setup">
                            <input type="hidden" name="state_id"
                                value="{{ auth()->check() && auth()->user()->state_id ? auth()->user()->state_id : '' }}"
                                class="state_id_setup">
                            <p class="p-3">{{ PriceHelper::GatewayText('paytabs') }}</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary btn-sm" type="button"
                        data-bs-dismiss="modal"><span>{{ __('Cancel') }}</span></button>
                    <button class="btn btn-primary btn-sm"
                        type="submit"><span>{{ __('Chekout With Paytabs') }}</span></button>
                </div>
                </form>
            </div>
        </div>
    </div>

{{-- DYNAMIC CUSTOM PAYMENT MODALS --}}
@php
    $custom_active_gateways = DB::table('payment_settings')->whereStatus(1)->whereNotIn('unique_keyword', [
        'cod', 'stripe', 'paypal', 'mollie', 'paytm', 'sslcommerz', 'mercadopago', 
        'authorize', 'flutterwave', 'razorpay', 'paystack', 'paytabs', 'bank'
    ])->get();
@endphp

@foreach($custom_active_gateways as $c_gateway)
@php
    $c_info = json_decode($c_gateway->information, true) ?? [];
@endphp
<div class="modal fade" id="{{ $c_gateway->unique_keyword }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">{{ __('Transactions via') }} {{ $c_gateway->name }}</h6>
                <button class="close" type="button" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <form class="interactive-credit-card row" action="{{ route('front.checkout.submit') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @if(!empty($c_info['account_name']) || !empty($c_info['account_number']) || !empty($c_info['bank_name']))
                            <div class="col-sm-12 mb-3">
                                <div class="p-3 border rounded" style="background-color: #f8f9fa;">
                                    <h6 class="mb-2 font-weight-bold text-dark"><i class="fas fa-university"></i> {{ __('Send Payment to:') }}</h6>
                                    @if(!empty($c_info['bank_name']))
                                        <p class="mb-1"><strong>{{ __('Bank / Provider Name :') }}</strong> <span class="text-primary">{{ $c_info['bank_name'] }}</span></p>
                                    @endif
                                    @if(!empty($c_info['account_name']))
                                        <p class="mb-1"><strong>{{ __('Account Holder Name :') }}</strong> <span class="text-primary">{{ $c_info['account_name'] }}</span></p>
                                    @endif
                                    @if(!empty($c_info['account_number']))
                                        <p class="mb-0"><strong>{{ __('Account Number :') }}</strong> <span class="text-primary font-weight-bold">{{ $c_info['account_number'] }}</span></p>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <div class="col-sm-12 mb-3 text-center">
                            <h5 style="color: purple; font-weight: 700;">{{ __('Total Amount to Pay :') }} <span class="modal_pay_amount_display">{{ PriceHelper::setCurrencyPrice($grand_total) }}</span></h5>
                        </div>

                        <input type="hidden" name="payment_method" value="{{ $c_gateway->name }}">
                        <input type="hidden" name="shipping_id" value="{{ $shipping ? $shipping->id : '' }}" class="shipping_id_setup">
                        <input type="hidden" name="state_id" value="{{ auth()->check() && auth()->user()->state_id ? auth()->user()->state_id : '' }}" class="state_id_setup">

                        @if(!empty($c_info['bank_name']))
                            <input type="hidden" name="bank_name" value="{{ $c_info['bank_name'] }}">
                        @endif

                        <div class="form-group col-sm-12">
                            <input class="form-control" type="text" name="account_name" placeholder="{{ __('Your Account Holder Name') }}" required>
                        </div>
                        <div class="form-group col-sm-12">
                            <input class="form-control" type="text" name="account_number" placeholder="{{ __('Your Account Number') }}" required>
                        </div>
                        <div class="form-group col-sm-12">
                            <input class="form-control check-txn-input" type="text" name="txn_id" placeholder="{{ __('Transaction ID') }}" required autocomplete="off">
                            <div class="txn-feedback mt-1" style="font-size: 12.5px; display: none;"></div>
                        </div>
                        <div class="form-group col-sm-12 mb-3">
                            <label>{{ __('Payment Screenshot') }}</label>
                            <input class="form-control" type="file" name="payment_screenshot" accept="image/*" required>
                        </div>

                        @if(!empty($c_gateway->text))
                            <p class="p-3 mb-0">{{ $c_gateway->text }}</p>
                        @endif
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary btn-sm" type="button" data-bs-dismiss="modal"><span>{{ __('Cancel') }}</span></button>
                <button class="btn btn-primary btn-sm" type="submit"><span>{{ __('Checkout With') }} {{ $c_gateway->name }}</span></button>
            </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var txnCheckTimer = null;
        var checkTxnUrl = "{{ route('front.checkout.check.txn') }}";
        var csrfToken = "{{ csrf_token() }}";

        $(document).on('input keyup paste change', '.check-txn-input', function() {
            var inputEl = $(this);
            var form = inputEl.closest('form');
            var submitBtn = form.find('button[type="submit"]');
            var feedbackEl = inputEl.siblings('.txn-feedback');
            if (feedbackEl.length === 0) {
                feedbackEl = inputEl.parent().find('.txn-feedback');
            }
            var val = $.trim(inputEl.val());

            clearTimeout(txnCheckTimer);

            if (val.length === 0) {
                inputEl.removeClass('is-invalid is-valid');
                inputEl.css({'border-color': '', 'box-shadow': ''});
                feedbackEl.hide().html('');
                submitBtn.prop('disabled', false).removeClass('disabled').css('pointer-events', 'auto');
                return;
            }

            feedbackEl.show().html('<span class="text-muted"><i class="fas fa-spinner fa-spin mr-1"></i> {{ __("Checking Transaction ID...") }}</span>');

            txnCheckTimer = setTimeout(function() {
                $.ajax({
                    url: checkTxnUrl,
                    type: 'POST',
                    data: {
                        _token: csrfToken,
                        txn_id: val
                    },
                    success: function(res) {
                        if (res.exists === true) {
                            inputEl.addClass('is-invalid').removeClass('is-valid');
                            inputEl.css({'border-color': '#dc3545', 'box-shadow': '0 0 6px rgba(220,53,69,0.45)'});
                            feedbackEl.show().html('<span class="text-danger font-weight-bold" style="color: #dc3545;"><i class="fas fa-times-circle mr-1"></i> ' + res.message + '</span>');
                            submitBtn.prop('disabled', true).addClass('disabled').css('pointer-events', 'none');
                        } else {
                            inputEl.addClass('is-valid').removeClass('is-invalid');
                            inputEl.css({'border-color': '#28a745', 'box-shadow': '0 0 6px rgba(40,167,69,0.35)'});
                            feedbackEl.show().html('<span class="text-success font-weight-bold" style="color: #15803d;"><i class="fas fa-check-circle mr-1"></i> ' + res.message + '</span>');
                            submitBtn.prop('disabled', false).removeClass('disabled').css('pointer-events', 'auto');
                        }
                    },
                    error: function() {
                        feedbackEl.hide();
                        submitBtn.prop('disabled', false).removeClass('disabled').css('pointer-events', 'auto');
                    }
                });
            }, 350);
        });

        $(document).on('submit', 'form:has(.check-txn-input)', function(e) {
            var inputEl = $(this).find('.check-txn-input');
            if (inputEl.length > 0 && inputEl.hasClass('is-invalid')) {
                e.preventDefault();
                alert("{{ __('This Transaction ID has already been used. Please enter a valid unique Transaction ID.') }}");
                return false;
            }
        });
    });
</script>






