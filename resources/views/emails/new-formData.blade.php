<html>
<head>
    <meta name="viewport" content="width=device-width"/>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
</head>
<body style="margin: 8px 0;font-family: 'Open Sans&quot';">

<div class="content" style="margin: 0 auto;max-width: 600px;background: #ededed;height: auto;">

    Nome e cognome: {{$formData->name}}<br>
    Email: {{$formData->email}}<br>
    Phone: {{$formData->phone}}<br>
    Offer name: {{$formData->brand . " " . $formData->vehicle . " " . $formData->variant }}<br>
    Anticipo: {{$formData->anticipo}}<br>
    Percorrenza: {{$formData->percorrenza}}<br>
    Durata: {{$formData->durata}}<br>
    Price: {{$formData->price}}<br>
    Discounted price: {{$formData->promo_price}}<br>
    User IP: {{$formData->user_ip}}<br>

</div>

</body>
</html>