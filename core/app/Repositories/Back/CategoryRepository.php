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
            if ($home) {
                $needsUpdate = false;

                // 1. Popular Category
                if (!empty($home->popular_category)) {
                    $popular_category = json_decode($home->popular_category, true);
                    if (is_array($popular_category)) {
                        for ($i = 1; $i <= 4; $i++) {
                            if (isset($popular_category['category_id' . $i]) && $popular_category['category_id' . $i] == $category->id) {
                                $popular_category['category_id' . $i] = null;
                                $popular_category['subcategory_id' . $i] = null;
                                $popular_category['childcategory_id' . $i] = null;
                                $needsUpdate = true;
                            }
                        }
                        if ($needsUpdate) {
                            $home->popular_category = json_encode($popular_category);
                        }
                    }
                }

                // 2. Feature Category
                if (!empty($home->feature_category)) {
                    $feature_category = json_decode($home->feature_category, true);
                    if (is_array($feature_category)) {
                        for ($i = 1; $i <= 4; $i++) {
                            if (isset($feature_category['category_id' . $i]) && $feature_category['category_id' . $i] == $category->id) {
                                $feature_category['category_id' . $i] = null;
                                $feature_category['subcategory_id' . $i] = null;
                                $feature_category['childcategory_id' . $i] = null;
                                $needsUpdate = true;
                            }
                        }
                        if ($needsUpdate) {
                            $home->feature_category = json_encode($feature_category);
                        }
                    }
                }

                // 3. Two / Three Column Category
                if (!empty($home->two_column_category)) {
                    $two_column_category = json_decode($home->two_column_category, true);
                    if (is_array($two_column_category)) {
                        for ($i = 1; $i <= 3; $i++) {
                            if (isset($two_column_category['category_id' . $i]) && $two_column_category['category_id' . $i] == $category->id) {
                                $two_column_category['category_id' . $i] = null;
                                $two_column_category['subcategory_id' . $i] = null;
                                $two_column_category['childcategory_id' . $i] = null;
                                $needsUpdate = true;
                            }
                        }
                        if ($needsUpdate) {
                            $home->two_column_category = json_encode($two_column_category);
                        }
                    }
                }

                // 4. Home 4 Popular Category
                if (!empty($home->home_4_popular_category)) {
                    $home_4_popular_category = json_decode($home->home_4_popular_category, true);
                    if (is_array($home_4_popular_category) && in_array($category->id, $home_4_popular_category)) {
                        $home_4_popular_category = array_values(array_filter($home_4_popular_category, function ($id) use ($category) {
                            return $id != $category->id;
                        }));
                        $home->home_4_popular_category = json_encode($home_4_popular_category);
                        $needsUpdate = true;
                    }
                }

                if ($needsUpdate) {
                    $home->save();
                }
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
