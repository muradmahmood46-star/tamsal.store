@extends('master.seller')

@section('content')

<div class="container-fluid">

    <!-- Page Heading & Breadcrumbs -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between">
                <div>
                    <h3 class="mb-0 font-weight-bold text-dark"><i class="fas fa-calendar-check text-info mr-2"></i>{{ __('Current Plan') }}</h3>
                    <p class="text-muted mb-0" style="font-size: 13.5px;">{{ __('Time-Based Subscription Overview & Extension') }}</p>
                </div>
                <ol class="breadcrumb mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="{{ route('seller.dashboard') }}">{{ __('Dashboard') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('Subscription Plans') }}</li>
                </ol>
            </div>
        </div>
    </div>

    @include('alerts.alerts')

    <!-- 1. Top 5 Overview Metric Cards -->
    <div class="row mb-4">

        <!-- Card 1: Plan Status -->
        <div class="col-12 col-sm-6 col-md-4 col-xl mb-3">
            <div class="card {{ $isPlanExpired ? 'border-left-danger' : 'border-left-primary' }} shadow h-100" style="border-left: 5px solid {{ $isPlanExpired ? '#dc3545' : '#4e73df' }} !important; border-radius: 10px;">
                <div class="card-body p-3 d-flex flex-column justify-content-between" style="min-height: 125px;">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="text-xs font-weight-bold {{ $isPlanExpired ? 'text-danger' : 'text-primary' }} text-uppercase">
                            {{ __('Plan Status') }}
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center {{ $isPlanExpired ? 'bg-danger' : 'bg-primary' }} text-white shadow-sm" style="width: 40px; height: 40px; font-size: 16px; min-width: 40px;">
                            <i class="fas {{ $isPlanExpired ? 'fa-exclamation-triangle' : 'fa-crown' }}"></i>
                        </div>
                    </div>
                    <div>
                        <div class="h5 mb-1 font-weight-bold text-gray-800">
                            @if($isPlanExpired)
                                <span class="badge badge-danger px-2 py-1" style="font-size: 12.5px;">{{ __('Plan Expired') }}</span>
                            @elseif($seller->plan_status === 'active_plan')
                                <span class="badge badge-primary px-2 py-1" style="font-size: 12.5px;">{{ __('Active Plan') }}</span>
                            @elseif(in_array($seller->plan_status, ['free_time', 'free_period']))
                                <span class="badge badge-success px-2 py-1" style="font-size: 12.5px;">{{ __('Free Time') }}</span>
                            @else
                                <span class="badge badge-primary px-2 py-1" style="font-size: 12.5px;">{{ __('Active Plan') }}</span>
                            @endif
                        </div>
                        <small class="text-muted d-block" style="font-size: 11.5px; line-height: 1.2;">
                            {{ __('Time-Based Store Subscription') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Days Left -->
        <div class="col-12 col-sm-6 col-md-4 col-xl mb-3">
            <div class="card border-left-info shadow h-100" style="border-left: 5px solid #36b9cc !important; border-radius: 10px;">
                <div class="card-body p-3 d-flex flex-column justify-content-between" style="min-height: 125px;">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase">
                            {{ __('Days Left') }}
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-info text-white shadow-sm" style="width: 40px; height: 40px; font-size: 16px; min-width: 40px;">
                            <i class="fas fa-hourglass-half"></i>
                        </div>
                    </div>
                    <div>
                        <div class="h5 mb-1 font-weight-bold {{ $isPlanExpired ? 'text-danger' : 'text-gray-800' }}">
                            {{ $daysRemaining }} {{ __('Day(s)') }}
                        </div>
                        <small class="text-muted d-block" style="font-size: 11.5px; line-height: 1.2;">
                            {{ __('Remaining validity') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3: Valid Until -->
        <div class="col-12 col-sm-6 col-md-4 col-xl mb-3">
            <div class="card border-left-success shadow h-100" style="border-left: 5px solid #1cc88a !important; border-radius: 10px;">
                <div class="card-body p-3 d-flex flex-column justify-content-between" style="min-height: 125px;">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase">
                            {{ __('Valid Until') }}
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-success text-white shadow-sm" style="width: 40px; height: 40px; font-size: 16px; min-width: 40px;">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                    </div>
                    <div>
                        <div class="h5 mb-1 font-weight-bold text-gray-800">
                            {{ $planEndDate ? $planEndDate->format('d M Y') : 'N/A' }}
                        </div>
                        <small class="text-muted d-block" style="font-size: 11.5px; line-height: 1.2;">
                            {{ __('Plan expiration date') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 4: Next Renewal Charge -->
        <div class="col-12 col-sm-6 col-md-4 col-xl mb-3">
            <div class="card border-left-warning shadow h-100" style="border-left: 5px solid #f6c23e !important; border-radius: 10px;">
                <div class="card-body p-3 d-flex flex-column justify-content-between" style="min-height: 125px;">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase">
                            {{ __('Next Renewal Charge') }}
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-warning text-white shadow-sm" style="width: 40px; height: 40px; font-size: 16px; min-width: 40px;">
                            <i class="fas fa-coins"></i>
                        </div>
                    </div>
                    <div>
                        <div class="h5 mb-1 font-weight-bold text-gray-800">
                            {{ PriceHelper::adminCurrency() }} {{ number_format($planCharge, 2) }}
                        </div>
                        <small class="text-muted d-block" style="font-size: 11.5px; line-height: 1.2;">
                            {{ __('0% sales commission') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 5: Renewal Status -->
        <div class="col-12 col-sm-6 col-md-4 col-xl mb-3">
            <div class="card {{ $isSufficient ? 'border-left-success' : 'border-left-danger' }} shadow h-100" style="border-left: 5px solid {{ $isSufficient ? '#28a745' : '#dc3545' }} !important; border-radius: 10px;">
                <div class="card-body p-3 d-flex flex-column justify-content-between" style="min-height: 125px;">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="text-xs font-weight-bold {{ $isSufficient ? 'text-success' : 'text-danger' }} text-uppercase">
                            {{ __('Renewal Status') }}
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center {{ $isSufficient ? 'bg-success' : 'bg-danger' }} text-white shadow-sm" style="width: 40px; height: 40px; font-size: 16px; min-width: 40px;">
                            <i class="fas {{ $isSufficient ? 'fa-sync-alt' : 'fa-wallet' }}"></i>
                        </div>
                    </div>
                    <div>
                        <div class="h5 mb-1 font-weight-bold">
                            @if($isSufficient)
                                <span class="text-success"><i class="fas fa-check-circle mr-1"></i>{{ __('Sufficient') }}</span>
                            @else
                                <span class="text-danger"><i class="fas fa-exclamation-triangle mr-1"></i>{{ __('Low Balance') }}</span>
                            @endif
                        </div>
                        <small class="text-muted d-block" style="font-size: 11.5px; line-height: 1.2;">
                            @if($isSufficient)
                                {{ __('Auto renew ready') }}
                            @else
                                {{ __('Deposit required') }}
                            @endif
                        </small>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- 2. Informative Notice (English Explaining Early Renewal & Validity Stacking) -->
    <div class="alert alert-info border-left-info shadow-sm py-3 mb-4" style="border-left: 5px solid #36b9cc; background-color: #f0fdf4; border-radius: 8px;">
        <div class="d-flex align-items-start">
            <div class="mr-3 mt-1">
                <i class="fas fa-info-circle text-info" style="font-size: 26px;"></i>
            </div>
            <div class="flex-grow-1">
                <h5 class="font-weight-bold text-dark mb-1" style="font-size: 15px;">
                    {{ __('Early Plan Renewal') }}
                </h5>
                <p class="mb-0 text-muted" style="font-size: 13.5px; line-height: 1.5;">
                    {{ __('You can renew your plan anytime before it expires. Any remaining days will automatically be added to your new plan, so you never lose any days.') }}
                </p>
            </div>
        </div>
    </div>

    <!-- 3. Plan Extension Action Box -->
    <div class="card shadow mb-4" style="border-radius: 10px;">
        <div class="card-header py-3 bg-white d-flex align-items-center justify-content-between" style="border-bottom: 1px solid #e3e6f0;">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-bolt text-warning mr-1"></i> {{ __('Extend / Renew Store Subscription Plan') }}
            </h6>
            <span class="badge {{ $isSufficient ? 'badge-success' : 'badge-danger' }} px-3 py-2" style="font-size: 12.5px;">
                {{ __('Wallet Balance') }}: {{ PriceHelper::adminCurrency() }} {{ number_format($seller->balance, 2) }}
            </span>
        </div>
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-lg-8 mb-3 mb-lg-0">
                    <div class="row">
                        <div class="col-sm-4 mb-2">
                            <span class="text-muted d-block small">{{ __('Extension Duration') }}:</span>
                            <strong class="text-dark h5 mb-0">+{{ $planDuration }} {{ __('Days') }}</strong>
                        </div>
                        <div class="col-sm-4 mb-2">
                            <span class="text-muted d-block small">{{ __('Plan Renewal Charge') }}:</span>
                            <strong class="text-primary h5 mb-0">{{ PriceHelper::adminCurrency() }} {{ number_format($planCharge, 2) }}</strong>
                        </div>
                        <div class="col-sm-4 mb-2">
                            <span class="text-muted d-block small">{{ __('New Expiry After Extension') }}:</span>
                            <strong class="text-success h5 mb-0">{{ $projectedEndDate->format('d M Y') }}</strong>
                            <small class="text-muted d-block font-weight-bold">({{ $projectedDays }} {{ __('Days Total Validity') }})</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 text-lg-right">
                    @if($isSufficient)
                        <button type="button" class="btn btn-primary btn-lg shadow-sm font-weight-bold px-4" data-toggle="modal" data-target="#renewPlanModal">
                            <i class="fas fa-bolt mr-2"></i> {{ __('Extend / Renew Plan (+:days Days)', ['days' => $planDuration]) }}
                        </button>
                    @else
                        <div class="d-inline-block text-left text-lg-right">
                            <div class="text-danger small font-weight-bold mb-2">
                                <i class="fas fa-exclamation-circle mr-1"></i> {{ __('Add :curr :amount more to extend', ['curr' => PriceHelper::adminCurrency(), 'amount' => number_format($planCharge - (float)$seller->balance, 2)]) }}
                            </div>
                            <a href="{{ route('seller.wallet.index') }}" class="btn btn-warning btn-lg shadow-sm font-weight-bold px-4">
                                <i class="fas fa-wallet mr-2"></i> {{ __('Deposit First') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Plan Confirmation Modal -->
    @if($isSufficient)
    <div class="modal fade" id="renewPlanModal" tabindex="-1" role="dialog" aria-labelledby="renewPlanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content shadow-lg border-0" style="border-radius: 12px;">
                <form action="{{ route('seller.plan.renew') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-primary text-white" style="border-top-left-radius: 12px; border-top-right-radius: 12px;">
                        <h5 class="modal-title font-weight-bold" id="renewPlanModalLabel">
                            <i class="fas fa-bolt mr-1 text-warning"></i> {{ __('Confirm Plan Extension') }}
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="text-center mb-3">
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center bg-primary text-white mb-2" style="width: 60px; height: 60px; font-size: 26px;">
                                <i class="fas fa-calendar-plus"></i>
                            </div>
                            <h5 class="font-weight-bold text-dark">{{ __('Extend Store Subscription') }}</h5>
                            <p class="text-muted small mb-0">{{ __('Your existing remaining days will be merged with the new :days days duration.', ['days' => $planDuration]) }}</p>
                        </div>

                        <div class="p-3 bg-light rounded border mb-3">
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted">{{ __('Current Wallet Balance') }}:</span>
                                <strong class="text-dark">{{ PriceHelper::adminCurrency() }} {{ number_format($seller->balance, 2) }}</strong>
                            </div>
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted">{{ __('Plan Renewal Charge') }}:</span>
                                <strong class="text-danger">-{{ PriceHelper::adminCurrency() }} {{ number_format($planCharge, 2) }}</strong>
                            </div>
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted">{{ __('Balance After Deduction') }}:</span>
                                <strong class="text-success">{{ PriceHelper::adminCurrency() }} {{ number_format($seller->balance - $planCharge, 2) }}</strong>
                            </div>
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted">{{ __('Duration Added') }}:</span>
                                <strong class="text-primary">+{{ $planDuration }} {{ __('Days') }}</strong>
                            </div>
                            <div class="d-flex justify-content-between py-1">
                                <span class="text-muted">{{ __('New Valid Until Date') }}:</span>
                                <strong class="text-success">{{ $projectedEndDate->format('d M Y') }}</strong>
                            </div>
                        </div>

                        <div class="alert alert-success py-2 mb-0" style="font-size: 13px;">
                            <i class="fas fa-shield-alt mr-1"></i> {{ __('0% sales commission on all orders throughout this period.') }}
                        </div>
                    </div>
                    <div class="modal-footer bg-white border-0 px-4 pb-4 pt-0">
                        <button type="button" class="btn btn-secondary px-4 font-weight-bold" data-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary px-4 font-weight-bold">
                            <i class="fas fa-check-circle mr-1"></i> {{ __('Confirm & Deduct :curr :amount', ['curr' => PriceHelper::adminCurrency(), 'amount' => number_format($planCharge, 2)]) }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- 5. My Plan Activation History -->
    <div class="card shadow mb-4" style="border-radius: 10px;">
        <div class="card-header py-3 bg-white d-flex align-items-center justify-content-between" style="border-bottom: 1px solid #e3e6f0;">
            <h6 class="m-0 font-weight-bold text-dark">
                <i class="fas fa-history text-info mr-1"></i> {{ __('My Plan Activation History') }}
            </h6>
            <span class="badge badge-info px-2 py-1" style="font-size: 12px;">
                {{ __('Total Records') }}: {{ $histories->count() }}
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-items-center mb-0" id="planHistoryTable">
                    <thead class="thead-light">
                        <tr>
                            <th class="border-top-0">#</th>
                            <th class="border-top-0">{{ __('Buy / Activation Date') }}</th>
                            <th class="border-top-0">{{ __('Plan Type / Source') }}</th>
                            <th class="border-top-0 text-center">{{ __('Duration Added') }}</th>
                            <th class="border-top-0">{{ __('Validity Period') }}</th>
                            <th class="border-top-0 text-right">{{ __('Amount Charged') }}</th>
                            <th class="border-top-0 text-right">{{ __('Balance After') }}</th>
                            <th class="border-top-0 text-center">{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody id="planHistoryBody">
                        @forelse($histories as $index => $history)
                            <tr class="plan-history-row" data-index="{{ $index }}" style="{{ $index >= 5 ? 'display: none;' : '' }}">
                                <td class="font-weight-bold text-muted">{{ $index + 1 }}</td>
                                <td>
                                    <strong class="text-dark">{{ $history->created_at ? $history->created_at->format('d M Y') : 'N/A' }}</strong>
                                    <small class="text-muted d-block">{{ $history->created_at ? $history->created_at->format('h:i A') : '' }}</small>
                                </td>
                                <td>
                                    @if($history->plan_type === 'manual_extension')
                                        <span class="badge badge-primary px-2 py-1"><i class="fas fa-bolt mr-1"></i>{{ __('Early Extension') }}</span>
                                    @elseif($history->plan_type === 'auto_renewal' || $history->plan_type === 'plan_renewal')
                                        <span class="badge badge-success px-2 py-1"><i class="fas fa-sync-alt mr-1"></i>{{ __('Auto Renewal') }}</span>
                                    @elseif($history->plan_type === 'free_time')
                                        <span class="badge badge-info px-2 py-1"><i class="fas fa-gift mr-1"></i>{{ __('Initial Free Time') }}</span>
                                    @elseif($history->plan_type === 'deposit_reactivation')
                                        <span class="badge badge-warning px-2 py-1"><i class="fas fa-check-double mr-1"></i>{{ __('Deposit Approval') }}</span>
                                    @else
                                        <span class="badge badge-secondary px-2 py-1">{{ ucfirst(str_replace('_', ' ', $history->plan_type)) }}</span>
                                    @endif
                                    <small class="text-muted d-block mt-1">{{ $history->details }}</small>
                                </td>
                                <td class="text-center font-weight-bold text-dark">
                                    +{{ $history->days_added }} {{ __('Days') }}
                                </td>
                                <td>
                                    <div style="font-size: 13px;">
                                        <span class="text-muted">{{ __('From') }}:</span> <strong class="text-dark">{{ $history->start_date ? $history->start_date->format('d M Y') : '-' }}</strong>
                                    </div>
                                    <div style="font-size: 13px;">
                                        <span class="text-muted">{{ __('To') }}:</span> <strong class="text-success">{{ $history->end_date ? $history->end_date->format('d M Y') : '-' }}</strong>
                                    </div>
                                </td>
                                <td class="text-right font-weight-bold">
                                    @if((float)$history->charge > 0)
                                        <span class="text-danger">-{{ PriceHelper::adminCurrency() }} {{ number_format($history->charge, 2) }}</span>
                                    @else
                                        <span class="badge badge-success px-2 py-1">{{ __('Free') }}</span>
                                    @endif
                                </td>
                                <td class="text-right font-weight-bold text-dark">
                                    {{ $history->balance_after !== null ? PriceHelper::adminCurrency() . ' ' . number_format($history->balance_after, 2) : '-' }}
                                </td>
                                <td class="text-center">
                                    @if($history->status === 'active' || ($history->end_date && $history->end_date->isFuture()))
                                        <span class="badge badge-success px-2 py-1">{{ __('Active / Valid') }}</span>
                                    @elseif($history->status === 'completed')
                                        <span class="badge badge-secondary px-2 py-1">{{ __('Completed') }}</span>
                                    @else
                                        <span class="badge badge-light px-2 py-1">{{ ucfirst($history->status) }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    <i class="fas fa-folder-open mb-2 d-block" style="font-size: 28px;"></i>
                                    {{ __('No plan activation history found.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- View More Button & Count Indicator -->
            @if($histories->count() > 5)
                <div class="card-footer bg-white border-top text-center py-3">
                    <button type="button" id="btnViewMoreHistory" class="btn btn-outline-primary px-4 py-2 font-weight-bold shadow-sm">
                        <i class="fas fa-chevron-down mr-1"></i> {{ __('View More (+5 records)') }}
                    </button>
                    <div id="historyCountText" class="text-muted small mt-2">
                        {{ __('Showing') }} <span id="historyVisibleCount" class="font-weight-bold text-dark">5</span> {{ __('of') }} {{ $histories->count() }} {{ __('records') }}
                    </div>
                </div>
            @endif
        </div>
    </div>

</div>

<!-- Client-side View More Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var btn = document.getElementById('btnViewMoreHistory');
        if (!btn) return;

        var allRows = document.querySelectorAll('.plan-history-row');
        var totalRows = allRows.length;
        var currentlyShown = 5;

        btn.addEventListener('click', function() {
            var nextLimit = currentlyShown + 5;
            for (var i = currentlyShown; i < nextLimit && i < totalRows; i++) {
                if (allRows[i]) {
                    allRows[i].style.display = '';
                }
            }
            currentlyShown = Math.min(nextLimit, totalRows);

            var counter = document.getElementById('historyVisibleCount');
            if (counter) {
                counter.textContent = currentlyShown;
            }

            if (currentlyShown >= totalRows) {
                btn.style.display = 'none';
                var countText = document.getElementById('historyCountText');
                if (countText) {
                    countText.innerHTML = '<span class="text-success font-weight-bold"><i class="fas fa-check mr-1"></i> ' + '{{ __("All records displayed") }}' + ' (' + totalRows + ')</span>';
                }
            }
        });
    });
</script>

@endsection
