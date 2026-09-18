<?php

namespace App\Repositories\Back;

use App\{
    Models\ChieldCategory,
    Helpers\ImageHelper
};

class ChieldCategoryRepository
{

    /**
     * Store category.
     *
     * @param  \App\Http\Requests\ChieldCategoryRequest  $request
     * @return void
     */

    public function store($request)
    {
        $input = $request->all();
        ChieldCategory::create($input);
    }

    /**
     * Update category.
     *
     * @param  \App\Http\Requests\ChieldCategoryRequest  $request
     * @return void
     */

    public function update($fcategory, $request)
    {
        $input = $request->all();
        $fcategory->update($input);
    }

    /**
     * Delete category.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function delete($fcategory)
    {
        \App\Models\Item::where('childcategory_id', $fcategory->id)->update([
            'childcategory_id' => null
        ]);
        $fcategory->delete();
    }

}
