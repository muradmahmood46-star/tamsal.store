<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PromotionTag extends Model
{
    protected $fillable = [
        'name',
        'status'
    ];

    public static function ensureTable()
    {
        try {
            if (!Schema::hasTable('promotion_tags')) {
                Schema::create('promotion_tags', function (Blueprint $table) {
                    $table->id();
                    $table->string('name');
                    $table->tinyInteger('status')->default(1);
                    $table->timestamps();
                });

                // Seed default highlight tags
                $defaults = [
                    'Best Product',
                    'Most Selling',
                    'Trending',
                    'Hot Deal',
                    'Top Rated',
                    'Super Saver'
                ];
                foreach ($defaults as $tag) {
                    self::create([
                        'name' => $tag,
                        'status' => 1
                    ]);
                }
            }
        } catch (\Throwable $e) {
            // Safety fallback
        }
    }
}
