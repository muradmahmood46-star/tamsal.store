<?php

namespace App\Http\Controllers\Back;

use App\{
    Models\ShippingService,
    Http\Requests\ShippingServiceRequest,
    Http\Controllers\Controller
};
use App\Models\Currency;

class ShippingServiceController extends Controller
{
    /**
     * Constructor Method.
     *
     * Setting Authentication
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('adminlocalize');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $shipping = ShippingService::where('id', 1)->first();
        if (!$shipping) {
            $shipping = ShippingService::create([
                'id' => 1,
                'title' => 'Free Delivery',
                'price' => 0,
                'minimum_price' => 0,
                'is_condition' => 1,
                'status' => 1
            ]);
        }

        $curr = Currency::where('is_default', 1)->first();

        return view('back.shipping.index', [
            'shipping' => $shipping,
            'curr' => $curr
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return redirect()->route('back.shipping.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ShippingServiceRequest $request)
    {
        return redirect()->route('back.shipping.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(ShippingService $shipping)
    {
        return redirect()->route('back.shipping.index');
    }


    /**
     * Change the status for editing the specified resource.
     *
     * @param  int  $id
     * @param  int  $status
     * @return \Illuminate\Http\Response
     */
    public function status($id,$status)
    {
        ShippingService::find($id)->update(['status' => $status]);
   
        return redirect()->route('back.shipping.index')->withSuccess(__('Status Updated Successfully.'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ShippingServiceRequest $request, $id)
    {
        $shipping = ShippingService::find($id);
        if (!$shipping) {
            $shipping = ShippingService::create([
                'id' => 1,
                'title' => 'Free Delivery',
                'price' => 0,
                'minimum_price' => 0,
                'is_condition' => 1,
                'status' => 1
            ]);
        }

        $curr = Currency::where('is_default', 1)->first();
        $currValue = ($curr && $curr->value > 0) ? $curr->value : 1;

        $is_condition = $request->has('is_condition') ? 1 : 0;
        $status = $is_condition; // Sync status with condition
        $minPrice = $request->filled('minimum_price') ? ($request->minimum_price / $currValue) : 0;

        $shipping->update([
            'title' => 'Free Delivery',
            'price' => 0,
            'is_condition' => $is_condition,
            'status' => $status,
            'minimum_price' => $minPrice
        ]);

        return redirect()->route('back.shipping.index')->withSuccess(__('Free Delivery Offer Updated Successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(ShippingService $shipping)
    {
        return redirect()->route('back.shipping.index');
    }
}
