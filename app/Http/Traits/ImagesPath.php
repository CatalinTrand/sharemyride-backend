<?php

namespace App\Http\Traits;

trait ImagesPath
{
    public function get_images_path()
    {
        return "C:/xampp/htdocs/donorium-shop/frontend/static/images/donorium/";
    }

    public function get_backend_path(){
        return "C:/xampp/htdocs/donorium-shop/backend/";
    }
}