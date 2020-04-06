<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OfferOptional extends Model
{
    protected $table = "offers_optionals";

    protected $fillable = [
        'offer_id', 'optional_id'
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];
}
