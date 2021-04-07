<?php

namespace App\Http\Controllers;

use App\Custcategory;
use App\CustcategoryGroup;
use Illuminate\Http\Request;

use App\Http\Requests;

class CustcategoryGroupController extends Controller
{
    // get index api
    public function getCustcategoryGroupsIndexApi()
    {
        // showing total amount init
        $total_amount = 0;
        // initiate the page num when null given
        $pageNum = request('pageNum') ? request('pageNum') : 100;

        $query = CustcategoryGroup::with('custcategories');

        if(request('name')) {
            $query = $query->where('name', 'LIKE', '%'.request('name').'%');
        }

        if(request('custcategory')) {
            $custcategories = request('custcategory');
            if (count($custcategories) == 1) {
                $custcategories = [$custcategories];
            }
            if(request('exclude_custcategory')) {
                $query = $query->whereHas('custcategories', function($query) use ($custcategories) {
                    $query->whereNotIn('id', $custcategories);
                });
            }else {
                $query = $query->whereHas('custcategories', function($query) use ($custcategories) {
                    $query->whereIn('id', $custcategories);
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
            'custcategoryGroups' => $query
        ];
    }

    // destroy single
    public function deleteCustcategoryGroupApi($id)
    {
        $model = CustcategoryGroup::findOrFail($id);

        if($model->custcategories) {
            foreach($model->custcategories as $custcategory) {
                $custcategory->custcategory_group_id = null;
                $custcategory->save();
            }
        }
        $model->delete();
    }

    // unbind single
    public function unbindCustcategoryGroupAttachment($id)
    {
        $model = Custcategory::findOrFail($id);
        $model->custcategory_group_id = null;
        $model->save();
    }

    // add new single
    public function createCustcategoryGroupApi(Request $request)
    {
        $name = $request->name;

        if($name) {
            CustcategoryGroup::create([
                'name' => $name
            ]);
        }
    }

    // bind category with group
    public function bindCustcategoryGroupAttachesApi(Request $request)
    {
        $custcategory_group_id = $request->custcategory_group_id;
        $custcategory_id = $request->custcategory_id;

        if($custcategory_group_id and $custcategory_id) {
            $model = Custcategory::findOrFail($custcategory_id);
            $model->custcategory_group_id = $custcategory_group_id;
            $model->save();
        }
    }
}
