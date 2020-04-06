<?php
/**
 * Created by PhpStorm.
 * User: Beeweb Dev
 * Date: 4/19/2019
 * Time: 7:51 PM
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

class OffersTags extends Model
{
    protected $table = "offers_tags";

    protected $fillable = [
        'name', 'color'
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];
}