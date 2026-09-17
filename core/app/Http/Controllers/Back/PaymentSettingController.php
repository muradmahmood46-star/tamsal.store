<?php

namespace App\Http\Controllers\Back;

use App\{
    Models\PaymentSetting,
    Http\Controllers\Controller,
    Http\Requests\PaymentSettingRequest,
    Repositories\Back\PaymentSettingRepository
};

use Illuminate\Http\Request;

class PaymentSettingController extends Controller
{
    /**
     * Constructor Method.
     *
     * Setting Authentication
     *
     * @param  \App\Repositories\Back\PaymentSettingRepository $repository
     *
     */
    public function __construct(PaymentSettingRepository $repository)
    {
        $this->middleware('auth:admin');
        $this->middleware('adminlocalize');
        $this->repository = $repository;
    }

    /**
     * Show the form for updating resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function payment()
    {
        return view('back.settings.payment', $this->repository->payment());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(PaymentSettingRequest $request)
    {
        $this->repository->update($request);
        return redirect()->back()->withSuccess(__('Payment Information Updated Successfully.'));
    }

    public function customStore(Request $request)
    {
        $this->repository->customStore($request);
        return redirect()->back()->withSuccess(__('New Payment Method Added Successfully.'));
    }

    public function customUpdate(Request $request, $id)
    {
        $this->repository->customUpdate($request, $id);
        return redirect()->back()->withSuccess(__('Payment Method Updated Successfully.'));
    }

    public function customDelete($id)
    {
        $this->repository->customDelete($id);
        return redirect()->back()->withSuccess(__('Payment Method Deleted Successfully.'));
    }

}
