@extends('master.back')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between">
                <h3 class="mb-0 text-dark"><b><i class="fas fa-cogs text-primary mr-2"></i> {{ __('Stores Rate & Setting') }}</b></h3>
                <a href="{{ route('back.store_request.index') }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-store mr-1"></i> {{ __('View Store Requests') }}
                </a>
            </div>
        </div>
    </div>

    @include('alerts.alerts')

    <!-- Admin Store Share Link Card -->
    <div class="card shadow-sm mb-4 border-0" style="border-radius: 12px; background: linear-gradient(to right, #f8fafc, #ffffff); border-left: 5px solid #0d6efd !important;">
        <div class="card-body p-3 p-md-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between">
                <div class="mb-2 mb-md-0">
                    <h5 class="font-weight-bold text-dark mb-1">
                        <i class="fas fa-store text-primary mr-2"></i> {{ __('Admin Official Store Link (Permanent)') }}
                        <span class="badge badge-light border text-dark font-weight-bold ml-2">{{ __('Code:') }} {{ $setting->getAdminStoreCode() }}</span>
                    </h5>
                    <p class="text-muted small mb-0">
                        {{ __('Shareable direct link for products listed directly by the platform/admin.') }}
                    </p>
                </div>
                <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                    <div class="input-group input-group-sm" style="min-width: 280px; max-width: 420px;">
                        <input type="text" id="adminStoreLinkInput" class="form-control font-weight-bold bg-white text-dark" value="{{ $setting->getAdminStoreUrl() }}" readonly style="cursor: text;">
                        <div class="input-group-append">
                            <button class="btn btn-primary font-weight-bold px-3 shadow-sm" type="button" id="copyAdminStoreBtn" onclick="copyAdminStoreLink()">
                                <i class="fas fa-copy mr-1" id="adminCopyIcon"></i> <span id="adminCopyBtnText">{{ __('Copy Link') }}</span>
                            </button>
                            <a href="{{ $setting->getAdminStoreUrl() }}" target="_blank" class="btn btn-outline-secondary font-weight-bold px-3" title="{{ __('Visit Admin Store') }}">
                                <i class="fas fa-external-link-alt mr-1"></i> {{ __('Visit') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- 1. STORE OPENING RATES & FREE TOGGLE -->
        <div class="col-lg-5 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary text-white py-3">
                    <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-sliders-h mr-2"></i> {{ __('Store Opening Fee & Settings') }}</h6>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('back.store_setting.update') }}" method="POST">
                        @csrf

                        <!-- Free Store Opening Toggle Box -->
                        <div class="card border mb-4 shadow-none" style="border-radius: 10px; background: #f8fafc; border-left: 4px solid #0d6efd !important;">
                            <div class="card-body p-3">
                                <div class="d-flex flex-wrap align-items-center justify-content-between mb-2 pb-2 border-bottom">
                                    <div class="font-weight-bold text-dark">
                                        <i class="fas fa-gift text-success mr-1"></i> {{ __('Store Opening Mode') }}
                                    </div>
                                    <div>
                                        <span class="badge {{ ($setting->is_store_opening_free ?? 1) == 1 ? 'badge-success' : 'badge-danger' }} px-2 py-1 font-weight-bold" id="freeStatusBadge">
                                            <i class="fas {{ ($setting->is_store_opening_free ?? 1) == 1 ? 'fa-check-circle' : 'fa-money-bill-wave' }} mr-1"></i>
                                            {{ ($setting->is_store_opening_free ?? 1) == 1 ? __('Free Opening (Active)') : __('Paid Opening (Active)') }}
                                        </span>
                                    </div>
                                </div>

                                <p class="small text-muted mb-3">
                                    {{ __('When turned ON, vendors can register their store for free. When OFF, vendors must pay the store opening fee and submit payment receipt.') }}
                                </p>

                                <div class="bg-white p-2 rounded border">
                                    <div class="custom-control custom-switch">
                                        <input type="hidden" name="is_store_opening_free" value="0">
                                        <input type="checkbox" class="custom-control-input" id="is_store_opening_free" name="is_store_opening_free" value="1" {{ ($setting->is_store_opening_free ?? 1) == 1 ? 'checked' : '' }} onchange="toggleFeeInput(this)">
                                        <label class="custom-control-label font-weight-bold text-dark cursor-pointer pl-1" for="is_store_opening_free" id="freeToggleLabel">
                                            {{ ($setting->is_store_opening_free ?? 1) == 1 ? __('Free Store Opening Enabled (No Fee)') : __('Paid Store Opening Enabled (Fee Applies)') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Store Opening Fee -->
                        <div class="form-group mb-4" id="feeInputGroup" style="{{ ($setting->is_store_opening_free ?? 1) == 1 ? 'opacity: 0.6;' : 'opacity: 1;' }}">
                            <label for="store_opening_fee" class="font-weight-bold">
                                {{ __('Store Opening Fee Amount') }} <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">{{ PriceHelper::adminCurrency() }}</span>
                                </div>
                                <input type="number" step="0.01" min="0" name="store_opening_fee" id="store_opening_fee" class="form-control" value="{{ old('store_opening_fee', $setting->store_opening_fee ?? 0) }}" placeholder="e.g. 1000" required>
                            </div>
                            <small class="text-muted">{{ __('The fee charged to users when applying for a new store (applies when Free Store Opening is OFF).') }}</small>
                        </div>

                        <hr class="my-4">
                        <h6 class="font-weight-bold text-primary mb-3"><i class="fas fa-percentage mr-1"></i> {{ __('Vendor Commission & Wallet Rules') }}</h6>

                        <!-- A) Vendor Free Orders Setting -->
                        <div class="form-group mb-4">
                            <label for="vendor_free_orders" class="font-weight-bold">
                                <i class="fas fa-gift text-success mr-1"></i> {{ __('Vendor Free Orders') }} <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-box-open"></i></span>
                                </div>
                                <input type="number" min="0" step="1" name="vendor_free_orders" id="vendor_free_orders" class="form-control" value="{{ old('vendor_free_orders', $setting->vendor_free_orders ?? 5) }}" placeholder="e.g. 5" required>
                                <div class="input-group-append">
                                    <span class="input-group-text">{{ __('Orders') }}</span>
                                </div>
                            </div>
                            <small class="text-muted">{{ __('Number of initial orders a vendor receives for FREE before commission-based balance deductions start applying.') }}</small>
                        </div>

                        <!-- B) Minimum Balance Setting -->
                        <div class="form-group mb-4">
                            <label for="vendor_min_balance" class="font-weight-bold">
                                <i class="fas fa-money-bill-wave text-warning mr-1"></i> {{ __('Minimum Balance to Add') }} <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">{{ PriceHelper::adminCurrency() }}</span>
                                </div>
                                <input type="number" step="0.01" min="0" name="vendor_min_balance" id="vendor_min_balance" class="form-control" value="{{ old('vendor_min_balance', $setting->vendor_min_balance ?? 500) }}" placeholder="e.g. 500" required>
                            </div>
                            <small class="text-muted">{{ __('Minimum deposit amount required when a vendor submits an "Add Balance" request. Submissions below this amount will be blocked.') }}</small>
                        </div>

                        <!-- C) Cut Commission Setting -->
                        <div class="form-group mb-4">
                            <label for="vendor_commission_percent" class="font-weight-bold">
                                <i class="fas fa-cut text-danger mr-1"></i> {{ __('Cut Commission') }} <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" max="100" name="vendor_commission_percent" id="vendor_commission_percent" class="form-control" value="{{ old('vendor_commission_percent', $setting->vendor_commission_percent ?? 2) }}" placeholder="e.g. 2" required>
                                <div class="input-group-append">
                                    <span class="input-group-text"><b>%</b></span>
                                </div>
                            </div>
                            <small class="text-muted">{{ __('Percentage commission deducted from vendor wallet on their own product sales once the free order limit is exceeded.') }}</small>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block py-2 font-weight-bold shadow-sm">
                            <i class="fas fa-save mr-1"></i> {{ __('Save Rate & Settings') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- 2. RECEIVING ACCOUNTS MANAGEMENT -->
        <div class="col-lg-7 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-dark text-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-wallet mr-2"></i> {{ __('Receiving Accounts (Payment Methods)') }}</h6>
                    <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#addAccountModal">
                        <i class="fas fa-plus-circle mr-1"></i> {{ __('Add Receiving Account') }}
                    </button>
                </div>
                <div class="card-body p-3">
                    <p class="small text-muted mb-3">
                        {{ __('These receiving payment accounts will be displayed to users during the payment step when applying to open a store.') }}
                    </p>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" width="100%" cellspacing="0" style="min-width: 620px;">
                            <thead class="thead-light">
                                <tr>
                                    <th style="min-width: 150px; white-space: nowrap;">{{ __('Method / Bank') }}</th>
                                    <th style="min-width: 140px; white-space: nowrap;">{{ __('Account Name') }}</th>
                                    <th style="min-width: 150px; white-space: nowrap;">{{ __('Account Number') }}</th>
                                    <th style="min-width: 85px; white-space: nowrap;" class="text-center">{{ __('Status') }}</th>
                                    <th style="min-width: 90px; white-space: nowrap;" class="text-center">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($accounts as $acc)
                                    <tr>
                                        <td style="min-width: 150px;">
                                            <strong class="text-primary d-block font-weight-bold">{{ $acc->payment_method }}</strong>
                                            @if($acc->note)
                                                <div class="small text-muted" style="word-break: break-word;">{{ Str::limit($acc->note, 40) }}</div>
                                            @endif
                                        </td>
                                        <td style="min-width: 140px; word-break: break-word;">
                                            <span class="font-weight-bold text-dark">{{ $acc->account_name }}</span>
                                        </td>
                                        <td style="min-width: 150px;">
                                            <code class="font-weight-bold text-dark px-2 py-1 bg-light border rounded d-inline-block">{{ $acc->account_number }}</code>
                                        </td>
                                        <td class="text-center align-middle" style="min-width: 85px;">
                                            @if($acc->status == 1)
                                                <span class="badge badge-success px-2 py-1">{{ __('Active') }}</span>
                                            @else
                                                <span class="badge badge-secondary px-2 py-1">{{ __('Inactive') }}</span>
                                            @endif
                                        </td>
                                        <td class="text-center align-middle" style="min-width: 90px;">
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-info btn-sm" onclick='openEditModal({!! json_encode($acc) !!})' title="{{ __('Edit') }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm" onclick="confirmDeleteAccount('{{ route('back.store_setting.account.delete', $acc->id) }}')" title="{{ __('Delete') }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            {{ __('No receiving accounts added yet. Click "Add Receiving Account" to create your first account (Easypaisa, JazzCash, Bank, etc.).') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. PROMOTION SETTINGS (1A: Highlight Tags & 1B: Days & Pricing) -->
    <div class="card shadow-sm mb-4 border-0" style="border-radius: 12px; overflow: hidden;">
        <div class="card-header bg-dark text-white py-3 d-flex align-items-center justify-content-between flex-wrap">
            <div>
                <h5 class="m-0 font-weight-bold text-white"><i class="fas fa-bullhorn text-warning mr-2"></i> {{ __('Promotion Settings (Vendor Product & Bundle Badges)') }}</h5>
                <small class="text-white-50">{{ __('Manage highlight badge tags and set global promotion durations & prices for vendor products and bundles.') }}</small>
            </div>
        </div>
        <div class="card-body p-4 bg-light">
            <div class="row">
                <!-- 1A: Highlight Tags Management -->
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <div class="card shadow-sm h-100 border-0">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                            <div>
                                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-tags mr-1"></i> {{ __('1A) Highlight Tags (Badges)') }}</h6>
                                <small class="text-muted">{{ __('Tags available for vendors to pick when promoting items (e.g. Best Product, Trending).') }}</small>
                            </div>
                            <button type="button" class="btn btn-primary btn-sm font-weight-bold shadow-sm" data-toggle="modal" data-target="#addTagModal">
                                <i class="fas fa-plus-circle mr-1"></i> {{ __('Add Tag') }}
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped mb-0" style="min-width: 480px;">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="width: 40px;">#</th>
                                            <th style="min-width: 130px; white-space: nowrap;">{{ __('Tag Name') }}</th>
                                            <th class="text-center" style="min-width: 130px; white-space: nowrap;">{{ __('Badge Preview') }}</th>
                                            <th class="text-center" style="min-width: 85px; white-space: nowrap;">{{ __('Status') }}</th>
                                            <th class="text-center" style="min-width: 90px; width: 90px; white-space: nowrap;">{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($promotionTags as $tag)
                                            <tr>
                                                <td class="font-weight-bold">{{ $loop->iteration }}</td>
                                                <td><strong class="text-dark">{{ $tag->name }}</strong></td>
                                                <td class="text-center">
                                                    <span class="badge badge-success px-2 py-1 font-weight-bold" style="background: linear-gradient(135deg, #15803d, #16a34a); letter-spacing: 0.3px; font-size: 11px;">
                                                        <i class="fas fa-certificate mr-1"></i> {{ $tag->name }}
                                                    </span>
                                                </td>
                                                <td class="text-center align-middle">
                                                    @if($tag->status == 1)
                                                        <span class="badge badge-success">{{ __('Active') }}</span>
                                                    @else
                                                        <span class="badge badge-secondary">{{ __('Inactive') }}</span>
                                                    @endif
                                                </td>
                                                <td class="text-center align-middle">
                                                    <div class="btn-group btn-group-sm">
                                                        <button type="button" class="btn btn-info" onclick='openEditTagModal({!! json_encode($tag) !!})' title="{{ __('Edit') }}">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-danger" onclick="confirmDeleteTag('{{ route('back.store_setting.tag.delete', $tag->id) }}')" title="{{ __('Delete') }}">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">
                                                    {{ __('No highlight tags added yet. Click "Add Tag" above to create tags (e.g. Best Product, Hot Deal).') }}
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 1B: Days & Pricing Management -->
                <div class="col-lg-6">
                    <div class="card shadow-sm h-100 border-0">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                            <div>
                                <h6 class="m-0 font-weight-bold text-success"><i class="fas fa-calendar-alt mr-1"></i> {{ __('1B) Promotion Days & Pricing') }}</h6>
                                <small class="text-muted">{{ __('Define promotion durations and price charged per product/bundle.') }}</small>
                            </div>
                            <button type="button" class="btn btn-success btn-sm font-weight-bold shadow-sm" data-toggle="modal" data-target="#addPlanModal">
                                <i class="fas fa-plus-circle mr-1"></i> {{ __('Add Duration & Price') }}
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped mb-0" style="min-width: 480px;">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="width: 40px;">#</th>
                                            <th style="min-width: 130px; white-space: nowrap;">{{ __('Duration (Days)') }}</th>
                                            <th style="min-width: 130px; white-space: nowrap;">{{ __('Price Per Item') }}</th>
                                            <th class="text-center" style="min-width: 85px; white-space: nowrap;">{{ __('Status') }}</th>
                                            <th class="text-center" style="min-width: 90px; width: 90px; white-space: nowrap;">{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($promotionPlans as $plan)
                                            <tr>
                                                <td class="font-weight-bold">{{ $loop->iteration }}</td>
                                                <td>
                                                    <span class="badge badge-primary px-2 py-1 font-weight-bold" style="font-size: 12px;">
                                                        <i class="fas fa-clock mr-1"></i> {{ $plan->days }} {{ __('Days') }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <strong class="text-success h6 mb-0 font-weight-bold">
                                                        {{ PriceHelper::adminCurrency() }} {{ number_format($plan->price, 2) }}
                                                    </strong>
                                                </td>
                                                <td class="text-center align-middle">
                                                    @if($plan->status == 1)
                                                        <span class="badge badge-success">{{ __('Active') }}</span>
                                                    @else
                                                        <span class="badge badge-secondary">{{ __('Inactive') }}</span>
                                                    @endif
                                                </td>
                                                <td class="text-center align-middle">
                                                    <div class="btn-group btn-group-sm">
                                                        <button type="button" class="btn btn-info" onclick='openEditPlanModal({!! json_encode($plan) !!})' title="{{ __('Edit') }}">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-danger" onclick="confirmDeletePlan('{{ route('back.store_setting.plan.delete', $plan->id) }}')" title="{{ __('Delete') }}">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">
                                                    {{ __('No duration options added yet. Click "Add Duration & Price" above to set day/pricing options (e.g. 3 Days = PKR 100).') }}
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ADD ACCOUNT MODAL -->
<div class="modal fade" id="addAccountModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title text-white"><i class="fas fa-plus-circle mr-2"></i> {{ __('Add Receiving Payment Account') }}</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('back.store_setting.account.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label for="add_payment_method" class="font-weight-bold">{{ __('Payment Method / Bank') }} <span class="text-danger">*</span></label>
                        <input type="text" name="payment_method" id="add_payment_method" class="form-control" placeholder="e.g. Easypaisa, JazzCash, HBL, Meezan Bank" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="add_account_name" class="font-weight-bold">{{ __('Account Title / Holder Name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="account_name" id="add_account_name" class="form-control" placeholder="e.g. Muhammad Ali" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="add_account_number" class="font-weight-bold">{{ __('Account / Mobile / IBAN Number') }} <span class="text-danger">*</span></label>
                        <input type="text" name="account_number" id="add_account_number" class="form-control" placeholder="e.g. 03001234567 or PK00HABB0000000000" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="add_note" class="font-weight-bold">{{ __('Instructions / Note (Optional)') }}</label>
                        <textarea name="note" id="add_note" rows="2" class="form-control" placeholder="e.g. Please add your order ID in reference"></textarea>
                    </div>

                    <div class="form-group mb-0">
                        <label for="add_status" class="font-weight-bold">{{ __('Status') }}</label>
                        <select name="status" id="add_status" class="form-control">
                            <option value="1">{{ __('Active (Visible to users)') }}</option>
                            <option value="0">{{ __('Inactive (Hidden)') }}</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-success px-4">{{ __('Add Account') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- EDIT ACCOUNT MODAL -->
<div class="modal fade" id="editAccountModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title text-white"><i class="fas fa-edit mr-2"></i> {{ __('Edit Receiving Account') }}</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editAccountForm" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label for="edit_payment_method" class="font-weight-bold">{{ __('Payment Method / Bank') }} <span class="text-danger">*</span></label>
                        <input type="text" name="payment_method" id="edit_payment_method" class="form-control" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="edit_account_name" class="font-weight-bold">{{ __('Account Title / Holder Name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="account_name" id="edit_account_name" class="form-control" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="edit_account_number" class="font-weight-bold">{{ __('Account / Mobile / IBAN Number') }} <span class="text-danger">*</span></label>
                        <input type="text" name="account_number" id="edit_account_number" class="form-control" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="edit_note" class="font-weight-bold">{{ __('Instructions / Note (Optional)') }}</label>
                        <textarea name="note" id="edit_note" rows="2" class="form-control"></textarea>
                    </div>

                    <div class="form-group mb-0">
                        <label for="edit_status" class="font-weight-bold">{{ __('Status') }}</label>
                        <select name="status" id="edit_status" class="form-control">
                            <option value="1">{{ __('Active (Visible to users)') }}</option>
                            <option value="0">{{ __('Inactive (Hidden)') }}</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-info px-4">{{ __('Update Account') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- DELETE ACCOUNT MODAL -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title text-white"><i class="fas fa-trash mr-2"></i> {{ __('Delete Account') }}</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="deleteAccountForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body p-4 text-center">
                    <p class="text-dark">{{ __('Are you sure you want to delete this receiving account? It will no longer be visible to applicants.') }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('Delete Account') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 1A: ADD TAG MODAL -->
<div class="modal fade" id="addTagModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white"><i class="fas fa-tag mr-2"></i> {{ __('Add Highlight Tag') }}</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('back.store_setting.tag.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label for="add_tag_name" class="font-weight-bold">{{ __('Tag Name / Badge Text') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="add_tag_name" class="form-control" placeholder="e.g. Best Product, Trending, Hot Deal, Most Selling" required>
                        <small class="text-muted">{{ __('This exact text will be displayed at top-center of promoted product and bundle cards.') }}</small>
                    </div>
                    <div class="form-group mb-0">
                        <label for="add_tag_status" class="font-weight-bold">{{ __('Status') }}</label>
                        <select name="status" id="add_tag_status" class="form-control">
                            <option value="1">{{ __('Active (Visible in vendor dropdown)') }}</option>
                            <option value="0">{{ __('Inactive (Hidden)') }}</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary px-4">{{ __('Add Tag') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 1A: EDIT TAG MODAL -->
<div class="modal fade" id="editTagModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title text-white"><i class="fas fa-edit mr-2"></i> {{ __('Edit Highlight Tag') }}</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editTagForm" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label for="edit_tag_name" class="font-weight-bold">{{ __('Tag Name / Badge Text') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="edit_tag_name" class="form-control" required>
                    </div>
                    <div class="form-group mb-0">
                        <label for="edit_tag_status" class="font-weight-bold">{{ __('Status') }}</label>
                        <select name="status" id="edit_tag_status" class="form-control">
                            <option value="1">{{ __('Active (Visible in vendor dropdown)') }}</option>
                            <option value="0">{{ __('Inactive (Hidden)') }}</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-info px-4">{{ __('Update Tag') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 1A: DELETE TAG MODAL -->
<div class="modal fade" id="deleteTagModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title text-white"><i class="fas fa-trash mr-2"></i> {{ __('Delete Highlight Tag') }}</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="deleteTagForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body p-4 text-center">
                    <p class="text-dark">{{ __('Are you sure you want to delete this highlight tag? Existing active promotions with this tag will continue until expiry.') }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('Delete Tag') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 1B: ADD PLAN MODAL -->
<div class="modal fade" id="addPlanModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title text-white"><i class="fas fa-plus-circle mr-2"></i> {{ __('Add Duration & Price Option') }}</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('back.store_setting.plan.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label for="add_plan_days" class="font-weight-bold">{{ __('Number of Days') }} <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" step="1" min="1" name="days" id="add_plan_days" class="form-control" placeholder="e.g. 3, 7, 15, 30" required>
                            <div class="input-group-append">
                                <span class="input-group-text">{{ __('Days') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="add_plan_price" class="font-weight-bold">{{ __('Price Per Product/Bundle') }} <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">{{ PriceHelper::adminCurrency() }}</span>
                            </div>
                            <input type="number" step="0.01" min="0" name="price" id="add_plan_price" class="form-control" placeholder="e.g. 100, 200, 350" required>
                        </div>
                    </div>
                    <div class="form-group mb-0">
                        <label for="add_plan_status" class="font-weight-bold">{{ __('Status') }}</label>
                        <select name="status" id="add_plan_status" class="form-control">
                            <option value="1">{{ __('Active (Available to vendors)') }}</option>
                            <option value="0">{{ __('Inactive (Hidden)') }}</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-success px-4">{{ __('Add Option') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 1B: EDIT PLAN MODAL -->
<div class="modal fade" id="editPlanModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title text-white"><i class="fas fa-edit mr-2"></i> {{ __('Edit Duration & Price') }}</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editPlanForm" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label for="edit_plan_days" class="font-weight-bold">{{ __('Number of Days') }} <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" step="1" min="1" name="days" id="edit_plan_days" class="form-control" required>
                            <div class="input-group-append">
                                <span class="input-group-text">{{ __('Days') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="edit_plan_price" class="font-weight-bold">{{ __('Price Per Product/Bundle') }} <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">{{ PriceHelper::adminCurrency() }}</span>
                            </div>
                            <input type="number" step="0.01" min="0" name="price" id="edit_plan_price" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group mb-0">
                        <label for="edit_plan_status" class="font-weight-bold">{{ __('Status') }}</label>
                        <select name="status" id="edit_plan_status" class="form-control">
                            <option value="1">{{ __('Active (Available to vendors)') }}</option>
                            <option value="0">{{ __('Inactive (Hidden)') }}</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-info px-4">{{ __('Update Option') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 1B: DELETE PLAN MODAL -->
<div class="modal fade" id="deletePlanModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title text-white"><i class="fas fa-trash mr-2"></i> {{ __('Delete Duration Option') }}</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="deletePlanForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body p-4 text-center">
                    <p class="text-dark">{{ __('Are you sure you want to delete this promotion duration and pricing option?') }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('Delete Option') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function toggleFeeInput(checkbox) {
        const badge = document.getElementById('freeStatusBadge');
        const label = document.getElementById('freeToggleLabel');
        const feeGroup = document.getElementById('feeInputGroup');

        if (checkbox.checked) {
            badge.innerHTML = '<i class="fas fa-check-circle mr-1"></i> ' + "{{ __('Free Opening (Active)') }}";
            badge.className = "badge badge-success px-2 py-1 font-weight-bold";
            if (label) label.innerText = "{{ __('Free Store Opening Enabled (No Fee)') }}";
            if (feeGroup) feeGroup.style.opacity = '0.6';
        } else {
            badge.innerHTML = '<i class="fas fa-money-bill-wave mr-1"></i> ' + "{{ __('Paid Opening (Active)') }}";
            badge.className = "badge badge-danger px-2 py-1 font-weight-bold";
            if (label) label.innerText = "{{ __('Paid Store Opening Enabled (Fee Applies)') }}";
            if (feeGroup) feeGroup.style.opacity = '1';
        }
    }

    function openEditModal(account) {
        $('#edit_payment_method').val(account.payment_method);
        $('#edit_account_name').val(account.account_name);
        $('#edit_account_number').val(account.account_number);
        $('#edit_note').val(account.note || '');
        $('#edit_status').val(account.status);

        const updateUrl = "{{ url('admin/store-settings/account/update') }}/" + account.id;
        $('#editAccountForm').attr('action', updateUrl);
        $('#editAccountModal').modal('show');
    }

    function confirmDeleteAccount(url) {
        $('#deleteAccountForm').attr('action', url);
        $('#deleteAccountModal').modal('show');
    }

    // 1A: Tag Edit & Delete
    function openEditTagModal(tag) {
        $('#edit_tag_name').val(tag.name);
        $('#edit_tag_status').val(tag.status);

        const updateUrl = "{{ url('admin/store-settings/promotion-tag/update') }}/" + tag.id;
        $('#editTagForm').attr('action', updateUrl);
        $('#editTagModal').modal('show');
    }

    function confirmDeleteTag(url) {
        $('#deleteTagForm').attr('action', url);
        $('#deleteTagModal').modal('show');
    }

    // 1B: Plan Edit & Delete
    function openEditPlanModal(plan) {
        $('#edit_plan_days').val(plan.days);
        $('#edit_plan_price').val(plan.price);
        $('#edit_plan_status').val(plan.status);

        const updateUrl = "{{ url('admin/store-settings/promotion-plan/update') }}/" + plan.id;
        $('#editPlanForm').attr('action', updateUrl);
        $('#editPlanModal').modal('show');
    }

    function confirmDeletePlan(url) {
        $('#deletePlanForm').attr('action', url);
        $('#deletePlanModal').modal('show');
    }

    function copyAdminStoreLink() {
        var input = document.getElementById('adminStoreLinkInput');
        if (!input) return;
        input.select();
        input.setSelectionRange(0, 99999);
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(input.value).then(handleCopied).catch(fallbackCopy);
        } else {
            fallbackCopy();
        }
        function fallbackCopy() {
            try {
                document.execCommand('copy');
                handleCopied();
            } catch (err) {
                alert('Failed to copy link.');
            }
        }
        function handleCopied() {
            var btn = document.getElementById('copyAdminStoreBtn');
            var icon = document.getElementById('adminCopyIcon');
            var text = document.getElementById('adminCopyBtnText');
            if (btn && icon && text) {
                var originalText = text.innerText;
                btn.classList.remove('btn-primary');
                btn.classList.add('btn-success');
                icon.className = 'fas fa-check mr-1';
                text.innerText = 'Copied!';
                setTimeout(function() {
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-primary');
                    icon.className = 'fas fa-copy mr-1';
                    text.innerText = originalText;
                }, 2500);
            }
        }
    }
</script>
@endsection
