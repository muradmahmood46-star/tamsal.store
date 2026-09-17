<?php

namespace App\Helpers;

use App\Models\Item;
use App\Models\User;

class ChatPolicyHelper
{
    /**
     * Map of written number words to digits (English + Roman Urdu)
     */
    protected static $wordMap = [
        'zero' => '0', 'sifar' => '0', 'cipher' => '0', 'oh' => '0',
        'one' => '1', 'aik' => '1', 'ek' => '1',
        'two' => '2', 'do' => '2', 'doo' => '2',
        'three' => '3', 'teen' => '3', 'tin' => '3',
        'four' => '4', 'char' => '4', 'chaar' => '4',
        'five' => '5', 'panch' => '5', 'paanch' => '5',
        'six' => '6', 'che' => '6', 'chay' => '6',
        'seven' => '7', 'sat' => '7', 'saat' => '7',
        'eight' => '8', 'aath' => '8', 'ath' => '8',
        'nine' => '9', 'nau' => '9'
    ];

    /**
     * Detect if message contains phone numbers or contact details
     */
    public static function containsPhoneNumber($text)
    {
        if (empty($text)) {
            return false;
        }

        $clean = strtolower($text);

        // 1. Direct Regex for PK / Intl formats with separators
        if (preg_match('/(?:\+?92|0092|0)[\s\-\.\_\/\,\(\)]*3[\s\-\.\_\/\,\(\)]*[0-9]{2}[\s\-\.\_\/\,\(\)]*[0-9]{3}[\s\-\.\_\/\,\(\)]*[0-9]{4}/', $clean)) {
            return true;
        }

        // 2. Normalize text by converting spelled words to digits
        $normalized = $clean;
        foreach (self::$wordMap as $word => $digit) {
            $normalized = preg_replace('/\b' . $word . '\b/i', $digit, $normalized);
        }

        // Check if normalized text matches standard phone regex
        if (preg_match('/(?:\+?92|0092|0)[\s\-\.\_\/\,\(\)]*3[\s\-\.\_\/\,\(\)]*[0-9]{2}[\s\-\.\_\/\,\(\)]*[0-9]{3}[\s\-\.\_\/\,\(\)]*[0-9]{4}/', $normalized)) {
            return true;
        }

        // 3. Extract only digits from normalized text
        $digitsOnly = preg_replace('/[^0-9]/', '', $normalized);

        // If total extracted digits contains an 11-digit PK mobile number
        if (preg_match('/(?:03[0-9]{9}|923[0-9]{9}|00923[0-9]{9})/', $digitsOnly)) {
            return true;
        }

        // 4. Any continuous or spaced block of 9 to 13 digits
        if (preg_match('/(?:(?:\d[\s\-\.\_\/\,\(\)]*){9,13})/', $normalized, $matches)) {
            $matchedDigits = preg_replace('/[^0-9]/', '', $matches[0]);
            if (strlen($matchedDigits) >= 9 && strlen($matchedDigits) <= 13) {
                return true;
            }
        }

        // 5. Contact keywords + 6+ digits
        $contactKeywords = ['whatsapp', 'wa', 'call', 'contact', 'rabta', 'number', 'num', 'phone', 'cell', 'mob', 'mobile', 'ph:'];
        foreach ($contactKeywords as $kw) {
            if (strpos($normalized, $kw) !== false) {
                if (preg_match('/(?:\d[\s\-\.\_]*){6,}/', $normalized)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check if a user is currently blocked from chat
     */
    public static function isUserBlocked($user)
    {
        if (!$user) {
            return false;
        }
        return ($user->chat_blocked == 1 || $user->is_seller_blocked == 1);
    }

    /**
     * Handle phone sharing policy violation (3-Strike System)
     *
     * @param User $user
     * @return array
     */
    public static function handleViolation($user)
    {
        if (!$user) {
            return [
                'is_blocked' => false,
                'warning_count' => 1,
                'message' => __('You cannot send phone numbers in chat. Contact exchange is strictly prohibited.')
            ];
        }

        $currentWarnings = (int)($user->chat_warnings_count ?? 0) + 1;
        $user->chat_warnings_count = $currentWarnings;

        if ($currentWarnings >= 3) {
            $user->chat_blocked = 1;
            $user->chat_blocked_reason = __('Account suspended for attempting to share phone numbers 3 times in chat.');

            // If user is a seller, block their store and hide products
            if ($user->isSeller()) {
                $user->is_seller_blocked = 1;
                if ($user->seller) {
                    $user->seller->update(['status' => 0]);
                }
                Item::where('vendor_id', $user->id)->update(['is_hidden_by_block' => 1]);
            }

            $user->save();

            return [
                'is_blocked' => true,
                'warning_count' => 3,
                'message' => __('You have attempted to send phone number 3 times. Your account has been BLOCKED for policy violations!')
            ];
        }

        $user->save();

        return [
            'is_blocked' => false,
            'warning_count' => $currentWarnings,
            'message' => __('You cannot send phone numbers in chat. If you try again, your account will be BLOCKED! (Warning :count of 3)', ['count' => $currentWarnings])
        ];
    }
}
