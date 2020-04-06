<?php

namespace App\Http\Controllers;

use App\OffersTabs;
use App\Tab;
use Illuminate\Http\Request;

class TabController extends Controller
{
    public function index()
    {
        $tabs = Tab::all();

        return response()->json($tabs, 200);
    }

    public function update_tab_visibility(Request $request)
    {
        $tab = Tab::find($request->tab_id);

        if ($tab)
        {
            $tab->visible_home = !$tab->visible_home;

            if ($tab->save())
                return response()->json(['message' => 'Tab updated.'], 201);
            else
                return response()->json(['message' => 'There was an error'], 500);
        }
    }

    public function for_offer($id)
    {
        $data = OffersTabs::where('offer_id',$id)->get();

        $tabs = [];

        foreach($data as $tab)
            $tabs[] = $tab->tab_id;

        if ($tabs)
            return response()->json($tabs, 200);
        else
            return response()->json([], 200);
    }

    public function link_offer_tabs(Request $request){
        foreach ($request->tabs as $tab){
            $offer_tab = new OffersTabs();
            $offer_tab->offer_id = $request->offer_id;
            $offer_tab->tab_id = $tab;

            $offer_tab->save();
        }
    }

    public function store(Request $request)
    {
        $tab = new Tab();
        $tab->name = $request->name;
        $tab->description = $request->description;
        $tab->color = $request->color;

        if ($tab->save())
            return response()->json(['message' => 'New tab created.'], 201);
        else
            return response()->json(['message' => 'There was an error'], 500);
    }

    public function show($id)
    {
        $tab = Tab::where('id', $id)->get()->first();

        if ($tab)
            return response()->json(['tab' => $tab], 200);
        else
            return response()->json(['message' => 'There was an error retrieving the data'], 500);
    }

    public function update(Request $request)
    {
        $tab = Tab::find($request->id);

        $tab->name = $request->name;
        $tab->description = $request->description;
        $tab->color = $request->color;

        if ($tab->save())
            return response()->json(['message' => 'Tab updated.'], 201);
        else
            return response()->json(['message' => 'There was an error'], 500);
    }

    public function destroy($id)
    {
        if (Tab::destroy($id) !== 0)
            return response()->json(['message' => 'Tab deleted.'], 201);
        else
            return response()->json(['message' => 'There was an error'], 500);
    }
}
