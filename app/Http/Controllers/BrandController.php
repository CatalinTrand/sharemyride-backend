<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManagerStatic as Image;
use App\Brand;
use App\Http\Traits\ImagesPath;

class BrandController extends Controller
{
    use ImagesPath;

    public function index()
    {
        $brands = Brand::all();

        return response()->json($brands, 200);
    }

    public function update_brand_visibility(Request $request)
    {
        $brand = Brand::find($request->brand_id);

        if ($brand)
        {
            $brand->visible_home = !$brand->visible_home;

            if ($brand->save())
                return response()->json(['message' => 'Brand updated.'], 201);
            else
                return response()->json(['message' => 'There was an error'], 500);
        }
    }

    public function listing()
    {
        $brands = Brand::all();

        return response()->json($brands, 200);
    }

    public function folder_exist($folder)
    {
        $path = realpath($folder);
        return ($path !== false && is_dir($path)) ? true : false;
    }

    public function store(Request $request)
    {
        $brand = new Brand();
        $brand->name = $request->name;
        $brand->slug = $request->slug;
        $brand->description = $request->description;

        $image = $request->file('image');
        $image_name = date('Y_m_d_H_i_s_') . $image->getClientOriginalName();

        $brand->logo = $image_name;

        $destinationPath = $this->get_images_path() . "categories/";

        if (!$this->folder_exist($destinationPath))
            mkdir($destinationPath, 0777, true);

        $image_location = $destinationPath . $image_name;
        chmod($destinationPath, 0755);

        $imageToBeSaved = Image::make($image->getRealPath());
        $imageToBeSaved->save($image_location);

        if ($brand->save())
            return response()->json(['message' => 'New brand created.'], 201);
        else
            return response()->json(['message' => 'There was an error'], 500);
    }

    public function show($id)
    {
        $brand = Brand::where('id', $id)->get()->first();

        if ($brand)
            return response()->json(['brand' => $brand], 200);
        else
            return response()->json(['message' => 'There was an error retrieving the data'], 500);
    }

    public function update(Request $request)
    {
        $brand = Brand::find($request->id);

        $brand->name = $request->name;
        $brand->slug = $request->slug;
        $brand->description = $request->description;

        if ($request->logo)
        {
            $image = $request->file('logo');
            $image_name = date('Y_m_d_H_i_s_') . $image->getClientOriginalName();

            $brand->logo = $image_name;

            $destinationPath = $this->get_images_path() . "categories/";

            if (!$this->folder_exist($destinationPath))
                mkdir($destinationPath, 0777, true);

            $image_location = $destinationPath . $image_name;
            chmod($destinationPath, 0755);

            $imageToBeSaved = Image::make($image->getRealPath());
            $imageToBeSaved->save($image_location);
        }

        if ($brand->save())
            return response()->json(['message' => 'Brand updated.'], 201);
        else
            return response()->json(['message' => 'There was an error'], 500);
    }

    public function updateLogo(Request $request)
    {

        $brand = Brand::find($request->id);

        $image = $request->file('image');
        $image_name = date('Y_m_d_H_i_s_') . $image->getClientOriginalName();

        $brand->logo = $image_name;

        $destinationPath = $this->get_images_path() . "categories/";

        if (!$this->folder_exist($destinationPath))
            mkdir($destinationPath, 0777, true);

        $image_location = $destinationPath . $image_name;
        chmod($destinationPath, 0755);

        $imageToBeSaved = Image::make($image->getRealPath());
        $imageToBeSaved->save($image_location);

        if ($brand->save())
            return response()->json(['message' => 'Brand logo updated.'], 201);
        else
            return response()->json(['message' => 'There was an error'], 500);
    }

    public function destroy($id)
    {
        if (Brand::destroy($id) !== 0)
            return response()->json(['message' => 'Brand deleted.'], 201);
        else
            return response()->json(['message' => 'There was an error'], 500);
    }
}
