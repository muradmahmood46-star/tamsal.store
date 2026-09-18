<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Deal;
use App\Models\DealItem;
use App\Models\Item;
use App\Helpers\PriceHelper;
use App\Helpers\ImageHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DealController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('adminlocalize');
        \App\Helpers\Helper::ensureDealsTable();
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
            'photo' => 'nullable|mimes:jpeg,jpg,png,svg,webp,gif,bmp,tiff,tif,avif,ico,jfif,heic,heif|max:20480',
            'item_ids' => 'required|array|min:2',
            'item_ids.*' => 'required|exists:items,id',
            'discount_type' => 'required|in:fixed,percent',
            'discount_value' => 'required|numeric|min:0.01',
            'delivery_charge' => 'nullable|numeric|min:0',
            'is_free_delivery' => 'nullable|boolean',
            'advance_discount' => 'nullable|numeric|min:0',
            'duration_days' => 'required|integer|min:1|max:20',
        ], [
            'photo.mimes' => __('The photo must be a valid image file (jpeg, jpg, png, svg, webp, gif, jfif, etc.).'),
            'photo.max' => __('The photo size may not be greater than 20MB.'),
            'photo.uploaded' => __('Failed to upload photo. Please check image format and size (max 20MB).'),
        ]);

        $itemIds = array_values(array_unique($request->item_ids));
        if (count($itemIds) < 2) {
            return back()->withInput()->withError(__('Please select at least 2 distinct products for this deal.'));
        }

        // Ensure all items belong to Admin
        $selectedItems = Item::whereIn('id', $itemIds)
            ->where(function ($query) {
                $query->where('vendor_id', 0)
                      ->orWhereNull('vendor_id');
            })
            ->get();

        if ($selectedItems->count() != count($itemIds)) {
            return back()->withInput()->withError(__('You can only select admin-listed products for this deal.'));
        }

        $totalOriginalPrice = $selectedItems->sum(function ($item) {
            $p = $item->discount_price > 0 ? $item->discount_price : $item->previous_price;
            return PriceHelper::parsePrice($p);
        });

        if ($totalOriginalPrice <= 0) {
            return back()->withInput()->withError(__('Total price of selected products must be greater than zero.'));
        }

        $discountType = $request->discount_type;
        $discountValue = $discountType === 'fixed'
            ? PriceHelper::convertPrice($request->discount_value)
            : (float) $request->discount_value;
        $isFreeDelivery = $request->boolean('is_free_delivery');
        $deliveryCharge = $isFreeDelivery ? 0 : PriceHelper::convertPrice($request->delivery_charge ?? 0);
        $advanceDiscount = PriceHelper::convertPrice($request->advance_discount ?? 0);

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

        $durationDays = (int) $request->duration_days;
        $startDate = Carbon::now();
        $endDate = (clone $startDate)->addDays($durationDays);

        // Generate unique slug
        $baseSlug = Str::slug($request->name) ?: 'bundle-' . time();
        $slug = $baseSlug;
        $counter = 1;
        while (Deal::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        try {
            DB::beginTransaction();

            $photo = null;
            if ($request->hasFile('photo')) {
                $photo = ImageHelper::handleUploadedImage($request->file('photo'), 'images');
            }

            $deal = Deal::create([
                'vendor_id' => 0,
                'name' => $request->name,
                'slug' => $slug,
                'photo' => $photo,
                'description' => $request->description,
                'discount_type' => $discountType,
                'discount_value' => $discountValue,
                'original_price' => $totalOriginalPrice,
                'discounted_price' => $finalDiscountedPrice,
                'delivery_charge' => $deliveryCharge,
                'is_free_delivery' => $isFreeDelivery,
                'advance_discount' => $advanceDiscount,
                'duration_days' => $durationDays,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => 1,
                'orders_count' => 0,
            ]);

            // Insert deal items with proportional discounted price
            foreach ($selectedItems as $item) {
                $itemOriginalPrice = PriceHelper::parsePrice($item->discount_price > 0 ? $item->discount_price : $item->previous_price);
                $itemDiscount = $itemOriginalPrice * $discountRatio;
                $itemDiscountedPrice = max(0, round($itemOriginalPrice - $itemDiscount, 2));

                DealItem::create([
                    'deal_id' => $deal->id,
                    'item_id' => $item->id,
                    'original_price' => $itemOriginalPrice,
                    'discounted_price' => $itemDiscountedPrice,
                ]);
            }

            DB::commit();

            return redirect()->route('back.deal.index')->withSuccess(__('Bundle launched successfully!'));
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Deal store error: ' . $e->getMessage(), ['exception' => $e]);
            return back()->withInput()->withError(__('Failed to create bundle: ') . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $deal = Deal::with('dealItems')->findOrFail($id);

        $items = Item::where('status', 1)
            ->where(function ($query) use ($deal) {
                if ($deal->vendor_id > 0) {
                    $query->where('vendor_id', $deal->vendor_id);
                } else {
                    $query->where('vendor_id', 0)->orWhereNull('vendor_id');
                }
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
            'photo' => 'nullable|mimes:jpeg,jpg,png,svg,webp,gif,bmp,tiff,tif,avif,ico,jfif,heic,heif|max:20480',
            'item_ids' => 'required|array|min:2',
            'item_ids.*' => 'required|exists:items,id',
            'discount_type' => 'required|in:fixed,percent',
            'discount_value' => 'required|numeric|min:0.01',
            'delivery_charge' => 'nullable|numeric|min:0',
            'is_free_delivery' => 'nullable|boolean',
            'advance_discount' => 'nullable|numeric|min:0',
            'duration_days' => 'required|integer|min:1|max:20',
        ], [
            'photo.mimes' => __('The photo must be a valid image file (jpeg, jpg, png, svg, webp, gif, jfif, etc.).'),
            'photo.max' => __('The photo size may not be greater than 20MB.'),
            'photo.uploaded' => __('Failed to upload photo. Please check image format and size (max 20MB).'),
        ]);

        $itemIds = array_values(array_unique($request->item_ids));
        if (count($itemIds) < 2) {
            return back()->withInput()->withError(__('Please select at least 2 distinct products for this deal.'));
        }

        $selectedItems = Item::whereIn('id', $itemIds)
            ->where(function ($query) use ($deal) {
                if ($deal->vendor_id > 0) {
                    $query->where('vendor_id', $deal->vendor_id);
                } else {
                    $query->where('vendor_id', 0)->orWhereNull('vendor_id');
                }
            })
            ->get();

        if ($selectedItems->count() != count($itemIds)) {
            return back()->withInput()->withError(__('Invalid product selection. All products must be eligible.'));
        }

        $totalOriginalPrice = $selectedItems->sum(function ($item) {
            $p = $item->discount_price > 0 ? $item->discount_price : $item->previous_price;
            return PriceHelper::parsePrice($p);
        });

        if ($totalOriginalPrice <= 0) {
            return back()->withInput()->withError(__('Total price of selected products must be greater than zero.'));
        }

        $discountType = $request->discount_type;
        $discountValue = $discountType === 'fixed'
            ? PriceHelper::convertPrice($request->discount_value)
            : (float) $request->discount_value;
        $isFreeDelivery = $request->boolean('is_free_delivery');
        $deliveryCharge = $isFreeDelivery ? 0 : PriceHelper::convertPrice($request->delivery_charge ?? 0);
        $advanceDiscount = PriceHelper::convertPrice($request->advance_discount ?? 0);

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

        $durationDays = (int) $request->duration_days;
        $endDate = Carbon::now()->addDays($durationDays);

        try {
            DB::beginTransaction();

            $photo = $deal->photo;
            if ($request->hasFile('photo')) {
                $photo = ImageHelper::handleUploadedImage($request->file('photo'), 'images', $deal->photo);
            }

            $deal->update([
                'name' => $request->name,
                'photo' => $photo,
                'description' => $request->description,
                'discount_type' => $discountType,
                'discount_value' => $discountValue,
                'original_price' => $totalOriginalPrice,
                'discounted_price' => $finalDiscountedPrice,
                'delivery_charge' => $deliveryCharge,
                'is_free_delivery' => $isFreeDelivery,
                'advance_discount' => $advanceDiscount,
                'duration_days' => $durationDays,
                'end_date' => $endDate,
            ]);

            // Sync deal items
            DealItem::where('deal_id', $deal->id)->delete();
            foreach ($selectedItems as $item) {
                $itemOriginalPrice = PriceHelper::parsePrice($item->discount_price > 0 ? $item->discount_price : $item->previous_price);
                $itemDiscount = $itemOriginalPrice * $discountRatio;
                $itemDiscountedPrice = max(0, round($itemOriginalPrice - $itemDiscount, 2));

                DealItem::create([
                    'deal_id' => $deal->id,
                    'item_id' => $item->id,
                    'original_price' => $itemOriginalPrice,
                    'discounted_price' => $itemDiscountedPrice,
                ]);
            }

            DB::commit();

            return redirect()->route('back.deal.index')->withSuccess(__('Bundle updated successfully!'));
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Deal update error: ' . $e->getMessage(), ['exception' => $e]);
            return back()->withInput()->withError(__('Failed to update bundle: ') . $e->getMessage());
        }
    }

    public function status($id, $status)
    {
        $deal = Deal::findOrFail($id);
        $deal->status = (int) $status;
        $deal->save();

        return redirect()->route('back.deal.index')->withSuccess(__('Bundle status updated successfully.'));
    }

    public function destroy($id)
    {
        $deal = Deal::findOrFail($id);
        if ($deal->photo) {
            ImageHelper::handleDeletedImage($deal, 'photo', 'images');
        }
        $deal->delete();

        return redirect()->route('back.deal.index')->withSuccess(__('Bundle deleted successfully.'));
    }
}

