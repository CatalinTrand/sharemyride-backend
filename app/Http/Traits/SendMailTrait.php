<?php

namespace App\Http\Traits;

use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

trait SendMailTrait
{
    public function __construct()
    {
        config([
            'mail.host' => 'cloudsoftware.it',
            'mail.port' => '465',
            'mail.username' => 'info@cloudsoftware.it',
            'mail.password' => 'noleggio#@!2019'
        ]);
    }

    public function attemptSend($formData, $template)
    {
        $email = $formData->email;
        Mail::send(['html' => $template], ['formData' => $formData], function ($m) use ($email, $formData) {
            $m->from('info@cloudsoftware.it', 'V-ITA');
            $m->to($email)->subject('Richiedi per ' . $formData->brand . " " . $formData->vehicle . " " . $formData->variant);
        });

        $provinces = [
            'Roma',
            'Frosinone',
            'Latina',
            'Viterbo',
            'Rieti',
            'Teramo',
            'Pescara',
            'Fermo',
            'Chieti',
            'L’Aquila',
            'Ancona',
            'Ascoli Piceno',
            'Macerata',
            'Pesaro Urbino',
            'Perugia',
            'Terni',
            'Firenze',
            'Grosseto',
            'Arezzo',
            'Livorno',
            'Lucca',
            'Massa Carrara',
            'Pistoia',
            'Pisa',
            'Prato',
            'Siena'
        ];

        if (in_array($formData->province, $provinces)){
            $email = 'email2@email.com';
            Mail::send(['html' => $template], ['formData' => $formData], function ($m) use ($email, $formData) {
                $m->from('info@cloudsoftware.it', 'V-ITA');
                $m->to($email)->subject('Richiedi per ' . $formData->brand . " " . $formData->vehicle . " " . $formData->variant);
            });
        }
    }
}