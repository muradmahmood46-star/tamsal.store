@extends('master.back')

@section('content')

<div class="container-fluid">

<!-- Page Heading -->
<div class="card mb-4">
    <div class="card-body">
        <div class="d-sm-flex align-items-center justify-content-between">
            <h3 class="mb-0 bc-title"><b>{{ __('Update Product') }}</b> </h3>
            <a class="btn btn-primary   btn-sm" href="{{route('back.item.index')}}"><i class="fas fa-chevron-left"></i> {{ __('Back') }}</a>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-lg-12">
            @include('alerts.alerts')
    </div>
</div>
<!-- Nested Row within Card Body -->

<form class="admin-form" action="{{ route('back.item.update',['item' => $item->id]) }}" method="POST"
    enctype="multipart/form-data">

    @csrf

    @method('PUT')
    <div class="row">

        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">{{ __('Name') }} *</label>
                        <input type="text" name="name" class="form-control item-name"
                            id="name"
                            placeholder="{{ __('Enter Name') }}"
                            value="{{ $item->name }}" >
                    </div>

                    <div class="form-group">
                        <label for="slug">{{ __('Slug') }} *</label>
                        <input type="text" name="slug" class="form-control"
                            id="slug"
                            placeholder="{{ __('Enter Slug') }}"
                            value="{{ $item->slug }}" >
                    </div>

                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="form-group pb-0  mb-0">
                        <label class="d-block">{{ __('Featured Image') }} *</label>
                    </div>
                    <div class="form-group pb-0 pt-0 mt-0 mb-0">
                    <img class="admin-img lg" src="{{ $item->photo ? url('/core/public/storage/images/'.$item->photo) : url('/core/public/storage/images/placeholder.png') }}" >
                    </div>
                    <div class="form-group position-relative ">
                        <label class="file">
                            <input type="file"  accept="image/*"   class="upload-photo" name="photo"
                                id="file"  aria-label="File browser example">
                            <span
                                class="file-custom text-left">{{ __('Upload Image...') }}</span>
                        </label>
                        <br>
                        <span class="mt-1 text-info">{{ __('Image Size Should Be 800 x 800. or square size') }}</span>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="form-group pb-0  mb-0">
                        <label>{{ __('Gallery Images') }} </label>
                    </div>
                    <div class="form-group pb-0 pt-0 mt-0 mb-0">
                        <div id="gallery-images">
                            <div class="d-block gallery_image_view">

                                @forelse($item->galleries as $gallery)
                                    <div class="single-g-item d-inline-block m-2">
                                            <span data-toggle="modal"
                                            data-target="#confirm-delete" href="javascript:;"
                                            data-href="{{ route('back.item.gallery.delete',$gallery->id) }}" class="remove-gallery-img">
                                                <i class="fas fa-trash"></i>
                                            </span>
                                            <a class="popup-link" href="{{ $gallery->photo ? url('/core/public/storage/images/'.$gallery->photo) : url('/core/public/storage/images/placeholder.png') }}">
                                                <img class="admin-gallery-img" src="{{ $gallery->photo ? url('/core/public/storage/images/'.$gallery->photo) : url('/core/public/storage/images/placeholder.png') }}"
                                                    alt="No Image Found">
                                            </a>
                                    </div>
                                @empty
                                    <h6><b>{{ __('No Images Added') }}</b></h6>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <div class="form-group position-relative ">
                        <label class="file">
                            <input type="file"  accept="image/*"   name="galleries[]" id="gallery_file"
                                    aria-label="File browser example" accept="image/*" multiple>
                            <span
                                class="file-custom text-left">{{ __('Upload Image...') }}</span>
                        </label>
                        <br>
                        <span class="mt-1 text-info">{{ __('Image Size Should Be 800 x 800. or square size') }}</span>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label for="sort_details">{{ __('Short Description') }} *</label>
                        <textarea name="sort_details" id="sort_details"
                            class="form-control"
                            placeholder="{{ __('Short Description') }}"
                            >{{$item->sort_details}}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="details">{{ __('Description') }} *</label>
                        <textarea name="details" id="details"
                            class="form-control"
                            rows="6"
                            placeholder="{{ __('Enter Description') }}"
                            >{{$item->details}}</textarea>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label for="tags">{{ __('Product Tags') }}
                            </label>
                        <input type="text" name="tags" class="tags"
                            id="tags"
                            placeholder="{{ __('Tags') }}"
                            value="{{$item->tags}}">
                    </div>
                    <div class="form-group">
                        <label class="switch-primary">
                            <input type="checkbox" class="switch switch-bootstrap status radio-check" name="is_specification" value="1" {{$item->is_specification ==1 ? 'checked' : ''}}>
                            <span class="switch-body"></span>
                            <span class="switch-text">{{ __('Specifications') }}</span>
                        </label>
                    </div>

                    <div id="specifications-section" class="{{ $item->is_specification == 0 ? 'd-none' : '' }}">
                        @if(!empty($specification_name))
                        @foreach(array_combine($specification_name,$specification_description) as  $name => $description)
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <div class="form-group">
                                    <input type="text" class="form-control"
                                        name="specification_name[]"
                                        placeholder="{{ __('Specification Name') }}" value="{{$name}}">
                                    </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="form-group">
                                    <input type="text" class="form-control"
                                        name="specification_description[]"
                                        placeholder="{{ __('Specification description') }}" value="{{$description}}">
                                    </div>
                            </div>
                            <div class="flex-btn">
                                @if($loop->first)
                                <button type="button" class="btn btn-success add-specification" data-text="{{ __('Specification Name') }}" data-text1="{{ __('Specification Description') }}"> <i class="fa fa-plus"></i> </button>
                                @else
                                <button type="button" class="btn btn-danger remove-spcification" data-text="{{ __('Specification Name') }}" data-text1="{{ __('Specification Description') }}"> <i class="fa fa-minus"></i> </button>
                                @endif
                            </div>
                        </div>

                        @endforeach
                        @else
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <div class="form-group">
                                    <input type="text" class="form-control"
                                        name="specification_name[]"
                                        placeholder="{{ __('Specification Name') }}" value="">
                                    </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="form-group">
                                    <input type="text" class="form-control"
                                        name="specification_description[]"
                                        placeholder="{{ __('Specification description') }}" value="">
                                    </div>
                            </div>
                            <div class="flex-btn">
                                <button type="button" class="btn btn-success add-specification" data-text="{{ __('Specification Name') }}" data-text1="{{ __('Specification Description') }}"> <i class="fa fa-plus"></i> </button>
                            </div>
                        </div>
                        @endif

                    </div>
                </div>
            </div>
            @php
                $existingVariants = [];
                if (!empty($item->item_variants)) {
                    $existingVariants = json_decode($item->item_variants, true) ?? [];
                }
                $hasVariants = !empty($existingVariants);
            @endphp

            <!-- Product Variants (Size, Color & Stock) -->
            <div class="card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 font-weight-bold text-dark"><i class="fas fa-layer-group text-primary mr-2"></i>{{ __('Product Variants (Size & Color with Stock)') }}</h5>
                </div>
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label class="switch-primary">
                            <input type="checkbox" class="switch switch-bootstrap status radio-check" id="is_variant_toggle" name="is_variant" value="1" {{ $hasVariants ? 'checked' : '' }} onchange="toggleVariantSection(this)">
                            <span class="switch-body"></span>
                            <span class="switch-text font-weight-bold text-dark">{{ __('Enable Size & Color Variants for this product') }}</span>
                        </label>
                        <p class="text-muted mb-0 small">{{ __('Enable this if this product has multiple colors or sizes with individual stock quantities (e.g. Black - M - 5 pcs, Black - L - 3 pcs).') }}</p>
                    </div>

                    <div id="variants_section_wrapper" style="display: {{ $hasVariants ? 'block' : 'none' }};">
                        {{-- Quick Combination Generator --}}
                        <div class="p-3 mb-3 border rounded shadow-sm" style="background-color: #f8fafc; border: 1px dashed #0284c7 !important;">
                            <h6 class="font-weight-bold text-primary mb-3"><i class="fas fa-magic text-warning mr-1"></i> {{ __('Quick Combinations Generator') }}</h6>
                            <div class="row align-items-end">
                                <div class="col-lg-5 col-md-5 col-sm-12 mb-2">
                                    <label class="font-weight-bold small text-dark mb-1">{{ __('Colors (comma separated)') }}</label>
                                    <input type="text" id="gen_colors" class="form-control form-control-sm" placeholder="{{ __('e.g. Black, White, Blue') }}">
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12 mb-2">
                                    <label class="font-weight-bold small text-dark mb-1">{{ __('Sizes (comma separated)') }}</label>
                                    <input type="text" id="gen_sizes" class="form-control form-control-sm" placeholder="{{ __('e.g. S, M, L, XL') }}">
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-12 mb-2">
                                    <button type="button" class="btn btn-primary btn-sm w-100 font-weight-bold text-nowrap d-flex align-items-center justify-content-center" style="height: 31px;" onclick="generateVariantRows()">
                                        <i class="fas fa-bolt mr-1"></i> {{ __('Generate') }}
                                    </button>
                                </div>
                            </div>
                            <small class="text-muted d-block mt-1">{{ __('Tip: Enter Colors and/or Sizes above and click Generate to auto-create variant combinations.') }}</small>
                        </div>

                        {{-- Variants Table --}}
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm text-center" id="variants_table">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="min-width: 130px;">{{ __('Color') }}</th>
                                        <th style="min-width: 130px;">{{ __('Size') }}</th>
                                        <th style="min-width: 120px;">{{ __('Stock (pcs)') }} *</th>
                                        <th style="min-width: 120px;">{{ __('Extra Price') }} ({{ PriceHelper::adminCurrency() }})</th>
                                        <th style="width: 50px;">{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody id="variants_tbody">
                                    {{-- Dynamically populated rows --}}
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <button type="button" class="btn btn-outline-success btn-sm font-weight-bold" onclick="addSingleVariantRow('', '', 0, 0)">
                                <i class="fas fa-plus"></i> {{ __('+ Add Custom Variant Row') }}
                            </button>
                            <div class="text-right">
                                <span class="font-weight-bold" style="font-size: 13.5px;">{{ __('Total Variant Stock:') }} <span id="total_variant_stock_display" class="text-primary font-weight-bold">0</span> {{ __('pcs') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Product Variants (Size, Color & Stock) Card End -->

            <!-- Rating & Review Management (Demo / Initial Rating) -->
            <div class="card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 font-weight-bold text-dark"><i class="fas fa-star text-warning mr-2"></i>{{ __('Rating & Reviews Management') }}</h5>
                    <span class="badge badge-warning text-dark font-weight-bold">{{ __('Admin Managed') }}</span>
                </div>
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label class="switch-primary">
                            <input type="checkbox" class="switch switch-bootstrap status radio-check" id="is_custom_rating_toggle" name="is_custom_rating" value="1" {{ $item->is_custom_rating == 1 ? 'checked' : '' }} onchange="toggleRatingSection(this)">
                            <span class="switch-body"></span>
                            <span class="switch-text font-weight-bold text-dark" style="font-size: 14.5px;">{{ __('Enable Custom / Demo Rating for this product') }}</span>
                        </label>
                        <p class="text-muted mb-0" style="font-size: 13px;">{{ __('Enable this to set custom star rating and review count for this product. Great for newly launched products and marketing initial trust.') }}</p>
                    </div>

                    <div id="custom_rating_wrapper" style="display: {{ $item->is_custom_rating == 1 ? 'block' : 'none' }};">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold text-dark mb-1">{{ __('Star Rating (1.0 to 5.0)') }}</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-warning text-dark"><i class="fas fa-star"></i></span>
                                    </div>
                                    <input type="number" step="0.1" min="1" max="5" id="custom_rating_input" name="custom_rating" class="form-control" value="{{ $item->custom_rating ?? 5.0 }}" placeholder="e.g. 4.8">
                                </div>
                                <div class="mt-2">
                                    <span class="small font-weight-bold text-muted mr-1">{{ __('Quick Presets:') }}</span>
                                    <button type="button" class="btn btn-outline-warning btn-xs py-0 px-2 text-dark font-weight-bold" onclick="setRatingPreset(5.0)">5.0 ★</button>
                                    <button type="button" class="btn btn-outline-warning btn-xs py-0 px-2 text-dark font-weight-bold" onclick="setRatingPreset(4.9)">4.9 ★</button>
                                    <button type="button" class="btn btn-outline-warning btn-xs py-0 px-2 text-dark font-weight-bold" onclick="setRatingPreset(4.8)">4.8 ★</button>
                                    <button type="button" class="btn btn-outline-warning btn-xs py-0 px-2 text-dark font-weight-bold" onclick="setRatingPreset(4.5)">4.5 ★</button>
                                    <button type="button" class="btn btn-outline-warning btn-xs py-0 px-2 text-dark font-weight-bold" onclick="setRatingPreset(4.0)">4.0 ★</button>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold text-dark mb-1">{{ __('Total Review Count') }}</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-info text-white"><i class="fas fa-comments"></i></span>
                                    </div>
                                    <input type="number" min="0" id="custom_rating_count_input" name="custom_rating_count" class="form-control" value="{{ $item->custom_rating_count ?? 25 }}" placeholder="e.g. 48">
                                </div>
                                <div class="mt-2">
                                    <span class="small font-weight-bold text-muted mr-1">{{ __('Quick Presets:') }}</span>
                                    <button type="button" class="btn btn-outline-info btn-xs py-0 px-2 font-weight-bold" onclick="setReviewCountPreset(12)">12</button>
                                    <button type="button" class="btn btn-outline-info btn-xs py-0 px-2 font-weight-bold" onclick="setReviewCountPreset(25)">25</button>
                                    <button type="button" class="btn btn-outline-info btn-xs py-0 px-2 font-weight-bold" onclick="setReviewCountPreset(48)">48</button>
                                    <button type="button" class="btn btn-outline-info btn-xs py-0 px-2 font-weight-bold" onclick="setReviewCountPreset(96)">96</button>
                                    <button type="button" class="btn btn-outline-info btn-xs py-0 px-2 font-weight-bold" onclick="setReviewCountPreset(150)">150+</button>
                                </div>
                            </div>
                        </div>

                        {{-- Existing Product Reviews Table --}}
                        @if ($item->reviews->count() > 0)
                            <div class="mb-3 border rounded p-2" style="background: #ffffff;">
                                <h6 class="font-weight-bold text-dark mb-2"><i class="fas fa-list-alt text-primary mr-1"></i> {{ __('Current Reviews on this Product') }} ({{ $item->reviews->count() }})</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm text-center mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>{{ __('Reviewer') }}</th>
                                                <th>{{ __('Rating') }}</th>
                                                <th>{{ __('Subject') }}</th>
                                                <th>{{ __('Status') }}</th>
                                                <th>{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($item->reviews as $rev)
                                                <tr>
                                                    <td>
                                                        <span class="font-weight-bold">{{ $rev->reviewer_name }}</span>
                                                        @if($rev->is_admin_added == 1)
                                                            <span class="badge badge-info ml-1" style="font-size: 10px;">{{ __('Admin Demo') }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-warning font-weight-bold">{{ $rev->rating }} ★</td>
                                                    <td><small>{{ Str::limit($rev->subject, 30) }}</small></td>
                                                    <td>
                                                        @if($rev->status == 1)
                                                            <span class="badge badge-success">{{ __('Live') }}</span>
                                                        @else
                                                            <span class="badge badge-warning">{{ __('Pending') }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('back.review.destroy', $rev->id) }}" class="btn btn-danger btn-xs py-0 px-1" title="{{ __('Delete Review') }}" onclick="return confirm('{{ __('Are you sure you want to delete this review?') }}')">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                        {{-- Custom Demo Reviews Creator --}}
                        <div class="p-3 mt-2 border rounded" style="background-color: #fcfcfd; border: 1px dashed #d97706 !important;">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="font-weight-bold text-dark mb-0"><i class="fas fa-user-edit text-warning mr-1"></i> {{ __('Add Demo Customer Reviews (Optional)') }}</h6>
                                <button type="button" class="btn btn-success btn-xs font-weight-bold" onclick="addDemoReviewRow()">
                                    <i class="fas fa-plus"></i> {{ __('+ Add Review') }}
                                </button>
                            </div>
                            <p class="text-muted small mb-2">{{ __('You can add realistic customer testimonials that will appear on the product page.') }}</p>
                            
                            <div id="demo_reviews_container">
                                {{-- Dynamically appended demo review cards --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Return Policy Management Card -->
            <div class="card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 font-weight-bold text-dark"><i class="fas fa-undo-alt text-primary mr-2"></i>{{ __('Return Policy') }}</h5>
                    <span class="badge badge-primary font-weight-bold">{{ __('Easy Return') }}</span>
                </div>
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label class="switch-primary">
                            <input type="checkbox" class="switch switch-bootstrap status radio-check" id="is_returnable_toggle" name="is_returnable" value="1" {{ $item->is_returnable == 1 ? 'checked' : '' }} onchange="toggleReturnPolicySection(this)">
                            <span class="switch-body"></span>
                            <span class="switch-text font-weight-bold text-dark" style="font-size: 14.5px;">{{ __('Enable Return Policy for this product') }}</span>
                        </label>
                        <p class="text-muted mb-0" style="font-size: 13px;">{{ __('Enable this if you offer easy return / replacement guarantee for this product.') }}</p>
                    </div>

                    <div id="return_policy_wrapper" style="display: {{ $item->is_returnable == 1 ? 'block' : 'none' }};">
                        <div class="form-group mb-0">
                            <label class="font-weight-bold text-dark mb-1">{{ __('Return Window (Number of Days)') }} *</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-primary text-white"><i class="fas fa-calendar-alt"></i></span>
                                </div>
                                <input type="number" min="1" max="365" id="return_days_input" name="return_days" class="form-control" value="{{ $item->return_days ?? 14 }}" placeholder="e.g. 14">
                                <div class="input-group-append">
                                    <span class="input-group-text font-weight-bold">{{ __('Days Easy Return') }}</span>
                                </div>
                            </div>
                            <div class="mt-2">
                                <span class="small font-weight-bold text-muted mr-1">{{ __('Quick Presets:') }}</span>
                                <button type="button" class="btn btn-outline-primary btn-xs py-0 px-2 font-weight-bold" onclick="setReturnDaysPreset(1)">1 Day</button>
                                <button type="button" class="btn btn-outline-primary btn-xs py-0 px-2 font-weight-bold" onclick="setReturnDaysPreset(3)">3 Days</button>
                                <button type="button" class="btn btn-outline-primary btn-xs py-0 px-2 font-weight-bold" onclick="setReturnDaysPreset(5)">5 Days</button>
                                <button type="button" class="btn btn-outline-primary btn-xs py-0 px-2 font-weight-bold" onclick="setReturnDaysPreset(7)">7 Days</button>
                                <button type="button" class="btn btn-outline-primary btn-xs py-0 px-2 font-weight-bold" onclick="setReturnDaysPreset(14)">14 Days</button>
                                <button type="button" class="btn btn-outline-primary btn-xs py-0 px-2 font-weight-bold" onclick="setReturnDaysPreset(30)">30 Days</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label for="meta_keywords">{{ __('Meta Keywords') }}
                            </label>
                        <input type="text" name="meta_keywords" class="tags"
                            id="meta_keywords"
                            placeholder="{{ __('Enter Meta Keywords') }}"
                            value="{{ $item->meta_keywords }}">
                    </div>
                    <div class="form-group">
                        <label
                            for="meta_description">{{ __('Meta Description') }}
                            </label>
                        <textarea name="meta_description" id="meta_description"
                            class="form-control" rows="5"
                            placeholder="{{ __('Enter Meta Description') }}">{{ $item->meta_description }}</textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <input type="hidden" class="check_button" name="is_button" value="0">
                    <button type="submit" class="btn btn-secondary mr-2">{{ __('Update') }}</button>
                    <a class="btn btn-success" href="{{ route('back.attribute.index',$item->id) }}">{{ __('Manage Attributes') }}</a>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label for="discount_price">{{ __('Current Price') }}
                            *</label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span
                                    class="input-group-text">{{ $curr->sign }}</span>
                            </div>
                            <input type="text" id="discount_price"
                                name="discount_price" class="form-control"
                                placeholder="{{ __('Enter Current Price') }}"
                                min="1" step="0.1"
                                value="{{ round($item->discount_price * $curr->value,2) }}" >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="previous_price">{{ __('Previous Price') }}
                            </label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span
                                    class="input-group-text">{{ $curr->sign }}</span>
                            </div>
                            <input type="text" id="previous_price"
                                name="previous_price" class="form-control"
                                placeholder="{{ __('Enter Previous Price') }}"
                                min="1" step="0.1"
                                value="{{ round($item->previous_price*$curr->value ,2)}}" >
                        </div>
                    </div>

                    <!-- Advance Payment Offer UI (Frontend Only) -->
                    <div class="form-group">
                        <label>{{ __('Advance Payment Offer Less') }}</label>
                        <div class="mb-2">
                            <div class="custom-control custom-radio mb-2">
                                <input type="radio" id="adv_type_percentage_edit" name="advance_payment_type" class="custom-control-input" value="percentage" {{ $item->advance_payment_type == 'percentage' || empty($item->advance_payment_type) ? 'checked' : '' }} onchange="document.getElementById('adv_payment_suffix_edit').innerText = '%'">
                                <label class="custom-control-label" style="white-space: normal; line-height: 1.4;" for="adv_type_percentage_edit">{{ __('Percentage (%)') }}</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="adv_type_fixed_edit" name="advance_payment_type" class="custom-control-input" value="fixed" {{ $item->advance_payment_type == 'fixed' ? 'checked' : '' }} onchange="document.getElementById('adv_payment_suffix_edit').innerText = '{{ $curr->sign }}'">
                                <label class="custom-control-label" style="white-space: normal; line-height: 1.4;" for="adv_type_fixed_edit">{{ __('Fixed Price (PKR)') }}</label>
                            </div>
                        </div>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="adv_payment_suffix_edit">{{ $item->advance_payment_type == 'fixed' ? $curr->sign : '%' }}</span>
                            </div>
                            <input type="number" id="advance_payment_amount" name="advance_payment_amount" class="form-control" placeholder="{{ __('Enter amount e.g. 100, 200, 300') }}" min="0" step="0.1" value="{{ ($item->advance_payment_amount + 0) }}">
                        </div>
                    </div>

                    <!-- Delivery Fee Setting -->
                    <div class="form-group border-top pt-3">
                        <label class="font-weight-bold">{{ __('Delivery Fees') }}</label>
                        <div class="mb-2">
                            <div class="custom-control custom-radio mb-2">
                                <input type="radio" id="delivery_type_amount_edit" name="is_free_delivery" class="custom-control-input" value="0" {{ $item->is_free_delivery == 1 ? '' : 'checked' }} onchange="document.getElementById('delivery_fee_input_group_edit').style.display = 'flex';">
                                <label class="custom-control-label" style="white-space: normal; line-height: 1.4;" for="delivery_type_amount_edit">{{ __('Delivery Charges (PKR)') }}</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="delivery_type_free_edit" name="is_free_delivery" class="custom-control-input" value="1" {{ $item->is_free_delivery == 1 ? 'checked' : '' }} onchange="document.getElementById('delivery_fee_input_group_edit').style.display = 'none';">
                                <label class="custom-control-label text-success font-weight-bold" style="white-space: normal; line-height: 1.4;" for="delivery_type_free_edit">{{ __('Free Delivery') }}</label>
                            </div>
                        </div>
                        <div class="input-group mb-2" id="delivery_fee_input_group_edit" style="{{ $item->is_free_delivery == 1 ? 'display: none;' : '' }}">
                            <div class="input-group-prepend">
                                <span class="input-group-text">{{ $curr->sign }}</span>
                            </div>
                            <input type="number" id="delivery_fee_edit" name="delivery_fee" class="form-control" placeholder="{{ __('Enter delivery fee e.g. 150, 200, 300') }}" min="0" step="1" value="{{ ($item->delivery_fee + 0) }}">
                        </div>
                    </div>

                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label for="category_id">{{ __('Select Category') }} *</label>
                        <select name="category_id" id="category_id" data-href="{{route('back.get.subcategory')}}" class="form-control" >
                            @foreach(DB::table('categories')->whereStatus(1)->get() as $cat)
                            <option value="{{ $cat->id }}" {{ $cat->id == $item->category_id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="subcategory_id">{{ __('Select Sub Category') }} </label>
                        <select name="subcategory_id" id="subcategory_id" class="form-control" data-href="{{route('back.get.childcategory')}}">
                            <option value="">{{__('Select one')}}</option>
                            @foreach(DB::table('subcategories')->where('category_id',$item->category_id)->whereStatus(1)->get() as $subcat)
                            <option value="{{ $subcat->id }}" {{ $subcat->id == $item->subcategory_id ? 'selected' : '' }}>{{ $subcat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="childcategory_id">{{ __('Select Child Category') }} </label>
                        <select name="childcategory_id" id="childcategory_id" class="form-control">
                            <option value="">{{__('Select one')}}</option>
                            @foreach(DB::table('chield_categories')->where('category_id',$item->category_id)->whereStatus(1)->get() as $chieldcategory)
                            <option value="{{ $chieldcategory->id }}" {{ $chieldcategory->id == $item->childcategory_id ? 'selected' : '' }}>{{ $chieldcategory->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="brand_id">{{ __('Select Brand') }} </label>
                        <select name="brand_id" id="brand_id" class="form-control" >
                            <option value="" selected>{{__('Select Brand')}}</option>
                            @foreach(DB::table('brands')->whereStatus(1)->where(function($q){ $q->whereNull('vendor_id')->orWhere('vendor_id', 0); })->get() as $brand)
                            <option value="{{ $brand->id }}" {{$brand->id == $item->brand_id ? 'selected' : ''}} >{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label for="stock">{{ __('Total in stock') }}
                            *</label>
                        <div class="input-group mb-3">
                            <input type="number" id="stock"
                                name="stock" class="form-control"
                                placeholder="{{ __('Total in stock') }}" value="{{$item->stock}}" >
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="tax_id">{{ __('Select Tax') }} *</label>
                        <select name="tax_id" id="tax_id" class="form-control">
                            <option value="">{{__('Select One')}}</option>
                            @foreach(DB::table('taxes')->whereStatus(1)->get() as $tax)
                            <option value="{{ $tax->id }}" {{$item->tax_id == $tax->id ? 'selected' : ''}} >{{ $tax->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="sku">{{ __('SKU') }} *</label>
                        <input type="text" name="sku" class="form-control"
                            id="sku" placeholder="{{ __('Enter SKU') }}"
                            value="{{$item->sku}}" >
                    </div>
                    <div class="form-group">
                        <label for="video">{{ __('Video Link') }} </label>
                        <input type="text" name="video" class="form-control"
                            id="video" placeholder="{{ __('Enter Video Link') }}"
                            value="{{$item->video}}" >
                    </div>
                    <div class="form-group">
                        <label for="estimated_profit" class="font-weight-bold text-success">
                            <i class="fas fa-coins mr-1"></i> {{ __('Estimated Profit') }} <span class="badge badge-danger text-white ml-1" style="font-size: 11px;">{{ __('Only for Admin & Vendor Use') }}</span>
                        </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light font-weight-bold text-success">{{ $curr->sign ?? 'PKR' }}</span>
                            </div>
                            <input type="number" step="0.01" min="0" name="estimated_profit" class="form-control"
                                id="estimated_profit" placeholder="{{ __('Enter Estimated Profit per Unit (e.g. 150.00)') }}"
                                value="{{ old('estimated_profit', $item->estimated_profit ?? 0) }}">
                        </div>
                        <small class="form-text text-muted font-italic">
                            <i class="fas fa-shield-alt text-primary mr-1"></i> {{ __('Only for internal admin/vendor calculations (never shown to buyers/users on product page). When an order with this product is accepted, this profit is added to total earnings.') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>

    </div>
</form>
</div>
{{-- DELETE MODAL --}}

<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="confirm-deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">

		<!-- Modal Header -->
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">{{ __('Confirm Delete?') }}</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
		</div>

		<!-- Modal Body -->
        <div class="modal-body">
			{{ __('You are going to delete this image from gallery.') }} {{ __('Do you want to delete it?') }}
		</div>

		<!-- Modal footer -->
        <div class="modal-footer">
			<button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
			<form action="" class="d-inline btn-ok" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">{{ __('Delete') }}</button>
			</form>
		</div>

      </div>
    </div>
  </div>

{{-- DELETE MODAL ENDS --}}

<script>
    function toggleVariantSection(checkbox) {
        var wrapper = document.getElementById('variants_section_wrapper');
        if (checkbox.checked) {
            wrapper.style.display = 'block';
            if (document.querySelectorAll('#variants_tbody tr').length === 0) {
                addSingleVariantRow('', '', 0, 0);
            }
            calculateTotalVariantStock();
        } else {
            wrapper.style.display = 'none';
        }
    }

    function addSingleVariantRow(color, size, stock, price) {
        var tbody = document.getElementById('variants_tbody');
        var row = document.createElement('tr');
        row.innerHTML = `
            <td>
                <input type="text" name="variant_color[]" class="form-control form-control-sm variant-color-input" placeholder="e.g. Black" value="${escapeHtml(color)}">
            </td>
            <td>
                <input type="text" name="variant_size[]" class="form-control form-control-sm variant-size-input" placeholder="e.g. M" value="${escapeHtml(size)}">
            </td>
            <td>
                <input type="number" name="variant_stock[]" class="form-control form-control-sm variant-stock-input text-center font-weight-bold" min="0" step="1" placeholder="0" value="${stock}" oninput="calculateTotalVariantStock()" required>
            </td>
            <td>
                <input type="number" name="variant_price[]" class="form-control form-control-sm text-center" min="0" step="0.1" placeholder="0" value="${price}">
            </td>
            <td>
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeVariantRow(this)" title="Delete"><i class="fas fa-trash"></i></button>
            </td>
        `;
        tbody.appendChild(row);
        calculateTotalVariantStock();
    }

    function removeVariantRow(btn) {
        var row = btn.closest('tr');
        row.remove();
        calculateTotalVariantStock();
    }

    function generateVariantRows() {
        var colorsInput = document.getElementById('gen_colors').value.trim();
        var sizesInput = document.getElementById('gen_sizes').value.trim();

        var colors = colorsInput ? colorsInput.split(',').map(function(s) { return s.trim(); }).filter(function(s) { return s.length > 0; }) : [];
        var sizes = sizesInput ? sizesInput.split(',').map(function(s) { return s.trim(); }).filter(function(s) { return s.length > 0; }) : [];

        if (colors.length === 0 && sizes.length === 0) {
            alert('Please enter at least one Color or Size.');
            return;
        }

        var tbody = document.getElementById('variants_tbody');
        tbody.innerHTML = '';

        if (colors.length > 0 && sizes.length > 0) {
            colors.forEach(function(color) {
                sizes.forEach(function(size) {
                    addSingleVariantRow(color, size, 0, 0);
                });
            });
        } else if (colors.length > 0) {
            colors.forEach(function(color) {
                addSingleVariantRow(color, '', 0, 0);
            });
        } else if (sizes.length > 0) {
            sizes.forEach(function(size) {
                addSingleVariantRow('', size, 0, 0);
            });
        }

        calculateTotalVariantStock();
    }

    function calculateTotalVariantStock() {
        var isVariantChecked = document.getElementById('is_variant_toggle').checked;
        if (!isVariantChecked) return;

        var stockInputs = document.querySelectorAll('.variant-stock-input');
        var total = 0;
        stockInputs.forEach(function(inp) {
            var val = parseInt(inp.value);
            if (!isNaN(val) && val > 0) {
                total += val;
            }
        });

        var displayEl = document.getElementById('total_variant_stock_display');
        if (displayEl) {
            displayEl.innerText = total;
        }

        var mainStockInput = document.getElementById('stock');
        if (mainStockInput && stockInputs.length > 0) {
            mainStockInput.value = total;
        }
    }

    function escapeHtml(text) {
        if (!text) return '';
        return String(text).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    }

    // Rating Management Functions
    function toggleRatingSection(el) {
        if (el.checked) {
            $('#custom_rating_wrapper').slideDown();
        } else {
            $('#custom_rating_wrapper').slideUp();
        }
    }

    function setRatingPreset(val) {
        $('#custom_rating_input').val(val);
    }

    function setReviewCountPreset(val) {
        $('#custom_rating_count_input').val(val);
    }

    // Return Policy Functions
    function toggleReturnPolicySection(el) {
        if (el.checked) {
            $('#return_policy_wrapper').slideDown();
        } else {
            $('#return_policy_wrapper').slideUp();
        }
    }

    function setReturnDaysPreset(days) {
        $('#return_days_input').val(days);
    }

    let demoReviewIndex = 0;
    function addDemoReviewRow(name = '', rating = 5, subject = '', review = '') {
        demoReviewIndex++;
        const html = `
            <div class="card border mb-2 demo-review-card shadow-sm" id="demo_review_card_${demoReviewIndex}" style="background: #ffffff;">
                <div class="card-body p-2">
                    <div class="row align-items-center mb-2">
                        <div class="col-md-5 mb-1">
                            <label class="small font-weight-bold text-dark mb-0">{{ __("Customer / Reviewer Name") }} *</label>
                            <input type="text" name="demo_reviewer_name[]" class="form-control form-control-sm" placeholder="e.g. Ahmed Khan" value="${escapeHtml(name)}" required>
                        </div>
                        <div class="col-md-3 mb-1">
                            <label class="small font-weight-bold text-dark mb-0">{{ __("Rating Stars") }}</label>
                            <select name="demo_rating[]" class="form-control form-control-sm">
                                <option value="5" ${rating == 5 ? 'selected' : ''}>⭐⭐⭐⭐⭐ (5 Stars)</option>
                                <option value="4" ${rating == 4 ? 'selected' : ''}>⭐⭐⭐⭐ (4 Stars)</option>
                                <option value="3" ${rating == 3 ? 'selected' : ''}>⭐⭐⭐ (3 Stars)</option>
                                <option value="2" ${rating == 2 ? 'selected' : ''}>⭐⭐ (2 Stars)</option>
                                <option value="1" ${rating == 1 ? 'selected' : ''}>⭐ (1 Star)</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-1">
                            <label class="small font-weight-bold text-dark mb-0">{{ __("Title / Headline") }}</label>
                            <input type="text" name="demo_subject[]" class="form-control form-control-sm" placeholder="e.g. Excellent Quality & Fast Delivery!" value="${escapeHtml(subject)}">
                        </div>
                    </div>
                    <div class="row align-items-center">
                        <div class="col-md-11 mb-1">
                            <label class="small font-weight-bold text-dark mb-0">{{ __("Review Comment") }} *</label>
                            <textarea name="demo_review[]" class="form-control form-control-sm" rows="2" placeholder="e.g. Ordered this and absolutely loved the fabric and quality! Will definitely buy again." required>${escapeHtml(review)}</textarea>
                        </div>
                        <div class="col-md-1 text-center mb-1">
                            <button type="button" class="btn btn-outline-danger btn-sm p-1 mt-3" title="{{ __('Remove') }}" onclick="$('#demo_review_card_${demoReviewIndex}').remove()">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        $('#demo_reviews_container').append(html);
    }

    document.addEventListener("DOMContentLoaded", function() {
        var initialVariants = {!! json_encode($existingVariants) !!};
        if (initialVariants && initialVariants.length > 0) {
            initialVariants.forEach(function(v) {
                addSingleVariantRow(v.color || '', v.size || '', v.stock !== undefined ? v.stock : 0, v.price !== undefined ? v.price : 0);
            });
        }
    });
</script>

@endsection
