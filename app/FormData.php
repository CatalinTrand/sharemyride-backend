<?php
/**
 * Created by PhpStorm.
 * User: Beeweb Dev
 * Date: 4/25/2019
 * Time: 2:42 PM
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

class FormData extends Model
{
    protected $table = 'form_data';

    protected $fillable = [
        'business',
        'name',
        'phone',
        'email',
        'region',
        'tax_id',
        'fiscal_code',
        'province',
        'note',
        'brand',
        'vehicle',
        'variant',
        'anticipo',
        'percorrenza',
        'durata',
        'price',
        'promo_price',
        'user_ip',
        "year",
        "month"
    ];
}