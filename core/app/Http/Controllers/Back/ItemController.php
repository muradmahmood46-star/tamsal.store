<?php

namespace App\Http\Controllers\Back;

use App\{
    Models\Item,
    Models\Gallery,
    Http\Requests\ItemRequest,
    Http\Controllers\Controller,
    Http\Requests\GalleryRequest,
    Repositories\Back\ItemRepository
};
use App\Helpers\ImageHelper;
use App\Models\Category;
use App\Models\ChieldCategory;
use App\Models\Currency;
use App\Models\PromotionTag;
use App\Models\PromotionPlan;
use App\Models\Subcategory;
use Illuminate\Http\Request;

class ItemController extends Controller
{

    /**
     * Constructor Method.
     *
     * Setting Authentication
     *
     * @param  \App\Repositories\Back\ItemRepository $repository
     *
     */
    public function __construct(ItemRepository $repository)
    {
        $this->middleware('auth:admin');
        $this->middleware('adminlocalize');
        $this->repository = $repository;
    }


    public function summernoteUpload(Request $request)
    {
        $name = ImageHelper::uploadSummernoteImage($request->file('image'), 'images/summernote');

        return response()->json([
            'success' => true,
            'image' => url('/core/public/storage/images/summernote/' . $name)
        ]);
    }


    public function add()
    {
        return view('back.item.add');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $item_type = $request->has('item_type') ? ($request->item_type ? $request->item_type : '') : '';
        $is_type = $request->has('is_type') ? ($request->is_type ? $request->is_type : '') : '';
        $category_id = $request->has('category_id') ? ($request->category_id ? $request->category_id : '') : '';
        $orderby = $request->has('orderby') ? ($request->orderby ? $request->orderby : 'desc') : 'desc';

        $datas = Item::when($item_type, function ($query, $item_type) {
                if ($item_type === 'my_products' || $item_type === 'admin') {
                    return $query->where(function ($q) {
                        $q->whereNull('vendor_id')->orWhere('vendor_id', 0);
                    });
                } elseif ($item_type === 'vendor_products' || $item_type === 'vendor') {
                    return $query->whereNotNull('vendor_id')->where('vendor_id', '>', 0);
                } elseif ($item_type !== 'all' && !empty($item_type)) {
                    return $query->where('item_type', $item_type);
                }
            })
            ->when($is_type, function ($query, $is_type) {
                if ($is_type != 'outofstock') {
                    return $query->where('is_type', $is_type);
                } else {
                    return $query->whereStock(0)->whereItemType('normal');
                }
            })
            ->when($category_id, function ($query, $category_id) {
                return $query->where('category_id', $category_id);
            })
            ->when($orderby, function ($query, $orderby) {
                return $query->orderby('id', $orderby);
            })
            ->get();

        return view('back.item.index', [
            'datas' => $datas
        ]);
    }

