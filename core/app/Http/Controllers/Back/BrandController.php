<?php

namespace App\Http\Controllers\Back;

use App\{
    Models\Brand,
    Models\Setting,
    Repositories\Back\BrandRepository,
    Http\Requests\BrandRequest,
    Http\Controllers\Controller
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class BrandController extends Controller
{
    /**
     * Constructor Method.
     *
     * Setting Authentication
     *
     * @param  \App\Repositories\Back\BrandRepository $repository
     *
     */
    public function __construct(BrandRepository $repository)
    {
        $this->middleware('auth:admin');
        $this->middleware('adminlocalize');
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('back.brand.index',[
            'datas' => Brand::where(function($q) {
                $q->whereNull('vendor_id')->orWhere('vendor_id', 0);
            })->orderBy('id','desc')->get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('back.brand.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BrandRequest $request)
    {
        $this->repository->store($request);
        return redirect()->route('back.brand.index')->withSuccess(__('New Brand Added Successfully.'));
    }

    /**
     * Toggle Brand Image / Logo visibility and requirement.
     *
     * @param int $status
     * @return \Illuminate\Http\Response
     */
    public function imageToggle($status)
    {
        if (Schema::hasTable('settings')) {
            if (!Schema::hasColumn('settings', 'is_brand_image')) {
                try {
                    Schema::table('settings', function (Blueprint $table) {
                        $table->tinyInteger('is_brand_image')->default(1)->nullable();
                    });
                } catch (\Throwable $e) {}
            }
        }

        $setting = Setting::first();
        if ($setting) {
            $setting->is_brand_image = (int)$status;
            $setting->save();
        }

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'status' => true,
                'is_brand_image' => (int)$status,
                'message' => ((int)$status == 1) ? __('Brand image has been enabled.') : __('Brand image has been disabled.')
            ]);
        }

        return redirect()->route('back.brand.index')->withSuccess(
            ((int)$status == 1) ? __('Brand image has been enabled.') : __('Brand image has been disabled.')
        );
    }

    /**
     * AJAX Toggle Brand Image / Logo.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function imageToggleAjax(Request $request)
    {
        $status = $request->input('status', 1);
        return $this->imageToggle($status);
    }

    /**
     * Change the status for editing the specified resource.
     *
     * @param  int  $id
     * @param  int  $status
     * @return \Illuminate\Http\Response
     */
    public function status($id,$status,$type)
    {
        Brand::find($id)->update([$type => $status]);
        return redirect()->route('back.brand.index')->withSuccess(__('Status Updated Successfully.'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Brand $brand)
    {
        return view('back.brand.edit',compact('brand'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(BrandRequest $request, Brand $brand)
    {
        $this->repository->update($brand, $request);
        return redirect()->route('back.brand.index')->withSuccess(__('Brand Updated Successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Brand $brand)
    {
        $this->repository->delete($brand);
        return redirect()->route('back.brand.index')->withSuccess(__('Brand Deleted Successfully.'));
    }
}
