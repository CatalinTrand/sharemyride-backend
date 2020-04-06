<?php
/**
 * Created by PhpStorm.
 * User: Beeweb Dev
 * Date: 4/19/2019
 * Time: 7:40 PM
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

class Tab extends Model
{
    protected $table = "tabs";

    protected $fillable = [
        'name', 'description', 'color', 'visible_home'
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];
}
