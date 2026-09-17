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
     */
    public function index()
    {
        $datas = Category::where('vendor_id', Auth::id())->orderBy('id', 'desc')->get();
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
            'slug' => 'nullable|string|max:255|unique:categories,slug',
            'photo' => 'nullable|file|mimes:jpeg,jpg,png,svg,webp,gif,bmp,tiff,tif,avif,ico,jfif,heic,heif|max:10240'
        ]);

        $name = $request->name;
        $slug = $request->slug ? Str::slug($request->slug) : Str::slug($name);
        
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
        Category::find($id)->update(['status' => $status]);
        return redirect()->route('seller.category.index')->withSuccess(__('Status Updated Successfully.'));
    }

    /**
     * Show form for editing category.
     */
    public function edit(Category $category)
    {
        return view('seller.category.edit', compact('category'));
    }

    /**
     * Update specified category.
     */
    public function update(CategoryRequest $request, Category $category)
    {
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
        $mgs = $this->repository->delete($category);
        if ($mgs['status'] == 1) {
            return redirect()->route('seller.category.index')->withSuccess($mgs['message']);
        } else {
            return redirect()->route('seller.category.index')->withError($mgs['message']);
        }
    }
}

