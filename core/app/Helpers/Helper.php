<?php

namespace App\Helpers;

use App\Models\Page;
use App\Models\Item;
use App\Models\Order;

class Helper
{


    public static function renderStarRating($rating, $maxRating = 5)
    {
        if ($rating instanceof \App\Models\Item) {
            $rating = $rating->rating;
        } elseif (is_numeric($rating)) {
            $rating = (float) $rating;
        } else {
            $rating = 0.0;
        }

        $rating = (float) $rating;
        $rating = $rating <= $maxRating ? $rating : $maxRating;
        $rating = max(0, $rating);

        $fullStar = "<i class='fas fa-star filled text-warning' style='color: #f59e0b !important;'></i>";
        $halfStar = "<i class='fas fa-star-half-alt filled text-warning' style='color: #f59e0b !important;'></i>";
        $emptyStar = "<i class='far fa-star text-muted' style='color: #cbd5e1 !important;'></i>";

        $fullStarCount = (int) floor($rating);
        $decimal = $rating - $fullStarCount;
        $halfStarCount = 0;
        if ($decimal >= 0.25 && $decimal <= 0.75) {
            $halfStarCount = 1;
        } elseif ($decimal > 0.75) {
            $fullStarCount++;
        }
        $emptyStarCount = max(0, $maxRating - $fullStarCount - $halfStarCount);

        $html = str_repeat($fullStar, $fullStarCount);
        $html .= str_repeat($halfStar, $halfStarCount);
        $html .= str_repeat($emptyStar, $emptyStarCount);

        return $html;
    }
    
    public static function getHref($link){
          
     
        $href = "#";

        if ($link["type"] == 'home') {
            $href = route('front.index');
        } 
        else if ($link["type"] == 'shop') {
            $href = route('front.catalog');
        } 
        else if ($link["type"] == 'campaign') {
            $href = route('front.campaign');
        } 
        else if ($link["type"] == 'brand') {
            $href = route('front.brand');
        } 
        else if ($link["type"] == 'blog') {
            $href = route('front.blog');
        }
        else if ($link["type"] == 'faq') {
            $href = route('front.faq');
        } 
        else if ($link["type"] == 'contact') {
            $href = route('front.contact');
        } 
        else {
            $pageid = (int)$link["type"];
            $page = Page::find($pageid);
            if (!empty($page)) {
                $href = route('front.page', [$page->slug]);
            } else {
                $href = '#';
            }
        }

        return $href;
        
    }

    public static function createMenu($arr){
        echo '<ul style="z-index: 0;" class="submenu">';
        foreach ($arr["children"] as $el) {

            // determine the href
            $href = Helper::getHref($el);

            echo '<li>';
            echo '<a  href="'.$href.'" target="'.$el["target"].'">'.$el["text"].'</a>';
            if (array_key_exists("children", $el)) {
                Helper::createMenu($el);
            }
            echo '</li>';
        }
        echo '</ul>';
    }

