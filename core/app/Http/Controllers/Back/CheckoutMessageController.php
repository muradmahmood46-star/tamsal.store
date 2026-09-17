<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class CheckoutMessageController extends Controller
{
    public function index()
    {
        $message = DB::table('checkout_messages')->first();
        $global_popup = DB::table('global_popup_settings')->first();
        return view('back.checkout_message.index', compact('message', 'global_popup'));
    }

    public function update(Request $request)
    {
        $request->validate(['message' => 'required']);

        if (DB::table('checkout_messages')->count() > 0) {
            DB::table('checkout_messages')->update([
                'message'    => $request->message,
                'is_active'  => 1,
                'updated_at' => now(),
            ]);
        } else {
            DB::table('checkout_messages')->insert([
                'message'    => $request->message,
                'is_active'  => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        return redirect()->back()->withSuccess('Message Updated Successfully.');
    }

    public function globalPopupUpdate(Request $request)
    {
        DB::table('global_popup_settings')->update([
            'is_enabled'          => $request->has('is_enabled') ? 1 : 0,
            'message'             => $request->message,
            
            'is_whatsapp_enabled' => $request->has('is_whatsapp_enabled') ? 1 : 0,
            'whatsapp_link'       => $request->whatsapp_link,
            
            'is_support_enabled'  => $request->has('is_support_enabled') ? 1 : 0,
            'support_link'        => $request->support_link,
            
            'is_tiktok_enabled'   => $request->has('is_tiktok_enabled') ? 1 : 0,
            'tiktok_link'         => $request->tiktok_link,
            
            'is_youtube_enabled'  => $request->has('is_youtube_enabled') ? 1 : 0,
            'youtube_link'        => $request->youtube_link,
            
            'is_facebook_enabled' => $request->has('is_facebook_enabled') ? 1 : 0,
            'facebook_link'       => $request->facebook_link,
            
            'is_telegram_enabled' => $request->has('is_telegram_enabled') ? 1 : 0,
            'telegram_link'       => $request->telegram_link,
            
            'updated_at'          => now(),
        ]);

        return redirect()->back()->withSuccess('Global Popup Settings Updated Successfully.');
    }
}