<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Deal;
use App\DriverLocation;
use App\OutletVisit;
use App\Transaction;
use App\User;
use Carbon\Carbon;
use DB;

use App\HasMonthOptions;

class DailyreportController extends Controller
{
    use HasMonthOptions;

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
    public function indexApi(Request $request, $type = 1)
    {

        $totalRaw = "(SELECT SUM(CASE WHEN transactions.gst=1 THEN (CASE WHEN transactions.is_gst_inclusive=0 THEN deals.amount ELSE deals.amount /(100 + transactions.gst_rate) * 100 END) ELSE deals.amount END) AS total, transactions.driver, transactions.delivery_date FROM deals
        LEFT JOIN transactions ON transactions.id = deals.transaction_id
        LEFT JOIN items ON items.id = deals.item_id
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

        if($request->is_commission != '') {
            $totalRaw .= "and items.is_commission = '".$request->is_commission."'";
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
            ->rightJoin('role_user', 'role_user.user_id', '=', 'users.id')
            ->leftJoin($totalRaw, function($join) {
                $join->on('totalRaw.driver', '=', 'transactions.driver');
                $join->on('totalRaw.delivery_date', '=', 'transactions.delivery_date');
            })
            ->leftJoin('driver_locations', function($join) {
                $join->on('driver_locations.delivery_date', '=', 'transactions.delivery_date');
                $join->on('driver_locations.user_id', '=', 'users.id');
            })
            ->leftJoin('users AS updater', 'updater.id', 'LIKE', 'driver_locations.updated_by')
            ->leftJoin('users AS approver', 'approver.id', 'LIKE', 'driver_locations.approved_by')

            ->select(
                'transactions.driver', 'transactions.status',
                DB::raw('DATE(transactions.delivery_date) AS delivery_date'),
                DB::raw('DAYNAME(transactions.delivery_date) AS delivery_day'),
                'totalRaw.total', 'users.id AS user_id', 'driver_locations.location_count', 'driver_locations.online_location_count', 'driver_locations.status AS submission_status', 'driver_locations.submission_date',
                DB::raw('DAYNAME(driver_locations.submission_date) AS submission_day'),
                'updater.name AS updated_by', 'approver.name AS approved_by', 'driver_locations.approved_at', 'driver_locations.daily_limit',
                'driver_locations.remarks',
                DB::raw('(driver_locations.location_count - driver_locations.daily_limit) AS extra_location_count')
            );

        // only include drivers
        if($type == 2) {
            $deals = $deals->whereIn('role_user.role_id', [6, 16]);
        }

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
            $deals = $deals->where('people.cust_id', 'LIKE', $request->cust_id.'%');
        }
        if($request->id_prefix) {
            $deals = $deals->where('people.cust_id', 'LIKE', $request->id_prefix.'%');
        }
        if($request->is_commission != '') {
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
        $extra_location_total = 0;
        $online_location_total = 0;

        $subtotal_query = clone $alldeals;
        $subtotalArr = $subtotal_query->get();
        // dd($subtotalArr);
        foreach($subtotalArr as $dealtotal) {
            $subtotal += $dealtotal->total;
            if($dealtotal->submission_status == DriverLocation::STATUS_APPROVED) {
                if($dealtotal->extra_location_count > 0) {
                    $extra_location_total += $dealtotal->extra_location_count;
                }
                if($dealtotal->online_location_count > 0) {
                    $online_location_total += $dealtotal->online_location_count;
                }
            }
        }

        if($request->driver) {
            $user = User::where('name', $request->driver)->first();

            if($user->hasRole('driver')) {
                $commission_rate = 0.0075;
                $totalcommission = $subtotal * $commission_rate;
/*
                if($subtotal <= 40000) {
                    $commission_rate = 0.006;
                    $totalcommission = $subtotal * $commission_rate;
                }else {
                    $commission_rate = 0.01;
                    $totalcommission = (40000 * 0.006) + ($subtotal - 40000) * $commission_rate;
                } */
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
            'driver' => $driver,
            'extra_location_total' => $extra_location_total,
            'online_location_total' => $online_location_total
        ];

        return $data;
    }

    // return driver location count page
    public function driverNumberOfLocationIndex()
    {
        return view('dailyreport.driver-location-count');
    }

    // update location count api
    public function updateLocationCountApi($status)
    {
        $user_id = request('user_id');
        $delivery_date = request('delivery_date');
        $location_count = request('location_count');
        $online_location_count = request('online_location_count');
        $daily_limit = request('daily_limit');
        $status_button = $status;

        $driverlocation = DriverLocation::where('user_id', $user_id)->whereDate('delivery_date', '=', $delivery_date)->first();

        if($status == 2 or $status == 0) {
            if($driverlocation) {
                if($location_count != null or $online_location_count != null) {
                    $driverlocation->location_count = $location_count ? $location_count : 0;
                    $driverlocation->online_location_count = $online_location_count ? $online_location_count : 0;
                    $driverlocation->daily_limit = $daily_limit ? $daily_limit : 0;
                    $driverlocation->updated_by = auth()->user()->id;
                    $driverlocation->save();
                }else {
                    $driverlocation->delete();
                }
            }else {
                if($location_count != null or $daily_limit != null or $online_location_count != null) {
                    $driverlocation = DriverLocation::create([
                        'delivery_date' => $delivery_date,
                        'location_count' =>$location_count ? $location_count : 0,
                        'online_location_count' => $online_location_count ? $online_location_count : 0,
                        'daily_limit' => $daily_limit ? $daily_limit : 0,
                        'user_id' => $user_id,
                        'status' => 2,
                        'submission_date' => Carbon::now(),
                        'updated_by' => auth()->user()->id
                    ]);
                }
            }
        }else {
            $driverlocation->status = $status;
            $driverlocation->approved_at = Carbon::now();
            $driverlocation->approved_by = auth()->user()->id;
            $driverlocation->save();
        }

        return [
            'driver' => $driverlocation->driver->name,
            'delivery_date' => $driverlocation->delivery_date,
            'delivery_day' => Carbon::parse($driverlocation->delivery_date)->format('l'),
            'user_id' => $driverlocation->driver->id,
            'location_count' => $driverlocation->location_count,
            'online_location_count' => $driverlocation->online_location_count,
            'submission_status' => $driverlocation->status,
            'submission_date' => $driverlocation->submission_date,
            'submission_day' => Carbon::parse($driverlocation->submission_date)->format('l'),
            'delivery_date' => $driverlocation->delivery_date,
            'updated_by' => $driverlocation->updater ? $driverlocation->updater->name : null,
            'approved_by' =>  $driverlocation->approver ? $driverlocation->approver->name : null,
            'approved_at' => $driverlocation->approved_at,
            'daily_limit' => $driverlocation->daily_limit,
            'extra_location_count' => $driverlocation->location_count - $driverlocation->daily_limit
        ];
    }

    // return account manager performance page
    public function getAccountManagerPerformanceIndex()
    {
        $monthOptions = $this->getMonthOptions();

        return view('dailyreport.account-manager-performance', compact('monthOptions'));
    }

    // return account manager performance api
    public function getAccountManagerPerformanceApi()
    {
        $transactions = Transaction::leftJoin('people', 'people.id', '=', 'transactions.person_id')
                                ->leftJoin('profiles', 'profiles.id', '=', 'people.profile_id')
                                ->leftJoin('users AS account_manager', 'account_manager.id', '=', 'people.account_manager');

        $outletVisits = OutletVisit::leftJoin('people', 'people.id', '=', 'outlet_visits.person_id')
                                ->leftJoin('profiles', 'profiles.id', '=', 'people.profile_id')
                                ->leftJoin('users AS account_manager', 'account_manager.id', '=', 'people.account_manager');

        if($profileId = request('profile_id')) {
            $transactions = $transactions->where('profiles.id', $profileId);
            $outletVisits = $outletVisits->where('profiles.id', $profileId);
        }

        if($currentMonth = request('current_month')) {
            $thisMonth = Carbon::createFromFormat('d-m-Y', '01-'.$currentMonth);
            $lastMonth = Carbon::createFromFormat('d-m-Y', '01-'.$currentMonth)->subMonth();
            $lastTwoMonth = Carbon::createFromFormat('d-m-Y', '01-'.$currentMonth)->subMonths(2);
            $transactions = $transactions->whereDate('transactions.delivery_date', '>=', $lastTwoMonth->copy()->startOfMonth()->toDateString());
            $transactions = $transactions->whereDate('transactions.delivery_date', '<=', $thisMonth->copy()->endOfMonth()->toDateString());
            $outletVisits = $outletVisits->whereDate('outlet_visits.date', '>=', $lastTwoMonth->copy()->startOfMonth()->toDateString());
            $outletVisits = $outletVisits->whereDate('outlet_visits.date', '<=', $thisMonth->copy()->endOfMonth()->toDateString());
        }

        if($status = request('status')) {
            if($status == 'Delivered') {
                $transactions = $transactions->where(function($query) {
                    $query->where('transactions.status', 'Delivered')->orWhere('transactions.status', 'Verified Owe')->orWhere('transactions.status', 'Verified Paid');
                });
            }else {
                $transactions = $transactions->where('transactions.status', $request->status);
            }
        }

        if($custId = request('cust_id')) {
            $transactions = $transactions->where('people.id', 'LIKE', '%'.$custId.'%');
            $outletVisits = $outletVisits->where('people.id', 'LIKE', '%'.$custId.'%');
        }

        if($company = request('company')) {
            $transactions = $transactions->where('people.id', 'LIKE', '%'.$company.'%');
            $outletVisits = $outletVisits->where('people.id', 'LIKE', '%'.$company.'%');
        }

        if($custCategory = request('custcategory')) {
            if (count($custCategory) == 1) {
                $custCategory = [$custCategory];
            }
            if($exclude_custcategory) {
                $transactions = $transactions->whereNotIn('people.custcategory_id', $custCategory);
                $outletVisits = $outletVisits->whereNotIn('people.custcategory_id', $custCategory);
            }else {
                $transactions = $transactions->whereIn('people.custcategory_id', $custCategory);
                $outletVisits = $outletVisits->whereIn('people.custcategory_id', $custCategory);
            }
        }

        if($acccountManager = request('account_manager')) {
            $transactions = $transactions->where('people.account_manager', $acccountManager);
            $outletVisits = $outletVisits->where('people.account_manager', $acccountManager);
        }else {
            $transactions = $transactions->whereNotNull('people.account_manager')->where('people.account_manager', '<>', '');
            $outletVisits = $outletVisits->whereNotNull('people.account_manager')->where('people.account_manager', '<>', '');
        }

        if($zones = request('zones')) {
            if(count($zones) == 1) {
                $zones = [$zones];
            }
            $transactions = $transactions->whereIn('people.zone_id', $zones);
            $outletVisits = $outletVisits->whereIn('people.zone_id', $zones);
        }

        $transactions = $transactions->select(
            'account_manager.id AS account_manager_id', 'account_manager.name AS account_manager_name',
            DB::raw('ROUND(SUM(CASE WHEN transactions.gst=1 THEN(CASE WHEN transactions.is_gst_inclusive=0 THEN transactions.total ELSE transactions.total * 100/ (100 + transactions.gst_rate) END) ELSE transactions.total END), 2) AS sales_total'),
            DB::raw('MONTH(delivery_date) AS month'),
            DB::raw('DATE(delivery_date) AS date')
        );
        $outletVisits = $outletVisits->select(
            'account_manager.id AS account_manager_id', 'account_manager.name AS account_manager_name',
            DB::raw('COUNT(outlet_visits.id) AS visited_total'),
            DB::raw('MONTH(date) AS month'),
            DB::raw('DATE(date) AS date')
        );

        $transactions = $transactions->groupBy('date')->groupBy('account_manager.id');
        $outletVisits = $outletVisits->groupBy('date')->groupBy('account_manager.id');


        if($sortName = request('sortName')){
            $transactions = $transactions->orderBy($sortName, request('sortBy') ? 'asc' : 'desc');
            $outletVisits = $outletVisits->orderBy($sortName, request('sortBy') ? 'asc' : 'desc');
        }else {
            $transactions = $transactions->orderBy('date', 'asc')->orderBy('account_manager.name', 'asc');
            $outletVisits = $outletVisits->orderBy('date', 'asc')->orderBy('account_manager.name', 'asc');
        }

        $transactions = $transactions->get();
        $outletvisits = $outletVisits->get();

        $dataArr = [
            [
                'title' => 'Current Month',
                'month' => $thisMonth->copy()->month,
                'dates' => []
            ],
            [
                'title' => 'Last Month',
                'month' => $lastMonth->copy()->month,
                'dates' => []
            ],
            [
                'title' => 'Last Two Month',
                'month' => $lastTwoMonth->copy()->month,
                'dates' => []
            ]
        ];

        foreach($dataArr as $monthIndex => $months) {
            $salesTotal = 0;
            $visitTotal = 0;
            if($transactions) {
                foreach($transactions as $transaction) {
                    $createNewTransaction = true;
                    if($transaction->month == $months['month']){
                        if($months['dates']){
                            foreach($months['dates'] as $dateIndex => $date) {
                                if($dateIndex == $transaction->date) {
                                    foreach($date as $managerIndex => $manager) {
                                        if($managerIndex == $transaction->account_manager_id) {
                                            $dataArr[$monthIndex]['dates'][$dateIndex][$transaction->date][$transaction->account_manager_id]['sales'] = $transaction->sales_total;
                                            $salesTotal += round($transaction->sales_total, 2);
                                            $createNewTransaction = false;
                                            unset($transaction);
                                        }
                                    }
                                }
                            }
                        }

                        if($createNewTransaction) {
                            $dataArr[$monthIndex]['dates'][$transaction->date][$transaction->account_manager_id]['account_manager_name'] = $transaction->account_manager_name;
                            $dataArr[$monthIndex]['dates'][$transaction->date][$transaction->account_manager_id]['sales'] = $transaction->sales_total;
                            $dataArr[$monthIndex]['dates'][$transaction->date][$transaction->account_manager_id]['date'] = $transaction->date;
                            $salesTotal += round($transaction->sales_total, 2);
                            // unset($transaction);
                        }

                    }
                }
            }
            if($outletvisits) {
                foreach($outletvisits as $outletvisit) {
                    $createNewVisit = true;
                    if($outletvisit->month == $months['month']){
                        // dd($outletVisit->toArray(), $months, $dataArr[$monthIndex]['dates']);
                        if($dataArr[$monthIndex]['dates']){
                            foreach($dataArr[$monthIndex]['dates'] as $dateIndex => $date) {
                                if($dateIndex == $outletvisit->date) {
                                    foreach($date as $managerIndex => $manager) {
                                        dd($outletvisit->toArray());
                                        if($managerIndex == $outletvisit->account_manager_id) {
                                            $dataArr[$monthIndex]['dates'][$dateIndex][$outletvisit->account_manager_id]['visits'] = $outletvisit->visited_total;
                                            $visitTotal += $outletvisit->visited_total;
                                            $createNewVisit = false;
                                            unset($outletvisit);
                                        }
                                    }
                                }
                            }
                        }

                        if($createNewVisit) {
                            $dataArr[$monthIndex]['dates'][$outletvisit->delivery_date][$outletvisit->account_manager_id]['account_manager_name'] = $outletvisit->account_manager_name;
                            $dataArr[$monthIndex]['dates'][$outletvisit->delivery_date][$outletvisit->account_manager_id]['visits'] = $outletvisit->visited_total;
                            $dataArr[$monthIndex]['dates'][$outletvisit->delivery_date][$outletvisit->account_manager_id]['date'] = $outletvisit->date;
                            $visitTotal += $outletvisit->visited_total;
                            // unset($outletvisit);
                        }

                    }
                }
            }
            $dataArr[$monthIndex]['salesTotal'] = round($salesTotal, 2);
            $dataArr[$monthIndex]['visitTotal'] = $visitTotal;
        }
        return $dataArr;
    }
}
