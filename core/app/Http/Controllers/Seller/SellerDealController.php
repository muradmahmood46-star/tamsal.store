<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Deal;
use App\Models\DealItem;
use App\Models\Item;
use App\Helpers\PriceHelper;
use App\Helpers\ImageHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SellerDealController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'seller']);
        \App\Helpers\Helper::ensureDealsTable();
    }

    public function index()
    {
        $vendorId = Auth::id();
        $deals = Deal::with('dealItems.item')
            ->where('vendor_id', $vendorId)
            ->where('vendor_id', '>', 0)
            ->whereNotNull('vendor_id')
            ->orderBy('id', 'desc')
            ->get();

        return view('seller.deal.index', [
            'deals' => $deals,
        ]);
    }

    public function create()
    {
        $vendorId = Auth::id();
        $items = Item::where('vendor_id', $vendorId)
            ->where('vendor_id', '>', 0)
            ->where('status', 1)
            ->where(function ($query) {
                $query->where('approval_status', 'Approved')
                      ->orWhereNull('approval_status');
            })
            ->where(function ($query) {
                $query->whereNull('is_hidden_by_block')
                      ->orWhere('is_hidden_by_block', 0);
            })
            ->select('id', 'name', 'discount_price', 'previous_price', 'photo', 'thumbnail', 'sku')
            ->orderBy('id', 'desc')
            ->get();

        return view('seller.deal.create', [
            'items' => $items,
        ]);
    }

    public function store(Request $request)
    {
        $vendorId = Auth::id();
        if (!$vendorId) {
            return redirect()->route('user.login');
        }

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

        // Ensure all selected items belong strictly to this vendor
        $selectedItems = Item::whereIn('id', $itemIds)
            ->where('vendor_id', $vendorId)
            ->where('vendor_id', '>', 0)
            ->where('status', 1)
            ->get();

        if ($selectedItems->count() != count($itemIds)) {
            return back()->withInput()->withError(__('You can only select products from your own store.'));
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
        $startDate = Carbon::now();
        $endDate = (clone $startDate)->addDays($durationDays);

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
                'vendor_id' => $vendorId,
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
                'duration_days' => $durationDays,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => 1,
                'orders_count' => 0,
            ]);

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

            return redirect()->route('seller.deal.index')->withSuccess(__('Bundle launched successfully!'));
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Seller deal store error: ' . $e->getMessage(), ['exception' => $e]);
            return back()->withInput()->withError(__('Failed to create bundle: ') . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $vendorId = Auth::id();
        $deal = Deal::where('vendor_id', $vendorId)->where('vendor_id', '>', 0)->with('dealItems')->findOrFail($id);

        $items = Item::where('vendor_id', $vendorId)
            ->where('vendor_id', '>', 0)
            ->where('status', 1)
            ->select('id', 'name', 'discount_price', 'previous_price', 'photo', 'thumbnail', 'sku')
            ->orderBy('id', 'desc')
            ->get();

        $selectedItemIds = $deal->dealItems->pluck('item_id')->toArray();

        return view('seller.deal.edit', [
            'deal' => $deal,
            'items' => $items,
            'selectedItemIds' => $selectedItemIds,
        ]);
    }

    public function update(Request $request, $id)
    {
        $vendorId = Auth::id();
        $deal = Deal::where('vendor_id', $vendorId)->where('vendor_id', '>', 0)->findOrFail($id);

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
            ->where('vendor_id', $vendorId)
            ->where('vendor_id', '>', 0)
            ->where('status', 1)
            ->get();

        if ($selectedItems->count() != count($itemIds)) {
            return back()->withInput()->withError(__('You can only select products from your own store.'));
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
                'duration_days' => $durationDays,
                'end_date' => $endDate,
            ]);

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

            return redirect()->route('seller.deal.index')->withSuccess(__('Bundle updated successfully!'));
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Seller deal update error: ' . $e->getMessage(), ['exception' => $e]);
            return back()->withInput()->withError(__('Failed to update bundle: ') . $e->getMessage());
        }
    }

    public function status($id, $status)
    {
        $vendorId = Auth::id();
        $deal = Deal::where('vendor_id', $vendorId)->where('vendor_id', '>', 0)->findOrFail($id);
        $deal->status = (int) $status;
        $deal->save();

        return redirect()->route('seller.deal.index')->withSuccess(__('Bundle status updated successfully.'));
    }

    public function destroy($id)
    {
        $vendorId = Auth::id();
        $deal = Deal::where('vendor_id', $vendorId)->where('vendor_id', '>', 0)->findOrFail($id);
        if ($deal->photo) {
            ImageHelper::handleDeletedImage($deal, 'photo', 'images');
        }
        $deal->delete();

        return redirect()->route('seller.deal.index')->withSuccess(__('Bundle deleted successfully.'));
    }
}

