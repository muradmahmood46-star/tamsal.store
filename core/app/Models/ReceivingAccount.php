<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReceivingAccount extends Model
{
    protected $fillable = [
        'payment_method',
        'account_name',
        'account_number',
        'note',
        'status'
    ];
}
