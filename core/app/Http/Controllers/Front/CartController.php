<?php

namespace App\Http\Controllers\Front;

use App\{
    Models\Item,
    Http\Controllers\Controller,
    Repositories\Front\CartRepository
};
use App\Helpers\PriceHelper;
use App\Models\ShippingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    /**
     * Constructor Method.
     *
     * @param  \App\Repositories\Front\CartRepository $repository
     *
     */
    public function __construct(CartRepository $repository)
    {
        $this->repository = $repository;
        $this->middleware('localize');
    }

    public function index()
    {
        if (Session::has('cart')) {
            $cart = Session::get('cart');
        } else {
            $cart = [];
        }
        return view('front.catalog.cart', [
            'cart' => $cart
        ]);
    }


    public function addToCart(Request $request)
    {

        $msg = $this->repository->store($request);


        if ($request->ajax()) {
            return $msg;
        }
    }

    public function store(Request $request)
    {

        $msg = $this->repository->store($request);
        if (isset($request->addtocart)) {
            Session::flash('success_message', __('Cart Added Successfully'));
            return back();
        }
        return redirect()->route('front.checkout.billing')->withSuccess($msg);
    }

    public function destroy($id = null, Request $request = null)
    {
        if ($id === null || $id === '') {
            $id = request()->get('id', request()->get('key'));
        }

        $cart = Session::get('cart');
        if (is_array($cart) && !empty($cart) && $id !== null && $id !== '') {
            $bundleId = null;
            if (strpos((string) $id, 'bundle-') === 0) {
                $bundleId = substr((string) $id, 7);
            }

            // A bundle is sold as one unit, so removing it must remove every product in that bundle.
            if ($bundleId !== null && $bundleId !== '') {
                foreach ($cart as $key => $item) {
                    if ((string) ($item['deal_id'] ?? '') === (string) $bundleId) {
                        unset($cart[$key]);
                    }
                }
            } else {
                $matchedKey = null;

                // 1. Direct key match
                if (isset($cart[$id])) {
                    $matchedKey = $id;
                }
                // 2. URL decoded match
                elseif (isset($cart[urldecode($id)])) {
                    $matchedKey = urldecode($id);
                }
                // 3. Raw URL decoded match
                elseif (isset($cart[rawurldecode($id)])) {
                    $matchedKey = rawurldecode($id);
                }
                // 4. Loose match (trimming, comparing string/decoded values)
                else {
                    $decodedId = urldecode($id);
                    $rawDecodedId = rawurldecode($id);
                    foreach ($cart as $key => $item) {
                        if (
                            (string)$key === (string)$id ||
                            (string)$key === (string)$decodedId ||
                            (string)$key === (string)$rawDecodedId ||
                            urldecode((string)$key) === (string)$decodedId ||
                            trim((string)$key) === trim((string)$id)
                        ) {
                            $matchedKey = $key;
                            break;
                        }
                    }
                }

                if ($matchedKey !== null) {
                    // Prevent manually crafted URLs from removing just one product from a bundle.
                    if (!empty($cart[$matchedKey]['deal_id'])) {
                        $matchedBundleId = $cart[$matchedKey]['deal_id'];
                        foreach ($cart as $key => $item) {
                            if ((string) ($item['deal_id'] ?? '') === (string) $matchedBundleId) {
                                unset($cart[$key]);
                            }
                        }
                    } else {
                        unset($cart[$matchedKey]);
                    }
                }
            }
        }

        if (is_array($cart) && count($cart) > 0) {
            Session::put('cart', $cart);
        } else {
            Session::forget('cart');
            Session::forget('coupon');
        }

        if (request()->ajax()) {
            return response()->json([
                'status' => true,
                'message' => __('Cart item remove successfully.'),
                'count' => Session::has('cart') ? count(Session::get('cart')) : 0
            ]);
        }

        Session::flash('success', __('Cart item remove successfully.'));
        return back();
    }

    public function promoStore(Request $request)
    {
        return response()->json($this->repository->promoStore($request));
    }

    public function shippingStore(Request $request)
    {
        return redirect()->route('front.checkout');
    }


    public function update($id)
    {
        return view('front.catalog.cart_form', [
            'item' => Item::findOrFail($id),
            'attributes' => Item::findOrFail($id)->attributes,
            'cart_item' => Session::get('cart')[$id],
        ]);
    }


    public function shippingCharge(Request $request)
    {

        $charges = [];
        $items = [];
        foreach ($request->user_id as $data) {
            $check = explode('|', $data);
            $charges[] = $check[0];
            $items[] = $check[1];
        }
        $cart = Session::get('cart');
        $delivery_amount = 0;
        foreach ($charges as $index => $charge) {
            if ($charge != 0) {
                $vendor_charge = Item::findOrFail($items[$index])->user->shipping->price;
                $delivery_amount += $vendor_charge;
                $cart[$items[$index]]['delivery_charge'] = $vendor_charge;
            } else {
                $cart[$items[$index]]['delivery_charge'] = 0;
            }
        }

        Session::put('cart', $cart);

        return response()->json(['delivery' => PriceHelper::setPrice($delivery_amount), 'main' => $delivery_amount]);
    }


    public function headerCartLoad()
    {
        return view('includes.header_cart');
    }
    public function CartLoad()
    {
        return view('includes.cart');
    }

    public function cartClear()
    {
        Session::forget('cart');
        Session::forget('coupon');
        Session::flash('success', __('Cart clear successfully'));
        return back();
    }

    public function promoDelete()
    {
        Session::forget('coupon');
        Session::flash('success', __('Promo code remove successfully'));
        return back();
    }
}
