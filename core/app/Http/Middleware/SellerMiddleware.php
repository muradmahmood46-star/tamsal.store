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

        if (!Auth::user()->isSeller()) {
            return redirect()->route('user.store.apply')->withErrors(__('You must have an approved store application to access the Seller Dashboard.'));
        }

        if (Auth::user()->isSellerBlocked()) {
            if (!$request->routeIs('seller.blocked') && !$request->routeIs('seller.unblock.submit') && !$request->routeIs('seller.fine.submit') && !$request->routeIs('seller.fine.pay_wallet')) {
                return redirect()->route('seller.blocked');
            }
        } elseif ($request->routeIs('seller.blocked')) {
            return redirect()->route('seller.dashboard');
        }

        return $next($request);
    }
}
