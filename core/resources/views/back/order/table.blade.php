@foreach ($datas as $data)
    @php
        $isPending = $data->order_status == 'Pending';
    @endphp
    <tr id="order-bulk-delete">
        <td><input type="checkbox" class="bulk-item" value="{{ $data->id }}"></td>

        <td>
            @if($isPending)
                <span style="color: #7c3aed; font-weight: 700;">{{ $data->transaction_number }}</span>
                <span class="badge badge-pill ml-1" style="background-color: #7c3aed; color: white; font-size: 10px;">New</span>
            @else
                <strong>{{ $data->transaction_number }}</strong>
            @endif
            @if($data->checkout_ref)
                <div class="small text-muted" style="font-size: 10.5px;">
                    <i class="fas fa-link mr-1"></i>{{ $data->checkout_ref }}
                </div>
            @endif
        </td>
        <td>
            @if($data->vendor_id && $data->seller)
                <span class="badge badge-primary py-1 px-2" style="font-size: 11px;">
                    <i class="fas fa-store mr-1"></i> {{ $data->seller->shop_name }}
                </span>
            @elseif($data->vendor_id && $data->vendorUser)
                <span class="badge badge-info py-1 px-2" style="font-size: 11px;">
                    <i class="fas fa-store mr-1"></i> {{ $data->vendorUser->first_name }}
                </span>
            @else
                <span class="badge badge-dark py-1 px-2" style="font-size: 11px;">
                    <i class="fas fa-user-shield mr-1"></i> {{ __('ORIVO') }}
                </span>
            @endif
        </td>
        <td>
            @php
                $billData = json_decode($data->billing_info, true);
                $custFirstName = $billData['bill_first_name'] ?? ($data->user ? $data->user->first_name : '');
                $custLastName = $billData['bill_last_name'] ?? ($data->user ? $data->user->last_name : '');
                $custFullName = trim($custFirstName . ' ' . $custLastName);
                $custPhone = $billData['bill_phone'] ?? ($data->user ? $data->user->phone : '');
            @endphp
            <strong>{{ $custFullName ?: __('Guest Customer') }}</strong>
            @if($custPhone)
                <div class="small text-muted" style="font-size: 11px;">
                    <i class="fas fa-phone mr-1"></i>{{ $custPhone }}
                </div>
            @endif
        </td>

        <td>
            @if ($setting->currency_direction == 1)
                {{ $data->currency_sign }}{{ PriceHelper::OrderTotal($data) }}
            @else
                {{ PriceHelper::OrderTotal($data) }}{{ $data->currency_sign }}
            @endif
        </td>

        <td>
            <div class="dropdown">
                @php
                    $isCod = in_array(strtolower(trim($data->payment_method)), ['cash on delivery', 'cod']);
                    $payBtnClass = $data->payment_status == 'Paid' ? 'btn-success' : ($data->payment_status == 'Pending' ? 'btn-warning' : 'btn-danger');
                    
                    if ($isCod) {
                        $payBtnLabel = $data->payment_status == 'Paid' ? __('Cash Received') : __("Cash Hasn't Received");
                    } else {
                        $payBtnLabel = $data->payment_status == 'Paid' ? __('Paid') : ($data->payment_status == 'Pending' ? __('Pending') : __('Unpaid'));
                    }
                @endphp
                <button
                    class="btn {{ $payBtnClass }} btn-sm dropdown-toggle"
                    type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false" style="font-weight: 600;">
                    {{ $payBtnLabel }}
                </button>
                <div class="dropdown-menu animated--fade-in" aria-labelledby="dropdownMenuButton">
                    @if($isCod)
                        <a class="dropdown-item" data-toggle="modal" data-target="#statusModal" href="javascript:;"
                            data-href="{{ route('back.order.status', [$data->id, 'payment_status', 'Paid']) }}">{{ __('Cash Received') }}</a>
                        <a class="dropdown-item" data-toggle="modal" data-target="#statusModal" href="javascript:;"
                            data-href="{{ route('back.order.status', [$data->id, 'payment_status', 'Unpaid']) }}">{{ __("Cash Hasn't Received") }}</a>
                    @else
                        <a class="dropdown-item" data-toggle="modal" data-target="#statusModal" href="javascript:;"
                            data-href="{{ route('back.order.status', [$data->id, 'payment_status', 'Paid']) }}">{{ __('Paid') }}</a>
                        <a class="dropdown-item" data-toggle="modal" data-target="#statusModal" href="javascript:;"
                            data-href="{{ route('back.order.status', [$data->id, 'payment_status', 'Pending']) }}">{{ __('Pending') }}</a>
                        <a class="dropdown-item" data-toggle="modal" data-target="#statusModal" href="javascript:;"
                            data-href="{{ route('back.order.status', [$data->id, 'payment_status', 'Unpaid']) }}">{{ __('Unpaid') }}</a>
                    @endif
                </div>
            </div>
        </td>
        <td>
            <div class="dropdown">
                @php
                    $isReattempt = App\Models\TrackOrder::where('order_id', $data->id)->where('title', 'Canceled')->exists();
                    $statusClass = 'btn-warning';
                    $statusLabel = __('Pending');

                    if ($data->order_status == 'Delivered') {
                        $statusClass = 'btn-success';
                        $statusLabel = $isReattempt ? __('Delivered (After Re-attempt)') : __('Delivered');
                    } elseif ($data->order_status == 'In Progress') {
                        $statusClass = 'btn-info';
                        $statusLabel = $isReattempt ? __('Delivery in Progress (Re-attempt)') : __('Delivery in Progress');
                    } elseif ($data->order_status == 'Send to Delivery House') {
                        $statusClass = '';
                        $statusLabel = __('Send to Delivery House');
                    } elseif ($data->order_status == 'Accepted') {
                        $statusClass = 'btn-primary';
                        $statusLabel = __('Accepted');
                    } elseif ($data->order_status == 'Canceled') {
                        $statusClass = 'btn-danger';
                        $statusLabel = __('Canceled');
                    } else {
                        $statusClass = 'btn-warning';
                        $statusLabel = $isReattempt ? __('Re-attempt (New Order)') : __('New Order');
                    }
                @endphp
                <button class="btn {{ $statusClass }} btn-sm dropdown-toggle" type="button"
                    id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="font-weight: 600;{{ $data->order_status == 'Send to Delivery House' ? 'background-color: #6f42c1; border-color: #6f42c1; color: #fff;' : '' }}">
                    {{ $statusLabel }}
                </button>
                <div class="dropdown-menu animated--fade-in" aria-labelledby="dropdownMenuButton">
                    @if($data->order_status != 'Pending')
                        <a class="dropdown-item" data-toggle="modal" data-target="#statusModal" href="javascript:;"
                            data-href="{{ route('back.order.status', [$data->id, 'order_status', 'Pending']) }}">{{ __('New Order (Pending)') }}</a>
                    @endif
                    @if($data->order_status != 'Accepted')
                        <a class="dropdown-item" data-toggle="modal" data-target="#statusModal" href="javascript:;"
                            data-href="{{ route('back.order.status', [$data->id, 'order_status', 'Accepted']) }}">{{ __('Accepted') }}</a>
                    @endif
                    @if($data->order_status != 'Send to Delivery House')
                        <a class="dropdown-item" data-toggle="modal" data-target="#statusModal" href="javascript:;"
                            data-href="{{ route('back.order.status', [$data->id, 'order_status', 'Send to Delivery House']) }}">{{ __('Send to Delivery House') }}</a>
                    @endif
                    @if($data->order_status != 'In Progress')
                        <a class="dropdown-item" data-toggle="modal" data-target="#statusModal" href="javascript:;"
                            data-href="{{ route('back.order.status', [$data->id, 'order_status', 'In Progress']) }}">{{ __('Delivery in Progress') }}</a>
                    @endif
                    @if($data->order_status != 'Pending' && $data->order_status != 'Delivered')
                        <a class="dropdown-item" data-toggle="modal" data-target="#statusModal" href="javascript:;"
                            data-href="{{ route('back.order.status', [$data->id, 'order_status', 'Delivered']) }}">{{ __('Delivered') }}</a>
                    @endif
                    @if($data->order_status != 'Canceled')
                        <a class="dropdown-item" data-toggle="modal" data-target="#statusModal" href="javascript:;"
                            data-href="{{ route('back.order.status', [$data->id, 'order_status', 'Canceled']) }}">{{ __('Canceled') }}</a>
                    @endif
                </div>
            </div>
        </td>
        <td>
            <div class="action-list">
                <a class="btn btn-secondary btn-sm" href="{{ route('back.order.invoice', $data->id) }}">
                    <i class="fas fa-eye"></i>
                </a>
                <a class="btn btn-info btn-sm " href="{{ route('back.order.edit', $data->id) }}">
                    <i class="fas fa-pen"></i>
                </a>
                <a class="btn btn-danger btn-sm " data-toggle="modal" data-target="#confirm-delete" href="javascript:;"
                    data-href="{{ route('back.order.delete', $data->id) }}">
                    <i class="fas fa-trash-alt"></i>
                </a>

            </div>
        </td>
    </tr>
@endforeach
