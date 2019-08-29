<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Deal;
use App\User;
use Carbon\Carbon;
use DB;

class DailyreportController extends Controller
{
    // detect authed
    public function __construct()
    {
        $this->middleware('auth');
    }

    // return daily report index page
    public function index()
    {
        return view('dailyreport.index');
    }

    // return daily report index api
    public function indexApi(Request $request)
    {
        $deals = DB::table('deals')
            ->leftJoin('items', 'items.id', '=', 'deals.item_id')
            ->leftJoin('transactions', 'transactions.id', '=', 'deals.transaction_id')
            ->leftJoin('people', 'transactions.person_id', '=', 'people.id')
            ->leftJoin('profiles', 'people.profile_id', '=', 'profiles.id')
            ->leftJoin('custcategories', 'custcategories.id', '=', 'people.custcategory_id')
            ->select(
                'transactions.total', 'transactions.driver', 'transactions.status',
                DB::raw('DATE(transactions.delivery_date) AS delivery_date')
            );

        if($request->profile_id) {
            $deals = $deals->where('profiles.id', $request->profile_id);
        }
        if($request->date_from) {
            $deals = $deals->whereDate('transactions.delivery_date', '>=', $request->date_from);
        }
        if($request->date_to) {
            $deals = $deals->whereDate('transactions.delivery_date', '<=', $request->date_to);
        }
        if($request->cust_id) {
            $deals = $deals->where('people.cust_id', 'LIKE', '%'.$request->cust_id.'%');
        }
        if($request->id_prefix) {
            $deals = $deals->where('people.cust_id', 'LIKE', $request->id_prefix.'%');
        }
        if($request->driver) {
            $deals = $deals->where('transactions.driver', $request->driver);
        }

        if ($request->custcategory) {
            $custcategory = $request->custcategory;
            if (count($custcategory) == 1) {
                $custcategory = [$custcategory];
            }
            $deals = $deals->whereIn('custcategories.id', $request->custcategory);
        }

        if($request->status) {
            if($request->status == 'Delivered') {
                $deals = $deals->where(function($query) {
                    $query->where('transactions.status', 'Delivered')->orWhere('transactions.status', 'Verified Owe')->orWhere('transactions.status', 'Verified Paid');
                });
            }else {
                $deals = $deals->where('transactions.status', $request->status);
            }
        }
/*
        if($request->tag) {
            if($request->tag == 'technician') {

            }
        } */

        if($request->driver) {
            if(auth()->user()->hasRole('driver')) {
                $deals = $deals->where('transactions.driver', auth()->user()->name);
            }else {
                $deals = $deals->where('transactions.driver', $request->driver);
            }
        }

        $alldeals = clone $deals;
        $subtotal_query = clone $deals;
        $commission_query = clone $deals;
        $commission_rate = 0;
        $commission = 0;
        $totalcommission = 0;
        $subtotal = 0;

        $alldeals = $alldeals
            ->groupBy('transactions.delivery_date')
            ->groupBy('transactions.driver')
            ->orderBy('transactions.delivery_date', 'desc')
            ->orderBy('transactions.driver');

        $subtotal_query = clone $alldeals;
        $subtotalArr = $subtotal_query->get();

        foreach($subtotalArr as $dealtotal) {

            $user = User::where('name', $dealtotal->driver)->first();

            if($user) {
                if($user->hasRole('driver')) {
                    if($subtotal <= 40000) {
                        $commission_rate = 0.006;
                    }else {
                        $commission_rate = 0.01;
                    }
                }

                if($user->hasRole('technician')) {
                    $commission_rate = 0.004;
                }

                $commission = $dealtotal->total * $commission_rate;
            }
            $totalcommission += $commission;
            $subtotal += $dealtotal->total;
        }

        if($request->sortName){
            $alldeals = $alldeals->orderBy($request->sortName, $request->sortBy ? 'asc' : 'desc');
        }

        $pageNum = $request->pageNum ? $request->pageNum : 100;
        if($pageNum == 'All'){
            $alldeals = $alldeals->get();
        }else{
            $alldeals = $alldeals->paginate($pageNum);
        }

        $data = [
            'alldeals' => $alldeals,
            'subtotal' => $subtotal,
            'totalcommission' => $totalcommission
        ];

        return $data;
    }
}