    /**
     * Get most selling products with proportional 30% Admin and 70% Vendor distribution.
     *
     * @param int|null $limit (e.g. 4 for homepage, null for all products on View All)
     * @return \Illuminate\Support\Collection
     */
    public static function getMostSellingProducts($limit = null)
    {
        // 1. Calculate sales count (total sold units) for all items from non-canceled orders
        $orders = Order::where('order_status', '!=', 'Canceled')
            ->select('id', 'cart')
            ->get();

        $itemSales = [];
        foreach ($orders as $order) {
            $cart = json_decode($order->cart, true);
            if (is_array($cart)) {
                foreach ($cart as $key => $cartItem) {
                    $itemId = (int) explode('-', $key)[0];
                    if ($itemId > 0) {
                        $qty = isset($cartItem['qty']) ? (int) $cartItem['qty'] : 1;
                        $itemSales[$itemId] = ($itemSales[$itemId] ?? 0) + $qty;
                    }
                }
            }
        }

        // 2. Fetch active, approved items that are not blocked/hidden
        $allItems = Item::with(['category', 'tax'])
            ->where('status', 1)
            ->where(function ($query) {
                $query->where('approval_status', 'Approved')
                    ->orWhereNull('approval_status');
            })
            ->where(function ($query) {
                $query->whereNull('is_hidden_by_block')
                    ->orWhere('is_hidden_by_block', 0);
            })
            ->get();

        // 3. Attach calculated sales_count to each item
        foreach ($allItems as $item) {
            $item->sales_count = $itemSales[$item->id] ?? 0;
        }

        // 4. Split into Admin items (vendor_id == 0 or null) and Vendor items (vendor_id > 0)
        // Sort each pool descending by sales_count, then by id descending
        $adminItems = $allItems->filter(function ($item) {
            return empty($item->vendor_id) || $item->vendor_id == 0;
        })->sort(function ($a, $b) {
            if ($b->sales_count !== $a->sales_count) {
                return $b->sales_count <=> $a->sales_count;
            }
            return $b->id <=> $a->id;
        })->values();

        $vendorItems = $allItems->filter(function ($item) {
            return !empty($item->vendor_id) && $item->vendor_id > 0;
        })->sort(function ($a, $b) {
            if ($b->sales_count !== $a->sales_count) {
                return $b->sales_count <=> $a->sales_count;
            }
            return $b->id <=> $a->id;
        })->values();

        // 5. If limit is specified (e.g. 8 for homepage, 4, etc.):
        // 30% Admin and 70% Vendor, max $limit total
        if ($limit && $limit > 0 && $limit <= 20) {
            $adminCount = $adminItems->count();
            $vendorCount = $vendorItems->count();

            $targetAdmin = $adminCount > 0 ? max(1, (int) round($limit * 0.30)) : 0;
            $targetVendor = $limit - $targetAdmin;

            // Balance if one pool has fewer items than targets
            if ($vendorCount < $targetVendor) {
                $targetVendor = $vendorCount;
                $targetAdmin = min($adminCount, $limit - $targetVendor);
            }
            if ($adminCount < $targetAdmin) {
                $targetAdmin = $adminCount;
                $targetVendor = min($vendorCount, $limit - $targetAdmin);
            }

            $adminSlice = $adminItems->take($targetAdmin);
            $vendorSlice = $vendorItems->take($targetVendor);

            // Merge and sort overall by sales_count descending
            $merged = $adminSlice->concat($vendorSlice)->sort(function ($a, $b) {
                if ($b->sales_count !== $a->sales_count) {
                    return $b->sales_count <=> $a->sales_count;
                }
                return $b->id <=> $a->id;
            })->values();

            return $merged;
        }

        // 6. For View All (Full list):
        // Proportional distribution (70% vendor, 30% admin) interleaved in chunks,
        // with admin products guaranteed to appear even with 0 sales
        $result = collect();
        $adminQueue = $adminItems;
        $vendorQueue = $vendorItems;

        while ($adminQueue->isNotEmpty() || $vendorQueue->isNotEmpty()) {
            $chunk = collect();

            $vTake = min(7, $vendorQueue->count());
            if ($vTake > 0) {
                $chunk = $chunk->concat($vendorQueue->splice(0, $vTake));
            }

            $aTake = min(3, $adminQueue->count());
            if ($aTake > 0) {
                $chunk = $chunk->concat($adminQueue->splice(0, $aTake));
            }

            if ($vendorQueue->isEmpty() && $adminQueue->isNotEmpty()) {
                $chunk = $chunk->concat($adminQueue->splice(0, $adminQueue->count()));
            }
            if ($adminQueue->isEmpty() && $vendorQueue->isNotEmpty()) {
                $chunk = $chunk->concat($vendorQueue->splice(0, $vendorQueue->count()));
            }

            $chunkSorted = $chunk->sort(function ($a, $b) {
                if ($b->sales_count !== $a->sales_count) {
                    return $b->sales_count <=> $a->sales_count;
                }
                return $b->id <=> $a->id;
            });

            $result = $result->concat($chunkSorted);
        }

        if ($limit && $limit > 0) {
            return $result->take($limit)->values();
        }

        return $result->values();
    }

