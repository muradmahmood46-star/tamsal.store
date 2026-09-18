<?php

namespace App\Http\Controllers\Front;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Deal;

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
        $deal = Deal::with(['dealItems.item.category', 'vendor'])->active()->where('slug', $slug)->firstOrFail();

        return view('front.deals.show', compact('deal'));
    }
}
