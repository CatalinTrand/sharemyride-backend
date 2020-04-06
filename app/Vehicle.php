<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = [
        'brand_id', 'name', 'slug', 'description','is_clothing', 'price', 'special_price'
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];
}
