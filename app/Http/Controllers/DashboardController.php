<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\FormData as lead;
use App\Brand;

class DashboardController extends Controller
{
    public function get_statistics()
    {
        // leads for the past 12 months (total, aziende, privati) done

        // brands requests count (for the current month) (total, aziende, privati) (exact number and percentage)

        // % and exact number of leads for aziende and privati (current year)

        // % and exact number of brands (current year)

        $months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        $current_month = date("M");
        $year = date("Y");
        $brandsQuery = Brand::all('name');
        $brands = [];

        foreach ($brandsQuery as $brand)
            $brands[] = $brand->name;

        $stats = new \stdclass;
        $stats->leads_per_month_aziende = new \stdclass;
        $stats->leads_per_month_privati = new \stdclass;

        foreach ($months as $month)
        {
            $stats->leads_per_month_aziende->{$month} = lead::where('year', $year)->where('month', $month)->where('business', 1)->count();
            $stats->leads_per_month_privati->{$month} = lead::where('year', $year)->where('month', $month)->where('business', 0)->count();
        }

        $stats->leads_brands_current_month_aziende = new \stdclass;
        $stats->leads_brands_current_month_privati = new \stdclass;

        foreach ($brands as $brand)
        {
            $stats->leads_brands_current_month_aziende->{$brand} = lead::where('year', $year)->where('month', $current_month)->where('business', 1)->where('brand', $brand)->count();
            $stats->leads_brands_current_month_privati->{$brand} = lead::where('year', $year)->where('month', $current_month)->where('business', 0)->where('brand', $brand)->count();
        }
    
        return response()->json($stats, 201);
    }
}
