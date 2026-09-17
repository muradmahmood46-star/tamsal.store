@extends('master.seller')

@section('content')

<div class="container-fluid">

	<!-- Page Heading -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between">
                <h3 class="mb-0 text-dark"><b>{{ __('Edit Sub Category') }}</b></h3>
                <a class="btn btn-primary btn-sm font-weight-bold" href="{{ route('seller.subcategory.index') }}">
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
							<form class="admin-form" action="{{ route('seller.subcategory.update', $subcategory->id) }}" method="POST">
                                @csrf
                                @method('PUT')

								@include('alerts.alerts')

								<div class="form-group">
									<label for="category_id">{{ __('Select Parent Category') }} *</label>
									<select name="category_id" id="category_id" class="form-control" required>
										<option value="" disabled>{{ __('Select One') }}</option>
										@foreach(DB::table('categories')->whereStatus(1)->get() as $cat)
											<option value="{{ $cat->id }}" {{ $subcategory->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
										@endforeach
									</select>
								</div>

								<div class="form-group">
									<label for="name">{{ __('Subcategory Name') }} *</label>
									<input type="text" name="name" class="form-control item-name" id="name" placeholder="{{ __('Enter Subcategory Name') }}" value="{{ $subcategory->name }}" required>
								</div>

								<div class="form-group">
									<label for="slug">{{ __('Slug') }} *</label>
									<input type="text" name="slug" class="form-control" id="slug" placeholder="{{ __('Enter Slug') }}" value="{{ $subcategory->slug }}" required>
								</div>

								<div class="form-group">
									<button type="submit" class="btn btn-primary font-weight-bold px-4">{{ __('Save Changes') }}</button>
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
