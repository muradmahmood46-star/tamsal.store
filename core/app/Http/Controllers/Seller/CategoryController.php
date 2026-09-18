<?php

namespace App\Http\Controllers\Seller;

use App\{
    Models\Category,
    Repositories\Back\CategoryRepository,
    Http\Requests\CategoryRequest,
    Http\Controllers\Controller
};
use App\Helpers\ImageHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    protected $repository;

    public function __construct(CategoryRepository $repository)
    {
        $this->middleware(['auth', 'seller']);
        $this->repository = $repository;
    }

    /**
     * Display listing of categories in Seller Panel.
     * Shows Admin categories + vendor's own categories.
     * Excludes categories created by other vendors.
     */
    public function index()
    {
        $vendorId = Auth::id();
        $datas = Category::where(function ($query) use ($vendorId) {
            $query->whereNull('vendor_id')
                  ->orWhere('vendor_id', 0)
                  ->orWhere('vendor_id', $vendorId);
        })->orderBy('id', 'desc')->get();

        return view('seller.category.index', compact('datas'));
    }

    /**
     * Show form for creating a new category.
     */
    public function create()
    {
        return view('seller.category.create');
    }

    /**
     * Store newly created category.
     */
    public function store(CategoryRequest $request)
    {
        $request->validate([
            'serial' => 'nullable|numeric|max:150'
        ]);
        
        $input = $request->all();
        $input['vendor_id'] = Auth::id();
        $input['photo'] = ImageHelper::handleUploadedImage($request->file('photo'), 'images');

        $slug = $request->slug ? Str::slug($request->slug) : Str::slug($request->name);
        if (empty($slug)) {
            $slug = 'category-' . time();
        }
        $originalSlug = $slug;
        $counter = 1;
        while (Category::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        $input['slug'] = $slug;

        Category::create($input);
        return redirect()->route('seller.category.index')->withSuccess(__('New Category Added Successfully.'));
    }

    /**
     * AJAX quick store for product creation modal.
     */
    public function quickStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'photo' => 'nullable|file|mimes:jpeg,jpg,png,svg,webp,gif,bmp,tiff,tif,avif,ico,jfif,heic,heif|max:10240'
        ]);

        $name = $request->name;
        $slug = $request->slug ? Str::slug($request->slug) : Str::slug($name);
        if (empty($slug)) {
            $slug = 'category-' . time();
        }
        
        // Ensure unique slug
        $originalSlug = $slug;
        $counter = 1;
        while (Category::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $photoName = null;
        if ($file = $request->file('photo')) {
            $photoName = ImageHelper::handleUploadedImage($file, 'images');
        }

        $category = Category::create([
            'vendor_id' => Auth::id(),
            'name' => $name,
            'slug' => $slug,
            'photo' => $photoName,
            'serial' => 0,
            'status' => 1
        ]);

        return response()->json([
            'status' => true,
            'message' => __('Category created successfully!'),
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug
            ]
        ]);
    }

    /**
     * Change category status.
     */
    public function status($id, $status)
    {
        $category = Category::where('id', $id)->where('vendor_id', Auth::id())->first();
        if (!$category) {
            return redirect()->route('seller.category.index')->withError(__('You can only modify status for your own categories.'));
        }
        $category->update(['status' => $status]);
        return redirect()->route('seller.category.index')->withSuccess(__('Status Updated Successfully.'));
    }

    /**
     * Show form for editing category.
     */
    public function edit(Category $category)
    {
        if ($category->vendor_id != Auth::id()) {
            return redirect()->route('seller.category.index')->withError(__('You can only edit your own categories.'));
        }
        return view('seller.category.edit', compact('category'));
    }

    /**
     * Update specified category.
     */
    public function update(CategoryRequest $request, Category $category)
    {
        if ($category->vendor_id != Auth::id()) {
            return redirect()->route('seller.category.index')->withError(__('You can only edit your own categories.'));
        }
        $request->validate([
            'serial' => 'nullable|numeric|max:150'
        ]);

        $this->repository->update($category, $request);
        return redirect()->route('seller.category.index')->withSuccess(__('Category Updated Successfully.'));
    }

    /**
     * Delete category.
     */
    public function destroy(Category $category)
    {
        if ($category->vendor_id != Auth::id()) {
            return redirect()->route('seller.category.index')->withError(__('You can only delete your own categories.'));
        }
        $mgs = $this->repository->delete($category);
        if ($mgs['status'] == 1) {
            return redirect()->route('seller.category.index')->withSuccess($mgs['message']);
        } else {
            return redirect()->route('seller.category.index')->withError($mgs['message']);
        }
    }
}