    /**
     * Show the form for get subcategory a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getsubCategory(Request $request)
    {

        if ($request->category_id) {
            $data = Subcategory::where('category_id', $request->category_id)->whereNull('vendor_id')->get();
        } else {
            $data = [];
        }

        return response()->json(['data' => $data]);
    }

    /**
     * Show the form for get subcategory a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getChildCategory(Request $request)
    {

        if ($request->subcategory_id) {
            $data = ChieldCategory::where('subcategory_id', $request->subcategory_id)->whereNull('vendor_id')->where('status', 1)->get();
        } else {
            $data = [];
        }

        return response()->json(['data' => $data]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('back.item.create', [
            'curr' => Currency::where('is_default', 1)->first()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ItemRequest $request)
    {
        $item_id = $this->repository->store($request);

        if ($request->is_button == 0) {
            return redirect()->route('back.item.index')->withSuccess(__('Product Added Successfully.'));
        } else {
            return redirect(route('back.item.edit', $item_id))->withSuccess(__('Product Added Successfully.'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($item)
    {
        $itemModel = $item instanceof Item ? $item : Item::findOrFail($item);
        $socialIcons = $itemModel->social_icons;
        $socialLinks = $itemModel->social_links;
        $specName = $itemModel->specification_name;
        $specDesc = $itemModel->specification_description;

        return view('back.item.edit', [
            'item' => $itemModel,
            'curr' => Currency::where('is_default', 1)->first(),
            'social_icons' => is_string($socialIcons) ? (json_decode($socialIcons, true) ?? []) : (is_array($socialIcons) ? $socialIcons : []),
            'social_links' => is_string($socialLinks) ? (json_decode($socialLinks, true) ?? []) : (is_array($socialLinks) ? $socialLinks : []),
            'specification_name' => is_string($specName) ? (json_decode($specName, true) ?? []) : (is_array($specName) ? $specName : []),
            'specification_description' => is_string($specDesc) ? (json_decode($specDesc, true) ?? []) : (is_array($specDesc) ? $specDesc : []),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\ItemRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function update(ItemRequest $request, $item)
    {
        try {
            $itemModel = $item instanceof Item ? $item : Item::findOrFail($item);
            $this->repository->update($itemModel, $request);

            if ($request->is_button == 0) {
                return redirect()->route('back.item.index')->withSuccess(__('Product Updated Successfully.'));
            } else {
                return redirect()->back()->withSuccess(__('Product Updated Successfully.'));
            }
        } catch (\Exception $e) {
            \Log::error('Product update failed: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            return redirect()->back()->withInput()->withError(__('Failed to update product: ') . $e->getMessage());
        }
    }

    /**
     * Change the status for editing the specified resource.
     *
     * @param  mixed  $item
     * @param  int  $status
     * @return \Illuminate\Http\Response
     */
    public function status($item, $status)
    {
        $itemModel = $item instanceof Item ? $item : Item::findOrFail($item);
        $itemModel->update(['status' => $status]);
        return redirect()->back()->withSuccess(__('Status Updated Successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  mixed  $item
     * @return \Illuminate\Http\Response
     */
    public function destroy($item)
    {
        $itemModel = $item instanceof Item ? $item : Item::findOrFail($item);
        $this->repository->delete($itemModel);
        return redirect()->back()->withSuccess(__('Product Deleted Successfully.'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function galleries($item)
    {
        $itemModel = $item instanceof Item ? $item : Item::findOrFail($item);
        return view('back.item.galleries', ['item' => $itemModel]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\GalleryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function galleriesUpdate(Request $request)
    {
        $this->repository->galleriesUpdate($request);
        return redirect()->back()->withSuccess(__('Gallery Information Updated Successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function galleryDelete(Gallery $gallery)
    {
        $this->repository->galleryDelete($gallery);
        return redirect()->back()->withSuccess(__('Successfully Deleted From Gallery.'));
    }


    public function highlight($item)
    {
        $itemModel = $item instanceof Item ? $item : Item::findOrFail($item);
        PromotionTag::ensureTable();
        PromotionPlan::ensureTable();

        $tags = PromotionTag::where('status', 1)->orderBy('name', 'asc')->get();
        $plans = PromotionPlan::where('status', 1)->orderBy('days', 'asc')->get();

        return view('back.item.highlight', [
            'item' => $itemModel,
            'tags' => $tags,
            'plans' => $plans,
        ]);
    }
    public function highlight_update($item, Request $request)
    {
        $itemModel = $item instanceof Item ? $item : Item::findOrFail($item);
        $this->repository->highlight($itemModel, $request);
        return redirect()->route('back.item.index')->withSuccess(__('Product Updated Successfully.'));
    }




    // ---------------- DIGITAL PRODUCT START ---------------//

    public function deigitalItemCreate()
    {
        return view('back.item.digital.create', [
            'curr' => Currency::where('is_default', 1)->first()
        ]);
    }

    public function deigitalItemStore(ItemRequest $request)
    {
        $this->repository->store($request);
        return redirect()->route('back.item.index')->withSuccess(__('New Product Added Successfully.'));
    }

    public function deigitalItemEdit($id)
    {
        $item = Item::findOrFail($id);

        return view('back.item.digital.edit', [
            'item' => $item,
            'curr' => Currency::where('is_default', 1)->first(),
            'social_icons' => json_decode($item->social_icons, true),
            'social_links' => json_decode($item->social_links, true),
            'specification_name' => json_decode($item->specification_name, true),
            'specification_description' => json_decode($item->specification_description, true),
        ]);
    }


    // ---------------- LICENSE PRODUCT START ---------------//

    public function licenseItemCreate()
    {
        return view('back.item.license.create', [
            'curr' => Currency::where('is_default', 1)->first()
        ]);
    }

    public function licenseItemStore(ItemRequest $request)
    {
        $this->repository->store($request);
        return redirect()->route('back.item.index')->withSuccess(__('New Product Added Successfully.'));
    }

    public function licenseItemEdit($id)
    {
        $item = Item::findOrFail($id);

        return view('back.item.license.edit', [
            'item' => $item,
            'curr' => Currency::where('is_default', 1)->first(),
            'social_icons' => json_decode($item->social_icons, true),
            'social_links' => json_decode($item->social_links, true),
            'specification_name' => json_decode($item->specification_name, true),
            'specification_description' => json_decode($item->specification_description, true),
            'license_name' => json_decode($item->license_name, true),
            'license_key' => json_decode($item->license_key, true),
        ]);
    }


    public function stockOut()
    {
        $datas = Item::where('item_type', 'normal')->where('stock', 0)->get();
        return view('back.item.stockout', compact('datas'));
    }
}
