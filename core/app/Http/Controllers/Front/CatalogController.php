<?php

namespace App\Http\Controllers\Front;

use Illuminate\{
    Http\Request,
};

use App\{
    Models\Item,
    Models\Category,
    Http\Controllers\Controller,
};
use App\Helpers\PriceHelper;
use App\Models\Attribute;
use App\Models\AttributeOption;
use App\Models\Brand;
use App\Models\ChieldCategory;
use App\Models\Setting;
use App\Models\Subcategory;
use Illuminate\Support\Facades\Session;

class CatalogController extends Controller
{
    public function __construct()
    {
        $this->middleware('localize');
    }

	public function index(Request $request)
	{

        // attribute search
        $attr_item_ids = [];
        if($request->attribute){
            $attrubutes_get = Attribute::where('name',$request->attribute)->get();
            foreach($attrubutes_get as $attr_item_id){
                $attr_item_ids[] = $attr_item_id->item_id;
            }
        }

        $option_attr_ids = [];

        if($request->option){
            $option_get = AttributeOption::whereIn('name',explode(',',$request->option))->get();
            foreach($option_get as $option_attr_id){
                $option_attr_ids[] = $option_attr_id->attribute_id;
            }
        }


        $option_wise_item_ids = [];
        foreach(Attribute::whereIn('id',$option_attr_ids)->get() as $attr_item_id){
            $option_wise_item_ids[] = $attr_item_id->item_id;
        }
        $setting = Setting::first();

        $sorting = $request->has('sorting') ?  ( !empty($request->sorting) ? $request->sorting : null ) : null;
        $new = $request->has('new') ?  ( !empty($request->new) ? 1 : null ) : null;
        $feature = $request->has('quick_filter') ?  ( !empty($request->quick_filter == 'feature') ? 1 : null ) : null;
        $top = $request->has('quick_filter') ?  ( !empty($request->quick_filter == 'top') ? 1 : null ) : null;
        $best = $request->has('quick_filter') ?  ( !empty($request->quick_filter == 'best') ? 1 : null ) : null;
        $new = $request->has('quick_filter') ?  ( !empty($request->quick_filter == 'new') ? 1 : null ) : null;
        $brand = $request->has('brand') ?  ( !empty($request->brand) ? Brand::whereSlug($request->brand)->firstOrFail() : null ) : null;
        $search = $request->has('search') ?  ( !empty($request->search) ? $request->search : null ) : null;
        $vendor = $request->has('vendor') ? ( !empty($request->vendor) ? $request->vendor : null ) : null;

        $selected_categories = [];
        $category = null;
        $category_ids = [];

        if ($request->filled('category')) {
            $catInput = $request->category;
            $catSlugs = is_array($catInput) ? $catInput : explode(',', (string)$catInput);
            $catSlugs = array_filter(array_map('trim', $catSlugs));
            if (!empty($catSlugs)) {
                $categoriesObj = Category::whereIn('slug', $catSlugs)->get();
                if ($categoriesObj->count() > 0) {
                    $category_ids = $categoriesObj->pluck('id')->toArray();
                    $selected_categories = $categoriesObj->pluck('slug')->toArray();
                    if (count($selected_categories) === 1) {
                        $category = $categoriesObj->first();
                    }
                }
            }
        }

        $selected_subcategories = [];
        $subcategory = null;
        $subcategory_ids = [];

        if ($request->filled('subcategory')) {
            $subInput = $request->subcategory;
            $subSlugs = is_array($subInput) ? $subInput : explode(',', (string)$subInput);
            $subSlugs = array_filter(array_map('trim', $subSlugs));
            if (!empty($subSlugs)) {
                $subcategoriesObj = Subcategory::whereIn('slug', $subSlugs)->get();
                if ($subcategoriesObj->count() > 0) {
                    $subcategory_ids = $subcategoriesObj->pluck('id')->toArray();
                    $selected_subcategories = $subcategoriesObj->pluck('slug')->toArray();
                    if (count($selected_subcategories) === 1) {
                        $subcategory = $subcategoriesObj->first();
                    }
                }
            }
        }

        $childcategory = $request->has('childcategory') ? ( !empty($request->childcategory) ? ChieldCategory::where('slug',$request->childcategory)->first() : null ) : null;
        $minPrice = $request->has('minPrice') ?  ( !empty($request->minPrice) ? PriceHelper::convertPrice($request->minPrice) : null ) : null;
        $maxPrice = $request->has('maxPrice') ?  ( !empty($request->maxPrice) ? PriceHelper::convertPrice($request->maxPrice) : null ) : null;
        $tag = $request->has('tag') ?  ( !empty($request->tag) ? trim($request->tag) : null ) : null;
        $items = Item::with('category')
        ->when(!empty($category_ids), function ($query) use ($category_ids) {
            return $query->whereIn('category_id', $category_ids);
        })
        ->when(!empty($subcategory_ids), function ($query) use ($subcategory_ids) {
            return $query->whereIn('subcategory_id', $subcategory_ids);
        })
        ->when($childcategory, function ($query, $childcategory) {
            return $query->where('childcategory_id', $childcategory->id);
        })

        ->when($feature, function ($query) {
            return $query->whereIsType('feature');
        })

        ->when($tag, function ($query, $tag) {
            return $query->where('tags', 'like', '%' . $tag . '%');
        })

        ->when($vendor !== null, function ($query) use ($vendor) {
            if ($vendor == 'admin' || $vendor == '0') {
                return $query->where(function($q) {
                    $q->whereNull('vendor_id')->orWhere('vendor_id', 0);
                });
            } else {
                return $query->where('vendor_id', $vendor);
            }
        })
      

        ->when($new, function ($query) {
            return $query->orderby('id','desc');
        })
        ->when($top, function ($query) {
            return $query->whereIsType('top');
        })
        ->when($best, function ($query) {
            return $query->whereIsType('best');
        })
        ->when($new, function ($query) {
            return $query->whereIsType('new');
        })

        ->when($brand, function ($query, $brand) {
            return $query->where('brand_id', $brand->id);
        })
        ->when($search, function ($query, $search) {
            return $query->whereStatus(1)->where('name', 'like', '%' . $search . '%')->orwhere('name', 'like', '%' . $search . '%');
        })
        ->when($minPrice, function($query, $minPrice) {
          return $query->where('discount_price', '>=', $minPrice);
        })

        ->when($maxPrice, function($query, $maxPrice) {
          return $query->where('discount_price', '<=', $maxPrice);
        })

        ->when($sorting, function($query, $sorting) {
            if($sorting == 'low_to_high'){
                return $query->orderby('discount_price','asc');
            }else{
                return $query->orderby('discount_price','desc');
            }
        }, function($query) {
            return $query->orderby('category_id', 'asc')->orderby('id', 'desc');
        })

        ->when($attr_item_ids, function($query, $attr_item_ids) {
          return $query->whereIn('id',$attr_item_ids);
        })
        ->when($option_wise_item_ids, function($query, $option_wise_item_ids) {
          return $query->whereIn('id',$option_wise_item_ids);
        })

        ->where('status',1)
        ->where(function ($query) {
            $query->where('approval_status', 'Approved')
                ->orWhereNull('approval_status');
        })
        ->where(function ($query) {
            $query->whereNull('is_hidden_by_block')
                ->orWhere('is_hidden_by_block', 0);
        })

        ->paginate($setting->view_product);

     
        $attrubutes_check =[];
       
        $options = AttributeOption::groupby('name')->select('attribute_id','name','id','keyword')->get();
        
        foreach($options as $option){
            if(!in_array(Attribute::withCount('options')->findOrFail($option->attribute_id)->keyword,$attrubutes_check)){
                $attrubutes_check[] = Attribute::withCount('options')->findOrFail($option->attribute_id)->keyword;
            }
        }

        
        $attrubutes = [];

        foreach($attrubutes_check as $attr_new_get){
            $attrubutes[] = Attribute::whereKeyword($attr_new_get)->first();
        }
      
        $blade = 'front.catalog.index';

        if($request->view_check){
            Session::put('view_catalog',$request->view_check);

        }

        if(Session::has('view_catalog')){
            $checkType = Session::get('view_catalog');
            $name_string_count = 55;
        }else{
            Session::put('view_catalog','grid');
            $checkType = Session::get('view_catalog');
            $name_string_count = 38;
        }


        $vendorStore = null;
        if ($vendor !== null) {
            if ($vendor == 'admin' || $vendor == '0') {
                $vendorStore = (object)[
                    'is_admin' => true,
                    'vendor_id' => 0,
                    'name' => ($setting->brand_name ?? 'Official Store'),
                    'logo_url' => ($setting->brand_logo ? url('/core/public/storage/images/' . $setting->brand_logo) : null),
                    'banner_url' => null,
                    'type' => __('Official Store'),
                    'address' => $setting->footer_address ?? null,
                    'details' => __('Official Store on :name. Genuine products with platform guarantee.', ['name' => ($setting->brand_name ?? 'Official Store')]),
                    'products_count' => Item::where('status', 1)->where(function($q) {
                        $q->whereNull('vendor_id')->orWhere('vendor_id', 0);
                    })->count(),
                ];
            } else {
                $vendorUser = \App\Models\User::find($vendor);
                if ($vendorUser) {
                    $seller = \App\Models\Seller::where('user_id', $vendorUser->id)->first();
                    $logoUrl = null;
                    if ($seller && !empty($seller->shop_logo)) {
                        $logoUrl = asset('core/public/storage/images/stores/' . $seller->shop_logo);
                    } elseif (!empty($vendorUser->photo)) {
                        $logoUrl = asset('core/public/storage/images/' . $vendorUser->photo);
                    }

                    $bannerUrl = null;
                    if ($seller && !empty($seller->shop_banner)) {
                        $bannerUrl = asset('core/public/storage/images/stores/' . $seller->shop_banner);
                    }

                    $vendorStore = (object)[
                        'is_admin' => false,
                        'vendor_id' => $vendorUser->id,
                        'name' => $seller && !empty($seller->shop_name) ? $seller->shop_name : ($vendorUser->first_name . '\'s Store'),
                        'logo_url' => $logoUrl,
                        'banner_url' => $bannerUrl,
                        'type' => __('Verified Store'),
                        'address' => $seller->shop_address ?? null,
                        'details' => $seller->shop_details ?? null,
                        'products_count' => Item::where('status', 1)->where('vendor_id', $vendorUser->id)->count(),
                    ];
                }
            }
        }

        if($request->ajax()) $blade = 'front.catalog.catalog';

        return view($blade,[
            'attrubutes' => $attrubutes,
            'options' => $options,
            'brand' => $brand,
            'items' => $items,
            'name_string_count' => $name_string_count,
            'category' => $category,
            'selected_categories' => $selected_categories,
            'subcategory' => $subcategory,
            'selected_subcategories' => $selected_subcategories,
            'childcategory' => $childcategory,
            'checkType'  => $checkType,
            'vendorStore' => $vendorStore,
            'vendor' => $vendor,
            'tag' => $tag,
            'brands' => Brand::withCount('items')->whereStatus(1)->get(),
            'categories' => Category::whereStatus(1)->orderby('serial','asc')->withCount(['items' => function($query) {
                $query->where('status',1);
            }])->take(Setting::first()->buyer_category_limit ?? 50)->get(),
        ]);
	}


    public function viewType($type)
    {
        Session::put('view_catalog',$type);
        return response()->json($type);
    }


    public function suggestSearch(Request $request)
    {
        $category = null;
        if($request->category){
            $category = Category::whereSlug($request->category)->first();
        }
        $search = $request->search;
        $items = Item::whereStatus(1)
        ->when($search, function ($query, $search) {
            return $query->where('name', 'like', '%' . $search . '%')->orderby('id','desc')->take(10);
        })
        ->when($category, function ($query, $category) {
            return $query->where('category_id', $category->id);
        })
        ->get();

        return view('includes.search_suggest',compact('items'));
    }

}
