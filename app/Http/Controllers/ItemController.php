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
use App\Transaction;
use DB;

class ItemController extends Controller
{
    //auth-only login can see
    public function __construct()
    {
        $this->middleware('auth');
    }

    // item index page items api
    public function getData()
    {
        $items =  Item::withoutGlobalScopes()->orderBy('product_id')->get();
        $total_available = Item::sum('qty_now');
        $total_booked = Item::sum('qty_order');
        $data = [
            'items' => $items,
            'total_available' => $total_available,
            'total_booked' => $total_booked,
        ];
        return $data;
    }

    // return item index page
    public function index()
    {
        return view('item.index');
    }

    // return item create page
    public function create()
    {
        return view('item.create');
    }

    // store newly create item(Fromrequest request)
    public function store(ItemRequest $request)
    {
        request()->merge(array('publish' => request()->has('publish') ? 1 : 0));
        request()->merge(array('is_inventory' => request()->has('is_inventory') ? 1 : 0));
        request()->merge(array('is_commission' => request()->has('is_commission') ? 1 : 0));
        request()->merge(array('is_healthier' => request()->has('is_healthier') ? 1 : 0));
        request()->merge(array('is_halal' => request()->has('is_halal') ? 1 : 0));

        $item = Item::create(request()->all());

        if($file = request()->file('main_imgpath')){
            $name = (Carbon::now()->format('dmYHi')).$file->getClientOriginalName();
            $file->move('item_asset/'.$item->id.'/', $name);
            $item->main_imgpath = '/item_asset/'.$item->id.'/'.$name;
            $item->save();
        }

        if($desc_file = request()->file('desc_imgpath')) {
            $name = (Carbon::now()->format('dmYHi')).$desc_file->getClientOriginalName();
            $file->move('item_asset/desc/'.$item->id.'/', $name);
            $item->desc_imgpath = '/item_asset/desc/'.$item->id.'/'.$name;
            $item->save();
        }

        if($nutri_file = request()->file('nutri_imgpath')) {
            $name = (Carbon::now()->format('dmYHi')).$nutri_file->getClientOriginalName();
            $file->move('item_asset/nutri/'.$item->id.'/', $name);
            $item->nutri_imgpath = '/item_asset/nutri/'.$item->id.'/'.$name;
            $item->save();
        }

        return redirect('item');
    }

    // return single item page
    public function show($id)
    {
        $item = Item::findOrFail($id);
        return view('item.edit', compact('item'));
    }

    public function edit($id)
    {
        $item = Item::withoutGlobalScopes()->findOrFail($id);
        return view('item.edit', compact('item'));
    }

    public function update(ItemRequest $request, $id)
    {
        request()->merge(array('publish' => request()->has('publish') == 'true' ? 1 : 0));
        request()->merge(array('is_inventory' => request()->has('is_inventory') == 'true' ? 1 : 0));
        request()->merge(array('is_commission' => request()->has('is_commission') == 'true' ? 1 : 0));
        request()->merge(array('is_healthier' => request()->has('is_healthier') == 'true' ? 1 : 0));
        request()->merge(array('is_halal' => request()->has('is_halal') == 'true' ? 1 : 0));

        $item = Item::withoutGlobalScopes()->findOrFail($id);
        $item->update(request()->all());

        if($file = request()->file('main_imgpath')){
            File::delete(public_path().$item->main_imgpath);
            $name = (Carbon::now()->format('dmYHi')).$file->getClientOriginalName();
            $file->move('item_asset/'.$item->id.'/', $name);
            $item->main_imgpath = '/item_asset/'.$item->id.'/'.$name;
            $item->save();
        }

        if($desc_file = request()->file('desc_imgpath')) {
            File::delete(public_path().$item->desc_imgpath);
            $name = (Carbon::now()->format('dmYHi')).$desc_file->getClientOriginalName();
            $desc_file->move('item_asset/desc/'.$item->id.'/', $name);
            $item->desc_imgpath = '/item_asset/desc/'.$item->id.'/'.$name;
            $item->save();
        }

        if($nutri_file = request()->file('nutri_imgpath')) {
            File::delete(public_path().$item->nutri_imgpath);
            $name = (Carbon::now()->format('dmYHi')).$nutri_file->getClientOriginalName();
            $nutri_file->move('item_asset/nutri/'.$item->id.'/', $name);
            $item->nutri_imgpath = '/item_asset/nutri/'.$item->id.'/'.$name;
            $item->save();
        }

        return Redirect::action('ItemController@edit', $item->id);
    }

    public function destroy($id)
    {
        $item = Item::withoutGlobalScopes()->findOrFail($id);
        $item->delete();
        return redirect('item');
    }

    public function destroyAjax($id)
    {
        $item = Item::withoutGlobalScopes()->findOrFail($id);
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
        $item = Item::withoutGlobalScopes()->findOrFail($id);
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

    // show the item's qty on order page (int item_id)
    public function getItemQtyOrder($item_id)
    {
        $item = Item::withoutGlobalScopes()->findOrFail($item_id);

        return view('item.qtyorder', compact('item'));
    }

    // show the item's qty on order api (int item_id, formrequest request)
    public function getItemQtyOrderApi($item_id, Request $request)
    {
        $transactions = Transaction::with(['person', 'person.profile', 'person.custcategory'])
                        ->whereHas('deals', function($query) use ($item_id) {
                            $query->withoutGlobalScopes()->whereQtyStatus(1)->whereItemId($item_id);
                        });

        if($request->sortName){
            $transactions = $transactions->orderBy($request->sortName, $request->sortBy ? 'asc' : 'desc');
        }

        $transactions = $transactions->latest()->get();
        $data = [
            'transactions' => $transactions,
        ];

        return $data;
    }

    // retrieve item by id and reverse the activation status(integer $item_id)
    public function setActiveState($item_id)
    {
        $item = Item::withoutGlobalScopes()->findOrFail($item_id);
        $item->is_active = $item->is_active == 1 ? 0 : 1;
        $item->save();

        return Redirect::action('ItemController@edit', $item->id);
    }

    // return items horizontal th for price matrix
    public function getPriceMatrixItems()
    {
        $items = $this->filterPriceMatrixItems();

        return $items;
    }

    // return items horizontal th for price matrix
    public function getPriceMatrixPeople()
    {
        $builderArr = [];

        $people = $this->filterPriceMatrixPeople();

        $items = $this->filterPriceMatrixItems();

        foreach($items as $item) {
            foreach($people as $person) {

            }
        }

        return $people;
    }

    // return price matrix items filter api()
    private function filterPriceMatrixItems()
    {
        $product_id = request('product_id');
        $name = request('name');

        $items = new Item();

        if($product_id) {
            $items = $items->where('product_id', 'LIKE', '%'.$product_id.'%');
        }
        if($name) {
            $items = $items->where('name', 'LIKE', '%'.$name.'%');
        }

        $items = $items->orderBy('product_id')->get();

        return $items;
    }

    // return price matrix customers filter api()
    private function filterPriceMatrixPeople()
    {
        $cust_id = request('cust_id');
        $custcategory_id = request('custcategory_id');
        $company = request('company');

        $people = new Person();

        if($cust_id) {
            $people = $people->where('cust_id', 'LIKE', '%'.$cust_id.'%');
        }
        if($custcategory_id) {
            $people = $people->where('custcategory_id', $custcategory_id);
        }
        if($company) {
            $people = $people->where('company', 'LIKE', '%'.$company.'%');
        }

        $people = $people->orderBy('cust_id')->get();

        return $people;
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
