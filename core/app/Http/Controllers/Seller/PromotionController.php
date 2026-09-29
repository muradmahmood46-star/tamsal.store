<?php

namespace App\Http\Controllers\Seller;

use App\Helpers\PriceHelper;
use App\Http\Controllers\Controller;
use App\Models\Deal;
use App\Models\Item;
use App\Models\ItemPromotion;
use App\Models\PromotionPlan;
use App\Models\PromotionTag;
use App\Models\Seller;
use App\Models\Setting;
use App\Models\VendorTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PromotionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'seller']);
    }

    public function index()
    {
        ItemPromotion::ensureTable();

        $user = Auth::user();
        $vendorId = $user->id;
        $seller = Seller::where('user_id', $vendorId)->first();
        $setting = Setting::first();

        $products = Item::where('vendor_id', $vendorId)->latest()->get();
        $bundles = Deal::where('vendor_id', $vendorId)->latest()->get();
        $tags = PromotionTag::where('status', 1)->get();
        $plans = PromotionPlan::where('status', 1)->orderBy('days', 'asc')->get();

        $promotionsHistory = ItemPromotion::with(['product', 'deal'])
            ->where('user_id', $vendorId)
            ->latest()
            ->paginate(15);

        return view('seller.promotion.index', compact(
            'user',
            'seller',
            'setting',
            'products',
            'bundles',
            'tags',
            'plans',
            'promotionsHistory'
        ));
    }

    public function purchase(Request $request)
    {
        ItemPromotion::ensureTable();

        $user = Auth::user();
        $vendorId = $user->id;
        $seller = Seller::where('user_id', $vendorId)->firstOrFail();

        $promotionsData = $request->input('promotions', []);

        if (empty($promotionsData) || !is_array($promotionsData)) {
            return redirect()->back()->withErrors(__('Please select at least one product or bundle to promote.'));
        }

        $validatedItems = [];
        $totalCost = 0.0;

        foreach ($promotionsData as $key => $data) {
            if (empty($data['selected']) || $data['selected'] != 1) {
                continue;
            }

            $type = $data['type'] ?? 'product';
            $itemId = (int)($data['id'] ?? 0);
            $tagId = (int)($data['tag_id'] ?? 0);
            $planId = (int)($data['plan_id'] ?? 0);

            if (!$itemId || !$tagId || !$planId) {
                return redirect()->back()->withErrors(__('Please select both a highlight tag and duration for all selected items.'));
            }

            $tag = PromotionTag::where('id', $tagId)->where('status', 1)->first();
            if (!$tag) {
                return redirect()->back()->withErrors(__('Invalid highlight tag selected.'));
            }

            $plan = PromotionPlan::where('id', $planId)->where('status', 1)->first();
            if (!$plan) {
                return redirect()->back()->withErrors(__('Invalid promotion duration plan selected.'));
            }

            if ($type === 'bundle' || $type === 'deal') {
                $item = Deal::where('id', $itemId)->where('vendor_id', $vendorId)->first();
                if (!$item) {
                    return redirect()->back()->withErrors(__('Selected bundle deal does not belong to your store.'));
                }
            } else {
                $item = Item::where('id', $itemId)->where('vendor_id', $vendorId)->first();
                if (!$item) {
                    return redirect()->back()->withErrors(__('Selected product does not belong to your store.'));
                }
            }

            $validatedItems[] = [
                'type' => ($type === 'bundle' || $type === 'deal') ? 'bundle' : 'product',
                'item' => $item,
                'tag' => $tag,
                'plan' => $plan,
                'price' => (float)$plan->price,
                'days' => (int)$plan->days,
            ];

            $totalCost += (float)$plan->price;
        }

        if (empty($validatedItems)) {
            return redirect()->back()->withErrors(__('Please select at least one product or bundle with a highlight tag and duration.'));
        }

        $currentBalance = (float)($seller->balance ?? 0);

        // Check Wallet Balance
        if ($currentBalance < $totalCost) {
            $currency = PriceHelper::adminCurrency();
            $diff = $totalCost - $currentBalance;

            return redirect()->back()->withInput()->with('insufficient_balance', [
                'message' => __('Insufficient amount. Deposit first.'),
                'required' => $totalCost,
                'available' => $currentBalance,
                'shortage' => $diff,
                'deposit_url' => route('seller.wallet.index'),
            ]);
        }

        // Deduct from wallet & activate promotions
        $runningBalance = $currentBalance;

        foreach ($validatedItems as $v) {
            $item = $v['item'];
            $tag = $v['tag'];
            $plan = $v['plan'];
            $type = $v['type'];
            $price = $v['price'];
            $days = $v['days'];

            $startsAt = Carbon::now();
            $expiresAt = Carbon::now()->addDays($days);

            // Update product/bundle model
            $item->update([
                'is_promoted' => 1,
                'promotion_tag' => $tag->name,
                'promotion_tag_id' => $tag->id,
                'promotion_days' => $days,
                'promotion_price' => $price,
                'promotion_starts_at' => $startsAt,
                'promotion_expires_at' => $expiresAt,
            ]);

            // Log ItemPromotion history
            ItemPromotion::create([
                'seller_id' => $seller->id,
                'user_id' => $vendorId,
                'item_type' => $type,
                'item_id' => $item->id,
                'tag_name' => $tag->name,
                'tag_id' => $tag->id,
                'days' => $days,
                'price' => $price,
                'starts_at' => $startsAt,
                'expires_at' => $expiresAt,
                'status' => 'active',
            ]);

            // Deduct individual price
            $runningBalance -= $price;
            $seller->balance = $runningBalance;
            $seller->save();

            // Log in vendor wallet transactions
            $curr = PriceHelper::adminCurrency();
            VendorTransaction::create([
                'seller_id' => $seller->id,
                'user_id' => $vendorId,
                'type' => 'promotion_fee',
                'amount' => -$price,
                'balance_after' => $runningBalance,
                'details' => __('Promotion Badge — :tag — :item — :curr :price — :days days', [
                    'tag' => $tag->name,
                    'item' => $item->name,
                    'curr' => $curr,
                    'price' => number_format($price, 2),
                    'days' => $days
                ]),
                'status' => 'completed'
            ]);
        }

        return redirect()->route('seller.promotion.index')->withSuccess(
            __('Promotion badge(s) activated successfully! Total :count item(s) are now highlighted on the store for their configured duration.', [
                'count' => count($validatedItems)
            ])
        );
    }
}
