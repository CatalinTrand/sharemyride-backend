<?php

namespace App\Http\Controllers;

use App\OffersTags;
use Illuminate\Http\Request;

class OffersTagsController extends Controller
{
    public function index() {
        $offerstags  = OffersTags::all();

        return response()->json($offerstags, 200);
    }

    public function show($id){

        $offertag = OffersTags::find($id);

        if ($offertag)
            return response()->json(['offertag' => $offertag], 200);
        else
            return response()->json(['message' => 'There was an error retrieving the data'], 500);
    }

    public function store(Request $request){

        $offertag = new OffersTags();
        $offertag->name = $request->name;
        $offertag->color = $request->color;

        if ($offertag->save())
            return response()->json(['message' => 'New tag created.'], 201);
        else
            return response()->json(['message' => 'There was an error'], 500);
    }

    public function update(Request $request){

        $offertag = OffersTags::find($request->id);
        $offertag->name = $request->name;
        $offertag->color = $request->color;

        if ($offertag->save())
            return response()->json(['message' => 'Tag updated.'], 201);
        else
            return response()->json(['message' => 'There was an error'], 500);
    }

    public function destroy($id){

        if(OffersTags::destroy($id))
            return response()->json(['message' => 'Tag deleted.'], 201);
        else
            return response()->json(['message' => 'There was an error'], 500);
    }
}
