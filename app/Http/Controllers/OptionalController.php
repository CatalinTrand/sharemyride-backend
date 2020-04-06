<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Optional;
use DB;

class OptionalController extends Controller
{
    public function index()
    {
        $optionals = Optional::all();

        return response()->json($optionals, 200);
    }

    public function for_offer($id)
    {
        $data = DB::select('
            select
                optional_id
            from
                offers_optionals
            where
                offer_id = ?
        ', [$id]);

        $optionals = [];

        foreach($data as $optional)
            $optionals[] = $optional->optional_id;

        if ($optionals)
            return response()->json($optionals, 200);
        else
            return response()->json([], 200);
    }

    public function store(Request $request)
    {
        $optional = new Optional();
        $optional->name = $request->name;

        if ($optional->save())
            return response()->json(['message' => 'New optional created.'], 201);
        else
            return response()->json(['message' => 'There was an error'], 500);
    }

    public function show($id)
    {
        $optional = Optional::where('id', $id)->get()->first();

        if ($optional)
            return response()->json(['optional' => $optional], 200);
        else
            return response()->json(['message' => 'There was an error retrieving the data'], 500);
    }

    public function update(Request $request)
    {
        $optional = Optional::find($request->id);

        $optional->name = $request->name;

        if ($optional->save())
            return response()->json(['message' => 'Optional updated.'], 201);
        else
            return response()->json(['message' => 'There was an error'], 500);
    }

    public function destroy($id)
    {
        if (Optional::destroy($id) !== 0)
            return response()->json(['message' => 'Optional deleted.'], 201);
        else
            return response()->json(['message' => 'There was an error'], 500);
    }
}
