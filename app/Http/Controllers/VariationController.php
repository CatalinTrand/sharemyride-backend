<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Vehicle;
use Illuminate\Http\Request;
use App\Variation;
use DB;

class VariationController extends Controller
{
    public function index()
    {
        $variations = Variation::all();

        return response()->json($variations, 200);
    }

    public function get_variations_by_vehicle($vehicle_id)
    {
        $variations = DB::select('
            select
                id, name
            from
                variations
            where
                vehicle_id = ?
        ', [$vehicle_id]);

        return response()->json($variations, 200);
    }

    public function shortVariations()
    {
        $variations = DB::select('
            select
                v.id, vh.name as vehicle_name, v.name, v.alimentazione
            from
                variations v
            left join
                vehicles vh
            on
                vh.id = v.vehicle_id
        ');

        return response()->json($variations, 200);
    }

    public function shortVariationsForSelects()
    {
        $variations = DB::table('variations')->select('id', 'name')->get();

        return response()->json($variations, 200);
    }

    public function store(Request $request)
    {
        $variation = new Variation();
        $variation->name = $request->name;
        $variation->short_name = $request->short_name;
        $variation->vehicle_id = $request->vehicle_id;
        $variation->description = $request->description;
        $variation->alimentazione = $request->alimentazione;
        $variation->cambio = $request->cambio;
        $variation->marce = $request->marce;
        $variation->trazione = $request->trazione;
        $variation->bagagliaio = $request->bagagliaio;
        $variation->passo = $request->passo;
        $variation->massa = $request->massa;
        $variation->cilindrata = $request->cilindrata;
        $variation->consumo_urbano = $request->consumo_urbano;
        $variation->consumo_extra_urbano = $request->consumo_extra_urbano;
        $variation->consumo_misto = $request->consumo_misto;
        $variation->emissioni_co2 = $request->emissioni_co2;
        $variation->categoria_euro = $request->categoria_euro;
        $variation->velocita_max = $request->velocita_max;
        $variation->accelerazione = $request->accelerazione;
        $variation->coppia_max_regime = $request->coppia_max_regime;
        $variation->potenza_max_regime = $request->potenza_max_regime;

        if ($variation->save())
            return response()->json(['message' => 'New variation created.'], 201);
        else
            return response()->json(['message' => 'There was an error'], 500);
    }

    public function show($id)
    {
        $variation = Variation::where('id', $id)->get()->first();

        if ($variation) {
            $variation->vehicle_name = Vehicle::find($variation->vehicle_id)->name;
            $brand = Brand::find(Vehicle::find($variation->vehicle_id)->id);
            $variation->brand_id = $brand->id;
            $variation->brand_name = $brand->name;
            return response()->json(['variation' => $variation], 200);
        } else
            return response()->json(['message' => 'There was an error retrieving the data'], 500);
    }

    public function update(Request $request)
    {
        $variation = Variation::find($request->id);

        $variation->name = $request->name;
        $variation->short_name = $request->short_name;
        $variation->vehicle_id = $request->vehicle_id;
        $variation->description = $request->description;
        $variation->alimentazione = $request->alimentazione;
        $variation->cambio = $request->cambio;
        $variation->marce = $request->marce;
        $variation->trazione = $request->trazione;
        $variation->bagagliaio = $request->bagagliaio;
        $variation->passo = $request->passo;
        $variation->massa = $request->massa;
        $variation->cilindrata = $request->cilindrata;
        $variation->consumo_urbano = $request->consumo_urbano;
        $variation->consumo_extra_urbano = $request->consumo_extra_urbano;
        $variation->consumo_misto = $request->consumo_misto;
        $variation->emissioni_co2 = $request->emissioni_co2;
        $variation->categoria_euro = $request->categoria_euro;
        $variation->velocita_max = $request->velocita_max;
        $variation->accelerazione = $request->accelerazione;
        $variation->coppia_max_regime = $request->coppia_max_regime;
        $variation->potenza_max_regime = $request->potenza_max_regime;

        if ($variation->save())
            return response()->json(['message' => 'Variation updated.'], 201);
        else
            return response()->json(['message' => 'There was an error'], 500);
    }

    public function destroy($id)
    {
        if (Variation::destroy($id) !== 0)
            return response()->json(['message' => 'Variation deleted.'], 201);
        else
            return response()->json(['message' => 'There was an error'], 500);
    }
}
