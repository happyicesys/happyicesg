<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Zone;

class ZoneController extends Controller
{
    public function getAllZoneApi()
    {
        $zones = Zone::all();
        return $zones;
    }
}