    /**
     * Get top rated products sorted by real customer rating descending with proportional 20% Admin and 80% Vendor distribution.
     *
     * @param int|null $limit (e.g. 4 for homepage, 100 for view all)
     * @return \Illuminate\Support\Collection
     */
    public static function getTopRatedProducts($limit = null)
    {
        // 1. Fetch active, approved items that are not blocked/hidden with reviews
        $allItems = Item::with(['category', 'tax', 'reviews'])
            ->where('status', 1)
            ->where(function ($query) {
                $query->where('approval_status', 'Approved')
                    ->orWhereNull('approval_status');
            })
            ->where(function ($query) {
                $query->whereNull('is_hidden_by_block')
                    ->orWhere('is_hidden_by_block', 0);
            })
            ->get();

        // Attach computed customer_rating and customer_rating_count for sorting
        foreach ($allItems as $item) {
            $item->computed_customer_rating = $item->customer_rating;
            $item->computed_customer_rating_count = $item->customer_rating_count;
        }

        // 2. Separate into Admin items and Vendor items
        // Sort each collection descending by customer_rating, then by customer_rating_count, then by id
        $adminItems = $allItems->filter(function ($item) {
            return empty($item->vendor_id) || $item->vendor_id == 0;
        })->sort(function ($a, $b) {
            if ($b->computed_customer_rating != $a->computed_customer_rating) {
                return $b->computed_customer_rating <=> $a->computed_customer_rating;
            }
            if ($b->computed_customer_rating_count != $a->computed_customer_rating_count) {
                return $b->computed_customer_rating_count <=> $a->computed_customer_rating_count;
            }
            return $b->id <=> $a->id;
        })->values();

        $vendorItems = $allItems->filter(function ($item) {
            return !empty($item->vendor_id) && $item->vendor_id > 0;
        })->sort(function ($a, $b) {
            if ($b->computed_customer_rating != $a->computed_customer_rating) {
                return $b->computed_customer_rating <=> $a->computed_customer_rating;
            }
            if ($b->computed_customer_rating_count != $a->computed_customer_rating_count) {
                return $b->computed_customer_rating_count <=> $a->computed_customer_rating_count;
            }
            return $b->id <=> $a->id;
        })->values();

        $adminCount = $adminItems->count();
        $vendorCount = $vendorItems->count();

        // If limit is specified (e.g. 4 for homepage, 100 for view all):
        if ($limit && $limit > 0) {
            // Calculate 20% admin quota (at least 1 admin product if listed and limit >= 1)
            $targetAdmin = $adminCount > 0 ? max(1, (int) round($limit * 0.20)) : 0;
            $targetVendor = $limit - $targetAdmin;

            // Balance if one pool has fewer items than targets
            if ($vendorCount < $targetVendor) {
                $targetVendor = $vendorCount;
                $targetAdmin = min($adminCount, $limit - $targetVendor);
            }
            if ($adminCount < $targetAdmin) {
                $targetAdmin = $adminCount;
                $targetVendor = min($vendorCount, $limit - $targetAdmin);
            }

            $adminSlice = $adminItems->take($targetAdmin);
            $vendorSlice = $vendorItems->take($targetVendor);

            // Merge and sort overall by customer rating descending
            $merged = $adminSlice->concat($vendorSlice)->sort(function ($a, $b) {
                if ($b->computed_customer_rating != $a->computed_customer_rating) {
                    return $b->computed_customer_rating <=> $a->computed_customer_rating;
                }
                if ($b->computed_customer_rating_count != $a->computed_customer_rating_count) {
                    return $b->computed_customer_rating_count <=> $a->computed_customer_rating_count;
                }
                return $b->id <=> $a->id;
            })->values();

            return $merged;
        }

        // Full list (20% admin, 80% vendor interleaved):
        $result = collect();
        $adminQueue = $adminItems;
        $vendorQueue = $vendorItems;

        while ($adminQueue->isNotEmpty() || $vendorQueue->isNotEmpty()) {
            $chunk = collect();

            $vTake = min(4, $vendorQueue->count());
            if ($vTake > 0) {
                $chunk = $chunk->concat($vendorQueue->splice(0, $vTake));
            }

            $aTake = min(1, $adminQueue->count());
            if ($aTake > 0) {
                $chunk = $chunk->concat($adminQueue->splice(0, $aTake));
            }

            if ($vendorQueue->isEmpty() && $adminQueue->isNotEmpty()) {
                $chunk = $chunk->concat($adminQueue->splice(0, $adminQueue->count()));
            }
            if ($adminQueue->isEmpty() && $vendorQueue->isNotEmpty()) {
                $chunk = $chunk->concat($vendorQueue->splice(0, $vendorQueue->count()));
            }

            $chunkSorted = $chunk->sort(function ($a, $b) {
                if ($b->computed_customer_rating != $a->computed_customer_rating) {
                    return $b->computed_customer_rating <=> $a->computed_customer_rating;
                }
                if ($b->computed_customer_rating_count != $a->computed_customer_rating_count) {
                    return $b->computed_customer_rating_count <=> $a->computed_customer_rating_count;
                }
                return $b->id <=> $a->id;
            });

            $result = $result->concat($chunkSorted);
        }

        return $result->values();
    }

