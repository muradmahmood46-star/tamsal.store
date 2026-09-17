<?php

namespace App\Http\Controllers\Back;

use App\Helpers\ImageHelper;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\ChieldCategory;
use App\Models\Item;
use App\Models\Order;
use App\Models\Subcategory;
use App\Models\Transaction;
use App\Models\DepositRequest;
use App\Models\FinePayment;
use App\Models\StoreRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;

class CsvProductController extends Controller
{

    public function index()
    {
        return view('back.item.bulk-upload');
    }
    public function export()
    {
        $headers = [
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=products_csv_export.csv',
            'Expires' => '0',
            'Pragma' => 'public',
        ];

        $lists = Item::where('item_type', '!=', 'affilite')->get();
        $new_list = [];
        foreach ($lists->toArray() as $list) {
            $list['photo'] = url('/core/public/storage/images/' . $list['photo']);
            $list['slug'] = Str::random(3) . $list['slug'] . Str::random(2);
            $list['category'] = Category::findOrFail($list['category_id'])->name;
            $list['subcategory'] = $list['subcategory_id'] ? Subcategory::findOrFail($list['subcategory_id'])->name : '';
            $list['childcategory'] = $list['childcategory_id'] ? ChieldCategory::findOrFail($list['childcategory_id'])->name : '';
            $list['brand'] = $list['brand_id'] ? Brand::findOrFail($list['brand_id'])->name : '';
            unset($list['category_id']);
            unset($list['subcategory_id']);
            unset($list['childcategory_id']);
            unset($list['brand_id']);
            $new_list[] = $list;
        }

        # add headers for each column in the CSV download
        array_unshift($new_list, array_keys($new_list[0]));

        $callback = function () use ($new_list) {
            $FH = fopen('php://output', 'w');
            foreach ($new_list as $row) {
                fputcsv($FH, $row);
            }
            fclose($FH);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function transactionExport()
    {
        $headers = [
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=platform_transactions_export_' . date('Y-m-d_H-i') . '.csv',
            'Expires' => '0',
            'Pragma' => 'public',
        ];

        $rows = [];
        $rows[] = [
            'ID',
            'Type / Category',
            'Customer / Vendor Name',
            'Store Name',
            'Email',
            'Phone',
            'Transaction ID / Ref',
            'System Reference',
            'Payment Method',
            'Amount (PKR)',
            'Status',
            'Details / Note',
            'Date & Time'
        ];

        // 1. Orders
        $orderTxns = Transaction::with(['order', 'order.user', 'order.seller'])->latest('id')->get();
        foreach ($orderTxns as $t) {
            $order = $t->order;
            $bill = $order ? json_decode($order->billing_info, true) : [];
            $customerName = '';
            if ($order && in_array($order->payment_method, ['Easypaisa', 'JazzCash', 'Bank Transfer']) && $order->account_name) {
                $customerName = $order->account_name;
            } elseif ($bill && (!empty($bill['bill_first_name']) || !empty($bill['bill_last_name']))) {
                $customerName = trim(($bill['bill_first_name'] ?? '') . ' ' . ($bill['bill_last_name'] ?? ''));
            } elseif ($order && $order->user) {
                $customerName = trim($order->user->first_name . ' ' . $order->user->last_name);
            } else {
                $customerName = 'Customer';
            }

            $storeName = ($order && $order->seller && $order->seller->shop_name) ? $order->seller->shop_name : (($order && $order->vendor_id) ? 'Store #' . $order->vendor_id : 'Official Store');

            $rows[] = [
                $t->id,
                'Order Payment',
                $customerName,
                $storeName,
                $order ? ($bill['bill_email'] ?? ($order->user->email ?? $t->email)) : $t->email,
                $order ? ($bill['bill_phone'] ?? ($order->user->phone ?? '')) : '',
                $order && $order->txnid ? $order->txnid : ($t->txn_id ?: '-'),
                $order ? $order->transaction_number : '-',
                $order ? ($order->payment_method ?: 'N/A') : 'N/A',
                number_format((float)$t->amount, 2, '.', ''),
                $order ? ($order->payment_status ?: 'Pending') : 'Pending',
                $order ? 'Order #' . $order->transaction_number . ' (' . $order->order_status . ')' : '',
                $t->created_at ? $t->created_at->format('Y-m-d H:i:s') : ''
            ];
        }

        // 2. Fine Payments
        $fines = FinePayment::with(['user', 'seller', 'unblockRequest'])->latest('id')->get();
        foreach ($fines as $f) {
            $storeName = $f->seller ? $f->seller->shop_name : ($f->unblockRequest ? $f->unblockRequest->store_name : 'Store #' . $f->user_id);
            $applicantName = $f->unblockRequest ? $f->unblockRequest->full_name : ($f->user ? $f->user->first_name . ' ' . $f->user->last_name : 'Vendor #' . $f->user_id);

            $rows[] = [
                'fine_' . $f->id,
                'Fine Approval',
                $applicantName,
                $storeName,
                $f->unblockRequest ? $f->unblockRequest->email : ($f->user ? $f->user->email : ''),
                $f->unblockRequest ? $f->unblockRequest->phone : ($f->user ? $f->user->phone : ''),
                $f->txn_id ?: '-',
                'FINE-REQ-' . ($f->store_unblock_request_id ?: $f->id),
                $f->payment_method ?: 'Direct Bank / Easypaisa',
                number_format((float)$f->fine_amount, 2, '.', ''),
                ucfirst($f->status),
                $f->admin_note ?: 'Store Unblock Fine Payment',
                $f->created_at ? $f->created_at->format('Y-m-d H:i:s') : ''
            ];
        }

        // 3. Store Requests
        $storeReqs = StoreRequest::with('user')->latest('id')->get();
        foreach ($storeReqs as $s) {
            if (!empty($s->transaction_id) || (float)$s->store_fee > 0 || !empty($s->payment_screenshot)) {
                $rows[] = [
                    'store_' . $s->id,
                    'Store Request Fee',
                    trim($s->first_name . ' ' . $s->last_name),
                    $s->shop_name,
                    $s->email,
                    $s->phone,
                    $s->transaction_id ?: '-',
                    'STORE-REQ-' . $s->id,
                    $s->account_type ?: 'Direct Transfer',
                    number_format((float)$s->store_fee, 2, '.', ''),
                    $s->status,
                    'Store Application Fee: ' . $s->shop_name,
                    $s->created_at ? $s->created_at->format('Y-m-d H:i:s') : ''
                ];
            }
        }

        // 4. Deposit Requests
        $deposits = DepositRequest::with(['user', 'seller'])->latest('id')->get();
        foreach ($deposits as $d) {
            $storeName = $d->seller ? $d->seller->shop_name : 'Store #' . $d->user_id;
            $userName = $d->user ? trim($d->user->first_name . ' ' . $d->user->last_name) : ($d->account_name ?: 'Vendor');

            $rows[] = [
                'deposit_' . $d->id,
                'Deposit Request',
                $userName,
                $storeName,
                $d->user ? $d->user->email : '',
                $d->user ? $d->user->phone : '',
                $d->txn_id ?: '-',
                'DEP-REQ-' . $d->id,
                $d->payment_method ?: 'Direct Transfer',
                number_format((float)$d->amount, 2, '.', ''),
                ucfirst($d->status),
                $d->admin_note ?: 'Vendor Wallet Deposit',
                $d->created_at ? $d->created_at->format('Y-m-d H:i:s') : ''
            ];
        }

        $callback = function () use ($rows) {
            $FH = fopen('php://output', 'w');
            foreach ($rows as $row) {
                fputcsv($FH, $row);
            }
            fclose($FH);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function orderExport()
    {
        $headers = [
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=order_csv_export.csv',
            'Expires' => '0',
            'Pragma' => 'public',
        ];

        $lists = Order::all()->toArray();
        $new_list = [];
        foreach ($lists as $list) {
            $new_list[] = $list;
        }

        # add headers for each column in the CSV download
        array_unshift($new_list, array_keys($new_list[0]));

        $callback = function () use ($new_list) {
            $FH = fopen('php://output', 'w');
            foreach ($new_list as $row) {
                fputcsv($FH, $row);
            }
            fclose($FH);
        };
        return response()->stream($callback, 200, $headers);
    }

    //*** POST Request
    public function import(Request $request)
    {

        try {
            $filename = '';
            if ($file = $request->file('csv')) {
                $filename = time() . "." . $file->getClientOriginalExtension();
                $file->move('assets/temp_files', $filename);
            }

            $file = fopen('assets/temp_files/' . $filename, "r");

            $i = 1;

            while (($line = fgetcsv($file)) !== false) {

                if ($i != 1) {

                    $category_id = $line[31] ? (Category::whereName($line[31])->exists() ? Category::whereName($line[31])->first()->id : 0) : 0;
                    $subcategory_id = $line[32] ? (SubCategory::whereName($line[32])->exists() ? SubCategory::whereName($line[32])->first()->id : 0) : 0;
                    $childcategory_id = $line[33] ? (ChieldCategory::whereName($line[33])->exists() ? ChieldCategory::whereName($line[33])->first()->id : 0) : 0;
                    $brand_id = $line[34] ? (Brand::whereName($line[33])->exists() ? Brand::whereName($line[33])->first()->id : 0) : 0;

                    $input['category_id'] = $category_id;
                    $input['subcategory_id'] = $subcategory_id;
                    $input['childcategory_id'] = $childcategory_id;
                    $input['brand_id'] = $brand_id;
                    $input['tax_id'] = $line[4];
                    $input['name'] = $line[6];
                    $input['slug'] = $line[7];
                    $input['sku'] = $line[8];
                    $input['tags'] = $line[9];
                    $input['video'] = $line[10];
                    $input['sort_details'] = $line[11];
                    $input['specification_name'] = $line[12];
                    $input['specification_description'] = $line[13];
                    $input['is_specification'] = $line[14];
                    $input['details'] = $line[15];
                    $input['discount_price'] = $line[17];
                    $input['previous_price'] = $line[18];
                    $input['stock'] = $line[19];
                    $input['meta_keywords'] = $line[20];
                    $input['meta_description'] = $line[21];
                    $input['status'] = $line[22];
                    $input['is_type'] = $line[23];
                    $input['date'] = $line[24];
                    $input['file'] = $line[26];
                    $input['link'] = $line[27];
                    $input['file_type'] = $line[28] ? $line[28] : null;

                    $input['item_type'] = $line[25];

                    $images_name = $this->uploadImage($line[16], 'images');
                    

                    $input['photo'] = $images_name[1];
                    $input['thumbnail'] = $images_name[1];



                    $data = new Item();
                    $data->fill($input)->save();
                }

                $i++;
            }
            fclose($file);

            $removefiles = glob('/assets/temp_files/*');

            // Deleting all the files in the list
            foreach ($removefiles as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }

            return back()->withSuccess(__('Bulk Product File Imported Successfully.'));
        } catch (\Throwable $th) {
            dd($th);
            return back()->withError(__('Something is wrong!'));
        }
    }


    public function uploadImage($file, $path, $delete = null)
    {
        if ($file) {

            if ($delete) {
                Storage::delete($path . '/' . $delete);
            }

            $photoName = 'OM_' . time() .  Str::random(8) . '.png';
            $thumbnailName = 'OM_' . time() .  Str::random(8) . '.png';

            Storage::putFileAs($path, $file, $photoName);


            $image = \Image::make($file)->resize(230, 230);


            $thumbnailPath = $path . '/' . $thumbnailName;
            Storage::put($thumbnailPath, (string) $image->encode());


            return [$photoName, $thumbnailName];
        }
    }
}
