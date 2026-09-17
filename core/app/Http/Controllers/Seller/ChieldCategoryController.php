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
     */
    public function index()
    {
        $datas = ChieldCategory::with(['category', 'subcategory'])->orderBy('id', 'desc')->get();
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
            'status' => 1
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
        ChieldCategory::find($id)->update(['status' => $status]);
        return redirect()->route('seller.childcategory.index')->withSuccess(__('Status Updated Successfully.'));
    }

    /**
     * Show form for editing child category.
     */
    public function edit(ChieldCategory $childcategory)
    {
        return view('seller.chieldcategory.edit', compact('childcategory'));
    }

    /**
     * Update specified child category.
     */
    public function update(ChieldcategoryRequest $request, ChieldCategory $childcategory)
    {
        $this->repository->update($childcategory, $request);
        return redirect()->route('seller.childcategory.index')->withSuccess(__('Childcategory Updated Successfully.'));
    }

    /**
     * Delete child category.
     */
    public function destroy(ChieldCategory $childcategory)
    {
        $this->repository->delete($childcategory);
        return redirect()->route('seller.childcategory.index')->withSuccess(__('Childcategory Deleted Successfully.'));
    }
}
