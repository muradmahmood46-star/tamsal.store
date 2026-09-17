<?php

namespace App\Http\Controllers\Seller;

use App\Helpers\ImageHelper;
use App\Http\Controllers\Controller;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AccountController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'seller']);
    }

    public function profile()
    {
        $user = Auth::user();
        $seller = Seller::firstOrCreate(['user_id' => $user->id], [
            'shop_name' => $user->first_name . '\'s Store',
            'shop_address' => $user->ship_address1 ?: '',
            'shop_phone' => $user->phone ?: '',
            'shop_email' => $user->email ?: '',
            'status' => 1
        ]);

        return view('seller.profile.index', compact('user', 'seller'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $seller = Seller::where('user_id', $user->id)->firstOrFail();

        $request->validate([
            'shop_name' => 'required|string|max:255',
            'shop_phone' => 'required|string|max:255',
            'shop_email' => 'required|email|max:255',
            'shop_address' => 'required|string|max:1000',
            'shop_details' => 'nullable|string|max:3000',
            'shop_logo' => 'nullable|image|mimes:jpeg,jpg,png,webp,svg,gif,bmp,tiff,tif,avif,ico,jfif,heic,heif|max:5120',
            'shop_banner' => 'nullable|image|mimes:jpeg,jpg,png,webp,svg,gif,bmp,tiff,tif,avif,ico,jfif,heic,heif|max:5120',
        ]);

        $data = $request->only(['shop_name', 'shop_phone', 'shop_email', 'shop_address', 'shop_details']);

        $uploadDir = public_path('storage/images/stores');
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if ($request->hasFile('shop_logo')) {
            $file = $request->file('shop_logo');
            $logoName = 'logo_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $logoName);
            $data['shop_logo'] = $logoName;
        }

        if ($request->hasFile('shop_banner')) {
            $file = $request->file('shop_banner');
            $bannerName = 'banner_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $bannerName);
            $data['shop_banner'] = $bannerName;
        }

        $seller->update($data);

        return redirect()->back()->withSuccess(__('Store profile updated successfully!'));
    }
}