    /**
     * Get newly listed products ordered strictly by latest listed first (id desc).
     *
     * @param int|null $limit (e.g. 8 for homepage, 100 for view all)
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
    public static function getNewlyListedProducts($limit = null)
    {
        $query = Item::with(['category', 'tax'])
            ->where('status', 1)
            ->where(function ($query) {
                $query->where('approval_status', 'Approved')
                    ->orWhereNull('approval_status');
            })
            ->where(function ($query) {
                $query->whereNull('is_hidden_by_block')
                    ->orWhere('is_hidden_by_block', 0);
            })
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc');

        if ($limit && $limit > 0) {
            return $query->take($limit)->get();
        }

        return $query->get();
    }

    /**
     * Get active, unexpired deals sorted by popularity (orders_count DESC, created_at DESC).
     *
     * @param int|null $limit (e.g. 8 for homepage Flash Deals section)
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getActiveDeals($limit = null)
    {
        try {
            $query = \App\Models\Deal::with(['items.category', 'dealItems.item', 'vendor'])
                ->active()
                ->orderBy('orders_count', 'desc')
                ->orderBy('created_at', 'desc');

            if ($limit && $limit > 0) {
                return $query->take($limit)->get();
            }

            return $query->get();
        } catch (\Throwable $e) {
            return collect();
        }
    }

    /**
     * Auto-ensure deals and deal_items tables and columns exist in database.
     */
    public static function ensureDealsTable()
    {
        try {
            if (!\Illuminate\Support\Facades\Schema::hasTable('deals')) {
                \Illuminate\Support\Facades\Schema::create('deals', function ($table) {
                    $table->id();
                    $table->unsignedBigInteger('vendor_id')->default(0)->index();
                    $table->string('name');
                    $table->string('slug')->unique();
                    $table->string('photo')->nullable();
                    $table->text('description')->nullable();
                    $table->enum('discount_type', ['fixed', 'percent'])->default('percent');
                    $table->decimal('discount_value', 12, 2)->default(0.00);
                    $table->decimal('original_price', 12, 2)->default(0.00);
                    $table->decimal('discounted_price', 12, 2)->default(0.00);
                    $table->decimal('delivery_charge', 12, 2)->default(0.00);
                    $table->boolean('is_free_delivery')->default(false);
                    $table->integer('duration_days')->default(1);
                    $table->dateTime('start_date')->nullable();
                    $table->dateTime('end_date')->nullable()->index();
                    $table->tinyInteger('status')->default(1)->index();
                    $table->unsignedInteger('orders_count')->default(0)->index();
                    $table->timestamps();
                });
            } else {
                if (!\Illuminate\Support\Facades\Schema::hasColumn('deals', 'photo')) {
                    \Illuminate\Support\Facades\Schema::table('deals', function ($table) {
                        $table->string('photo')->nullable()->after('slug');
                    });
                }
                if (!\Illuminate\Support\Facades\Schema::hasColumn('deals', 'delivery_charge')) {
                    \Illuminate\Support\Facades\Schema::table('deals', function ($table) {
                        $table->decimal('delivery_charge', 12, 2)->default(0.00)->after('discounted_price');
                    });
                }
                if (!\Illuminate\Support\Facades\Schema::hasColumn('deals', 'is_free_delivery')) {
                    \Illuminate\Support\Facades\Schema::table('deals', function ($table) {
                        $table->boolean('is_free_delivery')->default(false)->after('delivery_charge');
                    });
                }
                if (!\Illuminate\Support\Facades\Schema::hasColumn('deals', 'duration_days')) {
                    \Illuminate\Support\Facades\Schema::table('deals', function ($table) {
                        $table->integer('duration_days')->default(1)->after('is_free_delivery');
                    });
                }
                if (!\Illuminate\Support\Facades\Schema::hasColumn('deals', 'orders_count')) {
                    \Illuminate\Support\Facades\Schema::table('deals', function ($table) {
                        $table->unsignedInteger('orders_count')->default(0)->after('status')->index();
                    });
                }
            }

            if (!\Illuminate\Support\Facades\Schema::hasTable('deal_items')) {
                \Illuminate\Support\Facades\Schema::create('deal_items', function ($table) {
                    $table->id();
                    $table->unsignedBigInteger('deal_id')->index();
                    $table->unsignedBigInteger('item_id')->index();
                    $table->unsignedInteger('quantity')->default(1);
                    $table->decimal('original_price', 12, 2)->default(0.00);
                    $table->decimal('discounted_price', 12, 2)->default(0.00);
                    $table->timestamps();
                });
            } else {
                if (!\Illuminate\Support\Facades\Schema::hasColumn('deal_items', 'quantity')) {
                    \Illuminate\Support\Facades\Schema::table('deal_items', function ($table) {
                        $table->unsignedInteger('quantity')->default(1)->after('item_id');
                    });
                }
            }
        } catch (\Throwable $e) {}
    }
}


