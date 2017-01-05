<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests;
use App\Transaction;
use Carbon\Carbon;
use Auth;
use DB;

class DetailRptController extends Controller
{
    // detect authed
    public function __construct()
    {
        $this->middleware('auth');
    }

    // return index page for detailed report - account
    public function accountIndex()
    {
        return view('detailrpt.account.index');
    }

    // return index page for detailed report - sales
    public function salesIndex()
    {
        return view('detailrpt.sales.index');
    }

    // retrieve the account cust detail rpt(FormRequest $request)
    public function getAccountCustdetailApi(Request $request)
    {
        // showing total amount init
        $total_amount = 0;
        $input = $request->all();
        // initiate the page num when null given
        $pageNum = $request->pageNum ? $request->pageNum : 100;

        $transactions = DB::table('transactions')
                        ->leftJoin('people', 'transactions.person_id', '=', 'people.id')
                        ->leftJoin('profiles', 'people.profile_id', '=', 'profiles.id')
                        ->select(
                                    'transactions.id', 'people.cust_id', 'people.company',
                                    'people.name', 'people.id as person_id',
                                    'transactions.status', 'transactions.delivery_date', 'profiles.name as profile_name',
                                    'transactions.total', 'transactions.pay_status',
                                    'profiles.id as profile_id', 'transactions.order_date',
                                    'profiles.gst', 'transactions.delivery_fee', 'transactions.paid_at'
                                );

        // reading whether search input is filled
        if($request->id or $request->cust_id or $request->company or $request->status or $request->pay_status or $request->updated_by or $request->updated_at or $request->delivery_from or $request->delivery_to or $request->driver or $request->profile){
            $transactions = $this->searchDBFilter($transactions, $request);
        }else{
            if($request->sortName){
                $transactions = $transactions->orderBy($request->sortName, $request->sortBy ? 'asc' : 'desc');
            }
        }
        $total_amount = $this->calDBTransactionTotal($transactions);
        $delivery_total = $this->calDBDeliveryTotal($transactions);

        if($request->exportSOA) {
            $this->convertSoaExcel($transactions, $total_amount + $delivery_total);
        }

        if($pageNum == 'All'){
            $transactions = $transactions->latest('transactions.created_at')->get();
        }else{
            $transactions = $transactions->latest('transactions.created_at')->paginate($pageNum);
        }

        $data = [
            'total_amount' => $total_amount + $delivery_total,
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

        $thistotal = DB::raw("(SELECT ROUND(SUM(CASE WHEN profiles.gst=1 THEN (CASE WHEN delivery_fee>0 THEN total*107/100 + delivery_fee ELSE total*107/100 END) ELSE (CASE WHEN delivery_fee>0 THEN total + delivery_fee ELSE total END) END), 2) AS thistotal, people.id AS person_id, people.profile_id FROM transactions
                                LEFT JOIN people ON transactions.person_id=people.id
                                LEFT JOIN profiles ON people.profile_id=profiles.id
                                WHERE transactions.delivery_date>='".Carbon::now()->startOfMonth()->toDateString()."'
                                AND transactions.delivery_date<='".Carbon::now()->endOfMonth()->toDateString()."'
                                AND pay_status='Owe'
                                AND (status='Delivered' OR status='Verified Owe')
                                GROUP BY people.id) thistotal");

        $prevtotal = DB::raw("(SELECT ROUND(SUM(CASE WHEN profiles.gst=1 THEN (CASE WHEN delivery_fee>0 THEN total*107/100 + delivery_fee ELSE total*107/100 END) ELSE (CASE WHEN delivery_fee>0 THEN total + delivery_fee ELSE total END) END), 2) AS prevtotal, people.id AS person_id, people.profile_id FROM transactions
                                LEFT JOIN people ON transactions.person_id=people.id
                                LEFT JOIN profiles ON people.profile_id=profiles.id
                                WHERE transactions.delivery_date>='".Carbon::now()->subMonth()->startOfMonth()->toDateString()."'
                                AND transactions.delivery_date<='".Carbon::now()->subMonth()->endOfMonth()->toDateString()."'
                                AND pay_status='Owe'
                                AND (status='Delivered' OR status='Verified Owe')
                                GROUP BY people.id) prevtotal");

        $prev2total = DB::raw("(SELECT ROUND(SUM(CASE WHEN profiles.gst=1 THEN (CASE WHEN delivery_fee>0 THEN total*107/100 + delivery_fee ELSE total*107/100 END) ELSE (CASE WHEN delivery_fee>0 THEN total + delivery_fee ELSE total END) END), 2) AS prev2total, people.id AS person_id, people.profile_id FROM transactions
                                LEFT JOIN people ON transactions.person_id=people.id
                                LEFT JOIN profiles ON people.profile_id=profiles.id
                                WHERE transactions.delivery_date>='".Carbon::now()->subMonths(2)->startOfMonth()->toDateString()."'
                                AND transactions.delivery_date<='".Carbon::now()->subMonth(2)->endOfMonth()->toDateString()."'
                                AND pay_status='Owe'
                                AND (status='Delivered' OR status='Verified Owe')
                                GROUP BY people.id) prev2total");

        $prevmore3total = DB::raw("(SELECT ROUND(SUM(CASE WHEN profiles.gst=1 THEN (CASE WHEN delivery_fee>0 THEN total*107/100 + delivery_fee ELSE total*107/100 END) ELSE (CASE WHEN delivery_fee>0 THEN total + delivery_fee ELSE total END) END), 2) AS prevmore3total, people.id AS person_id, people.profile_id FROM transactions
                                LEFT JOIN people ON transactions.person_id=people.id
                                LEFT JOIN profiles ON people.profile_id=profiles.id
                                WHERE transactions.delivery_date<='".Carbon::now()->subMonths(3)->endOfMonth()->toDateString()."'
                                AND pay_status='Owe'
                                AND (status='Delivered' OR status='Verified Owe')
                                GROUP BY people.id) prevmore3total");

        $transactions = DB::table('transactions')
                        ->leftJoin('people', 'transactions.person_id', '=', 'people.id')
                        ->leftJoin('profiles', 'people.profile_id', '=', 'profiles.id')
                        ->leftJoin($thistotal, 'people.id', '=', 'thistotal.person_id')
                        ->leftJoin($prevtotal, 'people.id', '=', 'prevtotal.person_id')
                        ->leftJoin($prev2total, 'people.id', '=', 'prev2total.person_id')
                        ->leftJoin($prevmore3total, 'people.id', '=', 'prevmore3total.person_id')
                        ->distinct('people.id')
                        ->select(
                                    'people.cust_id', 'people.company', 'people.name', 'people.id as person_id',
                                    'profiles.name as profile_name', 'profiles.id as profile_id', 'profiles.gst',
                                    'transactions.id', 'transactions.status', 'transactions.delivery_date', 'transactions.pay_status', 'transactions.delivery_fee', 'transactions.paid_at', 'transactions.created_at',
                                    'thistotal.thistotal AS thistotal', 'prevtotal.prevtotal AS prevtotal', 'prev2total.prev2total AS prev2total', 'prevmore3total.prevmore3total AS prevmore3total'
                                );

        if($request->id or $request->cust_id or $request->company or $request->status or $request->pay_status or $request->updated_by or $request->updated_at or $request->delivery_from or $request->delivery_to or $request->driver or $request->profile){
            $transactions = $this->searchDBFilter($transactions, $request);
        }else{
            if($request->sortName){
                $transactions = $transactions->orderBy($request->sortName, $request->sortBy ? 'asc' : 'desc');
            }
        }
        $total_amount = $this->calTransactionTotalSql($transactions);

        if($pageNum == 'All'){
            $transactions = $transactions->latest('transactions.created_at')->groupBy('people.id')->get();
        }else{
            $transactions = $transactions->latest('transactions.created_at')->groupBy('people.id')->paginate($pageNum);
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
        // initiate the page num when null given
        $pageNum = $request->pageNum ? $request->pageNum : 100;

        $transactions = DB::table('transactions')
                        ->leftJoin('people', 'transactions.person_id', '=', 'people.id')
                        ->leftJoin('profiles', 'people.profile_id', '=', 'profiles.id')
                        ->select(
                                    'people.cust_id', 'people.company', 'people.name', 'people.id as person_id',
                                    'profiles.name as profile_name', 'profiles.id as profile_id',
                                    'transactions.id', 'transactions.delivery_fee', 'transactions.paid_at', 'transactions.status', 'transactions.delivery_date', 'transactions.pay_status', 'transactions.order_date', 'transactions.note', 'transactions.pay_method',
                                    DB::raw('(CASE WHEN transactions.delivery_fee>0 THEN (transactions.total + transactions.delivery_fee) ELSE transactions.total END) AS inv_amount'),
                                    DB::raw('(CASE WHEN profiles.gst=1 THEN (CASE WHEN transactions.delivery_fee>0 THEN (transactions.total * 107/100 + transactions.delivery_fee) ELSE (transactions.total * 107/100) END) ELSE transactions.total END) AS amount'),
                                    DB::raw('(CASE WHEN profiles.gst=1 THEN (transactions.total * 7/100) ELSE null END) AS gst')
                                );

        // reading whether search input is filled
        if($request->id or $request->cust_id or $request->company or $request->status or $request->pay_status or $request->updated_by or $request->updated_at or $request->delivery_from or $request->delivery_to or $request->driver or $request->profile){
            $transactions = $this->searchDBFilter($transactions, $request);
        }else{
            if($request->sortName){
                $transactions = $transactions->orderBy($request->sortName, $request->sortBy ? 'asc' : 'desc');
            }
        }
        $total_amount = $this->calDBTransactionTotal($transactions);
        $delivery_total = $this->calDBDeliveryTotal($transactions);

        if($request->exportSOA) {
            $this->convertSoaExcel($transactions, $total_amount + $delivery_total);
        }

        if($pageNum == 'All'){
            $transactions = $transactions->latest('transactions.created_at')->get();
        }else{
            $transactions = $transactions->latest('transactions.created_at')->paginate($pageNum);
        }

        $caldata = $this->calAllTotal($transactions);

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
                        ->leftJoin('profiles', 'people.profile_id', '=', 'profiles.id')
                        ->select(
                                    'profiles.name as profile_name', 'profiles.id as profile_id',
                                    'transactions.id', 'transactions.delivery_fee', 'transactions.paid_at', 'transactions.status', 'transactions.delivery_date', 'transactions.pay_status', 'transactions.order_date', 'transactions.note', 'transactions.pay_method',
                                    DB::raw('(CASE WHEN transactions.delivery_fee>0 THEN (transactions.total + transactions.delivery_fee) ELSE transactions.total END) AS inv_amount'),
                                    DB::raw('(CASE WHEN profiles.gst=1 THEN (CASE WHEN transactions.delivery_fee>0 THEN (transactions.total * 107/100 + transactions.delivery_fee) ELSE (transactions.total * 107/100) END) ELSE transactions.total END) AS amount'),
                                    DB::raw('(CASE WHEN profiles.gst=1 THEN (transactions.total * 7/100) ELSE null END) AS gst')
                                );

        // reading whether search input is filled
        if($request->id or $request->cust_id or $request->company or $request->status or $request->pay_status or $request->updated_by or $request->updated_at or $request->delivery_from or $request->delivery_to or $request->driver or $request->profile){
            $transactions = $this->searchDBFilter($transactions, $request);
        }else{
            if($request->sortName){
                $transactions = $transactions->orderBy($request->sortName, $request->sortBy ? 'asc' : 'desc');
            }
        }
        $total_amount = $this->calDBTransactionTotal($transactions);
        $delivery_total = $this->calDBDeliveryTotal($transactions);

        if($request->exportSOA) {
            $this->convertSoaExcel($transactions, $total_amount + $delivery_total);
        }

        if($pageNum == 'All'){
            $transactions = $transactions->latest('transactions.created_at')->get();
        }else{
            $transactions = $transactions->latest('transactions.created_at')->paginate($pageNum);
        }

        $caldata = $this->calAllTotal($transactions);

        $data = [
            'total_inv_amount' => $caldata['total_inv_amount'],
            'total_gst' => $caldata['total_gst'],
            'total_amount' => $caldata['total_amount'],
            'transactions' => $transactions,
        ];

        return $data;
    }

    // export SOA report(Array $data)
    private function convertSoaExcel($transactions, $total)
    {
        $soa_query = clone $transactions;
        $data = $soa_query->orderBy('people.cust_id')->orderBy('transactions.id')->get();
        $title = 'Account SOA';

        Excel::create($title.'_'.Carbon::now()->format('dmYHis'), function($excel) use ($data, $total) {
            $excel->sheet('sheet1', function($sheet) use ($data, $total) {
                $sheet->setAutoSize(true);
                $sheet->setColumnFormat(array(
                    'A:D' => '@',
                    'E' => '0.00'
                ));
                $sheet->loadView('detailrpt.account.custdetail_excel', compact('data', 'total'));
            });
        })->download('xls');
    }

    // conditional filter parser(Collection $query, Formrequest $request)
    private function searchDBFilter($transactions, $request)
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

        if($profile_id){
            $transactions = $transactions->where('profiles.id', $profile_id);
        }
        if($delivery_from){
            $transactions = $transactions->where('transactions.delivery_date', '>=', $delivery_from);
        }
        if($payment_from){
            $transactions = $transactions->where('transactions.paid_at', '>=', $payment_from);
        }
        if($cust_id){
            $transactions = $transactions->where('people.cust_id', 'LIKE', '%'.$cust_id.'%');
        }
        if($delivery_to){
            $transactions = $transactions->where('transactions.delivery_date', '<=', $delivery_to);
        }
        if($payment_to){
            $transactions = $transactions->where('transactions.paid_at', '<=', $payment_to);
        }
        if($status) {
            if($status == 'Delivered') {
                $transactions = $transactions->where(function($query) {
                    $query->where('transactions.status', 'Delivered')->orWhere('transactions.status', 'Verified Owe');
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
        $gst_amount = $query2->where('profiles.gst', 1)->sum(DB::raw('ROUND((transactions.total * 107/100), 2)'));

        $total_amount = $nonGst_amount + $gst_amount;

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
        $query1 = clone $query;
        $total_amount = $query1->sum('thistotal');
        return $total_amount;
    }

    // cal independent total for inv_total gst and amount
    private function calAllTotal($query)
    {
        $query1 = clone $query;
        $query2 = clone $query;
        $query3 = clone $query;

        $total_inv_amount = $query1->sum('inv_amount');
        $total_gst = $query2->sum('gst');
        $total_amount = $query3->sum('amount');

        $caldata = [
            'total_inv_amount' => $total_inv_amount,
            'total_gst' => $total_gst,
            'total_amount' => $total_amount,
        ];

        return $caldata;
    }
}
