<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VehicleImages extends Model {

    protected $table = "vehicles_images";

    protected $fillable = [
        'name', 'logo', 'description'
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];
}