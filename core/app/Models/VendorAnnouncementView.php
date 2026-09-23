<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class VendorAnnouncementView extends Model
{
    protected $table = 'vendor_announcement_views';

    protected $fillable = [
        'vendor_id',
        'announcement_id'
    ];

    /**
     * Auto-ensure table exists in database
     */
    public static function ensureTable()
    {
        if (!Schema::hasTable('vendor_announcement_views')) {
            Schema::create('vendor_announcement_views', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('vendor_id')->index();
                $table->unsignedBigInteger('announcement_id')->index();
                $table->timestamps();
            });
        }
    }

    public function announcement()
    {
        return $this->belongsTo(VendorAnnouncement::class, 'announcement_id');
    }

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }
}
