@extends('master.back')

@section('content')

<!-- Start of Main Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between">
                <h3 class="mb-0"><b>{{ __('Delivery Fees Calculation') }}</b></h3>
            </div>
        </div>
    </div>

    <!-- Delivery Fees Calculation Form -->
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Configure Product Quantity Delivery Fee Divisors') }}</h6>
                </div>
                <div class="card-body">
                    @include('alerts.alerts')

                    @php
                        $isEnabled = isset($setting->is_delivery_calc_enabled) ? (int)$setting->is_delivery_calc_enabled : 1;
                        $secondDivisor = isset($setting->delivery_second_item_divisor) && (float)$setting->delivery_second_item_divisor > 0
                            ? (float)$setting->delivery_second_item_divisor
                            : 2;
                        $subsequentDivisor = isset($setting->delivery_subsequent_item_divisor) && (float)$setting->delivery_subsequent_item_divisor > 0
                            ? (float)$setting->delivery_subsequent_item_divisor
                            : 3;
                    @endphp

                    <form class="admin-form" action="{{ route('back.delivery_calculation.update') }}" method="POST">
                        @csrf

                        <div class="form-group mb-4">
                            <input type="hidden" name="is_delivery_calc_enabled" value="0">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="is_delivery_calc_enabled" class="custom-control-input" id="is_delivery_calc_enabled" value="1" {{ old('is_delivery_calc_enabled', $isEnabled) == 1 ? 'checked' : '' }}>
                                <label class="custom-control-label font-weight-bold text-dark" for="is_delivery_calc_enabled" style="font-size: 15px; cursor: pointer;">
                                    {{ __('Enable Delivery Fee Calculation') }}
                                </label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label for="delivery_second_item_divisor" class="font-weight-bold text-dark" style="font-size: 15px;">
                                        {{ __('Second Item Divisor') }} *
                                    </label>
                                    <input type="number" id="delivery_second_item_divisor" name="delivery_second_item_divisor" class="form-control form-control-lg" placeholder="2" min="0.01" step="any" value="{{ old('delivery_second_item_divisor', $secondDivisor) }}" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label for="delivery_subsequent_item_divisor" class="font-weight-bold text-dark" style="font-size: 15px;">
                                        {{ __('Third and Subsequent Items Divisor') }} *
                                    </label>
                                    <input type="number" id="delivery_subsequent_item_divisor" name="delivery_subsequent_item_divisor" class="form-control form-control-lg" placeholder="3" min="0.01" step="any" value="{{ old('delivery_subsequent_item_divisor', $subsequentDivisor) }}" required>
                                </div>
                            </div>
                        </div>

                        <!-- Live Calculation Example Preview -->
                        <div class="card bg-light border mb-4">
                            <div class="card-body">
                                <h6 class="font-weight-bold text-dark mb-2">
                                    <i class="fas fa-calculator text-primary mr-1"></i> {{ __('Example Preview (Original Product Delivery Fee = Rs. 200)') }}
                                </h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered bg-white mb-0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>{{ __('Quantity of the same product') }}</th>
                                                <th>{{ __('Calculation') }}</th>
                                                <th class="text-right">{{ __('Total delivery fee') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody id="delivery_calc_preview_body">
                                            <!-- Populated dynamically by script below -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary px-4 py-2 font-weight-bold">
                                <i class="fas fa-save mr-1"></i> {{ __('Save Settings') }}
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

</div>
<!-- End of Main Content -->

<script>
    (function() {
        function updatePreviewTable() {
            var enabledEl = document.getElementById('is_delivery_calc_enabled');
            var d2El = document.getElementById('delivery_second_item_divisor');
            var d3El = document.getElementById('delivery_subsequent_item_divisor');
            var tbody = document.getElementById('delivery_calc_preview_body');
            if (!tbody) return;

            var isEnabled = enabledEl ? enabledEl.checked : true;
            var d2 = parseFloat(d2El ? d2El.value : 2);
            var d3 = parseFloat(d3El ? d3El.value : 3);
            if (isNaN(d2) || d2 <= 0) d2 = 2;
            if (isNaN(d3) || d3 <= 0) d3 = 3;

            var B = 200;
            var rowsHtml = '';
            for (var n = 1; n <= 5; n++) {
                var calcText = '';
                var total = B;
                if (!isEnabled) {
                    calcText = B + ' (Calculation Disabled)';
                    total = B;
                } else if (n === 1) {
                    calcText = '' + B;
                    total = B;
                } else if (n === 2) {
                    calcText = B + ' + ' + B + '/' + d2;
                    total = B + (B / d2);
                } else {
                    var parts = [B + '', B + '/' + d2];
                    for (var k = 3; k <= n; k++) {
                        parts.push(B + '/' + d3);
                    }
                    calcText = parts.join(' + ');
                    total = B + (B / d2) + (((n - 2) * B) / d3);
                }

                var rounded = (Math.round((total + Number.EPSILON) * 100) / 100).toFixed(2);
                rowsHtml += '<tr>' +
                    '<td>' + n + '</td>' +
                    '<td><code>' + calcText + '</code></td>' +
                    '<td class="text-right font-weight-bold">Rs. ' + rounded + '</td>' +
                    '</tr>';
            }
            tbody.innerHTML = rowsHtml;
        }

        document.addEventListener('DOMContentLoaded', function() {
            updatePreviewTable();
            var inputs = ['is_delivery_calc_enabled', 'delivery_second_item_divisor', 'delivery_subsequent_item_divisor'];
            inputs.forEach(function(id) {
                var el = document.getElementById(id);
                if (el) {
                    el.addEventListener('input', updatePreviewTable);
                    el.addEventListener('change', updatePreviewTable);
                }
            });
        });
    })();
</script>

@endsection
