<?php

namespace App\Http\Controllers\Back;

use App\Helpers\PriceHelper;
use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Setting;
use Illuminate\Http\Request;

class DeliveryCalculationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('adminlocalize');
    }

    public function index()
    {
        PriceHelper::ensureDeliveryCalcColumns();

        $setting = Setting::first();
        if (!$setting) {
            $setting = new Setting();
        }

        $curr = Currency::where('is_default', 1)->first();

        return view('back.delivery_calculation.index', [
            'setting' => $setting,
            'curr' => $curr,
        ]);
    }

    public function update(Request $request)
    {
        PriceHelper::ensureDeliveryCalcColumns();

        $request->validate([
            'delivery_second_item_divisor' => 'required|numeric|gt:0',
            'delivery_subsequent_item_divisor' => 'required|numeric|gt:0',
        ], [
            'delivery_second_item_divisor.required' => __('Second Item Divisor is required.'),
            'delivery_second_item_divisor.numeric' => __('Second Item Divisor must be a valid number.'),
            'delivery_second_item_divisor.gt' => __('Second Item Divisor must be greater than zero.'),
            'delivery_subsequent_item_divisor.required' => __('Third and Subsequent Items Divisor is required.'),
            'delivery_subsequent_item_divisor.numeric' => __('Third and Subsequent Items Divisor must be a valid number.'),
            'delivery_subsequent_item_divisor.gt' => __('Third and Subsequent Items Divisor must be greater than zero.'),
        ]);

        $setting = Setting::first();
        if (!$setting) {
            $setting = new Setting();
        }

        $isEnabled = $request->has('is_delivery_calc_enabled') && (string)$request->input('is_delivery_calc_enabled') !== '0' ? 1 : 0;

        $setting->is_delivery_calc_enabled = $isEnabled;
        $setting->delivery_second_item_divisor = (float) $request->input('delivery_second_item_divisor');
        $setting->delivery_subsequent_item_divisor = (float) $request->input('delivery_subsequent_item_divisor');
        $setting->save();

        return redirect()->route('back.delivery_calculation.index')->withSuccess(__('Delivery Fee Calculation Settings Updated Successfully.'));
    }
}
