@extends('master.seller')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between">
                <h3 class="mb-0 text-dark"><b><i class="fas fa-wallet text-primary mr-2"></i> {{ __('Store Wallet & Balance') }}</b></h3>
                <a href="{{ route('seller.transaction.index') }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-history mr-1"></i> {{ __('View Full Transaction History') }}
                </a>
            </div>
        </div>
    </div>

    @include('alerts.alerts')

    <!-- Top Stats Row -->
    <div class="row mb-4">
        <!-- 1. Current Balance Card -->
        <div class="col-xl-4 col-md-6 mb-3">
            <div class="card border-left-success shadow h-100 py-2" style="border-left: 5px solid #28a745 !important; border-radius: 10px;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                {{ __('Available Wallet Balance') }}
                            </div>
                            <div class="h3 mb-0 font-weight-bold text-gray-800">
                                {{ PriceHelper::adminCurrency() }} {{ number_format($seller->balance ?? 0, 2) }}
                            </div>
                            <small class="text-muted mt-1 d-block">
                                <i class="fas fa-info-circle text-info mr-1"></i> {{ __('Used for order commission deductions') }}
                            </small>
                        </div>
                        <div class="col-auto">
                            <div class="rounded-circle d-flex align-items-center justify-content-center bg-success text-white shadow-sm" style="width: 50px; height: 50px; font-size: 22px;">
                                <i class="fas fa-coins"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Free Orders Status Card -->
        <div class="col-xl-4 col-md-6 mb-3">
            @php
                $freeOrdersAllowed = (int)($setting->vendor_free_orders ?? 5);
                $vendorOrdersCount = \App\Models\Order::where('vendor_id', $user->id)->count();
                $freeRemaining = max(0, $freeOrdersAllowed - $vendorOrdersCount);
            @endphp
            <div class="card border-left-info shadow h-100 py-2" style="border-left: 5px solid #17a2b8 !important; border-radius: 10px;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                {{ __('Free Orders Allowance') }}
                            </div>
                            <div class="h3 mb-0 font-weight-bold text-gray-800">
                                {{ $vendorOrdersCount }} / {{ $freeOrdersAllowed }}
                            </div>
                            <small class="mt-1 d-block font-weight-bold {{ $freeRemaining > 0 ? 'text-success' : 'text-danger' }}">
                                @if($freeRemaining > 0)
                                    <i class="fas fa-gift mr-1"></i> {{ $freeRemaining }} {{ __('Free order(s) remaining (No balance cut)') }}
                                @else
                                    <i class="fas fa-check-double mr-1"></i> {{ __('Free limit reached. Commission applies on orders.') }}
                                @endif
                            </small>
                        </div>
                        <div class="col-auto">
                            <div class="rounded-circle d-flex align-items-center justify-content-center bg-info text-white shadow-sm" style="width: 50px; height: 50px; font-size: 22px;">
                                <i class="fas fa-gift"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Commission Rate Card -->
        <div class="col-xl-4 col-md-12 mb-3">
            <div class="card border-left-warning shadow h-100 py-2" style="border-left: 5px solid #ffc107 !important; border-radius: 10px;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                {{ __('Store Commission Rate') }}
                            </div>
                            <div class="h3 mb-0 font-weight-bold text-gray-800">
                                {{ $setting->vendor_commission_percent ?? 2 }}%
                            </div>
                            <small class="text-muted mt-1 d-block">
                                <i class="fas fa-shield-alt text-warning mr-1"></i> {{ __('Deducted only after free orders limit') }}
                            </small>
                        </div>
                        <div class="col-auto">
                            <div class="rounded-circle d-flex align-items-center justify-content-center bg-warning text-dark shadow-sm" style="width: 50px; height: 50px; font-size: 22px;">
                                <i class="fas fa-percentage"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content: Deposit Form & Instructions -->
    <div class="row">
        <!-- 1. Add Balance Submission Form -->
        <div class="col-lg-7 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary text-white py-3">
                    <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-plus-circle mr-2"></i> {{ __('Add Wallet Balance (Deposit Request)') }}</h6>
                </div>
                <div class="card-body p-4">
                    <div class="alert alert-info border-0 shadow-sm mb-4" style="border-radius: 8px;">
                        <h6 class="font-weight-bold mb-1"><i class="fas fa-info-circle mr-1"></i> {{ __('Minimum Balance Requirement:') }}</h6>
                        <p class="mb-0 small">
                            {{ __('Minimum amount to add:') }} <strong class="text-dark">{{ PriceHelper::adminCurrency() }} {{ number_format($setting->vendor_min_balance ?? 500, 2) }}</strong>.
                            {{ __('Transfer the amount to any admin account shown on the right, then submit your payment details below for instant approval.') }}
                        </p>
                    </div>

                    <form action="{{ route('seller.wallet.store') }}" method="POST" enctype="multipart/form-data" id="depositForm" onsubmit="return validateDepositForm()">
                        @csrf

                        <!-- Payment Method / Receiving Account Select -->
                        <div class="form-group mb-3">
                            <label for="payment_method" class="font-weight-bold">
                                {{ __('Select Payment Method / Account Sent To') }} <span class="text-danger">*</span>
                            </label>
                            <select name="payment_method" id="payment_method" class="form-control" required onchange="handlePaymentMethodChange(this)">
                                <option value="" disabled {{ old('payment_method') ? '' : 'selected' }}>{{ __('--- Select Admin Account Sent To ---') }}</option>
                                @forelse($receivingAccounts as $acc)
                                    <option value="{{ $acc->payment_method }} - {{ $acc->account_name }} ({{ $acc->account_number }})" 
                                            data-acc-id="{{ $acc->id }}"
                                            {{ old('payment_method') == ($acc->payment_method . ' - ' . $acc->account_name . ' (' . $acc->account_number . ')') ? 'selected' : '' }}>
                                        {{ $acc->payment_method }} &mdash; {{ $acc->account_name }} ({{ $acc->account_number }})
                                    </option>
                                @empty
                                    <option value="" disabled>{{ __('No receiving accounts added by Admin yet.') }}</option>
                                @endforelse
                            </select>
                        </div>

                        <div class="row">
                            <!-- Sender Bank / Wallet Name -->
                            <div class="col-md-12 mb-3">
                                <label for="bank_name" class="font-weight-bold">
                                    <i class="fas fa-university text-primary mr-1"></i> {{ __('Send From Bank / Wallet Title') }} <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="bank_name" id="bank_name" class="form-control" value="{{ old('bank_name') }}" placeholder="{{ __('e.g. Easypaisa, HBL, JazzCash, Meezan Bank, SadaPay') }}" required>
                            </div>

                            <!-- Sender Account Name -->
                            <div class="col-md-6 mb-3">
                                <label for="account_name" class="font-weight-bold">
                                    {{ __('Send By Account Name (Holder)') }} <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="account_name" id="account_name" class="form-control" value="{{ old('account_name') }}" placeholder="{{ __('e.g. Ahmad Khan') }}" required>
                            </div>

                            <!-- Sender Account Number -->
                            <div class="col-md-6 mb-3">
                                <label for="account_number" class="font-weight-bold">
                                    {{ __('Send By Account Number / Phone') }} <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="account_number" id="account_number" class="form-control" value="{{ old('account_number') }}" placeholder="{{ __('e.g. 03001234567') }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Payment Amount -->
                            <div class="col-md-6 mb-3">
                                <label for="amount" class="font-weight-bold">
                                    {{ __('Payment Amount') }} <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">{{ PriceHelper::adminCurrency() }}</span>
                                    </div>
                                    <input type="number" step="0.01" min="{{ $setting->vendor_min_balance ?? 1 }}" name="amount" id="amount" class="form-control" value="{{ old('amount', $setting->vendor_min_balance ?? 500) }}" placeholder="e.g. 1000" required oninput="checkMinAmount(this)">
                                </div>
                                <small id="amountHelper" class="text-muted d-block mt-1">
                                    {{ __('Minimum required:') }} <strong>{{ PriceHelper::adminCurrency() }} {{ number_format($setting->vendor_min_balance ?? 500, 2) }}</strong>
                                </small>
                                <small id="amountError" class="text-danger font-weight-bold d-none mt-1">
                                    <i class="fas fa-exclamation-triangle mr-1"></i> {{ __('Minimum balance to add is :currency :min. You cannot add less than this.', ['currency' => PriceHelper::adminCurrency(), 'min' => number_format($setting->vendor_min_balance ?? 500, 2)]) }}
                                </small>
                            </div>

                            <!-- Transaction ID -->
                            <div class="col-md-6 mb-3">
                                <label for="txn_id" class="font-weight-bold">
                                    {{ __('Transaction ID / Ref #') }} <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="txn_id" id="txn_id" class="form-control" value="{{ old('txn_id') }}" placeholder="{{ __('e.g. 3482910398') }}" required>
                            </div>
                        </div>

                        <!-- Payment Screenshot Upload -->
                        <div class="form-group mb-4">
                            <label class="font-weight-bold d-flex justify-content-between align-items-center mb-2">
                                <span><i class="fas fa-receipt text-primary mr-1"></i> {{ __('Payment Screenshot (Proof of Transfer)') }} <span class="text-danger">*</span></span>
                                <span class="badge badge-light border text-muted font-weight-normal">{{ __('JPG, PNG, WEBP, PDF (Max 10MB)') }}</span>
                            </label>

                            <!-- Modern Dropzone Container -->
                            <div id="screenshotDropzone" class="upload-dropzone text-center p-4 rounded position-relative">
                                <input type="file" name="screenshot" id="screenshot" class="dropzone-input" accept="image/*,application/pdf" required onchange="handleScreenshotSelected(this)">
                                
                                <!-- Idle State UI -->
                                <div id="dropzoneIdleState" class="dropzone-content">
                                    <div class="upload-icon-wrapper mb-2">
                                        <i class="fas fa-cloud-upload-alt fa-2x text-primary"></i>
                                    </div>
                                    <h6 class="font-weight-bold text-dark mb-1">{{ __('Click to Upload or Drag & Drop Screenshot') }}</h6>
                                    <p class="small text-muted mb-2">{{ __('Upload clear receipt showing Transaction ID, Date & Amount') }}</p>
                                    <span class="btn btn-outline-primary btn-sm px-3 shadow-none font-weight-bold" style="pointer-events: none;">
                                        <i class="fas fa-folder-open mr-1"></i> {{ __('Browse Files') }}
                                    </span>
                                </div>

                                <!-- Active Preview State UI (shown when file is selected) -->
                                <div id="dropzonePreviewState" class="d-none">
                                    <div class="preview-card bg-white p-3 rounded border text-left shadow-sm">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <img id="screenshotPreviewImg" src="" alt="Proof Preview" class="img-thumbnail rounded" style="width: 75px; height: 75px; object-fit: cover;">
                                            </div>
                                            <div class="col">
                                                <div class="d-flex align-items-center mb-1">
                                                    <span class="badge badge-success px-2 py-1 mr-2"><i class="fas fa-check-circle mr-1"></i> {{ __('Proof Attached') }}</span>
                                                    <small id="screenshotFileSize" class="text-muted font-weight-bold"></small>
                                                </div>
                                                <h6 id="screenshotFileName" class="text-dark font-weight-bold mb-1 text-truncate" style="max-width: 260px;"></h6>
                                                <small class="text-success"><i class="fas fa-shield-alt mr-1"></i> {{ __('Ready to submit for admin approval') }}</small>
                                            </div>
                                            <div class="col-auto">
                                                <button type="button" class="btn btn-danger btn-sm py-1 px-2" onclick="removeScreenshotFile(event)" title="{{ __('Remove file') }}">
                                                    <i class="fas fa-times mr-1"></i> {{ __('Remove') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <small class="text-muted mt-1 d-block"><i class="fas fa-info-circle mr-1 text-primary"></i> {{ __('Ensure Transaction ID matches the uploaded screenshot.') }}</small>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block py-2 font-weight-bold shadow-sm" id="submitDepositBtn">
                            <i class="fas fa-paper-plane mr-1"></i> {{ __('Submit Deposit Request') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- 2. Selected Admin Receiving Payment Account Display -->
        <div class="col-lg-5 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-dark text-white py-3">
                    <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-university mr-2"></i> {{ __('Admin Receiving Account Details') }}</h6>
                </div>
                <div class="card-body p-4">
                    <!-- Default Placeholder when no account is selected -->
                    <div id="no-account-selected-box" class="text-center py-5 text-muted">
                        <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-3 shadow-sm" style="width: 70px; height: 70px;">
                            <i class="fas fa-hand-pointer fa-2x text-primary"></i>
                        </div>
                        <h6 class="font-weight-bold text-dark mb-1">{{ __('Select a Payment Method') }}</h6>
                        <p class="small text-muted mb-0">
                            {{ __('Please choose a payment method from the dropdown on the left. The selected receiving account details will appear here.') }}
                        </p>
                    </div>

                    <!-- Individual Receiving Account Cards (Shown dynamically upon selection) -->
                    @forelse($receivingAccounts as $acc)
                        <div id="admin-acc-card-{{ $acc->id }}" class="admin-acc-card d-none">
                            <div class="card border shadow-sm mb-3" style="border-radius: 12px; background: #ffffff; border-left: 5px solid #0d6efd !important;">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                                        <div>
                                            <span class="badge badge-primary px-2 py-1 mb-1">{{ __('Verified Admin Account') }}</span>
                                            <h5 class="text-primary font-weight-bold mb-0">
                                                <i class="fas fa-money-check-alt mr-1"></i> {{ $acc->payment_method }}
                                            </h5>
                                        </div>
                                        <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i> {{ __('Active') }}</span>
                                    </div>

                                    <div class="mb-3">
                                        <label class="small text-muted font-weight-bold text-uppercase mb-1">{{ __('Account Title / Holder Name') }}</label>
                                        <div class="d-flex justify-content-between align-items-center bg-light border p-2 rounded">
                                            <strong class="text-dark h6 mb-0">{{ $acc->account_name }}</strong>
                                            <button type="button" class="btn btn-outline-primary btn-sm py-1 px-2 font-weight-bold" onclick="copyToClipboard('{{ $acc->account_name }}', this)">
                                                <i class="fas fa-copy mr-1"></i> {{ __('Copy') }}
                                            </button>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="small text-muted font-weight-bold text-uppercase mb-1">{{ __('Account Number / Phone / IBAN') }}</label>
                                        <div class="d-flex justify-content-between align-items-center bg-light border p-2 rounded">
                                            <code class="font-weight-bold text-dark h5 mb-0" style="letter-spacing: 1px;">{{ $acc->account_number }}</code>
                                            <button type="button" class="btn btn-outline-primary btn-sm py-1 px-2 font-weight-bold" onclick="copyToClipboard('{{ $acc->account_number }}', this)">
                                                <i class="fas fa-copy mr-1"></i> {{ __('Copy') }}
                                            </button>
                                        </div>
                                    </div>

                                    @if($acc->note)
                                        <div class="alert alert-warning py-2 px-3 mb-3 small" style="border-radius: 8px;">
                                            <strong><i class="fas fa-info-circle mr-1"></i> {{ __('Instructions / Note:') }}</strong>
                                            <div class="mt-1">{{ $acc->note }}</div>
                                        </div>
                                    @endif

                                    <div class="p-3 bg-light rounded border">
                                        <h6 class="font-weight-bold text-dark small mb-2"><i class="fas fa-list-ol text-primary mr-1"></i> {{ __('Payment Steps:') }}</h6>
                                        <ol class="small text-muted pl-3 mb-0">
                                            <li>{{ __('Transfer amount to the account details above.') }}</li>
                                            <li>{{ __('Take a screenshot and note down the Transaction ID.') }}</li>
                                            <li>{{ __('Enter Sender info & Txn ID and submit form on the left.') }}</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-university fa-3x mb-2 d-block text-muted"></i>
                            <p class="mb-0">{{ __('No receiving accounts configured by admin yet.') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Deposit Requests History Table -->
    <div class="card shadow mb-4">
        <div class="card-header bg-light py-3">
            <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-list-alt mr-2 text-primary"></i> {{ __('My Deposit Requests History') }}</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>{{ __('ID') }}</th>
                            <th>{{ __('Payment Method') }}</th>
                            <th>{{ __('Send By') }}</th>
                            <th>{{ __('Amount') }}</th>
                            <th>{{ __('Txn ID') }}</th>
                            <th>{{ __('Proof') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Date') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($depositRequests as $req)
                            <tr>
                                <td>#{{ $req->id }}</td>
                                <td><strong class="text-primary">{{ $req->payment_method }}</strong></td>
                                <td>
                                    @if($req->bank_name)
                                        <span class="badge badge-info mb-1">{{ $req->bank_name }}</span>
                                    @endif
                                    <div><strong>{{ $req->account_name }}</strong></div>
                                    <small class="text-muted">{{ $req->account_number }}</small>
                                </td>
                                <td>
                                    <strong class="text-success font-weight-bold">
                                        + {{ PriceHelper::adminCurrency() }} {{ number_format($req->amount, 2) }}
                                    </strong>
                                </td>
                                <td><code>{{ $req->txn_id }}</code></td>
                                <td>
                                    @if($req->screenshot)
                                        <a href="{{ asset('storage/images/deposits/' . $req->screenshot) }}" target="_blank" class="btn btn-sm btn-outline-info py-0 px-2">
                                            <i class="fas fa-image mr-1"></i> {{ __('View Proof') }}
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($req->status == 'approved')
                                        <span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i> {{ __('Approved') }}</span>
                                    @elseif($req->status == 'rejected')
                                        <span class="badge badge-danger"><i class="fas fa-times-circle mr-1"></i> {{ __('Rejected') }}</span>
                                        @if($req->admin_note)
                                            <div class="small text-danger mt-1">{{ $req->admin_note }}</div>
                                        @endif
                                    @else
                                        <span class="badge badge-warning text-dark"><i class="fas fa-clock mr-1"></i> {{ __('Pending Approval') }}</span>
                                    @endif
                                </td>
                                <td><small>{{ $req->created_at->format('M d, Y h:i A') }}</small></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    <i class="fas fa-wallet fa-3x mb-2 d-block text-muted"></i>
                                    {{ __('No deposit requests submitted yet.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-3">
                {{ $depositRequests->links() }}
            </div>
        </div>
    </div>
</div>

<script>
    const MIN_BALANCE = {{ (float)($setting->vendor_min_balance ?? 500) }};

    function checkMinAmount(input) {
        const val = parseFloat(input.value) || 0;
        const errorEl = document.getElementById('amountError');
        const helperEl = document.getElementById('amountHelper');
        const submitBtn = document.getElementById('submitDepositBtn');

        if (val < MIN_BALANCE) {
            errorEl.classList.remove('d-none');
            helperEl.classList.add('d-none');
            input.classList.add('is-invalid');
        } else {
            errorEl.classList.add('d-none');
            helperEl.classList.remove('d-none');
            input.classList.remove('is-invalid');
        }
    }

    function validateDepositForm() {
        const amountInput = document.getElementById('amount');
        const val = parseFloat(amountInput.value) || 0;
        if (val < MIN_BALANCE) {
            alert('Minimum balance to add is {{ PriceHelper::adminCurrency() }} ' + MIN_BALANCE + '. You cannot add less than this.');
            amountInput.focus();
            return false;
        }
        return true;
    }

    function handleScreenshotSelected(input) {
        const file = input.files ? input.files[0] : null;
        if (!file) return;

        const idleState = document.getElementById('dropzoneIdleState');
        const previewState = document.getElementById('dropzonePreviewState');
        const previewImg = document.getElementById('screenshotPreviewImg');
        const fileNameEl = document.getElementById('screenshotFileName');
        const fileSizeEl = document.getElementById('screenshotFileSize');

        fileNameEl.textContent = file.name;
        const sizeInKB = (file.size / 1024).toFixed(1);
        const sizeFormatted = file.size > 1048576 
            ? (file.size / (1024 * 1024)).toFixed(2) + ' MB' 
            : sizeInKB + ' KB';
        fileSizeEl.textContent = sizeFormatted;

        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
            }
            reader.readAsDataURL(file);
        } else {
            // PDF or other document icon placeholder
            previewImg.src = 'https://cdn-icons-png.flaticon.com/512/337/337946.png';
        }

        idleState.classList.add('d-none');
        previewState.classList.remove('d-none');
    }

    function removeScreenshotFile(event) {
        if (event) {
            event.stopPropagation();
            event.preventDefault();
        }
        const fileInput = document.getElementById('screenshot');
        fileInput.value = '';

        const idleState = document.getElementById('dropzoneIdleState');
        const previewState = document.getElementById('dropzonePreviewState');
        const previewImg = document.getElementById('screenshotPreviewImg');

        previewImg.src = '';
        previewState.classList.add('d-none');
        idleState.classList.remove('d-none');
    }

    // Drag and Drop Event Listeners
    document.addEventListener('DOMContentLoaded', function() {
        const dropzone = document.getElementById('screenshotDropzone');
        const fileInput = document.getElementById('screenshot');

        if (dropzone && fileInput) {
            ['dragenter', 'dragover'].forEach(eventName => {
                dropzone.addEventListener(eventName, function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    dropzone.classList.add('dragover');
                }, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropzone.addEventListener(eventName, function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    dropzone.classList.remove('dragover');
                }, false);
            });

            dropzone.addEventListener('drop', function(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                if (files && files.length) {
                    fileInput.files = files;
                    handleScreenshotSelected(fileInput);
                }
            }, false);
        }
    });

    function handlePaymentMethodChange(select) {
        const selectedOption = select.options[select.selectedIndex];
        const accId = selectedOption ? selectedOption.getAttribute('data-acc-id') : null;

        // Hide all account cards
        document.querySelectorAll('.admin-acc-card').forEach(function(card) {
            card.classList.add('d-none');
        });

        const noAccBox = document.getElementById('no-account-selected-box');

        if (accId) {
            if (noAccBox) noAccBox.classList.add('d-none');
            const targetCard = document.getElementById('admin-acc-card-' + accId);
            if (targetCard) {
                targetCard.classList.remove('d-none');
            }
        } else {
            if (noAccBox) noAccBox.classList.remove('d-none');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const paymentSelect = document.getElementById('payment_method');
        if (paymentSelect && paymentSelect.value) {
            handlePaymentMethodChange(paymentSelect);
        }
    });

    function copyToClipboard(text, btn) {
        navigator.clipboard.writeText(text).then(function() {
            const orig = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check mr-1"></i> Copied!';
            btn.classList.remove('btn-outline-primary');
            btn.classList.add('btn-success');
            setTimeout(function() {
                btn.innerHTML = orig;
                btn.classList.remove('btn-success');
                btn.classList.add('btn-outline-primary');
            }, 2000);
        });
    }
</script>

<style>
.upload-dropzone {
    border: 2px dashed #3b82f6;
    background: #f8fafc;
    border-radius: 12px !important;
    transition: all 0.25s ease-in-out;
    cursor: pointer;
}
.upload-dropzone:hover, .upload-dropzone.dragover {
    background: #eff6ff;
    border-color: #1d4ed8;
    box-shadow: 0 4px 15px rgba(59, 130, 246, 0.12);
}
.dropzone-input {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
    z-index: 5;
}
.upload-icon-wrapper {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 60px;
    height: 60px;
    background: #e0e7ff;
    border-radius: 50%;
    transition: transform 0.2s;
}
.upload-dropzone:hover .upload-icon-wrapper {
    transform: scale(1.08);
}
.preview-card {
    position: relative;
    z-index: 10;
    border-radius: 10px;
    border-left: 4px solid #10b981 !important;
}
</style>
@endsection
