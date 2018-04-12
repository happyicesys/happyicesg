<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests;
use App\Ftransaction;
use App\Fdeal;
use App\Person;
use App\Item;
use App\GeneralSetting;
use Carbon\Carbon;
use Laracasts\Flash\Flash;
use DB;
use PDF;

// traits
use App\HasMonthOptions;
use App\HasProfileAccess;
use App\GetIncrement;

class FtransactionController extends Controller
{
	use HasMonthOptions, HasProfileAccess, GetIncrement;

    //auth-only login can see
    public function __construct()
    {
        $this->middleware('auth');
    }

    // return index page()
	public function index()
	{
		return view('franchisee.index');
	}

	// return ftransaction index page api()
	public function indexApi()
	{
        // die(var_dump(request()->all()));
        // showing total amount init
        $total_vend_amount = 0;
        // initiate the page num when null given
        $pageNum = request('pageNum') ? request('pageNum') : 100;

        $ftransactions = DB::table('ftransactions AS x')
                        ->leftJoin('people', 'x.person_id', '=', 'people.id')
                        ->leftJoin('profiles', 'people.profile_id', '=', 'profiles.id')
                        ->leftJoin('users', 'users.id', '=', 'x.franchisee_id')
                        ->leftJoin('users AS update_person', 'update_person.id', '=', 'x.updated_by')
                        ->select(
                                    'people.cust_id', 'people.company',
                                    'people.name', 'people.id as person_id', 'x.id', 'x.ftransaction_id', 'x.total',
                                    DB::raw('DATE(x.collection_datetime) AS collection_date'),
                                    DB::raw('TIME_FORMAT(TIME(x.collection_datetime), "%h:%i %p") AS collection_time'),
                                    DB::raw('ROUND((CASE WHEN x.sales THEN x.total/ x.sales ELSE 0 END), 2) AS avg_sales_piece'),
                                    DB::raw('ROUND(x.sales/ABS(DATEDIFF(x.collection_datetime,
                                                (SELECT collection_datetime FROM ftransactions WHERE person_id=x.person_id AND DATE(collection_datetime)<DATE(x.collection_datetime) ORDER BY collection_datetime DESC LIMIT 1)
                                                )), 1)
                                                    AS avg_sales_day'),
                                    'x.digital_clock', 'x.analog_clock', 'x.sales', 'x.taxtotal', 'x.finaltotal', 'x.remarks', 'x.bankin_date',
                                    'users.name', 'users.user_code',
                                    'profiles.id as profile_id', 'profiles.gst', 'people.is_gst_inclusive', 'profiles.gst_rate',
                                    'update_person.name AS updated_by'
                                );

        // reading whether search input is filled
		if(request('id') or request('cust_id') or request('company') or request('collection_from') or request('collection_to') or  request('franchisee_id') or request('person_id')){
            $ftransactions = $this->searchDBFilter($ftransactions);
        }

        // add user profile filters
        $ftransactions = $this->filterUserDbProfile($ftransactions);

        // filter off franchisee
        if(auth()->user()->hasRole('franchisee')) {
            $ftransactions = $ftransactions->where('x.franchisee_id', auth()->user()->id);
        }

        $totals = $this->calDBFtransactionTotal($ftransactions);
        $dynamictotals = $this->calDBDynamicFtransactionTotal($ftransactions);

        if(request('sortName')){
            $ftransactions = $ftransactions->orderBy(request('sortName'), request('sortBy') ? 'asc' : 'desc');
        }

        if($pageNum == 'All'){
            $ftransactions = $ftransactions->latest('x.collection_datetime')->get();
        }else{
            $ftransactions = $ftransactions->latest('x.collection_datetime')->paginate($pageNum);
        }

        $data = [
            'totals' => $totals,
            'ftransactions' => $ftransactions,
            'dynamictotals' => $dynamictotals
        ];

        if(request('export_excel')) {
            $this->exportFtransactionIndexExcel($data);
        }

        return $data;
	}

    // delete ftransaction(int id)
    public function destroyApi($id)
    {
        $ftransaction = Ftransaction::findOrFail($id);
        $ftransaction->delete();
    }

    // pass value into filter search for DB (collection) [query]
    private function searchDBFilter($ftransactions)
    {
        if(request('id')){
            $ftransactions = $ftransactions->where('x.id', 'LIKE', '%'.request('id').'%');
        }
        if(request('cust_id')){
            $ftransactions = $ftransactions->where('people.cust_id', 'LIKE', '%'.request('cust_id').'%');
        }
        if(request('company')){
            $com = request('company');
            $ftransactions = $ftransactions->where(function($query) use ($com){
                $query->where('people.company', 'LIKE', '%'.$com.'%')
                        ->orWhere(function ($query) use ($com){
                            $query->where('people.cust_id', 'LIKE', 'D%')
                                    ->where('people.name', 'LIKE', '%'.$com.'%');
                        });
                });
        }
        if(request('collection_from') === request('collection_to')){
            if(request('collection_from') != '' and request('collection_to') != ''){
                $ftransactions = $ftransactions->whereDate('x.collection_datetime', '=', request('collection_to'));
            }
        }else{
            if(request('collection_from')){
                $ftransactions = $ftransactions->whereDate('x.collection_datetime', '>=', request('collection_from'));
            }
            if(request('collection_to')){
                $ftransactions = $ftransactions->whereDate('x.collection_datetime', '<=', request('collection_to'));
            }
        }
        if(request('franchisee_id')){
            $ftransactions = $ftransactions->where('x.franchisee_id', request('franchisee_id'));
        }
        if(request('person_id')) {
            $ftransactions = $ftransactions->where('x.person_id', request('person_id'));
        }
        if(request('sortName')){
            $ftransactions = $ftransactions->orderBy(request('sortName'), request('sortBy') ? 'asc' : 'desc');
        }
        return $ftransactions;
    }

    // retrieve franchisee id if current user is franchisee or return null()
    public function getFranchiseeIdApi()
    {
        if(auth()->user()->hasRole('franchisee')) {
            $user = auth()->user()->id;
        }else {
            $user = null;
        }

        return $user;
    }

    // form create ftransaction entry()
    public function submitEntry()
    {
        $person_id = request('person_id');
        $collection_date = request('collection_date');
        $collection_time = request('collection_time');
        $digital_clock = request('digital_clock');
        $analog_clock = request('analog_clock');
        $total = request('total');
        $franchisee_id = request('franchisee_id');
        $remarks = request('remarks');

        $getTaxFinalTotals = $this->calTaxFinaltotal($total, $person_id);
        $taxtotal = $getTaxFinalTotals['tax_total'];
        $finaltotal = $getTaxFinalTotals['final_total'];

        if(auth()->user()->hasRole('franchisee')) {
            $franchisee_id = auth()->user()->id;
        }else {
            $this->validate(request(), [
                'franchisee_id' => 'required'
            ], [
                'franchisee_id.required' => 'Please choose a franchisee'
            ]);
        }

        $ftransaction = Ftransaction::create([
            'ftransaction_id' => $this->getFtransactionIncrement($franchisee_id),
            'person_id' => $person_id,
            'collection_datetime' => $this->convertDateTimeCarbon($collection_date, $collection_time),
            'digital_clock' => $digital_clock,
            'analog_clock' => $analog_clock,
            'total' => $total,
            'taxtotal' => $taxtotal,
            'finaltotal' => $finaltotal,
            'sales' => $this->calAnalogSales($person_id, $analog_clock),
            'franchisee_id' => $franchisee_id,
            'remarks' => $remarks,
            'updated_by' => auth()->user()->id,
        ]);

        $this->updateLaterAnalogSales($ftransaction->id);
    }

    // update ftransactions remarks when post request(int ftransaction_id)
    public function editApi($id)
    {
        $remarks = request('remarks');
        $bankin_date = request('bankin_date');

        $ftransaction = Ftransaction::findOrFail($id);
        if($remarks) {
            $ftransaction->remarks = $remarks;
        }
        if($bankin_date) {
            $ftransaction->bankin_date = $bankin_date;
        }
        $ftransaction->save();
    }

    // search if there is any deal later than this, update analog sales deduction(int id)
    private function updateLaterAnalogSales($id)
    {
        $ftransaction = Ftransaction::findOrFail($id);
        $laterftransactions = Ftransaction::where('person_id', $ftransaction->person_id)->whereDate('collection_datetime', '>', $ftransaction->collection_datetime)->get();
        if(count($laterftransactions)>0) {
            foreach($laterftransactions as $laterftransaction) {
                $previousftransaction = Ftransaction::where('collection_datetime', '<', $laterftransaction->collection_datetime)->where('person_id', $laterftransaction->person_id)->latest('collection_datetime')->first();
                $laterftransaction->sales = $laterftransaction->analog_clock - $previousftransaction->analog_clock;
                $laterftransaction->save();
            }
        }
    }

    // calculating tax and final total based on input(float input_total, int person_id)
    private function calTaxFinaltotal($input_total, $person_id)
    {
        $person = Person::findOrFail($person_id);
        $tax_total = 0;
        $final_total = $input_total;

        if($person->profile->gst) {
            $tax_total = number_format($input_total - $input_total/((100 + $person->gst_rate)/ 100), 2);
            $final_total = number_format($input_total/ ((100 + $person->gst_rate)/ 100), 2);
        }

        return [
            'tax_total' => $tax_total,
            'final_total' => $final_total
        ];
    }

    // converting date and time into datetime(String date, String time)
    private function convertDateTimeCarbon($date, $time)
    {
        if(!$date) {
            $date = Carbon::today()->toDateString();
        }
        if(!$time) {
            $time = Carbon::now()->toTimeString();
        }
        $datetime = Carbon::parse($date.' '.$time);

        return $datetime;
    }

    // cal sales based on previous analog and current analog(int person_id, int current_analog)
    private function calAnalogSales($person_id, $current_analog)
    {
        $collection_datetime = $this->convertDateTimeCarbon(request('collection_date'), request('collection_time'));
        $prev_ftrans = Ftransaction::where('person_id', $person_id)->where('collection_datetime', '<', $collection_datetime)->latest('collection_datetime')->first();
        // $latertrans_exist = Ftransaction::where('person_id', $person_id)->latest()->where('collection_datetime', '>', $collection_datetime)->first();
        $sales = null;

        // if(!$latertrans_exist) {
            if($prev_ftrans and $current_analog) {
                $sales = $current_analog - $prev_ftrans->analog_clock;
            }
        // }
        return $sales;
    }

    // calculating gst and non for delivered total
    private function calDBFtransactionTotal($query)
    {
        $total_vend_amount = 0;
        $total_sales_pieces = 0;
        $person_id = request('person_id');
        $query1 = clone $query;

        $total_vend_amount = $query1->sum(DB::raw('ROUND(x.total, 2)'));

        $total_stock_in = DB::table('deals')
                            ->leftJoin('transactions', 'transactions.id', '=', 'deals.transaction_id')
                            ->leftJoin('items', 'items.id', '=', 'deals.item_id')
                            ->leftJoin('people', 'people.id', '=', 'transactions.person_id');
        $total_stock_in = $this->filterTransactionFromTo($total_stock_in);
        $total_stock_in = $total_stock_in
                            ->where('people.id', $person_id)
                            ->where(function($query) {
                                $query->where('transactions.status', 'Delivered')
                                    ->orWhere('transactions.status', 'Verified Owe')
                                    ->orWhere('transactions.status', 'Verified Paid');
                                });
        $total_stock_in = $total_stock_in->select(DB::raw('ROUND(SUM(CASE WHEN deals.divisor>1 THEN (items.base_unit * deals.dividend/deals.divisor) ELSE (deals.qty * items.base_unit) END)) AS pieces'))->first()->pieces;

        $total_sold_qty = DB::table('ftransactions')
                            ->leftJoin('people', 'people.id', '=', 'ftransactions.person_id');
        $total_sold_qty = $this->filterCollectionFromTo($total_sold_qty);
        $total_sold_qty = $total_sold_qty
                            ->where('people.id', $person_id);
        $total_sold_qty = $total_sold_qty->sum('ftransactions.sales');

        $difference_stock_sold = $total_stock_in - $total_sold_qty;
        $total_sales_pieces = $total_sold_qty ? $total_vend_amount/$total_sold_qty : 0;
        $avg_pieces_day = $total_sold_qty/ $this->getCarbonDateDiff(request('collection_from'), request('collection_to'), request('person_id'));

        $transactions = DB::table('transactions')
                        ->leftJoin('people', 'transactions.person_id', '=', 'people.id')
                        ->leftJoin('profiles', 'people.profile_id', '=', 'profiles.id')
                        ->leftJoin('custcategories', 'people.custcategory_id', '=', 'custcategories.id')
                        ->select(
                                    'people.cust_id', 'people.company',
                                    'people.name', 'people.id as person_id', 'transactions.del_postcode',
                                    'transactions.status', 'transactions.delivery_date', 'transactions.driver',
                                    'transactions.total_qty', 'transactions.pay_status',
                                    'transactions.updated_by', 'transactions.updated_at', 'transactions.delivery_fee', 'transactions.id',
                                    DB::raw('DATE(transactions.delivery_date) AS delivery_date'),
                                    DB::raw('ROUND((CASE WHEN profiles.gst=1 THEN (
                                                CASE
                                                WHEN people.is_gst_inclusive=0
                                                THEN total*((100+people.gst_rate)/100)
                                                ELSE transactions.total
                                                END) ELSE transactions.total END) + (CASE WHEN transactions.delivery_fee>0 THEN transactions.delivery_fee ELSE 0 END), 2) AS caltotal'),
                                    'profiles.id as profile_id', 'profiles.gst', 'people.is_gst_inclusive', 'people.gst_rate',
                                     'custcategories.name as custcategory'
                                );
        $transactions = $this->filterTransactionFromTo($transactions);
        if($person_id) {
            $transactions = $transactions->where('person_id', $person_id);
        }

        $transactions_owe = clone $transactions;
        $transactions_paid = clone $transactions;

        $transactions_total = $transactions->sum(DB::raw('ROUND((CASE WHEN profiles.gst=1 THEN (
                                                            CASE
                                                            WHEN people.is_gst_inclusive=0
                                                            THEN transactions.total*((100+people.gst_rate)/100)
                                                            ELSE transactions.total
                                                            END) ELSE transactions.total END), 2)'));
        $transactions_owe = $transactions_owe->where('transactions.pay_status', 'Owe')->sum(DB::raw('ROUND((CASE WHEN profiles.gst=1 THEN (
                                                                                                    CASE
                                                                                                    WHEN people.is_gst_inclusive=0
                                                                                                    THEN transactions.total*((100+people.gst_rate)/100)
                                                                                                    ELSE transactions.total
                                                                                                    END) ELSE transactions.total END), 2)'));
        $transactions_paid = $transactions_paid->where('transactions.pay_status', 'Paid')->sum(DB::raw('ROUND((CASE WHEN profiles.gst=1 THEN (
                                                                                                        CASE
                                                                                                        WHEN people.is_gst_inclusive=0
                                                                                                        THEN transactions.total*((100+people.gst_rate)/100)
                                                                                                        ELSE transactions.total
                                                                                                        END) ELSE transactions.total END), 2)'));      

        $data = [
            'total_vend_amount' => $total_vend_amount,
            'total_sales_pieces' => $total_sales_pieces,
            'avg_pieces_day' => $avg_pieces_day,
            'total_stock_in' => $total_stock_in,
            'total_sold_qty' => $total_sold_qty,
            'difference_stock_sold' => $difference_stock_sold,
            'transactions_total' => $transactions_total,
            'transactions_owe' => $transactions_owe,
            'transactions_paid' => $transactions_paid
        ];

        return $data;
    }

    // calculating gst and non for delivered dynamic total
    private function calDBDynamicFtransactionTotal($query)
    {
        $dynamic_vend_amount = 0;
        $dynamic_sales_pieces = 0;
        $query1 = clone $query;
        $query2 = clone $query;
        $query3 = clone $query;
        $dynamic_vend_amount = $query1->sum(DB::raw('ROUND(x.total, 2)'));

        $dynamic_stock_in = DB::table('deals')
                            ->leftJoin('transactions', 'transactions.id', '=', 'deals.transaction_id')
                            ->leftJoin('items', 'items.id', '=', 'deals.item_id');
        $dynamic_stock_in = $this->filterTransactionFromTo($dynamic_stock_in);
        $dynamic_stock_in = $dynamic_stock_in
                            ->where(function($query) {
                                $query->where('transactions.status', 'Delivered')
                                    ->orWhere('transactions.status', 'Verified Owe')
                                    ->orWhere('transactions.status', 'Verified Paid');
                                });
        $dynamic_stock_in = $dynamic_stock_in->select(DB::raw('ROUND(SUM(CASE WHEN deals.divisor>1 THEN (items.base_unit * deals.dividend/deals.divisor) ELSE (deals.qty * items.base_unit) END)) AS pieces'))->first()->pieces;

        $dynamic_sold_qty = DB::table('transactions');
        $dynamic_sold_qty = $this->filterTransactionFromTo($dynamic_sold_qty);
        $dynamic_sold_qty = $dynamic_sold_qty
                            ->where(function($query) {
                                $query->where('transactions.status', 'Delivered')
                                    ->orWhere('transactions.status', 'Verified Owe')
                                    ->orWhere('transactions.status', 'Verified Paid');
                                });

        $dynamic_sold_qty = $dynamic_sold_qty->select(
                                DB::raw('(MAX(transactions.analog_clock) - MIN(transactions.analog_clock)) AS sold_qty')
                            )->first()->sold_qty;

        $dynamic_difference_stock_sold = $dynamic_stock_in - $dynamic_sold_qty;

        $dynamic_sales_pieces = $query2->sum(DB::raw('ROUND(x.total/ ((SELECT analog_clock FROM ftransactions WHERE person_id=x.person_id ORDER BY collection_datetime DESC LIMIT 1) - (SELECT analog_clock FROM ftransactions WHERE person_id=x.person_id ORDER BY collection_datetime ASC LIMIT 1)), 2)'));
        $dynamic_avg_pieces_day = $query3->sum(DB::raw('ROUND(x.sales/ ABS(DATEDIFF(
                                                (SELECT collection_datetime FROM ftransactions WHERE person_id=x.person_id ORDER BY collection_datetime DESC LIMIT 1),
                                                (SELECT collection_datetime FROM ftransactions WHERE person_id=x.person_id ORDER BY collection_datetime ASC LIMIT 1)
                                                )), 1)'));

        $data = [
            'dynamic_vend_amount' => $dynamic_vend_amount,
            'dynamic_sales_pieces' => $dynamic_sales_pieces,
            'dynamic_avg_pieces_day' => $dynamic_avg_pieces_day,
            'dynamic_stock_in' => $dynamic_stock_in,
            'dynamic_sold_qty' => $dynamic_sold_qty,
            'dynamic_difference_stock_sold' => $dynamic_difference_stock_sold
        ];

        return $data;
    }

    // export excel index for franchisee(Array $data)
    private function exportFtransactionIndexExcel($data)
    {
        $title = 'FVendCash';
        Excel::create($title.'_'.Carbon::now()->format('dmYHis'), function($excel) use ($data) {
            $excel->sheet('sheet1', function($sheet) use ($data) {
                $sheet->setColumnFormat(array('A:P' => '@'));
                $sheet->setColumnFormat(array('I:J' => '0.00'));
                $sheet->getPageSetup()->setPaperSize('A4');
                $sheet->loadView('franchisee.index_excel', compact('data'));
            });
        })->download('xlsx');
    }

    // filter collection from and to($ftransactions)
    private function filterCollectionFromTo($ftransactions)
    {
        if(request('collection_from') === request('collection_to')){
            if(request('collection_from') != '' and request('collection_to') != ''){
                $ftransactions = $ftransactions->whereDate('ftransactions.collection_datetime', '=', request('collection_to'));
            }
        }else{
            if(request('collection_from')){
                $ftransactions = $ftransactions->whereDate('ftransactions.collection_datetime', '>=', request('collection_from'));
            }
            if(request('collection_to')){
                $ftransactions = $ftransactions->whereDate('ftransactions.collection_datetime', '<=', request('collection_to'));
            }
        }

        return $ftransactions;
    }

    // filter collection from and to($transactions)
    private function filterTransactionFromTo($transactions)
    {
        if(request('collection_from') === request('collection_to')){
            if(request('collection_from') != '' and request('collection_to') != ''){
                $transactions = $transactions->whereDate('transactions.delivery_date', '=', request('collection_to'));
            }
        }else{
            if(request('collection_from')){
                $transactions = $transactions->whereDate('transactions.delivery_date', '>=', request('collection_from'));
            }
            if(request('collection_to')){
                $transactions = $transactions->whereDate('transactions.delivery_date', '<=', request('collection_to'));
            }
        }

        return $transactions;
    }

    // retrieve day diff(request input_from, request input_to, request person_id=null)
    private function getCarbonDateDiff($input_from, $input_to, $person_id=null)
    {
        $date_diff = 1;
        if($input_from == $input_to && $input_from != '') {
            $date_diff = 1;
        }else {
            if($input_from) {
                $date_from = Carbon::parse($input_from);
            }else {
                $date_from = DB::table('ftransactions');
                if($person_id) {
                    $date_from = $date_from->where('person_id', $person_id);
                }
                $date_from = $date_from->min('collection_datetime');
                $date_from = Carbon::parse($date_from);
            }

            if($input_to) {
                $date_to = Carbon::parse($input_to);
            }else {
                $date_to = DB::table('ftransactions');
                if($person_id) {
                    $date_to = $date_to->where('person_id', $person_id);
                }
                $date_to = $date_to->max('collection_datetime');
                $date_to = Carbon::parse($date_to);
            }

            $date_diff = $date_from->diffInDays($date_to) + 1;
        }

        return $date_diff;
    }
}
