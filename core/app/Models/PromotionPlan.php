<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PromotionPlan extends Model
{
    protected $fillable = [
        'days',
        'price',
        'status'
    ];

    public static function ensureTable()
    {
        try {
            if (!Schema::hasTable('promotion_plans')) {
                Schema::create('promotion_plans', function (Blueprint $table) {
                    $table->id();
                    $table->integer('days');
                    $table->decimal('price', 10, 2);
                    $table->tinyInteger('status')->default(1);
                    $table->timestamps();
                });

                // Seed default durations & pricing
                $defaults = [
                    ['days' => 3, 'price' => 100.00],
                    ['days' => 7, 'price' => 200.00],
                    ['days' => 15, 'price' => 350.00],
                    ['days' => 30, 'price' => 600.00]
                ];
                foreach ($defaults as $plan) {
                    self::create([
                        'days' => $plan['days'],
                        'price' => $plan['price'],
                        'status' => 1
                    ]);
                }
            }
        } catch (\Throwable $e) {
            // Safety fallback
        }
    }
}
