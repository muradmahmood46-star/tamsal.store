@php
    $categoryLimit = \App\Models\Setting::first()->buyer_category_limit ?? 50;
    $categories = App\Models\Category::with(['subcategory.childcategory'])->whereStatus(1)->orderby('serial','asc')->take($categoryLimit)->get();
@endphp

<div class="mobile-categories-drawer">
    <ul class="mobile-cat-nav-list list-unstyled mb-0">
        @foreach ($categories as $getcategory)
            <li class="mobile-cat-nav-item {{ $getcategory->subcategory->count() > 0 ? 'has-sub' : '' }}">
                <div class="mobile-cat-nav-row d-flex align-items-center justify-content-between">
                    <a class="mobile-cat-nav-link flex-grow-1" href="{{ route('front.catalog', ['category' => $getcategory->slug]) }}">
                        @if(!empty($getcategory->photo))
                            <img src="{{ url('/core/public/storage/images/' . $getcategory->photo) }}" class="mobile-cat-nav-img" alt="{{ $getcategory->name }}">
                        @else
                            <span class="mobile-cat-nav-icon"><i class="icon-grid"></i></span>
                        @endif
                        <span class="mobile-cat-nav-name">{{ $getcategory->name }}</span>
                    </a>
                    @if ($getcategory->subcategory->count() > 0)
                        <button type="button" class="mobile-sub-toggle" aria-label="Toggle subcategories">
                            <i class="icon-chevron-down"></i>
                        </button>
                    @endif
                </div>

                @if ($getcategory->subcategory->count() > 0)
                    <ul class="mobile-subcat-nav-list list-unstyled" style="display: none;">
                        @foreach ($getcategory->subcategory as $getsubcategory)
                            <li class="mobile-subcat-nav-item">
                                <div class="mobile-subcat-nav-row d-flex align-items-center justify-content-between">
                                    <a class="mobile-subcat-nav-link flex-grow-1" href="{{ route('front.catalog', ['subcategory' => $getsubcategory->slug]) }}">
                                        <i class="icon-chevron-right mr-1"></i> {{ $getsubcategory->name }}
                                    </a>
                                    @if ($getsubcategory->childcategory->count() > 0)
                                        <button type="button" class="mobile-child-toggle" aria-label="Toggle child categories">
                                            <i class="icon-chevron-down"></i>
                                        </button>
                                    @endif
                                </div>

                                @if ($getsubcategory->childcategory->count() > 0)
                                    <ul class="mobile-childcat-nav-list list-unstyled" style="display: none;">
                                        @foreach ($getsubcategory->childcategory as $getchildcategory)
                                            <li class="mobile-childcat-nav-item">
                                                <a class="mobile-childcat-nav-link" href="{{ route('front.catalog', ['childcategory' => $getchildcategory->slug]) }}">
                                                    {{ $getchildcategory->name }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </li>
        @endforeach

        <li class="mobile-cat-nav-item view-all-cat-item mt-2">
            <a class="mobile-cat-nav-link text-primary font-weight-bold" href="{{ route('front.catalog') }}">
                <span class="mobile-cat-nav-icon bg-primary text-white"><i class="icon-layers"></i></span>
                <span class="mobile-cat-nav-name">{{ __('View All Categories →') }}</span>
            </a>
        </li>
    </ul>
</div>
