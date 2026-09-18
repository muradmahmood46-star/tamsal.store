<?php

namespace App\Repositories\Back;

use App\{
    Models\Category,
    Helpers\ImageHelper
};
use App\Models\HomeCutomize;

class CategoryRepository
{

    /**
     * Store category.
     *
     * @param  \App\Http\Requests\CategoryRequest  $request
     * @return void
     */

    public function store($request)
    {
        $input = $request->all();
        $input['photo'] = ImageHelper::handleUploadedImage($request->file('photo'),'images');
        Category::create($input);
    }

    /**
     * Update category.
     *
     * @param  \App\Http\Requests\CategoryRequest  $request
     * @return void
     */

    public function update($category, $request)
    {
        $input = $request->all();
        if ($file = $request->file('photo')) {
            $input['photo'] = ImageHelper::handleUpdatedUploadedImage($file,'images',$category,'images','photo');
        }
        $category->update($input);
    }

    /**
     * Delete category.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function delete($category)
    {
        try {
            $home = HomeCutomize::first();
            $popular_category = ($home && !empty($home->popular_category)) ? json_decode($home->popular_category, true) : [];
            $feature_category = ($home && !empty($home->feature_category)) ? json_decode($home->feature_category, true) : [];
            $two_column_category = ($home && !empty($home->two_column_category)) ? json_decode($home->two_column_category, true) : [];
            $home_4_popular_category = ($home && !empty($home->home_4_popular_category)) ? json_decode($home->home_4_popular_category, true) : [];
            $check = false;

            if (is_array($popular_category)) {
                for ($i = 1; $i <= 4; $i++) {
                    if (isset($popular_category['category_id' . $i]) && $popular_category['category_id' . $i] == $category->id) {
                        $check = true;
                        break;
                    }
                }
            }

            if (is_array($feature_category)) {
                for ($i = 1; $i <= 4; $i++) {
                    if (isset($feature_category['category_id' . $i]) && $feature_category['category_id' . $i] == $category->id) {
                        $check = true;
                        break;
                    }
                }
            }

            if (is_array($two_column_category)) {
                for ($i = 1; $i <= 2; $i++) {
                    if (isset($two_column_category['category_id' . $i]) && $two_column_category['category_id' . $i] == $category->id) {
                        $check = true;
                        break;
                    }
                }
            }

            if (is_array($home_4_popular_category)) {
                if (in_array($category->id, $home_4_popular_category)) {
                    $check = true;
                }
            }

            if ($check) {
                return [
                    'message' => __('This Category is currently used in the Home Page section. Please change this category in Home Page settings before deleting it.'),
                    'status' => 0
                ];
            }

            // Safely unlink or clean up related childcategories and subcategories
            \App\Models\ChieldCategory::where('category_id', $category->id)->delete();
            \App\Models\Subcategory::where('category_id', $category->id)->delete();

            // Nullify category references on items so items are not corrupted or breaking foreign keys
            \App\Models\Item::where('category_id', $category->id)->update([
                'category_id' => null,
                'subcategory_id' => null,
                'childcategory_id' => null
            ]);

            try {
                ImageHelper::handleDeletedImage($category, 'photo', 'images');
            } catch (\Throwable $e) {}

            $category->delete();

            return ['message' => __('Category Deleted Successfully.'), 'status' => 1];
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Category Delete Error: ' . $e->getMessage());
            return ['message' => __('Error deleting category: ') . $e->getMessage(), 'status' => 0];
        }
    }

}
