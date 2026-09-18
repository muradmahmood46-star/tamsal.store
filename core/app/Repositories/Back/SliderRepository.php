<?php

namespace App\Repositories\Back;

use App\{
    Models\Slider,
    Helpers\ImageHelper
};

class SliderRepository
{

    /**
     * Store slider.
     *
     * @param  \App\Http\Requests\ImageStoreRequest  $request
     * @return void
     */

    public function store($request)
    {
        $input = $request->all();
        $input['photo'] = ImageHelper::handleUploadedImage($request->file('photo'),'images');
        $input['logo'] = ImageHelper::handleUploadedImage($request->file('logo'),'images');
        Slider::create($input);
    }

    /**
     * Update slider.
     *
     * @param  \App\Http\Requests\ImageUpdateRequest  $request
     * @return void
     */

    public function update($slider, $request)
    {
        $input = $request->all();
        if ($file = $request->file('photo')) {
            $input['photo'] = ImageHelper::handleUpdatedUploadedImage($file,'images/',$slider,'images/','photo');
        }
        if ($file = $request->file('logo')) {
            $input['logo'] = ImageHelper::handleUpdatedUploadedImage($file,'images/',$slider,'images/','logo');
        } elseif ($request->has('remove_logo') && $request->remove_logo == 1) {
            ImageHelper::handleDeletedImage($slider,'logo','images');
            $input['logo'] = null;
        }
        $slider->update($input);
    }

    /**
     * Delete slider logo.
     *
     * @param  \App\Models\Slider $slider
     * @return void
     */
    public function deleteLogo($slider)
    {
        ImageHelper::handleDeletedImage($slider,'logo','images');
        $slider->logo = null;
        $slider->save();
    }

    /**
     * Delete slider.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function delete($slider)
    {
        ImageHelper::handleDeletedImage($slider,'photo','images');
        ImageHelper::handleDeletedImage($slider,'logo','images');
        $slider->delete();
    }

}
