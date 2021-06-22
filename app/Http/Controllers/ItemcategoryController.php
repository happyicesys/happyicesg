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
        $items = Item::whereItemcategoryId($itemcategory_id)->where('publish', 1)->orderBy('product_id')->get();
        return $items;
    }

    // return create new itemcategory page()
    public function create()
    {
        return view('user.itemcategory.create');
    }

    // store new created itemcategory(FormRequest $request)
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required'
        ]);

        $input = $request->all();
        $data = Itemcategory::create($input);
        return redirect()->action(
            'ItemcategoryController@edit', ['id' => $data->id]
        );
    }

    // retrieve single itemcategory api(int id)
    public function getItemcategoryApi($id)
    {
        $data = Itemcategory::findOrFail($id);
        return $data;
    }

    // return itemcategory edit page(int itemcategory)
    public function edit($id)
    {
    	$itemcategory = Itemcategory::findOrFail($id);
    	return view('user.itemcategory.edit', compact('itemcategory'));
    }

    // update itemcategory(FormRequest $request, int $custcategory_id)
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required'
        ]);

        $data = Itemcategory::findOrFail($id);
        $input = $request->all();
        $data->update($input);
        return redirect()->action(
            'ItemcategoryController@edit', ['id' => $data->id]
        );
    }

    // ajax destroy itemcategory (int $itemcategory_id)
    public function destroyAjax($id)
    {
        $data = Itemcategory::findOrFail($id);
        $data->delete();
        return $data->name . 'has been successfully deleted';
        return redirect('user');
    }

    // ajax destroy itemcategory (int $itemcategory_id)
    public function destroy($id)
    {
        $data = Itemcategory::findOrFail($id);
        $data->delete();
        return redirect('user');
    }
}
