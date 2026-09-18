@extends('master.back')
@section('styles')
    <link rel="stylesheet" href="{{asset('assets/back/css/select2.css')}}">
@endsection
@section('content')

<!-- Start of Main Content -->
<div class="container-fluid">

	<!-- Page Heading -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between">
                <h3 class="mb-0 bc-title"><b>{{ __('Language') }}</b></h3>
                </div>
        </div>
    </div>

    {{-- Create Table Btn --}}

	<!-- DataTales -->
	<div class="card shadow mb-4">
		<div class="card-body">
            <div class="row">
                <div class="col-5 col-md-3">
                    <div class="nav flex-column nav-pills nav-secondary" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <a class="nav-link active" id="v-pills-t9-tab" data-toggle="pill" href="#v-pills-t9" role="tab" aria-controls="v-pills-t9" aria-selected="true">{{ __('Hero Section Banner') }}</a>
                        <a class="nav-link" id="v-pills-t1-tab" data-toggle="pill" href="#v-pills-t1" role="tab" aria-controls="v-pills-t1" aria-selected="false">{{ __('4 Column Banner First') }}</a>
                        <a class="nav-link" id="v-pills-t2-tab" data-toggle="pill" href="#v-pills-t2" role="tab" aria-controls="v-pills-t2" aria-selected="false">{{ __('Popular Categories') }}</a>
                        <a class="nav-link" id="v-pills-t5-tab" data-toggle="pill" href="#v-pills-t5" role="tab" aria-controls="v-pills-t5" aria-selected="false">{{ __('3 column banner Second') }}</a>
                        <a class="nav-link" id="v-pills-t3-tab" data-toggle="pill" href="#v-pills-t3" role="tab" aria-controls="v-pills-t3" aria-selected="false">{{ __('Three column category') }}</a>
                        <a class="nav-link" id="v-pills-t4-tab" data-toggle="pill" href="#v-pills-t4" role="tab" aria-controls="v-pills-t4" aria-selected="false">{{ __('Newly Listed Products') }}</a>
                        <a class="nav-link" id="v-pills-t6-tab" data-toggle="pill" href="#v-pills-t6" role="tab" aria-controls="v-pills-t6" aria-selected="false">{{ __('2 column banner') }}</a>
                        <a class="nav-link" id="v-pills-t-blogs-tab" data-toggle="pill" href="#v-pills-t-blogs" role="tab" aria-controls="v-pills-t-blogs" aria-selected="false">{{ __('Blogs Section') }}</a>
                        <a class="nav-link" id="v-pills-t7-tab" data-toggle="pill" href="#v-pills-t7" role="tab" aria-controls="v-pills-t7" aria-selected="false">{{ __('Home Page 4 Banner 5 Column') }}</a>
                        <a class="nav-link" id="v-pills-t8-tab" data-toggle="pill" href="#v-pills-t8" role="tab" aria-controls="v-pills-t8" aria-selected="false">{{ __('Home Page 4 Popular Categories') }}</a>
                    </div>
                </div>
                <div class="col-7 col-md-9">
                    <div class="tab-content" id="v-pills-tabContent">
                        <div class="tab-pane fade show active" id="v-pills-t9" role="tabpanel" aria-labelledby="v-pills-t9-tab">
                            <form class="admin-form" action="{{route('back.hero.banner.update')}}"method="POST" enctype="multipart/form-data">
                                @include('alerts.alerts')
                                @csrf
                                        <div class="form-group">
                                            <label for="name">{{ __('Image 1 / Lottie Animation') }} *</label>
                                            <br>
                                            @php
                                                $img1Val = $hero_banner['img1'] ?? '';
                                                $isLottie1 = !empty($img1Val) && \Illuminate\Support\Str::endsWith(strtolower($img1Val), ['.json', '.lottie']);
                                            @endphp
                                            @if($isLottie1)
                                                <div style="max-width: 250px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; padding: 10px; margin-bottom: 8px;">
                                                    <lottie-player src="{{ url('/core/public/storage/images/'.$img1Val) }}" background="transparent" speed="1" style="width: 100%; height: 120px;" loop autoplay></lottie-player>
                                                    <span class="badge badge-info mt-1"><i class="fas fa-play-circle mr-1"></i> Lottie Animation ({{ $img1Val }})</span>
                                                </div>
                                            @else
                                                <img class="admin-img"
                                                    src="{{isset($hero_banner['img1']) ? url('/core/public/storage/images/'.$hero_banner['img1']) : url('/core/public/storage/images/placeholder.png') }}"
                                                    alt="No Image Found">
                                            @endif
                                            <br>
                                            <span class="mt-1">{{ __('Image Size: 496 x 204 or upload .json / .lottie animation file.') }}</span>
                                        </div>
                                        <div class="form-group position-relative">
                                            <label class="file">
                                                <input type="file" accept="image/*,.json,.lottie,application/json" class="upload-photo" name="img1" id="file"
                                                    aria-label="File browser example">
                                                <span class="file-custom text-left">{{ __('Upload Image or Lottie File (.json, .lottie)...') }}</span>
                                            </label>
                                        </div>

                                        <div class="form-group">
                                            <label for="title1">{{ __('Title') }} </label>
                                            <input type="text" name="title1" class="form-control" id="title1"
                                                placeholder="{{ __('Enter Title') }}"  value="{{isset($hero_banner['title1']) ? $hero_banner['title1'] : ''}}" >
                                        </div>
                                        <div class="form-group">
                                            <label for="subtitle1">{{ __('Subtitle') }} </label>
                                            <input type="text" name="subtitle1" class="form-control" id="subtitle1"
                                                placeholder="{{ __('Enter Subtitle') }}"  value="{{isset($hero_banner['subtitle1']) ? $hero_banner['subtitle1'] : ''}}" >
                                        </div>
                                        <div class="form-group">
                                            <label for="url1">{{ __('URL 1') }} </label>
                                            <input type="text" name="url1" class="form-control" id="url1"
                                                placeholder="{{ __('Enter Url') }}"  value="{{isset($hero_banner['url1']) ? $hero_banner['url1'] : ''}}" >
                                        </div>

                                        <hr>

                                        <div class="form-group">
                                            <label for="name">{{ __('Image 2 / Lottie Animation') }} *</label>
                                            <br>
                                            @php
                                                $img2Val = $hero_banner['img2'] ?? '';
                                                $isLottie2 = !empty($img2Val) && \Illuminate\Support\Str::endsWith(strtolower($img2Val), ['.json', '.lottie']);
                                            @endphp
                                            @if($isLottie2)
                                                <div style="max-width: 250px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; padding: 10px; margin-bottom: 8px;">
                                                    <lottie-player src="{{ url('/core/public/storage/images/'.$img2Val) }}" background="transparent" speed="1" style="width: 100%; height: 120px;" loop autoplay></lottie-player>
                                                    <span class="badge badge-info mt-1"><i class="fas fa-play-circle mr-1"></i> Lottie Animation ({{ $img2Val }})</span>
                                                </div>
                                            @else
                                                <img class="admin-img"
                                                    src="{{isset($hero_banner['img2']) ? url('/core/public/storage/images/'.$hero_banner['img2']) : url('/core/public/storage/images/placeholder.png') }}"
                                                    alt="No Image Found">
                                            @endif
                                            <br>
                                            <span class="mt-1">{{ __('Image Size: 496 x 204 or upload .json / .lottie animation file.') }}</span>
                                        </div>
                                        <div class="form-group position-relative">
                                            <label class="file">
                                                <input type="file" accept="image/*,.json,.lottie,application/json" class="upload-photo" name="img2" id="file"
                                                    aria-label="File browser example">
                                                <span class="file-custom text-left">{{ __('Upload Image or Lottie File (.json, .lottie)...') }}</span>
                                            </label>
                                        </div>

                                        <div class="form-group">
                                            <label for="title2">{{ __('Title') }} </label>
                                            <input type="text" name="title2" class="form-control" id="title2"
                                                placeholder="{{ __('Enter Title') }}"  value="{{isset($hero_banner['title2']) ? $hero_banner['title2'] : ''}}" >
                                        </div>
                                        <div class="form-group">
                                            <label for="subtitle2">{{ __('Subtitle') }} </label>
                                            <input type="text" name="subtitle2" class="form-control" id="subtitle2"
                                                placeholder="{{ __('Enter Subtitle') }}"  value="{{isset($hero_banner['subtitle2']) ? $hero_banner['subtitle2'] : ''}}" >
                                        </div>
                                        <div class="form-group">
                                            <label for="url2">{{ __('URL 2') }} </label>
                                            <input type="text" name="url2" class="form-control" id="url2"
                                                placeholder="{{ __('Enter Url') }}"  value="{{isset($hero_banner['url2']) ? $hero_banner['url2'] : ''}}" >
                                        </div>


                                    <div class="form-group">
                                        <button type="submit" class="btn btn-secondary ">{{ __('Submit') }}</button>
                                    </div>
                            </form>
                        </div>
                        <div class="tab-pane fade show " id="v-pills-t1" role="tabpanel" aria-labelledby="v-pills-t1-tab">
                            <form class="admin-form" action="{{route('back.first.banner.update')}}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="form_submitted" value="1">
                                <div class="card mb-4 border-0 shadow-sm" style="background: #f8fafc; border: 1px solid #e2e8f0 !important; border-radius: 10px;">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <h5 class="mb-1 font-weight-bold text-dark"><i class="fas fa-toggle-on text-primary mr-2"></i>{{ __('Show 4 Column Banner First on Home Page') }}</h5>
                                                <small class="text-muted">{{ __('Toggle OFF to completely hide this banner section from homepage on both mobile and PC.') }}</small>
                                            </div>
                                            <div class="mt-2 mt-sm-0">
                                                <label class="switch-primary mb-0">
                                                    <input type="checkbox" class="switch switch-bootstrap status section-toggle-ajax" data-field="is_three_c_b_first" name="is_three_c_b_first" value="1" {{ $setting->is_three_c_b_first == 1 ? 'checked' : '' }}>
                                                    <span class="switch-body"></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                        <div class="form-group">
                                            <label for="name">{{ __('Image 1') }} *</label>
                                            <br>
                                                <img class="admin-img"
                                                    src="{{  url('/core/public/storage/images/'.$first_banner['img1']) }}"
                                                    alt="No Image Found">
                                            <br>
                                            <span class="mt-1">{{ __('Image Size Should Be 496 x 204.') }}</span>
                                        </div>
                                        <div class="form-group position-relative">
                                            <label class="file">
                                                <input type="file"  accept="image/*,.json,.lottie,application/json"  class="upload-photo" name="img1" id="file"
                                                    aria-label="File browser example">
                                                <span class="file-custom text-left">{{ __('Upload Image or Lottie File (.json, .lottie)...') }}</span>
                                            </label>
                                        </div>

                                        <div class="form-group">
                                            <label for="title1">{{ __('Title') }} *</label>
                                            <input type="text" name="title1" class="form-control" id="title1"
                                                placeholder="{{ __('Enter Title') }}"  value="{{isset($first_banner['title1']) ? $first_banner['title1'] : ''}}" >
                                        </div>
                                        <div class="form-group">
                                            <label for="subtitle1">{{ __('Subtitle') }} *</label>
                                            <input type="text" name="subtitle1" class="form-control" id="subtitle1"
                                                placeholder="{{ __('Enter Subtitle') }}"  value="{{isset($first_banner['subtitle1']) ? $first_banner['subtitle1'] : ''}}" >
                                        </div>

                                        <div class="form-group">
                                            <label for="url">{{ __('URL 1') }} *</label>
                                            <input type="text" name="firsturl1" class="form-control" id="firsturl1"
                                                placeholder="{{ __('Enter Banner Url') }}" value="{{$first_banner['firsturl1']}}" >
                                        </div>
                                        <hr>

                                        <div class="form-group">
                                            <label for="name">{{ __('Image 2') }} *</label>
                                            <br>
                                                <img class="admin-img"
                                                    src="{{  url('/core/public/storage/images/'.$first_banner['img2']) }}"
                                                    alt="No Image Found">
                                            <br>
                                            <span class="mt-1">{{ __('Image Size Should Be 496 x 204.') }}</span>
                                        </div>
                                        <div class="form-group position-relative">
                                            <label class="file">
                                                <input type="file"  accept="image/*,.json,.lottie,application/json"  class="upload-photo" name="img2" id="file"
                                                    aria-label="File browser example">
                                                <span class="file-custom text-left">{{ __('Upload Image or Lottie File (.json, .lottie)...') }}</span>
                                            </label>
                                        </div>

                                        <div class="form-group">
                                            <label for="title2">{{ __('Title') }} *</label>
                                            <input type="text" name="title2" class="form-control" id="title2"
                                                placeholder="{{ __('Enter Title') }}"  value="{{isset($first_banner['title2']) ? $first_banner['title2'] : ''}}" >
                                        </div>
                                        <div class="form-group">
                                            <label for="subtitle2">{{ __('Subtitle') }} *</label>
                                            <input type="text" name="subtitle2" class="form-control" id="subtitle2"
                                                placeholder="{{ __('Enter Subtitle') }}"  value="{{isset($first_banner['subtitle2']) ? $first_banner['subtitle2'] : ''}}" >
                                        </div>

                                        <div class="form-group">
                                            <label for="firsturl2">{{ __('URL 2') }} *</label>
                                            <input type="text" name="firsturl2" class="form-control" id="firsturl2"
                                                placeholder="{{ __('Enter Banner Url') }}" value="{{$first_banner['firsturl2']}}" >
                                        </div>
                                        <hr>

                                        <div class="form-group">
                                            <label for="name">{{ __('Image 3') }} *</label>
                                            <br>
                                                <img class="admin-img"
                                                    src="{{  url('/core/public/storage/images/'.$first_banner['img3']) }}"
                                                    alt="No Image Found">
                                            <br>
                                            <span class="mt-1">{{ __('Image Size Should Be 496 x 204.') }}</span>
                                        </div>
                                        <div class="form-group position-relative">
                                            <label class="file">
                                                <input type="file"  accept="image/*,.json,.lottie,application/json"  class="upload-photo" name="img3" id="file"
                                                    aria-label="File browser example">
                                                <span class="file-custom text-left">{{ __('Upload Image or Lottie File (.json, .lottie)...') }}</span>
                                            </label>
                                        </div>

                                        <div class="form-group">
                                            <label for="title3">{{ __('Title') }} *</label>
                                            <input type="text" name="title3" class="form-control" id="title3"
                                                placeholder="{{ __('Enter Title') }}"  value="{{isset($first_banner['title3']) ? $first_banner['title3'] : ''}}" >
                                        </div>
                                        <div class="form-group">
                                            <label for="subtitle3">{{ __('Subtitle') }} *</label>
                                            <input type="text" name="subtitle3" class="form-control" id="subtitle3"
                                                placeholder="{{ __('Enter Subtitle') }}"  value="{{isset($first_banner['subtitle3']) ? $first_banner['subtitle3'] : ''}}" >
                                        </div>


                                        <div class="form-group">
                                            <label for="firsturl3">{{ __('URL 3') }} *</label>
                                            <input type="text" name="firsturl3" class="form-control" id="firsturl3"
                                                placeholder="{{ __('Enter Banner Url') }}" value="{{$first_banner['firsturl3']}}" >
                                        </div>
                                        <hr>

                                        <div class="form-group">
                                            <label for="name">{{ __('Image 4') }} *</label>
                                            <br>
                                                <img class="admin-img"
                                                    src="{{  isset($first_banner['img4']) && $first_banner['img4'] != '' ? url('/core/public/storage/images/'.$first_banner['img4']) : '' }}"
                                                    alt="No Image Found">
                                            <br>
                                            <span class="mt-1">{{ __('Image Size Should Be 496 x 204.') }}</span>
                                        </div>
                                        <div class="form-group position-relative">
                                            <label class="file">
                                                <input type="file"  accept="image/*,.json,.lottie,application/json"  class="upload-photo" name="img4" id="file"
                                                    aria-label="File browser example">
                                                <span class="file-custom text-left">{{ __('Upload Image or Lottie File (.json, .lottie)...') }}</span>
                                            </label>
                                        </div>

                                        <div class="form-group">
                                            <label for="title4">{{ __('Title') }} *</label>
                                            <input type="text" name="title4" class="form-control" id="title4"
                                                placeholder="{{ __('Enter Title') }}"  value="{{isset($first_banner['title4']) ? $first_banner['title4'] : ''}}" >
                                        </div>
                                        <div class="form-group">
                                            <label for="subtitle4">{{ __('Subtitle') }} *</label>
                                            <input type="text" name="subtitle4" class="form-control" id="subtitle4"
                                                placeholder="{{ __('Enter Subtitle') }}"  value="{{isset($first_banner['subtitle4']) ? $first_banner['subtitle4'] : ''}}" >
                                        </div>


                                        <div class="form-group">
                                            <label for="firsturl4">{{ __('URL 4') }} *</label>
                                            <input type="text" name="firsturl4" class="form-control" id="firsturl4"
                                                placeholder="{{ __('Enter Banner Url') }}" value="{{isset($first_banner['firsturl4']) ? $first_banner['firsturl4'] : ''}}" >
                                        </div>

                                    <div class="form-group">
                                            <button type="submit" class="btn btn-secondary ">{{ __('Submit') }}</button>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="v-pills-t2" role="tabpanel" aria-labelledby="v-pills-t2-tab">

                            <form class="admin-form" action="{{route('back.popular.category.update')}}" method="POST">
                                @csrf
                                    <div class="form-group">
                                        <label for="popular_title">{{ __('Section Title') }} *</label>
                                        <input type="text" name="popular_title" class="form-control" id="popular_title"
                                            placeholder="{{ __('Popular Category') }}" value="{{$popular_category['popular_title']}}" >
                                    </div>
                                    <hr>
                                    <h2 class=""><b>{{ __('Category 1 :') }}</b></h2>

                                    <div class="form-group">
                                        <label for="category_id1">{{ __('Select Category') }} *</label>
                                        <select name="category_id1"  id="category_id1" data-href="{{route('back.get.subcategory')}}" class="form-control" >
                                            <option value="" >{{__('Select One')}}</option>
                                            @foreach(DB::table('categories')->whereStatus(1)->get() as $cat)
                                            <option value="{{ $cat->id }}" {{$cat->id == $popular_category['category_id1'] ? 'selected' : ''}} >{{ $cat->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="subcategory_id1">{{ __('Select Sub Category') }} </label>
                                        <select name="subcategory_id1" id="subcategory_id1" class="form-control" data-href="{{route('back.get.childcategory')}}">
                                            <option value="">{{__('Select one')}}</option>
                                            @foreach(DB::table('subcategories')->where('category_id',$popular_category['category_id1'])->whereStatus(1)->get() as $subcat)
                                            <option value="{{ $subcat->id }}" {{ $subcat->id == $popular_category['subcategory_id1']? 'selected' : '' }}>{{ $subcat->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="childcategory_id1">{{ __('Select Child Category') }} </label>
                                        <select name="childcategory_id1" id="childcategory_id1" class="form-control">
                                            <option value="">{{__('Select one')}}</option>
                                            @foreach(DB::table('chield_categories')->where('category_id',$popular_category['category_id1'])->whereStatus(1)->get() as $chieldcategory)
                                            <option value="{{ $chieldcategory->id }}" {{ $chieldcategory->id == $popular_category['childcategory_id1'] ? 'selected' : '' }}>{{ $chieldcategory->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <hr>
                                    <h2 class=""><b>{{ __('Category 2 :') }}</b></h2>
                                    <div class="form-group">
                                        <label for="category_id2">{{ __('Select Category') }} *</label>
                                        <select name="category_id2" id="category_id2" data-href="{{route('back.get.subcategory')}}" class="form-control" >
                                            <option value="" >{{__('Select One')}}</option>
                                            @foreach(DB::table('categories')->whereStatus(1)->get() as $cat)
                                            <option value="{{ $cat->id }}" {{$cat->id == $popular_category['category_id2'] ? 'selected' : ''}}>{{ $cat->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="subcategory_id2">{{ __('Select Sub Category') }} </label>
                                        <select name="subcategory_id2" id="subcategory_id2" class="form-control" data-href="{{route('back.get.childcategory')}}">
                                            <option value="">{{__('Select one')}}</option>
                                            @foreach(DB::table('subcategories')->where('category_id',$popular_category['category_id2'])->whereStatus(1)->get() as $subcat)
                                            <option value="{{ $subcat->id }}" {{ $subcat->id == $popular_category['subcategory_id2']? 'selected' : '' }}>{{ $subcat->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="childcategory_id2">{{ __('Select Child Category') }} </label>
                                        <select name="childcategory_id2" id="childcategory_id2" class="form-control">
                                            <option value="">{{__('Select one')}}</option>
                                            @foreach(DB::table('chield_categories')->where('category_id',$popular_category['category_id2'])->whereStatus(1)->get() as $chieldcategory)
                                            <option value="{{ $chieldcategory->id }}" {{ $chieldcategory->id == $popular_category['childcategory_id2'] ? 'selected' : '' }}>{{ $chieldcategory->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <hr>
                                    <h2 class=""><b>{{ __('Category 3 :') }}</b></h2>
                                    <div class="form-group">
                                        <label for="category_id3">{{ __('Select Category') }} *</label>
                                        <select name="category_id3" id="category_id3" data-href="{{route('back.get.subcategory')}}" class="form-control" >
                                            <option value="" >{{__('Select One')}}</option>
                                            @foreach(DB::table('categories')->whereStatus(1)->get() as $cat)
                                            <option value="{{ $cat->id }}" {{$cat->id == $popular_category['category_id3'] ? 'selected' : ''}} >{{ $cat->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="subcategory_id3">{{ __('Select Sub Category') }} </label>
                                        <select name="subcategory_id3" id="subcategory_id3" class="form-control" data-href="{{route('back.get.childcategory')}}">
                                            <option value="">{{__('Select one')}}</option>
                                            @foreach(DB::table('subcategories')->where('category_id',$popular_category['category_id3'])->whereStatus(1)->get() as $subcat)
                                            <option value="{{ $subcat->id }}" {{ $subcat->id == $popular_category['subcategory_id3']? 'selected' : '' }}>{{ $subcat->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="childcategory_id3">{{ __('Select Child Category') }} </label>
                                        <select name="childcategory_id3" id="childcategory_id3" class="form-control">
                                            <option value="">{{__('Select one')}}</option>
                                            @foreach(DB::table('chield_categories')->where('category_id',$popular_category['category_id3'])->whereStatus(1)->get() as $chieldcategory)
                                            <option value="{{ $chieldcategory->id }}" {{ $chieldcategory->id == $popular_category['childcategory_id3'] ? 'selected' : '' }}>{{ $chieldcategory->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <hr>
                                    <h2 class=""><b>{{ __('Category 4 :') }}</b></h2>
                                    <div class="form-group">
                                        <label for="category_id4">{{ __('Select Category') }} *</label>
                                        <select name="category_id4" id="category_id4" data-href="{{route('back.get.subcategory')}}" class="form-control" >
                                            <option value="" >{{__('Select One')}}</option>
                                            @foreach(DB::table('categories')->whereStatus(1)->get() as $cat)
                                            <option value="{{ $cat->id }}" {{$cat->id == $popular_category['category_id4'] ? 'selected' : ''}}>{{ $cat->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="subcategory_id4">{{ __('Select Sub Category') }} </label>
                                        <select name="subcategory_id4" id="subcategory_id4" class="form-control" data-href="{{route('back.get.childcategory')}}">
                                            <option value="">{{__('Select one')}}</option>
                                            @foreach(DB::table('subcategories')->where('category_id',$popular_category['category_id4'])->whereStatus(1)->get() as $subcat)
                                            <option value="{{ $subcat->id }}" {{ $subcat->id == $popular_category['subcategory_id4']? 'selected' : '' }}>{{ $subcat->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="childcategory_id4">{{ __('Select Child Category') }} </label>
                                        <select name="childcategory_id4" id="childcategory_id4" class="form-control">
                                            <option value="">{{__('Select one')}}</option>
                                            @foreach(DB::table('chield_categories')->where('category_id',$popular_category['category_id4'])->whereStatus(1)->get() as $chieldcategory)
                                            <option value="{{ $chieldcategory->id }}" {{ $chieldcategory->id == $popular_category['childcategory_id4'] ? 'selected' : '' }}>{{ $chieldcategory->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>


                                <div class="form-group">
                                <button type="submit" class="btn btn-secondary ">{{ __('Submit') }}</button>
                            </div>
                        </form>
                        </div>

                        <div class="tab-pane fade" id="v-pills-t5" role="tabpanel" aria-labelledby="v-pills-t5-tab">
                            <form class="admin-form" action="{{route('back.secend.banner.update')}}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="form_submitted" value="1">
                                <div class="card mb-4 border-0 shadow-sm" style="background: #f8fafc; border: 1px solid #e2e8f0 !important; border-radius: 10px;">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <h5 class="mb-1 font-weight-bold text-dark"><i class="fas fa-toggle-on text-primary mr-2"></i>{{ __('Show 3 Column Banner Second on Home Page') }}</h5>
                                                <small class="text-muted">{{ __('Toggle OFF to completely hide this banner section from homepage on both mobile and PC.') }}</small>
                                            </div>
                                            <div class="mt-2 mt-sm-0">
                                                <label class="switch-primary mb-0">
                                                    <input type="checkbox" class="switch switch-bootstrap status section-toggle-ajax" data-field="is_three_c_b_second" name="is_three_c_b_second" value="1" {{ $setting->is_three_c_b_second == 1 ? 'checked' : '' }}>
                                                    <span class="switch-body"></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                        <div class="form-group">
                                            <label for="name">{{ __('Image 1') }} *</label>
                                            <br>
                                                <img class="admin-img"
                                                    src="{{  url('/core/public/storage/images/'.$secend_banner['img1']) }}"
                                                    alt="No Image Found">
                                            <br>
                                            <span class="mt-1">{{ __('Image Size Should Be 496 x 204.') }}</span>
                                        </div>
                                        <div class="form-group position-relative">
                                            <label class="file">
                                                <input type="file"  accept="image/*,.json,.lottie,application/json"  class="upload-photo" name="img1" id="file"
                                                    aria-label="File browser example">
                                                <span class="file-custom text-left">{{ __('Upload Image or Lottie File (.json, .lottie)...') }}</span>
                                            </label>
                                        </div>

                                        <div class="form-group">
                                            <label for="title1">{{ __('Title') }} *</label>
                                            <input type="text" name="title1" class="form-control" id="title1"
                                                placeholder="{{ __('Enter Title') }}"  value="{{isset($secend_banner['title1']) ? $secend_banner['title1'] : ''}}" >
                                        </div>
                                        <div class="form-group">
                                            <label for="subtitle1">{{ __('Subtitle') }} *</label>
                                            <input type="text" name="subtitle1" class="form-control" id="subtitle1"
                                                placeholder="{{ __('Enter Subtitle') }}"  value="{{isset($secend_banner['subtitle1']) ? $secend_banner['subtitle1'] : ''}}" >
                                        </div>

                                        <div class="form-group">
                                            <label for="url">{{ __('URL 1') }} *</label>
                                            <input type="text" name="url1" class="form-control" id="url1"
                                                placeholder="{{ __('Enter Banner Url') }}" value="{{$secend_banner['url1']}}" >
                                        </div>
                                        <hr>

                                        <div class="form-group">
                                            <label for="name">{{ __('Image 2') }} *</label>
                                            <br>
                                                <img class="admin-img"
                                                    src="{{  url('/core/public/storage/images/'.$secend_banner['img2']) }}"
                                                    alt="No Image Found">
                                            <br>
                                            <span class="mt-1">{{ __('Image Size Should Be 496 x 204.') }}</span>
                                        </div>
                                        <div class="form-group position-relative">
                                            <label class="file">
                                                <input type="file"  accept="image/*,.json,.lottie,application/json"  class="upload-photo" name="img2" id="file"
                                                    aria-label="File browser example">
                                                <span class="file-custom text-left">{{ __('Upload Image or Lottie File (.json, .lottie)...') }}</span>
                                            </label>
                                        </div>

                                        <div class="form-group">
                                            <label for="title2">{{ __('Title') }} *</label>
                                            <input type="text" name="title2" class="form-control" id="title2"
                                                placeholder="{{ __('Enter Title') }}"  value="{{isset($secend_banner['title2']) ? $secend_banner['title2'] : ''}}" >
                                        </div>
                                        <div class="form-group">
                                            <label for="subtitle2">{{ __('Subtitle') }} *</label>
                                            <input type="text" name="subtitle2" class="form-control" id="subtitle2"
                                                placeholder="{{ __('Enter Subtitle') }}"  value="{{isset($secend_banner['subtitle2']) ? $secend_banner['subtitle2'] : ''}}" >
                                        </div>

                                        <div class="form-group">
                                            <label for="url">{{ __('URL 2') }} *</label>
                                            <input type="text" name="url2" class="form-control" id="url2"
                                                placeholder="{{ __('Enter Banner Url') }}" value="{{$secend_banner['url2']}}" >
                                        </div>
                                        <hr>

                                        <div class="form-group">
                                            <label for="name">{{ __('Image 3') }} *</label>
                                            <br>
                                                <img class="admin-img"
                                                    src="{{  url('/core/public/storage/images/'.$secend_banner['img3']) }}"
                                                    alt="No Image Found">
                                            <br>
                                            <span class="mt-1">{{ __('Image Size Should Be 496 x 204.') }}</span>
                                        </div>
                                        <div class="form-group position-relative">
                                            <label class="file">
                                                <input type="file"  accept="image/*,.json,.lottie,application/json"  class="upload-photo" name="img3" id="file"
                                                    aria-label="File browser example">
                                                <span class="file-custom text-left">{{ __('Upload Image or Lottie File (.json, .lottie)...') }}</span>
                                            </label>
                                        </div>

                                        <div class="form-group">
                                            <label for="title3">{{ __('Title') }} *</label>
                                            <input type="text" name="title3" class="form-control" id="title3"
                                                placeholder="{{ __('Enter Title') }}"  value="{{isset($secend_banner['title3']) ? $secend_banner['title3'] : ''}}" >
                                        </div>
                                        <div class="form-group">
                                            <label for="subtitle3">{{ __('Subtitle') }} *</label>
                                            <input type="text" name="subtitle3" class="form-control" id="subtitle3"
                                                placeholder="{{ __('Enter Subtitle') }}"  value="{{isset($secend_banner['subtitle3']) ? $secend_banner['subtitle3'] : ''}}" >
                                        </div>


                                        <div class="form-group">
                                            <label for="url">{{ __('URL 3') }} *</label>
                                            <input type="text" name="url3" class="form-control" id="url3"
                                                placeholder="{{ __('Enter Banner Url') }}" value="{{$secend_banner['url3']}}" >
                                        </div>

                                    <div class="form-group">
                                            <button type="submit" class="btn btn-secondary ">{{ __('Submit') }}</button>
                                </div>
                            </form>
                        </div>

                        <div class="tab-pane fade" id="v-pills-t3" role="tabpanel" aria-labelledby="v-pills-t3-tab">
                            <form class="admin-form" action="{{route('back.tree.column.category.update')}}" method="POST">
                                @csrf
                                <hr>
                                <h2 class=""><b>{{ __('Category 1 :') }}</b></h2>

                                <div class="form-group">
                                    <label for="column_category_id1">{{ __('Select Category') }} *</label>
                                    <select name="category_id1" id="column_category_id1" data-href="{{route('back.get.subcategory')}}" class="form-control" >
                                        <option value="" >{{__('Select One')}}</option>
                                        @foreach(DB::table('categories')->whereStatus(1)->get() as $cat)
                                        <option value="{{ $cat->id }}" {{$cat->id == $three_column_category['category_id1'] ? 'selected' : ''}} >{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="cloumn_subcategory_id2">{{ __('Select Sub Category') }} </label>
                                    <select name="subcategory_id1" id="cloumn_subcategory_id1" class="form-control" data-href="{{route('back.get.childcategory')}}">
                                        <option value="">{{__('Select one')}}</option>
                                        @foreach(DB::table('subcategories')->where('category_id',$three_column_category['category_id1'])->whereStatus(1)->get() as $subcat)
                                        <option value="{{ $subcat->id }}" {{ $subcat->id == $three_column_category['subcategory_id1']? 'selected' : '' }}>{{ $subcat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="cloumn_childcategory_id1">{{ __('Select Child Category') }} </label>
                                    <select name="childcategory_id1"  id="cloumn_childcategory_id1" class="form-control">
                                        <option value="">{{__('Select one')}}</option>
                                        @foreach(DB::table('chield_categories')->where('category_id',$three_column_category['category_id1'])->whereStatus(1)->get() as $chieldcategory)
                                        <option value="{{ $chieldcategory->id }}" {{ $chieldcategory->id == $three_column_category['childcategory_id1'] ? 'selected' : '' }}>{{ $chieldcategory->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <hr>
                                <h2 class=""><b>{{ __('Category 2 :') }}</b></h2>
                                <div class="form-group">
                                    <label for="column_category_id2">{{ __('Select Category') }} *</label>
                                    <select name="category_id2" id="column_category_id2" data-href="{{route('back.get.subcategory')}}" class="form-control" >
                                        <option value="" >{{__('Select One')}}</option>
                                        @foreach(DB::table('categories')->whereStatus(1)->get() as $cat)
                                        <option value="{{ $cat->id }}" {{$cat->id == $three_column_category['category_id2'] ? 'selected' : ''}}>{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="cloumn_subcategory_id2">{{ __('Select Sub Category') }} </label>
                                    <select name="subcategory_id2" id="cloumn_subcategory_id2" class="form-control" data-href="{{route('back.get.childcategory')}}">
                                        <option value="">{{__('Select one')}}</option>
                                        @foreach(DB::table('subcategories')->where('category_id',$three_column_category['category_id2'])->whereStatus(1)->get() as $subcat)
                                        <option value="{{ $subcat->id }}" {{ $subcat->id == $three_column_category['subcategory_id2']? 'selected' : '' }}>{{ $subcat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="cloumn_childcategory_id2">{{ __('Select Child Category') }} </label>
                                    <select name="childcategory_id2" id="cloumn_childcategory_id2" class="form-control">
                                        <option value="">{{__('Select one')}}</option>
                                        @foreach(DB::table('chield_categories')->where('category_id',$three_column_category['category_id2'])->whereStatus(1)->get() as $chieldcategory)
                                        <option value="{{ $chieldcategory->id }}" {{ $chieldcategory->id == $three_column_category['childcategory_id2'] ? 'selected' : '' }}>{{ $chieldcategory->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <hr>
                                <h2 class=""><b>{{ __('Category 3 :') }}</b></h2>
                                <div class="form-group">
                                    <label for="column_category_id3">{{ __('Select Category') }} *</label>
                                    <select name="category_id3" id="column_category_id3" data-href="{{route('back.get.subcategory')}}" class="form-control" >
                                        <option value="" >{{__('Select One')}}</option>
                                        @foreach(DB::table('categories')->whereStatus(1)->get() as $cat)
                                        <option value="{{ $cat->id }}" {{ isset($three_column_category['category_id3']) && $cat->id == $three_column_category['category_id3'] ? 'selected' : ''}}>{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="cloumn_subcategory_id3">{{ __('Select Sub Category') }} </label>
                                    <select name="subcategory_id3" id="cloumn_subcategory_id3" class="form-control" data-href="{{route('back.get.childcategory')}}">
                                        <option value="">{{__('Select one')}}</option>
                                        @php
                                            if(isset($three_column_category['category_id3'])){
                                                $subcategory = DB::table('subcategories')->where('category_id', $three_column_category['category_id3'])->whereStatus(1)->get();
                                            }else{
                                                $subcategory = DB::table('subcategories')->whereStatus(1)->get();
                                            }
                                        @endphp
                                        @foreach($subcategory as $subcat)
                                        <option value="{{ $subcat->id }}" {{ isset($three_column_category['category_id3']) &&  $subcat->id == $three_column_category['subcategory_id3']? 'selected' : '' }}>{{ $subcat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="cloumn_childcategory_id3">{{ __('Select Child Category') }} </label>
                                    <select name="childcategory_id3" id="cloumn_childcategory_id3" class="form-control">
                                        <option value="">{{__('Select one')}}</option>
                                        @php
                                            if(isset($three_column_category['category_id3'])){
                                                $childcategory = DB::table('chield_categories')->where('category_id',$three_column_category['category_id3'])->whereStatus(1)->get();
                                            }else{
                                                $childcategory = DB::table('chield_categories')->whereStatus(1)->get();
                                            }
                                        @endphp
                                        @foreach($childcategory as $chieldcategory)
                                        <option value="{{ $chieldcategory->id }}" {{isset($three_column_category['category_id3']) &&  $chieldcategory->id == $three_column_category['childcategory_id3'] ? 'selected' : '' }}>{{ $chieldcategory->name }}</option>
                                        @endforeach
                                    </select>
                                </div>



                                <div class="form-group">
                                    <button type="submit" class="btn btn-secondary ">{{ __('Submit') }}</button>
                                </div>
                            </form>
                        </div>

                        <div class="tab-pane fade" id="v-pills-t4" role="tabpanel" aria-labelledby="v-pills-t4-tab">
                            <form class="admin-form" action="{{route('back.feature.category.update')}}" method="POST">
                                @csrf
                                <input type="hidden" name="form_submitted" value="1">
                                <div class="card mb-4 border-0 shadow-sm" style="background: #f8fafc; border: 1px solid #e2e8f0 !important; border-radius: 10px;">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <h5 class="mb-1 font-weight-bold text-dark"><i class="fas fa-toggle-on text-primary mr-2"></i>{{ __('Show Newly Listed Products on Home Page') }}</h5>
                                                <small class="text-muted">{{ __('Toggle OFF to completely hide this section from homepage on both mobile and PC.') }}</small>
                                            </div>
                                            <div class="mt-2 mt-sm-0">
                                                <label class="switch-primary mb-0">
                                                    <input type="checkbox" class="switch switch-bootstrap status section-toggle-ajax" data-field="is_featured_category" name="is_featured_category" value="1" {{ $setting->is_featured_category == 1 ? 'checked' : '' }}>
                                                    <span class="switch-body"></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="feature_title">{{ __('Section Title') }} *</label>
                                    <input type="text" name="feature_title" class="form-control" id="feature_title"
                                        placeholder="{{ __('Newly Listed Products') }}" value="{{ $feature_category['feature_title'] ?? ($feature_category['title'] ?? __('Newly Listed Products')) }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="newly_listed_limit">{{ __('Number of Products to Show on Home Page') }} *</label>
                                    <select name="limit" id="newly_listed_limit" class="form-control">
                                        @php
                                            $currentLimit = isset($feature_category['limit']) ? (int)$feature_category['limit'] : 8;
                                        @endphp
                                        <option value="4" {{ $currentLimit == 4 ? 'selected' : '' }}>4 {{ __('Products') }} (1 {{ __('Row on PC') }}, 2 {{ __('Rows on Mobile') }})</option>
                                        <option value="8" {{ $currentLimit == 8 ? 'selected' : '' }}>8 {{ __('Products') }} (2 {{ __('Rows on PC') }}, 4 {{ __('Rows on Mobile') }})</option>
                                        <option value="12" {{ $currentLimit == 12 ? 'selected' : '' }}>12 {{ __('Products') }} (3 {{ __('Rows on PC') }}, 6 {{ __('Rows on Mobile') }})</option>
                                        <option value="16" {{ $currentLimit == 16 ? 'selected' : '' }}>16 {{ __('Products') }} (4 {{ __('Rows on PC') }}, 8 {{ __('Rows on Mobile') }})</option>
                                        <option value="20" {{ $currentLimit == 20 ? 'selected' : '' }}>20 {{ __('Products') }} (5 {{ __('Rows on PC') }}, 10 {{ __('Rows on Mobile') }})</option>
                                        <option value="24" {{ $currentLimit == 24 ? 'selected' : '' }}>24 {{ __('Products') }} (6 {{ __('Rows on PC') }}, 12 {{ __('Rows on Mobile') }})</option>
                                    </select>
                                    <small class="text-muted d-block mt-2"><i class="fas fa-info-circle mr-1"></i> {{ __('Desktop displays 4 products per row. Mobile displays 2 products per row. Automatically contains 25% Admin products and 75% Vendor products. A "View All" button links to the Top 100 Newly Listed Products.') }}</small>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-secondary ">{{ __('Submit') }}</button>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="v-pills-t6" role="tabpanel" aria-labelledby="v-pills-t6-tab">
                            <form class="admin-form" action="{{route('back.third.banner.update')}}" method="post" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="form_submitted" value="1">
                                <div class="card mb-4 border-0 shadow-sm" style="background: #f8fafc; border: 1px solid #e2e8f0 !important; border-radius: 10px;">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <h5 class="mb-1 font-weight-bold text-dark"><i class="fas fa-toggle-on text-primary mr-2"></i>{{ __('Show 2 Column Banner on Home Page') }}</h5>
                                                <small class="text-muted">{{ __('Toggle OFF to completely hide this banner section from homepage on both mobile and PC.') }}</small>
                                            </div>
                                            <div class="mt-2 mt-sm-0">
                                                <label class="switch-primary mb-0">
                                                    <input type="checkbox" class="switch switch-bootstrap status section-toggle-ajax" data-field="is_two_c_b" name="is_two_c_b" value="1" {{ $setting->is_two_c_b == 1 ? 'checked' : '' }}>
                                                    <span class="switch-body"></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                        <div class="form-group">
                                            <label for="name">{{ __('Image 1') }} *</label>
                                            <br>
                                                <img class="admin-img"
                                                    src="{{  url('/core/public/storage/images/'.$third_banner['img1']) }}"
                                                    alt="No Image Found">
                                            <br>
                                            <span class="mt-1">{{ __('Image Size Should Be 496 x 204.') }}</span>
                                        </div>
                                        <div class="form-group position-relative">
                                            <label class="file">
                                                <input type="file"  accept="image/*,.json,.lottie,application/json"  class="upload-photo" name="img1" id="file"
                                                    aria-label="File browser example">
                                                <span class="file-custom text-left">{{ __('Upload Image or Lottie File (.json, .lottie)...') }}</span>
                                            </label>
                                        </div>
                                        <div class="form-group">
                                            <label for="title1">{{ __('Title') }} *</label>
                                            <input type="text" name="title1" class="form-control" id="title1"
                                                placeholder="{{ __('Enter Title') }}"  value="{{isset($third_banner['title1']) ? $third_banner['title1'] : ''}}" >
                                        </div>
                                        <div class="form-group">
                                            <label for="subtitle1">{{ __('Subtitle') }} *</label>
                                            <input type="text" name="subtitle1" class="form-control" id="subtitle1"
                                                placeholder="{{ __('Enter Subtitle') }}"  value="{{isset($third_banner['subtitle1']) ? $third_banner['subtitle1'] : ''}}" >
                                        </div>

                                        <div class="form-group">
                                            <label for="url">{{ __('URL 1') }} *</label>
                                            <input type="text" name="url1" class="form-control" id="url1"
                                                placeholder="{{ __('Enter Banner Url') }}" value="{{$third_banner['url1']}}" >
                                        </div>
                                        <hr>

                                        <div class="form-group">
                                            <label for="name">{{ __('Image 2') }} *</label>
                                            <br>
                                                <img class="admin-img"
                                                    src="{{  url('/core/public/storage/images/'.$third_banner['img2']) }}"
                                                    alt="No Image Found">
                                            <br>
                                            <span class="mt-1">{{ __('Image Size Should Be 496 x 204.') }}</span>
                                        </div>
                                        <div class="form-group position-relative">
                                            <label class="file">
                                                <input type="file"  accept="image/*,.json,.lottie,application/json"  class="upload-photo" name="img2" id="file"
                                                    aria-label="File browser example">
                                                <span class="file-custom text-left">{{ __('Upload Image or Lottie File (.json, .lottie)...') }}</span>
                                            </label>
                                        </div>

                                        <div class="form-group">
                                            <label for="title2">{{ __('Title') }} *</label>
                                            <input type="text" name="title2" class="form-control" id="title2"
                                                placeholder="{{ __('Enter Title') }}"  value="{{isset($third_banner['title2']) ? $third_banner['title2'] : ''}}" >
                                        </div>
                                        <div class="form-group">
                                            <label for="subtitle2">{{ __('Subtitle') }} *</label>
                                            <input type="text" name="subtitle2" class="form-control" id="subtitle2"
                                                placeholder="{{ __('Enter Subtitle') }}"  value="{{isset($third_banner['subtitle2']) ? $third_banner['subtitle2'] : ''}}" >
                                        </div>
                                        <div class="form-group">
                                            <label for="url">{{ __('URL 2') }} *</label>
                                            <input type="text" name="url2" class="form-control" id="url2"
                                                placeholder="{{ __('Enter Banner Url') }}" value="{{$third_banner['url2']}}" >
                                        </div>

                                    <div class="form-group">
                                            <button type="submit"
                                                class="btn btn-secondary ">{{ __('Submit') }}</button>
                                </div>
                            </form>
                        </div>

                        <div class="tab-pane fade" id="v-pills-t7" role="tabpanel" aria-labelledby="v-pills-t7-tab">
                            <form class="admin-form" action="{{route('back.home_page4.banner.update')}}"method="POST" enctype="multipart/form-data">
                                @include('alerts.alerts')
                                @csrf
                                        <div class="form-group">
                                            <label for="name">{{ __('Banner 1 Image') }} *</label>
                                            <br>
                                                <img class="admin-img"
                                                    src="{{ isset($home4_banner['img1']) ?  url('/core/public/storage/images/'.$home4_banner['img1']) : url('/core/public/storage/images/placeholder.png') }}"
                                                    alt="No Image Found">
                                            <br>
                                            <span class="mt-1">{{ __('Image Size Should Be 496 x 204.') }}</span>
                                        </div>
                                        <div class="form-group position-relative">
                                            <label class="file">
                                                <input type="file"  accept="image/*,.json,.lottie,application/json"  class="upload-photo" name="img1" id="file"
                                                    aria-label="File browser example">
                                                <span class="file-custom text-left">{{ __('Upload Image or Lottie File (.json, .lottie)...') }}</span>
                                            </label>
                                        </div>
                                        <div class="form-group">
                                            <label for="label1">{{ __('Banner 1 Button Text') }} *</label>
                                            <input type="text" name="label1" class="form-control" id="label1"
                                                placeholder="{{ __('Enter Banner Url') }}"  value="{{isset($home4_banner['label1']) ? $home4_banner['label1'] : ''}}" >
                                        </div>
                                        <div class="form-group">
                                            <label for="url1">{{ __('Banner 1 Button Link') }} *</label>
                                            <input type="text" name="url1" class="form-control" id="url1"
                                                placeholder="{{ __('Enter Banner Url') }}"  value="{{isset($home4_banner['url1']) ? $home4_banner['url1']: ''}}" >
                                        </div>

                                        <hr>

                                        <div class="form-group">
                                            <label for="name">{{ __('Banner 2 Image') }} *</label>
                                            <br>
                                                <img class="admin-img"
                                                    src="{{ isset($home4_banner['img2']) ?  url('/core/public/storage/images/'.$home4_banner['img2']) : url('/core/public/storage/images/placeholder.png') }}"
                                                    alt="No Image Found">
                                            <br>
                                            <span class="mt-1">{{ __('Image Size Should Be 496 x 204.') }}</span>
                                        </div>

                                        <div class="form-group position-relative">
                                            <label class="file">
                                                <input type="file"  accept="image/*,.json,.lottie,application/json"  class="upload-photo" name="img2" id="file"
                                                    aria-label="File browser example">
                                                <span class="file-custom text-left">{{ __('Upload Image or Lottie File (.json, .lottie)...') }}</span>
                                            </label>
                                        </div>

                                        <div class="form-group">
                                            <label for="label2">{{ __('Banner 2 Button Text') }} *</label>
                                            <input type="text" name="label2" class="form-control" id="label2"
                                                placeholder="{{ __('Enter Banner Url') }}"  value="{{isset($home4_banner['label2']) ? $home4_banner['label2'] : ''}}" >
                                        </div>
                                        <div class="form-group">
                                            <label for="url2">{{ __('Banner 2 Button Link') }} *</label>
                                            <input type="text" name="url2" class="form-control" id="url2"
                                                placeholder="{{ __('Enter Banner Url') }}"  value="{{isset($home4_banner['url2']) ? $home4_banner['url2'] : ''}}" >
                                        </div>

                                        <hr>

                                        <div class="form-group">
                                            <label for="name">{{ __('Banner 3 Image') }} * <small>({{ __('Middle Big Image') }})</small></label>
                                            <br>
                                                <img class="admin-img"
                                                    src="{{ isset($home4_banner['img3']) ?  url('/core/public/storage/images/'.$home4_banner['img3']) : url('/core/public/storage/images/placeholder.png') }}"
                                                    alt="No Image Found">
                                            <br>
                                            <span class="mt-1">{{ __('Image Size Should Be 496 x 204.') }}</span>
                                        </div>
                                        <div class="form-group position-relative">
                                            <label class="file">
                                                <input type="file"  accept="image/*,.json,.lottie,application/json"  class="upload-photo" name="img3" id="file"
                                                    aria-label="File browser example">
                                                <span class="file-custom text-left">{{ __('Upload Image or Lottie File (.json, .lottie)...') }}</span>
                                            </label>
                                        </div>
                                        <div class="form-group">
                                            <label for="label3">{{ __('Banner 3 Button Text') }} *</label>
                                            <input type="text" name="label3" class="form-control" id="label3"
                                                placeholder="{{ __('Enter Banner Url') }}"  value="{{isset($home4_banner['label3']) ? $home4_banner['label3'] : ''}}" >
                                        </div>
                                        <div class="form-group">
                                            <label for="url3">{{ __('Banner 3 Button Link') }} *</label>
                                            <input type="text" name="url3" class="form-control" id="url3"
                                                placeholder="{{ __('Enter Banner Url') }}"  value="{{isset($home4_banner['url3']) ? $home4_banner['url3'] : ''}}" >
                                        </div>

                                        <hr>

                                        <div class="form-group">
                                            <label for="name">{{ __('Banner 4 Image') }} *</label>
                                            <br>
                                                <img class="admin-img"
                                                    src="{{ isset($home4_banner['img4']) ?  url('/core/public/storage/images/'.$home4_banner['img4']) : url('/core/public/storage/images/placeholder.png') }}"
                                                    alt="No Image Found">
                                            <br>
                                            <span class="mt-1">{{ __('Image Size Should Be 496 x 204.') }}</span>
                                        </div>
                                        <div class="form-group position-relative">
                                            <label class="file">
                                                <input type="file"  accept="image/*,.json,.lottie,application/json"  class="upload-photo" name="img4" id="file"
                                                    aria-label="File browser example">
                                                <span class="file-custom text-left">{{ __('Upload Image or Lottie File (.json, .lottie)...') }}</span>
                                            </label>
                                        </div>
                                        <div class="form-group">
                                            <label for="label4">{{ __('Banner 4 Button Text') }} *</label>
                                            <input type="text" name="label4" class="form-control" id="label4"
                                                placeholder="{{ __('Enter Banner Url') }}"  value="{{isset($home4_banner['label4']) ? $home4_banner['label4'] : ''}}" >
                                        </div>
                                        <div class="form-group">
                                            <label for="url4">{{ __('Banner 4 Button Link') }} *</label>
                                            <input type="text" name="url4" class="form-control" id="url4"
                                                placeholder="{{ __('Enter Banner Url') }}"  value="{{isset($home4_banner['url4']) ? $home4_banner['url4'] : ''}}" >
                                        </div>

                                        <hr>

                                        <div class="form-group">
                                            <label for="name">{{ __('Banner 5 Image') }} *</label>
                                            <br>
                                                <img class="admin-img"
                                                    src="{{ isset($home4_banner['img5']) ?  url('/core/public/storage/images/'.$home4_banner['img5']) : url('/core/public/storage/images/placeholder.png') }}"
                                                    alt="No Image Found">
                                            <br>
                                            <span class="mt-1">{{ __('Image Size Should Be 496 x 204.') }}</span>
                                        </div>
                                        <div class="form-group position-relative">
                                            <label class="file">
                                                <input type="file"  accept="image/*,.json,.lottie,application/json"  class="upload-photo" name="img5" id="file"
                                                    aria-label="File browser example">
                                                <span class="file-custom text-left">{{ __('Upload Image or Lottie File (.json, .lottie)...') }}</span>
                                            </label>
                                        </div>

                                        <div class="form-group">
                                            <label for="label5">{{ __('Banner 5 Button Text') }} *</label>
                                            <input type="text" name="label5" class="form-control" id="label5"
                                                placeholder="{{ __('Enter Banner Url') }}"  value="{{isset($home4_banner['label5']) ? $home4_banner['label5'] : ''}}" >
                                        </div>
                                        <div class="form-group">
                                            <label for="url5">{{ __('Banner 5 Button Link') }} *</label>
                                            <input type="text" name="url5" class="form-control" id="url5"
                                                placeholder="{{ __('Enter Banner Url') }}"  value="{{isset($home4_banner['url5']) ? $home4_banner['url5'] : ''}}" >
                                        </div>

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-secondary ">{{ __('Submit') }}</button>
                                    </div>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="v-pills-t8" role="tabpanel" aria-labelledby="v-pills-t8-tab">
                            <form class="admin-form" action="{{route('back.home4.category.update')}}"
                                method="post" enctype="multipart/form-data">
                                @csrf
                                @php
                                    if(isset($home_4_popular_category)){
                                        $home_4_popular_category = $home_4_popular_category;
                                    }else{
                                        $home_4_popular_category = [];
                                    }
                                @endphp
                                <label for="basic">{{ __('Select Sub Category') }} </label>
                                <select name="home_4_popular_category[]" id="basic" class="form-control" multiple data-href="{{route('back.get.childcategory')}}">
                                    @foreach(DB::table('categories')->whereStatus(1)->get() as $category)
                                    <option value="{{ $category->id }}" {{ in_array($category->id,$home_4_popular_category) ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-secondary ">{{ __('Submit') }}</button>
                        </div>
                    </form>
                            </div>

                        <div class="tab-pane fade" id="v-pills-t-blogs" role="tabpanel" aria-labelledby="v-pills-t-blogs-tab">
                            <div class="card mb-4 border-0 shadow-sm" style="background: #f8fafc; border: 1px solid #e2e8f0 !important; border-radius: 10px;">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap">
                                        <div class="d-flex align-items-center">
                                            <div class="mr-3 text-primary">
                                                <i class="fas fa-rss-square" style="font-size: 32px;"></i>
                                            </div>
                                            <div>
                                                <h4 class="mb-1 font-weight-bold text-dark">{{ __('Show Blogs Section on Home Page') }}</h4>
                                                <p class="text-muted mb-0">{{ __('Toggle ON/OFF to show or hide the "Our Blog" section on the homepage for both mobile and PC.') }}</p>
                                            </div>
                                        </div>
                                        <div class="mt-3 mt-sm-0">
                                            <label class="switch-primary mb-0">
                                                <input type="checkbox" class="switch switch-bootstrap status section-toggle-ajax" data-field="is_blogs" name="is_blogs" value="1" {{ $setting->is_blogs == 1 ? 'checked' : '' }}>
                                                <span class="switch-body"></span>
                                            </label>
                                        </div>
                                    </div>
                                    <hr class="my-4">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap">
                                        <span class="text-muted">{{ __('To add, edit or delete blog posts, visit the Manage Blogs page.') }}</span>
                                        <a href="{{ route('back.post.index') }}" class="btn btn-primary btn-sm mt-2 mt-sm-0"><i class="fas fa-edit mr-1"></i> {{ __('Manage Blogs') }}</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        </div>
                    </div>
                </div>
            </div>
		</div>
	</div>

</div>

</div>
<!-- End of Main Content -->



@endsection

@section('scripts')
    <script type="" src="{{asset('assets/back/js/select2.js')}}"></script>
    <script>
        $('#basic').select2({
			theme: "bootstrap"
		});

        $(document).on('change', '.section-toggle-ajax', function() {
            var $this = $(this);
            var field = $this.data('field');
            var status = $this.is(':checked') ? 1 : 0;
            
            $.ajax({
                url: '{{ route("back.setting.toggle.section") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    field: field,
                    status: status
                },
                success: function(response) {
                    $.notify({
                        icon: 'flaticon-alarm-1',
                        title: '{{ __("Success") }}',
                        message: response.message || '{{ __("Setting updated successfully.") }}',
                    },{
                        type: 'secondary',
                        placement: {
                            from: "bottom",
                            align: "right"
                        },
                        time: 1000,
                    });
                },
                error: function(xhr) {
                    $.notify({
                        icon: 'flaticon-error',
                        title: '{{ __("Error") }}',
                        message: '{{ __("Failed to update status.") }}',
                    },{
                        type: 'danger',
                        placement: {
                            from: "bottom",
                            align: "right"
                        },
                        time: 1000,
                    });
                }
            });
        });
    </script>
@endsection