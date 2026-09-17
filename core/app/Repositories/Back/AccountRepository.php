<?php

namespace App\Repositories\Back;

use Auth;
use App\{
    Models\Post,
    Models\User,
    Models\Order,
    Helpers\ImageHelper,
    Helpers\PriceHelper
};
use App\Models\Admin;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Currency;
use App\Models\Item;
use App\Models\Review;
use App\Models\Setting;
use App\Models\Seller;
use App\Models\DepositRequest;
use App\Models\FinePayment;
use App\Models\StoreRequest;
use App\Models\Subscriber;
use App\Models\Ticket;
use App\Models\Transaction;
use App\Models\Withdraw;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class AccountRepository
{

    /**
     * Update profile.
     *
     * @param  \App\Http\Requests\ImageUpdateRequest  $request
     * @return \Illuminate\Http\Response
     */

    public function updateProfile($request)
    {
        $input = $request->all();
        $data = Auth::guard('admin')->user();
        if ($file = $request->file('photo')) {
            $input['photo'] = ImageHelper::handleUpdatedUploadedImage($file,'images',$data,'images/','photo');
        }
        $data->update($input);
    }


    /**
     * Update password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function updatePassword($request)
    {
        $data = Auth::guard('admin')->user();

        if ($request->current_password){
            if (Hash::check($request->current_password, $data->password)){
                if ($request->new_password == $request->renew_password){
                    $input['password'] = Hash::make($request->new_password);
                }else{
                    return [
                        'status'  => false,
                        'message' => __('Confirm password does not match.')
                    ];
                }
            }else{
                return [
                    'status'  => false,
                    'message' => __('Current password Does not match.')
                ];
            }
        }

        $data->update($input);

        return [
            'status'  => true,
            'message' => __('Successfully changed your password')
        ];

    }

    public function getTotalOrders()
    {
        return Order::count();
    }
    public function getPendingOrders()
    {
        return Order::whereOrderStatus('Pending')->count();
    }
    public function getAcceptedOrders()
    {
        return Order::whereOrderStatus('Accepted')->count();
    }
    public function getSendToDeliveryOrders()
    {
        return Order::whereOrderStatus('Send to Delivery House')->count();
    }
    public function getInProgressOrders()
    {
        return Order::whereOrderStatus('In Progress')->count();
    }
    public function getDeliveredOrders()
    {
        return Order::whereOrderStatus('Delivered')->count();
    }
    public function getCanceledOrders()
    {
        return Order::whereOrderStatus('Canceled')->count();
    }

    public function getTodayOrders()
    {
        return Order::whereDate('created_at', Carbon::today())->count();
    }

    public function calculateAcceptedOrdersSales($startDate = null, $endDate = null)
    {
        $query = Order::whereIn('order_status', ['Accepted', 'Send to Delivery House', 'In Progress', 'Delivered']);
        if ($startDate && $endDate) {
            if ($startDate === $endDate) {
                $query->whereDate('created_at', '=', $startDate);
            } else {
                $query->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate);
            }
        } elseif ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        $orders = $query->get();
        $totalSales = 0;
        foreach ($orders as $order) {
            $cart = json_decode($order->cart, true);
            if (is_array($cart)) {
                foreach ($cart as $item) {
                    $price = $item['main_price'] ?? 0;
                    $attrPrice = $item['attribute_price'] ?? 0;
                    $qty = $item['qty'] ?? 1;
                    $totalSales += ($price + $attrPrice) * $qty;
                }
            }
        }

        return $totalSales;
    }

    public function getTotalProductSale()
    {
        $total = $this->calculateAcceptedOrdersSales();
        $curr = Currency::where('is_default', 1)->first();
        $setting = Setting::first();
        if ($setting && $setting->currency_direction == 1) {
            return ($curr ? $curr->sign : 'PKR') . ' ' . number_format($total, 2);
        } else {
            return number_format($total, 2) . ' ' . ($curr ? $curr->sign : 'PKR');
        }
    }

    public function getcurrentMonthProductSale()
    {
        $first_day = Carbon::now()->startOfMonth()->toDateString();
        $current_date = Carbon::now()->toDateString();
        $total = $this->calculateAcceptedOrdersSales($first_day, $current_date);
        $curr = Currency::where('is_default', 1)->first();
        $setting = Setting::first();
        if ($setting && $setting->currency_direction == 1) {
            return ($curr ? $curr->sign : 'PKR') . ' ' . number_format($total, 2);
        } else {
            return number_format($total, 2) . ' ' . ($curr ? $curr->sign : 'PKR');
        }
    }

    public function getTodayProductSale()
    {
        $current_date = Carbon::now()->toDateString();
        $total = $this->calculateAcceptedOrdersSales($current_date, $current_date);
        $curr = Currency::where('is_default', 1)->first();
        $setting = Setting::first();
        if ($setting && $setting->currency_direction == 1) {
            return ($curr ? $curr->sign : 'PKR') . ' ' . number_format($total, 2);
        } else {
            return number_format($total, 2) . ' ' . ($curr ? $curr->sign : 'PKR');
        }
    }

    public function getYearProductSale()
    {
        $year = Carbon::now()->startOfYear()->toDateString();
        $current_date = Carbon::now()->toDateString();
        $total = $this->calculateAcceptedOrdersSales($year, $current_date);
        $curr = Currency::where('is_default', 1)->first();
        $setting = Setting::first();
        if ($setting && $setting->currency_direction == 1) {
            return ($curr ? $curr->sign : 'PKR') . ' ' . number_format($total, 2);
        } else {
            return number_format($total, 2) . ' ' . ($curr ? $curr->sign : 'PKR');
        }
    }

    public function getAdminProductOrdersProfit($startDate = null, $endDate = null)
    {
        $query = Order::where(function($q) {
            $q->whereNull('vendor_id')->orWhere('vendor_id', 0);
        })->whereIn('order_status', ['Accepted', 'Send to Delivery House', 'In Progress', 'Delivered']);

        if ($startDate && $endDate) {
            if ($startDate === $endDate) {
                $query->whereDate('created_at', '=', $startDate);
            } else {
                $query->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate);
            }
        } elseif ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        $orders = $query->get();
        $totalProfit = 0;
        foreach ($orders as $order) {
            $cart = json_decode($order->cart, true);
            if (is_array($cart)) {
                foreach ($cart as $key => $item) {
                    $qty = isset($item['qty']) ? (int)$item['qty'] : 1;
                    $profit = 0;
                    if (isset($item['estimated_profit']) && (float)$item['estimated_profit'] > 0) {
                        $profit = (float)$item['estimated_profit'];
                    } else {
                        $itemId = (int)explode('-', $key)[0];
                        $prod = Item::find($itemId);
                        if ($prod && (float)$prod->estimated_profit > 0) {
                            $profit = (float)$prod->estimated_profit;
                        }
                    }
                    $totalProfit += $profit * $qty;
                }
            }
        }

        return $totalProfit;
    }

    public function getTotalEarning()
    {
        $depositTotal = (float) DepositRequest::where('status', 'approved')->sum('amount');
        $directFineTotal = (float) FinePayment::where('status', 'approved')
            ->where('payment_method', '!=', 'Store Wallet Balance')
            ->where(function($q) {
                $q->whereNull('bank_name')->orWhere('bank_name', '!=', 'Wallet Balance Deduction');
            })
            ->sum('fine_amount');

        $adminOrderProfit = $this->getAdminProductOrdersProfit();

        $total = $depositTotal + $directFineTotal + $adminOrderProfit;

        $curr = Currency::where('is_default', 1)->first();
        $setting = Setting::first();
      
        if ($setting && $setting->currency_direction == 1) {
            return ($curr ? $curr->sign : 'PKR') . ' ' . number_format($total, 2);
        } else {
            return number_format($total, 2) . ' ' . ($curr ? $curr->sign : 'PKR');
        }
    }

    public function getTodayEarning()
    {
        $current_date = Carbon::now()->toDateString();
        $depositTotal = (float) DepositRequest::where('status', 'approved')
            ->whereDate('created_at', '=', $current_date)
            ->sum('amount');

        $directFineTotal = (float) FinePayment::where('status', 'approved')
            ->where('payment_method', '!=', 'Store Wallet Balance')
            ->where(function($q) {
                $q->whereNull('bank_name')->orWhere('bank_name', '!=', 'Wallet Balance Deduction');
            })
            ->whereDate('created_at', '=', $current_date)
            ->sum('fine_amount');

        $adminOrderProfit = $this->getAdminProductOrdersProfit($current_date, $current_date);

        $total = $depositTotal + $directFineTotal + $adminOrderProfit;

        $curr = Currency::where('is_default', 1)->first();
        $setting = Setting::first();
        if ($setting && $setting->currency_direction == 1) {
            return ($curr ? $curr->sign : 'PKR') . ' ' . number_format($total, 2);
        } else {
            return number_format($total, 2) . ' ' . ($curr ? $curr->sign : 'PKR');
        }
    }

    public function getMonthEarning()
    {
        $first_day = Carbon::now()->startOfMonth()->toDateString();
        $current_date = Carbon::now()->toDateString();

        $depositTotal = (float) DepositRequest::where('status', 'approved')
            ->whereDate('created_at', '>=', $first_day)
            ->whereDate('created_at', '<=', $current_date)
            ->sum('amount');

        $directFineTotal = (float) FinePayment::where('status', 'approved')
            ->where('payment_method', '!=', 'Store Wallet Balance')
            ->where(function($q) {
                $q->whereNull('bank_name')->orWhere('bank_name', '!=', 'Wallet Balance Deduction');
            })
            ->whereDate('created_at', '>=', $first_day)
            ->whereDate('created_at', '<=', $current_date)
            ->sum('fine_amount');

        $adminOrderProfit = $this->getAdminProductOrdersProfit($first_day, $current_date);

        $total = $depositTotal + $directFineTotal + $adminOrderProfit;

        $curr = Currency::where('is_default', 1)->first();
        $setting = Setting::first();
        if ($setting && $setting->currency_direction == 1) {
            return ($curr ? $curr->sign : 'PKR') . ' ' . number_format($total, 2);
        } else {
            return number_format($total, 2) . ' ' . ($curr ? $curr->sign : 'PKR');
        }
    }

    public function getYearEarning()
    {
        $year = Carbon::now()->startOfYear()->toDateString();
        $current_date = Carbon::now()->toDateString();

        $depositTotal = (float) DepositRequest::where('status', 'approved')
            ->whereDate('created_at', '>=', $year)
            ->whereDate('created_at', '<=', $current_date)
            ->sum('amount');

        $directFineTotal = (float) FinePayment::where('status', 'approved')
            ->where('payment_method', '!=', 'Store Wallet Balance')
            ->where(function($q) {
                $q->whereNull('bank_name')->orWhere('bank_name', '!=', 'Wallet Balance Deduction');
            })
            ->whereDate('created_at', '>=', $year)
            ->whereDate('created_at', '<=', $current_date)
            ->sum('fine_amount');

        $adminOrderProfit = $this->getAdminProductOrdersProfit($year, $current_date);

        $total = $depositTotal + $directFineTotal + $adminOrderProfit;

        $curr = Currency::where('is_default', 1)->first();
        $setting = Setting::first();
        if ($setting && $setting->currency_direction == 1) {
            return ($curr ? $curr->sign : 'PKR') . ' ' . number_format($total, 2);
        } else {
            return number_format($total, 2) . ' ' . ($curr ? $curr->sign : 'PKR');
        }
    }

    public function getSystemUser()
    {
        return Admin::where('id','!=',1)->count();
    }


    public function getTotalUsers()
    {
        return User::count();
    }

    public function getTotalItems()
    {
        return Item::count();
    }

    public function getRecentOrders()
    {
        return Order::latest('id')->take(10)->get();
    }

    public function getRecentUsers()
    {
        return User::latest('id')->take(10)->get();
    }

    public function getRecentProducts()
    {
        return Item::latest('id')->take(10)->get();
    }
    public function getTotalCategory()
    {
        return Category::count();
    }
    public function getTotalBrand()
    {
        return Brand::count();
    }
    public function getTotalReview()
    {
        return Review::count();
    }
    public function getTotalTransaction()
    {
        $orderTxns = Transaction::count();
        $fineTxns = FinePayment::count();
        $storeTxns = StoreRequest::where(function($q) {
            $q->whereNotNull('transaction_id')
              ->orWhere('store_fee', '>', 0)
              ->orWhereNotNull('payment_screenshot');
        })->count();
        $depositTxns = DepositRequest::count();

        return $orderTxns + $fineTxns + $storeTxns + $depositTxns;
    }
    public function getTotalPendingTicket()
    {
        return Ticket::whereStatus('Pending')->count();
    }
    public function getTotalTicket()
    {
        return Ticket::count();
    }
    public function getTotalVendorProducts()
    {
        return Item::whereNotNull('vendor_id')
            ->where('vendor_id', '>', 0)
            ->where('status', 1)
            ->where(function($q) {
                $q->whereNull('is_hidden_by_block')->orWhere('is_hidden_by_block', 0);
            })
            ->count();
    }
    public function getTotalBlog()
    {
        return Post::count();
    }
    public function getTotalStores()
    {
        return Seller::where('status', 1)->count();
    }
    public function getTotalSubscriber()
    {
        return Subscriber::count();
    }
    public function getTotalStoreFees()
    {
        $total = DepositRequest::where('status', 'approved')->sum('amount');
        $curr = Currency::where('is_default', 1)->first();
        $setting = Setting::first();
        if ($setting && $setting->currency_direction == 1) {
            return ($curr ? $curr->sign : 'PKR') . ' ' . number_format($total, 2);
        } else {
            return number_format($total, 2) . ' ' . ($curr ? $curr->sign : 'PKR');
        }
    }

}
