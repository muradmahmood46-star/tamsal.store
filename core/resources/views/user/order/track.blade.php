@if (!isset($error) && isset($order))
    @php
        $activeOrderId = $activeOrderId ?? $order->id;
        if (!isset($allSplitOrders)) {
            $checkoutRef = $order->checkout_ref ?? null;
            $allSplitOrders = $checkoutRef ? \App\Models\Order::where('checkout_ref', $checkoutRef)->orderBy('id', 'asc')->get() : collect([$order]);
        }
        if (!isset($allTracksGrouped)) {
            $allTracksGrouped = \App\Models\TrackOrder::whereIn('order_id', $allSplitOrders->pluck('id'))->orderBy('created_at', 'asc')->orderBy('id', 'asc')->get()->groupBy('order_id');
        }

        $steps = [
            [
                'key' => 'Pending',
                'label' => __('Pending / Placed'),
                'icon' => 'fas fa-clock',
                'color' => '#ffc107',
            ],
            [
                'key' => 'Accepted',
                'label' => __('Accepted'),
                'icon' => 'fas fa-check',
                'color' => '#0d6efd',
            ],
            [
                'key' => 'Send to Delivery House',
                'label' => __('Delivery House'),
                'icon' => 'fas fa-warehouse',
                'color' => '#6f42c1',
            ],
            [
                'key' => 'In Progress',
                'label' => __('Out for Delivery'),
                'icon' => 'fas fa-truck',
                'color' => '#17a2b8',
            ],
            [
                'key' => 'Delivered',
                'label' => __('Delivered'),
                'icon' => 'fas fa-check-double',
                'color' => '#28a745',
            ]
        ];

        $checkoutGrandTotal = 0;
        foreach($allSplitOrders as $sp) {
            $checkoutGrandTotal += \App\Helpers\PriceHelper::parsePrice(\App\Helpers\PriceHelper::OrderTotal($sp));
        }
    @endphp

    <style>
        .pkg-tab-btn {
            display: inline-flex !important;
            align-items: center !important;
            padding: 8px 16px !important;
            border-radius: 8px !important;
            font-size: 13px !important;
            font-weight: 600 !important;
            border: 1.5px solid #dee2e6 !important;
            background-color: #ffffff !important;
            color: #2b3445 !important;
            cursor: pointer !important;
            outline: none !important;
            transition: all 0.2s ease-in-out !important;
            text-decoration: none !important;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05) !important;
            line-height: 1.4 !important;
        }
        .pkg-tab-btn:hover,
        .pkg-tab-btn:focus {
            background-color: #eef4ff !important;
            border-color: #0d6efd !important;
            color: #0d6efd !important;
            text-decoration: none !important;
            box-shadow: 0 2px 6px rgba(13,110,253,0.18) !important;
        }
        .pkg-tab-btn.active {
            background-color: #0d6efd !important;
            border-color: #0d6efd !important;
            color: #ffffff !important;
            box-shadow: 0 3px 10px rgba(13,110,253,0.3) !important;
        }
        .pkg-tab-btn .badge {
            font-size: 11px !important;
            font-weight: 600 !important;
            padding: 3px 8px !important;
            border-radius: 4px !important;
            transition: all 0.2s ease !important;
        }
        .pkg-tab-btn.active .badge {
            background-color: rgba(255,255,255,0.25) !important;
            color: #ffffff !important;
        }
        .pkg-tab-btn:not(.active) .badge {
            background-color: #e9ecef !important;
            color: #495057 !important;
        }
        .pkg-tab-btn:not(.active):hover .badge {
            background-color: #dbeafe !important;
            color: #0d6efd !important;
        }

        .track-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border: 1px solid #eef0f3;
            overflow: hidden;
            margin-bottom: 25px;
        }
        .track-card-header {
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
            color: #fff;
            padding: 18px 24px;
        }
        .track-steps-bar {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin: 30px 0 20px;
            padding: 0;
            list-style: none;
        }
        .track-steps-bar::before {
            content: '';
            position: absolute;
            top: 24px;
            left: 5%;
            right: 5%;
            height: 4px;
            background: #e9ecef;
            z-index: 1;
        }
        .track-step-item {
            position: relative;
            z-index: 2;
            text-align: center;
            flex: 1;
        }
        .track-step-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #ffffff;
            border: 3px solid #dee2e6;
            color: #adb5bd;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 6px rgba(0,0,0,0.06);
        }
        .track-step-item.active .track-step-icon {
            color: #fff;
            border-color: transparent;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transform: scale(1.08);
        }
        .track-step-item.active.step-pending .track-step-icon { background: #ffc107; color: #212529; }
        .track-step-item.active.step-accepted .track-step-icon { background: #0d6efd; }
        .track-step-item.active.step-delivery-house .track-step-icon { background: #6f42c1; }
        .track-step-item.active.step-in-progress .track-step-icon { background: #17a2b8; }
        .track-step-item.active.step-delivered .track-step-icon { background: #28a745; }
        
        .track-step-title {
            font-size: 13px;
            font-weight: 700;
            margin-top: 10px;
            color: #6c757d;
        }
        .track-step-item.active .track-step-title {
            color: #212529;
        }
        .track-step-date {
            font-size: 11px;
            color: #868e96;
            margin-top: 2px;
        }
        .track-timeline {
            position: relative;
            padding-left: 30px;
            margin: 20px 0;
        }
        .track-timeline::before {
            content: '';
            position: absolute;
            top: 5px;
            bottom: 5px;
            left: 11px;
            width: 2px;
            background: #e2e8f0;
        }
        .track-timeline-item {
            position: relative;
            margin-bottom: 22px;
        }
        .track-timeline-item:last-child {
            margin-bottom: 0;
        }
        .track-timeline-point {
            position: absolute;
            left: -30px;
            top: 2px;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            color: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.12);
        }
        .track-timeline-content {
            background: #f8fafc;
            border: 1px solid #eef2f6;
            border-radius: 10px;
            padding: 14px 18px;
            border-left: 4px solid #0d6efd;
        }
        .track-timeline-content.status-pending { border-left-color: #ffc107; }
        .track-timeline-content.status-accepted { border-left-color: #0d6efd; }
        .track-timeline-content.status-delivery-house { border-left-color: #6f42c1; }
        .track-timeline-content.status-in-progress { border-left-color: #17a2b8; }
        .track-timeline-content.status-delivered { border-left-color: #28a745; }
        .track-timeline-content.status-canceled { border-left-color: #dc3545; }

        @media (max-width: 767px) {
            .track-steps-bar {
                flex-direction: column;
                gap: 15px;
            }
            .track-steps-bar::before {
                display: none;
            }
            .track-step-item {
                display: flex;
                align-items: center;
                text-align: left;
                gap: 15px;
            }
            .track-step-title {
                margin-top: 0;
            }
        }
    </style>

    {{-- Multi-Store Switcher Tabs (Shown once at the top if multiple stores) --}}
    @if($allSplitOrders->count() > 1)
        <div class="alert alert-light border p-3 mb-4 rounded-lg shadow-sm" style="background: #ffffff; border-left: 4px solid #0d6efd !important;">
            <div class="d-flex flex-wrap align-items-center justify-content-between">
                <div class="mb-2 mb-md-0">
                    <strong class="text-dark"><i class="fas fa-boxes text-primary mr-1"></i> {{ __('Multi-Store Purchase:') }}</strong>
                    <span class="small text-muted">{{ __('This checkout has :count packages. Click below to view each store\'s updates & items:', ['count' => $allSplitOrders->count()]) }}</span>
                </div>
                <div class="d-flex flex-wrap gap-2" style="gap: 8px;">
                    @foreach($allSplitOrders as $spOrd)
                        <button type="button" 
                                class="pkg-tab-btn {{ $spOrd->id == $activeOrderId ? 'active' : '' }}" 
                                data-pkg-id="{{ $spOrd->id }}" 
                                data-txnid="{{ $spOrd->transaction_number }}"
                                onclick="switchPackageTab({{ $spOrd->id }}, '{{ $spOrd->transaction_number }}', this)">
                            <i class="fas fa-store mr-1"></i> <strong>{{ $spOrd->store_name }}</strong> 
                            <span class="ml-1 font-weight-normal">({{ $spOrd->transaction_number }})</span>
                            <span class="badge ml-1">{{ __($spOrd->order_status) }}</span>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- Package Panels (Each package has its own scoped card, progress bar, timeline, and items) --}}
    @foreach($allSplitOrders as $pkgOrder)
        @php
            $pkgTracks = $allTracksGrouped->get($pkgOrder->id, collect([]));
            $pkgTrackMap = [];
            foreach ($pkgTracks as $t) {
                $pkgTrackMap[$t->title] = $t;
            }

            $pkgIsCanceled = isset($pkgTrackMap['Canceled']) || $pkgOrder->order_status == 'Canceled';
            $pkgCart = json_decode($pkgOrder->cart, true) ?: [];
            $pkgBill = json_decode($pkgOrder->billing_info, true) ?: [];
            $pkgShip = json_decode($pkgOrder->shipping_info, true) ?: [];
            $pkgCustomerName = trim(($pkgBill['bill_first_name'] ?? ($pkgOrder->user->first_name ?? '')) . ' ' . ($pkgBill['bill_last_name'] ?? ($pkgOrder->user->last_name ?? '')));
            $pkgStoreName = $pkgOrder->store_name;
        @endphp

        <div class="pkg-panel" id="pkg-panel-{{ $pkgOrder->id }}" style="{{ $pkgOrder->id == $activeOrderId ? 'display: block;' : 'display: none;' }}">
            <div class="track-card">
                <!-- Header -->
                <div class="track-card-header d-flex flex-wrap justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1 text-white font-weight-bold">
                            <i class="fas fa-receipt mr-2"></i> {{ __('Order #') }}: {{ $pkgOrder->transaction_number }}
                        </h5>
                        <small class="text-white-50">
                            <i class="far fa-calendar-alt mr-1"></i> {{ $pkgOrder->created_at->format('M d, Y h:i A') }} 
                            | <i class="fas fa-store mr-1"></i> {{ $pkgStoreName }}
                            @if($pkgOrder->checkout_ref)
                                | <span class="badge badge-light text-primary font-weight-normal">{{ __('Checkout:') }} {{ $pkgOrder->checkout_ref }}</span>
                            @endif
                        </small>
                    </div>
                    <div class="mt-2 mt-sm-0">
                        @if($pkgIsCanceled)
                            <span class="badge badge-danger p-2 font-weight-bold"><i class="fas fa-ban mr-1"></i> {{ __('Canceled') }}</span>
                        @elseif($pkgOrder->order_status == 'Delivered')
                            <span class="badge badge-success p-2 font-weight-bold"><i class="fas fa-check-circle mr-1"></i> {{ __('Delivered') }}</span>
                        @elseif($pkgOrder->order_status == 'In Progress')
                            <span class="badge badge-info p-2 font-weight-bold"><i class="fas fa-truck mr-1"></i> {{ __('In Progress') }}</span>
                        @elseif($pkgOrder->order_status == 'Send to Delivery House')
                            <span class="badge p-2 font-weight-bold text-white" style="background-color: #6f42c1;"><i class="fas fa-warehouse mr-1"></i> {{ __('At Delivery House') }}</span>
                        @elseif($pkgOrder->order_status == 'Accepted')
                            <span class="badge badge-primary p-2 font-weight-bold"><i class="fas fa-check mr-1"></i> {{ __('Accepted') }}</span>
                        @else
                            <span class="badge badge-warning text-dark p-2 font-weight-bold"><i class="fas fa-clock mr-1"></i> {{ __('Pending') }}</span>
                        @endif
                    </div>
                </div>

                <div class="card-body p-4">
                    @if($pkgIsCanceled)
                        <!-- Canceled Banner -->
                        <div class="alert alert-danger d-flex align-items-center p-3 mb-4 rounded-lg shadow-sm">
                            <i class="fas fa-times-circle fa-2x mr-3 text-danger"></i>
                            <div>
                                <h6 class="font-weight-bold mb-1 text-danger">{{ __('This Package Has Been Canceled') }}</h6>
                                <p class="mb-0 small text-dark">
                                    {{ isset($pkgTrackMap['Canceled']) && !empty($pkgTrackMap['Canceled']->text) ? $pkgTrackMap['Canceled']->text : __('This package has been canceled.') }}
                                </p>
                            </div>
                        </div>
                    @else
                        <!-- 5-Step Visual Progress Bar -->
                        <div class="py-2">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="font-weight-bold text-muted text-uppercase">
                                    <i class="fas fa-box text-primary mr-1"></i> {{ __('Status for Store') }}: <span class="text-dark font-weight-bold">{{ $pkgStoreName }} ({{ $pkgOrder->transaction_number }})</span>
                                </small>
                            </div>
                            <ul class="track-steps-bar">
                                @foreach($steps as $s)
                                    @php
                                        $isStepActive = isset($pkgTrackMap[$s['key']]);
                                        $stepTrack = $pkgTrackMap[$s['key']] ?? null;
                                        $stepClass = '';
                                        if ($s['key'] == 'Pending') $stepClass = 'step-pending';
                                        elseif ($s['key'] == 'Accepted') $stepClass = 'step-accepted';
                                        elseif ($s['key'] == 'Send to Delivery House') $stepClass = 'step-delivery-house';
                                        elseif ($s['key'] == 'In Progress') $stepClass = 'step-in-progress';
                                        elseif ($s['key'] == 'Delivered') $stepClass = 'step-delivered';
                                    @endphp
                                    <li class="track-step-item {{ $isStepActive ? 'active ' . $stepClass : '' }}">
                                        <div class="track-step-icon">
                                            <i class="{{ $s['icon'] }}"></i>
                                        </div>
                                        <div>
                                            <div class="track-step-title">{{ $s['label'] }}</div>
                                            @if($isStepActive && $stepTrack)
                                                <div class="track-step-date">
                                                    {{ \Carbon\Carbon::parse($stepTrack->created_at)->format('M d, h:i A') }}
                                                </div>
                                            @else
                                                <div class="track-step-date text-muted">{{ __('Waiting') }}</div>
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <hr class="my-4">

                    <!-- Live Status Updates Timeline & Items Row -->
                    <div class="row">
                        <!-- Left: Live Order Updates & Activity (Scoped to this package) -->
                        <div class="col-lg-7 mb-4 mb-lg-0">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h6 class="font-weight-bold text-dark mb-0">
                                    <i class="fas fa-stream text-primary mr-2"></i> {{ __('Live Order Updates & Activity') }}
                                </h6>
                                <span class="badge badge-light border text-dark" style="font-size: 11px;">
                                    <i class="fas fa-store text-primary mr-1"></i> {{ $pkgStoreName }}
                                </span>
                            </div>

                            @if(count($pkgTracks) > 0)
                                <div class="track-timeline">
                                    @foreach($pkgTracks as $trackItem)
                                        @php
                                            $trackTitle = $trackItem->title;
                                            $tText = !empty($trackItem->text) ? $trackItem->text : '';

                                            $tColor = '#0d6efd';
                                            $tIcon = 'fas fa-info-circle';
                                            $statusCss = 'status-pending';

                                            if ($trackTitle == 'Pending') {
                                                $tColor = '#ffc107';
                                                $tIcon = 'fas fa-clock';
                                                $statusCss = 'status-pending';
                                                if (empty($tText)) $tText = 'Your order for package #' . $pkgOrder->transaction_number . ' has been received and is awaiting confirmation from ' . $pkgStoreName . '.';
                                            } elseif ($trackTitle == 'Accepted') {
                                                $tColor = '#0d6efd';
                                                $tIcon = 'fas fa-check';
                                                $statusCss = 'status-accepted';
                                                if (empty($tText)) $tText = 'Order has been accepted by vendor "' . $pkgStoreName . '".';
                                            } elseif ($trackTitle == 'Send to Delivery House') {
                                                $tColor = '#6f42c1';
                                                $tIcon = 'fas fa-warehouse';
                                                $statusCss = 'status-delivery-house';
                                                if (empty($tText)) $tText = 'Your order has been dispatched from seller "' . $pkgStoreName . '" and has arrived at the Delivery House.';
                                            } elseif ($trackTitle == 'In Progress') {
                                                $tColor = '#17a2b8';
                                                $tIcon = 'fas fa-truck';
                                                $statusCss = 'status-in-progress';
                                                if (empty($tText)) $tText = 'Package #' . $pkgOrder->transaction_number . ' is out for delivery. The delivery agent will contact you shortly.';
                                            } elseif ($trackTitle == 'Delivered') {
                                                $tColor = '#28a745';
                                                $tIcon = 'fas fa-check-double';
                                                $statusCss = 'status-delivered';
                                                if (empty($tText)) $tText = 'Package #' . $pkgOrder->transaction_number . ' from "' . $pkgStoreName . '" has been delivered successfully.';
                                            } elseif ($trackTitle == 'Canceled') {
                                                $tColor = '#dc3545';
                                                $tIcon = 'fas fa-times-circle';
                                                $statusCss = 'status-canceled';
                                                if (empty($tText)) $tText = 'Package #' . $pkgOrder->transaction_number . ' has been canceled.';
                                            }
                                        @endphp
                                        <div class="track-timeline-item">
                                            <div class="track-timeline-point" style="background-color: {{ $tColor }};">
                                                <i class="{{ $tIcon }}"></i>
                                            </div>
                                            <div class="track-timeline-content {{ $statusCss }}">
                                                <div class="d-flex flex-wrap justify-content-between align-items-center mb-1">
                                                    <strong class="text-dark" style="font-size: 14px;">
                                                        {{ __($trackTitle) }}
                                                    </strong>
                                                    <span class="badge badge-light border text-muted">
                                                        <i class="far fa-clock mr-1"></i>
                                                        {{ \Carbon\Carbon::parse($trackItem->created_at)->format('l, d M, Y - h:i A') }}
                                                    </span>
                                                </div>
                                                <p class="mb-0 text-dark" style="font-size: 13.5px; line-height: 1.5;">
                                                    {{ $tText }}
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">{{ __('No tracking updates available yet for this package.') }}</p>
                            @endif
                        </div>

                        <!-- Right: Items & Summary (Scoped to this package) -->
                        <div class="col-lg-5">
                            <div class="bg-light p-3 rounded-lg border">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <h6 class="font-weight-bold text-dark mb-0">
                                        <i class="fas fa-shopping-bag text-primary mr-1"></i> {{ __('Items in this Package') }}
                                    </h6>
                                    <span class="badge badge-light border text-primary font-weight-bold">
                                        {{ $pkgStoreName }}
                                    </span>
                                </div>
                                <div class="mb-3">
                                    @foreach($pkgCart as $item)
                                        <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                            <div class="d-flex align-items-center">
                                                @if(!empty($item['photo']))
                                                    <img src="{{ asset('core/public/storage/images/' . $item['photo']) }}" alt="{{ $item['name'] ?? '' }}" style="width: 38px; height: 38px; object-fit: cover; border-radius: 6px; margin-right: 10px; border: 1px solid #dee2e6;">
                                                @endif
                                                <div>
                                                    <div class="font-weight-bold text-dark" style="font-size: 13px;">{{ $item['name'] ?? 'Product' }}</div>
                                                    <small class="text-muted">{{ __('Qty') }}: {{ $item['qty'] ?? 1 }}</small>
                                                </div>
                                            </div>
                                            <div class="font-weight-bold text-primary" style="font-size: 13px;">
                                                {{ \App\Helpers\PriceHelper::setCurrencyPrice(($item['main_price'] ?? 0) * ($item['qty'] ?? 1)) }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="d-flex justify-content-between font-weight-bold pt-1 mb-3" style="font-size: 15px;">
                                    <span>{{ __('Package Subtotal') }}:</span>
                                    <span class="text-success">
                                        @if(isset($setting) && $setting->currency_direction == 1)
                                            {{ $pkgOrder->currency_sign }}{{ \App\Helpers\PriceHelper::OrderTotal($pkgOrder) }}
                                        @else
                                            {{ \App\Helpers\PriceHelper::OrderTotal($pkgOrder) }}{{ $pkgOrder->currency_sign }}
                                        @endif
                                    </span>
                                </div>

                                @if($allSplitOrders->count() > 1)
                                    <div class="d-flex justify-content-between font-weight-bold pt-2 mb-3 border-top" style="font-size: 13.5px; color: #495057;">
                                        <span>{{ __('Total Checkout (:count packages)', ['count' => $allSplitOrders->count()]) }}:</span>
                                        <span class="text-primary">
                                            @if(isset($setting) && $setting->currency_direction == 1)
                                                {{ $pkgOrder->currency_sign }}{{ number_format($checkoutGrandTotal, 2) }}
                                            @else
                                                {{ number_format($checkoutGrandTotal, 2) }}{{ $pkgOrder->currency_sign }}
                                            @endif
                                        </span>
                                    </div>
                                @endif

                                <h6 class="font-weight-bold text-dark pt-2 border-top mb-2" style="font-size: 13px;">
                                    <i class="fas fa-map-marker-alt text-danger mr-1"></i> {{ __('Delivery Address') }}
                                </h6>
                                <p class="small text-muted mb-0">
                                    <strong>{{ $pkgCustomerName ?: 'Customer' }}</strong><br>
                                    {{ $pkgShip['ship_address1'] ?? ($pkgBill['bill_address1'] ?? '') }} {{ $pkgShip['ship_city'] ?? ($pkgBill['bill_city'] ?? '') }}<br>
                                    <i class="fas fa-phone mr-1"></i> {{ $pkgShip['ship_phone'] ?? ($pkgBill['bill_phone'] ?? 'N/A') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <script>
        function switchPackageTab(orderId, txnNumber, btnElem) {
            $('.pkg-panel').hide();
            $('#pkg-panel-' + orderId).fadeIn(150);
            $('.pkg-tab-btn').removeClass('active');
            $('.pkg-tab-btn[data-pkg-id="' + orderId + '"]').addClass('active');
            $('#order_number').val(txnNumber);
        }
    </script>
@else
    <div class="alert alert-warning text-center p-4 rounded-lg shadow-sm">
        <i class="fas fa-search fa-2x text-warning mb-2 d-block"></i>
        <h5 class="font-weight-bold text-dark">{{ __('Order Not Found') }}</h5>
        <p class="text-muted mb-0">{{ __('Please check your Order Number and try again.') }}</p>
    </div>
@endif
