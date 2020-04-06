<?php
/**
 * Created by PhpStorm.
 * User: Beeweb Dev
 * Date: 4/19/2019
 * Time: 8:05 PM
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

class OffersTabs extends Model
{
    protected $table = "offers_tabs";

    protected $fillable = [
        'offer_id','tab_id'
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];
}