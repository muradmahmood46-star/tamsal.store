<?php

namespace App\Http\Controllers\Seller;

use App\Helpers\ImageHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\GalleryRequest;
use App\Http\Requests\ItemRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Currency;
use App\Models\Gallery;
use App\Models\Item;
use App\Models\Subcategory;
use App\Models\Tax;
use App\Repositories\Back\ItemRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    protected $repository;

    public function __construct(ItemRepository $repository)
    {
        $this->middleware(['auth', 'seller']);
        $this->repository = $repository;
    }

    public function add()
    {
        return redirect()->route('seller.item.create');
    }

    public function index(Request $request)
    {
        $vendorId = Auth::id();
        $item_type = $request->has('item_type') ? ($request->item_type ? $request->item_type : '') : '';
        $is_type = $request->has('is_type') ? ($request->is_type ? $request->is_type : '') : '';
        $category_id = $request->has('category_id') ? ($request->category_id ? $request->category_id : '') : '';
        $orderby = $request->has('orderby') ? ($request->orderby ? $request->orderby : 'desc') : 'desc';

        $datas = Item::where('vendor_id', $vendorId)
            ->when($item_type, function ($query, $item_type) {
                return $query->where('item_type', $item_type);
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
            ->paginate(15);

        return view('seller.item.index', compact('datas'));
    }

    public function stockOut()
    {
        $vendorId = Auth::id();
        $datas = Item::where('vendor_id', $vendorId)
            ->where('stock', 0)
            ->where('item_type', 'normal')
            ->orderBy('id', 'desc')
            ->paginate(15);

        return view('seller.item.stockout', compact('datas'));
    }

    public function create()
    {
        $vendorId = Auth::id();
        return view('seller.item.create', [
            'curr' => Currency::where('is_default', 1)->first(),
            'categories' => Category::where('status', 1)->where(function($q) use ($vendorId) {
                $q->whereNull('vendor_id')->orWhere('vendor_id', 0)->orWhere('vendor_id', $vendorId);
            })->orderBy('name', 'asc')->get(),
            'brands' => Brand::where('status', 1)->where(function($q) use ($vendorId) {
                $q->whereNull('vendor_id')->orWhere('vendor_id', 0)->orWhere('vendor_id', $vendorId);
            })->orderBy('name', 'asc')->get(),
            'taxes' => Tax::where('status', 1)->get(),
        ]);
    }

    public function store(ItemRequest $request)
    {
        $this->authorizedSubcategory($request->subcategory_id, $request->category_id);
        // Inject seller ID into request
        $request->merge([
            'vendor_id' => Auth::id(),
            'status' => 0,
            'approval_status' => 'Pending',
            'reject_reason' => null
        ]);

        $item_id = $this->repository->store($request);
        $item = Item::find($item_id);
        if ($item) {
            $item->vendor_id = Auth::id();
            $item->status = 0;
            $item->approval_status = 'Pending';
            $item->reject_reason = null;
            $item->save();
        }

        $msg = __('Your request to list this product has been received. Our team will verify it shortly and your product will be listed soon.');

        if ($request->is_button == 0) {
            return redirect()->route('seller.item.index')->withSuccess($msg);
        } else {
            return redirect(route('seller.item.edit', $item_id))->withSuccess($msg);
        }
    }

    public function edit($id)
    {
        $vendorId = Auth::id();
        $item = Item::where('id', $id)->where('vendor_id', $vendorId)->firstOrFail();

        return view('seller.item.edit', [
            'item' => $item,
            'curr' => Currency::where('is_default', 1)->first(),
            'subcategories' => Subcategory::where('category_id', $item->category_id)->where(function ($query) use ($vendorId) {
                $query->whereNull('vendor_id')->orWhere('vendor_id', $vendorId);
            })->get(),
            'categories' => Category::where('status', 1)->where(function($q) use ($vendorId) {
                $q->whereNull('vendor_id')->orWhere('vendor_id', 0)->orWhere('vendor_id', $vendorId);
            })->orderBy('name', 'asc')->get(),
            'brands' => Brand::where('status', 1)->where(function($q) use ($vendorId) {
                $q->whereNull('vendor_id')->orWhere('vendor_id', 0)->orWhere('vendor_id', $vendorId);
            })->orderBy('name', 'asc')->get(),
            'taxes' => Tax::where('status', 1)->get(),
            'social_icons' => json_decode($item->social_icons, true),
            'social_links' => json_decode($item->social_links, true),
            'specification_name' => json_decode($item->specification_name, true),
            'specification_description' => json_decode($item->specification_description, true),
        ]);
    }

    public function update(ItemRequest $request, $id)
    {
        $item = Item::where('id', $id)->where('vendor_id', Auth::id())->firstOrFail();
        $this->authorizedSubcategory($request->subcategory_id, $request->category_id);

        $request->merge([
            'vendor_id' => Auth::id(),
            'status' => 0,
            'approval_status' => 'Pending',
            'reject_reason' => null
        ]);

        $this->repository->update($item, $request);

        $item->refresh();
        $item->status = 0;
        $item->approval_status = 'Pending';
        $item->reject_reason = null;
        $item->save();

        $msg = __('Your request to list this product has been received. Our team will verify it shortly and your product will be listed soon.');

        if ($request->is_button == 0) {
            return redirect()->route('seller.item.index')->withSuccess($msg);
        } else {
            return redirect()->back()->withSuccess($msg);
        }
    }

    public function status($id, $status)
    {
        $item = Item::where('id', $id)->where('vendor_id', Auth::id())->firstOrFail();
        
        if ($item->approval_status !== 'Approved') {
            return redirect()->back()->withError(__('This product is pending admin review or rejected and cannot be activated until approved.'));
        }

        $item->update(['status' => $status]);
        return redirect()->back()->withSuccess(__('Status Updated Successfully.'));
    }

    public function destroy($id)
    {
        $item = Item::where('id', $id)->where('vendor_id', Auth::id())->firstOrFail();
        $this->repository->delete($item);
        return redirect()->back()->withSuccess(__('Product Deleted Successfully.'));
    }

    public function galleries($id)
    {
        $item = Item::where('id', $id)->where('vendor_id', Auth::id())->firstOrFail();
        return view('seller.item.galleries', compact('item'));
    }

    public function galleriesUpdate(Request $request)
    {
        $item = Item::where('id', $request->item_id)->where('vendor_id', Auth::id())->firstOrFail();
        $this->repository->galleriesUpdate($request, $item->id);
        return redirect()->back()->withSuccess(__('Gallery Information Updated Successfully.'));
    }

    public function galleryDelete($id)
    {
        $gallery = Gallery::findOrFail($id);
        $item = Item::where('id', $gallery->item_id)->where('vendor_id', Auth::id())->firstOrFail();
        $this->repository->galleryDelete($gallery);
        return redirect()->back()->withSuccess(__('Successfully Deleted From Gallery.'));
    }

    public function getsubCategory(Request $request)
    {
        $data = [];
        if ($request->category_id) {
            $vendorId = Auth::id();
            $category = Category::where('id', $request->category_id)->where(function ($query) use ($vendorId) {
                $query->whereNull('vendor_id')->orWhere('vendor_id', 0)->orWhere('vendor_id', $vendorId);
            })->firstOrFail();

            $data = Subcategory::where('category_id', $category->id)->where(function ($query) use ($vendorId) {
                $query->whereNull('vendor_id')->orWhere('vendor_id', $vendorId);
            })->get();
        }

        return response()->json(['data' => $data]);
    }

    public function getChildCategory(Request $request)
    {
        if ($request->subcategory_id) {
            $data = Subcategory::where('id', $request->subcategory_id)->where(function ($query) {
                $query->whereNull('vendor_id')->orWhere('vendor_id', Auth::id());
            })->firstOrFail();
            $data = $data->childcategory;
        } else {
            $data = [];
        }

        return response()->json(['data' => $data]);
    }

    private function authorizedSubcategory($subcategoryId, $categoryId)
    {
        if ($subcategoryId) {
            Subcategory::where('id', $subcategoryId)->where('category_id', $categoryId)->where(function ($query) {
                $query->whereNull('vendor_id')->orWhere('vendor_id', Auth::id());
            })->firstOrFail();
        }
    }
}
