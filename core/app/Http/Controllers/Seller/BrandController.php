<?php

namespace App\Http\Controllers\Seller;

use App\{
    Models\Brand,
    Http\Requests\BrandRequest,
    Http\Controllers\Controller
};
use App\Helpers\ImageHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    /**
     * Constructor Method.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'seller']);
    }

    /**
     * Display a listing of vendor's brands.
     * Shows Admin brands + vendor's own brands.
     */
    public function index()
    {
        $vendorId = Auth::id();
        $datas = Brand::where(function ($query) use ($vendorId) {
            $query->whereNull('vendor_id')
                  ->orWhere('vendor_id', 0)
                  ->orWhere('vendor_id', $vendorId);
        })->orderBy('id', 'desc')->get();
        return view('seller.brand.index', compact('datas'));
    }

    /**
     * Show the form for creating a new brand.
     */
    public function create()
    {
        return view('seller.brand.create');
    }

    /**
     * Store a newly created brand in storage.
     */
    public function store(BrandRequest $request)
    {
        $input = $request->all();
        $input['vendor_id'] = Auth::id();
        $input['photo'] = ImageHelper::handleUploadedImage($request->file('photo'), 'images');
        $input['status'] = 1;
        $input['is_popular'] = $request->is_popular ?? 0;

        $slug = $request->slug ? Str::slug($request->slug) : Str::slug($request->name);
        if (empty($slug)) {
            $slug = 'brand-' . time();
        }
        $originalSlug = $slug;
        $counter = 1;
        while (Brand::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        $input['slug'] = $slug;

        Brand::create($input);
        return redirect()->route('seller.brand.index')->withSuccess(__('New Brand Added Successfully.'));
    }

    /**
     * AJAX quick store for product creation modal/form.
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
            $slug = 'brand-' . time();
        }
        
        $originalSlug = $slug;
        $counter = 1;
        while (Brand::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $photoName = null;
        if ($file = $request->file('photo')) {
            $photoName = ImageHelper::handleUploadedImage($file, 'images');
        }

        $brand = Brand::create([
            'vendor_id' => Auth::id(),
            'name' => $name,
            'slug' => $slug,
            'photo' => $photoName,
            'status' => 1,
            'is_popular' => 0
        ]);

        return response()->json([
            'status' => true,
            'message' => __('Brand created successfully!'),
            'brand' => [
                'id' => $brand->id,
                'name' => $brand->name,
                'slug' => $brand->slug
            ]
        ]);
    }

    /**
     * Change status / popularity for the brand.
     */
    public function status($id, $status, $type)
    {
        $brand = Brand::where('id', $id)->where('vendor_id', Auth::id())->firstOrFail();
        $brand->update([$type => $status]);
        return redirect()->route('seller.brand.index')->withSuccess(__('Status Updated Successfully.'));
    }

    /**
     * Show the form for editing the brand.
     */
    public function edit($id)
    {
        $brand = Brand::where('id', $id)->where('vendor_id', Auth::id())->firstOrFail();
        return view('seller.brand.edit', compact('brand'));
    }

    /**
     * Update the specified brand in storage.
     */
    public function update(BrandRequest $request, $id)
    {
        $brand = Brand::where('id', $id)->where('vendor_id', Auth::id())->firstOrFail();
        $input = $request->all();

        if ($file = $request->file('photo')) {
            $input['photo'] = ImageHelper::handleUpdatedUploadedImage($file, 'images', $brand, 'images/', 'photo');
        }

        $slug = $request->slug ? Str::slug($request->slug) : Str::slug($request->name);
        if (empty($slug)) {
            $slug = $brand->slug ?: ('brand-' . $brand->id);
        }
        $originalSlug = $slug;
        $counter = 1;
        while (Brand::where('slug', $slug)->where('id', '!=', $brand->id)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        $input['slug'] = $slug;

        $brand->update($input);
        return redirect()->route('seller.brand.index')->withSuccess(__('Brand Updated Successfully.'));
    }

    /**
     * Remove the specified brand from storage.
     */
    public function destroy($id)
    {
        $brand = Brand::where('id', $id)->where('vendor_id', Auth::id())->firstOrFail();
        ImageHelper::handleDeletedImage($brand, 'photo', 'images');
        $brand->delete();
        return redirect()->route('seller.brand.index')->withSuccess(__('Brand Deleted Successfully.'));
    }
}
