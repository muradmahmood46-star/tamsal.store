<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SellerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('user.login');
        }

        $seller = \App\Models\Seller::where('user_id', Auth::id())->first();
        if (!Auth::user()->isSeller() || !$seller) {
            return redirect()->route('user.store.apply')->withErrors(__('You must have an approved store application to access the Seller Dashboard.'));
        }

        if (Auth::user()->isSellerBlocked()) {
            if (!$request->routeIs('seller.blocked') && !$request->routeIs('seller.unblock.submit') && !$request->routeIs('seller.fine.submit') && !$request->routeIs('seller.fine.pay_wallet')) {
                return redirect()->route('seller.blocked');
            }
        } elseif ($request->routeIs('seller.blocked')) {
            return redirect()->route('seller.dashboard');
        }

        // Plan Expired Restriction for Time-Based Mode (Product/Deal Add/Edit & Promotions Blocked)
        if ($seller->isTimeBasedMode() && $seller->isPlanExpired()) {
            $restrictedPatterns = [
                'seller.item.create',
                'seller.item.add',
                'seller.item.store',
                'seller.item.edit',
                'seller.item.update',
                'seller.item.status',
                'seller.item.destroy',
                'seller.item.stock.out',
                'seller.item.galleries.update',
                'seller.item.gallery.delete',
                'seller.deal.create',
                'seller.deal.store',
                'seller.deal.edit',
                'seller.deal.update',
                'seller.deal.status',
                'seller.deal.destroy',
                'seller.promotion.purchase',
                'seller.bulk.product.index',
                'seller.csv.export',
                'seller.category.*',
                'seller.subcategory.*',
            ];

            foreach ($restrictedPatterns as $pattern) {
                if ($request->routeIs($pattern)) {
                    $setting = \App\Models\Setting::first();
                    $planCharge = (float)($setting->vendor_plan_charge ?? 1000.00);
                    $curr = \App\Helpers\PriceHelper::adminCurrency();
                    return redirect()->route('seller.dashboard')->withErrors(
                        __('Your plan has expired. Please deposit at least :curr :amount to continue managing your store.', [
                            'curr' => $curr,
                            'amount' => number_format($planCharge, 2)
                        ])
                    );
                }
            }
        }

        return $next($request);
    }
}
