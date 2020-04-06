<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
      protected $fillable = [
          'variation_id', 'description', 'interni', 'offer_type', 'anticipo_1', 'anticipo_2',
          'anticipo_3', 'anticipo_4', 'anticipo_privati', 'prezzo_privati', 'prezzo_privati_discount', 'in_slider', 'slide_order'
      ];
}
