<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class VendorNotification extends Model
{
    protected $table = 'vendor_notifications';

    protected $fillable = [
        'vendor_id',
        'type',
        'title',
        'message',
        'link',
        'icon',
        'badge_color',
        'is_read',
        'read_at',
    ];

    protected $dates = [
        'read_at',
        'created_at',
        'updated_at',
    ];

    /**
     * Auto-ensure table structure exists
     */
    public static function ensureTable()
    {
        try {
            if (!Schema::hasTable('vendor_notifications')) {
                Schema::create('vendor_notifications', function (Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('vendor_id')->index();
                    $table->string('type', 50)->default('general')->index(); // order, product_approved, product_rejected, fine_approved, deposit_approved, announcement
                    $table->string('title', 255);
                    $table->text('message')->nullable();
                    $table->string('link', 255)->nullable();
                    $table->string('icon', 100)->default('fas fa-bell');
                    $table->string('badge_color', 50)->default('primary');
                    $table->tinyInteger('is_read')->default(0)->index();
                    $table->timestamp('read_at')->nullable();
                    $table->timestamps();
                });
            }
        } catch (\Throwable $e) {}
    }

    /**
     * Helper to log a notification for a vendor
     */
    public static function log($vendorId, $type, $title, $message = null, $link = null, $icon = 'fas fa-bell', $badgeColor = 'primary')
    {
        self::ensureTable();

        if (empty($vendorId)) {
            return null;
        }

        return self::create([
            'vendor_id'   => $vendorId,
            'type'        => $type,
            'title'       => $title,
            'message'     => $message,
            'link'        => $link,
            'icon'        => $icon,
            'badge_color' => $badgeColor,
            'is_read'     => 0,
        ]);
    }

    /**
     * Broadcast notification to all active vendors (e.g., when a new announcement is posted)
     */
    public static function broadcastAll($type, $title, $message = null, $link = null, $icon = 'fas fa-bullhorn', $badgeColor = 'info')
    {
        self::ensureTable();

        $vendorIds = User::where('is_seller', 1)->pluck('id')->toArray();
        if (empty($vendorIds)) {
            return;
        }

        $now = now();
        $records = [];
        foreach ($vendorIds as $vId) {
            $records[] = [
                'vendor_id'   => $vId,
                'type'        => $type,
                'title'       => $title,
                'message'     => $message,
                'link'        => $link,
                'icon'        => $icon,
                'badge_color' => $badgeColor,
                'is_read'     => 0,
                'created_at'  => $now,
                'updated_at'  => $now,
            ];
        }

        self::insert($records);
    }

    /**
     * Count unread notifications for a specific vendor
     */
    public static function unreadCount($vendorId)
    {
        self::ensureTable();
        return self::where('vendor_id', $vendorId)->where('is_read', 0)->count();
    }
}
