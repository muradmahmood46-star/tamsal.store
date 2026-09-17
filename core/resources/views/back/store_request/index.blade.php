@extends('master.back')

@section('styles')
<style>
    .store-req-table-wrapper {
        width: 100% !important;
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch;
        margin-bottom: 1rem;
        border: 1px solid #ebedf2;
        border-radius: 4px;
    }
    .store-req-table-wrapper::-webkit-scrollbar {
        height: 6px;
    }
    .store-req-table-wrapper::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    .store-req-table-wrapper::-webkit-scrollbar-thumb {
        background: #ced4da;
        border-radius: 4px;
    }
    .store-req-table-wrapper::-webkit-scrollbar-thumb:hover {
        background: #6c757d;
    }
    .store-req-table {
        min-width: 950px;
        width: 100% !important;
        margin-bottom: 0 !important;
    }
    .store-req-table th {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        white-space: nowrap;
        background-color: #f8f9fa !important;
        vertical-align: middle !important;
        padding: 10px 8px !important;
        border-bottom: 2px solid #dee2e6 !important;
    }
    .store-req-table td {
        font-size: 13px;
        vertical-align: middle !important;
        padding: 8px 8px !important;
    }
    .store-req-table .col-id { width: 40px; text-align: center; }
    .store-req-table .col-store { min-width: 140px; max-width: 180px; }
    .store-req-table .col-applicant { min-width: 150px; max-width: 190px; }
    .store-req-table .col-cnic { width: 120px; text-align: center; white-space: nowrap; }
    .store-req-table .col-docs { width: 95px; text-align: center; white-space: nowrap; }
    .store-req-table .col-payment { min-width: 120px; white-space: nowrap; }
    .store-req-table .col-status { width: 100px; text-align: center; white-space: nowrap; }
    .store-req-table .col-date { width: 90px; text-align: center; white-space: nowrap; }
    .store-req-table .col-actions { width: 140px; text-align: center; white-space: nowrap; }
    .action-btn-group {
        display: inline-flex !important;
        white-space: nowrap !important;
    }
    .action-btn-group .btn {
        padding: 4px 7px !important;
        font-size: 12px !important;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between">
                <h3 class="mb-0 text-dark"><b><i class="fas fa-store text-primary mr-2"></i> {{ __('Store Requests (Vendor Applications)') }}</b></h3>
                <a href="{{ route('back.store_setting.index') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-cogs mr-1"></i> {{ __('Stores Rate & Setting') }}
                </a>
            </div>
        </div>
    </div>

    @include('alerts.alerts')

    <!-- Status Tabs -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="btn-group flex-wrap" role="group">
                <a href="{{ route('back.store_request.index') }}" class="btn btn-sm {{ empty($status) ? 'btn-primary' : 'btn-outline-primary' }}">
                    {{ __('All Requests') }} <span class="badge badge-light ml-1">{{ $counts['all'] }}</span>
                </a>
                <a href="{{ route('back.store_request.index', ['status' => 'Pending']) }}" class="btn btn-sm {{ $status == 'Pending' ? 'btn-warning text-dark font-weight-bold' : 'btn-outline-warning text-dark' }}">
                    <i class="fas fa-clock mr-1"></i> {{ __('Pending') }} <span class="badge badge-warning ml-1">{{ $counts['pending'] }}</span>
                </a>
                <a href="{{ route('back.store_request.index', ['status' => 'Approved']) }}" class="btn btn-sm {{ $status == 'Approved' ? 'btn-success' : 'btn-outline-success' }}">
                    <i class="fas fa-check mr-1"></i> {{ __('Approved') }} <span class="badge badge-success ml-1">{{ $counts['approved'] }}</span>
                </a>
                <a href="{{ route('back.store_request.index', ['status' => 'Rejected']) }}" class="btn btn-sm {{ $status == 'Rejected' ? 'btn-danger' : 'btn-outline-danger' }}">
                    <i class="fas fa-times mr-1"></i> {{ __('Rejected') }} <span class="badge badge-danger ml-1">{{ $counts['rejected'] }}</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('Store Applications List') }}</h6>
            <form action="{{ route('back.store_request.index') }}" method="GET" class="form-inline mt-2 mt-sm-0">
                @if($status)
                    <input type="hidden" name="status" value="{{ $status }}">
                @endif
                <div class="input-group input-group-sm">
                    <input type="text" name="search" class="form-control" placeholder="{{ __('Search shop, name, cnic...') }}" value="{{ $search }}">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </form>
        </div>

        <div class="card-body">
            <div class="table-responsive store-req-table-wrapper">
                <table class="table table-bordered table-striped table-hover store-req-table" id="store_request_table" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th class="col-id">#</th>
                            <th class="col-store">{{ __('Store Info') }}</th>
                            <th class="col-applicant">{{ __('Applicant Details') }}</th>
                            <th class="col-cnic">{{ __('CNIC / ID') }}</th>
                            <th class="col-docs">{{ __('Verification Docs') }}</th>
                            <th class="col-payment">{{ __('Payment') }}</th>
                            <th class="col-status">{{ __('Status') }}</th>
                            <th class="col-date">{{ __('Date') }}</th>
                            <th class="col-actions">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($datas as $data)
                            <tr>
                                <td class="col-id text-center font-weight-bold">{{ $loop->iteration }}</td>
                                <td class="col-store">
                                    <strong class="d-block text-dark">{{ $data->shop_name }}</strong>
                                    @if($data->product_types)
                                        <small class="text-primary d-block font-weight-bold" title="{{ $data->product_types }}"><i class="fas fa-tags mr-1"></i>{{ Str::limit($data->product_types, 28) }}</small>
                                    @endif
                                    <small class="text-muted d-block text-truncate" style="max-width: 170px;" title="{{ $data->shop_address }}"><i class="fas fa-map-marker-alt text-danger mr-1"></i>{{ Str::limit($data->shop_address, 35) }}</small>
                                </td>
                                <td class="col-applicant">
                                    <strong class="d-block text-dark">{{ $data->first_name }} {{ $data->last_name }}</strong>
                                    <small class="text-muted d-block"><i class="fas fa-phone mr-1"></i>{{ $data->phone }}</small>
                                    <small class="text-muted d-block text-truncate" style="max-width: 170px;" title="{{ $data->email }}"><i class="fas fa-envelope mr-1"></i>{{ $data->email }}</small>
                                </td>
                                <td class="col-cnic text-center">
                                    <span class="badge badge-light border font-weight-bold px-2 py-1">{{ $data->cnic }}</span>
                                </td>
                                <td class="col-docs text-center">
                                    <button type="button" class="btn btn-outline-info btn-xs py-1 px-2" onclick='viewDetailsModal({!! json_encode($data) !!})'>
                                        <i class="fas fa-images mr-1"></i>{{ __('Docs') }}
                                    </button>
                                </td>
                                <td class="col-payment">
                                    @if($data->is_free == 1)
                                        <span class="badge badge-success px-2 py-1 font-weight-bold">{{ __('FREE') }}</span>
                                    @else
                                        <div><span class="badge badge-primary px-2 py-1">{{ PriceHelper::storeOpeningFee($data->store_fee) }}</span></div>
                                        <small class="text-muted font-weight-bold d-block mt-1">{{ $data->account_type }}</small>
                                        @if($data->transaction_id)
                                            <small class="text-muted d-block" style="font-size: 11px;">TXN: <strong>{{ Str::limit($data->transaction_id, 14) }}</strong></small>
                                        @endif
                                    @endif
                                </td>
                                <td class="col-status text-center">
                                    @if($data->status == 'Pending')
                                        <span class="badge badge-warning text-dark px-2 py-1"><i class="fas fa-clock mr-1"></i>{{ __('Pending') }}</span>
                                    @elseif($data->status == 'Approved')
                                        <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i>{{ __('Approved') }}</span>
                                        @if(($data->seller_status ?? 'Active') == 'Blocked' || ($data->user && $data->user->is_seller_blocked == 1))
                                            <div class="mt-1"><span class="badge badge-danger px-2 py-1"><i class="fas fa-ban mr-1"></i>{{ __('Blocked') }}</span></div>
                                        @else
                                            <div class="mt-1"><span class="badge badge-primary px-2 py-1"><i class="fas fa-store mr-1"></i>{{ __('Active') }}</span></div>
                                        @endif
                                    @elseif($data->status == 'Rejected')
                                        <span class="badge badge-danger px-2 py-1"><i class="fas fa-times-circle mr-1"></i>{{ __('Rejected') }}</span>
                                    @endif
                                </td>
                                <td class="col-date text-center">
                                    <small class="font-weight-bold">{{ $data->created_at ? $data->created_at->format('d M, Y') : '' }}</small>
                                </td>
                                <td class="col-actions text-center">
                                    <div class="btn-group btn-group-sm action-btn-group" role="group">
                                        <button type="button" class="btn btn-info" title="{{ __('View Details') }}" onclick='viewDetailsModal({!! json_encode($data) !!})'>
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        @if($data->status != 'Approved')
                                            <button type="button" class="btn btn-success" title="{{ __('Approve Application') }}" onclick="confirmApprove('{{ route('back.store_request.approve', $data->id) }}', '{{ addslashes($data->shop_name) }}')">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        @endif

                                        @if($data->status != 'Rejected')
                                            <button type="button" class="btn btn-warning text-dark" title="{{ __('Reject Application') }}" onclick="openRejectModal('{{ route('back.store_request.reject', $data->id) }}', '{{ addslashes($data->shop_name) }}')">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif

                                        <button type="button" class="btn btn-danger" title="{{ __('Delete Record') }}" onclick="confirmDelete('{{ route('back.store_request.delete', $data->id) }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    <i class="fas fa-inbox fa-3x mb-2 d-block"></i>
                                    {{ __('No store requests found.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-3">
                {{ $datas->appends(request()->input())->links() }}
            </div>
        </div>
    </div>
</div>

<!-- VIEW DETAILS MODAL -->
<div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white" id="detailsModalLabel"><i class="fas fa-store mr-2"></i> <span id="modalShopName"></span></h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <!-- Applicant & Store Info -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded border h-100">
                            <h6 class="font-weight-bold text-primary border-bottom pb-2 mb-2"><i class="fas fa-user mr-1"></i> {{ __('Applicant Information') }}</h6>
                            <p class="mb-1"><strong>{{ __('Name:') }}</strong> <span id="modalApplicantName"></span></p>
                            <p class="mb-1"><strong>{{ __('Phone:') }}</strong> <span id="modalApplicantPhone"></span></p>
                            <p class="mb-1"><strong>{{ __('Email:') }}</strong> <span id="modalApplicantEmail"></span></p>
                            <p class="mb-1"><strong>{{ __('CNIC:') }}</strong> <span id="modalApplicantCnic" class="badge badge-dark"></span></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded border h-100">
                            <h6 class="font-weight-bold text-primary border-bottom pb-2 mb-2"><i class="fas fa-store mr-1"></i> {{ __('Store Details') }}</h6>
                            <p class="mb-1"><strong>{{ __('Store Name:') }}</strong> <span id="modalStoreNameText"></span></p>
                            <p class="mb-1"><strong>{{ __('Product Types:') }}</strong> <span id="modalProductTypes" class="badge badge-primary px-2 py-1" style="font-size: 12px; white-space: normal;"></span></p>
                            <p class="mb-1"><strong>{{ __('Store Address:') }}</strong> <span id="modalStoreAddress"></span></p>
                            <p class="mb-1"><strong>{{ __('Application Date:') }}</strong> <span id="modalDate"></span></p>
                            <p class="mb-1"><strong>{{ __('Request Status:') }}</strong> <span id="modalStatusBadge"></span></p>
                            <p class="mb-1" id="modalSellerStatusRow"><strong>{{ __('Seller Account:') }}</strong> <span id="modalSellerStatusBadge"></span></p>
                        </div>
                    </div>
                </div>

                <!-- Verification Documents Preview -->
                <h6 class="font-weight-bold text-dark border-bottom pb-2 mb-3"><i class="fas fa-file-alt mr-1"></i> {{ __('Uploaded Documents & Identity Proof') }}</h6>
                <div class="row mb-4">
                    <!-- ID Card Front -->
                    <div class="col-md-4 text-center mb-3">
                        <div class="card p-2 border bg-light h-100">
                            <span class="font-weight-bold small mb-2">{{ __('ID Card (CNIC)') }}</span>
                            <div id="modalIdCardWrap" class="mb-2" style="height: 140px; display:flex; align-items:center; justify-content:center; background:#fff; border-radius:4px; overflow:hidden;"></div>
                            <div id="modalIdCardLink"></div>
                        </div>
                    </div>

                    <!-- Selfie with ID Card -->
                    <div class="col-md-4 text-center mb-3">
                        <div class="card p-2 border bg-light h-100">
                            <span class="font-weight-bold small mb-2">{{ __('Selfie with ID Card') }}</span>
                            <div id="modalSelfieWrap" class="mb-2" style="height: 140px; display:flex; align-items:center; justify-content:center; background:#fff; border-radius:4px; overflow:hidden;"></div>
                            <div id="modalSelfieLink"></div>
                        </div>
                    </div>

                    <!-- Store Documents (Image / PDF) -->
                    <div class="col-md-4 text-center mb-3">
                        <div class="card p-2 border bg-light h-100">
                            <span class="font-weight-bold small mb-2">{{ __('Store Documents') }}</span>
                            <div id="modalStoreDocsWrap" class="mb-2" style="height: 140px; display:flex; align-items:center; justify-content:center; background:#fff; border-radius:4px; overflow:hidden;"></div>
                            <div id="modalStoreDocsLink"></div>
                        </div>
                    </div>
                </div>

                <!-- Sample Products Verification Preview -->
                <h6 class="font-weight-bold text-dark border-bottom pb-2 mb-3 mt-3"><i class="fas fa-boxes mr-1 text-primary"></i> {{ __('Sample Products Verification (3 Required Products)') }}</h6>
                <div class="row mb-4">
                    <!-- Product 1 -->
                    <div class="col-md-4 text-center mb-3">
                        <div class="card p-2 border bg-light h-100 shadow-sm">
                            <span class="badge badge-primary mb-1 small">{{ __('Product 1') }}</span>
                            <div class="font-weight-bold small mb-2 text-truncate" id="modalSampleProd1Name" title="">-</div>
                            <div id="modalSampleProd1Wrap" class="mb-2" style="height: 140px; display:flex; align-items:center; justify-content:center; background:#fff; border-radius:4px; overflow:hidden; border:1px solid #eee;"></div>
                            <div id="modalSampleProd1Link"></div>
                        </div>
                    </div>

                    <!-- Product 2 -->
                    <div class="col-md-4 text-center mb-3">
                        <div class="card p-2 border bg-light h-100 shadow-sm">
                            <span class="badge badge-primary mb-1 small">{{ __('Product 2') }}</span>
                            <div class="font-weight-bold small mb-2 text-truncate" id="modalSampleProd2Name" title="">-</div>
                            <div id="modalSampleProd2Wrap" class="mb-2" style="height: 140px; display:flex; align-items:center; justify-content:center; background:#fff; border-radius:4px; overflow:hidden; border:1px solid #eee;"></div>
                            <div id="modalSampleProd2Link"></div>
                        </div>
                    </div>

                    <!-- Product 3 -->
                    <div class="col-md-4 text-center mb-3">
                        <div class="card p-2 border bg-light h-100 shadow-sm">
                            <span class="badge badge-primary mb-1 small">{{ __('Product 3') }}</span>
                            <div class="font-weight-bold small mb-2 text-truncate" id="modalSampleProd3Name" title="">-</div>
                            <div id="modalSampleProd3Wrap" class="mb-2" style="height: 140px; display:flex; align-items:center; justify-content:center; background:#fff; border-radius:4px; overflow:hidden; border:1px solid #eee;"></div>
                            <div id="modalSampleProd3Link"></div>
                        </div>
                    </div>
                </div>

                <!-- Payment Step Details -->
                <div id="modalPaymentSection" class="p-3 bg-light rounded border">
                    <h6 class="font-weight-bold text-primary border-bottom pb-2 mb-2"><i class="fas fa-money-bill-wave mr-1"></i> {{ __('Payment Proof & Transaction Details') }}</h6>
                    <div class="row">
                        <div class="col-md-7">
                            <p class="mb-1"><strong>{{ __('Fee Type:') }}</strong> <span id="modalFeeType"></span></p>
                            <p class="mb-1"><strong>{{ __('Fee Amount:') }}</strong> <span id="modalFeeAmount" class="text-primary font-weight-bold"></span></p>
                            <p class="mb-1"><strong>{{ __('Payment Method:') }}</strong> <span id="modalPaymentMethod"></span></p>
                            <p class="mb-1"><strong>{{ __('Sender Account:') }}</strong> <span id="modalSenderAccount"></span></p>
                            <p class="mb-1"><strong>{{ __('Transaction ID / TRX:') }}</strong> <span id="modalTxnId" class="badge badge-info"></span></p>
                        </div>
                        <div class="col-md-5 text-center">
                            <span class="font-weight-bold small d-block mb-1">{{ __('Payment Receipt Screenshot') }}</span>
                            <div id="modalReceiptWrap" style="max-height: 130px; display:flex; align-items:center; justify-content:center; background:#fff; border-radius:4px; overflow:hidden; border:1px solid #ddd;"></div>
                            <div id="modalReceiptLink" class="mt-1"></div>
                        </div>
                    </div>
                </div>

                <!-- Rejection Reason if any -->
                <div id="modalRejectReasonSection" class="alert alert-danger mt-3 d-none">
                    <strong>{{ __('Rejection Reason:') }}</strong> <span id="modalRejectReasonText"></span>
                </div>

                <!-- Direct Message to Store Owner / Applicant -->
                <div class="card border mt-4 shadow-sm" style="border-radius: 8px; border-color: #cbd5e1 !important;">
                    <div class="card-header bg-dark text-white py-2 d-flex justify-content-between align-items-center" style="border-radius: 7px 7px 0 0;">
                        <span class="font-weight-bold" style="font-size: 13px;">
                            <i class="fas fa-paper-plane mr-1 text-info"></i> {{ __('Direct Message to Store Applicant / Owner') }}
                        </span>
                        <span class="badge badge-info small" id="modalDirectChatBadge">{{ __('Direct Line') }}</span>
                    </div>
                    <div class="card-body p-3 bg-white">
                        <!-- History of messages -->
                        <div id="modalChatHistory" class="p-2 mb-3 bg-light border rounded" style="max-height: 160px; overflow-y: auto; font-size: 12.5px; display: none;"></div>

                        <div class="input-group">
                            <input type="text" id="modalStoreMessageInput" class="form-control" placeholder="{{ __('Type a direct message to this store applicant (e.g. Please re-upload clear CNIC, congratulations, etc.)...') }}" onkeypress="handleModalStoreMsgKey(event)">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-primary font-weight-bold" id="modalSendStoreMsgBtn" onclick="sendDirectStoreMessage()">
                                    <i class="fas fa-paper-plane mr-1"></i> {{ __('Send Message') }}
                                </button>
                            </div>
                        </div>
                        <div id="modalMsgSuccessAlert" class="alert alert-success mt-2 py-1 px-2 small d-none"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                <div id="modalActionButtons"></div>
            </div>
        </div>
    </div>
</div>

<!-- APPROVE MODAL -->
<div class="modal fade" id="approveModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title text-white"><i class="fas fa-check-circle mr-2"></i> {{ __('Approve Store Request') }}</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="approveForm" method="POST">
                @csrf
                <div class="modal-body p-4 text-center">
                    <i class="fas fa-store fa-4x text-success mb-3"></i>
                    <h5 class="font-weight-bold">{{ __('Are you sure you want to approve this store?') }}</h5>
                    <p class="text-muted">{{ __('Approving this request will immediately upgrade the user to Seller and grant them full access to their Seller Dashboard.') }}</p>
                    <p class="font-weight-bold text-dark" id="approveShopName"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-success px-4">{{ __('Yes, Approve Store') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- REJECT MODAL -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title text-dark"><i class="fas fa-times-circle mr-2"></i> {{ __('Reject Store Request') }}</h5>
                <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <p class="text-muted mb-2">{{ __('Please specify the reason for rejecting this application (visible to the user so they can correct and reapply):') }}</p>
                    <p class="font-weight-bold text-dark mb-3" id="rejectShopName"></p>
                    <div class="form-group p-0">
                        <label for="reject_reason" class="font-weight-bold">{{ __('Rejection Reason / Note (Optional)') }}</label>
                        <textarea name="reject_reason" id="reject_reason" rows="4" class="form-control" placeholder="e.g. ID card photo is blurry, or incorrect transaction ID entered. Please upload a clear photo and re-submit."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-danger px-4">{{ __('Reject Application') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- BLOCK SELLER MODAL -->
<div class="modal fade" id="blockModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title text-white"><i class="fas fa-ban mr-2"></i> {{ __('Block Seller & Store') }}</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="blockForm" method="POST">
                @csrf
                <div class="modal-body p-4 text-center">
                    <i class="fas fa-store-slash fa-4x text-danger mb-3"></i>
                    <h5 class="font-weight-bold text-danger">{{ __('Are you sure you want to block this seller?') }}</h5>
                    <p class="font-weight-bold text-dark h6 mb-3" id="blockShopName"></p>
                    <div class="alert alert-warning text-left p-3 mb-0">
                        <h6 class="font-weight-bold mb-2"><i class="fas fa-exclamation-triangle mr-1"></i> {{ __('Blocking will immediately:') }}</h6>
                        <ul class="mb-0 pl-3 small">
                            <li>{{ __('Set seller account status to "Blocked".') }}</li>
                            <li>{{ __('Temporarily hide all of this seller\'s products from the public catalog.') }}</li>
                            <li>{{ __('Suspend access to the Seller Dashboard features.') }}</li>
                            <li>{{ __('Existing order history and transaction data will remain completely intact.') }}</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-danger px-4">{{ __('Yes, Block Seller') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- UNBLOCK SELLER MODAL -->
<div class="modal fade" id="unblockModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title text-white"><i class="fas fa-check-circle mr-2"></i> {{ __('Unblock Seller & Store') }}</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="unblockForm" method="POST">
                @csrf
                <div class="modal-body p-4 text-center">
                    <i class="fas fa-store fa-4x text-success mb-3"></i>
                    <h5 class="font-weight-bold text-success">{{ __('Are you sure you want to unblock this seller?') }}</h5>
                    <p class="font-weight-bold text-dark h6 mb-3" id="unblockShopName"></p>
                    <div class="alert alert-success text-left p-3 mb-0">
                        <h6 class="font-weight-bold mb-2"><i class="fas fa-info-circle mr-1"></i> {{ __('Unblocking will immediately:') }}</h6>
                        <ul class="mb-0 pl-3 small">
                            <li>{{ __('Set seller account status back to "Active".') }}</li>
                            <li>{{ __('Automatically restore all previously hidden products to the public store.') }}</li>
                            <li>{{ __('Reinstate full access to the Seller Dashboard.') }}</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-success px-4">{{ __('Yes, Unblock Seller') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- DELETE CONFIRMATION MODAL -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title text-white"><i class="fas fa-trash mr-2"></i> {{ __('Delete Store Request') }}</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body p-4 text-center">
                    <p class="text-dark">{{ __('Are you sure you want to delete this store request record? This action cannot be undone.') }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('Delete') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const imageBaseUrl = "{{ asset('core/public/storage/images/stores/') }}/";

    function viewDetailsModal(data) {
        $('#modalShopName').text(data.shop_name);
        $('#modalApplicantName').text(data.first_name + ' ' + data.last_name);
        $('#modalApplicantPhone').text(data.phone || 'N/A');
        $('#modalApplicantEmail').text(data.email || 'N/A');
        $('#modalApplicantCnic').text(data.cnic || 'N/A');
        $('#modalStoreNameText').text(data.shop_name);
        $('#modalProductTypes').text(data.product_types || 'Not Specified');
        $('#modalStoreAddress').text(data.shop_address || 'N/A');
        $('#modalDate').text(data.created_at ? new Date(data.created_at).toLocaleDateString() : 'N/A');

        // Request Status Badge
        let statusBadge = '';
        if (data.status === 'Pending') {
            statusBadge = '<span class="badge badge-warning text-dark"><i class="fas fa-clock mr-1"></i> Pending</span>';
        } else if (data.status === 'Approved') {
            statusBadge = '<span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i> Approved</span>';
        } else if (data.status === 'Rejected') {
            statusBadge = '<span class="badge badge-danger"><i class="fas fa-times-circle mr-1"></i> Rejected</span>';
        }
        $('#modalStatusBadge').html(statusBadge);

        // Seller Account Status (Active vs Blocked)
        const isBlocked = (data.seller_status === 'Blocked' || (data.user && data.user.is_seller_blocked == 1));
        if (data.status === 'Approved') {
            $('#modalSellerStatusRow').show();
            if (isBlocked) {
                $('#modalSellerStatusBadge').html('<span class="badge badge-danger"><i class="fas fa-ban mr-1"></i> Blocked</span>');
            } else {
                $('#modalSellerStatusBadge').html('<span class="badge badge-primary"><i class="fas fa-check-circle mr-1"></i> Active</span>');
            }
        } else {
            $('#modalSellerStatusRow').hide();
        }

        // ID Card Preview
        if (data.id_card_front) {
            $('#modalIdCardWrap').html('<a href="' + imageBaseUrl + data.id_card_front + '" target="_blank"><img src="' + imageBaseUrl + data.id_card_front + '" style="max-height:130px; max-width:100%; object-fit:contain;"></a>');
            $('#modalIdCardLink').html('<a href="' + imageBaseUrl + data.id_card_front + '" target="_blank" class="btn btn-outline-primary btn-xs mt-1"><i class="fas fa-external-link-alt"></i> Full View</a>');
        } else {
            $('#modalIdCardWrap').html('<span class="text-muted font-italic">No ID uploaded</span>');
            $('#modalIdCardLink').html('');
        }

        // Selfie Preview
        if (data.selfie_with_id) {
            $('#modalSelfieWrap').html('<a href="' + imageBaseUrl + data.selfie_with_id + '" target="_blank"><img src="' + imageBaseUrl + data.selfie_with_id + '" style="max-height:130px; max-width:100%; object-fit:contain;"></a>');
            $('#modalSelfieLink').html('<a href="' + imageBaseUrl + data.selfie_with_id + '" target="_blank" class="btn btn-outline-primary btn-xs mt-1"><i class="fas fa-external-link-alt"></i> Full View</a>');
        } else {
            $('#modalSelfieWrap').html('<span class="text-muted font-italic">No selfie uploaded</span>');
            $('#modalSelfieLink').html('');
        }

        // Store Documents Preview (Image or PDF)
        if (data.store_documents) {
            const isPdf = data.store_documents.toLowerCase().endsWith('.pdf');
            if (isPdf) {
                $('#modalStoreDocsWrap').html('<i class="fas fa-file-pdf fa-3x text-danger"></i><div class="small mt-1">' + data.store_documents + '</div>');
                $('#modalStoreDocsLink').html('<a href="' + imageBaseUrl + data.store_documents + '" target="_blank" class="btn btn-danger btn-xs mt-1"><i class="fas fa-file-download"></i> View / Download PDF</a>');
            } else {
                $('#modalStoreDocsWrap').html('<a href="' + imageBaseUrl + data.store_documents + '" target="_blank"><img src="' + imageBaseUrl + data.store_documents + '" style="max-height:130px; max-width:100%; object-fit:contain;"></a>');
                $('#modalStoreDocsLink').html('<a href="' + imageBaseUrl + data.store_documents + '" target="_blank" class="btn btn-outline-primary btn-xs mt-1"><i class="fas fa-external-link-alt"></i> Full View</a>');
            }
        } else {
            $('#modalStoreDocsWrap').html('<span class="text-muted font-italic">No documents</span>');
            $('#modalStoreDocsLink').html('');
        }

        // Sample Product 1 Preview
        $('#modalSampleProd1Name').text(data.sample_product_1_name || 'Product 1').attr('title', data.sample_product_1_name || '');
        if (data.sample_product_1_image) {
            $('#modalSampleProd1Wrap').html('<a href="' + imageBaseUrl + data.sample_product_1_image + '" target="_blank"><img src="' + imageBaseUrl + data.sample_product_1_image + '" style="max-height:130px; max-width:100%; object-fit:contain;"></a>');
            $('#modalSampleProd1Link').html('<a href="' + imageBaseUrl + data.sample_product_1_image + '" target="_blank" class="btn btn-outline-primary btn-xs mt-1"><i class="fas fa-external-link-alt"></i> Full View</a>');
        } else {
            $('#modalSampleProd1Wrap').html('<span class="text-muted font-italic">No photo uploaded</span>');
            $('#modalSampleProd1Link').html('');
        }

        // Sample Product 2 Preview
        $('#modalSampleProd2Name').text(data.sample_product_2_name || 'Product 2').attr('title', data.sample_product_2_name || '');
        if (data.sample_product_2_image) {
            $('#modalSampleProd2Wrap').html('<a href="' + imageBaseUrl + data.sample_product_2_image + '" target="_blank"><img src="' + imageBaseUrl + data.sample_product_2_image + '" style="max-height:130px; max-width:100%; object-fit:contain;"></a>');
            $('#modalSampleProd2Link').html('<a href="' + imageBaseUrl + data.sample_product_2_image + '" target="_blank" class="btn btn-outline-primary btn-xs mt-1"><i class="fas fa-external-link-alt"></i> Full View</a>');
        } else {
            $('#modalSampleProd2Wrap').html('<span class="text-muted font-italic">No photo uploaded</span>');
            $('#modalSampleProd2Link').html('');
        }

        // Sample Product 3 Preview
        $('#modalSampleProd3Name').text(data.sample_product_3_name || 'Product 3').attr('title', data.sample_product_3_name || '');
        if (data.sample_product_3_image) {
            $('#modalSampleProd3Wrap').html('<a href="' + imageBaseUrl + data.sample_product_3_image + '" target="_blank"><img src="' + imageBaseUrl + data.sample_product_3_image + '" style="max-height:130px; max-width:100%; object-fit:contain;"></a>');
            $('#modalSampleProd3Link').html('<a href="' + imageBaseUrl + data.sample_product_3_image + '" target="_blank" class="btn btn-outline-primary btn-xs mt-1"><i class="fas fa-external-link-alt"></i> Full View</a>');
        } else {
            $('#modalSampleProd3Wrap').html('<span class="text-muted font-italic">No photo uploaded</span>');
            $('#modalSampleProd3Link').html('');
        }

        // Payment Info
        if (data.is_free == 1) {
            $('#modalFeeType').html('<span class="badge badge-success">Free Store Opening</span>');
            $('#modalFeeAmount').text('0.00');
            $('#modalPaymentMethod').text('N/A (Free)');
            $('#modalSenderAccount').text('N/A');
            $('#modalTxnId').text('N/A');
            $('#modalReceiptWrap').html('<span class="text-muted font-italic">Free Store Opening (No Receipt)</span>');
            $('#modalReceiptLink').html('');
        } else {
            $('#modalFeeType').html('<span class="badge badge-primary">Paid Store Opening</span>');
            $('#modalFeeAmount').text(data.store_fee);
            $('#modalPaymentMethod').text(data.account_type || 'N/A');
            $('#modalSenderAccount').text((data.account_name || '') + ' (' + (data.account_number || '') + ')');
            $('#modalTxnId').text(data.transaction_id || 'N/A');

            if (data.payment_screenshot) {
                $('#modalReceiptWrap').html('<a href="' + imageBaseUrl + data.payment_screenshot + '" target="_blank"><img src="' + imageBaseUrl + data.payment_screenshot + '" style="max-height:120px; max-width:100%; object-fit:contain;"></a>');
                $('#modalReceiptLink').html('<a href="' + imageBaseUrl + data.payment_screenshot + '" target="_blank" class="btn btn-primary btn-xs"><i class="fas fa-external-link-alt"></i> Full Receipt</a>');
            } else {
                $('#modalReceiptWrap').html('<span class="text-danger font-italic">No receipt uploaded</span>');
                $('#modalReceiptLink').html('');
            }
        }

        // Rejection Reason
        if (data.status === 'Rejected' && data.reject_reason) {
            $('#modalRejectReasonText').text(data.reject_reason);
            $('#modalRejectReasonSection').removeClass('d-none');
        } else {
            $('#modalRejectReasonSection').addClass('d-none');
        }

        // Action Buttons inside Modal
        let actions = '';
        if (data.status === 'Approved') {
            // Toggle Block / Unblock Seller Buttons
            if (isBlocked) {
                const unblockUrl = "{{ url('admin/store-requests/unblock') }}/" + data.id;
                actions += '<button type="button" class="btn btn-success btn-sm mr-2" onclick="openUnblockModal(\'' + unblockUrl + '\', \'' + data.shop_name + '\')"><i class="fas fa-check-circle"></i> {{ __("Unblock Seller") }}</button>';
            } else {
                const blockUrl = "{{ url('admin/store-requests/block') }}/" + data.id;
                actions += '<button type="button" class="btn btn-danger btn-sm mr-2" onclick="openBlockModal(\'' + blockUrl + '\', \'' + data.shop_name + '\')"><i class="fas fa-ban"></i> {{ __("Block Seller") }}</button>';
            }
            if (data.status !== 'Rejected') {
                const rejectUrl = "{{ url('admin/store-requests/reject') }}/" + data.id;
                actions += '<button type="button" class="btn btn-warning btn-sm" onclick="openRejectModal(\'' + rejectUrl + '\', \'' + data.shop_name + '\')"><i class="fas fa-times"></i> {{ __("Reject Application") }}</button>';
            }
        } else {
            if (data.status !== 'Approved') {
                const approveUrl = "{{ url('admin/store-requests/approve') }}/" + data.id;
                actions += '<button type="button" class="btn btn-success btn-sm mr-2" onclick="confirmApprove(\'' + approveUrl + '\', \'' + data.shop_name + '\')"><i class="fas fa-check"></i> {{ __("Approve Application") }}</button>';
            }
            if (data.status !== 'Rejected') {
                const rejectUrl = "{{ url('admin/store-requests/reject') }}/" + data.id;
                actions += '<button type="button" class="btn btn-warning btn-sm" onclick="openRejectModal(\'' + rejectUrl + '\', \'' + data.shop_name + '\')"><i class="fas fa-times"></i> {{ __("Reject Application") }}</button>';
            }
        }
        $('#modalActionButtons').html(actions);

        // Reset and Load Direct Message Section
        activeStoreRequestId = data.id;
        $('#modalStoreMessageInput').val('');
        $('#modalMsgSuccessAlert').addClass('d-none').text('');
        loadStoreRequestMessages(data.id);

        $('#detailsModal').modal('show');
    }

    let activeStoreRequestId = null;

    function loadStoreRequestMessages(id) {
        const historyBox = $('#modalChatHistory');
        historyBox.html('<div class="text-center text-muted small py-2"><i class="fas fa-spinner fa-spin mr-1"></i> {{ __("Loading message history...") }}</div>').show();

        fetch("{{ url('admin/store-requests/messages') }}/" + id, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(res => {
            if (res.success && res.messages && res.messages.length > 0) {
                let html = '';
                res.messages.forEach(m => {
                    const isAdmin = m.is_admin;
                    html += `
                        <div class="p-2 mb-2 rounded ${isAdmin ? 'bg-white border text-right' : 'bg-info-light border text-left'}" style="background: ${isAdmin ? '#eff6ff' : '#f0fdf4'}; border-color: ${isAdmin ? '#bfdbfe' : '#bbf7d0'} !important;">
                            <div class="font-weight-bold" style="font-size: 11px; color: ${isAdmin ? '#1e40af' : '#15803d'};">
                                <i class="fas ${isAdmin ? 'fa-user-shield' : 'fa-store'} mr-1"></i>
                                ${isAdmin ? '{{ __("Admin") }}' : '{{ __("Store Applicant") }}'} • <span class="text-muted font-weight-normal">${m.time}</span>
                            </div>
                            <div class="text-dark mt-1" style="font-size: 12.5px;">${escapeHtml(m.message)}</div>
                        </div>
                    `;
                });
                historyBox.html(html).show();
                historyBox.scrollTop(historyBox[0].scrollHeight);
            } else {
                historyBox.html('<div class="text-center text-muted small py-2">{{ __("No messages exchanged yet. Send a direct message below.") }}</div>').show();
            }
        })
        .catch(e => {
            historyBox.html('<div class="text-center text-muted small py-2">{{ __("No previous messages.") }}</div>').show();
        });
    }

    function sendDirectStoreMessage() {
        if (!activeStoreRequestId) return;
        const input = $('#modalStoreMessageInput');
        const text = input.val().trim();
        if (!text) return;

        const btn = $('#modalSendStoreMsgBtn');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> {{ __("Sending...") }}');

        fetch("{{ url('admin/store-requests/send-message') }}/" + activeStoreRequestId, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ message: text })
        })
        .then(r => r.json())
        .then(res => {
            btn.prop('disabled', false).html('<i class="fas fa-paper-plane mr-1"></i> {{ __("Send Message") }}');
            if (res.success) {
                input.val('');
                $('#modalMsgSuccessAlert').removeClass('d-none').text(res.message);
                loadStoreRequestMessages(activeStoreRequestId);
            } else {
                alert(res.message || '{{ __("Failed to send message.") }}');
            }
        })
        .catch(err => {
            btn.prop('disabled', false).html('<i class="fas fa-paper-plane mr-1"></i> {{ __("Send Message") }}');
            alert('{{ __("Error sending message.") }}');
        });
    }

    function handleModalStoreMsgKey(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            sendDirectStoreMessage();
        }
    }

    function escapeHtml(t) {
        const m = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return t.replace(/[&<>"']/g, function(k) { return m[k]; });
    }

    function confirmApprove(url, shopName) {
        $('#detailsModal').modal('hide');
        $('#approveShopName').text(shopName);
        $('#approveForm').attr('action', url);
        $('#approveModal').modal('show');
    }

    function openRejectModal(url, shopName) {
        $('#detailsModal').modal('hide');
        $('#rejectShopName').text(shopName);
        $('#rejectForm').attr('action', url);
        $('#rejectModal').modal('show');
    }

    function openBlockModal(url, shopName) {
        $('#detailsModal').modal('hide');
        $('#blockShopName').text(shopName);
        $('#blockForm').attr('action', url);
        $('#blockModal').modal('show');
    }

    function openUnblockModal(url, shopName) {
        $('#detailsModal').modal('hide');
        $('#unblockShopName').text(shopName);
        $('#unblockForm').attr('action', url);
        $('#unblockModal').modal('show');
    }

    function confirmDelete(url) {
        $('#deleteForm').attr('action', url);
        $('#deleteModal').modal('show');
    }
</script>
@endsection
