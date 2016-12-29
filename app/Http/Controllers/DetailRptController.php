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

        $transactions = DB::table('transactions')
                        ->leftJoin('people', 'transactions.person_id', '=', 'people.id')
                        ->leftJoin('profiles', 'people.profile_id', '=', 'profiles.id')
                        ->distinct('people.id')
                        ->select(
                                    'transactions.id', 'people.cust_id', 'people.company',
                                    'people.name', 'people.id as person_id',
                                    'transactions.status', 'transactions.delivery_date', 'profiles.name as profile_name',
                                    'transactions.total', 'transactions.pay_status',
                                    'profiles.id as profile_id',
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
            $transactions = $transactions->latest('transactions.created_at')->groupBy('people.id')->get();
        }else{
            $transactions = $transactions->latest('transactions.created_at')->groupBy('people.id')->paginate($pageNum);
        }

        $data = [
            'total_amount' => $total_amount + $delivery_total,
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
        if($company){
            $transactions = $transactions->where(function($query) use ($company){
                $query->where('people.company', 'LIKE', '%'.$company.'%')
                        ->orWhere(function ($query) use ($company){
                            $query->where('people.cust_id', 'LIKE', 'D%')
                                    ->where('people.name', 'LIKE', '%'.$company.'%');
                        });
                });
        }
        if($status){
            $transactions = $transactions->where('transactions.status', $status);
        }
        if($person_id){
            $transactions = $transactions->where('people.id', $person_id);
        }
        if($payment){
            $transaction = $transactions->where('transactions.pay_status',$payment);
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
}
