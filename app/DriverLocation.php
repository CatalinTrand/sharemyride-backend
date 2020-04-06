<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DriverLocation extends Model
{
    protected $fillable = [
        'driver_id', 'lat', 'lng'
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];
}
