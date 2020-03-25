<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Deal;
use App\DriverLocation;
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
    public function commissionIndex()
    {
        return view('dailyreport.commission');
    }

    // return daily report index api
    public function indexApi(Request $request)
    {

        $totalRaw = "(SELECT SUM(CASE WHEN transactions.gst=1 THEN (CASE WHEN transactions.is_gst_inclusive=0 THEN transactions.total ELSE transactions.total /(100 + transactions.gst_rate) * 100 END) ELSE transactions.total END) AS total, transactions.driver, transactions.delivery_date FROM transactions
        LEFT JOIN people ON people.id = transactions.person_id
        LEFT JOIN profiles ON profiles.id = people.profile_id
        LEFT JOIN custcategories ON custcategories.id = people.custcategory_id
        where 1=1";

        if($request->profile_id) {
            $totalRaw .= " and profiles.id =".$request->profile_id." ";
        }

        if ($request->person_active) {
            $personstatus = $request->person_active;

            $personstatus = implode("','",$personstatus);
            $totalRaw .= " and people.active IN ('".$personstatus."')";
        }

        if($request->status) {
            if($request->status == 'Delivered') {
                $totalRaw .= " and (transactions.status = 'Delivered' or transactions.status = 'Verified Owe' or transactions.status = 'Verified Paid') ";
            }else {
                $totalRaw .= " and (transactions.status = '".$request->status."') ";
            }
        }

        if($request->custcategory) {
            $custcategories = $request->custcategory;
            $custcategories = implode("','",$custcategories);

            if($request->exclude_custcategory) {
                $totalRaw .= " and custcategories.id NOT IN ('".$custcategories."')";
            }else {
                $totalRaw .= " and custcategories.id IN ('".$custcategories."')";
            }
        }


        $totalRaw .= " GROUP BY transactions.delivery_date, transactions.driver) totalRaw";

        $totalRaw = DB::raw($totalRaw);

        $deals = DB::table('deals')
            ->leftJoin('items', 'items.id', '=', 'deals.item_id')
            ->leftJoin('transactions', 'transactions.id', '=', 'deals.transaction_id')
            ->leftJoin('people', 'transactions.person_id', '=', 'people.id')
            ->leftJoin('profiles', 'people.profile_id', '=', 'profiles.id')
            ->leftJoin('custcategories', 'custcategories.id', '=', 'people.custcategory_id')
            ->leftJoin('users', 'users.name', 'LIKE', DB::raw( "CONCAT('%', transactions.driver, '%')"))
            ->leftJoin($totalRaw, function($join) {
                $join->on('totalRaw.driver', '=', 'transactions.driver');
                $join->on('totalRaw.delivery_date', '=', 'transactions.delivery_date');
            })
            ->leftJoin('driver_locations', function($join) {
                $join->on('driver_locations.delivery_date', '=', 'transactions.delivery_date');
                $join->on('driver_locations.user_id', '=', 'users.id');
            })
            ->select(
                'transactions.driver', 'transactions.status',
                DB::raw('DATE(transactions.delivery_date) AS delivery_date'),
                'totalRaw.total', 'users.id AS user_id', 'driver_locations.location_count'
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
        if($request->is_commission != '') {
            // dd('hrere');
            $deals = $deals->where('items.is_commission', $request->is_commission);
        }
        if ($request->person_active) {
            // dd($request->person_active);
            $personstatus = $request->person_active;
            if (count($personstatus) == 1) {
                $personstatus = [$personstatus];
            }
            $deals = $deals->whereIn('people.active', $personstatus);
        }

        // set logic to distinguish driver or technician role
        $driver = '';

        if($request->driver) {
            $driver_role = User::where('name', $request->driver)->first();
            if($driver_role->hasRole('driver')) {
                $driver = 'driver';
                $deals = $deals->where('transactions.driver', $request->driver);
            }else if($driver_role->hasRole('technician')) {
                $driver = 'technician';
                $deals = $deals->where('items.product_id', '051');
            }
        }

        if($request->custcategory) {
            $custcategories = $request->custcategory;
            if (count($custcategories) == 1) {
                $custcategories = [$custcategories];
            }
            if($request->exclude_custcategory) {
                $deals = $deals->whereNotIn('custcategories.id', $custcategories);
            }else {
                $deals = $deals->whereIn('custcategories.id', $custcategories);
            }
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
        // dd($deals->where('transactions.driver', 'LIKE', '%'.'iris'.'%')->get());
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
            if(auth()->user()->hasRole('driver')) {
                $deals = $deals->where('transactions.driver', auth()->user()->name);
            }
        }

        $deals = $deals->groupBy('transactions.delivery_date')->groupBy('transactions.driver');


        $alldeals = clone $deals;
        $subtotal_query = clone $deals;

        $commission_rate = 0;
        $totalcommission = 0;
        $subtotal = 0;
/*
        $alldeals = $alldeals
            ->groupBy('transactions.delivery_date')
            ->groupBy('transactions.driver')
            ->orderBy('transactions.delivery_date', 'desc')
            ->orderBy('transactions.driver'); */

        $subtotal_query = clone $alldeals;
        $subtotalArr = $subtotal_query->get();

        // dd($subtotalArr);

        // dd($subtotalArr);
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
        // $alldeals = $alldeals->orderBy('transactions.delivery_date', 'desc');

        if($request->sortName){
            $alldeals = $alldeals
            ->orderBy($request->sortName, $request->sortBy ? 'asc' : 'desc');
        }else {
            $alldeals = $alldeals
            ->orderBy('transactions.delivery_date', 'desc')
            ->orderBy('transactions.driver');
        }

        // dd($alldeals->toSql());
        $pageNum = $request->pageNum ? $request->pageNum : 100;
        if($pageNum == 'All'){
            $alldeals = $alldeals->get();
        }else{
            $alldeals = $alldeals->paginate($pageNum);
        }

        $data = [
            'alldeals' => $alldeals,
            'subtotal' => $subtotal,
            'totalcommission' => $totalcommission,
            'driver' => $driver
        ];

        return $data;
    }

    // return driver location count page
    public function driverNumberOfLocationIndex()
    {
        return view('dailyreport.driver-location-count');
    }

    // update location count api
    public function updateLocationCountApi()
    {
        $user_id = request('user_id');
        $delivery_date = request('delivery_date');
        $location_count = request('location_count');

        $driverlocation = DriverLocation::where('user_id', $user_id)->whereDate('delivery_date', '=', $delivery_date)->first();

        if($driverlocation) {
            if($location_count) {
                $driverlocation->location_count = $location_count;
                $driverlocation->save();
            }else {
                $driverlocation->delete();
            }
        }else {
            if($location_count) {
                DriverLocation::create([
                    'delivery_date' => $delivery_date,
                    'location_count' =>$location_count,
                    'user_id' => $user_id
                ]);
            }
        }
    }
}
