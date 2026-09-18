<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Deal;
use App\Models\DealItem;
use App\Models\Item;
use App\Helpers\PriceHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DealController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('adminlocalize');
    }

    public function index()
    {
        $deals = Deal::with(['dealItems.item', 'vendor'])
            ->orderBy('id', 'desc')
            ->get();

        return view('back.deal.index', [
            'deals' => $deals,
        ]);
    }

    public function create()
    {
        $items = Item::where('status', 1)
            ->where(function ($query) {
                $query->where('vendor_id', 0)
                      ->orWhereNull('vendor_id');
            })
            ->where(function ($query) {
                $query->where('approval_status', 'Approved')
                      ->orWhereNull('approval_status');
            })
            ->select('id', 'name', 'discount_price', 'previous_price', 'photo', 'thumbnail', 'sku')
            ->orderBy('id', 'desc')
            ->get();

        return view('back.deal.create', [
            'items' => $items,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'item_ids' => 'required|array|min:1',
            'item_ids.*' => 'required|exists:items,id',
            'discount_type' => 'required|in:fixed,percent',
            'discount_value' => 'required|numeric|min:0.01',
            'end_date' => 'required|date|after:now|before_or_equal:' . Carbon::now()->addDays(20)->format('Y-m-d H:i:s'),
        ]);

        // Ensure all items belong to Admin
        $selectedItems = Item::whereIn('id', $request->item_ids)
            ->where(function ($query) {
                $query->where('vendor_id', 0)
                      ->orWhereNull('vendor_id');
            })
            ->get();

        if ($selectedItems->count() != count($request->item_ids)) {
            return back()->withInput()->withError(__('You can only select admin-listed products for this deal.'));
        }

        $totalOriginalPrice = $selectedItems->sum(function ($item) {
            return $item->discount_price > 0 ? $item->discount_price : $item->previous_price;
        });

        if ($totalOriginalPrice <= 0) {
            return back()->withInput()->withError(__('Total price of selected products must be greater than zero.'));
        }

        $discountType = $request->discount_type;
        $discountValue = $request->discount_type === 'fixed'
            ? PriceHelper::convertPrice($request->discount_value)
            : (float) $request->discount_value;

        if ($discountType === 'percent') {
            if ($discountValue >= 100) {
                return back()->withInput()->withError(__('Discount percentage must be less than 100%.'));
            }
            $discountAmount = ($totalOriginalPrice * $discountValue) / 100;
        } else {
            if ($discountValue >= $totalOriginalPrice) {
                return back()->withInput()->withError(__('Fixed discount amount must be less than the total price.'));
            }
            $discountAmount = $discountValue;
        }

        $finalDiscountedPrice = max(0, round($totalOriginalPrice - $discountAmount, 2));
        $discountRatio = $totalOriginalPrice > 0 ? ($discountAmount / $totalOriginalPrice) : 0;

        $startDate = Carbon::now();
        $endDate = Carbon::parse($request->end_date);
        $durationDays = max(1, (int) ceil($startDate->diffInMinutes($endDate) / 1440));

        // Generate unique slug
        $baseSlug = Str::slug($request->name);
        $slug = $baseSlug;
        $counter = 1;
        while (Deal::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        $deal = Deal::create([
            'vendor_id' => 0,
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'discount_type' => $discountType,
            'discount_value' => $discountValue,
            'original_price' => $totalOriginalPrice,
            'discounted_price' => $finalDiscountedPrice,
            'duration_days' => $durationDays,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => 1,
            'orders_count' => 0,
        ]);

        // Insert deal items with proportional discounted price
        foreach ($selectedItems as $item) {
            $itemOriginalPrice = $item->discount_price > 0 ? $item->discount_price : $item->previous_price;
            $itemDiscount = $itemOriginalPrice * $discountRatio;
            $itemDiscountedPrice = max(0, round($itemOriginalPrice - $itemDiscount, 2));

            DealItem::create([
                'deal_id' => $deal->id,
                'item_id' => $item->id,
                'original_price' => $itemOriginalPrice,
                'discounted_price' => $itemDiscountedPrice,
            ]);
        }

        return redirect()->route('back.deal.index')->withSuccess(__('Deal launched successfully!'));
    }

    public function edit($id)
    {
        $deal = Deal::with('dealItems')->findOrFail($id);
        $items = Item::where('status', 1)
            ->where(function ($query) {
                $query->where('vendor_id', 0)
                      ->orWhereNull('vendor_id');
            })
            ->select('id', 'name', 'discount_price', 'previous_price', 'photo', 'thumbnail', 'sku')
            ->orderBy('id', 'desc')
            ->get();

        $selectedItemIds = $deal->dealItems->pluck('item_id')->toArray();

        return view('back.deal.edit', [
            'deal' => $deal,
            'items' => $items,
            'selectedItemIds' => $selectedItemIds,
        ]);
    }

    public function update(Request $request, $id)
    {
        $deal = Deal::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'item_ids' => 'required|array|min:1',
            'item_ids.*' => 'required|exists:items,id',
            'discount_type' => 'required|in:fixed,percent',
            'discount_value' => 'required|numeric|min:0.01',
            'end_date' => 'required|date|after:now|before_or_equal:' . Carbon::now()->addDays(20)->format('Y-m-d H:i:s'),
        ]);

        $selectedItems = Item::whereIn('id', $request->item_ids)
            ->where(function ($query) use ($deal) {
                if ($deal->vendor_id > 0) {
                    $query->where('vendor_id', $deal->vendor_id);
                } else {
                    $query->where('vendor_id', 0)->orWhereNull('vendor_id');
                }
            })
            ->get();

        if ($selectedItems->count() != count($request->item_ids)) {
            return back()->withInput()->withError(__('Invalid product selection.'));
        }

        $totalOriginalPrice = $selectedItems->sum(function ($item) {
            return $item->discount_price > 0 ? $item->discount_price : $item->previous_price;
        });

        $discountType = $request->discount_type;
        $discountValue = $request->discount_type === 'fixed'
            ? PriceHelper::convertPrice($request->discount_value)
            : (float) $request->discount_value;

        if ($discountType === 'percent') {
            if ($discountValue >= 100) {
                return back()->withInput()->withError(__('Discount percentage must be less than 100%.'));
            }
            $discountAmount = ($totalOriginalPrice * $discountValue) / 100;
        } else {
            if ($discountValue >= $totalOriginalPrice) {
                return back()->withInput()->withError(__('Fixed discount amount must be less than total price.'));
            }
            $discountAmount = $discountValue;
        }

        $finalDiscountedPrice = max(0, round($totalOriginalPrice - $discountAmount, 2));
        $discountRatio = $totalOriginalPrice > 0 ? ($discountAmount / $totalOriginalPrice) : 0;

        $startDate = $deal->start_date ?: Carbon::now();
        $endDate = Carbon::parse($request->end_date);
        $durationDays = max(1, (int) ceil(Carbon::now()->diffInMinutes($endDate) / 1440));

        $deal->update([
            'name' => $request->name,
            'description' => $request->description,
            'discount_type' => $discountType,
            'discount_value' => $discountValue,
            'original_price' => $totalOriginalPrice,
            'discounted_price' => $finalDiscountedPrice,
            'duration_days' => $durationDays,
            'end_date' => $endDate,
        ]);

        // Sync deal items
        DealItem::where('deal_id', $deal->id)->delete();
        foreach ($selectedItems as $item) {
            $itemOriginalPrice = $item->discount_price > 0 ? $item->discount_price : $item->previous_price;
            $itemDiscount = $itemOriginalPrice * $discountRatio;
            $itemDiscountedPrice = max(0, round($itemOriginalPrice - $itemDiscount, 2));

            DealItem::create([
                'deal_id' => $deal->id,
                'item_id' => $item->id,
                'original_price' => $itemOriginalPrice,
                'discounted_price' => $itemDiscountedPrice,
            ]);
        }

        return redirect()->route('back.deal.index')->withSuccess(__('Deal updated successfully!'));
    }

    public function status($id, $status)
    {
        $deal = Deal::findOrFail($id);
        $deal->status = (int) $status;
        $deal->save();

        return redirect()->route('back.deal.index')->withSuccess(__('Deal status updated successfully.'));
    }

    public function destroy($id)
    {
        $deal = Deal::findOrFail($id);
        $deal->delete();

        return redirect()->route('back.deal.index')->withSuccess(__('Deal deleted successfully.'));
    }
}
