<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Helpers\WhatsAppHelper;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class WhatsAppSettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('adminlocalize');
    }

    public static function ensureColumnsExist()
    {
        try {
            if (Schema::hasTable('settings')) {
                Schema::table('settings', function (Blueprint $table) {
                    if (!Schema::hasColumn('settings', 'whatsapp_enabled')) {
                        $table->tinyInteger('whatsapp_enabled')->default(0)->after('is_twilio');
                    }
                    if (!Schema::hasColumn('settings', 'whatsapp_phone_number_id')) {
                        $table->string('whatsapp_phone_number_id')->nullable()->after('whatsapp_enabled');
                    }
                    if (!Schema::hasColumn('settings', 'whatsapp_access_token')) {
                        $table->text('whatsapp_access_token')->nullable()->after('whatsapp_phone_number_id');
                    }
                    if (!Schema::hasColumn('settings', 'whatsapp_from_number')) {
                        $table->string('whatsapp_from_number')->nullable()->after('whatsapp_access_token');
                    }
                    if (!Schema::hasColumn('settings', 'whatsapp_template_order_confirmed')) {
                        $table->string('whatsapp_template_order_confirmed')->default('order_confirmed')->after('whatsapp_from_number');
                    }
                    if (!Schema::hasColumn('settings', 'whatsapp_template_in_progress')) {
                        $table->string('whatsapp_template_in_progress')->default('order_in_progress')->after('whatsapp_template_order_confirmed');
                    }
                    if (!Schema::hasColumn('settings', 'whatsapp_template_delivered')) {
                        $table->string('whatsapp_template_delivered')->default('order_delivered')->after('whatsapp_template_in_progress');
                    }
                    if (!Schema::hasColumn('settings', 'whatsapp_template_canceled')) {
                        $table->string('whatsapp_template_canceled')->default('order_canceled')->after('whatsapp_template_delivered');
                    }
                });
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Could not auto-add whatsapp columns: ' . $e->getMessage());
        }
    }

    /**
     * Show WhatsApp settings page.
     */
    public function index()
    {
        self::ensureColumnsExist();
        $setting = Setting::first();
        return view('back.settings.whatsapp', compact('setting'));
    }

    /**
     * Save WhatsApp configuration.
     */
    public function update(Request $request)
    {
        $request->validate([
            'whatsapp_phone_number_id' => 'nullable|string|max:100',
            'whatsapp_access_token'    => 'nullable|string|max:2000',
            'whatsapp_from_number'     => 'nullable|string|max:30',
        ]);

        $data = [
            'whatsapp_enabled'                  => $request->has('whatsapp_enabled') ? 1 : 0,
            'whatsapp_phone_number_id'          => trim($request->input('whatsapp_phone_number_id', '')),
            'whatsapp_access_token'             => trim($request->input('whatsapp_access_token', '')),
            'whatsapp_from_number'              => trim($request->input('whatsapp_from_number', '')),
            'whatsapp_template_order_confirmed' => trim($request->input('whatsapp_template_order_confirmed', 'order_confirmed')),
            'whatsapp_template_in_progress'     => trim($request->input('whatsapp_template_in_progress', 'order_in_progress')),
            'whatsapp_template_delivered'       => trim($request->input('whatsapp_template_delivered', 'order_delivered')),
            'whatsapp_template_canceled'        => trim($request->input('whatsapp_template_canceled', 'order_canceled')),
        ];

        self::ensureColumnsExist();
        Setting::first()->update($data);

        return redirect()->back()->withSuccess(__('WhatsApp settings saved successfully.'));
    }

    /**
     * Send a test WhatsApp message.
     */
    public function test(Request $request)
    {
        $request->validate([
            'test_number' => 'required|string|max:20',
        ]);

        // Temporarily enable check bypass for test (credentials might be just saved)
        $setting = Setting::first();

        if (empty($setting->whatsapp_phone_number_id) || empty($setting->whatsapp_access_token)) {
            return redirect()->back()->withErrors(__('Please save your Phone Number ID and Access Token first before testing.'));
        }

        $testNumber = trim($request->input('test_number'));
        $siteTitle  = $setting->title ?? 'Namartzone';

        // Force enable temporarily for this test call
        $originalEnabled = $setting->whatsapp_enabled;
        if (!$originalEnabled) {
            $setting->whatsapp_enabled = 1;
            $setting->save();
        }

        $success = WhatsAppHelper::sendTestMessage($testNumber, $siteTitle);

        // Restore original state
        if (!$originalEnabled) {
            $setting->whatsapp_enabled = $originalEnabled;
            $setting->save();
        }

        if ($success) {
            return redirect()->back()->withSuccess(__('✅ Test WhatsApp message sent successfully! Check your WhatsApp.'));
        }

        return redirect()->back()->withErrors(__('❌ Failed to send test message. Please check your Phone Number ID and Access Token. See storage/logs/laravel.log for details.'));
    }
}
