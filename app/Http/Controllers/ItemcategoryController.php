<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Itemcategory;
use App\Item;

class ItemcategoryController extends Controller
{
    // retrieve all itemcategories api()
    public function getIndexApi()
    {
    	$itemcategories = Itemcategory::with('items')->get();
    	return $itemcategories;
    }

    // retrieve items by give itemcategory_id (integer $itemcategory_id)
    public function getItemsByItemcategory($itemcategory_id)
    {
        $items = Item::whereItemcategoryId($itemcategory_id)->orderBy('product_id')->get();
        return $items;
    }
}
