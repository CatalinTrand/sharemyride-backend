<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Variation extends Model
{
    protected $fillable = [
        'model_id', 'name', 'description', 'alimentazione', 'cambio', 'marce', 'trazione', 'bagagliaio',
        'passo', 'massa', 'cilindrata', 'consumo_urbano', 'consumo_extra_urbano', 'consumo_misto',
        'emissioni_co2', 'categoria_euro', 'velocita_max', 'accelerazione', 'coppia_max_regime', 'potenza_max_regime'
    ];
}
