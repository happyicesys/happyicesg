<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests;
use App\Paysummaryinfo;
use App\Transaction;
use App\Month;
use App\Item;
use App\Person;
use App\Deal;
use Carbon\Carbon;
use Auth;
use DB;
use PDF;
use App\Profile;
use Laracasts\Flash\Flash;
use App\GeneralSetting;

// traits
use App\HasProfileAccess;
use App\HasMonthOptions;

class DetailRptController extends Controller
{
    use HasProfileAccess, HasMonthOptions;

    // detect authed
    public function __construct()
    {
        $this->middleware('auth');
    }

    // return index page for detailed report - account
    public function accountIndex()
    {
        $month_options = $this->getMonthOptions();
        return view('detailrpt.account.index', compact('month_options'));
    }

    // return index page for detailed report - sales
    public function salesIndex()
    {
        $month_options = $this->getMonthOptions();
        return view('detailrpt.sales.index', compact('month_options'));
    }

    // retrieve the account cust detail rpt(FormRequest $request)
    public function getAccountCustdetailApi(Request $request)
    {
        // showing total amount init
        $total_amount = 0;
        $input = $request->all();
        // initiate the page num when null given
        $pageNum = $request->pageNum ? $request->pageNum : 100;

        $transactions = DB::table('deals')
                        ->leftJoin('items', 'items.id', '=', 'deals.item_id')
                        ->leftJoin('transactions', 'transactions.id', '=', 'deals.transaction_id')
                        ->leftJoin('people', 'transactions.person_id', '=', 'people.id')
                        ->leftJoin('profiles', 'people.profile_id', '=', 'profiles.id')
                        ->leftJoin('custcategories', 'custcategories.id', '=', 'people.custcategory_id')
                        ->select(
                                    DB::raw('ROUND(CASE WHEN profiles.gst=1 THEN (CASE WHEN transactions.delivery_fee>0 THEN transactions.total*(100+people.gst_rate)/100 + transactions.delivery_fee ELSE transactions.total*(100+people.gst_rate)/100 END) ELSE (CASE WHEN transactions.delivery_fee>0 THEN transactions.total + transactions.delivery_fee ELSE transactions.total END) END, 2) AS total'),
                                    DB::raw('ROUND(SUM(CASE WHEN deals.divisor > 1 THEN (items.base_unit * deals.dividend/ deals.divisor) ELSE (items.base_unit * deals.qty) END)) AS pieces'),
                                    'transactions.id', 'people.cust_id', 'people.company',
                                    'people.name', 'people.id as person_id',
                                    'transactions.status', 'transactions.delivery_date', 'profiles.name as profile_name',
                                    'transactions.pay_status',
                                    'profiles.id as profile_id', 'transactions.order_date',
                                    'profiles.gst', 'people.gst_rate', 'transactions.delivery_fee', 'transactions.paid_at',
                                    'custcategories.name as custcategory'
                                );

        // reading whether search input is filled
        if($request->id or $request->cust_id or $request->company or $request->status or $request->pay_status or $request->updated_by or $request->updated_at or $request->delivery_from or $request->delivery_to or $request->driver or $request->profile or $request->custcategory or $request->franchisee_id){
            $transactions = $this->searchTransactionDBFilter($transactions, $request);
        }

        // add user profile filters
        $transactions = $this->filterUserDbProfile($transactions);

        $total_amount = $this->calDBOriginalTotal($transactions);

        if($request->exportSOA) {
            $this->convertSoaExcel($transactions, $total_amount);
        }

        if($request->sortName){
            $transactions = $transactions->orderBy($request->sortName, $request->sortBy ? 'asc' : 'desc');
        }

        $transactions = $transactions->latest('transactions.created_at')->groupBy('transactions.id');

        if($pageNum == 'All'){
            $transactions = $transactions->get();
        }else{
            $transactions = $transactions->paginate($pageNum);
        }

        if($request->exportExcel) {
            $this->convertAccountCustdetailExcel($transactions, $total_amount);
        }

        $data = [
            'total_amount' => $total_amount,
            'transactions' => $transactions,
        ];

        return $data;
    }

    // retrieve the account outstanding rpt(FormRequest $request)
    public function getAccountOutstandingApi(Request $request)
    {
        // showing total amount init
        $total_amount = 0;
        $input = $request->all();
        // initiate the page num when null given
        $pageNum = $request->pageNum ? $request->pageNum : 100;

        // indicate the month and year
        $carbondate = Carbon::createFromFormat('d-m-Y', '01-'.$request->current_month);
        $prevMonth = Carbon::createFromFormat('d-m-Y', '01-'.$request->current_month)->subMonth();
        $prev2Months = Carbon::createFromFormat('d-m-Y', '01-'.$request->current_month)->subMonths(2);
        $prev3Months = Carbon::createFromFormat('d-m-Y', '01-'.$request->current_month)->subMonths(3);

        $thistotal = DB::raw("(SELECT ROUND(SUM(CASE WHEN profiles.gst=1 THEN (CASE WHEN people.is_gst_inclusive=0 THEN total*(100+people.gst_rate)/100 ELSE total END) ELSE total END) + (CASE WHEN delivery_fee>0 THEN delivery_fee ELSE 0 END), 2) AS thistotal, people.id AS person_id, people.profile_id FROM transactions
                                LEFT JOIN people ON transactions.person_id=people.id
                                LEFT JOIN profiles ON people.profile_id=profiles.id
                                WHERE transactions.delivery_date>='".$carbondate->startOfMonth()->toDateString()."'
                                AND transactions.delivery_date<='".$carbondate->endOfMonth()->toDateString()."'
                                AND pay_status='Owe'
                                AND (status='Delivered' OR status='Verified Owe')
                                GROUP BY people.id) thistotal");

        $prevtotal = DB::raw("(SELECT ROUND(SUM(CASE WHEN profiles.gst=1 THEN (CASE WHEN people.is_gst_inclusive=0 THEN total*(100+people.gst_rate)/100 ELSE total END) ELSE total END) + (CASE WHEN delivery_fee>0 THEN delivery_fee ELSE 0 END), 2) AS prevtotal, people.id AS person_id, people.profile_id FROM transactions
                                LEFT JOIN people ON transactions.person_id=people.id
                                LEFT JOIN profiles ON people.profile_id=profiles.id
                                WHERE transactions.delivery_date>='".$prevMonth->startOfMonth()->toDateString()."'
                                AND transactions.delivery_date<='".$prevMonth->endOfMonth()->toDateString()."'
                                AND pay_status='Owe'
                                AND (status='Delivered' OR status='Verified Owe')
                                GROUP BY people.id) prevtotal");

        $prev2total = DB::raw("(SELECT ROUND(SUM(CASE WHEN profiles.gst=1 THEN (CASE WHEN people.is_gst_inclusive=0 THEN total*(100+people.gst_rate)/100 ELSE total END) ELSE total END) + (CASE WHEN delivery_fee>0 THEN delivery_fee ELSE 0 END), 2) AS prev2total, people.id AS person_id, people.profile_id FROM transactions
                                LEFT JOIN people ON transactions.person_id=people.id
                                LEFT JOIN profiles ON people.profile_id=profiles.id
                                WHERE transactions.delivery_date>='".$prev2Months->startOfMonth()->toDateString()."'
                                AND transactions.delivery_date<='".$prev2Months->endOfMonth()->toDateString()."'
                                AND pay_status='Owe'
                                AND (status='Delivered' OR status='Verified Owe')
                                GROUP BY people.id) prev2total");

        $prevmore3total = DB::raw("(SELECT ROUND(SUM(CASE WHEN profiles.gst=1 THEN (CASE WHEN people.is_gst_inclusive=0 THEN total*(100+people.gst_rate)/100 ELSE total END) ELSE total END) + (CASE WHEN delivery_fee>0 THEN delivery_fee ELSE 0 END), 2) AS prevmore3total, people.id AS person_id, people.profile_id FROM transactions
                                LEFT JOIN people ON transactions.person_id=people.id
                                LEFT JOIN profiles ON people.profile_id=profiles.id
                                WHERE transactions.delivery_date<='".$prev3Months->endOfMonth()->toDateString()."'
                                AND pay_status='Owe'
                                AND (status='Delivered' OR status='Verified Owe')
                                GROUP BY people.id) prevmore3total");

        $transactions = DB::table('transactions')
                        ->leftJoin('people', 'transactions.person_id', '=', 'people.id')
                        ->leftJoin('profiles', 'people.profile_id', '=', 'profiles.id')
                        ->leftJoin('custcategories', 'custcategories.id', '=', 'people.custcategory_id')
                        ->leftJoin($thistotal, 'people.id', '=', 'thistotal.person_id')
                        ->leftJoin($prevtotal, 'people.id', '=', 'prevtotal.person_id')
                        ->leftJoin($prev2total, 'people.id', '=', 'prev2total.person_id')
                        ->leftJoin($prevmore3total, 'people.id', '=', 'prevmore3total.person_id')
                        ->select(
                                    'people.cust_id', 'people.company', 'people.name', 'people.id as person_id',
                                    'profiles.name as profile_name', 'profiles.id as profile_id', 'profiles.gst',
                                    'people.is_gst_inclusive', 'people.gst_rate',
                                    'transactions.id', 'transactions.status', 'transactions.delivery_date', 'transactions.pay_status', 'transactions.delivery_fee', 'transactions.paid_at', 'transactions.created_at',
                                    'custcategories.name as custcategory',
                                    'thistotal.thistotal AS thistotal', 'prevtotal.prevtotal AS prevtotal', 'prev2total.prev2total AS prev2total', 'prevmore3total.prevmore3total AS prevmore3total'
                                );

        if($request->id or $request->cust_id or $request->company or $request->status or $request->pay_status or $request->updated_by or $request->updated_at or $request->driver or $request->profile){
            $transactions = $this->searchTransactionDBFilter($transactions, $request);
        }

        // add user profile filters
        $transactions = $this->filterUserDbProfile($transactions);

        $transactions = $transactions->latest('transactions.created_at')->groupBy('people.id');

        if($request->sortName){
            $transactions = $transactions->orderBy($request->sortName, $request->sortBy ? 'asc' : 'desc');
        }

        $total_amount = $this->calCustoutstandingTotal($transactions);

        if($pageNum == 'All'){
            $transactions = $transactions->get();
        }else{
            $transactions = $transactions->paginate($pageNum);
        }

        $data = [
            'total_amount' => $total_amount,
            'transactions' => $transactions,
        ];

        return $data;
    }

    // retrieve the account customer payment detail rpt
    public function getAccountPaydetailApi(Request $request)
    {
        // showing total amount init
        $total_amount = 0;
        $input = $request->all();
        $pageNum = $request->pageNum ? $request->pageNum : 100;

        $transactions = DB::table('transactions')
                        ->leftJoin('people', 'transactions.person_id', '=', 'people.id')
                        ->leftJoin('profiles', 'people.profile_id', '=', 'profiles.id')
                        ->leftJoin('custcategories', 'custcategories.id', '=', 'people.custcategory_id')
                        ->select(
                                    'people.cust_id', 'people.company', 'people.name', 'people.id as person_id',
                                    'profiles.name as profile_name', 'profiles.id as profile_id',
                                    'transactions.id', 'transactions.delivery_fee', 'transactions.paid_at', 'transactions.status', 'transactions.delivery_date', 'transactions.pay_status', 'transactions.order_date', 'transactions.note', 'transactions.pay_method',
                                    'custcategories.name as custcategory',
                                    // DB::raw('(CASE WHEN transactions.delivery_fee>0 THEN (transactions.total + transactions.delivery_fee) ELSE transactions.total END) AS inv_amount'),
                                    DB::raw('(CASE WHEN profiles.gst=1 THEN (CASE WHEN people.is_gst_inclusive=1 THEN (transactions.total - (transactions.total - transactions.total/((100+people.gst_rate)/100))) ELSE transactions.total END) ELSE transactions.total END) + (CASE WHEN transactions.delivery_fee>0 THEN transactions.delivery_fee ELSE 0 END) AS inv_amount'),
                                    DB::raw('(CASE WHEN profiles.gst=1 THEN (CASE WHEN people.is_gst_inclusive=0 THEN (transactions.total * (100+people.gst_rate)/100) ELSE transactions.total END) ELSE transactions.total END) + (CASE WHEN transactions.delivery_fee>0 THEN transactions.delivery_fee ELSE 0 END) AS amount'),
                                    DB::raw('(CASE WHEN profiles.gst=1 THEN (CASE WHEN people.is_gst_inclusive=1 THEN (transactions.total - transactions.total/((100+people.gst_rate)/100)) ELSE transactions.total * (people.gst_rate)/100 END) ELSE null END) AS gst')
                                );
        // reading whether search input is filled
        if($request->profile_id or $request->payment_from or $request->delivery_from or $request->cust_id or $request->payment_to or $request->delivery_to or $request->company or $request->payment or $request->status or $request->person_id or $request->pay_method or $request->custcategory) {
            $transactions = $this->searchTransactionDBFilter($transactions, $request);
        }
        if($request->sortName){
            $transactions = $transactions->orderBy($request->sortName, $request->sortBy ? 'asc' : 'desc');
        }

        // add user profile filters
        $transactions = $this->filterUserDbProfile($transactions);

        $caldata = $this->calPayDetailTotal($transactions);

        if($pageNum == 'All'){
            $transactions = $transactions->latest('transactions.created_at')->get();
        }else{
            $transactions = $transactions->latest('transactions.created_at')->paginate($pageNum);
        }

        $data = [
            'total_inv_amount' => $caldata['total_inv_amount'],
            'total_gst' => $caldata['total_gst'],
            'total_amount' => $caldata['total_amount'],
            'transactions' => $transactions,
        ];

        return $data;
    }

