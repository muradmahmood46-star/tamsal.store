<?php

namespace App\Http\Controllers\Front;

use App\Helpers\Helper;
use App\Helpers\PriceHelper;
use App\Http\Controllers\Controller;
use App\Models\Deal;
use Illuminate\Support\Facades\Session;

class DealController extends Controller
{
    public function __construct()
    {
        $this->middleware('localize');
    }

    public function index()
    {
        return view('front.deals.index', ['deals' => Helper::getActiveDeals()]);
    }

    public function show($slug)
    {
        $deal = Deal::with(['dealItems.item.category', 'vendor'])->where('status', 1)->where('slug', $slug)->firstOrFail();
        return view('front.deals.show', compact('deal'));
    }

    public function addToCart($slug)
    {
        $deal = Deal::with(['dealItems.item'])->where('status', 1)->where('slug', $slug)->firstOrFail();

        $cart = Session::get('cart', []);

        foreach ($deal->dealItems as $dealItem) {
            $item = $dealItem->item;
            if (!$item) continue;

            $cartKey = $item->id . '-deal' . $deal->id;
            $cart[$cartKey] = [
                'options_id'          => [],
                'attribute'           => ['names' => [], 'option_name' => [], 'option_price' => []],
                'attribute_price'     => 0,
                'name'                => $item->name,
                'slug'                => $item->slug,
                'qty'                 => 1,
                'price'               => $dealItem->discounted_price,
                'main_price'          => $dealItem->discounted_price,
                'estimated_profit'    => (float)($item->estimated_profit ?? 0),
                'photo'               => $item->photo,
                'type'                => $item->item_type,
                'item_type'           => $item->item_type,
                'item_l_n'            => null,
                'item_l_k'            => null,
                'deal_id'             => $deal->id,
                'deal_delivery_charge'=> $deal->is_free_delivery ? 0 : $deal->delivery_charge,
                'deal_free_delivery'  => $deal->is_free_delivery,
            ];
        }

        Session::put('cart', $cart);

        return redirect()->route('front.cart')->with('success', __('Bundle added to cart!'));
    }
}
