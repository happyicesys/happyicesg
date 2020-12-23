<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\PotentialCustomer;

class PotentialCustomerController extends Controller
{
    //auth-only login can see

    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    // get index page
    public function index()
    {
        return view('potential-customer.index');
    }

    // get data api
    public function getDataApi(Request $request)
    {
        $model = PotentialCustomer::with(['accountManager', 'custcategory', 'creator', 'updater']);
                // ->leftJoin('users as account_manager', 'potential_customers.account_manager_id', '=', 'account_manager.id')
                // ->leftJoin('custcategories', 'potential_customers.custcategory_id', '=', 'custcategories.id');

        $model = $this->potentialCustomerFilter($model, $request);

        if ($request->sortName) {
            $model = $model->orderBy($request->sortName, $request->sortBy ? 'asc' : 'desc');
        }

        $pageNum = $request->pageNum ? $request->pageNum : 100;

        if ($pageNum == 'All') {
            $model = $model->orderBy('potential_customers.created_at', 'desc')->get();
        } else {
            $model = $model->orderBy('potential_customers.created_at', 'desc')->paginate($pageNum);
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

        // dd($request->all());
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

    // store attachments
    public function storeAttachments(Request $request, $id)
    {
        dd($id, $request->all());
    }

    // potentialCustomerFilter
    private function potentialCustomerFilter($query, $request)
    {
        $custcategory = $request->custcategory;
        $name = $request->name;
        $account_manager = $request->account_manager;
        $contact = $request->contact;
        $created_at = $request->created_at;
        $updated_at = $request->updated_at;

        if($custcategory) {
            if (count($custcategory) == 1) {
                $custcategory = [$custcategory];
            }
            $query = $query->whereIn('custcategory_id', $custcategory);
        }

        if($name) {
            $query = $query->where('potential_customers.name', 'LIKE', '%'.$name.'%');
        }

        if($account_manager) {
            $query = $query->where('account_manager_id', $account_manager);
        }

        if($contact) {
            $query = $query->where('contact', 'LIKE', '%'.$contact.'%');
        }

        if($created_at) {
            $query = $query->whereDate('potential_customers.created_at', '=', $created_at);
        }

        if($updated_at) {
            $query = $query->whereDate('potential_customers.updated_at', '=', $updated_at);
        }

        return $query;
    }
}
