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

        $totalRaw = DB::raw("(SELECT SUM(transactions.total) AS total, transactions.driver, transactions.delivery_date FROM transactions
                    GROUP BY transactions.delivery_date, transactions.driver) totalRaw");

        $deals = DB::table('deals')
            ->leftJoin('items', 'items.id', '=', 'deals.item_id')
            ->leftJoin('transactions', 'transactions.id', '=', 'deals.transaction_id')
            ->leftJoin('people', 'transactions.person_id', '=', 'people.id')
            ->leftJoin('profiles', 'people.profile_id', '=', 'profiles.id')
            ->leftJoin('custcategories', 'custcategories.id', '=', 'people.custcategory_id')
            ->leftJoin($totalRaw, function($join) {
                $join->on('totalRaw.driver', '=', 'transactions.driver');
                $join->on('totalRaw.delivery_date', '=', 'transactions.delivery_date');
            })
            ->select(
                'transactions.driver', 'transactions.status',
                DB::raw('DATE(transactions.delivery_date) AS delivery_date'),
                'totalRaw.total'
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
        // dd($deals->get());

        $commission051_query = clone $deals;
        $commission051 = 0;
        $commission051 = $commission051_query->where('items.product_id', '051')->sum('amount');

        if($request->driver) {
            if(auth()->user()->hasRole('driver') or auth()->user()->hasRole('technician')) {
                $deals = $deals->where('transactions.driver', auth()->user()->name);
            }else {
                $deals = $deals->where('transactions.driver', $request->driver);
            }
        }

        $alldeals = clone $deals;
        $subtotal_query = clone $deals;

        $commission_rate = 0;
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
            $subtotal += $dealtotal->total;

        }

        if($request->driver) {
            $user = User::where('name', $request->driver)->first();

            if($user->hasRole('driver')) {
                // dd('here1');
                if($subtotal <= 40000) {
                    $commission_rate = 0.006;
                    $totalcommission = $subtotal * $commission_rate;
                }else {
                    $commission_rate = 0.01;
                    $totalcommission = (40000 * 0.006) + ($subtotal - 40000) * $commission_rate;
                }
            }

            if($user->hasRole('technician')) {
                // dd('here2');
                $commission_rate = 0.004;
                $totalcommission = $commission051 * $commission_rate;
            }
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
