<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class AdministrativeUnits extends Controller
{
    public function city()
    {
        $city = DB::select('SELECT * from city');

        return response() -> json($city, 200);
    }

    public function district(Request $request)
    {
        $district = DB::select('SELECT * from district where city_code = ?', [$request->city_code]);

        return response() -> json($district, 200);
    }

    public function ward(Request $request)
    {
        $ward = DB::select('SELECT * from ward where district_code = ?', [$request->district_code]);

        return response() -> json($ward, 200);
    }
}
