<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = [
        'user_id', 's_lat', 's_lng', 'd_lat', 'd_lng', 'accepted', 'conversation_id'
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];
}
