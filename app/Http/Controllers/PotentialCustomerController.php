<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\PotentialCustomer;

class PotentialCustomerController extends Controller
{
    //auth-only login can see
    public function __construct()
    {
        $this->middleware('auth');
    }

    // get index page
    public function index()
    {
        return view('potential-customer.index');
    }

    // get data api
    public function getDataApi(Request $request)
    {
        $model = PotentialCustomer::with(['accountManager', 'custcategory']);

        $model = $this->potentialCustomerFilter($model, $request);

        if ($request->sortName) {
            $model = $model->orderBy($request->sortName, $request->sortBy ? 'asc' : 'desc');
        }    
        
        $pageNum = $request->pageNum ? $request->pageNum : 100;        

        if ($pageNum == 'All') {
            $model = $model->orderBy('created_at', 'desc')->get();
        } else {
            $model = $model->orderBy('created_at', 'desc')->paginate($pageNum);
        }

        return [
            'data' => $model
        ];       
    }

    // store new potential customer(Request $request)
    public function storeUpdateApi(Request $request)
    {
        $id = $request->id;
        $currentUserId = auth()->user()->id;

        if($id) {
            $model = PotentialCustomer::findOrFail($id);
            $model->update($request->all());
            $model->updated_by = $currentUserId;
            $model->save();
        }else {
            $model = PotentialCustomer::create($request->all());
            $model->created_by = $currentUserId;
            $model->save();
        }
    }    

    // potentialCustomerFilter
    private function potentialCustomerFilter($query, $request)
    {
        $custcategory = $request->custcategory;
        $name = $request->name;
        $account_manager = $request->account_manager;
        $contact = $request->contact;

        if($custcategory) {
            if (count($custcategory) == 1) {
                $custcategory = [$custcategory];
            }
            $query = $query->whereIn('custcategory_id', $custcategory);
        }

        if($name) {
            $query = $query->where('name', 'LIKE', '%'.$name.'%');
        }

        if($account_manager) {
            $query = $query->where('account_manager_id', $account_manager);
        } 
        
        if($contact) {
            $query = $query->where('contact', 'LIKE', '%'.$contact.'%');
        }        

        return $query;
    }
}
