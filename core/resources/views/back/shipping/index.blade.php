@extends('master.back')

@section('content')

<!-- Start of Main Content -->
<div class="container-fluid">

	<!-- Free Delivery Heading -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between">
                <h3 class="mb-0"><b>{{ __('Free Delivery Offer') }}</b></h3>
            </div>
        </div>
    </div>

	<!-- Free Delivery Offer Form -->
	<div class="row">
		<div class="col-xl-12 col-lg-12 col-md-12">
			<div class="card shadow mb-4">
				<div class="card-header py-3">
					<h6 class="m-0 font-weight-bold text-primary">{{ __('Configure Minimum Order for Free Delivery') }}</h6>
				</div>
				<div class="card-body">
					@include('alerts.alerts')

					<form class="admin-form" action="{{ route('back.shipping.update', $shipping->id) }}" method="POST">
						@csrf
						@method('PUT')

						<div class="form-group mb-4">
							<div class="custom-control custom-switch">
								<input type="checkbox" name="is_condition" class="custom-control-input" id="free_delivery_toggle" value="1" {{ ($shipping->is_condition == 1 || $shipping->status == 1) ? 'checked' : '' }}>
								<label class="custom-control-label font-weight-bold text-dark" for="free_delivery_toggle" style="font-size: 15px; cursor: pointer;">
									{{ __('Enable Free Delivery Offer') }}
								</label>
							</div>
							<small class="form-text text-muted">
								{{ __('Turn ON this switch to offer free delivery to customers when their order reaches the minimum amount.') }}
							</small>
						</div>

						<div class="form-group">
							<label for="minimum_price" class="font-weight-bold text-dark" style="font-size: 15px;">
								{{ __('Offer free delivery to customer on orders above price (amount)') }} *
							</label>
							<div class="input-group mb-2" style="max-width: 450px;">
								<div class="input-group-prepend">
									<span class="input-group-text font-weight-bold bg-light text-primary">
										{{ PriceHelper::adminCurrency() }}
									</span>
								</div>
								<input type="number" id="minimum_price" name="minimum_price" class="form-control form-control-lg" placeholder="{{ __('Enter amount e.g. 2000, 3000, 5000') }}" min="0" step="1" value="{{ round($shipping->minimum_price * ($curr ? $curr->value : 1), 0) }}">
							</div>
							<small class="form-text text-muted">
								{{ __('Enter the minimum order subtotal required to get 100% free delivery.') }}
							</small>
						</div>

						<div class="alert alert-info border-left-info py-3 my-4" style="border-left: 4px solid #36b9cc; background-color: #f8fafc;">
							<div class="d-flex align-items-center">
								<i class="fas fa-info-circle text-info mr-3" style="font-size: 24px;"></i>
								<div>
									<strong class="text-dark">{{ __('How this works:') }}</strong>
									<p class="mb-0 text-muted" style="font-size: 13.5px; line-height: 1.5;">
										{{ __('When a customer adds items to cart and their order total is equal to or higher than this amount, delivery charges will automatically become Free (PKR 0.00), and a Free Delivery notification will be shown on the checkout page.') }}
									</p>
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

@endsection
