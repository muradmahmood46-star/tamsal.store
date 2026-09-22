<?php

namespace App\Http\Controllers\Seller;

use App\{
    Models\Category,
    Repositories\Back\ChieldCategoryRepository,
    Http\Requests\ChieldcategoryRequest,
    Http\Controllers\Controller
};
use App\Models\ChieldCategory;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ChieldCategoryController extends Controller
{
    protected $repository;

    public function __construct(ChieldCategoryRepository $repository)
    {
        $this->middleware(['auth', 'seller']);
        $this->repository = $repository;
    }

    /**
     * Display listing of child categories in Seller Panel.
     * Shows admin child categories and the current vendor's child categories.
     */
    public function index()
    {
        $vendorId = Auth::id();
        $datas = ChieldCategory::where(function ($query) use ($vendorId) {
            $query->whereNull('vendor_id')->orWhere('vendor_id', $vendorId);
        })->whereHas('category', function ($query) use ($vendorId) {
            $query->whereNull('vendor_id')
                  ->orWhere('vendor_id', 0)
                  ->orWhere('vendor_id', $vendorId);
        })->whereHas('subcategory', function ($query) use ($vendorId) {
            $query->whereNull('vendor_id')->orWhere('vendor_id', $vendorId);
        })->with(['category', 'subcategory'])->orderBy('id', 'desc')->get();
        return view('seller.chieldcategory.index', compact('datas'));
    }

    /**
     * Show form for creating a new child category.
     */
    public function create()
    {
        return view('seller.chieldcategory.create');
    }

    /**
     * Store newly created child category.
     */
    public function store(ChieldcategoryRequest $request)
    {
        $this->authorizedSubcategory($request->subcategory_id, $request->category_id);
        $request->merge(['vendor_id' => Auth::id()]);
        $this->repository->store($request);
        return redirect()->route('seller.childcategory.index')->withSuccess(__('New Childcategory Added Successfully.'));
    }

    /**
     * AJAX quick store for product creation modal.
     */
    public function quickStore(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'required|exists:subcategories,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255'
        ]);

        $this->authorizedSubcategory($request->subcategory_id, $request->category_id);

        $name = $request->name;
        $slug = $request->slug ? Str::slug($request->slug) : Str::slug($name);
        
        $originalSlug = $slug;
        $counter = 1;
        while (ChieldCategory::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $childcategory = ChieldCategory::create([
            'category_id' => $request->category_id,
            'subcategory_id' => $request->subcategory_id,
            'name' => $name,
            'slug' => $slug,
            'status' => 1,
            'vendor_id' => Auth::id()
        ]);

        return response()->json([
            'status' => true,
            'message' => __('Childcategory created successfully!'),
            'childcategory' => [
                'id' => $childcategory->id,
                'category_id' => $childcategory->category_id,
                'subcategory_id' => $childcategory->subcategory_id,
                'name' => $childcategory->name,
                'slug' => $childcategory->slug
            ]
        ]);
    }

    /**
     * Change child category status.
     */
    public function status($id, $status)
    {
        $this->visibleChildcategory($id)->update(['status' => $status]);
        return redirect()->route('seller.childcategory.index')->withSuccess(__('Status Updated Successfully.'));
    }

    /**
     * Show form for editing child category.
     */
    public function edit(ChieldCategory $childcategory)
    {
        $this->ensureVisibleToVendor($childcategory);
        return view('seller.chieldcategory.edit', compact('childcategory'));
    }

    /**
     * Update specified child category.
     */
    public function update(ChieldcategoryRequest $request, ChieldCategory $childcategory)
    {
        $this->ensureVisibleToVendor($childcategory);
        $this->authorizedSubcategory($request->subcategory_id, $request->category_id);
        $request->merge(['vendor_id' => $childcategory->vendor_id]);
        $this->repository->update($childcategory, $request);
        return redirect()->route('seller.childcategory.index')->withSuccess(__('Childcategory Updated Successfully.'));
    }

    /**
     * Delete child category.
     */
    public function destroy(ChieldCategory $childcategory)
    {
        $this->ensureVisibleToVendor($childcategory);
        $this->repository->delete($childcategory);
        return redirect()->route('seller.childcategory.index')->withSuccess(__('Childcategory Deleted Successfully.'));
    }

    private function authorizedSubcategory($subcategoryId, $categoryId)
    {
        return Subcategory::where('id', $subcategoryId)->where('category_id', $categoryId)->where(function ($query) {
            $query->whereNull('vendor_id')->orWhere('vendor_id', Auth::id());
        })->firstOrFail();
    }

    private function visibleChildcategory($id)
    {
        return ChieldCategory::where('id', $id)->where(function ($query) {
            $query->whereNull('vendor_id')->orWhere('vendor_id', Auth::id());
        })->firstOrFail();
    }

    private function ensureVisibleToVendor(ChieldCategory $childcategory)
    {
        abort_unless(is_null($childcategory->vendor_id) || $childcategory->vendor_id == Auth::id(), 404);
    }
}
