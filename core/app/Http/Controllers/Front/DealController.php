<?php

namespace App\Http\Controllers\Front;

use App\Helpers\Helper;
use App\Helpers\PriceHelper;
use App\Http\Controllers\Controller;
use App\Models\Deal;
use Illuminate\Http\Request;
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

    public function addToCart(Request $request, $slug)
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
                'deal_name'           => $deal->name,
                'deal_delivery_charge'=> (bool)$deal->is_free_delivery ? 0 : (float)$deal->delivery_charge,
                'deal_free_delivery'  => (bool)$deal->is_free_delivery,
            ];
        }

        Session::put('cart', $cart);

        if ($request->has('buy_now') || $request->input('action') === 'buy_now') {
            return redirect()->route('front.checkout.billing')->with('success', __('Bundle added to cart! Proceeding to checkout.'));
        }

        return redirect()->route('front.cart')->with('success', __('Bundle added to cart!'));
    }
}
