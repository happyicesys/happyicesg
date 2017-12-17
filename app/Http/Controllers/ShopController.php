<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class ShopController extends Controller
{
    // return client shop index view()
    public function getShopIndex()
    {
    	return view('client.shop.index');
    }
}
