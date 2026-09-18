@extends('master.back')

@section('content')

<div class="container-fluid">

	<!-- Page Heading -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between">
                <h3 class=" mb-0"><b>{{ __('Update Slider') }}</b> </h3>
                <a class="btn btn-primary btn-sm" href="{{route('back.slider.index')}}"><i class="fas fa-chevron-left"></i> {{ __('Back') }}</a>
                </div>
        </div>
    </div>

	<!-- Form -->
	<div class="row">

		<div class="col-xl-12 col-lg-12 col-md-12">

			<div class="card o-hidden border-0 shadow-lg">
				<div class="card-body ">
					<!-- Nested Row within Card Body -->
					<div class="row justify-content-center">
						<div class="col-lg-12">
								<form class="admin-form" action="{{ route('back.slider.update',$slider->id) }}"
									method="POST" enctype="multipart/form-data">

                                    @csrf

                                    @method('PUT')

									@include('alerts.alerts')

									<input type="hidden" name="home_page" value="{{$slider->home_page}}">

									@if ($slider->home_page != 'theme4')
									<div class="form-group">
										<label id="change_label" for="name">{{ $slider->home_page == 'theme3' || $slider->home_page == 'theme4' ? __('Feature Image') : __('Brand Logo') }} <small class="text-muted">({{ __('Optional - Slider can be without logo') }})</small></label>
										<br>
											@if ($slider->logo)
												<div class="d-flex align-items-center mb-2" style="gap: 15px; flex-wrap: wrap;">
													<div class="border rounded p-2 bg-light d-inline-block shadow-sm">
														<img class="admin-img mb-0" style="max-height: 50px; max-width: 140px; object-fit: contain;"
															src="{{ url('/core/public/storage/images/'.$slider->logo) }}"
															alt="Slider Logo">
													</div>
													<a href="{{ route('back.slider.delete.logo', $slider->id) }}" 
													   onclick="return confirm('{{ __('Are you sure you want to delete this logo from the slider?') }}')" 
													   class="btn btn-danger btn-sm shadow-sm">
														<i class="fas fa-trash-alt mr-1"></i> {{ __('Delete / Remove Logo') }}
													</a>
												</div>
												<div class="custom-control custom-checkbox mb-2">
													<input type="checkbox" class="custom-control-input" id="remove_logo" name="remove_logo" value="1">
													<label class="custom-control-label text-danger font-weight-bold" for="remove_logo">
														<i class="fas fa-times-circle mr-1"></i> {{ __('Remove Logo on form update') }}
													</label>
												</div>
											@else
												<div class="alert alert-light border py-2 px-3 d-inline-flex align-items-center mb-2 text-muted" style="border-radius: 6px;">
													<i class="fas fa-info-circle mr-2 text-primary"></i> {{ __('No logo currently set. Slider will display cleanly without any logo.') }}
												</div>
											@endif
										<br>
										<span id="change_message" class="mt-1 text-muted">{{ $slider->home_page == 'theme3' || $slider->home_page == 'theme4' ? __('Image Size Should Be 435 x 530')  :  __('Image Size Should Be 130 x 40 (Optional - Leave empty if no logo is needed)')}}</span>
									</div>

									<div class="form-group position-relative ">
										<label class="file">
											<input type="file"  accept="image/*,.json,.lottie,application/json"  class="upload-photo" name="logo" id="file"
												aria-label="File browser example">
											<span class="file-custom text-left">{{ __('Upload New Logo (Optional)...') }}</span>
										</label>
									</div>
									<div class="form-group">
										<label for="title">{{ __('Title') }} *</label>
										<input type="text" name="title" class="form-control" id="title"
											placeholder="{{ __('Enter Title') }}" value="{{ $slider->title }}" >
									</div>

									<div class="form-group">
										<label for="slider-link">{{ __('Link') }} *</label>
										<input type="text" name="link" class="form-control" id="slider-link"
											placeholder="{{ __('Enter Link') }}" value="{{ $slider->link }}" >
									</div>


									<div class="form-group">
										<label for="details">{{ __('Details') }} *</label>
										<textarea name="details" id="details" class="form-control" rows="5"
											placeholder="{{ __('Enter Details') }}"
											>{{ $slider->details }}</textarea>
									</div>

									<div class="form-group">
										<label id="slider_text" for="name">{{ $slider->home_page == 'theme3' || $slider->home_page == 'theme4' ? __('Set Background Image') :__('Current Slider Image / Lottie') }} *</label>
										<br>
											@php
												$sliderPhotoVal = $slider->photo ?? '';
												$isLottieSliderPhoto = !empty($sliderPhotoVal) && \Illuminate\Support\Str::endsWith(strtolower($sliderPhotoVal), ['.json', '.lottie']);
											@endphp
											@if($isLottieSliderPhoto)
												<div style="max-width: 300px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; padding: 10px; margin-bottom: 8px;">
													<lottie-player src="{{ url('/core/public/storage/images/'.$sliderPhotoVal) }}" background="transparent" speed="1" style="width: 100%; height: 140px;" loop autoplay></lottie-player>
													<span class="badge badge-info mt-1"><i class="fas fa-play-circle mr-1"></i> Lottie Animation ({{ $sliderPhotoVal }})</span>
												</div>
											@else
												<img class="admin-img"
													src="{{ $slider->photo ? url('/core/public/storage/images/'.$slider->photo) : url('/core/public/storage/images/placeholder.png') }}"
													alt="No Image Found">
											@endif
										<br>
										<span id="chenge_label2" class="mt-1">{{$slider->home_page == 'theme3' || $slider->home_page == 'theme4' ? __('Image Size Should Be 1920 x 750') : __('Image Size Should Be 1000 x 530 or .json / .lottie animation file') }}</span>
									</div>

									<div class="form-group position-relative ">
										<label class="file">
											<input type="file"  accept="image/*,.json,.lottie,application/json"  class="upload-photo" name="photo" id="file"
												aria-label="File browser example">
											<span class="file-custom text-left">{{ __('Upload Image or Lottie File (.json, .lottie)...') }}</span>
										</label>
									</div>

									@else
									<div class="form-group">
										<label for="slider-link">{{ __('Link') }} *</label>
										<input type="text" name="link" class="form-control" id="slider-link"
											placeholder="{{ __('Enter Link') }}" value="{{ $slider->link }}" >
									</div>
									<input name="details" type="hidden" id="details" value="theme4" class="form-control" rows="5"
                                    placeholder="{{ __('Enter Details') }}"
                                    >
									<input type="hidden" name="title" class="form-control" id="title"
                                    placeholder="{{ __('Enter Title') }}" value="theme 4" >
									<div class="form-group">
										<label id="slider_text" for="name">{{ $slider->home_page == 'theme3' || $slider->home_page == 'theme4' ? __('Set Background Image') :__('Current Slider Image / Lottie') }} *</label>
										<br>
											@php
												$sliderPhotoVal = $slider->photo ?? '';
												$isLottieSliderPhoto = !empty($sliderPhotoVal) && \Illuminate\Support\Str::endsWith(strtolower($sliderPhotoVal), ['.json', '.lottie']);
											@endphp
											@if($isLottieSliderPhoto)
												<div style="max-width: 300px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; padding: 10px; margin-bottom: 8px;">
													<lottie-player src="{{ url('/core/public/storage/images/'.$sliderPhotoVal) }}" background="transparent" speed="1" style="width: 100%; height: 140px;" loop autoplay></lottie-player>
													<span class="badge badge-info mt-1"><i class="fas fa-play-circle mr-1"></i> Lottie Animation ({{ $sliderPhotoVal }})</span>
												</div>
											@else
												<img class="admin-img"
													src="{{ $slider->photo ? url('/core/public/storage/images/'.$slider->photo) : url('/core/public/storage/images/placeholder.png') }}"
													alt="No Image Found">
											@endif
										<br>
										<span id="chenge_label2" class="mt-1">{{$slider->home_page == 'theme3' || $slider->home_page == 'theme4' ? __('Image Size Should Be 1920 x 750') : __('Image Size Should Be 1000 x 530 or .json / .lottie animation file') }}</span>
									</div>

									<div class="form-group position-relative ">
										<label class="file">
											<input type="file"  accept="image/*,.json,.lottie,application/json"  class="upload-photo" name="photo" id="file"
												aria-label="File browser example">
											<span class="file-custom text-left">{{ __('Upload Image or Lottie File (.json, .lottie)...') }}</span>
										</label>
									</div>
									@endif
									


								    <div class="form-group">
										<button type="submit"
											class="btn btn-secondary ">{{ __('Submit') }}</button>
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
