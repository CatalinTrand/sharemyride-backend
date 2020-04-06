<?php

namespace App\Http\Controllers;

use App\Http\Traits\ImagesPath;
use App\OffersTabs;
use App\OffersTags;
use App\VehicleImages;
use Illuminate\Http\Request;
use DB;
use App\Offer;
use App\Variation;
use App\Vehicle;
use App\Brand;
use App\Tab;
use phpDocumentor\Reflection\DocBlock\Tag;

class HomepageController extends Controller
{
    use ImagesPath;

    public function update_slider(Request $request)
    {
        $offer = Vehicle::find($request->offer_id);

        if ($offer) {
            $offer->in_slider = !$offer->in_slider;

            if($offer->in_slider) {
                $currentlyInSlider = count(Vehicle::where('in_slider',1)->get());
                $offer->slide_order = $currentlyInSlider;
            } else {
                $offer->slide_order = null;
            }

            if ($offer->save())
                return response()->json(['message' => 'Slider updated.'], 201);
            else
                return response()->json(['message' => 'There was an error'], 500);
        }
    }

    public function countsByAlimentazione(Request $request)
    {
        return DB::select("
            select
                v.alimentazione as name, count(*) as count
            from
                offers o, variations v
            where
                o.variation_id = v.id
            group by v.alimentazione
        ");
    }

    public function getHomeData()
    {
        $data = new \stdClass;

        // slider offers
        $data->categories = Brand::all();
        $sliderProducts = Vehicle::where('in_slider','1')->orderBy('slide_order')->get();

        forEach ( $sliderProducts as $vehicle) {
            $vehicle->brand_name = Brand::find($vehicle->brand_id)->name;
            $vehicle->brand_slug = Brand::find($vehicle->brand_id)->slug;
            $vehicle->images = VehicleImages::where('vehicle_id',$vehicle->id)->get();
        }

        $data->sliderProducts = $sliderProducts;

        return response()->json($data, 200);
    }

    static function getLowestPriceAnticipo($offer){
        $standardPrice1 = explode(";",$offer->anticipo_1)[3];
        $promoPrice1 = explode(";",$offer->anticipo_1)[4];

        if(strcmp($promoPrice1, "") == 0)
            $promoPrice1 = $standardPrice1;

        $lowest = (int)($promoPrice1);

        $standardPrice2 = explode(";",$offer->anticipo_2)[3];
        $promoPrice2 = explode(";",$offer->anticipo_2)[4];

        if(strcmp($promoPrice2, "") == 0)
            $promoPrice2 = $standardPrice2;

        if((int)($promoPrice2) < $lowest)
            $lowest = (int)($promoPrice2);

        $standardPrice3 = explode(";",$offer->anticipo_3)[3];
        $promoPrice3 = explode(";",$offer->anticipo_3)[4];

        if(strcmp($promoPrice3, "") == 0)
            $promoPrice3 = $standardPrice3;

        if((int)($promoPrice3) < $lowest)
            $lowest = (int)($promoPrice3);

        $standardPrice4 = explode(";",$offer->anticipo_4)[3];
        $promoPrice4 = explode(";",$offer->anticipo_4)[4];

        if(strcmp($promoPrice4, "") == 0)
            $promoPrice4 = $standardPrice4;

        if((int)($promoPrice4) < $lowest)
            $lowest = (int)($promoPrice4);

        if( $lowest == $promoPrice1)
            return $offer->anticipo_1;

        if( $lowest == $promoPrice2)
            return $offer->anticipo_2;

        if( $lowest == $promoPrice3)
            return $offer->anticipo_3;

        return $offer->anticipo_4;
    }

    public function generateFeed()
    {

		return "muie marco";
		
        $allOffers = DB::select("
            select o.* , var.name as 'variant_name', v.name as 'vehicle_name', b.name as 'brand_name', b.slug as 'brand_slug', var.short_name as 'short_variant_name', var.alimentazione as 'alimentazione', v.tipologia_auto as 'category'
              from offers o, variations var, vehicles v, brands b
              where o.variation_id = var.id and var.vehicle_id = v.id and v.brand_id = b.id and o.offer_type = 'Aziende'
        ");

        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
        <rss xmlns:g=\"http://base.google.com/ns/1.0\" version=\"2.0\" >
            <title>Noleggio Semplice</title>
            <link>https://www.noleggiosemplice.it/</link>
            <description>Noleggio Semplice</description>";

        $serverNameSrc = "https://noleggiosemplice.it/";

        foreach ($allOffers as $offer) {
            $offerXML = "
            <item>
                <id>" . $offer->id . "</id>
                <title>" . $offer->brand_name . " " . $offer->vehicle_name . " " . $offer->variant_name . "</title>
                <link>https://noleggiosemplice.it/" . (strcmp($offer->offer_type, "Aziende" ) == 0 ? "noleggio-lungo-termine/" : "privati/") . str_replace(" ", "-", strtolower($offer->brand_name)) . "/" . str_replace(" ", "-", strtolower($offer->vehicle_name . "-" . ($offer->short_variant_name == null ? $offer->variant_name : $offer->short_variant_name) . "-" . $offer->id)) . "</link>
                <image_link>" . $serverNameSrc . "static/images/offers/" . $offer->default_image . "</image_link>
                <price>" . (strcmp($offer->offer_type, "Aziende" ) == 0 ? explode(";", HomepageController::getLowestPriceAnticipo($offer))[3] : $offer->prezzo_privati) . "</price>
                <sale_price>" . (strcmp($offer->offer_type, "Aziende" ) == 0 ? strcmp(explode(";", HomepageController::getLowestPriceAnticipo($offer))[4], "") == 0 ? explode(";", HomepageController::getLowestPriceAnticipo($offer))[3] : explode(";", HomepageController::getLowestPriceAnticipo($offer))[4] : ($offer->prezzo_privati_discount == null ? $offer->prezzo_privati : $offer->prezzo_privati_discount)) . "</sale_price>
                <brand>" . $offer->brand_name . "</brand>
                <product_type>" . $offer->category . "</product_type>
                <km_inclusi>" . (strcmp($offer->offer_type, "Aziende" ) == 0 ? explode(";", HomepageController::getLowestPriceAnticipo($offer))[2] : "40.000") . "</km_inclusi>
                <durata_mesi>" . (strcmp($offer->offer_type, "Aziende" ) == 0 ? explode(";", HomepageController::getLowestPriceAnticipo($offer))[1] : "18") . "</durata_mesi>
                <anticipo>" . (strcmp($offer->offer_type, "Aziende" ) == 0 ? explode(";", HomepageController::getLowestPriceAnticipo($offer))[0] : $offer->anticipo_privati) . "</anticipo>
                <alimentazione>" . $offer->alimentazione . "</alimentazione>
            </item>";

            $xml .= $offerXML;
        }

        $xml .= "
        </rss>
</xml>";

        $myfile = fopen($this->get_backend_path() . "/public/feed.xml", "w");
        fwrite($myfile, $xml);

        return $xml;
    }

    public function getListData(Request $request)
    {
        $o_type = $request->business == 1 ? "Aziende" : "Privati";
        $columns = $request->business == 1 ? 'o.anticipo_1, o.anticipo_2, o.anticipo_3, o.anticipo_4' : 'o.prezzo_privati, o.prezzo_privati_discount, o.anticipo_privati';

        $offers = DB::select("
            select
                o.id, o.tag_id, v.name as variation_name, o.variation_id, o.default_image, o.offer_type, o.description," . $columns . "
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
            $offer->tag = $offer->tag_id == 0 ? null : OffersTags::find($offer->tag_id);
            $offer->promo = [];

            $links = OffersTabs::where('offer_id', $offer->id)->get();

            foreach ($links as $link)
                array_push($offer->promo, Tab::find($link->tab_id));
        }

        $brands = DB::Select('
            select id, name from brands order by name
        ');

        $data = new \stdClass();
        $data->offers = $offers;
        $data->brands = $brands;
        $data->tabs = Tab::all();

        return response()->json($data, 200);
    }

    public function getAllBrandsData()
    {
        $brands = Brand::all();

        return response()->json(['brands' => $brands], 200);
    }

    public function getCategoryListData(Request $request)
    {
        $data = new \stdClass();

        $data->category = Brand::where('slug',$request->category)->get()[0];
        $products = Vehicle::where('brand_id', $data->category->id)->get();

        foreach ($products as $product) {
            $product->brand_name = Brand::find($product->brand_id)->name;
            $product->images = VehicleImages::where('vehicle_id', $product->id)->get();
        }

        $data->products = $products;

        return response()->json($data, 200);
    }

    public function sortResult($a, $b)
    {
        return $this->getPrice($a) - $this->getPrice($b);
    }

    public function getPrice($offer)
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

    public function getSliderOffers()
    {
        $slider_offers = DB::select('
            select
                *
            from
                offers
            where
                in_slider = 1
            order by
                slide_order
            asc
        ');

        forEach ($slider_offers as $offer) {
            $variation = Variation::find($offer->variation_id);
            $vehicle = Vehicle::find($variation->vehicle_id);
            $brand = Brand::find($vehicle->brand_id);
            $offer->fullName = $brand->name . " " . $vehicle->name . " " . $variation->name;
        }

        $data = new \stdClass;

        $data->slider_offers = $slider_offers;

        return response()->json($data, 200);
    }


}
