@extends('master.seller')

@section('content')

<div class="container-fluid">

	<!-- Page Heading -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between">
                <h3 class="mb-0 text-dark"><b>{{ __('Edit Child Category') }}</b></h3>
                <a class="btn btn-primary btn-sm font-weight-bold" href="{{ route('seller.childcategory.index') }}">
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
							<form class="admin-form" action="{{ route('seller.childcategory.update', $childcategory->id) }}" method="POST">
                                @csrf
                                @method('PUT')

								@include('alerts.alerts')

								<div class="form-group">
									<label for="category_id">{{ __('Select Parent Category') }} *</label>
									<select name="category_id" id="category_id" data-href="{{ route('seller.get.subcategory') }}" class="form-control" required>
										<option value="" disabled>{{ __('Select One') }}</option>
										@foreach(DB::table('categories')->where('status', 1)->where(function($q) { $q->whereNull('vendor_id')->orWhere('vendor_id', 0)->orWhere('vendor_id', Auth::id()); })->orderBy('name', 'asc')->get() as $cat)
											<option value="{{ $cat->id }}" {{ $childcategory->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
										@endforeach
									</select>
								</div>

								<div class="form-group">
									<label for="subcategory_id">{{ __('Select Subcategory') }} *</label>
									<select name="subcategory_id" id="subcategory_id" class="form-control" required>
										<option value="" disabled>{{ __('Select One') }}</option>
										@foreach(DB::table('subcategories')->where('category_id', $childcategory->category_id)->get() as $subcat)
											<option value="{{ $subcat->id }}" {{ $childcategory->subcategory_id == $subcat->id ? 'selected' : '' }}>{{ $subcat->name }}</option>
										@endforeach
									</select>
								</div>

								<div class="form-group">
									<label for="name">{{ __('Childcategory Name') }} *</label>
									<input type="text" name="name" class="form-control item-name" id="name" placeholder="{{ __('Enter Childcategory Name') }}" value="{{ $childcategory->name }}" required>
								</div>

								<div class="form-group">
									<label for="slug">{{ __('Slug') }} *</label>
									<input type="text" name="slug" class="form-control" id="slug" placeholder="{{ __('Enter Slug') }}" value="{{ $childcategory->slug }}" required>
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

@section('scripts')
<script>
    $('#category_id').on('change', function () {
        var url = $(this).attr('data-href');
        var category_id = $(this).val();
        if (category_id) {
            $.get(url + '?category_id=' + category_id, function (data) {
                $('#subcategory_id').html(data);
            });
        }
    });
</script>
@endsection
