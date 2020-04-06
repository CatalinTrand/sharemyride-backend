<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HomepageSlider extends Model
{
      protected $table = 'homepage_slider';

      protected $fillable = [
          'offer_id', 'order'
      ];
}
