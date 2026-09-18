<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DealItem extends Model
{
    protected $fillable = [
        'deal_id',
        'item_id',
        'original_price',
        'discounted_price',
    ];

    protected $casts = [
        'original_price' => 'float',
        'discounted_price' => 'float',
    ];

    public function deal()
    {
        return $this->belongsTo(Deal::class, 'deal_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}
