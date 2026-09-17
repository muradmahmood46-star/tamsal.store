<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $fillable = [
        'conversation_id',
        'sender_type',
        'sender_id',
        'message',
        'attachment',
        'is_read',
        'deleted_by_user',
        'deleted_by_vendor'
    ];

    public function conversation()
    {
        return $this->belongsTo('App\Models\Conversation', 'conversation_id');
    }

    public function sender()
    {
        return $this->belongsTo('App\Models\User', 'sender_id')->withDefault();
    }
}