    // retrieve the account customer payment summary api
    public function getAccountPaysummaryApi(Request $request)
    {
        // showing total amount init
        $total_amount = 0;
        $input = $request->all();
        // initiate the page num when null given
        $pageNum = $request->pageNum ? $request->pageNum : 100;

        $transactions = DB::table('transactions')
                        ->leftJoin('people', 'people.id', '=', 'transactions.person_id')
                        ->leftJoin('profiles', 'people.profile_id', '=', 'profiles.id')
                        ->leftJoin('paysummaryinfos', function($join) {
                            $join->on(DB::raw('Date(paysummaryinfos.paid_at)'), '=', DB::raw('Date(transactions.paid_at)'));
                            $join->on('paysummaryinfos.pay_method', '=', 'transactions.pay_method');
                            $join->on('paysummaryinfos.profile_id', '=', 'profiles.id');
                        })
                        ->leftJoin('users', 'users.id', '=', 'paysummaryinfos.user_id')
                        ->select(
                                    'profiles.name as profile', 'profiles.id as profile_id',
                                    'transactions.delivery_fee', 'transactions.pay_status', 'transactions.pay_method', 'transactions.paid_at as payreceived_date',
                                    'users.name',
                                    'paysummaryinfos.remark',
                                    'paysummaryinfos.is_verified',
                                    DB::raw('DATE(paysummaryinfos.bankin_date) AS bankin_date'),
                                    DB::raw('SUM(ROUND((CASE WHEN profiles.gst=1 THEN (CASE WHEN people.is_gst_inclusive=0 THEN (transactions.total * (100+people.gst_rate)/100) ELSE transactions.total END) ELSE transactions.total END) + (CASE WHEN transactions.delivery_fee>0 THEN transactions.delivery_fee ELSE 0 END), 2)) AS total')
                                );
        // reading whether search input is filled
        if($request->profile_id or $request->payment_from or $request->payment_to or $request->bankin_from or $request->bankin_to){
            $transactions = $this->searchTransactionDBFilter($transactions, $request);
        }
        // paid conditions
        $transactions = $transactions->where('transactions.pay_status', 'Paid')->whereNotNull('transactions.pay_method');

        // add user profile filters
        $transactions = $this->filterUserDbProfile($transactions);

        $totals = $this->calAccPaySummary($transactions);

        $transactions = $transactions->groupBy(DB::raw('Date(transactions.paid_at)'), 'profiles.id', 'transactions.pay_method')->orderBy('transactions.paid_at', 'profiles.id');
        if($request->sortName){
            $transactions = $transactions->orderBy($request->sortName, $request->sortBy ? 'asc' : 'desc');
        }
        if($pageNum == 'All'){
            $transactions = $transactions->get();
        }else{
            $transactions = $transactions->paginate($pageNum);
        }

        $data = [
            'totals' => $totals,
            'transactions' => $transactions,
        ];

        if($request->export_excel) {
            $this->paySummaryExportExcel($data);
        }

        return $data;
    }

    // retrieve the sales cust detail(FormRequest $request)
    public function getSalesCustdetailApi(Request $request)
    {
        // showing total amount init
        $total_amount = 0;
        $input = $request->all();
        // initiate the page num when null given
        $pageNum = $request->pageNum ? $request->pageNum : 100;

        // indicate the month and year
        $carbondate = Carbon::createFromFormat('d-m-Y', '01-'.$request->current_month);
        $prevMonth = Carbon::createFromFormat('d-m-Y', '01-'.$request->current_month)->subMonth();
        $prev2Months = Carbon::createFromFormat('d-m-Y', '01-'.$request->current_month)->subMonths(2);
        $prevYear = Carbon::createFromFormat('d-m-Y', '01-'.$request->current_month)->subYear();
        $delivery_from = $carbondate->startOfMonth()->toDateString();
        $delivery_to = $carbondate->endOfMonth()->toDateString();
        $request->merge(array('delivery_from' => $delivery_from));
        $request->merge(array('delivery_to' => $delivery_to));
        $status = $request->status;

        if($status) {
            if($status == 'Delivered') {
                $statusStr = " (transactions.status='Delivered' or transactions.status='Verified Owe' or transactions.status='Verified Paid')";
            }else {
                $statusStr = " transactions.status='".$status."'";
            }
        }else {
            $statusStr = ' 1=1';
        }
        if(request('is_commission') != '') {
            $commissionStr = " items.is_commission='".request('is_commission')."' ";
        }else {
            $commissionStr = " 1=1 ";
        }

        $thistotal = DB::raw("(
                            SELECT people.id AS person_id, ROUND(SUM(deals.amount), 2) AS thistotal,
                                people.profile_id
                                FROM deals
                                LEFT JOIN items ON items.id=deals.item_id
                                LEFT JOIN transactions ON transactions.id=deals.transaction_id
                                LEFT JOIN people ON transactions.person_id=people.id
                                LEFT JOIN profiles ON people.profile_id=profiles.id
                                WHERE transactions.delivery_date>='".$delivery_from."'
                                AND transactions.delivery_date<='".$delivery_to."'
                                AND ".$statusStr."
                                AND ".$commissionStr."
                                GROUP BY people.id) thistotal");

        $prevtotal = DB::raw("(SELECT people.id AS person_id, ROUND(SUM(deals.amount), 2) AS prevtotal,
                                people.profile_id
                                FROM deals
                                LEFT JOIN items ON items.id=deals.item_id
                                LEFT JOIN transactions ON transactions.id=deals.transaction_id
                                LEFT JOIN people ON transactions.person_id=people.id
                                LEFT JOIN profiles ON people.profile_id=profiles.id
                                WHERE transactions.delivery_date>='".$prevMonth->startOfMonth()->toDateString()."'
                                AND transactions.delivery_date<='".$prevMonth->endOfMonth()->toDateString()."'
                                AND ".$statusStr."
                                AND ".$commissionStr."
                                GROUP BY people.id) prevtotal");

        $prev2total = DB::raw("(SELECT people.id AS person_id, ROUND(SUM(deals.amount), 2) AS prev2total,
                                people.profile_id
                                FROM deals
                                LEFT JOIN items ON items.id=deals.item_id
                                LEFT JOIN transactions ON transactions.id=deals.transaction_id
                                LEFT JOIN people ON transactions.person_id=people.id
                                LEFT JOIN profiles ON people.profile_id=profiles.id
                                WHERE transactions.delivery_date>='".$prev2Months->startOfMonth()->toDateString()."'
                                AND transactions.delivery_date<='".$prev2Months->endOfMonth()->toDateString()."'
                                AND ".$statusStr."
                                AND ".$commissionStr."
                                GROUP BY people.id) prev2total");

        $prevyeartotal = DB::raw("(SELECT people.id AS person_id, ROUND(SUM(deals.amount), 2) AS prevyeartotal,
                                people.profile_id
                                FROM deals
                                LEFT JOIN items ON items.id=deals.item_id
                                LEFT JOIN transactions ON transactions.id=deals.transaction_id
                                LEFT JOIN people ON transactions.person_id=people.id
                                LEFT JOIN profiles ON people.profile_id=profiles.id
                                WHERE transactions.delivery_date>='".$prevYear->startOfMonth()->toDateString()."'
                                AND transactions.delivery_date<='".$prevYear->endOfMonth()->toDateString()."'
                                AND ".$statusStr."
                                AND ".$commissionStr."
                                GROUP BY people.id) prevyeartotal");

        $transactions = DB::table('deals')
                        ->leftJoin('items', 'items.id', '=', 'deals.item_id')
                        ->leftJoin('transactions', 'transactions.id', '=', 'deals.transaction_id')
                        ->leftJoin('people', 'transactions.person_id', '=', 'people.id')
                        ->leftJoin('profiles', 'people.profile_id', '=', 'profiles.id')
                        ->leftJoin('custcategories', 'custcategories.id', '=', 'people.custcategory_id')
                        ->leftJoin($thistotal, 'people.id', '=', 'thistotal.person_id')
                        ->leftJoin($prevtotal, 'people.id', '=', 'prevtotal.person_id')
                        ->leftJoin($prev2total, 'people.id', '=', 'prev2total.person_id')
                        ->leftJoin($prevyeartotal, 'people.id', '=', 'prevyeartotal.person_id')
                        ->select(
                                    'items.is_commission',
                                    'people.cust_id', 'people.company', 'people.name', 'people.id as person_id',
                                    'profiles.name as profile_name', 'profiles.id as profile_id', 'profiles.gst', 'people.gst_rate',
                                    'transactions.id', 'transactions.status', 'transactions.delivery_date', 'transactions.pay_status', 'transactions.delivery_fee', 'transactions.paid_at', 'transactions.created_at',
                                    'custcategories.name as custcategory',
                                    'thistotal.thistotal AS thistotal', 'prevtotal.prevtotal AS prevtotal', 'prev2total.prev2total AS prev2total', 'prevyeartotal.prevyeartotal AS prevyeartotal'
                                );

        if($request->id or $request->current_month or $request->cust_id or $request->company or $request->delivery_from or $request->delivery_to or $request->profile_id or $request->id_prefix or $request->custcategory or $request->status){
            $transactions = $this->searchTransactionDBFilter($transactions, $request);
        }

        // add user profile filters
        $transactions = $this->filterUserDbProfile($transactions);

        if(request('is_commission') != '') {
            $transactions = $transactions->where('items.is_commission', request('is_commission'));
        }

        $transactions = $transactions->orderBy('people.cust_id')->groupBy('people.id');

        if($request->sortName){
            $transactions = $transactions->orderBy($request->sortName, $request->sortBy ? 'asc' : 'desc');
        }

        $total_amount = $this->calTransactionTotalSql($transactions);

        if($pageNum == 'All'){
            $transactions = $transactions->get();
        }else{
            $transactions = $transactions->paginate($pageNum);
        }

        $data = [
            'total_amount' => $total_amount,
            'transactions' => $transactions,
        ];

        return $data;
    }

