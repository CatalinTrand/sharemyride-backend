<?php
/**
 * Created by PhpStorm.
 * User: Catalin
 * Date: 07/09/2019
 * Time: 18:25
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{

    protected $table = "transactions";

    protected $fillable = [
        'cart_data', 'fiscal_data', 'delivery_data', 'paid', 'processed'
    ];
}