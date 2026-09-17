@extends('master.seller')

@section('content')

<div class="container-fluid">

	<!-- Page Heading -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between">
                <h3 class="mb-0 text-dark"><b>{{ __('Create Brand') }}</b></h3>
                <a class="btn btn-primary btn-sm font-weight-bold" href="{{ route('seller.brand.index') }}">
                    <i class="fas fa-chevron-left mr-1"></i> {{ __('Back') }}
                </a>
            </div>
        </div>
    </div>

	<!-- Form -->
	<div class="row">
		<div class="col-xl-12 col-lg-12 col-md-12">
			<div class="card o-hidden border-0 shadow-lg">
				<div class="card-body">
					<div class="row justify-content-center">
						<div class="col-lg-12">
							<form class="admin-form" action="{{ route('seller.brand.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf

								@include('alerts.alerts')

								<div class="form-group">
									<label for="name">{{ __('Brand Logo / Image') }} *</label>
									<br>
									<img class="admin-img" src="{{ url('/core/public/storage/images/placeholder.png') }}" alt="Placeholder" style="max-height: 100px;">
									<br>
									<span class="mt-1 small text-muted">{{ __('Recommended Image Size: 110 x 81.') }}</span>
								</div>

								<div class="form-group position-relative">
									<label class="file">
										<input type="file" accept="image/*" class="upload-photo" name="photo" id="file" required>
										<span class="file-custom text-left">{{ __('Upload Image...') }}</span>
									</label>
                                </div>

								<div class="form-group">
									<label for="name">{{ __('Brand Name') }} *</label>
									<input type="text" name="name" class="form-control item-name" id="name" placeholder="{{ __('Enter Brand Name') }}" value="{{ old('name') }}" required>
								</div>

								<div class="form-group">
									<label for="slug">{{ __('Slug') }} *</label>
									<input type="text" name="slug" class="form-control" id="slug" placeholder="{{ __('Enter Slug') }}" value="{{ old('slug') }}" required>
								</div>

								<div class="form-group">
									<button type="submit" class="btn btn-primary font-weight-bold">
                                        <i class="fas fa-save mr-1"></i> {{ __('Save Brand') }}
                                    </button>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

</div>

@endsection