    // retrieve the product detail for month api(FormRequest $request)
    public function getSalesProductDetailMonthApi(Request $request)
    {
        // showing total amount init
        $total_amount = 0;
        $input = $request->all();
        // initiate the page num when null given
        $pageNum = $request->pageNum ? $request->pageNum : 100;
        $thismonth = Carbon::createFromFormat('d-m-Y', '01-'.$request->current_month);
        $prevMonth = Carbon::createFromFormat('d-m-Y', '01-'.$request->current_month)->subMonth();
        $prev2Months = Carbon::createFromFormat('d-m-Y', '01-'.$request->current_month)->subMonths(2);
        $prevYear = Carbon::createFromFormat('d-m-Y', '01-'.$request->current_month)->subYear();
        $profile_id = $request->profile_id;

        $thistotal = "(SELECT ROUND(SUM(amount), 2) AS amount, ROUND(SUM(qty), 4) AS qty, deals.item_id, profiles.name AS profile_name, profiles.id AS profile_id, deals.id AS id FROM deals
                        LEFT JOIN transactions ON transactions.id=deals.transaction_id
                        LEFT JOIN people ON people.id=transactions.person_id
                        LEFT JOIN profiles ON profiles.id=people.profile_id
                        WHERE transactions.delivery_date>='".$thismonth->startOfMonth()->toDateString()."'
                        AND transactions.delivery_date<='".$thismonth->endOfMonth()->toDateString()."'";
        $prevqty = "(SELECT ROUND(SUM(qty), 4) AS qty, deals.item_id, profiles.name AS profile_name, profiles.id AS profile_id, deals.id FROM deals
                    LEFT JOIN transactions ON transactions.id=deals.transaction_id
                    LEFT JOIN people ON people.id=transactions.person_id
                    LEFT JOIN profiles ON profiles.id=people.profile_id
                    WHERE transactions.delivery_date>='".$prevMonth->startOfMonth()->toDateString()."'
                    AND transactions.delivery_date<='".$prevMonth->endOfMonth()->toDateString()."'";
        $prev2qty = "(SELECT ROUND(SUM(qty), 4) AS qty, deals.item_id, profiles.name AS profile_name, profiles.id AS profile_id, deals.id FROM deals
                    LEFT JOIN transactions ON transactions.id=deals.transaction_id
                    LEFT JOIN people ON people.id=transactions.person_id
                    LEFT JOIN profiles ON profiles.id=people.profile_id
                    WHERE transactions.delivery_date>='".$prev2Months->startOfMonth()->toDateString()."'
                    AND transactions.delivery_date<='".$prev2Months->endOfMonth()->toDateString()."'";
        $prevyrqty = "(SELECT ROUND(SUM(qty), 4) AS qty, deals.item_id, profiles.name AS profile_name, profiles.id AS profile_id, deals.id FROM deals
                        LEFT JOIN transactions ON transactions.id=deals.transaction_id
                        LEFT JOIN people ON people.id=transactions.person_id
                        LEFT JOIN profiles ON profiles.id=people.profile_id
                        WHERE transactions.delivery_date>='".$prevYear->startOfMonth()->toDateString()."'
                        AND transactions.delivery_date<='".$prevYear->endOfMonth()->toDateString()."'";

        if($request->status) {
            if($request->status === 'Delivered') {
                $thistotal .= " AND (transactions.status='Delivered' OR transactions.status='Verified Owe' OR transactions.status='Verified Paid')";
                $prevqty .= " AND (transactions.status='Delivered' OR transactions.status='Verified Owe' OR transactions.status='Verified Paid')";
                $prev2qty .= " AND (transactions.status='Delivered' OR transactions.status='Verified Owe' OR transactions.status='Verified Paid')";
                $prevyrqty .= " AND (transactions.status='Delivered' OR transactions.status='Verified Owe' OR transactions.status='Verified Paid')";
            }else {
                $thistotal .= " AND transactions.status='".$request->status."'";
                $prevqty .= " AND transactions.status='".$request->status."'";
                $prev2qty .= " AND transactions.status='".$request->status."'";
                $prevyrqty .= " AND transactions.status='".$request->status."'";
            }
        }

        if(count($profileIds = $this->getUserProfileIdArray()) > 0) {
            $profileIdStr = implode(",", $profileIds);
            $thistotal .= " AND profiles.id IN (".$profileIdStr.")";
            $prevqty .= " AND profiles.id IN (".$profileIdStr.")";
            $prev2qty .= " AND profiles.id IN (".$profileIdStr.")";
            $prevyrqty .= " AND profiles.id IN (".$profileIdStr.")";
        }


        if($request->profile_id) {
            $thistotal .= " GROUP BY item_id, profile_id) thistotal";
            $prevqty .= " GROUP BY item_id, profile_id) prevqty";
            $prev2qty .= " GROUP BY item_id, profile_id) prev2qty";
            $prevyrqty .= " GROUP BY item_id, profile_id) prevyrqty";
        }else {
            $thistotal .= " GROUP BY item_id) thistotal";
            $prevqty .= " GROUP BY item_id) prevqty";
            $prev2qty .= " GROUP BY item_id) prev2qty";
            $prevyrqty .= " GROUP BY item_id) prevyrqty";
        }
        $thistotal = DB::raw($thistotal);
        $prevqty = DB::raw($prevqty);
        $prev2qty = DB::raw($prev2qty);
        $prevyrqty = DB::raw($prevyrqty);

        $items = DB::table('deals')
                ->leftJoin('items', 'items.id', '=', 'deals.item_id')
                ->leftJoin('transactions', 'transactions.id', '=', 'deals.transaction_id')
                ->leftJoin('people', 'people.id', '=', 'transactions.person_id')
                ->leftJoin('profiles', 'profiles.id', '=', 'people.profile_id')
                ->leftJoin($thistotal, function($join) use ($profile_id) {
                    if($profile_id) {
                        $join->on('thistotal.profile_id', '=', 'profiles.id');
                    }
                    $join->on('thistotal.item_id', '=', 'items.id');
                })
                ->leftJoin($prevqty, function($join) use ($profile_id){
                    if($profile_id) {
                        $join->on('thistotal.profile_id', '=', 'profiles.id');
                    }
                    $join->on('prevqty.item_id', '=', 'items.id');
                })
                ->leftJoin($prev2qty, function($join) use ($profile_id){
                    if($profile_id) {
                        $join->on('prev2qty.profile_id', '=', 'profiles.id');
                    }
                    $join->on('prev2qty.item_id', '=', 'items.id');
                })
                ->leftJoin($prevyrqty, function($join) use ($profile_id){
                    if($profile_id) {
                        $join->on('prevyrqty.profile_id', '=', 'profiles.id');
                    }
                    $join->on('prevyrqty.item_id', '=', 'items.id');
                })
                ->select(
                        'items.name AS product_name', 'items.remark', 'items.product_id', 'items.id', 'items.is_inventory', 'items.is_commission',
                        'thistotal.amount AS amount', 'thistotal.qty AS qty', 'profiles.name AS profile_name', 'profiles.id AS profile_id',
                        'transactions.status',
                        'prevqty.qty AS prevqty', 'prev2qty.qty AS prev2qty', 'prevyrqty.qty AS prevyrqty'
                    );

        // reading whether search input is filled
        if($request->profile_id or $request->current_month or $request->id_prefix or $request->cust_id or $request->company or $request->custcategory or $request->status) {
            $items = $this->searchItemDBFilter($items, $request);
        }

        // add user profile filters
        $items = $this->filterUserDbProfile($items);

        if(request('is_commission') != '') {
            $items = $items->where('items.is_commission', request('is_commission'));
        }

        if($request->profile_id) {
            $items = $items->groupBy('items.id', 'profiles.id')->orderBy('items.product_id');
        }else {
            $items = $items->groupBy('items.id')->orderBy('items.product_id');
        }

        if($request->sortName){
            $items = $items->orderBy($request->sortName, $request->sortBy ? 'asc' : 'desc');
        }

        if($pageNum == 'All'){
            $items = $items->get();
        }else{
            $items = $items->paginate($pageNum);
        }

        $totals = $this->calSalesProductDetailMonthTotals($items);

        $data = [
            'total_qty' => $totals['total_qty'],
            'total_amount' => $totals['total_amount'],
            'items' => $items,
        ];
        return $data;
    }

    // retrieve the product detail for day api(FormRequest $request)
    public function getSalesProductDetailDayApi(Request $request)
    {
        // showing total amount init
        $total_amount = 0;
        $input = $request->all();
        // initiate the page num when null given
        $pageNum = $request->pageNum ? $request->pageNum : 100;

        $amountstr = "SELECT ROUND(SUM(amount), 2) AS thisamount, ROUND(SUM(CASE WHEN items.is_inventory=1 THEN qty END), 4) AS thisqty, item_id, transaction_id
                        FROM deals
                        LEFT JOIN items ON items.id=deals.item_id
                        LEFT JOIN transactions ON transactions.id=deals.transaction_id
                        LEFT JOIN people ON people.id=transactions.person_id
                        LEFT JOIN profiles ON profiles.id=people.profile_id
                        LEFT JOIN custcategories ON custcategories.id=people.custcategory_id
                        WHERE transactions.delivery_date>='".request('delivery_from')."'
                        AND transactions.delivery_date<='".request('delivery_to')."'";

        if($request->delivery_from) {
            $amountstr = $amountstr." AND delivery_date >= '".$request->delivery_from."'";
        }
        if($request->delivery_to) {
            $amountstr = $amountstr." AND delivery_date <= '".$request->delivery_to."'";
        }
        if($request->cust_id) {
            $amountstr = $amountstr." AND people.cust_id LIKE '%".$request->cust_id."%'";
        }
        if($request->company) {
            $amountstr = $amountstr." AND people.company LIKE '%".$request->company."%'";
        }
        if($request->profile_id) {
            $amountstr = $amountstr." AND profiles.id =".$request->profile_id;
        }
        if($request->cust_category) {
            $amountstr = $amountstr." AND custcategories.id =".$request->cust_category;
        }
        if($request->status) {
            if($request->status === 'Delivered') {
                $amountstr .= " AND (transactions.status='Delivered' OR transactions.status='Verified Owe' OR transactions.status='Verified Paid')";
            }else {
                $amountstr .=" AND transactions.status='".$request->status."'";
            }
        }

        if(request('is_commission') != '') {
            $amountstr .= " AND items.is_commission='".request('is_commission')."'";
        }

        if(count($profileIds = $this->getUserProfileIdArray()) > 0) {
            $profileIdStr = implode(",", $profileIds);
            $amountstr .= " AND profiles.id IN (".$profileIdStr.")";
        }

        $totals = DB::raw("(".$amountstr." GROUP BY item_id) totals");

        $items = DB::table('items')
                        ->leftJoin($totals, 'items.id', '=', 'totals.item_id')
                        ->select(
                                    'items.name AS product_name', 'items.remark', 'items.product_id',
                                    'totals.thisamount AS amount', 'totals.thisqty AS qty'
                                );
        if($request->sortName){
            $items = $items->orderBy($request->sortName, $request->sortBy ? 'asc' : 'desc');
        }

        if($pageNum == 'All'){
            $items = $items->orderBy('items.product_id')->get();
        }else{
            $items = $items->orderBy('items.product_id')->paginate($pageNum);
        }

        $totals_arr = $this->calItemTotals($items);

        $data = [
            'total_qty' => $totals_arr['total_qty'],
            'total_amount' => $totals_arr['total_amount'],
            'items' => $items,
        ];

        return $data;
    }

    // submit pay summary form (Formrequest $request)
    public function submitPaySummary(Request $request)
    {
        $checkboxes = $request->checkboxes;
        $bankin_dates = $request->bankin_dates;
        $paid_ats = $request->paid_ats;
        $pay_methods = $request->pay_methods;
        $profile_ids = $request->profile_ids;
        $remarks = $request->remarks;
        $submitbtn = $request->submitbtn;

        if($checkboxes) {
            foreach($checkboxes as $index => $checkbox) {
                if($bankin_dates[$index] !== '' or $remarks[$index] !== '') {

                    switch($submitbtn) {
                        case 'submit':
                            $exist = Paysummaryinfo::whereDate('paid_at', '=', Carbon::parse($paid_ats[$index])->toDateString())->wherePayMethod($pay_methods[$index])->whereProfileId($profile_ids[$index])->first();
                            if (!$exist) {
                                $paysummaryinfo = new Paysummaryinfo();
                                $paysummaryinfo->paid_at = $paid_ats[$index];
                                $paysummaryinfo->pay_method = $pay_methods[$index];
                                $paysummaryinfo->profile_id = $profile_ids[$index];
                            } else {
                                $paysummaryinfo = $exist;
                            }
                            $paysummaryinfo->remark = $remarks[$index];
                            $paysummaryinfo->bankin_date = $bankin_dates[$index] ? Carbon::parse($bankin_dates[$index]) : null;
                            $paysummaryinfo->user_id = Auth::user()->id;
                            $paysummaryinfo->save();
                            break;
                        
                        case 'verify':
                            $exist = Paysummaryinfo::whereDate('paid_at', '=', Carbon::parse($paid_ats[$index])->toDateString())->wherePayMethod($pay_methods[$index])->whereProfileId($profile_ids[$index])->first();
                            if (!$exist) {
                                $paysummaryinfo = new Paysummaryinfo();
                                $paysummaryinfo->paid_at = $paid_ats[$index];
                                $paysummaryinfo->pay_method = $pay_methods[$index];
                                $paysummaryinfo->profile_id = $profile_ids[$index];
                            } else {
                                $paysummaryinfo = $exist;
                            }
                            $paysummaryinfo->remark = $remarks[$index];
                            $paysummaryinfo->bankin_date = $bankin_dates[$index] ? Carbon::parse($bankin_dates[$index]) : null;
                            $paysummaryinfo->user_id = Auth::user()->id;
                            $paysummaryinfo->is_verified = 1;
                            $paysummaryinfo->save();
                            break;

                        case 'reject':
                            $exist = Paysummaryinfo::whereDate('paid_at', '=', Carbon::parse($paid_ats[$index])->toDateString())->wherePayMethod($pay_methods[$index])->whereProfileId($profile_ids[$index])->first();
                            if (!$exist) {
                                $paysummaryinfo = new Paysummaryinfo();
                                $paysummaryinfo->paid_at = $paid_ats[$index];
                                $paysummaryinfo->pay_method = $pay_methods[$index];
                                $paysummaryinfo->profile_id = $profile_ids[$index];
                            } else {
                                $paysummaryinfo = $exist;
                            }
                            $paysummaryinfo->remark = $remarks[$index];
                            $paysummaryinfo->bankin_date = $bankin_dates[$index] ? Carbon::parse($bankin_dates[$index]) : null;
                            $paysummaryinfo->user_id = Auth::user()->id;
                            $paysummaryinfo->is_verified = 0;
                            $paysummaryinfo->save();
                            break;                            
                    }

                }
            }
        }
        return redirect('detailrpt/account');
    }

    // verify pay summary single()
    public function verifyAccountPaysummaryApi()
    {
        $bankin_date = request('bankin_date');
        $paid_at = request('paid_at');
        $pay_method = request('pay_method');
        $profile_id = request('profile_id');
        $remark = request('remark');
        $is_verified = request('is_verified');

        if ($bankin_date != '' or $remark != '') {
            $exist = Paysummaryinfo::whereDate('paid_at', '=', Carbon::parse($paid_at)->toDateString())->wherePayMethod($pay_method)->whereProfileId($profile_id)->first();

            if (!$exist) {
                $paysummaryinfo = new Paysummaryinfo();
                $paysummaryinfo->paid_at = $paid_at;
                $paysummaryinfo->pay_method = $pay_method;
                $paysummaryinfo->profile_id = $profile_id;
            } else {
                $paysummaryinfo = $exist;
            }
            $paysummaryinfo->remark = $remark;
            $paysummaryinfo->bankin_date = $bankin_date ? Carbon::parse($bankin_date) : null;
            $paysummaryinfo->user_id = Auth::user()->id;
            $paysummaryinfo->is_verified = $is_verified;
            $paysummaryinfo->save();
        }
    }

