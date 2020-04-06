<?php

namespace App\Http\Controllers;

use App\Brand;
use App\FormData;
use App\Http\Traits\SendMailTrait;
use App\Optional;
use App\Tab;
use App\Variation;
use App\Vehicle;
use App\VehicleImages;
use Exception;
use http\Env\Response;
use Illuminate\Http\Request;
use App\Offer;
use App\OfferOptional;
use App\OffersTabs as OfferTab;
use DB;
use Intervention\Image\ImageManagerStatic as Image;
use phpDocumentor\Reflection\Types\Array_;
use App\Http\Traits\ImagesPath;

class OfferController extends Controller
{

    use SendMailTrait;
    use ImagesPath;

    public function index()
    {
        $offers = DB::select('
            select
                o.id, b.name as brand_name, veh.name as vehicle_name, var.name as variation_name, o.default_image, o.offer_type
            from
                offers o, variations var, vehicles veh, brands b
            where
            var.id = o.variation_id and veh.id = var.vehicle_id and b.id = veh.brand_id
        ');

        return response()->json($offers, 200);
    }

    public function index_slider()
    {
        $offers_visible = DB::select('
            select
                *
            from
                vehicles
            where
                in_slider = 1
            order by
                case when slide_order is null then id else 0 end, slide_order
            asc
        ');

        forEach ($offers_visible as $vehicle) {
            $vehicle->brand_name = Brand::find($vehicle->brand_id)->name;
            $vehicle->images = VehicleImages::where('vehicle_id',$vehicle->id)->get();
        }

        $leftovers = DB::select('
            select
                *
            from
                vehicles
            where
                in_slider = 0
        ');

        forEach ($leftovers as $vehicle) {
            $vehicle->brand_name = Brand::find($vehicle->brand_id)->name;
            $vehicle->images = VehicleImages::where('vehicle_id',$vehicle->id)->get();
        }

        $offers = $offers_visible;

        foreach ($leftovers as $offer)
            $offers[] = $offer;

        return response()->json($offers, 200);
    }

    public function swapPlacesInSlider(Request $request) {
        $offer1 = Vehicle::find($request->id1);
        $offer2 = Vehicle::find($request->id2);

        $aux = $offer1->slide_order;
        $offer1->slide_order = $offer2->slide_order;
        $offer2->slide_order = $aux;

        $offer1->save();
        $offer2->save();

        return $request;
    }

    public static function getPrice($offer)
    {
        if (!strcmp($offer->offer_type, "Privati")) {
            if ($offer->prezzo_privati_discount != null)
                return (int)$offer->prezzo_privati_discount;
            return (int)$offer->prezzo_privati;
        } else {
            $parts = explode(";", $offer->anticipo_4);
            if (strcmp($parts[4], "") != 0)
                return (int)$parts[4];
            return (int)$parts[3];
        }
    }

    public static function sortResult($a, $b)
    {
        return OfferController::getPrice($a) - OfferController::getPrice($b);
    }

    public function topOffers()
    {
        $result = new \stdClass();
        $result->default = [];

        //citycar
        $result->citycar = DB::select("
            select o.* , var.name as 'variant_name', v.name as 'vehicle_name', b.name as 'brand_name', var.short_name as 'short_variant_name'
              from offers o, variations var, vehicles v, brands b
              where o.variation_id = var.id and var.vehicle_id = v.id and v.brand_id = b.id and v.tipologia_auto = 'Citycar' and o.offer_type = 'Aziende'
        ");

        usort($result->citycar, array($this, "sortResult"));
        $result->citycar = array_slice($result->citycar, 0, 8);
        $result->default = array_merge($result->default, array_slice($result->citycar, 0, 2));

        //berlina
        $result->berlina = DB::select("
            select o.* , var.name as 'variant_name', v.name as 'vehicle_name', b.name as 'brand_name', var.short_name as 'short_variant_name'
              from offers o, variations var, vehicles v, brands b
              where o.variation_id = var.id and var.vehicle_id = v.id and v.brand_id = b.id and v.tipologia_auto = 'Berlina' and o.offer_type = 'Aziende'
        ");

        usort($result->berlina, array($this, "sortResult"));
        $result->berlina = array_slice($result->berlina, 0, 8);
        $result->default = array_merge($result->default, array_slice($result->berlina, 0, 2));

        //station wagon
        $result->station_wagon = DB::select("
            select o.* , var.name as 'variant_name', v.name as 'vehicle_name', b.name as 'brand_name', var.short_name as 'short_variant_name'
              from offers o, variations var, vehicles v, brands b
              where o.variation_id = var.id and var.vehicle_id = v.id and v.brand_id = b.id and v.tipologia_auto = 'Station Wagon' and o.offer_type = 'Aziende'
        ");

        usort($result->station_wagon, array($this, "sortResult"));
        $result->station_wagon = array_slice($result->station_wagon, 0, 8);
        $result->default = array_merge($result->default, array_slice($result->station_wagon, 0, 2));

        //monovolume
        $result->monovolume = DB::select("
            select o.* , var.name as 'variant_name', v.name as 'vehicle_name', b.name as 'brand_name', var.short_name as 'short_variant_name'
              from offers o, variations var, vehicles v, brands b
              where o.variation_id = var.id and var.vehicle_id = v.id and v.brand_id = b.id and v.tipologia_auto = 'Monovolume' and o.offer_type = 'Aziende'
        ");

        usort($result->monovolume, array($this, "sortResult"));
        $result->monovolume = array_slice($result->monovolume, 0, 8);
        $result->default = array_merge($result->default, array_slice($result->monovolume, 0, 2));

        //suv / crossover
        $result->suv_crossover = DB::select("
            select o.* , var.name as 'variant_name', v.name as 'vehicle_name', b.name as 'brand_name', var.short_name as 'short_variant_name'
              from offers o, variations var, vehicles v, brands b
              where o.variation_id = var.id and var.vehicle_id = v.id and v.brand_id = b.id and v.tipologia_auto = 'Suv / Crossover' and o.offer_type = 'Aziende'
        ");

        usort($result->suv_crossover, array($this, "sortResult"));
        $result->suv_crossover = array_slice($result->suv_crossover, 0, 8);
        $result->default = array_merge($result->default, array_slice($result->suv_crossover, 0, 2));

        //veicolo commerciali
        $result->veicolo_commerciali = DB::select("
            select o.* , var.name as 'variant_name', v.name as 'vehicle_name', b.name as 'brand_name', var.short_name as 'short_variant_name'
              from offers o, variations var, vehicles v, brands b
              where o.variation_id = var.id and var.vehicle_id = v.id and v.brand_id = b.id and v.tipologia_auto = 'Veicolo Commerciali' and o.offer_type = 'Aziende'
        ");

        usort($result->veicolo_commerciali, array($this, "sortResult"));
        $result->veicolo_commerciali = array_slice($result->veicolo_commerciali, 0, 8);
        $result->default = array_merge($result->default, array_slice($result->veicolo_commerciali, 0, 2));

        return response()->json($result, 200);
    }

    public function listing(Request $request)
    {
        if (!strcmp($request->bussiness, "true")) {
            $o_type = 'Aziende';
            $columns = 'o.anticipo_4';
        } else {
            $o_type = 'Privati';
            $columns = 'o.prezzo_privati, o.prezzo_privati_discount, o.anticipo_privati';
        }

        $offers = DB::select("
            select
                o.id, v.name as variation_name, o.variation_id, o.default_image, o.offer_type, " . $columns . "
            from
                offers o
            inner join variations v
                on
            v.id = o.variation_id
                and
            o.offer_type = '" . $o_type . "'
        ");

        foreach ($offers as $offer) {
            $offer->variation = Variation::find($offer->variation_id);
            $offer->variation->vehicle = Vehicle::find($offer->variation->vehicle_id);
            $offer->variation->vehicle->brand = Brand::find($offer->variation->vehicle->brand_id);
        }

        return response()->json($offers, 200);
    }

    public function listingByID($id)
    {
        $offer = Offer::find($id);

        if ($offer) {
            $offer->variation = Variation::find($offer->variation_id);
            $offer->variation->vehicle = Vehicle::find($offer->variation->vehicle_id);
            $offer->variation->vehicle->brand = Brand::find($offer->variation->vehicle->brand_id);
            $vehicleImages = VehicleImages::where('vehicle_id', $offer->variation->vehicle->id)->get();
            $offer->variation->vehicle->images = $vehicleImages;
            $optionals = [];
            $options = OfferOptional::where('offer_id', $offer->id)->get();
            foreach ($options as $option) {
                array_push($optionals, Optional::find($option->optional_id));
            }
            $offer->options = $optionals;
            return response()->json(['offer' => $offer], 200);
        } else
            return response()->json(['message' => 'There was an error retrieving the data'], 500);
    }

    public function folder_exist($folder)
    {
        $path = realpath($folder);
        return ($path !== false && is_dir($path)) ? true : false;
    }

    public function store(Request $request)
    {
        $offer = new Offer();
        $offer->variation_id = $request->variation_id;

        $offer->description = $request->description;
        $offer->interni = $request->interni;
        $offer->offer_type = $request->offer_type;

        if ($request->offer_type == 'Privati') {
            $offer->anticipo_privati = $request->anticipo_privati;
            $offer->prezzo_privati = $request->prezzo_privati;
            $offer->prezzo_privati_discount = $request->prezzo_privati_discount;
        } else if ($request->offer_type == 'Aziende') {
            $offer->anticipo_1 = $request->anticipo_1;
            $offer->anticipo_2 = $request->anticipo_2;
            $offer->anticipo_3 = $request->anticipo_3;
            $offer->anticipo_4 = $request->anticipo_4;
        }

        if ($request->hasFile('default_image')) {
            $image = $request->file('default_image');

            $image_name = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME) . '-' . str_random(6);

            $offer->default_image = $image_name;

            $destinationPath = $this->get_images_path() . "offers/";

            if (!$this->folder_exist($destinationPath))
                mkdir($destinationPath, 0777, true);

            $image_location = $destinationPath . $image_name . '.' . 'png';
            chmod($destinationPath, 0755);

            $imageToBeSaved = Image::make($image->getRealPath())->encode('png');
            $imageToBeSaved->save($image_location);

            // make thumb for listing

            $thumb_image_name = $image_name . '-thumb';
            $thumb_destination = $this->get_images_path() . "offers/";

            $thumb_location = $thumb_destination . $thumb_image_name . '.' . 'png';

            $thumbToBeSaved = Image::make($image->getRealPath())->encode('png');

            // resize the image to a width of 250 and constrain aspect ratio (auto height)
            $thumbToBeSaved->resize(250, null, function ($constraint) {
                $constraint->aspectRatio();
            });

            $thumbToBeSaved->save($thumb_location);

            // end make thumb for listing
        }

        if ($request->tag)
            $offer->tag_id = $request->tag;
        else
            $offer->tag_id = null;

        if ($offer->save()) {
            if ($request->chosen_optionals) {
                $chosen_optionals = json_decode($request->chosen_optionals);

                foreach ($chosen_optionals as $optional_id) {
                    $optional = new OfferOptional();

                    $optional->offer_id = $offer->id;
                    $optional->optional_id = $optional_id;

                    $optional->save();
                }
            }

            if ($request->chosen_tabs) {
                $chosen_tabs = json_decode($request->chosen_tabs);

                foreach ($chosen_tabs as $tabID) {
                    $tab = new OfferTab();

                    $tab->offer_id = $offer->id;
                    $tab->tab_id = $tabID;

                    $tab->save();
                }
            }

            return response()->json(['message' => 'New offer created.'], 201);
        } else
            return response()->json(['message' => 'There was an error'], 500);
    }

    public function show($id)
    {
        $offer = Offer::where('id', $id)->get()->first();

        if ($offer)
            return response()->json(['offer' => $offer], 200);
        else
            return response()->json(['message' => 'There was an error retrieving the data'], 500);
    }

    public function update(Request $request)
    {
        $offer = Offer::find($request->id);

        $offer->variation_id = $request->variation_id;
        $offer->description = $request->description;
        $offer->interni = $request->interni;
        $offer->offer_type = $request->offer_type;

        if ($request->offer_type == 'Privati') {
            $offer->anticipo_1 = NULL;
            $offer->anticipo_2 = NULL;
            $offer->anticipo_3 = NULL;
            $offer->anticipo_4 = NULL;

            $offer->anticipo_privati = $request->anticipo_privati;
            $offer->prezzo_privati = $request->prezzo_privati;
            $offer->prezzo_privati_discount = $request->prezzo_privati_discount;
        } else if ($request->offer_type == 'Aziende') {
            $offer->anticipo_1 = $request->anticipo_1;
            $offer->anticipo_2 = $request->anticipo_2;
            $offer->anticipo_3 = $request->anticipo_3;
            $offer->anticipo_4 = $request->anticipo_4;

            $offer->anticipo_privati = NULL;
            $offer->prezzo_privati = NULL;
            $offer->prezzo_privati_discount = NULL;
        }

        if ($request->default_image) {
            $image = $request->file('default_image');
            $image_name = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME) . '-' . str_random(6);

            $offer->default_image = $image_name;

            $destinationPath = $this->get_images_path() . "offers/";

            if (!$this->folder_exist($destinationPath))
                mkdir($destinationPath, 0777, true);

            $image_location = $destinationPath . $image_name . '.' . 'png';
            chmod($destinationPath, 0755);

            $imageToBeSaved = Image::make($image->getRealPath())->encode('png');
            $imageToBeSaved->save($image_location);

            // make thumb for listing 

            $thumb_image_name = $image_name . '-thumb';
            $thumb_destination = $this->get_images_path() . "offers/";

            $thumb_location = $thumb_destination . $thumb_image_name . '.' . 'png';

            $thumbToBeSaved = Image::make($image->getRealPath())->encode('png');

            // resize the image to a width of 250 and constrain aspect ratio (auto height)
            $thumbToBeSaved->resize(250, null, function ($constraint) {
                $constraint->aspectRatio();
            });

            $thumbToBeSaved->save($thumb_location);

            // end make thumb for listing
        }

        if ($request->tag)
            $offer->tag_id = $request->tag;

        if ($offer->save()) {
            if ($request->chosen_optionals) {
                $request->chosen_optionals = json_decode($request->chosen_optionals);

                $actual_optionals = DB::select('
                    select
                        id, optional_id
                    from
                        offers_optionals
                    where
                        offer_id = ?
                ', [$offer->id]);

                foreach ($actual_optionals as $index => $actual_optional)
                    $actual_optionals[$index]->optional_id = json_encode($actual_optional->optional_id);

                foreach ($actual_optionals as $actual_optional) {
                    if (array_search($actual_optional->optional_id, $request->chosen_optionals) === false)
                        OfferOptional::destroy($actual_optional->id);
                    else {
                        $poz = array_search($actual_optional->optional_id, $request->chosen_optionals);
                        array_splice($request->chosen_optionals, $poz, 1);
                    }
                }

                if (count($request->chosen_optionals) > 0) {
                    foreach ($request->chosen_optionals as $opt) {
                        $optional = new OfferOptional;

                        $optional->offer_id = $offer->id;
                        $optional->optional_id = $opt;

                        $optional->save();
                    }
                }
            } else {
                $optionals = OfferOptional::where('offer_id', $offer->id)->get();

                foreach ($optionals as $optional)
                    OfferOptional::destroy($optional->id);
            }

            if ($request->chosen_tabs) {
                $request->chosen_tabs = json_decode($request->chosen_tabs);

                $actual_tabs = DB::select('
                    select
                        id, tab_id
                    from
                        offers_tabs
                    where
                        offer_id = ?
                ', [$offer->id]);

                foreach ($actual_tabs as $index => $actual_tab)
                    $actual_tabs[$index]->tab_id = json_encode($actual_tab->tab_id);

                foreach ($actual_tabs as $actual_tab) {
                    if (array_search($actual_tab->tab_id, $request->chosen_tabs) === false)
                        OfferTab::destroy($actual_tab->id);
                    else {
                        $poz = array_search($actual_tab->tab_id, $request->chosen_tabs);
                        array_splice($request->chosen_tabs, $poz, 1);
                    }
                }

                if (count($request->chosen_tabs) > 0) {
                    foreach ($request->chosen_tabs as $tabID) {
                        $tab = new OfferTab();

                        $tab->offer_id = $offer->id;
                        $tab->tab_id = $tabID;

                        $tab->save();
                    }
                }
            } else {
                $tabs = OfferTab::where('offer_id', $offer->id)->get();

                foreach ($tabs as $tab)
                    OfferTab::destroy($tab->id);
            }

            return response()->json(['message' => 'Offer updated.'], 201);
        } else
            return response()->json(['message' => 'There was an error'], 500);
    }

    public function destroy($id)
    {
        if (Offer::destroy($id) !== 0)
            return response()->json(['message' => 'Offer deleted.'], 201);
        else
            return response()->json(['message' => 'There was an error'], 500);
    }

    public function sendFormData(Request $request)
    {

        $formData = new FormData();

        $formData->business = $request->business;
        $formData->name = $request->name;
        $formData->phone = $request->phone;
        $formData->email = $request->email;
        $formData->region = $request->region;
        $formData->tax_id = $request->tax_id;
        $formData->fiscal_code = $request->fiscal_code;
        $formData->province = $request->province;
        $formData->note = $request->note;
        $formData->brand = $request->brand_name;
        $formData->vehicle = $request->vehicle_name;
        $formData->variant = $request->variation_name;
        $formData->anticipo = $request->anticipo;
        $formData->percorrenza = $request->percorrenza;
        $formData->durata = $request->durata;
        $formData->price = $request->price;
        $formData->promo_price = $request->promo_price;
        $formData->user_ip = $request->ip();
        $formData->year = date("Y");
        $formData->month = date("M");

        // $this->attemptSend($formData, 'emails.new-formData');

        // OfferController::sendToMailup($formData);

        // $response = OfferController::sendToProwebsuite($formData, $request->alimentazione, $request->tipologia);

        //return response()->json($response);

        if ($formData->save())
            return response()->json(['message' => 'Form saved.'], 200);
        else
            return response()->json(['message' => 'There was an error.'], 500);
    }

    public function sendToMailup($formData)
    {
        //Auth to MailUp

        $curl = curl_init("https://services.mailup.com/Authorization/OAuth/Token");
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);

        curl_setopt($curl, CURLOPT_POSTFIELDS, rawurldecode(http_build_query(array(
            'grant_type' => 'password',
            'client_id' => 'd4dd6692-23fd-41a6-a783-84df17dfa9d5',
            'client_secret' => 'c4fe24af-c65f-40a1-98a4-42a270acf606',
            'username' => 'm24831',
            'password' => '$!#NdbWeb2017',
        ))));

        $token = json_decode(curl_exec($curl))->access_token;

        // Send data

        if ($formData->business == 1) {
            $link = "https://services.mailup.com/API/v1.1/Rest/ConsoleService.svc/Console/Group/226/Recipient";
        } else {
            $link = "https://services.mailup.com/API/v1.1/Rest/ConsoleService.svc/Console/Group/227/Recipient";
        }

        $headers = array(
            'Content-Type: application/json',
            sprintf('Authorization: Bearer %s', $token)
        );

        $curl = curl_init($link);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);


        $fields = [];

        $field1 = new \stdClass();
        $field1->Id = 1;
        $field1->Value = $formData->name;
        array_push($fields,$field1);

        $field1 = new \stdClass();
        $field1->Id = 2;
        $field1->Value = $formData->name;
        array_push($fields,$field1);

        if ($formData->business == 1){
            $field1 = new \stdClass();
            $field1->Id = 3;
            $field1->Value = $formData->region;
            array_push($fields,$field1);
        }

        $field1 = new \stdClass();
        $field1->Id = 5;
        $field1->Value = $formData->province;
        array_push($fields,$field1);

        $field1 = new \stdClass();
        $field1->Id = 11;
        $field1->Value = $formData->phone;
        array_push($fields,$field1);

        $field1 = new \stdClass();
        $field1->Id = 28;
        $field1->Value = $formData->user_ip;
        array_push($fields,$field1);

        $payload = json_encode(array(
            "Fields" => $fields,
            "Email" => $formData->email,
        ));

        curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);

        $result = json_decode(curl_exec($curl));
        curl_close($curl);
        return $result;
    }

    public function sendToProwebsuite($formData, $alimentazione, $tipologia)
    {

        if ($formData->business == 1) {
            $jsonArr[0]['ragione_sociale'] = $formData->region;
            $jsonArr[0]['piva'] = $formData->tax_id;
            $jsonArr[0]['codice_fiscale'] = '';
        } else {
            $jsonArr[0]['ragione_sociale'] = $formData->name;
            $jsonArr[0]['piva'] = '';
            $jsonArr[0]['codice_fiscale'] = $formData->fiscal_code;
        }

        $jsonArr[0]['email'] = $formData->email;
        $jsonArr[0]['indirizzo'] = ''; //TODO - address

        $jsonArr[0]['provincia'] = $formData->province;
        $jsonArr[0]['citta'] = $formData->province; //TODO - city
        $jsonArr[0]['telefono'] = $formData->phone;


        $jsonArr[0]['cellulare'] = $formData->phone;
        $jsonArr[0]['cap'] = ''; //TODO - postal code
        $jsonArr[0]['tipologia'] = $formData->business == 1 ? "A" : "P";
        $jsonArr[0]['note'] = $formData->note;
        $jsonArr[0]['marca'] = $formData->brand;
        $jsonArr[0]['prezzo'] = ($formData->promo_price == null || strcmp($formData->promo_price,"" == 0) ) ? $formData->price : $formData->promo_price;
        $jsonArr[0]['anticipo'] = $formData->anticipo;
        $jsonArr[0]['modello'] = $formData->variant . " " . $formData->vehicle;
        $jsonArr[0]['chilometri'] = $formData->percorrenza;
        $jsonArr[0]['durata'] = $formData->durata;
        $jsonArr[0]['carburante'] = $alimentazione;

        $access_token = OfferController::getAccessCode('http://noleggiosemplice.prowebsuite.com/api/login', 3, 'CdGfHuJSwBOcGeFJFjr7B0u1SMjgdrjzwF03sSfo', 'api1@noleggiosemplice.it', '123456__');

        return OfferController::postJsonValues('http://noleggiosemplice.prowebsuite.com/api/put-leads', $access_token, $jsonArr);
    }


    public function getAccessCode($loginUrl, $client_id, $client_secret, $email, $password)
    {

        try {

            $curl = curl_init($loginUrl);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, rawurldecode(http_build_query(array(
                'client_id' => $client_id,
                'client_secret' => $client_secret,
                'email' => $email,
                'password' => $password
            ))));

            $json = json_decode(curl_exec($curl));
            curl_close($curl);
            return $json->success->token;

        } catch (Exception $e) {
            die($e->getMessage());
        }

    }

    public function postJsonValues($postUrl, $token, $dataArr)
    {

        try {

            $data_string = json_encode($dataArr);

            $headers = array(
                'Content-Type: application/json',
                sprintf('Authorization: Bearer %s', $token)
            );

            $curl = curl_init($postUrl);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_TIMEOUT, 5);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);

            $output = curl_exec($curl);

            if ($output === false)
                throw new Exception('Curl error: ' . curl_error($curl), 1);
            else {
                curl_close($curl);
                return json_decode($output);
            }

        } catch (Exception $e) {
            die($e->getMessage());
        }

    }
}
