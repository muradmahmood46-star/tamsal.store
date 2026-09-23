<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class VendorAnnouncement extends Model
{
    protected $table = 'vendor_announcements';

    protected $fillable = [
        'title',
        'message',
        'badge_type',
        'status'
    ];

    /**
     * Auto-ensure table exists in database
     */
    public static function ensureTable()
    {
        if (!Schema::hasTable('vendor_announcements')) {
            Schema::create('vendor_announcements', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->longText('message');
                $table->string('badge_type')->default('info');
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });
        }
    }

    public function views()
    {
        return $this->hasMany(VendorAnnouncementView::class, 'announcement_id');
    }
}