    // retrieve customers sales summary api(Formrequest $request)
    public function getSalesCustSummaryApi(Request $request)
    {
        // showing total amount init
        $total_amount = 0;
        $input = $request->all();
        // initiate the page num when null given
        $pageNum = $request->pageNum ? $request->pageNum : 100;

        // indicate the month and year
        $carbondate = Carbon::createFromFormat('d-m-Y', '01-'.$request->current_month);
        $prevMonth = Carbon::createFromFormat('d-m-Y', '01-'.$request->current_month)->subMonth();
        $prev2Months = Carbon::createFromFormat('d-m-Y', '01-'.$request->current_month)->subMonths(2);
        $prevYear = Carbon::createFromFormat('d-m-Y', '01-'.$request->current_month)->subYear();
        $delivery_from = $carbondate->startOfMonth()->toDateString();
        $delivery_to = $carbondate->endOfMonth()->toDateString();
        $profile_id = $request->profile_id;
        $request->merge(array('delivery_from' => $delivery_from));
        $request->merge(array('delivery_to' => $delivery_to));
        $status = $request->status;
        $is_commission = request('is_commission');
        if($status) {
            if($status == 'Delivered') {
                $statusStr = " AND (transactions.status='Delivered' or transactions.status='Verified Owe' or transactions.status='Verified Paid')";
            }else {
                $statusStr = " AND transactions.status='".$status."'";
            }
        }

        if($is_commission != '') {
            $commissionStr = " AND items.is_commission='".$is_commission."' ";
        }

        $thistotal_str = "(SELECT people.id AS person_id, ROUND(SUM(deals.amount), 2) AS thistotal,
                            people.profile_id, custcategories.id AS custcategory_id
                            FROM deals
                            LEFT JOIN items ON items.id=deals.item_id
                            LEFT JOIN transactions ON transactions.id=deals.transaction_id
                            LEFT JOIN people ON transactions.person_id=people.id
                            LEFT JOIN profiles ON people.profile_id=profiles.id
                            LEFT JOIN custcategories ON custcategories.id=people.custcategory_id
                            WHERE transactions.delivery_date>='".$delivery_from."'
                            AND transactions.delivery_date<='".$delivery_to."'";

        $prevtotal_str = "(SELECT people.id AS person_id, ROUND(SUM(deals.amount), 2) AS prevtotal,
                            people.profile_id, custcategories.id AS custcategory_id
                            FROM deals
                            LEFT JOIN items ON items.id=deals.item_id
                            LEFT JOIN transactions ON transactions.id=deals.transaction_id
                            LEFT JOIN people ON transactions.person_id=people.id
                            LEFT JOIN profiles ON people.profile_id=profiles.id
                            LEFT JOIN custcategories ON custcategories.id=people.custcategory_id
                            WHERE transactions.delivery_date>='".$prevMonth->startOfMonth()->toDateString()."'
                            AND transactions.delivery_date<='".$prevMonth->endOfMonth()->toDateString()."'";

        $prev2total_str = "(SELECT people.id AS person_id, ROUND(SUM(deals.amount), 2) AS prev2total,
                            people.profile_id, custcategories.id AS custcategory_id
                            FROM deals
                            LEFT JOIN items ON items.id=deals.item_id
                            LEFT JOIN transactions ON transactions.id=deals.transaction_id
                            LEFT JOIN people ON transactions.person_id=people.id
                            LEFT JOIN profiles ON people.profile_id=profiles.id
                            LEFT JOIN custcategories ON custcategories.id=people.custcategory_id
                            WHERE transactions.delivery_date>='".$prev2Months->startOfMonth()->toDateString()."'
                            AND transactions.delivery_date<='".$prev2Months->endOfMonth()->toDateString()."'";

        $prevyeartotal_str = "(SELECT people.id AS person_id, ROUND(SUM(deals.amount), 2) AS prevyeartotal,
                                people.profile_id, custcategories.id AS custcategory_id
                                FROM deals
                                LEFT JOIN items ON items.id=deals.item_id
                                LEFT JOIN transactions ON transactions.id=deals.transaction_id
                                LEFT JOIN people ON transactions.person_id=people.id
                                LEFT JOIN profiles ON people.profile_id=profiles.id
                                LEFT JOIN custcategories ON custcategories.id=people.custcategory_id
                                WHERE transactions.delivery_date>='".$prevYear->startOfMonth()->toDateString()."'
                                AND transactions.delivery_date<='".$prevYear->endOfMonth()->toDateString()."'";

        if($status) {
            $thistotal_str .= $statusStr;
            $prevtotal_str .= $statusStr;
            $prev2total_str .= $statusStr;
            $prevyeartotal_str .= $statusStr;
        }

        if($is_commission != '') {
            $thistotal_str .= $commissionStr;
            $prevtotal_str .= $commissionStr;
            $prev2total_str .= $commissionStr;
            $prevyeartotal_str .= $commissionStr;
        }

        if(count($profileIds = $this->getUserProfileIdArray()) > 0) {
            $profileIdStr = implode(",", $profileIds);
            $thistotal_str .= " AND profiles.id IN (".$profileIdStr.")";
            $prevtotal_str .= " AND profiles.id IN (".$profileIdStr.")";
            $prev2total_str .= " AND profiles.id IN (".$profileIdStr.")";
            $prevyeartotal_str .= " AND profiles.id IN (".$profileIdStr.")";
        }

        if($profile_id) {
            $thistotal_str .= " GROUP BY profiles.id, custcategories.id) thistotal";
            $prevtotal_str .= " GROUP BY profiles.id, custcategories.id) prevtotal";
            $prev2total_str .= " GROUP BY profiles.id, custcategories.id) prev2total";
            $prevyeartotal_str .= " GROUP BY profiles.id, custcategories.id) prevyeartotal";
        }else  {
            $thistotal_str .= " GROUP BY custcategories.id) thistotal";
            $prevtotal_str .= " GROUP BY custcategories.id) prevtotal";
            $prev2total_str .= " GROUP BY custcategories.id) prev2total";
            $prevyeartotal_str .= " GROUP BY custcategories.id) prevyeartotal";
        }

        $thistotal = DB::raw($thistotal_str);
        $prevtotal = DB::raw($prevtotal_str);
        $prev2total = DB::raw($prev2total_str);
        $prevyeartotal = DB::raw($prevyeartotal_str);

        $transactions = DB::table('deals')
                        ->leftJoin('transactions', 'transactions.id', '=', 'deals.transaction_id')
                        ->leftJoin('items', 'items.id', '=', 'deals.item_id')
                        ->leftJoin('people', 'transactions.person_id', '=', 'people.id')
                        ->leftJoin('profiles', 'people.profile_id', '=', 'profiles.id')
                        ->leftJoin('custcategories', 'custcategories.id', '=', 'people.custcategory_id')
                        ->leftJoin($thistotal, function($join) use ($profile_id) {
                            if($profile_id) {
                                $join->on('thistotal.profile_id', '=', 'profiles.id');
                            }
                            $join->on('thistotal.custcategory_id', '=', 'custcategories.id');
                        })
                        ->leftJoin($prevtotal, function($join) use ($profile_id) {
                            if($profile_id) {
                                $join->on('prevtotal.profile_id', '=', 'profiles.id');
                            }
                            $join->on('prevtotal.custcategory_id', '=', 'custcategories.id');
                        })
                        ->leftJoin($prev2total, function($join) use ($profile_id) {
                            if($profile_id) {
                                $join->on('prev2total.profile_id', '=', 'profiles.id');
                            }
                            $join->on('prev2total.custcategory_id', '=', 'custcategories.id');
                        })
                        ->leftJoin($prevyeartotal, function($join) use ($profile_id) {
                            if($profile_id) {
                                $join->on('prevyeartotal.profile_id', '=', 'profiles.id');
                            }
                            $join->on('prevyeartotal.custcategory_id', '=', 'custcategories.id');
                        })
                        ->select(
                                    'people.cust_id', 'people.company', 'people.name', 'people.id as person_id',
                                    'profiles.name as profile_name', 'profiles.id as profile_id', 'profiles.gst', 'people.gst_rate',
                                    'transactions.id', 'transactions.status', 'transactions.delivery_date', 'transactions.pay_status', 'transactions.delivery_fee', 'transactions.paid_at', 'transactions.created_at',
                                    'custcategories.name AS custcategory', 'custcategories.desc AS custcategory_desc',
                                    'thistotal.thistotal AS thistotal', 'prevtotal.prevtotal AS prevtotal', 'prev2total.prev2total AS prev2total', 'prevyeartotal.prevyeartotal AS prevyeartotal'
                                );

        if($request->id or $request->current_month or $request->cust_id or $request->company or $request->delivery_from or $request->delivery_to or $request->profile_id or $request->id_prefix or $request->custcategory or $request->status){
            $transactions = $this->searchTransactionDBFilter($transactions, $request);
        }

        // add user profile filters
        $transactions = $this->filterUserDbProfile($transactions);

        if($profile_id) {
            $transactions = $transactions->orderBy('custcategories.name')->groupBy('custcategories.id', 'profiles.id');
        }else {
            $transactions = $transactions->orderBy('custcategories.name')->groupBy('custcategories.id');
        }

        if($request->sortName){
            $transactions = $transactions->orderBy($request->sortName, $request->sortBy ? 'asc' : 'desc');
        }

        $total_amount = $this->calTransactionTotalSql($transactions);

        if($pageNum == 'All'){
            $transactions = $transactions->get();
        }else{
            $transactions = $transactions->paginate($pageNum);
        }

        $data = [
            'total_amount' => $total_amount,
            'transactions' => $transactions,
        ];

        return $data;
    }

    // retrieve invoice breakdown detail (Formrequest $request)
    public function getInvoiceBreakdownDetail(Request $request)
    {
        $itemsId = [];
        // $latest3ArrId = [];
        $transactionsId = [];
        $status = $request->status;
        $delivery_from = $request->delivery_from;
        $delivery_to = $request->delivery_to;

        $transactions = Transaction::with(['deals', 'deals.item'])->wherePersonId($request->person_id);
        // $allTransactions = clone $transactions;

        if($status) {
            if($status == 'Delivered') {
                $transactions = $transactions->where(function($query) {
                    $query->where('transactions.status', 'Delivered')->orWhere('transactions.status', 'Verified Owe')->orWhere('transactions.status', 'Verified Paid');
                });
            }else {
                $transactions = $transactions->where('transactions.status', $status);
            }
        }
        // $allTransactions = $allTransactions->latest()->get();

        if($delivery_from){
            $transactions = $transactions->whereDate('transactions.delivery_date', '>=', $delivery_from);
        }
        if($delivery_to){
            $transactions = $transactions->whereDate('transactions.delivery_date', '<=', $delivery_to);
        }

        $transactions = $transactions->orderBy('created_at', 'desc')->get();

        foreach($transactions as $transaction) {
            array_push($transactionsId, $transaction->id);
            foreach($transaction->deals as $deal) {
                array_push($itemsId, $deal->item_id);
            }
        }
        $itemsId = array_unique($itemsId);
        $person_id = $request->person_id ? Person::find($request->person_id)->id : null ;

        if($request->export_excel) {
            $this->exportInvoiceBreakdownExcel($request, $transactionsId, $itemsId, $person_id);
        }

        return view('detailrpt.invbreakdown.detail', compact('request' ,'transactionsId', 'itemsId', 'person_id'));
    }

    // get invoice breakdown page()
    public function getInvoiceBreakdownSummary()
    {
        return view('detailrpt.invbreakdown.summary');
    }

