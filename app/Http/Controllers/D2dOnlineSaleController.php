<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\D2dOnlineSale;

class D2dOnlineSaleController extends Controller
{
    // return all data in d2donlinesales item
    public function allApi()
    {
        $salesitems = D2dOnlineSale::with('item')->orderBy('sequence')->get();
        return $salesitems;
    }
}
