<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\ReceivingAccount;
use App\Models\Setting;
use App\Models\StoreRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class StoreApplyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function apply(Request $request)
    {
        $user = Auth::user();

        // If user is already approved as seller
        if ($user->isSeller()) {
            return redirect()->route('seller.dashboard')->withSuccess(__('You are already an approved seller. Welcome to your store dashboard!'));
        }

        $latestRequest = StoreRequest::where('user_id', $user->id)->latest()->first();

        // If user wants to reapply explicitly
        if ($request->has('reapply') && $request->reapply == '1') {
            $setting = Setting::first();
            $receivingAccounts = ReceivingAccount::where('status', 1)->get();
            return view('user.store.apply', compact('user', 'setting', 'receivingAccounts', 'latestRequest'));
        }

        if ($latestRequest) {
            if ($latestRequest->status == 'Pending') {
                return view('user.store.status', compact('user', 'latestRequest'));
            } elseif ($latestRequest->status == 'Rejected') {
                return view('user.store.status', compact('user', 'latestRequest'));
            } elseif ($latestRequest->status == 'Approved') {
                // In case status is approved but is_seller flag wasn't synced
                $user->update(['is_seller' => 1]);
                return redirect()->route('seller.dashboard');
            }
        }

        $setting = Setting::first();
        $receivingAccounts = ReceivingAccount::where('status', 1)->get();

        return view('user.store.apply', compact('user', 'setting', 'receivingAccounts', 'latestRequest'));
    }

    public function submit(Request $request)
    {
        $user = Auth::user();
        $setting = Setting::first();
        $isFree = $setting ? (int) $setting->is_store_opening_free : 1;
        $storeFee = $setting ? (float) $setting->store_opening_fee : 0;

        $latestRequest = StoreRequest::where('user_id', $user->id)->latest()->first();
        $hasExistingDocs = $latestRequest && $latestRequest->id_card_front;
        $hasExistingSamples = $latestRequest && $latestRequest->sample_product_1_image && $latestRequest->sample_product_2_image && $latestRequest->sample_product_3_image;

        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'cnic' => 'required|string|max:50',
            'shop_name' => 'required|string|max:255',
            'product_types' => 'required|string|max:500',
            'courier_company' => 'required|string|max:255',
            'shop_address' => 'required|string|max:1000',
            'sample_product_1_name' => 'required|string|max:255',
            'sample_product_2_name' => 'required|string|max:255',
            'sample_product_3_name' => 'required|string|max:255',
        ];

        // Step 3 Document validation
        if (!$hasExistingDocs) {
            if (!$request->hasFile('id_card_front') && !$request->filled('id_card_front_cam')) {
                return redirect()->back()->withInput()->withErrors(['id_card_front' => __('Please upload or take a photo of your ID Card.')]);
            }
            if (!$request->hasFile('selfie_with_id') && !$request->filled('selfie_with_id_cam')) {
                return redirect()->back()->withInput()->withErrors(['selfie_with_id' => __('Please upload or take a photo of your Selfie with ID Card.')]);
            }
            if (!$request->hasFile('store_documents') && !$request->filled('store_documents_cam')) {
                return redirect()->back()->withInput()->withErrors(['store_documents' => __('Please upload or take a photo of your Store Documents.')]);
            }
        }

        // Step 3 Sample products image validation
        if (!$hasExistingSamples) {
            if (!$request->hasFile('sample_product_1') && !$request->filled('sample_product_1_cam')) {
                return redirect()->back()->withInput()->withErrors(['sample_product_1' => __('Please upload or take a photo of Sample Product 1.')]);
            }
            if (!$request->hasFile('sample_product_2') && !$request->filled('sample_product_2_cam')) {
                return redirect()->back()->withInput()->withErrors(['sample_product_2' => __('Please upload or take a photo of Sample Product 2.')]);
            }
            if (!$request->hasFile('sample_product_3') && !$request->filled('sample_product_3_cam')) {
                return redirect()->back()->withInput()->withErrors(['sample_product_3' => __('Please upload or take a photo of Sample Product 3.')]);
            }
        }

        if ($request->hasFile('id_card_front')) {
            $rules['id_card_front'] = 'file|mimes:jpeg,jpg,png,webp,svg,gif,bmp,tiff,tif,avif,ico,jfif,heic,heif|max:10240';
        }
        if ($request->hasFile('selfie_with_id')) {
            $rules['selfie_with_id'] = 'file|mimes:jpeg,jpg,png,webp,svg,gif,bmp,tiff,tif,avif,ico,jfif,heic,heif|max:10240';
        }
        if ($request->hasFile('store_documents')) {
            $rules['store_documents'] = 'file|mimes:jpeg,jpg,png,webp,svg,gif,bmp,tiff,tif,avif,ico,jfif,heic,heif,pdf|max:20480';
        }
        if ($request->hasFile('sample_product_1')) {
            $rules['sample_product_1'] = 'file|mimes:jpeg,jpg,png,webp,svg,gif,bmp,tiff,tif,avif,ico,jfif,heic,heif|max:10240';
        }
        if ($request->hasFile('sample_product_2')) {
            $rules['sample_product_2'] = 'file|mimes:jpeg,jpg,png,webp,svg,gif,bmp,tiff,tif,avif,ico,jfif,heic,heif|max:10240';
        }
        if ($request->hasFile('sample_product_3')) {
            $rules['sample_product_3'] = 'file|mimes:jpeg,jpg,png,webp,svg,gif,bmp,tiff,tif,avif,ico,jfif,heic,heif|max:10240';
        }

        // Step 4 Payment validation if fee is enabled
        if ($isFree == 0) {
            $rules['account_type'] = 'required|string|max:255';
            $rules['account_name'] = 'required|string|max:255';
            $rules['account_number'] = 'required|string|max:255';
            $rules['transaction_id'] = 'required|string|max:255';
            
            if (!$request->hasFile('payment_screenshot') && !$request->filled('payment_screenshot_cam')) {
                return redirect()->back()->withInput()->withErrors(['payment_screenshot' => __('Please upload or capture a photo of your payment receipt/screenshot.')]);
            }
            if ($request->hasFile('payment_screenshot')) {
                $rules['payment_screenshot'] = 'file|mimes:jpeg,jpg,png,webp,svg,gif,bmp,tiff,tif,avif,ico,jfif,heic,heif|max:10240';
            }
        }

        $request->validate($rules);

        $uploadDir = public_path('storage/images/stores');
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Helper to process uploaded file or base64 camera capture
        $processFileOrBase64 = function ($fileInputName, $base64InputName, $existingFilename = null) use ($request, $uploadDir) {
            if ($request->hasFile($fileInputName)) {
                $file = $request->file($fileInputName);
                $ext = $file->getClientOriginalExtension();
                $filename = time() . '_' . Str::random(10) . '.' . $ext;
                $file->move($uploadDir, $filename);
                return $filename;
            } elseif ($request->filled($base64InputName)) {
                $base64Data = $request->input($base64InputName);
                if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $type)) {
                    $base64Data = substr($base64Data, strpos($base64Data, ',') + 1);
                    $type = strtolower($type[1]);
                    $base64Data = base64_decode($base64Data);
                    if ($base64Data !== false) {
                        $filename = time() . '_' . Str::random(10) . '.' . $type;
                        file_put_contents($uploadDir . '/' . $filename, $base64Data);
                        return $filename;
                    }
                }
            }
            return $existingFilename;
        };

        $idCardFront = $processFileOrBase64('id_card_front', 'id_card_front_cam', $latestRequest ? $latestRequest->id_card_front : null);
        $selfieWithId = $processFileOrBase64('selfie_with_id', 'selfie_with_id_cam', $latestRequest ? $latestRequest->selfie_with_id : null);
        $storeDocuments = $processFileOrBase64('store_documents', 'store_documents_cam', $latestRequest ? $latestRequest->store_documents : null);
        
        $sampleProduct1Image = $processFileOrBase64('sample_product_1', 'sample_product_1_cam', $latestRequest ? $latestRequest->sample_product_1_image : null);
        $sampleProduct2Image = $processFileOrBase64('sample_product_2', 'sample_product_2_cam', $latestRequest ? $latestRequest->sample_product_2_image : null);
        $sampleProduct3Image = $processFileOrBase64('sample_product_3', 'sample_product_3_cam', $latestRequest ? $latestRequest->sample_product_3_image : null);

        $paymentScreenshot = null;

        if ($isFree == 0) {
            $paymentScreenshot = $processFileOrBase64('payment_screenshot', 'payment_screenshot_cam', null);
        }

        // Create or update store request
        $storeRequest = StoreRequest::create([
            'user_id' => $user->id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'email' => $request->email,
            'cnic' => $request->cnic,
            'shop_name' => $request->shop_name,
            'product_types' => $request->product_types,
            'courier_company' => $request->courier_company,
            'shop_address' => $request->shop_address,
            'id_card_front' => $idCardFront,
            'selfie_with_id' => $selfieWithId,
            'store_documents' => $storeDocuments,
            'sample_product_1_name' => $request->sample_product_1_name,
            'sample_product_1_image' => $sampleProduct1Image,
            'sample_product_2_name' => $request->sample_product_2_name,
            'sample_product_2_image' => $sampleProduct2Image,
            'sample_product_3_name' => $request->sample_product_3_name,
            'sample_product_3_image' => $sampleProduct3Image,
            'is_free' => $isFree,
            'store_fee' => $isFree ? 0 : $storeFee,
            'account_type' => $isFree ? null : $request->account_type,
            'account_name' => $isFree ? null : $request->account_name,
            'account_number' => $isFree ? null : $request->account_number,
            'transaction_id' => $isFree ? null : $request->transaction_id,
            'payment_screenshot' => $paymentScreenshot,
            'status' => 'Pending',
            'reject_reason' => null
        ]);

        // Create notification for admin
        try {
            Notification::create([
                'user_id' => $user->id,
                'order_id' => null,
                'is_read' => 0
            ]);
        } catch (\Throwable $e) {
            // Notification table might use different fields
        }

        return redirect()->route('user.store.apply')->withSuccess(__('Your store application has been submitted successfully and is currently under review by our team!'));
    }
}
