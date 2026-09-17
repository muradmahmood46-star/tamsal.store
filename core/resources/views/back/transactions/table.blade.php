@forelse($datas as $index => $data)
    @php
        $stLower = strtolower($data->status ?? '');
        $statusClass = 'badge-secondary';
        if (in_array($stLower, ['paid', 'approved', 'completed', 'active'])) {
            $statusClass = 'badge-success text-white';
        } elseif (in_array($stLower, ['pending', 'pending fine'])) {
            $statusClass = 'badge-warning text-dark font-weight-bold';
        } elseif (in_array($stLower, ['rejected', 'canceled', 'unpaid'])) {
            $statusClass = 'badge-danger text-white';
        }

        $methodLower = strtolower(trim($data->payment_method ?? ''));
        $methodClass = 'badge-light border text-dark';
        if (str_contains($methodLower, 'easypaisa')) $methodClass = 'badge-success text-white';
        elseif (str_contains($methodLower, 'jazzcash')) $methodClass = 'badge-danger text-white';
        elseif (str_contains($methodLower, 'bank')) $methodClass = 'badge-primary text-white';
        elseif (str_contains($methodLower, 'cod') || str_contains($methodLower, 'cash')) $methodClass = 'badge-dark text-white';
        elseif (str_contains($methodLower, 'wallet')) $methodClass = 'badge-info text-white';

        $jsonItem = [
            'id' => $data->id,
            'type' => $data->type,
            'type_label' => $data->type_label,
            'type_badge' => $data->type_badge,
            'type_icon' => $data->type_icon,
            'name' => $data->name,
            'email' => $data->email,
            'phone' => $data->phone,
            'store_name' => $data->store_name,
            'store_url' => $data->store_url,
            'txn_id' => $data->txn_id,
            'reference' => $data->reference,
            'payment_method' => $data->payment_method,
            'bank_name' => $data->bank_name,
            'account_name' => $data->account_name,
            'account_number' => $data->account_number,
            'amount' => $data->amount,
            'currency_sign' => $data->currency_sign,
            'status' => $data->status,
            'screenshot_url' => $data->screenshot_url,
            'details' => $data->details,
            'created_at' => $data->created_at ? $data->created_at->toISOString() : null,
            'view_url' => $data->view_url,
            'direct_url' => $data->direct_url,
            'direct_label' => $data->direct_label
        ];
    @endphp
    <tr>
        <td class="text-center font-weight-bold text-muted">{{ $datas->firstItem() ? ($datas->firstItem() + $index) : ($index + 1) }}</td>
        
        <!-- Type / Source -->
        <td>
            <span class="badge {{ $data->type_badge }} px-2 py-1 font-weight-bold d-inline-flex align-items-center" style="font-size: 11px; border-radius: 6px;">
                <i class="{{ $data->type_icon }} mr-1"></i> {{ $data->type_label }}
            </span>
        </td>

        <!-- Customer / Store / Vendor -->
        <td>
            <div class="font-weight-bold text-dark">{{ $data->name ?: '-' }}</div>
            @if($data->store_name && $data->store_name !== 'Official Store')
                <div class="small">
                    @if($data->store_url)
                        <a href="{{ $data->store_url }}" target="_blank" class="text-primary text-decoration-none">
                            <i class="fas fa-store mr-1"></i>{{ $data->store_name }}
                            <i class="fas fa-external-link-alt ml-1" style="font-size: 8.5px;"></i>
                        </a>
                    @else
                        <span class="text-muted"><i class="fas fa-store mr-1"></i>{{ $data->store_name }}</span>
                    @endif
                </div>
            @elseif($data->store_name === 'Official Store')
                <div class="small text-muted"><i class="fas fa-shield-alt mr-1"></i>{{ __('Official Store') }}</div>
            @endif
            @if($data->phone)
                <div class="small text-muted" style="font-size: 11px;"><i class="fas fa-phone mr-1"></i>{{ $data->phone }}</div>
            @endif
        </td>

        <!-- Transaction ID / Ref -->
        <td>
            <div class="d-flex align-items-center">
                <span class="badge badge-light border text-dark font-weight-bold px-2 py-1" style="font-size: 12px; letter-spacing: 0.3px;">
                    {{ $data->txn_id }}
                </span>
            </div>
            @if($data->reference && $data->reference !== '-')
                <div class="small text-muted font-italic mt-1" style="font-size: 11px;">
                    {{ __('Ref:') }} <strong>{{ $data->reference }}</strong>
                </div>
            @endif
        </td>

        <!-- Payment Method -->
        <td>
            <span class="badge {{ $methodClass }} px-2 py-1 font-weight-bold" style="font-size: 11px;">
                {{ $data->payment_method }}
            </span>
            @if($data->account_number)
                <div class="small text-muted mt-1 font-italic" style="font-size: 11px;">
                    {{ $data->account_number }}
                </div>
            @endif
        </td>

        <!-- Proof Screenshot -->
        <td class="text-center">
            @if($data->screenshot_url)
                <a href="javascript:void(0)" onclick="previewScreenshot('{{ $data->screenshot_url }}', '{{ addslashes($data->txn_id) }}')" class="d-inline-block shadow-sm rounded overflow-hidden" style="border: 2px solid #e2e8f0; transition: transform 0.2s;" title="{{ __('Click to preview full image') }}">
                    <img src="{{ $data->screenshot_url }}" alt="Proof" style="width: 42px; height: 42px; object-fit: cover; display: block;">
                </a>
            @else
                <span class="text-muted small font-italic">-</span>
            @endif
        </td>

        <!-- Status -->
        <td class="text-center">
            <span class="badge {{ $statusClass }} px-2 py-1 font-weight-bold" style="font-size: 11px; border-radius: 12px;">
                {{ $data->status }}
            </span>
            @if($data->order_status && $data->type === 'order')
                <small class="d-block text-muted mt-1" style="font-size: 10px;">
                    {{ $data->order_status }}
                </small>
            @endif
        </td>

        <!-- Amount -->
        <td class="text-right">
            <strong class="text-dark font-weight-bold" style="font-size: 14.5px;">
                {{ $data->currency_sign }} {{ number_format($data->amount, 2) }}
            </strong>
        </td>

        <!-- Date & Time -->
        <td>
            <span class="d-block text-dark font-weight-bold" style="font-size: 12px;">
                {{ $data->created_at ? $data->created_at->format('M d, Y') : '-' }}
            </span>
            <small class="text-muted" style="font-size: 10.5px;">
                {{ $data->created_at ? $data->created_at->format('h:i A') : '' }}
            </small>
        </td>

        <!-- Actions -->
        <td class="text-center">
            <div class="d-flex align-items-center justify-content-center gap-1" style="gap: 4px;">
                <!-- Quick View Modal Button -->
                <button type="button" class="btn btn-sm btn-primary px-2 py-1 font-weight-bold" title="{{ __('Quick View Details') }}" onclick='openTxnModal({!! json_encode($jsonItem) !!})'>
                    <i class="fas fa-eye"></i>
                </button>

                @if($data->direct_url)
                    <!-- Direct Link Button -->
                    <a href="{{ $data->direct_url }}" class="btn btn-sm btn-outline-info px-2 py-1" title="{{ $data->direct_label }}">
                        <i class="fas fa-external-link-alt"></i>
                    </a>
                @endif

                <!-- Delete Button -->
                <button type="button" class="btn btn-sm btn-outline-danger px-2 py-1" title="{{ __('Delete Record') }}" onclick="confirmDeleteTxn('{{ route('back.transaction.delete', $data->id) }}')">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="10" class="text-center py-5 text-muted">
            <i class="fas fa-exchange-alt fa-3x text-secondary mb-3 d-block"></i>
            <h5>{{ __('No Transactions Found') }}</h5>
            <p class="small text-muted">{{ __('Transactions from orders, fine approvals, store requests, and deposit requests will appear here.') }}</p>
        </td>
    </tr>
@endforelse