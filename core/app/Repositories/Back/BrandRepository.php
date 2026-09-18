<?php

namespace App\Repositories\Back;

use App\{
    Models\Brand,
};
use App\Helpers\ImageHelper;

class BrandRepository
{

    /**
     * Store meal.
     *
     * @param  \App\Http\Requests\ImageStoreRequest  $request
     * @return void
     */

    public function store($request)
    {
        $input = $request->all();
        $input['vendor_id'] = $request->vendor_id ?? 0;
        $input['photo'] = ImageHelper::handleUploadedImage($request->file('photo'),'images');

        $slug = $request->slug ? \Illuminate\Support\Str::slug($request->slug) : \Illuminate\Support\Str::slug($request->name);
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
    }

    /**
     * Update Brand.
     *
     * @param  \App\Http\Requests\ImageUpdateRequest  $request
     * @return void
     */

    public function update($brand, $request)
    {
        $input = $request->all();
        if ($file = $request->file('photo')) {
            $input['photo'] = ImageHelper::handleUpdatedUploadedImage($file,'images',$brand,'images/','photo');

        }

        $slug = $request->slug ? \Illuminate\Support\Str::slug($request->slug) : \Illuminate\Support\Str::slug($request->name);
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
    }

    /**
     * Delete brand.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function delete($brand)
    {
        ImageHelper::handleDeletedImage($brand,'photo','images');
        $brand->delete();
    }

}
