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
        \App\Helpers\Helper::ensureDealsTable();
    }

    public function index()
    {
        return view('front.deals.index', ['deals' => Helper::getActiveDeals()]);
    }

    public function show($slug)
    {
        $slugClean = trim($slug);

        $deal = Deal::with(['dealItems.item.category', 'vendor.seller'])
            ->where(function ($q) use ($slugClean) {
                $q->where('sku', $slugClean)
                  ->orWhere('slug', $slugClean)
                  ->orWhere('id', is_numeric($slugClean) ? (int)$slugClean : 0)
                  ->orWhere('name', 'like', '%' . str_replace('-', ' ', $slugClean) . '%');
            })
            ->first();

        if (!$deal) {
            $words = array_values(array_filter(explode('-', $slugClean), function($w) { return strlen($w) >= 3; }));
            if (!empty($words)) {
                $deal = Deal::with(['dealItems.item.category', 'vendor.seller'])
                    ->where(function($q) use ($words) {
                        foreach ($words as $w) {
                            $q->orWhere('name', 'like', '%' . $w . '%');
                        }
                    })->first();
            }
        }

        if (!$deal) {
            return redirect()->route('front.deal.index')->with('error', __('This bundle is currently unavailable.'));
        }

        $directReviews = $deal->reviews()->where('status', 1)->latest()->get();
        if ($directReviews->isEmpty()) {
            $itemIds = $deal->dealItems->pluck('item_id')->toArray();
            $reviews = !empty($itemIds)
                ? \App\Models\Review::with('user')->whereIn('item_id', $itemIds)->where('status', 1)->latest()->paginate(10)
                : collect();
        } else {
            $reviews = $deal->reviews()->with('user')->where('status', 1)->latest()->paginate(10);
        }

        return view('front.deals.show', compact('deal', 'reviews'));
    }

    public function addToCart(Request $request, $slug)
    {
        $slugClean = trim($slug);

        $deal = Deal::with(['dealItems.item'])
            ->where(function ($q) use ($slugClean) {
                $q->where('sku', $slugClean)
                  ->orWhere('slug', $slugClean)
                  ->orWhere('id', is_numeric($slugClean) ? (int)$slugClean : 0)
                  ->orWhere('name', 'like', '%' . str_replace('-', ' ', $slugClean) . '%');
            })
            ->first();

        if (!$deal) {
            $words = array_values(array_filter(explode('-', $slugClean), function($w) { return strlen($w) >= 3; }));
            if (!empty($words)) {
                $deal = Deal::with(['dealItems.item'])
                    ->where(function($q) use ($words) {
                        foreach ($words as $w) {
                            $q->orWhere('name', 'like', '%' . $w . '%');
                        }
                    })->first();
            }
        }

        if (!$deal || $deal->status == 0) {
            return redirect()->route('front.deal.index')->with('error', __('This bundle is currently unavailable.'));
        }

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
                'qty'                 => (int)($dealItem->quantity ?: 1),
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
                'deal_advance_discount'=> (float)($deal->advance_discount ?? 0),
            ];
        }

        Session::put('cart', $cart);

        if ($request->has('buy_now') || $request->input('action') === 'buy_now') {
            return redirect()->route('front.checkout.billing')->with('success', __('Bundle added to cart! Proceeding to checkout.'));
        }

        return redirect()->route('front.cart')->with('success', __('Bundle added to cart!'));
    }
}
