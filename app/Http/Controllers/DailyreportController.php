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
    public function indexApi(Request $request)
    {

        $deals = Deal::with([
            'item',
            'transaction.person.profile',
            'transaction.person.custcategory.custcategoryGroup',
            'transaction.person.custPrefix',
        ])
        ->leftJoin('transactions', 'transactions.id', '=', 'deals.transaction_id')
        ->leftJoin('users', 'users.name', '=', 'transactions.driver')
        ->leftJoin('driver_locations', function($join) {
            $join->on(DB::raw('DATE(driver_locations.delivery_date)'), '=', DB::raw('DATE(transactions.delivery_date)'));
            $join->on('driver_locations.user_id', '=', 'users.id');
        })
        ->select(
            'driver',
            DB::raw('DATE(transactions.delivery_date) AS delivery_date'),
            DB::raw('DAYNAME(transactions.delivery_date) AS delivery_day'),
            DB::raw('ROUND(SUM(CASE WHEN transactions.gst=1 THEN (CASE WHEN transactions.is_gst_inclusive=0 THEN deals.amount ELSE deals.amount /(100 + transactions.gst_rate) * 100 END) ELSE deals.amount END), 2) AS total'),
            'driver_locations.location_count',
            'driver_locations.daily_limit',
            'driver_locations.online_location_count',
            DB::raw('(driver_locations.location_count - driver_locations.daily_limit) AS extra_location_count'),
            'driver_locations.status AS submission_status'
            );

        $deals = $this->filterCommission($deals, $request);

        $driverTechnicianName = User::whereHas('roles', function($query) {
            $query->whereIn('name', ['driver', 'technician', 'driver-supervisor']);
        })->pluck('name');
        $deals = $deals->whereIn('transactions.driver', $driverTechnicianName);

        // $deals = $deals->groupBy(DB::raw('DATE(transactions.delivery_date)'))
        //                 ->groupBy('transactions.driver')
        //                 ->orderBy('transactions.delivery_date', 'DESC')
        //                 ->orderBy('transactions.driver', 'ASC')
        //                 ->get();

        // dd($deals->toArray());


        $commission051_query = clone $deals;
        $commission051 = 0;
        // $commission051 = $commission051_query->where('items.product_id', '051')->sum('amount');
        $commission051 = $commission051_query->whereHas('item', function($query) {
            $query->where('product_id', '051');
        })->sum('amount');

        if($request->driver) {
            if(auth()->user()->hasRole('driver')) {
                $deals = $deals->where('transactions.driver', auth()->user()->name);
            }
        }

        $deals = $deals->groupBy(DB::raw('DATE(transactions.delivery_date)'))->groupBy('users.id');

        $alldeals = clone $deals;
        $subtotal_query = clone $deals;

        $commission_rate = 0;
        $totalcommission = 0;
        $subtotal = 0;
        $extra_location_total = 0;
        $online_location_total = 0;

        $subtotal_query = clone $alldeals;
        $allSubtotalArr = $subtotal_query->get();
        $subtotalArr = $allSubtotalArr->where('submission_status', DriverLocation::STATUS_APPROVED);

        $extra_location_total = 0;
        $location_total = $allSubtotalArr->sum('location_count');
        $daily_limit_total = $allSubtotalArr->sum('daily_limit');
        $extra_location_total = $location_total - $daily_limit_total;
        $online_location_total = $allSubtotalArr->sum('online_location_count');
        $subtotal = $allSubtotalArr->sum('total');

        if($request->driver) {
            $user = User::where('name', $request->driver)->first();

            if($user->hasRole('driver')) {
                $commission_rate = 0.008;
                $totalcommission = $subtotal * $commission_rate;
            }

            if($user->hasRole('technician')) {
                $commission_rate = 0.004;
                $totalcommission = $commission051 * $commission_rate;
            }
        }

        if($request->sortName){
            $alldeals = $alldeals
            ->orderBy($request->sortName, $request->sortBy ? 'ASC' : 'DESC');
        }else {
            $alldeals = $alldeals
            ->orderBy('transactions.delivery_date', 'DESC')
            ->orderBy('transactions.driver', 'ASC');
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
            'totalcommission' => $totalcommission,
            'driver' => $driver,
            'extra_location_total' => $extra_location_total,
            'online_location_total' => $online_location_total
        ];

        return $data;
    }

        // return daily report index api
    public function getLocationCountApi(Request $request)
    {

        $deals = DB::table('deals')
            ->leftJoin('items', 'items.id', '=', 'deals.item_id')
            ->leftJoin('transactions', 'transactions.id', '=', 'deals.transaction_id')
            ->leftJoin('users', 'users.name', '=', 'transactions.driver')
            ->rightJoin('role_user', 'role_user.user_id', '=', 'users.id')
            ->leftJoin('driver_locations', function($join) {
                $join->on(DB::raw('DATE(driver_locations.delivery_date)'), '=', DB::raw('DATE(transactions.delivery_date)'));
                $join->on('driver_locations.user_id', '=', 'users.id');
            })
            ->leftJoin('users AS updater', 'updater.id', 'LIKE', 'driver_locations.updated_by')
            ->leftJoin('users AS approver', 'approver.id', 'LIKE', 'driver_locations.approved_by')

            ->select(
                'transactions.driver', 'transactions.status',
                DB::raw('DATE(transactions.delivery_date) AS delivery_date'),
                DB::raw('DAYNAME(transactions.delivery_date) AS delivery_day'),
                'users.id AS user_id', 'driver_locations.location_count', 'driver_locations.online_location_count', 'driver_locations.status AS submission_status', 'driver_locations.submission_date',
                DB::raw('DAYNAME(driver_locations.submission_date) AS submission_day'),
                'updater.name AS updated_by', 'approver.name AS approved_by', 'driver_locations.approved_at', 'driver_locations.daily_limit',
                'driver_locations.remarks',
                DB::raw('(driver_locations.location_count - driver_locations.daily_limit) AS extra_location_count')
            );

        // only include drivers

        $deals = $deals->whereIn('role_user.role_id', [6, 16]);

        if($request->date_from) {
            $deals = $deals->whereDate('transactions.delivery_date', '>=', $request->date_from);
        }
        if($request->date_to) {
            $deals = $deals->whereDate('transactions.delivery_date', '<=', $request->date_to);
        }

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

        $deals = $deals->groupBy(DB::raw('DATE(transactions.delivery_date)'))->groupBy('users.id');

        $alldeals = clone $deals;

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
                                ->leftJoin('cust_prefixes', 'cust_prefixes.id', '=', 'people.cust_prefix_id')
                                ->leftJoin('custcategories', 'custcategories.id', '=', 'people.custcategory_id')
                                ->leftJoin('custcategory_groups', 'custcategory_groups.id', '=', 'custcategories.custcategory_group_id')
                                ->leftJoin('profiles', 'profiles.id', '=', 'people.profile_id')
                                ->leftJoin('users AS account_manager', 'account_manager.id', '=', 'people.account_manager');
        $deals = Deal::leftJoin('transactions', 'transactions.id', '=', 'people.transaction_id')
                                ->leftJoin('people', 'people.id', '=', 'transactions.person_id')
                                ->leftJoin('cust_prefixes', 'cust_prefixes.id', '=', 'people.cust_prefix_id')
                                ->leftJoin('custcategories', 'custcategories.id', '=', 'people.custcategory_id')
                                ->leftJoin('custcategory_groups', 'custcategory_groups.id', '=', 'custcategories.custcategory_group_id')
                                ->leftJoin('profiles', 'profiles.id', '=', 'people.profile_id')
                                ->leftJoin('items', 'items.id', '=', 'deals.item_id')
                                ->leftJoin('users AS account_manager', 'account_manager.id', '=', 'people.account_manager');

        $outletVisits = OutletVisit::leftJoin('people', 'people.id', '=', 'outlet_visits.person_id')
                                ->leftJoin('cust_prefixes', 'cust_prefixes.id', '=', 'people.cust_prefix_id')
                                ->leftJoin('custcategories', 'custcategories.id', '=', 'people.custcategory_id')
                                ->leftJoin('custcategory_groups', 'custcategory_groups.id', '=', 'custcategories.custcategory_group_id')
                                ->leftJoin('profiles', 'profiles.id', '=', 'people.profile_id')
                                ->leftJoin('users AS account_manager', 'account_manager.id', '=', 'people.account_manager');

        if($profileId = request('profile_id')) {
            $transactions = $transactions->where('profiles.id', $profileId);
            $deals = $deals->where('profiles.id', $profileId);
            $outletVisits = $outletVisits->where('profiles.id', $profileId);
        }

        if($currentMonth = request('current_month')) {
            $thisMonth = Carbon::createFromFormat('d-m-Y', '01-'.$currentMonth);
            $lastMonth = Carbon::createFromFormat('d-m-Y', '01-'.$currentMonth)->subMonth();
            $lastTwoMonth = Carbon::createFromFormat('d-m-Y', '01-'.$currentMonth)->subMonths(2);
            $transactions = $transactions->whereDate('transactions.delivery_date', '>=', $lastTwoMonth->copy()->startOfMonth()->toDateString());
            $transactions = $transactions->whereDate('transactions.delivery_date', '<=', $thisMonth->copy()->endOfMonth()->toDateString());
            $deals = $deals->whereDate('transactions.delivery_date', '>=', $lastTwoMonth->copy()->startOfMonth()->toDateString());
            $deals = $deals->whereDate('transactions.delivery_date', '<=', $thisMonth->copy()->endOfMonth()->toDateString());
            $outletVisits = $outletVisits->whereDate('outlet_visits.date', '>=', $lastTwoMonth->copy()->startOfMonth()->toDateString());
            $outletVisits = $outletVisits->whereDate('outlet_visits.date', '<=', $thisMonth->copy()->endOfMonth()->toDateString());
        }

        if($status = request('status')) {
            if($status == 'Delivered') {
                $transactions = $transactions->where(function($query) {
                    $query->where('transactions.status', 'Delivered')->orWhere('transactions.status', 'Verified Owe')->orWhere('transactions.status', 'Verified Paid');
                });
                $deals = $deals->where(function($query) {
                    $query->where('transactions.status', 'Delivered')->orWhere('transactions.status', 'Verified Owe')->orWhere('transactions.status', 'Verified Paid');
                });
            }else {
                $transactions = $transactions->where('transactions.status', $request->status);
                $deals = $deals->where('transactions.status', $request->status);
            }
        }

        if($custId = request('cust_id')) {
            $transactions = $transactions->where('people.id', 'LIKE', '%'.$custId.'%');
            $deals = $deals->where('people.id', 'LIKE', '%'.$custId.'%');
            $outletVisits = $outletVisits->where('people.id', 'LIKE', '%'.$custId.'%');
        }

        if($prefixCode = request('prefix_code')) {
            $transactions = $transactions->where(function($query) use ($prefixCode) {
                $lettersOnly = preg_replace("/[^a-zA-Z]/", "", $prefixCode);
                $numbersOnly = preg_replace("/[^0-9]/", "", $prefixCode);
                if($lettersOnly && !$numbersOnly) {
                    $query->where('cust_prefixes.code', 'LIKE', '%' . $lettersOnly . '%');
                }
                if($numbersOnly && !$lettersOnly) {
                    $query->where('people.code', 'LIKE', '%' . $numbersOnly . '%');
                }
                if($lettersOnly && $numbersOnly) {
                    $query->where('cust_prefixes.code', 'LIKE', '%' . $lettersOnly . '%')->where('people.code', 'LIKE', '%' . $numbersOnly . '%');
                }
            });
            $deals = $deals->where(function($query) use ($prefixCode) {
                $lettersOnly = preg_replace("/[^a-zA-Z]/", "", $prefixCode);
                $numbersOnly = preg_replace("/[^0-9]/", "", $prefixCode);
                if($lettersOnly && !$numbersOnly) {
                    $query->where('cust_prefixes.code', 'LIKE', '%' . $lettersOnly . '%');
                }
                if($numbersOnly && !$lettersOnly) {
                    $query->where('people.code', 'LIKE', '%' . $numbersOnly . '%');
                }
                if($lettersOnly && $numbersOnly) {
                    $query->where('cust_prefixes.code', 'LIKE', '%' . $lettersOnly . '%')->where('people.code', 'LIKE', '%' . $numbersOnly . '%');
                }
            });
            $outletVisits = $outletVisits->where(function($query) use ($prefixCode) {
                $lettersOnly = preg_replace("/[^a-zA-Z]/", "", $prefixCode);
                $numbersOnly = preg_replace("/[^0-9]/", "", $prefixCode);
                if($lettersOnly && !$numbersOnly) {
                    $query->where('cust_prefixes.code', 'LIKE', '%' . $lettersOnly . '%');
                }
                if($numbersOnly && !$lettersOnly) {
                    $query->where('people.code', 'LIKE', '%' . $numbersOnly . '%');
                }
                if($lettersOnly && $numbersOnly) {
                    $query->where('cust_prefixes.code', 'LIKE', '%' . $lettersOnly . '%')->where('people.code', 'LIKE', '%' . $numbersOnly . '%');
                }
            });
        }

        if($company = request('company')) {
            $transactions = $transactions->where('people.id', 'LIKE', '%'.$company.'%');
            $deals = $deals->where('people.id', 'LIKE', '%'.$company.'%');
            $outletVisits = $outletVisits->where('people.id', 'LIKE', '%'.$company.'%');
        }

        $exclude_custcategory = request('exclude_custcategory');
        if($custCategory = request('custcategory')) {
            if (count($custCategory) == 1) {
                $custCategory = [$custCategory];
            }
            if($exclude_custcategory) {
                $transactions = $transactions->whereNotIn('custcategories.id', $custCategory);
                $deals = $deals->whereNotIn('custcategories.id', $custCategory);
                $outletVisits = $outletVisits->whereNotIn('custcategories.id', $custCategory);
            }else {
                $transactions = $transactions->whereIn('custcategories.id', $custCategory);
                $deals = $deals->whereIn('custcategories.id', $custCategory);
                $outletVisits = $outletVisits->whereIn('custcategories.id', $custCategory);
            }
        }

        $exclude_custcategory_group = request('exclude_custcategory_group');
        if($custCategoryGroup = request('custcategory_group')) {
            if (count($custCategoryGroup) == 1) {
                $custCategoryGroup = [$custCategoryGroup];
            }
            if($exclude_custcategory_group) {
                $transactions = $transactions->whereNotIn('custcategory_groups.id', $custCategoryGroup);
                $deals = $deals->whereNotIn('custcategory_groups.id', $custCategoryGroup);
                $outletVisits = $outletVisits->whereNotIn('custcategory_groups.id', $custCategoryGroup);
            }else {
                $transactions = $transactions->whereIn('custcategory_groups.id', $custCategoryGroup);
                $deals = $deals->whereIn('custcategory_groups.id', $custCategoryGroup);
                $outletVisits = $outletVisits->whereIn('custcategory_groups.id', $custCategoryGroup);
            }
        }

        if($acccountManager = request('account_manager')) {
            if($acccountManager === 'unassigned') {
                $transactions = $transactions->where(function($query) {
                    $query->where('people.account_manager', '=', null)->orWhere('people.account_manager', '=', '');
                });
                $deals = $deals->where(function($query) {
                    $query->where('people.account_manager', '=', null)->orWhere('people.account_manager', '=', '');
                });
                $outletVisits = $outletVisits->where(function($query) {
                    $query->where('people.account_manager', '=', null)->orWhere('people.account_manager', '=', '');
                });
            }else if($acccountManager !== 'unassigned' and $acccountManager !== 'total'){
                $transactions = $transactions->where('people.account_manager', $acccountManager);
                $deals = $deals->where('people.account_manager', $acccountManager);
                $outletVisits = $outletVisits->where('people.account_manager', $acccountManager);
            }
        }
/*
        else {
            $transactions = $transactions->whereNotNull('people.account_manager')->where('people.account_manager', '<>', '');
            $outletVisits = $outletVisits->whereNotNull('people.account_manager')->where('people.account_manager', '<>', '');
        } */

        if($zones = request('zones')) {
            if(count($zones) == 1) {
                $zones = [$zones];
            }
            $transactions = $transactions->whereIn('people.zone_id', $zones);
            $deals = $deals->whereIn('people.zone_id', $zones);
            $outletVisits = $outletVisits->whereIn('people.zone_id', $zones);
        }

        $transactions = $transactions->select(
            'account_manager.id AS account_manager_id', 'account_manager.name AS account_manager_name',
            DB::raw('ROUND(SUM(CASE WHEN transactions.gst=1 THEN(CASE WHEN transactions.is_gst_inclusive=0 THEN transactions.total ELSE transactions.total * 100/ (100 + transactions.gst_rate) END) ELSE transactions.total END), 2) AS sales_total'),
            DB::raw('MONTH(delivery_date) AS month'),
            DB::raw('DATE(delivery_date) AS date'),
            DB::raw('DATE_FORMAT(delivery_date, "%a") AS day')
        );
        $outletVisits = $outletVisits->select(
            'account_manager.id AS account_manager_id', 'account_manager.name AS account_manager_name',
            DB::raw('COUNT(outlet_visits.id) AS visited_total'),
            DB::raw('MONTH(date) AS month'),
            DB::raw('DATE(date) AS date'),
            DB::raw('DATE_FORMAT(date, "%a") AS day')
        );

        if(request('account_manager') === 'total') {
            $transactions = $transactions->groupBy('date');
            $outletVisits = $outletVisits->groupBy('date');
        }else {
            $transactions = $transactions->groupBy('date')->groupBy('account_manager.id');
            $outletVisits = $outletVisits->groupBy('date')->groupBy('account_manager.id');
        }

        if($sortName = request('sortName')){
            $transactions = $transactions->orderBy($sortName, request('sortBy') ? 'asc' : 'desc');
            $outletVisits = $outletVisits->orderBy($sortName, request('sortBy') ? 'asc' : 'desc');
        }else {
            $transactions = $transactions->orderBy('date', 'desc')->orderBy('account_manager.name', 'asc');
            $outletVisits = $outletVisits->orderBy('date', 'desc')->orderBy('account_manager.name', 'asc');
        }

        $transactions = $transactions->get();
        $outletvisits = $outletVisits->get();
        // dd($outlet);
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
                                            // $dataArr[$monthIndex]['dates'][$dateIndex][$transaction->date][$transaction->account_manager_id]['sales'] = $transaction->sales_total;
                                            $dataArr[$monthIndex]['dates'][$transaction->date][$transaction->account_manager_id]['sales'] = $transaction->sales_total;
                                            $salesTotal += round($transaction->sales_total, 2);
                                            $createNewTransaction = false;
                                            // unset($transaction);
                                        }
                                    }
                                }
                            }
                        }

                        if($createNewTransaction) {
                            if(request('account_manager') !== 'total') {
                                $dataArr[$monthIndex]['dates'][$transaction->date][$transaction->account_manager_id]['account_manager_name'] = $transaction->account_manager_name ?? 'UNASSIGNED';
                            }
                            $dataArr[$monthIndex]['dates'][$transaction->date][$transaction->account_manager_id]['sales'] = $transaction->sales_total;
                            $dataArr[$monthIndex]['dates'][$transaction->date][$transaction->account_manager_id]['date'] = $transaction->date;
                            $dataArr[$monthIndex]['dates'][$transaction->date][$transaction->account_manager_id]['day'] = $transaction->day;
                            $salesTotal += round($transaction->sales_total, 2);
                            // unset($transaction);
                        }

                    }
                }
            }

            if(request('account_manager') !== 'total') {
            if($outletvisits) {
                foreach($outletvisits as $outletvisit) {
                    $createNewVisit = true;
                    if($outletvisit->month == $months['month']){
                        // dd($outletvisit->toArray(), $months, $dataArr[$monthIndex]['dates']);
                        if($dataArr[$monthIndex]['dates']){
                            foreach($dataArr[$monthIndex]['dates'] as $dateIndex => $date) {
                                if($dateIndex == $outletvisit->date) {
                                    foreach($date as $managerIndex => $manager) {
                                        if($managerIndex == $outletvisit->account_manager_id) {
                                            $dataArr[$monthIndex]['dates'][$dateIndex][$outletvisit->account_manager_id]['visits'] = $outletvisit->visited_total;
                                            $visitTotal += $outletvisit->visited_total;
                                            $createNewVisit = false;
                                            // unset($outletvisit);
                                            // dd($outletvisit->account_manager_id, $outletvisit['account_manager_id'], $outletvisits->toArray(), $outletvisit->toArray());
                                        }
                                    }
                                }
                            }
                        }

                        if($createNewVisit) {
                            $dataArr[$monthIndex]['dates'][$outletvisit->delivery_date][$outletvisit->account_manager_id]['account_manager_name'] = $outletvisit->account_manager_name ?? 'UNASSIGNED';
                            $dataArr[$monthIndex]['dates'][$outletvisit->delivery_date][$outletvisit->account_manager_id]['visits'] = $outletvisit->visited_total;
                            $dataArr[$monthIndex]['dates'][$outletvisit->delivery_date][$outletvisit->account_manager_id]['date'] = $outletvisit->date;
                            $dataArr[$monthIndex]['dates'][$outletvisit->delivery_date][$outletvisit->account_manager_id]['day'] = $outletvisit->day;
                            $visitTotal += $outletvisit->visited_total;
                            // unset($outletvisit);
                        }

                    }
                }
            }
            }
            $dataArr[$monthIndex]['salesTotal'] = round($salesTotal, 2);
            $dataArr[$monthIndex]['visitTotal'] = $visitTotal;
        }
        return $dataArr;
    }

    private function filterCommission($deals, $request)
    {
        if($request->profile_id) {
            $deals = $deals->whereHas('transaction.person', function($query) use ($request) {
                $query->where('profile_id', $request->profile_id);
            });
        }
        if($request->date_from) {
            $deals = $deals->whereHas('transaction', function($query) use ($request) {
                $query->whereDate('delivery_date', '>=', $request->date_from);
            });
        }
        if($request->date_to) {
            $deals = $deals->whereHas('transaction', function($query) use ($request) {
                $query->whereDate('delivery_date', '<=', $request->date_to);
            });
        }
        if($request->cust_id) {
            $deals = $deals->whereHas('transaction.person', function($query) use ($request) {
                $query->where('cust_id', 'LIKE', $request->cust_id.'%');
            });
        }
        if($prefixCode = $request->prefix_code) {
            $transactions = $transactions->where(function($query) use ($prefixCode) {
                $lettersOnly = preg_replace("/[^a-zA-Z]/", "", $prefixCode);
                $numbersOnly = preg_replace("/[^0-9]/", "", $prefixCode);
                if($lettersOnly && !$numbersOnly) {
                    $query->whereHas('transaction.person.custPrefix', function($query) use ($lettersOnly) {
                        $query->where('code', 'LIKE', '%' . $lettersOnly . '%');
                    });
                }
                if($numbersOnly && !$lettersOnly) {
                    $query->whereHas('transaction.person', function($query) use ($numbersOnly) {
                        $query->where('code', 'LIKE', '%' . $numbersOnly . '%');
                    });
                }
                if($lettersOnly && $numbersOnly) {
                    $query->whereHas('transaction.person.custPrefix', function($query) use ($lettersOnly) {
                        $query->where('code', 'LIKE', '%' . $lettersOnly . '%');
                    })->whereHas('transaction.person', function($query) use ($numbersOnly) {
                        $query->where('code', 'LIKE', '%' . $numbersOnly . '%');
                    });
                }
            });
        }
        if($request->id_prefix) {
            $deals = $deals->whereHas('person', function($query) use ($request) {
                $query->where('cust_id', 'LIKE', $request->id_prefix.'%');
            });
        }

        if($request->is_commission != '') {
            $is_commission = $request->is_commission;
            switch($is_commission) {
                case '0':
                    $deals = $deals->whereHas('item', function($query) use ($request) {
                        $query->where('is_commission', $request->is_commission);
                        $query->where('is_supermarket_fee', $request->is_commission);
                    });
                    break;
                case '1':
                    $deals = $deals->whereHas('item', function($query) use ($request) {
                        $query->where('is_commission', 1);
                        $query->where('is_supermarket_fee', 0);
                    });
                    break;
                case '2':
                    $deals = $deals->whereHas('item', function($query) use ($request) {
                        $query->where('is_commission', 0);
                        $query->where('is_supermarket_fee', 1);
                    });
                    break;
            }
        }
        if($request->person_active) {
            $personstatus = $request->person_active;
            if (count($personstatus) == 1) {
                $personstatus = [$personstatus];
            }
            $deals = $deals->whereHas('person', function($query) use ($personstatus) {
                $query->whereIn('active', $personstatus);
            });
        }

        // set logic to distinguish driver or technician role
        $driver = '';

        if($request->driver) {
            $driver_role = User::where('name', $request->driver)->first();
            if($driver_role->hasRole('driver')) {
                $driver = 'driver';
                $deals = $deals->whereHas('transaction', function($query) use ($request) {
                    $query->where('driver', $request->driver);
                });
            }else if($driver_role->hasRole('technician')) {
                $driver = 'technician';
                $deals = $deals->whereHas('item', function($query) {
                    $query->where('product', '051');
                });
            }
        }

        if($request->custcategory) {
            $custcategories = $request->custcategory;
            if (count($custcategories) == 1) {
                $custcategories = [$custcategories];
            }
            if($request->exclude_custcategory) {
                $deals = $deals->whereHas('transaction', function($query) use ($custcategories) {
                    $query->whereHas('person', function($query) use ($custcategories) {
                        $query->whereNotIn('custcategory_id', $custcategories);
                    });
                });
            }else {
                $deals = $deals->whereHas('transaction', function($query) use ($custcategories) {
                    $query->whereHas('person', function($query) use ($custcategories) {
                        $query->whereIn('custcategory_id', $custcategories);
                    });
                });
            }
        }

        if($request->custcategory_group) {
            $custcategory_groups = $request->custcategory_group;
            if (count($custcategory_groups) == 1) {
                $custcategory_groups = [$custcategory_groups];
            }
            if($request->exclude_custcategory_group) {
                $deals = $deals->whereNotIn('custcategory_groups.id', $custcategory_groups);
            }else {
                $deals = $deals->whereIn('custcategory_groups.id', $custcategory_groups);
            }

            if($request->exclude_custcategory_group) {
                $deals = $deals->whereHas('person', function($query) use ($custcategory_groups) {
                    $query->whereHas('custcategory', function($query) use ($custcategory_groups) {
                        $query->whereNotIn('custcategory_group_id', $custcategory_groups);
                    });
                });
            }else {
                $deals = $deals->whereHas('person', function($query) use ($custcategory_groups) {
                    $query->whereHas('custcategory', function($query) use ($custcategory_groups) {
                        $query->whereIn('custcategory_group_id', $custcategory_groups);
                    });
                });
            }
        }

        if($request->status) {
            if($request->status == 'Delivered') {
                $deals = $deals->whereHas('transaction', function($query) {
                    $query->whereIn('status', ['Delivered', 'Verified Owe', 'Verified Paid']);
                });
            }else {
                $deals = $deals->whereHas('transaction', function($query) use ($request) {
                    $query->where('status', $request->status);
                });
            }
        }

        return $deals;
    }
}
