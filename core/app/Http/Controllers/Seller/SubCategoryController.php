<?php

namespace App\Http\Controllers\Seller;

use App\{
    Models\Category,
    Repositories\Back\SubCategoryRepository,
    Http\Requests\SubCategoryRequest,
    Http\Controllers\Controller
};
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SubCategoryController extends Controller
{
    protected $repository;

    public function __construct(SubCategoryRepository $repository)
    {
        $this->middleware(['auth', 'seller']);
        $this->repository = $repository;
    }

    /**
     * Display listing of subcategories in Seller Panel.
     * Shows admin subcategories and the current vendor's subcategories.
     */
    public function index()
    {
        $vendorId = Auth::id();
        $datas = Subcategory::where(function ($query) use ($vendorId) {
            $query->whereNull('vendor_id')->orWhere('vendor_id', $vendorId);
        })->whereHas('category', function ($query) use ($vendorId) {
            $query->whereNull('vendor_id')
                  ->orWhere('vendor_id', 0)
                  ->orWhere('vendor_id', $vendorId);
        })->with('category')->orderBy('id', 'desc')->get();
        return view('seller.subcategory.index', compact('datas'));
    }

    /**
     * Show form for creating a new subcategory.
     */
    public function create()
    {
        return view('seller.subcategory.create');
    }

    /**
     * Store newly created subcategory.
     */
    public function store(SubCategoryRequest $request)
    {
        $this->authorizedCategory($request->category_id);
        $request->merge(['vendor_id' => Auth::id()]);
        $this->repository->store($request);
        return redirect()->route('seller.subcategory.index')->withSuccess(__('New Subcategory Added Successfully.'));
    }

    /**
     * AJAX quick store for product creation modal.
     */
    public function quickStore(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255'
        ]);

        $this->authorizedCategory($request->category_id);

        $name = $request->name;
        $slug = $request->slug ? Str::slug($request->slug) : Str::slug($name);
        
        $originalSlug = $slug;
        $counter = 1;
        while (Subcategory::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $subcategory = Subcategory::create([
            'category_id' => $request->category_id,
            'name' => $name,
            'slug' => $slug,
            'status' => 1,
            'vendor_id' => Auth::id()
        ]);

        return response()->json([
            'status' => true,
            'message' => __('Subcategory created successfully!'),
            'subcategory' => [
                'id' => $subcategory->id,
                'category_id' => $subcategory->category_id,
                'name' => $subcategory->name,
                'slug' => $subcategory->slug
            ]
        ]);
    }

    /**
     * Change subcategory status.
     */
    public function status($id, $status)
    {
        $this->visibleSubcategory($id)->update(['status' => $status]);
        return redirect()->route('seller.subcategory.index')->withSuccess(__('Status Updated Successfully.'));
    }

    /**
     * Show form for editing subcategory.
     */
    public function edit(Subcategory $subcategory)
    {
        $this->ensureVisibleToVendor($subcategory);
        return view('seller.subcategory.edit', compact('subcategory'));
    }

    /**
     * Update specified subcategory.
     */
    public function update(SubCategoryRequest $request, Subcategory $subcategory)
    {
        $this->ensureVisibleToVendor($subcategory);
        $this->authorizedCategory($request->category_id);
        $request->merge(['vendor_id' => $subcategory->vendor_id]);
        $this->repository->update($subcategory, $request);
        return redirect()->route('seller.subcategory.index')->withSuccess(__('Subcategory Updated Successfully.'));
    }

    /**
     * Delete subcategory.
     */
    public function destroy(Subcategory $subcategory)
    {
        $this->ensureVisibleToVendor($subcategory);
        $this->repository->delete($subcategory);
        return redirect()->route('seller.subcategory.index')->withSuccess(__('Subcategory Deleted Successfully.'));
    }

    private function authorizedCategory($categoryId)
    {
        return Category::where('id', $categoryId)->where(function ($query) {
            $query->whereNull('vendor_id')
                ->orWhere('vendor_id', 0)
                ->orWhere('vendor_id', Auth::id());
        })->firstOrFail();
    }

    private function visibleSubcategory($id)
    {
        return Subcategory::where('id', $id)->where(function ($query) {
            $query->whereNull('vendor_id')->orWhere('vendor_id', Auth::id());
        })->firstOrFail();
    }

    private function ensureVisibleToVendor(Subcategory $subcategory)
    {
        abort_unless(is_null($subcategory->vendor_id) || $subcategory->vendor_id == Auth::id(), 404);
    }
}
