<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\Item;
use App\Repositories\Back\ItemRepository;
use Illuminate\Http\Request;

class VendorProductController extends Controller
{
    protected $repository;

    public function __construct(ItemRepository $repository)
    {
        $this->middleware('auth:admin');
        $this->middleware('adminlocalize');
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        $status = $request->status;
        $search = $request->search;

        $query = Item::whereNotNull('vendor_id')
            ->where('vendor_id', '!=', 0)
            ->with(['category', 'subcategory', 'user', 'seller', 'galleries'])
            ->latest('id');

        if ($status && in_array($status, ['Pending', 'Approved', 'Rejected'])) {
            $query->where('approval_status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('first_name', 'like', "%{$search}%")
                           ->orWhere('last_name', 'like', "%{$search}%")
                           ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhereHas('seller', function ($sq) use ($search) {
                        $sq->where('shop_name', 'like', "%{$search}%");
                    });
            });
        }

        $datas = $query->paginate(15);

        $counts = [
            'all' => Item::whereNotNull('vendor_id')->where('vendor_id', '!=', 0)->count(),
            'pending' => Item::whereNotNull('vendor_id')->where('vendor_id', '!=', 0)->where('approval_status', 'Pending')->count(),
            'approved' => Item::whereNotNull('vendor_id')->where('vendor_id', '!=', 0)->where('approval_status', 'Approved')->count(),
            'rejected' => Item::whereNotNull('vendor_id')->where('vendor_id', '!=', 0)->where('approval_status', 'Rejected')->count(),
        ];

        return view('back.vendor_product.index', compact('datas', 'counts', 'status', 'search'));
    }

    public function show($id)
    {
        $item = Item::whereNotNull('vendor_id')
            ->with(['category', 'subcategory', 'childcategory', 'brand', 'tax', 'user', 'seller', 'galleries'])
            ->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $item
        ]);
    }

    public function approve($id)
    {
        $item = Item::whereNotNull('vendor_id')
            ->where('vendor_id', '!=', 0)
            ->findOrFail($id);

        $item->update([
            'approval_status' => 'Approved',
            'status' => 1,
            'reject_reason' => null
        ]);

        return redirect()->back()->withSuccess(__('Product ":name" has been approved and is now live on the store.', ['name' => $item->name]));
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'reject_reason' => 'required|string|max:1000'
        ], [
            'reject_reason.required' => __('Please provide a reason for rejecting this product submission.')
        ]);

        $item = Item::whereNotNull('vendor_id')
            ->where('vendor_id', '!=', 0)
            ->findOrFail($id);

        $item->update([
            'approval_status' => 'Rejected',
            'status' => 0,
            'reject_reason' => $request->reject_reason
        ]);

        return redirect()->back()->withSuccess(__('Product ":name" has been rejected. The rejection reason was sent to the vendor.', ['name' => $item->name]));
    }

    public function destroy($id)
    {
        $item = Item::whereNotNull('vendor_id')
            ->where('vendor_id', '!=', 0)
            ->findOrFail($id);

        $this->repository->delete($item);

        return redirect()->back()->withSuccess(__('Vendor product deleted successfully.'));
    }
}