    // retrieve invoice breakdown summary(formrequest request)
    public function getInvoiceBreakdownSummaryApi(Request $request)
    {
        // initiate the page num when null given
        $pageNum = $request->pageNum ? $request->pageNum : 100;
        $delivery_from = $request->delivery_from;
        $delivery_to = $request->delivery_to;
        if($delivery_from and $delivery_to) {
            $date_diff = Carbon::parse($delivery_from)->diffInDays(Carbon::parse($delivery_to)) + 1;
        }else {
            $date_diff = 1;
        }
        if(request('is_commission') != '') {
            $isCommissionStr = " items.is_commission='".request('is_commission')."' ";
        }else {
            $isCommissionStr = " 1=1 ";
        }

        $first_date = DB::raw("(SELECT MIN(DATE(transactions.delivery_date)) AS delivery_date, people.id AS person_id FROM transactions
                                LEFT JOIN people ON people.id=transactions.person_id
                                GROUP BY people.id) AS first_date");
        $sales = DB::raw(
                "(SELECT (MAX(transactions.analog_clock) - MIN(transactions.analog_clock)) AS sales_qty,
                ((MAX(transactions.analog_clock) - MIN(transactions.analog_clock))/ ".$date_diff.") AS sales_avg_day,
                people.id AS person_id,
                transactions.id AS transaction_id
                FROM deals
                LEFT JOIN items ON items.id=deals.item_id
                LEFT JOIN transactions ON transactions.id=deals.transaction_id
                LEFT JOIN people ON people.id=transactions.person_id
                WHERE transactions.delivery_date>='".$delivery_from."'
                AND transactions.delivery_date<='".$delivery_to."'
                AND transactions.is_required_analog=1
                AND ".$isCommissionStr."
                GROUP BY people.id) AS sales"
            );
        $latest_data = DB::raw(
                "(SELECT people.id AS person_id, SUBSTRING_INDEX(GROUP_CONCAT(transactions.balance_coin ORDER BY transactions.created_at DESC), ',' ,1) AS balance_coin, SUBSTRING_INDEX(GROUP_CONCAT(transactions.analog_clock ORDER BY transactions.created_at DESC), ',' ,1) AS analog_clock, transactions.created_at FROM deals
                LEFT JOIN items ON items.id=deals.item_id
                LEFT JOIN transactions ON transactions.id = deals.transaction_id
                LEFT JOIN people ON people.id=transactions.person_id
                WHERE transactions.delivery_date>='".$delivery_from."'
                AND transactions.delivery_date<='".$delivery_to."'
                AND transactions.is_required_analog=1
                AND ".$isCommissionStr."
                GROUP BY people.id) AS latest_data"
            );
        $oldest_data = DB::raw(
                "(SELECT people.id AS person_id, SUBSTRING_INDEX(GROUP_CONCAT(transactions.analog_clock ORDER BY transactions.created_at ASC), ',' ,1) AS analog_clock, transactions.created_at FROM deals
                LEFT JOIN items ON items.id=deals.item_id
                LEFT JOIN transactions ON transactions.id=deals.transaction_id
                LEFT JOIN people ON people.id=transactions.person_id
                WHERE transactions.delivery_date>='".$delivery_from."'
                AND transactions.delivery_date<='".$delivery_to."'
                AND transactions.is_required_analog=1
                AND ".$isCommissionStr."
                GROUP BY people.id) AS oldest_data"
            );
        $total_vending_cash = DB::raw(
                "(SELECT SUM(deals.amount) AS amount, people.id AS person_id FROM deals
                LEFT JOIN transactions ON transactions.id=deals.transaction_id
                LEFT JOIN items ON items.id=deals.item_id
                LEFT JOIN people ON people.id=transactions.person_id
                WHERE items.product_id='051'
                AND transactions.delivery_date>='".$delivery_from."'
                AND transactions.delivery_date<='".$delivery_to."'
                GROUP BY people.id) AS total_vending_cash"
            );
        $total_vending_float = DB::raw(
                "(SELECT SUM(deals.amount) AS amount, people.id AS person_id FROM deals
                LEFT JOIN transactions ON transactions.id=deals.transaction_id
                LEFT JOIN items ON items.id=deals.item_id
                LEFT JOIN people ON people.id=transactions.person_id
                WHERE items.product_id='052'
                AND transactions.delivery_date>='".$delivery_from."'
                AND transactions.delivery_date<='".$delivery_to."'
                GROUP BY people.id) AS total_vending_float"
            );
        $total_stock_value = DB::raw(
                "(SELECT SUM(deals.amount) AS amount, people.id AS person_id FROM deals
                LEFT JOIN transactions ON transactions.id=deals.transaction_id
                LEFT JOIN items ON items.id=deals.item_id
                LEFT JOIN people ON people.id=transactions.person_id
                WHERE items.product_id='051a'
                AND transactions.delivery_date>='".$delivery_from."'
                AND transactions.delivery_date<='".$delivery_to."'
                GROUP BY people.id) AS total_stock_value"
            );


        $deals = DB::table('deals')
                ->leftJoin('items', 'items.id', '=', 'deals.item_id')
                ->leftJoin('transactions', 'transactions.id', '=', 'deals.transaction_id')
                ->leftJoin('people', 'people.id', '=', 'transactions.person_id')
                ->leftJoin('profiles', 'profiles.id', '=', 'people.profile_id')
                ->leftJoin('custcategories', 'custcategories.id', '=', 'people.custcategory_id')
                ->leftJoin($first_date, 'people.id', '=', 'first_date.person_id')
                ->leftJoin($sales, 'people.id', '=', 'sales.person_id')
                ->leftjoin($latest_data, 'people.id', '=', 'latest_data.person_id')
                ->leftjoin($oldest_data, 'people.id', '=', 'oldest_data.person_id')
                ->leftjoin($total_vending_cash, 'people.id', '=', 'total_vending_cash.person_id')
                ->leftjoin($total_vending_float, 'people.id', '=', 'total_vending_float.person_id')
                ->leftjoin($total_stock_value, 'people.id', '=', 'total_stock_value.person_id')
                ->select(
                    'items.is_commission', 'items.is_inventory',
                    'people.cust_id AS cust_id', 'people.company AS company',
                    'custcategories.name AS custcategory_name',
                    'first_date.delivery_date AS first_date',
                    DB::raw('ROUND(CASE WHEN profiles.gst=1 THEN (CASE WHEN people.is_gst_inclusive=0 THEN ROUND((SUM(deals.amount)/((100+people.gst_rate)/100)), 2) ELSE SUM(deals.amount) END) ELSE (SUM(deals.amount)) END, 2) AS total'),
                    'profiles.gst AS gst',
                    DB::raw('(CASE WHEN profiles.gst=1 THEN (CASE WHEN people.is_gst_inclusive=0 THEN ROUND(SUM(deals.amount) * (people.gst_rate/100), 2) ELSE (SUM(deals.amount) - SUM(deals.amount)/((100+people.gst_rate)/100) ) END) ELSE NULL END) AS gsttotal'),
                    DB::raw('ROUND(CASE WHEN profiles.gst=1 THEN (CASE WHEN people.is_gst_inclusive=0 THEN ROUND(SUM(deals.amount), 2) ELSE SUM(deals.amount) - (SUM(deals.amount) - SUM(deals.amount)/((100+people.gst_rate)/100)) END) ELSE (SUM(deals.amount)) END, 2) AS subtotal'),
                    DB::raw('ROUND(SUM(deals.unit_cost * deals.qty), 2) AS cost'),
                    DB::raw('(SUM(deals.amount) - ROUND(SUM(deals.unit_cost * deals.qty), 2)) AS gross_money'),
                    DB::raw('ROUND(CASE WHEN SUM(deals.amount)>0 THEN ((SUM(deals.amount) - ROUND(SUM(deals.unit_cost * deals.qty), 2))/ SUM(deals.amount) * 100) ELSE (SUM(deals.amount) - ROUND(SUM(deals.unit_cost * deals.qty), 2)) END, 2) AS gross_percent'),
                    DB::raw('ROUND(CASE WHEN transactions.pay_status="Paid" THEN SUM(deals.amount) END, 2) AS paid'),
                    DB::raw('ROUND(CASE WHEN transactions.pay_status="Owe" THEN SUM(deals.amount) END, 2) AS owe'),
                    'people.is_vending', 'people.vending_piece_price', 'people.vending_monthly_rental', 'people.vending_profit_sharing',
                    'sales.sales_qty AS sales_qty', 'sales.sales_avg_day AS sales_avg_day',
                    DB::raw('ROUND(((COALESCE(latest_data.balance_coin, 0) + COALESCE(total_vending_cash.amount, 0) + COALESCE(total_vending_float.amount, 0))-((COALESCE(latest_data.analog_clock, 0) - COALESCE(oldest_data.analog_clock, 0)) * COALESCE(people.vending_piece_price, 0))), 2) AS difference'),
                    DB::raw('ROUND((COALESCE(total_stock_value.amount, 0) + COALESCE(latest_data.balance_coin, 0)) + (COALESCE(total_vending_cash.amount, 0) + COALESCE(total_vending_float.amount, 0)), 2) AS vm_stock_value')
                );

        if($request->profile_id or $request->delivery_from or $request->delivery_to or $request->status or $request->cust_id or $request->company or $request->person_id or $request->custcategory or request('is_commission')) {
            $deals = $this->invoiceBreakdownSummaryFilter($request, $deals);
        }

        // add user profile filters
        $deals = $this->filterUserDbProfile($deals);

        $deals = $deals->groupBy('people.id');

        if($request->sortName){
            $deals = $deals->orderBy($request->sortName, $request->sortBy ? 'asc' : 'desc');
        }else {
            $deals = $deals->orderBy('cust_id');
        }

        $fixedtotals = $this->calInvbreakdownSummaryFixedTotals($deals);

        if($pageNum == 'All'){
            $deals = $deals->get();
        }else{
            $deals = $deals->paginate($pageNum);
        }

        $dynamictotals = $this->calInvbreakdownSummaryDynamicTotals($deals);

        $data = [
            'deals' => $deals,
            'fixedtotals' => $fixedtotals,
            'dynamictotals' => $dynamictotals
        ];

        return $data;
    }

    // show the total this month sales product detail month(int $item_id)
    public function getProductDetailMonthThisMonth($item_id)
    {
        $item = Item::findOrFail($item_id);

        return view('detailrpt.sales.thismonth_total', compact('item', 'request'));
    }

    // show the total this month sales product detail month(int $item_id)
    public function getProductDetailMonthThisMonthApi($item_id)
    {
        // dd(request()->all(), $item_id);
        $item = Item::findOrFail($item_id);
        $current_from = request('current_from');
        $current_to = request('current_to');

        $transactions = Transaction::with(['person', 'person.profile', 'person.custcategory'])
                        ->whereHas('deals', function($query) use ($item_id) {
                            $query->whereItemId($item_id);
                        })
                        ->where('delivery_date', '>=', $current_from)
                        ->where('delivery_date', '<=', $current_to)
                        ->whereHas('person.profile', function($query) {
                            $query->filterUserProfile();
                        });

        if(request('sortName')){
            $transactions = $transactions->orderBy(request('sortName'), request('sortBy') ? 'asc' : 'desc');
        }

        $transactions = $transactions->latest()->get();

        $data = [
            'transactions' => $transactions,
            'item' => $item
        ];

        return $data;
    }

    // return stock per customer page()
    public function getStockPerCustomer()
    {
        $peopleIdArr = [];
        $peopleIdAllArr = [];
        $dealsIdArr = [];
        $transactionsIdArr = [];
        $transaction_order = DB::raw('(SELECT transactions.created_at, transactions.id FROM deals
                                        LEFT JOIN transactions ON transactions.id=deals.transaction_id
                                        GROUP BY transactions.id) AS transaction_order');
        $deals = DB::table('deals')
                    ->leftJoin('items', 'items.id', '=', 'deals.item_id')
                    ->leftJoin('transactions', 'transactions.id', '=', 'deals.transaction_id')
                    ->leftJoin('people', 'people.id', '=', 'transactions.person_id')
                    ->leftJoin('profiles', 'profiles.id', '=', 'people.profile_id')
                    ->leftJoin('custcategories', 'custcategories.id', '=', 'people.custcategory_id')
                    ->leftJoin($transaction_order, 'transaction_order.id', '=', 'transactions.id')
                    ->select(
                        'deals.qty', 'deals.amount', 'deals.id AS deal_id',
                        'items.is_inventory', 'items.id AS item_id', 'items.unit',
                        'transactions.id', 'transactions.status AS status', 'transactions.delivery_date',
                        'people.id AS person_id', 'people.cust_id', 'people.company',
                        'profiles.id AS profile_id', 'profiles.name AS profile_name',
                        'custcategories.id AS custcategory_id', 'custcategories.name AS custcategory_name',
                        'transaction_order.created_at AS created_at'
                    );

        $deals = $this->detailrptStockFilters(request(), $deals);

        $deals = $deals->where(function($query) {
                        $query->where('transactions.status', 'Delivered')
                                ->orWhere('transactions.status', 'Verified Owe')
                                ->orWhere('transactions.status', 'Verified Paid');
                    });

        // add user profile filters
        $deals = $this->filterUserDbProfile($deals);

        $transactions = clone $deals;
        $itemsPeople = clone $deals;

        $transactions = $transactions
                        ->groupBy('transactions.id')
                        ->latest()
                        ->get();
        $itemsPeople = $itemsPeople
                        ->get();

        foreach($transactions as $transaction) {
            array_push($transactionsIdArr, $transaction->id);
            if(! in_array($transaction->person_id, $peopleIdAllArr)) {
                array_push($peopleIdAllArr, $transaction->person_id);
            }
        }
        foreach($itemsPeople as $deal) {
            array_push($dealsIdArr, $deal->deal_id);
        }
        for($x=0; $x<5; $x++) {
            array_push($peopleIdArr, $peopleIdAllArr[$x]);
        }
        $request = request();

        $items = Item::whereNotNull('created_at');
        $is_inventory = request('is_inventory') === 1 ? 1 : null;
        if(request()->isMethod('get')) {
            $is_inventory = 1;
        }
        if($is_inventory) {
            $items = $items->where('is_inventory', $is_inventory);
        }
        $items = $items->orderBy('product_id')->get();

        if(request('export_excel')) {
            $this->exportStockPerCustomerExcel($request, $peopleIdAllArr, $dealsIdArr, $items, $transactionsIdArr);
        }

        return view('detailrpt.stock.customer', compact('peopleIdArr', 'dealsIdArr', 'items', 'request'));
    }

    // get invoice breakdown page()
    public function getStockBilling()
    {
        return view('detailrpt.stock.billing');
    }

    // return stock billing api()
    public function getStockBillingApi()
    {
        // initiate the page num when null given
        $pageNum = request('pageNum') ? request('pageNum') : 100;

        $deals = $this->stockBillingSql()['deals'];

        $totals = $this->stockBillingSql()['totals'];
/*
        if(request('consolidate_rpt')) {
            $this->exportConsolidateRpt($deals, $totals, request('bill_profile'));
        }*/

        if($pageNum == 'All'){
            $deals = $deals->get();
        }else{
            $deals = $deals->paginate($pageNum);
        }

        $data = [
            'deals' => $deals,
            'totals' => $totals
        ];

        return $data;
    }

    // return stock date api()
    public function getStockDate()
    {
        $itemsIdArr = [];
        $sevenDateTransactionIds = [];
        $allDateTransactionIds = [];
        $allDatesArr = [];
        $sevenDatesArr = [];

        $transaction_order = DB::raw('(SELECT transactions.created_at, transactions.id FROM deals
                                        LEFT JOIN transactions ON transactions.id=deals.transaction_id
                                        GROUP BY transactions.id) AS transaction_order');
        $deals = DB::table('deals')
                    ->leftJoin('items', 'items.id', '=', 'deals.item_id')
                    ->leftJoin('transactions', 'transactions.id', '=', 'deals.transaction_id')
                    ->leftJoin('people', 'people.id', '=', 'transactions.person_id')
                    ->leftJoin('profiles', 'profiles.id', '=', 'people.profile_id')
                    ->leftJoin('custcategories', 'custcategories.id', '=', 'people.custcategory_id')
                    ->leftJoin($transaction_order, 'transaction_order.id', '=', 'transactions.id')
                    ->select(
                        'deals.qty', 'deals.amount', 'deals.id AS deal_id',
                        'items.is_inventory', 'items.id AS item_id', 'items.unit',
                        'transactions.id', 'transactions.status AS status', 'transactions.delivery_date',
                        'people.id AS person_id', 'people.cust_id', 'people.company',
                        'profiles.id AS profile_id', 'profiles.name AS profile_name',
                        'custcategories.id AS custcategory_id', 'custcategories.name AS custcategory_name',
                        'transaction_order.created_at AS created_at'
                    );

        $deals = $deals->where(function($query) {
                        $query->where('transactions.status', 'Delivered')
                                ->orWhere('transactions.status', 'Verified Owe')
                                ->orWhere('transactions.status', 'Verified Paid');
                    });

        $deals = $this->detailrptStockFilters(request(), $deals);

        // add user profile filters
        $deals = $this->filterUserDbProfile($deals);

        $transactions = clone $deals;
        $transactionDates = clone $deals;
        $sevenTransactionDateId = clone $deals;

        $transactions = $transactions
                        ->groupBy('transactions.id')
                        ->latest('transactions.delivery_date')
                        ->get();

        $transactionDates = $transactionDates
                            ->groupBy('transactions.delivery_date')
                            ->latest('transactions.delivery_date')
                            ->get();

        foreach($transactionDates as $transactionDate) {
            array_push($allDatesArr, $transactionDate->delivery_date);
        }

        if(count($transactionDates) < 7) {
            $endcounter = count($transactionDates);
        }else {
            $endcounter = 7;
        }
        for($x=0; $x<$endcounter; $x++) {
            array_push($sevenDatesArr, $transactionDates[$x]);
        }

        if(count($sevenDatesArr) > 0) {
            $sevenTransactionDateId = $sevenTransactionDateId->whereDate('delivery_date', '>=', $sevenDatesArr[count($sevenDatesArr) - 1]->delivery_date)->whereDate('delivery_date', '<=', $sevenDatesArr[0]->delivery_date)->get();
        }else {
            $sevenTransactionDateId = [];
        }

        $items = Item::whereNotNull('created_at');

        if(request()->isMethod('post') and request('is_inventory') !== 'All') {
            $items = $items->where('is_inventory', request('is_inventory'));
        }else if(request()->isMethod('get')) {
            $items = $items->where('is_inventory', 1);
        }

        $items = $items->get();

        foreach($items as $item) {
            array_push($itemsIdArr, $item->id);
        }

        foreach($transactions as $transaction) {
            array_push($allDateTransactionIds, $transaction->id);
        }

        foreach($sevenTransactionDateId as $sevenDateId) {
            array_push($sevenDateTransactionIds, $sevenDateId->id);
        }

        if(request('export_excel')) {
            $this->exportStockDateExcel(request(), $allDatesArr, $itemsIdArr, $allDateTransactionIds);
        }

        return view('detailrpt.stock.date', compact('sevenDatesArr', 'itemsIdArr', 'allDateTransactionIds', 'sevenDateTransactionIds'));
    }

    // export pdf for issue bill to another profile in billing (Query $query, Array $totals, int $bill_profile)
    public function exportBillingPdf()
    {
        if(request('issue_bill')) {
            $type = request('issue_bill');
        }else if(request('consolidate_rpt')) {
            $type = request('consolidate_rpt');
        }
        $deals = $this->stockBillingSql()['deals'];
        $deals = $deals->get();
        $totals = $this->stockBillingSql()['totals'];
        // $running_no = Carbon::today()->format('ymd');
        $delivery_from = request('delivery_from');
        $delivery_to = request('delivery_to');

        if($type = request('exportpdf')) {
            switch($type) {
                case 'bill':
                    $profile = Profile::find(request('profile_id'));
                    $issuebillprofile = Profile::find(request('bill_profile'));
                    $running_no = GeneralSetting::firstOrFail()->internal_billing_prefix.Carbon::parse($delivery_to)->format('ymd').'-'.$profile->acronym;
                    $name = 'Internal_Billing('.$running_no.')_'.$issuebillprofile->name.'-'.$profile->name.'.pdf';
                    break;
                case 'consolidate':
                    $profile = Profile::find(request('profile_id'));
                    $issuebillprofile = Profile::find(request('profile_id'));
                    $running_no = $issuebillprofile->acronym.Carbon::parse($delivery_to)->format('ymd');
                    $name = 'Consolidate_Rpt('.$running_no.')_'.$issuebillprofile->name.'.pdf';
                    break;
            }
        }

        $data = [
            'deals' => $deals,
            'profile' => $profile,
            'issuebillprofile' => $issuebillprofile,
            'running_no' => $running_no,
            'type' => $type,
            'delivery_from' => $delivery_from,
            'delivery_to' => $delivery_to,
            'totals' => $totals,
        ];

        $pdf = PDF::loadView('detailrpt.stock.pdf.internalpdf', $data);
        $pdf->setPaper('a4');
        $pdf->setOption('margin-top', 5);
        $pdf->setOption('margin-bottom', 5);
        $pdf->setOption('margin-left', 2);
        $pdf->setOption('margin-right', 2);
        $pdf->setOption('footer-right', 'Page [page]/[topage]');
        // $pdf->setOption('disable-smart-shrinking', true);
        $pdf->setOption('dpi', 70);
        $pdf->setOption('page-width', '210mm');
        $pdf->setOption('page-height', '297mm');
        return $pdf->download($name);
    }

    // filters function for stock (formrequest request(), query deals)
    private function detailrptStockFilters($request, $deals)
    {
        $profile_id = request('profile_id');
        $delivery_from = request('delivery_from');
        $delivery_to = request('delivery_to');
        $stock_status = request('stock_status');
        $cust_id = request('cust_id');
        $company = request('company');
        $person_id = request('person_id');
        $custcategory_id = request('custcategory_id');
        $is_inventory = request('is_inventory');

        if(request()->isMethod('get')) {
            $delivery_from = Carbon::today()->startOfMonth()->toDateString();
            $delivery_to = Carbon::today()->toDateString();
            $stock_status = 'Sold';
            $is_inventory = 1;
        }

        if($profile_id) {
            $deals = $deals->where('profiles.id', $profile_id);
        }
        if($delivery_from) {
            $deals = $deals->whereDate('transactions.delivery_date', '>=', $delivery_from);
        }
        if($delivery_to) {
            $deals = $deals->whereDate('transactions.delivery_date', '<=', $delivery_to);
        }
        if($cust_id) {
            $deals = $deals->where('people.cust_id', 'LIKE', '%'.$cust_id.'%');
        }
        if($company) {
            $deals = $deals->where('people.company', 'LIKE', '%'.$company.'%');
        }
        if($person_id) {
            $deals = $deals->where('people.id', $person_id);
        }
        if($custcategory_id) {
            $deals = $deals->where('custcategories.id', $custcategory_id);
        }
        if($is_inventory !== 'All') {
            $deals = $deals->where('items.is_inventory', $is_inventory);
        }

        return $deals;
    }

    // stock billing filters(formrequest request, query deals)
    private function stockBillingFilters($request, $deals)
    {
        $profile_id = request('profile_id');
        $delivery_from = request('delivery_from');
        $delivery_to = request('delivery_to');
        $cust_id = request('cust_id');
        $company = request('company');
        $person_id = request('person_id');
        $custcategory_id = request('custcategory_id');
        $is_inventory = request('is_inventory');
        $is_commission = request('is_commission');

        if($profile_id) {
            $deals = $deals->where('profiles.id', $profile_id);
        }
        if($delivery_from) {
            $deals = $deals->whereDate('transactions.delivery_date', '>=', $delivery_from);
        }
        if($delivery_to) {
            $deals = $deals->whereDate('transactions.delivery_date', '<=', $delivery_to);
        }
        if($cust_id) {
            $deals = $deals->where('people.cust_id', 'LIKE', '%'.$cust_id.'%');
        }
        if($company) {
            $deals = $deals->where('people.company', 'LIKE', '%'.$company.'%');
        }
        if($person_id) {
            $deals = $deals->where('people.id', $person_id);
        }
        if($custcategory_id) {
            $deals = $deals->where('custcategories.id', $custcategory_id);
        }
        if($is_inventory) {
            $deals = $deals->where('items.is_inventory', $is_inventory);
        }
        if($is_commission != '') {
            $deals = $deals->where('items.is_commission', $is_commission);
        }
        return $deals;
    }

    // calculate stock billing totals(query $deals)
    private function calStockBillingTotals($deals)
    {
        $total_qty = 0;
        $total_costs = 0;
        $total_sell_value = 0;
        $total_gross_profit = 0;
        $calculateDeals = clone $deals;
        foreach($calculateDeals->get() as $deal) {
            $total_qty += $deal->qty;
            $total_costs += $deal->total_cost;
            $total_sell_value += $deal->amount;
            $total_gross_profit += $deal->gross;
        }

        $totals = [
            'total_qty' => $total_qty,
            'total_costs' => $total_costs,
            'total_sell_value' => $total_sell_value,
            'total_gross_profit' => $total_gross_profit,
        ];
        return $totals;
    }

    // export stock per customer excel(formrequest request, array peopleIdAllArr, array dealsIdArr, collection items, array transactionsIdArr)
    private function exportStockPerCustomerExcel($request, $peopleIdAllArr, $dealsIdArr, $items, $transactionsIdArr)
    {
        $title = 'Stock Per Customer';
        Excel::create($title.'_'.Carbon::now()->format('dmYHis'), function($excel) use ($request, $peopleIdAllArr, $dealsIdArr, $items, $transactionsIdArr) {
            $excel->sheet('sheet1', function($sheet) use ($request, $peopleIdAllArr, $dealsIdArr, $items, $transactionsIdArr) {
                $sheet->setColumnFormat(array('A:P' => '@'));
                $sheet->getPageSetup()->setPaperSize('A4');
                $sheet->setAutoSize(true);
                $sheet->loadView('detailrpt.stockpercustomer_excel', compact('request', 'peopleIdAllArr', 'dealsIdArr', 'items', 'transactionsIdArr'));
            });
        })->download('xlsx');
    }

    // filter functions for invoice breakdown summary (formrequest request, query deals)
    private function invoiceBreakdownSummaryFilter($request, $deals)
    {
        $profile_id = $request->profile_id;
        $delivery_from = $request->delivery_from;
        $delivery_to = $request->delivery_to;
        $status = $request->status;
        $cust_id = $request->cust_id;
        $company = $request->company;
        $person_id = $request->person_id;
        $custcategory = $request->custcategory;
        $is_commission = request('is_commission');

        if($profile_id) {
            $deals = $deals->where('profiles.id', $profile_id);
        }
        if($delivery_from) {
            $deals = $deals->whereDate('transactions.delivery_date', '>=', $delivery_from);
        }else {
            $deals = $deals->whereDate('transactions.delivery_date', '>=', Carbon::today()->startOfMonth()->toDateString());
        }
        if($delivery_to) {
            $deals = $deals->whereDate('transactions.delivery_date', '<=', $delivery_to);
        }else {
            $deals = $deals->whereDate('transactions.delivery_date', '<=', Carbon::today()->toDateString());
        }
        if($status) {
            if($status === 'Delivered' ) {
                $deals = $deals->where(function($query) {
                    $query->where('transactions.status', 'Delivered')->orWhere('transactions.status', 'Verified Owe')->orWhere('transactions.status', 'Verified Paid');
                });
            }else {
                $deals = $deals->where('transactions.status', $status);
            }
        }
        if($cust_id) {
            $deals = $deals->where('people.cust_id', 'LIKE', '%'.$cust_id.'%');
        }
        if($company) {
            $deals = $deals->where('people.company', 'LIKE', '%'.$company.'%');
        }
        if($person_id) {
            $deals = $deals->where('people.id', '=', $person_id);
        }
        if($custcategory) {
            $deals = $deals->where('custcategories.id', $custcategory);
        }
        if($is_commission != '') {
            $deals = $deals->where('items.is_commission', $is_commission);
        }
        return $deals;
    }

    // calculate totals for the invoice breakdown summary(collection $deals)
    private function calInvbreakdownSummaryFixedTotals($deals)
    {
        $grand_total = 0;
        $taxtotal = 0;
        $subtotal = 0;
        $total_gross_money = 0;
        $total_gross_percent = 0;

        foreach($deals->get() as $deal) {
            $grand_total += $deal->total;
            if($deal->gst) {
                $taxtotal += $deal->gsttotal;
            }
            $subtotal += $deal->subtotal;
            $total_gross_money += $deal->gross_money;
            $total_gross_percent += $deal->gross_percent;
        }

        $totals = [
            'grand_total' => $grand_total,
            'taxtotal' => $taxtotal,
            'subtotal' => $subtotal,
            'total_gross_money' => $total_gross_money,
            'total_gross_percent' => $total_gross_percent
        ];

        return $totals;
    }

    // calculate dynamic average and totals for invoice breakdown summary(collection $deals)
    private function calInvbreakdownSummaryDynamicTotals($deals)
    {
        $avg_grand_total = 0;
        $avg_subtotal = 0;
        $avg_cost = 0;
        $avg_gross_money = 0;
        $avg_gross_percent = 0;
        $avg_vending_piece_price = 0;
        $avg_vending_monthly_rental = 0;
        $avg_sales_qty = 0;
        $avg_sales_avg_day = 0;
        $avg_difference = 0;
        $avg_vm_stock_value = 0;

        $total_grand_total = 0;
        $total_gsttotal = 0;
        $total_subtotal = 0;
        $total_cost = 0;
        $total_gross_money = 0;
        $total_gross_percent = 0;
        $total_owe = 0;
        $total_paid = 0;
        $total_vending_monthly_rental = 0;
        $total_sales_qty = 0;
        $total_difference = 0;
        $total_vm_stock_value = 0;
        // placeholder
        $total_vending_piece_price = 0;
        $total_sales_avg_day = 0;

        $dealscount = count($deals);

        foreach($deals as $deal) {
            $total_grand_total += $deal->total;
            $total_subtotal += $deal->subtotal;
            if($deal->gst) {
                $total_gsttotal += $deal->gsttotal;
            }
            $total_cost += $deal->cost;
            $total_gross_money += $deal->gross_money;
            $total_gross_percent += $deal->gross_percent;
            $total_owe += $deal->owe;
            $total_paid += $deal->paid;
            $total_vending_monthly_rental += $deal->vending_monthly_rental;
            $total_sales_qty += $deal->sales_qty;
            $total_difference += $deal->difference;
            $total_vm_stock_value += $deal->vm_stock_value;
            $total_vending_piece_price += $deal->vending_piece_price;
            $total_sales_avg_day += $deal->sales_avg_day;
        }

        if($dealscount > 0) {
            $avg_grand_total = $total_grand_total / $dealscount;
            $avg_subtotal = $total_subtotal / $dealscount;
            $avg_cost = $total_cost / $dealscount;
            $avg_gross_money = $total_gross_money / $dealscount;
            $avg_gross_percent = $total_gross_percent / $dealscount;
            $avg_vending_piece_price = $total_vending_piece_price / $dealscount;
            $avg_vending_monthly_rental = $total_vending_monthly_rental / $dealscount;
            $avg_sales_qty = $total_sales_qty / $dealscount;
            $avg_sales_avg_day = $total_sales_avg_day / $dealscount;
            $avg_difference = $total_difference / $dealscount;
            $avg_vm_stock_value = $total_vm_stock_value / $dealscount;
        }

        $totals = [
            'avg_grand_total' => $avg_grand_total,
            'avg_subtotal' => $avg_subtotal,
            'avg_cost' => $avg_cost,
            'avg_gross_money' => $avg_gross_money,
            'avg_gross_percent' => $avg_gross_percent,
            'avg_vending_piece_price' => $avg_vending_piece_price,
            'avg_vending_monthly_rental' => $avg_vending_monthly_rental,
            'avg_sales_qty' => $avg_sales_qty,
            'avg_sales_avg_day' => $avg_sales_avg_day,
            'avg_difference' => $avg_difference,
            'avg_vm_stock_value' => $avg_vm_stock_value,

            'total_grand_total' => $total_grand_total,
            'total_subtotal' => $total_subtotal,
            'total_gsttotal' => $total_gsttotal,
            'total_cost' => $total_cost,
            'total_gross_money' => $total_gross_money,
            'total_gross_percent' => $total_gross_percent,
            'total_owe' => $total_owe,
            'total_paid' => $total_paid,
            'total_vending_monthly_rental' => $total_vending_monthly_rental,
            'total_sales_qty' => $total_sales_qty,
            'total_difference' => $total_difference,
            'total_vm_stock_value' => $total_vm_stock_value
        ];

        return $totals;
    }

    // export SOA report(Array $data)
    private function convertSoaExcel($transactions, $total)
    {
        $soa_query = clone $transactions;
        $data = $soa_query->orderBy('people.cust_id')->orderBy('transactions.id')->groupBy('transactions.id')->get();
        $title = 'Account SOA';

        Excel::create($title.'_'.Carbon::now()->format('dmYHis'), function($excel) use ($data, $total) {
            $excel->sheet('sheet1', function($sheet) use ($data, $total) {
                $sheet->setAutoSize(true);
                $sheet->setColumnFormat(array(
                    'A:E' => '@',
                    'F' => '0.00'
                ));
                $sheet->loadView('detailrpt.account.custdetail_soa_excel', compact('data', 'total'));
            });
        })->download('xls');
    }

    // export account cust detail excel(Collection $transactions, float $total)
    private function convertAccountCustdetailExcel($transactions, $total)
    {
        $data = $transactions;
        $title = 'Cust Detail (Account)';
        Excel::create($title.'_'.Carbon::now()->format('dmYHis'), function($excel) use ($data, $total) {
            $excel->sheet('sheet1', function($sheet) use ($data, $total) {
                $sheet->setAutoSize(true);
                $sheet->setColumnFormat(array(
                    'A:F' => '@',
                    'G' => '0.00'
                ));
                $sheet->loadView('detailrpt.account.custdetail_excel', compact('data', 'total'));
            });
        })->download('xls');
    }

    // conditional filter parser(Collection $query, Formrequest $request)
    private function searchTransactionDBFilter($transactions, $request)
    {
        $profile_id = $request->profile_id;
        $delivery_from = $request->delivery_from;
        $payment_from = $request->payment_from;
        $cust_id = $request->cust_id;
        $delivery_to = $request->delivery_to;
        $payment_to = $request->payment_to;
        $company = $request->company;
        $status = $request->status;
        $person_id = $request->person_id;
        $payment = $request->payment;
        $pay_method = $request->pay_method;
        $id_prefix = $request->id_prefix;
        $custcategory = $request->custcategory;
        $bankin_from = $request->bankin_from;
        $bankin_to = $request->bankin_to;
        $franchisee_id = $request->franchisee_id;

        if($profile_id){
            $transactions = $transactions->where('profiles.id', $profile_id);
        }
        if($delivery_from){
            $transactions = $transactions->whereDate('transactions.delivery_date', '>=', $delivery_from);
        }
        if($payment_from){
            $transactions = $transactions->whereDate('transactions.paid_at', '>=', $payment_from);
        }
        if($cust_id){
            $transactions = $transactions->where('people.cust_id', 'LIKE', '%'.$cust_id.'%');
        }
        if($delivery_to){
            $transactions = $transactions->whereDate('transactions.delivery_date', '<=', $delivery_to);
        }
        if($payment_to){
            $transactions = $transactions->whereDate('transactions.paid_at', '<=', $payment_to);
        }
        if($status) {
            if($status == 'Delivered') {
                $transactions = $transactions->where(function($query) {
                    $query->where('transactions.status', 'Delivered')->orWhere('transactions.status', 'Verified Owe')->orWhere('transactions.status', 'Verified Paid');
                });
            }else {
                $transactions = $transactions->where('transactions.status', $status);
            }
        }
        if($company) {
            $transactions = $transactions->where(function($query) use ($company){
                $query->where('people.company', 'LIKE', '%'.$company.'%')
                        ->orWhere(function ($query) use ($company){
                            $query->where('people.cust_id', 'LIKE', 'D%')
                                    ->where('people.name', 'LIKE', '%'.$company.'%');
                        });
                });
        }
        if($person_id) {
            $transactions = $transactions->where('people.id', $person_id);
        }
        if($payment) {
            $transactions = $transactions->where('transactions.pay_status', $payment);
        }
        if($pay_method) {
            $transactions = $transactions->where('transactions.pay_method', $pay_method);
        }
        if($id_prefix) {
            $transactions = $transactions->where('people.cust_id', 'LIKE', $id_prefix.'%');
        }
/*         if($custcategory) {
            $transactions = $transactions->where('custcategories.id', $custcategory);
        } */
        if ($custcategory) {
            if (count($custcategory) == 1) {
                $custcategory = [$custcategory];
            }
            $transactions = $transactions->whereIn('custcategories.id', $custcategory);
        }        
        if($bankin_from) {
            $transactions = $transactions->whereDate('paysummaryinfos.bankin_date', '>=', $bankin_from);
        }
        if($bankin_to) {
            $transactions = $transactions->whereDate('paysummaryinfos.bankin_date', '<=', $bankin_to);
        }
        if($franchisee_id) {
            $transactions = $transactions->where('people.franchisee_id', $franchisee_id);
        }
        if($request->sortName){
            $transactions = $transactions->orderBy($request->sortName, $request->sortBy ? 'asc' : 'desc');
        }

        return $transactions;
    }

    // calculating gst and non for delivered total
    private function calDBTransactionTotal($query)
    {
        $total_amount = 0;
        $nonGst_amount = 0;
        $gst_amount = 0;
        $query1 = clone $query;
        $query2 = clone $query;

        $nonGst_amount = $query1->where('profiles.gst', 0)->sum(DB::raw('ROUND(transactions.total, 2)'));
        $gst_amount = $query2->where('profiles.gst', 1)->sum(DB::raw('ROUND((transactions.total * (100+people.gst_rate)/100), 2)'));

        $total_amount = $nonGst_amount + $gst_amount;

        return $total_amount;
    }

    // calculate original total
    private function calDBOriginalTotal($query)
    {
        $total_amount = 0;
        $transactionsIdArr = [];
        $query1 = clone $query;
        $transactions = $query1->groupBy('transactions.id')->get();
        foreach($transactions as $transaction) {
            array_push($transactionsIdArr, $transaction->id);
        }

        $total_amount = DB::table('transactions')
                            ->leftJoin('people', 'people.id', '=', 'transactions.person_id')
                            ->leftJoin('profiles', 'profiles.id', '=', 'people.profile_id')
                            ->whereIn('transactions.id', $transactionsIdArr)
                            ->sum(
                                    DB::raw('ROUND((CASE WHEN profiles.gst=1 THEN (
                                                CASE
                                                WHEN people.is_gst_inclusive=0
                                                THEN total*((100+people.gst_rate)/100)
                                                ELSE transactions.total
                                                END) ELSE transactions.total END) + (CASE WHEN transactions.delivery_fee>0 THEN transactions.delivery_fee ELSE 0 END), 2)')
                                );

        return $total_amount;
    }

    // calculate delivery fees total
    private function calDBDeliveryTotal($query)
    {
        $query3 = clone $query;
        $delivery_fee = $query3->sum(DB::raw('ROUND(transactions.delivery_fee, 2)'));
        return $delivery_fee;
    }

    // calculate total when sql done the filter job
    private function calTransactionTotalSql($query)
    {
        $total_amount = 0;
        $query1 = clone $query;
        $totals = $query1->get();
        foreach($totals as $total) {
            $total_amount += $total->thistotal;
        }
        return $total_amount;
    }

    // calculate account cust outstanding total
    private function calCustoutstandingTotal($query)
    {
        $total_amount = 0;
        $query1 = clone $query;
        $totals = $query1->get();
        foreach($totals as $total) {
            $total_amount += $total->thistotal;
        }
        return $total_amount;
    }

    // cal independent total for inv_total gst and amount
    private function calPayDetailTotal($query)
    {
        $query1 = clone $query;
        $query2 = clone $query;
        $query3 = clone $query;

        $total_inv_amount = $query1->sum(DB::raw('(CASE WHEN profiles.gst=1 THEN (CASE WHEN people.is_gst_inclusive=1 THEN (transactions.total - (transactions.total - transactions.total/((100+people.gst_rate)/100))) ELSE transactions.total END) ELSE transactions.total END) + (CASE WHEN transactions.delivery_fee>0 THEN transactions.delivery_fee ELSE 0 END)'));
        $total_gst = $query2->sum(DB::raw('(CASE WHEN profiles.gst=1 THEN (CASE WHEN people.is_gst_inclusive=1 THEN (transactions.total - transactions.total/((100+people.gst_rate)/100)) ELSE transactions.total * (people.gst_rate)/100 END) ELSE null END)'));
        $total_amount = $query3->sum(DB::raw('(CASE WHEN profiles.gst=1 THEN (CASE WHEN people.is_gst_inclusive=0 THEN (transactions.total * (100+people.gst_rate)/100) ELSE transactions.total END) ELSE transactions.total END) + (CASE WHEN transactions.delivery_fee>0 THEN transactions.delivery_fee ELSE 0 END)'));

        $caldata = [
            'total_inv_amount' => $total_inv_amount,
            'total_gst' => $total_gst,
            'total_amount' => $total_amount,
        ];

        return $caldata;
    }

    // conditional filter items parser(Collection $query, Formrequest $request)
    private function searchItemDBFilter($items, $request)
    {
        $product_id = $request->product_id;
        $product_name = $request->product_name;
        $profile_id = $request->profile_id;
        $status = $request->status;

        if($product_id) {
            $items = $items->where('items.product_id', 'LIKE', '%'.$product_id.'%');
        }
        if($product_name) {
            $items = $items->where('items.name', 'LIKE', '%'.$product_name.'%');
        }
        if($profile_id) {
            $items = $items->where('profiles.id', $profile_id);
        }
        if($status) {
            if($status === 'Delivered') {
                $items = $items->where(function($query) {
                    $query->where('transactions.status', 'Delivered')->orWhere('transactions.status', 'Verified Owe')->orWhere('transactions.status', 'Verified Paid');
                });
            }else {
                $items = $items->where('transactions.status', $status);
            }
        }
        if($request->sortName){
            $items = $items->orderBy($request->sortName, $request->sortBy ? 'asc' : 'desc');
        }
        return $items;
    }

    // retrieve total amount and qty for the items product detail daily(Collection $items)
    private function calItemTotals($items)
    {
        $total_amount = 0;
        $total_qty = 0;
        
        foreach($items as $item) {
            $total_amount += $item->amount;
            $total_qty += $item->qty;
        }
        
        $totals = [
            'total_amount' => $total_amount,
            'total_qty' => $total_qty
        ];

        return $totals;
    }

    // calculate all the totals for pay summary detailed rpt (query $transactions)
    private function calAccPaySummary($transactions)
    {
        $data = [];
        $profiles = Profile::all();

        foreach($profiles as $profile) {
            $profileArr = [];
            $profileArr['name'] = $profile->name;
            $cash = clone $transactions;
            $profileArr['cash'] = $cash->where('profiles.id', $profile->id)->where('transactions.pay_method', '=', 'cash')->sum(DB::raw('ROUND((CASE WHEN profiles.gst=1 THEN (CASE WHEN people.is_gst_inclusive=0 THEN (transactions.total * (100+people.gst_rate)/100) ELSE (transactions.total) END) ELSE transactions.total END) + (CASE WHEN transactions.delivery_fee>0 THEN transactions.delivery_fee ELSE 0 END), 2)'));
            $chequein = clone $transactions;
            $profileArr['chequein'] = $chequein->where('profiles.id', $profile->id)->where('transactions.pay_method', '=', 'cheque')->where('transactions.total', '>', 0)->sum(DB::raw('ROUND((CASE WHEN profiles.gst=1 THEN (CASE WHEN people.is_gst_inclusive=0 THEN (transactions.total * (100+people.gst_rate)/100) ELSE (transactions.total) END) ELSE transactions.total END) + (CASE WHEN transactions.delivery_fee>0 THEN transactions.delivery_fee ELSE 0 END), 2)'));
            $chequeout = clone $transactions;
            $profileArr['chequeout'] = $chequeout->where('profiles.id', $profile->id)->where('transactions.pay_method', '=', 'cheque')->where('transactions.total', '<', 0)->sum(DB::raw('ROUND((CASE WHEN profiles.gst=1 THEN (CASE WHEN people.is_gst_inclusive=0 THEN (transactions.total * (100+people.gst_rate)/100) ELSE (transactions.total) END) ELSE transactions.total END) + (CASE WHEN transactions.delivery_fee>0 THEN transactions.delivery_fee ELSE 0 END), 2)'));
            $tt = clone $transactions;
            $profileArr['tt'] = $tt->where('profiles.id', $profile->id)->where('transactions.pay_method', '=', 'tt')->sum(DB::raw('ROUND((CASE WHEN profiles.gst=1 THEN (CASE WHEN people.is_gst_inclusive=0 THEN (transactions.total * (100+people.gst_rate)/100) ELSE (transactions.total) END) ELSE transactions.total END) + (CASE WHEN transactions.delivery_fee>0 THEN transactions.delivery_fee ELSE 0 END), 2)'));
            $subtotal = clone $transactions;
            $profileArr['subtotal'] = $subtotal->where('profiles.id', $profile->id)->sum(DB::raw('ROUND((CASE WHEN profiles.gst=1 THEN (CASE WHEN people.is_gst_inclusive=0 THEN (transactions.total * (100+people.gst_rate)/100) ELSE (transactions.total) END) ELSE transactions.total END) + (CASE WHEN transactions.delivery_fee>0 THEN transactions.delivery_fee ELSE 0 END), 2)'));
            array_push($data, $profileArr);
        }

        $profileArr = [];
        $profileArr['name'] = 'All Profile(s)';
        $cash_all = clone $transactions;
        $profileArr['cash'] = $cash_all->where('transactions.pay_method', '=', 'cash')->sum(DB::raw('ROUND((CASE WHEN profiles.gst=1 THEN (CASE WHEN people.is_gst_inclusive=0 THEN (transactions.total * (100+people.gst_rate)/100) ELSE (transactions.total) END) ELSE transactions.total END) + (CASE WHEN transactions.delivery_fee>0 THEN transactions.delivery_fee ELSE 0 END), 2)'));
        $chequein_all = clone $transactions;
        $profileArr['chequein'] = $chequein_all->where('transactions.pay_method', '=', 'cheque')->where('transactions.total', '>', 0)->sum(DB::raw('ROUND((CASE WHEN profiles.gst=1 THEN (CASE WHEN people.is_gst_inclusive=0 THEN (transactions.total * (100+people.gst_rate)/100) ELSE (transactions.total) END) ELSE transactions.total END) + (CASE WHEN transactions.delivery_fee>0 THEN transactions.delivery_fee ELSE 0 END), 2)'));
        $chequeout_all = clone $transactions;
        $profileArr['chequeout'] = $chequeout_all->where('transactions.pay_method', '=', 'cheque')->where('transactions.total', '<', 0)->sum(DB::raw('ROUND((CASE WHEN profiles.gst=1 THEN (CASE WHEN people.is_gst_inclusive=0 THEN (transactions.total * (100+people.gst_rate)/100) ELSE (transactions.total) END) ELSE transactions.total END) + (CASE WHEN transactions.delivery_fee>0 THEN transactions.delivery_fee ELSE 0 END), 2)'));
        $tt_all = clone $transactions;
        $profileArr['tt'] = $tt_all->where('transactions.pay_method', '=', 'tt')->sum(DB::raw('ROUND((CASE WHEN profiles.gst=1 THEN (CASE WHEN people.is_gst_inclusive=0 THEN (transactions.total * (100+people.gst_rate)/100) ELSE (transactions.total) END) ELSE transactions.total END) + (CASE WHEN transactions.delivery_fee>0 THEN transactions.delivery_fee ELSE 0 END), 2)'));
        $all = clone $transactions;
        $profileArr['subtotal'] = $all->sum(DB::raw('ROUND((CASE WHEN profiles.gst=1 THEN (CASE WHEN people.is_gst_inclusive=0 THEN (transactions.total * (100+people.gst_rate)/100) ELSE (transactions.total) END) ELSE transactions.total END) + (CASE WHEN transactions.delivery_fee>0 THEN transactions.delivery_fee ELSE 0 END), 2)'));
        array_push($data, $profileArr);

        return $data;
    }

    // calculate sales product detail months total(query $items)
    private function calSalesProductDetailMonthTotals($items)
    {
        $total_amount = 0;
        $total_qty = 0;
        foreach($items as $item) {
            $total_amount += $item->amount;
        }
        foreach($items as $item) {
            if($item->is_inventory === 1) {
                $total_qty += $item->qty;
            }

        }
        return $data = [
            'total_amount' => $total_amount,
            'total_qty' => $total_qty
        ];
    }

    // export excel for account pay summary (Collection $data)
    private function paySummaryExportExcel($data)
    {
        $title = 'Payment Summary(Account)';
        Excel::create($title.'_'.Carbon::now()->format('dmYHis'), function($excel) use ($data) {
            $excel->sheet('sheet1', function($sheet) use ($data) {
                $sheet->setColumnFormat(array('A:P' => '@'));
                $sheet->getPageSetup()->setPaperSize('A4');
                $sheet->loadView('detailrpt.account.paymentsummary_excel', compact('data'));
            });
        })->download('xlsx');
    }

    // search sales invoice breakdown db scope(Query $transactions, Formrequest $request)
    private function searchSalesInvoiceBreakdown($transactions, $request)
    {
        $status = $request->status;
        $delivery_from = $request->delivery_from;
        $delivery_to = $request->delivery_to;

        if($status) {
            if($status == 'Delivered') {
                $transactions = $transactions->where(function($query) {
                    $query->where('transactions.status', 'Delivered')->orWhere('transactions.status', 'Verified Owe')->orWhere('transactions.status', 'Verified Paid');
                });
            }else {
                $transactions = $transactions->where('transactions.status', $status);
            }
        }

        if($delivery_from){
            $transactions = $transactions->whereDate('transactions.delivery_date', '>=', $delivery_from);
        }else {
            $transactions = $transactions->whereDate('transactions.delivery_date', '>=', Carbon::today()->subMonth()->startOfMonth()->toDateString());
        }
        if($delivery_to){
            $transactions = $transactions->whereDate('transactions.delivery_date', '<=', $delivery_to);
        }else {
            $transactions = $transactions->whereDate('transactions.delivery_date', '<=', Carbon::today()->endOfMonth()->toDateString());
        }
        return $transactions;
    }

    // export excel for invoice breakdown (Formrequest $request, Array $transactionsId, Array itemsId, int person_id)
    private function exportInvoiceBreakdownExcel($request, $transactionsId, $itemsId, $person_id)
    {
        $person = Person::findOrFail($person_id);
        $title = 'Invoice Breakdown ('.$person->cust_id.')';
        Excel::create($title.'_'.Carbon::now()->format('dmYHis'), function($excel) use ($request, $transactionsId, $itemsId, $person_id) {
            $excel->sheet('sheet1', function($sheet) use ($request, $transactionsId, $itemsId, $person_id) {
                $sheet->setColumnFormat(array('A:P' => '@'));
                $sheet->getPageSetup()->setPaperSize('A4');
                $sheet->setAutoSize(true);
                $sheet->loadView('detailrpt.invoicebreakdown_excel', compact('request', 'transactionsId', 'itemsId', 'person_id'));
            });
        })->download('xlsx');
    }

    // export excel for stock date balace and sold(formrequest request, array itemsidarr, array alldatetransactionids)
    private function exportStockDateExcel($request, $allDatesArr, $itemsIdArr, $allDateTransactionIds)
    {
        $title = 'Stock Date (Balance/ Sold)';
        Excel::create($title.'_'.Carbon::now()->format('dmYHis'), function($excel) use ($request, $allDatesArr, $itemsIdArr, $allDateTransactionIds) {
            $excel->sheet('sheet1', function($sheet) use ($request, $allDatesArr, $itemsIdArr, $allDateTransactionIds) {
                $sheet->setColumnFormat(array('A:P' => '@'));
                $sheet->getPageSetup()->setPaperSize('A4');
                $sheet->setAutoSize(true);
                $sheet->loadView('detailrpt.stockdate_excel', compact('request', 'allDatesArr', 'itemsIdArr', 'allDateTransactionIds'));
            });
        })->download('xlsx');
    }

    // running stock billing queries
    private function stockBillingSql()
    {
        $deals = DB::table('deals')
                ->leftJoin('items', 'items.id', '=', 'deals.item_id')
                ->leftJoin('transactions', 'transactions.id', '=', 'deals.transaction_id')
                ->leftJoin('people', 'people.id', '=', 'transactions.person_id')
                ->leftJoin('profiles', 'profiles.id', '=', 'people.profile_id')
                ->leftJoin('custcategories', 'custcategories.id', '=', 'people.custcategory_id')
                ->leftJoin('unitcosts', function($join) {
                    $join->on('items.id', '=', 'unitcosts.item_id');
                    $join->on('profiles.id', '=', 'unitcosts.profile_id');
                })
                ->select(
                    'deals.divisor', 'deals.dividend',
                    'profiles.id AS profile_id', 'profiles.name AS profile_name', 'profiles.gst', 'people.gst_rate',
                    'items.id AS item_id', 'items.product_id', 'items.name AS item_name', 'items.is_inventory', 'items.unit', 'items.remark AS item_remark',
                    DB::raw('ROUND(SUM(deals.qty), 4) AS qty'),
                    DB::raw('ROUND(CASE WHEN deals.unit_cost IS NOT NULL THEN SUM(deals.unit_cost * deals.qty) ELSE SUM(unitcosts.unit_cost * deals.qty) END / SUM(deals.qty), 2) AS avg_unit_cost'),
                    DB::raw('ROUND(CASE WHEN deals.unit_cost IS NOT NULL THEN SUM(deals.unit_cost * deals.qty) ELSE SUM(unitcosts.unit_cost * deals.qty) END, 2) AS total_cost'),
                    DB::raw('ROUND(SUM(deals.amount), 2) AS amount'),
                    DB::raw('ROUND(SUM(deals.amount) / SUM(deals.qty), 2) AS avg_sell_value'),
                    DB::raw('ROUND(CASE WHEN items.is_inventory=1 THEN (SUM(deals.amount) - SUM(CASE WHEN deals.unit_cost IS NOT NULL THEN deals.unit_cost ELSE unitcosts.unit_cost END * qty)) ELSE SUM(deals.amount) END, 2) AS gross')
                );
        if(request('profile_id') or request('delivery_from') or request('delivery_to') or request('cust_id') or request('company') or request('person_id') or request('custcategory_id') or request('is_inventory') or request('is_commission')) {
            $deals = $this->stockBillingFilters(request(), $deals);
        }

        $deals = $deals->where(function($query) {
                        $query->where('transactions.status', 'Delivered')
                                ->orWhere('transactions.status', 'Verified Owe')
                                ->orWhere('transactions.status', 'Verified Paid');
                    });

        if(request('profile_id')) {
            $deals = $deals->groupBy('items.id', 'profiles.id');
        }else {
            $deals = $deals->groupBy('items.id');
        }

        // add user profile filters
        $deals = $this->filterUserDbProfile($deals);

        if(request('sortName')){
            $deals = $deals->orderBy(request('sortName'), request('sortBy') ? 'asc' : 'desc');
        }else {
            $deals = $deals->orderBy('items.product_id')->orderBy('profiles.id');
        }

        $totals = $this->calStockBillingTotals($deals);

        $data = [
            'deals' => $deals,
            'totals' => $totals
        ];

        return $data;
    }
}
