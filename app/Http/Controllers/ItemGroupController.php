<?php

namespace App\Http\Controllers;

use App\Item;
use App\ItemGroup;
use Illuminate\Http\Request;

use App\Http\Requests;

class ItemGroupController extends Controller
{
    // get index api
    public function getItemGroupsIndexApi()
    {
        // showing total amount init
        $total_amount = 0;
        // initiate the page num when null given
        $pageNum = request('pageNum') ? request('pageNum') : 100;

        $query = ItemGroup::with('items');

        if(request('name')) {
            $query = $query->where('name', 'LIKE', '%'.request('name').'%');
        }

        if(request('item_id')) {
            $items = request('item_id');
            if (count($items) == 1) {
                $items = [$items];
            }
            if(request('exclude_item')) {
                $query = $query->whereHas('items', function($query) use ($items) {
                    $query->whereNotIn('id', $items);
                });
            }else {
                $query = $query->whereHas('items', function($query) use ($items) {
                    $query->whereIn('id', $items);
                });
            }
        }

        if(request('sortName')){
            $query = $query->orderBy(request('sortName'), request('sortBy') ? 'asc' : 'desc');
        }

        if($pageNum == 'All'){
            $query = $query->orderBy('name', 'asc')->get();
        }else{
            $query = $query->orderBy('name', 'asc')->paginate($pageNum);
        }

        return [
            'itemGroups' => $query
        ];
    }

    // destroy single
    public function deleteItemGroupApi($id)
    {
        $model = ItemGroup::findOrFail($id);

        if($model->items) {
            foreach($model->items as $item) {
                $item->item_group_id = null;
                $item->save();
            }
        }
        $model->delete();
    }

    // unbind single
    public function unbindItemGroupAttachment($id)
    {
        $model = Item::findOrFail($id);
        $model->item_group_id = null;
        $model->save();
    }

    // add new single
    public function createItemGroupApi(Request $request)
    {
        $name = $request->name;

        if($name) {
            ItemGroup::create([
                'name' => $name
            ]);
        }
    }

    // bind category with group
    public function bindItemGroupAttachesApi(Request $request)
    {
        $item_group_id = $request->item_group_id;
        $item_id = $request->item_id;

        if($item_group_id and $item_id) {
            $model = Item::findOrFail($item_id);
            $model->item_group_id = $item_group_id;
            $model->save();
        }
    }
}
