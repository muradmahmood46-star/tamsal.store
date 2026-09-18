<?php

namespace App\Repositories\Back;


use App\Models\Subcategory;

class SubCategoryRepository
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
        Subcategory::create($input);
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
        \App\Models\ChieldCategory::where('subcategory_id', $category->id)->delete();
        \App\Models\Item::where('subcategory_id', $category->id)->update([
            'subcategory_id' => null,
            'childcategory_id' => null
        ]);
        $category->delete();
    }

}
