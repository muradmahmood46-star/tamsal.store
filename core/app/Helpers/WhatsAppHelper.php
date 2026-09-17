<?php

namespace App\Helpers;

use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class WhatsAppHelper
{
    /**
     * Check if WhatsApp notifications are enabled and configured.
     */
    public static function isEnabled(): bool
    {
        $setting = Setting::first();
        return $setting
            && $setting->whatsapp_enabled == 1
            && !empty($setting->whatsapp_phone_number_id)
            && !empty($setting->whatsapp_access_token);
    }

    /**
     * Extract customer phone number from order.
     */
    public static function getPhoneNumber($order): ?string
    {
        // Prefer user model phone
        if ($order->user && !empty($order->user->phone)) {
            return self::formatNumber($order->user->phone);
        }

        // Fallback: billing phone
        $billing = json_decode($order->billing_info, true) ?: [];
        $phone = $billing['bill_phone'] ?? null;
        if ($phone) {
            return self::formatNumber($phone);
        }

        // Fallback: shipping phone
        $shipping = json_decode($order->shipping_info, true) ?: [];
        $phone = $shipping['ship_phone'] ?? null;
        if ($phone) {
            return self::formatNumber($phone);
        }

        return null;
    }

    /**
     * Format phone number: remove spaces/dashes, ensure starts with country code digits.
     */
    public static function formatNumber(string $phone): string
    {
        // Remove all non-digit characters except leading +
        $phone = preg_replace('/[^\d+]/', '', $phone);
        // Remove leading + for Meta API (it expects plain digits with country code)
        $phone = ltrim($phone, '+');
        // If starts with 0, assume Pakistan (+92) — adjust as needed
        if (strlen($phone) > 0 && $phone[0] === '0') {
            $phone = '92' . substr($phone, 1);
        }
        return $phone;
    }

    /**
     * Get customer name from order.
     */
    public static function getCustomerName($order): string
    {
        if ($order->user && !empty($order->user->name)) {
            return $order->user->name;
        }
        $billing = json_decode($order->billing_info, true) ?: [];
        $name = trim(($billing['bill_first_name'] ?? '') . ' ' . ($billing['bill_last_name'] ?? ''));
        return $name ?: 'Valued Customer';
    }

    /**
     * Send a WhatsApp template message via Meta Cloud API.
     *
     * @param  string  $to          Phone number with country code (e.g. 923001234567)
     * @param  string  $template    Approved template name in Meta Business Manager
     * @param  array   $variables   Array of positional variable values [ '{{1}}' value, '{{2}}' value, ... ]
     * @param  string  $language    Template language code (default: en_US)
     */
    public static function sendTemplateMessage(string $to, string $template, array $variables = [], string $language = 'en_US'): bool
    {
        if (!self::isEnabled()) {
            return false;
        }

        $setting = Setting::first();
        $phoneNumberId = $setting->whatsapp_phone_number_id;
        $accessToken   = $setting->whatsapp_access_token;

        // Build components array for template variables
        $components = [];
        if (!empty($variables)) {
            $params = array_map(fn($v) => ['type' => 'text', 'text' => (string) $v], $variables);
            $components[] = ['type' => 'body', 'parameters' => $params];
        }

        $payload = [
            'messaging_product' => 'whatsapp',
            'to'                => $to,
            'type'              => 'template',
            'template'          => [
                'name'       => $template,
                'language'   => ['code' => $language],
                'components' => $components,
            ],
        ];

        return self::callApi($phoneNumberId, $accessToken, $payload);
    }

    /**
     * Send a free-form text message (only works for numbers in test whitelist or within 24h window).
     */
    public static function sendTextMessage(string $to, string $text): bool
    {
        if (!self::isEnabled()) {
            return false;
        }

        $setting = Setting::first();
        $phoneNumberId = $setting->whatsapp_phone_number_id;
        $accessToken   = $setting->whatsapp_access_token;

        $payload = [
            'messaging_product' => 'whatsapp',
            'to'                => $to,
            'type'              => 'text',
            'text'              => ['body' => $text],
        ];

        return self::callApi($phoneNumberId, $accessToken, $payload);
    }

    /**
     * Execute the Meta Graph API call.
     */
    private static function callApi(string $phoneNumberId, string $accessToken, array $payload): bool
    {
        try {
            $url = "https://graph.facebook.com/v18.0/{$phoneNumberId}/messages";

            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_HTTPHEADER     => [
                    'Authorization: Bearer ' . $accessToken,
                    'Content-Type: application/json',
                ],
                CURLOPT_POSTFIELDS     => json_encode($payload),
                CURLOPT_TIMEOUT        => 15,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($curlError) {
                Log::error('WhatsApp API cURL error: ' . $curlError);
                return false;
            }

            $responseData = json_decode($response, true);

            if ($httpCode >= 200 && $httpCode < 300 && isset($responseData['messages'])) {
                Log::info('WhatsApp sent successfully to ' . ($payload['to'] ?? 'unknown'));
                return true;
            }

            Log::error('WhatsApp API error [' . $httpCode . ']: ' . $response);
            return false;

        } catch (\Throwable $e) {
            Log::error('WhatsApp send exception: ' . $e->getMessage());
            return false;
        }
    }

    // ─────────────────────────────────────────────────
    // Convenience methods for each order event
    // ─────────────────────────────────────────────────

    /**
     * Order Confirmed (Payment Accepted / Mark as Paid)
     */
    public static function sendOrderConfirmed($order): void
    {
        $phone = self::getPhoneNumber($order);
        if (!$phone) return;

        $setting      = Setting::first();
        $template     = $setting->whatsapp_template_order_confirmed ?? 'order_confirmed';
        $customerName = self::getCustomerName($order);
        $orderNum     = $order->transaction_number;
        $siteTitle    = $setting->title ?? 'Namartzone';

        // Variables: {{1}} = customer name, {{2}} = order number, {{3}} = site name
        self::sendTemplateMessage($phone, $template, [$customerName, $orderNum, $siteTitle]);
    }

    /**
     * Order In Progress (Accepted & dispatched)
     */
    public static function sendOrderInProgress($order): void
    {
        $phone = self::getPhoneNumber($order);
        if (!$phone) return;

        $setting      = Setting::first();
        $template     = $setting->whatsapp_template_in_progress ?? 'order_in_progress';
        $customerName = self::getCustomerName($order);
        $orderNum     = $order->transaction_number;
        $siteTitle    = $setting->title ?? 'Namartzone';

        self::sendTemplateMessage($phone, $template, [$customerName, $orderNum, $siteTitle]);
    }

    /**
     * Order Delivered
     */
    public static function sendOrderDelivered($order): void
    {
        $phone = self::getPhoneNumber($order);
        if (!$phone) return;

        $setting      = Setting::first();
        $template     = $setting->whatsapp_template_delivered ?? 'order_delivered';
        $customerName = self::getCustomerName($order);
        $orderNum     = $order->transaction_number;
        $siteTitle    = $setting->title ?? 'Namartzone';

        self::sendTemplateMessage($phone, $template, [$customerName, $orderNum, $siteTitle]);
    }

    /**
     * Order Canceled
     */
    public static function sendOrderCanceled($order): void
    {
        $phone = self::getPhoneNumber($order);
        if (!$phone) return;

        $setting      = Setting::first();
        $template     = $setting->whatsapp_template_canceled ?? 'order_canceled';
        $customerName = self::getCustomerName($order);
        $orderNum     = $order->transaction_number;
        $siteTitle    = $setting->title ?? 'Namartzone';

        self::sendTemplateMessage($phone, $template, [$customerName, $orderNum, $siteTitle]);
    }

    /**
     * Send a test WhatsApp message (free-form text) to verify API credentials.
     */
    public static function sendTestMessage(string $to, string $siteTitle = 'Namartzone'): bool
    {
        $to = self::formatNumber($to);
        $text = "✅ *{$siteTitle} — WhatsApp Test*\n\nYour WhatsApp API is configured correctly and working! 🎉\n\n_This is a test message from your admin panel._";
        return self::sendTextMessage($to, $text);
    }
}
