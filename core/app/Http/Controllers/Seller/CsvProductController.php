<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CsvProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'seller']);
    }

    public function index()
    {
        return view('seller.item.csv');
    }

    public function export()
    {
        $headers = [
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=seller_products_' . date('Y-m-d') . '.csv',
            'Expires' => '0',
            'Pragma' => 'public'
        ];

        $items = Item::where('vendor_id', Auth::id())->get();
        $columns = ['ID', 'Name', 'SKU', 'Discount Price', 'Previous Price', 'Stock', 'Status', 'Date'];

        $callback = function() use ($items, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($items as $item) {
                fputcsv($file, [
                    $item->id,
                    $item->name,
                    $item->sku,
                    $item->discount_price,
                    $item->previous_price,
                    $item->stock,
                    $item->status == 1 ? 'Active' : 'Inactive',
                    $item->created_at ? $item->created_at->format('Y-m-d') : ''
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
