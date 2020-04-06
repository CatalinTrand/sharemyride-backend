<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = [
        'driver_id', 'passengers'
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];
}
