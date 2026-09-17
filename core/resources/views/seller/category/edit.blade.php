@extends('master.seller')

@section('content')

<div class="container-fluid">

	<!-- Page Heading -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between">
                <h3 class="mb-0 text-dark"><b>{{ __('Edit Category') }}</b></h3>
                <a class="btn btn-primary btn-sm font-weight-bold" href="{{ route('seller.category.index') }}">
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
							<form class="admin-form" action="{{ route('seller.category.update', $category->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

								@include('alerts.alerts')

								<div class="form-group">
									<label for="name">{{ __('Current Image') }}</label>
                                    <br>
									<img class="admin-img" id="category_preview" src="{{ $category->photo ? url('/core/public/storage/images/'.$category->photo) : url('/core/public/storage/images/placeholder.png') }}" alt="No Image Found" style="width: 80px; height: 80px; object-fit: cover; border-radius: 6px; border: 1px solid #ddd;">
                                    <br>
									<span class="mt-1 small text-muted">{{ __('Recommended Image Size: 60 x 60 or Square image.') }}</span>
								</div>

								<div class="form-group position-relative">
									<label class="file">
										<input type="file" accept="image/*" class="upload-photo custom-file-input" name="photo" id="file" onchange="previewImg(this, 'category_preview')">
										<span class="file-custom text-left">{{ __('Upload Image...') }}</span>
									</label>
                                </div>

								<div class="form-group">
									<label for="name">{{ __('Category Name') }} *</label>
									<input type="text" name="name" class="form-control item-name" id="name" placeholder="{{ __('Enter Category Name') }}" value="{{ $category->name }}" required>
								</div>

								<div class="form-group">
									<label for="slug">{{ __('Slug') }} *</label>
									<input type="text" name="slug" class="form-control" id="slug" placeholder="{{ __('Enter Slug') }}" value="{{ $category->slug }}" required>
								</div>

								<div class="form-group">
									<label for="meta_keywords">{{ __('Meta Keywords') }}</label>
									<input type="text" name="meta_keywords" class="tags" id="meta_keywords" placeholder="{{ __('Enter Meta Keywords') }}" value="{{ $category->meta_keywords }}">
								</div>

								<div class="form-group">
									<label for="meta_description">{{ __('Meta Description') }}</label>
									<textarea name="meta_descriptions" id="meta_description" class="form-control" rows="4" placeholder="{{ __('Enter Meta Description') }}">{{ $category->meta_descriptions }}</textarea>
								</div>

								<div class="form-group">
									<label for="serial">{{ __('Serial / Sort Order') }}</label>
									<input type="number" name="serial" class="form-control" id="serial" placeholder="{{ __('Enter Serial Number') }}" value="{{ $category->serial }}">
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
    function previewImg(input, targetId) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#' + targetId).attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
