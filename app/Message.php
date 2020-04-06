<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'is_driver','sender_name', 'conversation_id', 'text', 'created_at'
    ];

    protected $hidden = [
        'updated_at'
    ];
}
