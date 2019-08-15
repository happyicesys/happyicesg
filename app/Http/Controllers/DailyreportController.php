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
                'deals.amount'
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

        $subtotal_query = clone $deals;
        $today_query = clone $deals;
        $yesterday_query = clone $deals;
        $last_two_day_query = clone $deals;
        $commission_query = clone $deals;
        $commission_rate = 0;
        $commission = 0;

        $subtotal = $subtotal_query->sum('amount');
        $today_total = $today_query->where('transactions.delivery_date', Carbon::today()->toDateString())->sum('amount');
        $yesterday_total = $yesterday_query->where('transactions.delivery_date', Carbon::today()->subDay()->toDateString())->sum('amount');
        $last_two_day_total = $last_two_day_query->where('transactions.delivery_date', Carbon::today()->subDays(2)->toDateString())->sum('amount');

        if($request->driver) {
            $user = User::where('name', $request->driver)->first();

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

                $commission = $subtotal * $commission_rate;
            }
        }

        $data = [
            'subtotal' => $subtotal,
            'today_total' => $today_total,
            'yesterday_total' => $yesterday_total,
            'last_two_day_total' => $last_two_day_total,
            'yesterday_total' => $yesterday_total,
            'commission' => $commission
        ];

        return $data;
    }
}
