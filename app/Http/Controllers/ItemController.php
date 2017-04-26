<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\File;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Laracasts\Flash\Flash;
use Carbon\Carbon;
use App\Item;
use App\Profile;
use App\Unitcost;
use App\ImageItem;

class ItemController extends Controller
{
    //auth-only login can see
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getData()
    {
        $item =  Item::orderBy('product_id')->get();
        return $item;
    }

    public function index()
    {
        return view('item.index');
    }

    public function create()
    {
        return view('item.create');
    }

    public function store(ItemRequest $request)
    {
        $publish = $request->has('publish')? 1 : 0;
        $is_inventory = $request->has('is_inventory')? 1 : 0;
        $request->merge(array('publish' => $publish));
        $request->merge(array('is_inventory' => $is_inventory));
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

    public function show($id)
    {
        $item = Item::findOrFail($id);
        return view('item.edit', compact('item'));
    }

    public function edit($id)
    {
        $item = Item::findOrFail($id);
        return view('item.edit', compact('item'));
    }

    public function update(ItemRequest $request, $id)
    {
        $publish = $request->has('publish')? 1 : 0;
        $is_inventory = $request->has('is_inventory')? 1 : 0;
        $request->merge(array('publish' => $publish));
        $request->merge(array('is_inventory' => $is_inventory));
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

    public function destroy($id)
    {
        $item = Item::findOrFail($id);
        $item->delete();
        return redirect('item');
    }

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

    // retrive unit cost index api(Formrequest request)
    public function getUnitcostIndexApi(Request $request)
    {
        $total_amount = 0;
        $input = $request->all();
        $items = Item::whereNotNull('created_at');
        $profiles = Profile::whereNotNull('created_at');
        $unitcosts = Unitcost::whereNotNull('created_at');
        // reading whether search input is filled
        if($request->product_id) {
            $items = $items->where('product_id', '=', $request->product_id);
        }
        if($request->name) {
            $items = $items->where('name', 'LIKE', '%'.$request->name.'%');
        }
        if($request->sortName){
            $items = $items->orderBy($request->sortName, $request->sortBy ? 'asc' : 'desc');
        }
        if($request->profile_id) {
            $profiles = $profiles->where('id', '=', $request->profile_id);
        }

        $items = $items->get();
        $profiles = $profiles->get();
        $unitcosts = $unitcosts->get();

        if($request->exportExcel) {
            $this->exportUnitcostExcel($profiles, $items);
        }

        $data = [
            'profiles' => $profiles,
            'items' => $items,
            'unitcosts' => $unitcosts
        ];
        // dd($data['profiles']->toArray(), $data['items']->toArray());
        return $data;
    }

    // batch update unit costs (Request $request)
    public function batchUpdateUnitcost(Request $request)
    {
        $checkboxes = $request->checkboxes;
        $unit_costs = $request->unit_costs;
        $profile_ids = $request->profile_ids;
        $item_ids = $request->item_ids;

        if($checkboxes) {
            foreach($checkboxes as $index => $checkbox) {
                $unitcost = Unitcost::where('profile_id', $profile_ids[$index])->where('item_id', $item_ids[$index])->first();
                if($unit_costs[$index]) {
                    if($unitcost) {
                        $unitcost->unit_cost = $unit_costs[$index];
                        $unitcost->item_id = $item_ids[$index];
                        $unitcost->profile_id = $profile_ids[$index];
                        $unitcost->save();
                    }else {
                        $unitcost = new Unitcost;
                        $unitcost->unit_cost = $unit_costs[$index];
                        $unitcost->item_id = $item_ids[$index];
                        $unitcost->profile_id = $profile_ids[$index];
                        $unitcost->save();
                    }
                }else {
                    if($unitcost) {
                        $unitcost->delete();
                    }
                }
            }
        }else {
            Flash::error('Please select at least one item to update');
        }

        return redirect('/item');
    }

    // export unit cost excel(Collection $profiles, Collection $items, Collection $unitcosts)
    private function exportUnitcostExcel($profiles, $items)
    {
        $title = 'Unit Cost';
        Excel::create($title.'_'.Carbon::now()->format('dmYHis'), function($excel) use ($profiles, $items) {
            $excel->sheet('sheet1', function($sheet) use ($profiles, $items) {
                $sheet->setAutoSize(true);
                $sheet->setColumnFormat(array(
                    'A:T' => '@'
                ));
                $sheet->loadView('item.excel_unitcost', compact('profiles', 'items'));
            });
        })->download('xls');
    }
}
