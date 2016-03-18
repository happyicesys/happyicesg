<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\File;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Laracasts\Flash\Flash;
use Carbon\Carbon;
use App\Item;
use App\ImageItem;

class ItemController extends Controller
{
    //auth-only login can see
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function getData()
    {
        $item =  Item::orderBy('product_id')->get();

        return $item;
    }

    /**
     * Return viewing page.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        return view('item.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('item.create');
    }

    /**
     * Store a newly created resource in storage.
     *Item
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ItemRequest $request)
    {
        $publish = $request->has('publish')? 1 : 0;

        $request->merge(array('publish' => $publish));

        $input = $request->all();

        $item = Item::create($input);

        if($request->file('main_imgpath')){

            $file = $request->file('main_imgpath');

            $name = (Carbon::now()->format('dmYHi')).$file->getClientOriginalName();

            $file->move('item_asset/'.$item->id.'/', $name);

            $item->main_imgpath = '/item_asset/'.$item->id.'/'.$name;

            $item->save();
        }

        return redirect('item');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $item = Item::findOrFail($id);

        return view('item.edit', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $item = Item::findOrFail($id);

        return view('item.edit', compact('item'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ItemRequest $request, $id)
    {
        $publish = $request->has('publish')? 1 : 0;

        $request->merge(array('publish' => $publish));

        $item = Item::findOrFail($id);

        $input = $request->all();

        $item->update($input);

        if($request->file('main_imgpath')){

            File::delete(public_path().$item->main_imgpath);

            $file = $request->file('main_imgpath');

            $name = (Carbon::now()->format('dmYHi')).$file->getClientOriginalName();

            $file->move('item_asset/'.$item->id.'/', $name);

            $item->main_imgpath = '/item_asset/'.$item->id.'/'.$name;

            $item->save();
        }

        return Redirect::action('ItemController@edit', $item->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $item = Item::findOrFail($id);

        $item->delete();

        return redirect('item');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return json
     */
    public function destroyAjax($id)
    {
        $item = Item::findOrFail($id);

        $item->delete();

        return $item->name . 'has been successfully deleted';
    }

    // find out how many images
    public function imageItem($item_id)
    {
        $imageitems = ImageItem::whereItemId($item_id)->get();

        return $imageitems;
    }

    // adding new photos
    public function addImage(Request $request, $id)
    {
        $item = Item::findOrFail($id);

        $file = $request->file('file');

        $name = (Carbon::now()->format('dmYHi')).$file->getClientOriginalName();

        $file->move('item_asset/'.$item->id.'/', $name);

        if($item->images()->create(['path' => "/item_asset/".$item->id."/{$name}"])){

            $item->img_remain = $item->img_remain - 1;

            $item->save();

        }else{

            Flash::error('Please Try Again');

        }

    }

    // destroy image
    public function destroyImageAjax($image_id)
    {
        $imageitem = ImageItem::findOrFail($image_id);

        $file = $imageitem->path;

        $path = public_path();

        File::delete($path.$file);

        $imageitem->delete();

        $item = Item::findOrFail($imageitem->item_id);

        $item->img_remain = $item->img_remain + 1;

        $item->save();

        return $imageitem->id . 'has been successfully deleted';
    }

    // mass editing the photo caption
    public function editCaption(Request $request, $item_id)
    {
        $captions = $request->caption;

        foreach($captions as $index => $caption){

            if($caption != ''){

                $imageitem = ImageItem::findOrFail($index);

                $imageitem->caption = $caption;

                $imageitem->save();

            }
        }

        return Redirect::action('ItemController@edit', $item_id);
    }

}
