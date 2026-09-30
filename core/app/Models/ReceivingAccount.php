<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ReceivingAccount extends Model
{
    protected $fillable = [
        'payment_method',
        'account_name',
        'account_number',
        'note',
        'status'
    ];

    public static function ensureTable()
    {
        try {
            if (!Schema::hasTable('receiving_accounts')) {
                Schema::create('receiving_accounts', function (Blueprint $table) {
                    $table->id();
                    $table->string('payment_method');
                    $table->string('account_name');
                    $table->string('account_number');
                    $table->text('note')->nullable();
                    $table->tinyInteger('status')->default(1)->index();
                    $table->timestamps();
                });
            }
        } catch (\Throwable $e) {}
    }
}
