<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Item;

class ClientController extends Controller
{
    public function clientProduct()
    {
        $items = Item::wherePublish(1)->get();

        return $items;
    }
}
