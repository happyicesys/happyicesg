<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Vending;
use App\Person;
use App\Month;
use App\Item;
use App\Price;
use App\Transaction;
use App\Deal;
use Carbon\Carbon;
use Laracasts\Flash\Flash;
use DB;

// traits
use App\HasMonthOptions;

class VendingController extends Controller
{
    use HasMonthOptions;

    //auth-only login can see
    public function __construct()
    {
        $this->middleware('auth');
    }

    // return vending machine page()
    public function getVendingIndex()
    {
        $month_options = $this->getMonthOptions();

        return view('detailrpt.vending.index', compact('month_options'));
    }

    // return vending generate invoice api()
    public function getVendingGenerateInvoiceApi()
    {
        // initiate the page num when null given
        $pageNum = request('pageNum') ? request('pageNum') : 100;

        $transactions = $this->getGenerateVendingInvoicePerson();
        // dd($transactions->get());

        $totals = $this->calVendingGenerateInvoiceIndex($transactions);

        if($pageNum == 'All'){
            $transactions = $transactions->get();
        }else{
            $transactions = $transactions->paginate($pageNum);
        }

        $data = [
            'totals' => $totals,
            'transactions' => $transactions,
        ];

        return $data;
    }

    // generate batch vending invoices by creating transactions
    public function batchGenerateVendingInvoice()
    {
        // indicate the month and year
        $this_month = Carbon::createFromFormat('m-Y', request('current_month'));
        $people = $this->getGenerateVendingInvoicePerson();
        $totals = $this->calVendingGenerateInvoiceIndex($people);
        $checkboxes = request('checkbox');

        $transactionsid = [];

        if(!$checkboxes) {
            Flash::error('Please choose at least one of the entries');
            return redirect()->action('VendingController@getVendingIndex');
        }

        foreach($people->get() as $person)
        {
            if(array_key_exists($person->person_id, $checkboxes)) {
                $transaction = new Transaction();
                $transaction->person_id = $person->person_id;
                $transaction->person_code = $person->cust_id;
                $transaction->name = $person->name;
                $transaction->status = 'Confirmed';
                $transaction->pay_status = 'Owe';
                $transaction->delivery_date = $this_month->endOfMonth()->toDateString();
                $transaction->order_date = Carbon::today();
                $transaction->del_address = $person->del_address;
                $transaction->updated_by = auth()->user()->name;
                $transaction->contact = $person->contact;
                $transaction->del_postcode = $person->del_postcode;
                $transaction->bill_address = $person->bill_address;
                $transaction->total = -$person->subtotal_payout;
                $daysdiff = Carbon::parse($person->begin_date)->diffInDays(Carbon::parse($person->end_date));
                $remarkStr = '';
                if($person->is_vending) {
                    $remarkStr = "Vending Machine Commission Report:\n Begin Date: ".Carbon::parse($person->begin_date)->toDateString().", Begin Analog Clock: ".$person->begin_analog."\n End Date: ".Carbon::parse($person->end_date)->toDateString().", End Analog Clock: ".$person->end_analog."\n Delta: ".$person->clocker_delta."\n Adjustment Rate: ".$person->clocker_adjustment."%\n Sales # Ice Cream: ".$person->sales;
                }else if($person->is_dvm) {
                    $remarkStr = "Vending Machine Commission Report:\n Begin Date: ".Carbon::parse($person->begin_date)->toDateString()."\n End Date: ".Carbon::parse($person->end_date)->toDateString()."\n Days Diff: ".$daysdiff." \n Total Revenue: $".number_format($person->subtotal_sales, 2)."\n Commission Rate: ".$person->profit_sharing.' %';
                }
                $transaction->transremark = $remarkStr;
                $transaction->is_required_analog = 0;
                $transaction->save();

                array_push($transactionsid, $transaction->id);

                $this->createVendingDeals($transaction->id, $person);
            }
        }

        Flash::success(count($transactionsid).' Invoices successfully created :'.implode(", ", $transactionsid));

        return redirect()->action('VendingController@getVendingIndex');
    }

    // retrieve binded vendings api by person id(int $person_id)
    public function getPersonVendingApi($person_id)
    {
    	$vendings = Person::findOrFail($person_id)->vendings;

    	return $vendings;
    }

    // retrieve (unbinded/ available) vendings api by person id(int $person_id)
    public function getPersonAvailableVendingApi($person_id)
    {
        $vendings = Vending::whereDoesntHave('people', function($query) use ($person_id) {
                        $query->where('id', $person_id);
                    })->get();

    	return $vendings;
    }

    // add vending machine to the person()
    public function addVendingPerson($person_id)
    {
        $person = Person::findOrFail($person_id);

        $vending = Vending::findOrFail(request('vending_id'));

        $person->vendings()->attach($vending);
    }

    // remove vending by given person id(int $vending_id, int $person_id)
    public function removeVendingPerson($vending_id, $person_id)
    {
        $vending = Vending::findOrFail($vending_id);

        $person = Person::findOrFail($person_id);

        $person->vendings()->detach($vending);
    }

    // conditional filter parser(Collection $query, Formrequest $request)
    private function searchTransactionDBFilter($transactions)
    {
    	$profile_id = request('profile_id');
    	$current_month = request('current_month') ? Carbon::createFromFormat('m-Y', request('current_month')) : null;
    	$cust_id = request('cust_id');
    	$id_prefix = request('id_prefix');
    	$company = request('company');
    	$custcategory = request('custcategory');
    	$status = request('status');
        $is_profit_sharing_report = request('is_profit_sharing_report');
        $is_rental = request('is_rental');
        $is_active = request('is_active');

        if($profile_id) {
            $transactions = $transactions->where('profiles.id', $profile_id);
        }
        if($current_month) {
        	$transactions = $transactions
        					->whereDate('transactions.delivery_date', '>=', $current_month->startOfMonth()->toDateString())
        					->whereDate('transactions.delivery_date', '<=', $current_month->endOfMonth()->toDateString());
        }
        if($cust_id){
            $transactions = $transactions->where('people.cust_id', 'LIKE', '%'.$cust_id.'%');
        }
        if($id_prefix) {
            $transactions = $transactions->where('people.cust_id', 'LIKE', $id_prefix.'%');
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
        if($custcategory) {
            if(count($custcategory) == 1) {
                $custcategory = [$custcategory];
            }
            $transactions = $transactions->whereIn('custcategories.id', $custcategory);
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

        if($is_profit_sharing_report != 'All') {
            switch($is_profit_sharing_report) {
                case 1:
                    $transactions = $transactions->where('is_profit_sharing_report', 1);
                    break;
                case 0:
                    $transactions = $transactions->where('is_profit_sharing_report', 0);
                    break;
            }
        }

        if($is_rental) {
            switch($is_rental) {
                case 'Yes':
                    $transactions = $transactions->where('people.vending_monthly_rental', '>', 0);
                    break;
                case 'No':
                    $transactions = $transactions->where('people.vending_monthly_rental', '=', 0);
                    break;
            }
        }

        if($is_active) {
            $transactions = $transactions->where('people.active', $is_active);
        }

        return $transactions;
    }

    // generate vending invoices api by person()
    private function getGenerateVendingInvoicePerson()
    {
        // indicate the month and year
        if(request('begin_date') or request('end_date')) {
                $begin_date = request('begin_date');
                $end_date = request('end_date');
            $this_month_start = $begin_date;
            $this_month_end = $end_date;
            $last_month_start = Carbon::parse($begin_date)->subMonth()->toDateString();
            $last_month_end = Carbon::parse($end_date)->subMonth()->toDateString();
        }else {
            $current_month = Carbon::createFromFormat('m-Y', request('current_month'));
            $last_month = Carbon::createFromFormat('m-Y', request('current_month'))->subMonth();
            $this_month_start = $current_month->startOfMonth()->toDateString();
            $this_month_end = $current_month->endOfMonth()->toDateString();
            $last_month_start = $last_month->startOfMonth()->toDateString();
            $last_month_end = $last_month->endOfMonth()->toDateString();
        }

        if(request()->isMethod('get')) {
            $status = 'Delivered';
        }else {
            $status = request('status');
        }

        if($status) {
            if($status == 'Delivered') {
                $statusStr = " (transactions.status='Delivered' or transactions.status='Verified Owe' or transactions.status='Verified Paid')";
            }else {
                $statusStr = " transactions.status='".$status."'";
            }
        }else {
            $statusStr = ' 1=1';
        }

        $analog_start = DB::raw("(SELECT MAX(transactions.delivery_date) AS delivery_date, MAX(transactions.analog_clock) AS analog_clock, people.id AS person_id
                                FROM transactions
                                LEFT JOIN people ON transactions.person_id=people.id
                                LEFT JOIN profiles ON people.profile_id=profiles.id
                                WHERE ".$statusStr."
                                AND transactions.is_required_analog=1
                                AND DATE(transactions.delivery_date)<'".$this_month_start."'
                                GROUP BY people.id
                                ORDER BY transactions.delivery_date DESC
                                ) analog_start");

        $analog_first = DB::raw("(SELECT MIN(transactions.delivery_date) AS delivery_date, MIN(transactions.analog_clock) AS analog_clock, people.id AS person_id
                                FROM transactions
                                LEFT JOIN people ON transactions.person_id=people.id
                                LEFT JOIN profiles ON people.profile_id=profiles.id
                                WHERE ".$statusStr."
                                AND transactions.is_required_analog=1
                                AND DATE(transactions.delivery_date)>='".$this_month_start."'
                                AND DATE(transactions.delivery_date)<='".$this_month_end."'
                                GROUP BY people.id
                                ORDER BY transactions.delivery_date DESC
                                ) analog_first");

        $analog_end = DB::raw("(SELECT MAX(transactions.delivery_date) AS delivery_date, MAX(transactions.analog_clock) AS analog_clock, people.id AS person_id
                                FROM transactions
                                LEFT JOIN people ON transactions.person_id=people.id
                                LEFT JOIN profiles ON people.profile_id=profiles.id
                                WHERE ".$statusStr."
                                AND transactions.is_required_analog=1
                                AND DATE(transactions.delivery_date)<='".$this_month_end."'
                                GROUP BY people.id
                                ORDER BY transactions.delivery_date DESC
                                ) analog_end");

        $analog_lastmonth_start = DB::raw("(SELECT MAX(transactions.delivery_date) AS delivery_date, MAX(transactions.analog_clock) AS analog_clock, people.id AS person_id
                                FROM transactions
                                LEFT JOIN people ON transactions.person_id=people.id
                                LEFT JOIN profiles ON people.profile_id=profiles.id
                                WHERE ".$statusStr."
                                AND transactions.is_required_analog=1
                                AND DATE(transactions.delivery_date)<'".$last_month_start."'
                                GROUP BY people.id
                                ORDER BY transactions.delivery_date DESC
                                ) analog_lastmonth_start");

        $analog_lastmonth_first = DB::raw("(SELECT MIN(transactions.delivery_date) AS delivery_date, MIN(transactions.analog_clock) AS analog_clock, people.id AS person_id
                                FROM transactions
                                LEFT JOIN people ON transactions.person_id=people.id
                                LEFT JOIN profiles ON people.profile_id=profiles.id
                                WHERE ".$statusStr."
                                AND transactions.is_required_analog=1
                                AND DATE(transactions.delivery_date)>='".$last_month_start."'
                                AND DATE(transactions.delivery_date)<='".$last_month_end."'
                                GROUP BY people.id
                                ORDER BY transactions.delivery_date DESC
                                ) analog_lastmonth_first");

        $analog_lastmonth_end = DB::raw("(SELECT MAX(transactions.delivery_date) AS delivery_date, MAX(transactions.analog_clock) AS analog_clock, people.id AS person_id
                                FROM transactions
                                LEFT JOIN people ON transactions.person_id=people.id
                                LEFT JOIN profiles ON people.profile_id=profiles.id
                                WHERE ".$statusStr."
                                AND transactions.is_required_analog=1
                                AND DATE(transactions.delivery_date)<='".$last_month_end."'
                                GROUP BY people.id
                                ORDER BY transactions.delivery_date DESC
                                ) analog_lastmonth_end");

        $melted = DB::raw("(SELECT SUM(ABS(deals.amount)) AS melted_amount, people.id AS person_id
                                FROM deals
                                LEFT JOIN items ON items.id=deals.item_id
                                LEFT JOIN transactions ON transactions.id=deals.transaction_id
                                LEFT JOIN people ON transactions.person_id=people.id
                                LEFT JOIN profiles ON people.profile_id=profiles.id
                                WHERE ".$statusStr."
                                AND items.product_id='051b'
                                AND DATE(transactions.delivery_date)>= (SELECT x.delivery_date FROM transactions x WHERE DATE(x.delivery_date)<'".$this_month_start."' ORDER BY x.delivery_date DESC LIMIT 1)
                                AND DATE(transactions.delivery_date)<='".$this_month_end."'
                                GROUP BY people.id
                                ORDER BY transactions.delivery_date DESC
                                ) melted");

        $vend_received = DB::raw("(SELECT SUM(deals.amount) AS vend_received, MAX(transactions.delivery_date) AS max_delivery_date, MIN(transactions.delivery_date) AS min_delivery_date, people.id AS person_id
                                FROM deals
                                LEFT JOIN items ON items.id=deals.item_id
                                LEFT JOIN transactions ON transactions.id=deals.transaction_id
                                LEFT JOIN people ON transactions.person_id=people.id
                                LEFT JOIN profiles ON people.profile_id=profiles.id
                                WHERE ".$statusStr."
                                AND items.product_id='051'
                                AND DATE(transactions.delivery_date)>='".$this_month_start."'
                                AND DATE(transactions.delivery_date)<='".$this_month_end."'
                                AND deals.amount > 0
                                GROUP BY people.id
                                ORDER BY transactions.delivery_date DESC
                                ) vend_received");

        $sales_count = DB::raw("(SELECT SUM(transactions.sales_count) AS sales_count, people.id AS person_id
                                FROM transactions
                                LEFT JOIN people ON transactions.person_id=people.id
                                LEFT JOIN profiles ON people.profile_id=profiles.id
                                WHERE ".$statusStr."
                                AND DATE(transactions.delivery_date)>='".$this_month_start."'
                                AND DATE(transactions.delivery_date)<='".$this_month_end."'
                                GROUP BY people.id
                                ORDER BY transactions.delivery_date DESC
                                ) sales_count");

        $transactions = DB::table('deals')
                        ->leftJoin('items', 'items.id', '=', 'deals.item_id')
                        ->leftJoin('transactions', 'transactions.id', '=', 'deals.transaction_id')
                        ->leftJoin('people', 'transactions.person_id', '=', 'people.id')
                        ->leftJoin('profiles', 'people.profile_id', '=', 'profiles.id')
                        ->leftJoin('custcategories', 'custcategories.id', '=', 'people.custcategory_id')
                        ->leftJoin($analog_start, 'people.id', '=', 'analog_start.person_id')
                        ->leftJoin($analog_first, 'people.id', '=', 'analog_first.person_id')
                        ->leftJoin($analog_end, 'people.id', '=', 'analog_end.person_id')
                        ->leftJoin($analog_lastmonth_start, 'people.id', '=', 'analog_lastmonth_start.person_id')
                        ->leftJoin($analog_lastmonth_first, 'people.id', '=', 'analog_lastmonth_first.person_id')
                        ->leftJoin($analog_lastmonth_end, 'people.id', '=', 'analog_lastmonth_end.person_id')
                        ->leftJoin($melted, 'people.id', '=', 'melted.person_id')
                        ->leftJoin($vend_received, 'people.id', '=', 'vend_received.person_id')
                        ->leftJoin($sales_count, 'people.id', '=', 'sales_count.person_id')
                        ->select(
                                    'items.is_commission',
                                    'people.cust_id', 'people.company', 'people.name', 'people.id as person_id', 'people.del_address', 'people.contact', 'people.del_postcode', 'people.bill_address', 'people.is_vending', 'people.is_dvm',
                                    'profiles.name as profile_name', 'profiles.id as profile_id', 'profiles.gst',
                                    'transactions.id', 'transactions.status', 'transactions.delivery_date', 'transactions.delivery_fee', 'transactions.paid_at', 'transactions.created_at',
                                    'custcategories.name as custcategory',
                                    DB::raw('CASE WHEN people.is_vending THEN (CASE WHEN analog_start.delivery_date THEN analog_start.delivery_date ELSE analog_first.delivery_date END) ELSE vend_received.min_delivery_date END AS begin_date'),
                                    DB::raw('(CASE WHEN analog_start.analog_clock THEN analog_start.analog_clock ELSE analog_first.analog_clock END) AS begin_analog'),
                                    DB::raw('CASE WHEN people.is_vending THEN analog_end.delivery_date ELSE vend_received.max_delivery_date END AS end_date'),
                                    'analog_end.analog_clock AS end_analog',
                                    DB::raw('(analog_end.analog_clock - (CASE WHEN analog_start.analog_clock THEN analog_start.analog_clock ELSE analog_first.analog_clock END)) AS clocker_delta'),
                                    DB::raw('(analog_lastmonth_end.analog_clock - (CASE WHEN analog_lastmonth_start.analog_clock THEN analog_lastmonth_start.analog_clock ELSE analog_lastmonth_first.analog_clock END)) AS last_clocker_delta'),
                                    'people.vending_clocker_adjustment AS clocker_adjustment',
                                    DB::raw('CASE WHEN people.is_vending THEN FLOOR((analog_end.analog_clock - (CASE WHEN analog_start.analog_clock THEN analog_start.analog_clock ELSE analog_first.analog_clock END))- (CASE WHEN people.vending_clocker_adjustment THEN ((analog_end.analog_clock - (CASE WHEN analog_start.analog_clock THEN analog_start.analog_clock ELSE analog_first.analog_clock END)) * people.vending_clocker_adjustment/ 100) ELSE 0 END)) ELSE sales_count.sales_count END AS sales'),
                                    DB::raw('CASE WHEN people.is_vending THEN FLOOR((analog_end.analog_clock - (CASE WHEN analog_start.analog_clock THEN analog_start.analog_clock ELSE analog_first.analog_clock END))- (CASE WHEN people.vending_clocker_adjustment THEN ((analog_end.analog_clock - (CASE WHEN analog_start.analog_clock THEN analog_start.analog_clock ELSE analog_first.analog_clock END)) * people.vending_clocker_adjustment/ 100) ELSE 0 END)) * people.vending_piece_price ELSE vend_received.vend_received END AS subtotal_sales'),
                                    'people.vending_profit_sharing AS profit_sharing',
                                    DB::raw('(CASE WHEN people.is_vending THEN "$" ELSE "%" END) AS profit_sharing_format'),
                                    'people.vending_monthly_rental AS vending_monthly_rental',
                                    DB::raw('CASE WHEN people.is_vending THEN (FLOOR((analog_end.analog_clock - (CASE WHEN analog_start.analog_clock THEN analog_start.analog_clock ELSE analog_first.analog_clock END)) - ((analog_end.analog_clock - (CASE WHEN analog_start.analog_clock THEN analog_start.analog_clock ELSE analog_first.analog_clock END)) * people.vending_clocker_adjustment/ 100)) * people.vending_profit_sharing) ELSE (vend_received.vend_received * people.vending_profit_sharing/100) END AS subtotal_profit_sharing'),
                                    'people.vending_monthly_utilities AS utility_subsidy',
                                    DB::raw('(
                                                CASE
                                                WHEN people.is_vending
                                                THEN ((FLOOR((analog_end.analog_clock - (
                                                    CASE
                                                    WHEN analog_start.analog_clock
                                                    THEN analog_start.analog_clock
                                                    ELSE analog_first.analog_clock
                                                    END)) -
                                                    ((analog_end.analog_clock - (
                                                        CASE
                                                        WHEN analog_start.analog_clock
                                                        THEN analog_start.analog_clock
                                                        ELSE analog_first.analog_clock
                                                        END)) *
                                                        people.vending_clocker_adjustment/ 100)) * people.vending_profit_sharing) + people.vending_monthly_utilities + people.vending_monthly_rental)
                                                ELSE (vend_received.vend_received * people.vending_profit_sharing/100) +  people.vending_monthly_utilities + people.vending_monthly_rental
                                                END) AS subtotal_payout'),
                                    DB::raw('(CASE WHEN people.is_vending THEN FLOOR((analog_end.analog_clock - (CASE WHEN analog_start.analog_clock THEN analog_start.analog_clock ELSE analog_first.analog_clock END))- (CASE WHEN people.vending_clocker_adjustment THEN ((analog_end.analog_clock - (CASE WHEN analog_start.analog_clock THEN analog_start.analog_clock ELSE analog_first.analog_clock END)) * people.vending_clocker_adjustment/ 100) ELSE 0 END)) * people.vending_piece_price ELSE vend_received.vend_received END) -
                                                (CASE WHEN people.is_vending THEN ((FLOOR((analog_end.analog_clock - (CASE WHEN analog_start.analog_clock THEN analog_start.analog_clock ELSE analog_first.analog_clock END)) - ((analog_end.analog_clock - (CASE WHEN analog_start.analog_clock THEN analog_start.analog_clock ELSE analog_first.analog_clock END)) * people.vending_clocker_adjustment/ 100)) * people.vending_profit_sharing) + people.vending_monthly_utilities + people.vending_monthly_rental) ELSE (vend_received.vend_received * people.vending_profit_sharing/100) +  people.vending_monthly_utilities + people.vending_monthly_rental END) AS subtotal_gross_profit'),
                                    DB::raw('((CASE WHEN people.is_vending THEN FLOOR((analog_end.analog_clock - (CASE WHEN analog_start.analog_clock THEN analog_start.analog_clock ELSE analog_first.analog_clock END))- (CASE WHEN people.vending_clocker_adjustment THEN ((analog_end.analog_clock - (CASE WHEN analog_start.analog_clock THEN analog_start.analog_clock ELSE analog_first.analog_clock END)) * people.vending_clocker_adjustment/ 100) ELSE 0 END)) * people.vending_piece_price ELSE vend_received.vend_received END) -
                                                (CASE WHEN people.is_vending THEN ((FLOOR((analog_end.analog_clock - (CASE WHEN analog_start.analog_clock THEN analog_start.analog_clock ELSE analog_first.analog_clock END)) - ((analog_end.analog_clock - (CASE WHEN analog_start.analog_clock THEN analog_start.analog_clock ELSE analog_first.analog_clock END)) * people.vending_clocker_adjustment/ 100)) * people.vending_profit_sharing) + people.vending_monthly_utilities + people.vending_monthly_rental) ELSE (vend_received.vend_received * people.vending_profit_sharing/100) +  people.vending_monthly_utilities + people.vending_monthly_rental END))/ (CASE WHEN people.is_vending THEN FLOOR((analog_end.analog_clock - (CASE WHEN analog_start.analog_clock THEN analog_start.analog_clock ELSE analog_first.analog_clock END))- (CASE WHEN people.vending_clocker_adjustment THEN ((analog_end.analog_clock - (CASE WHEN analog_start.analog_clock THEN analog_start.analog_clock ELSE analog_first.analog_clock END)) * people.vending_clocker_adjustment/ 100) ELSE 0 END)) ELSE sales_count.sales_count END) AS avg_selling_price'),
                                    'melted.melted_amount AS melted_amount',
                                    'vend_received.vend_received AS vend_received', 'vend_received.max_delivery_date AS max_vend_date', 'vend_received.min_delivery_date AS min_vend_date'
                                );

        if(request('profile_id') or request('current_month') or request('cust_id') or request('id_prefix') or request('company') or $request('custcategory') or request('status') or request('is_profit_sharing_report') or request('is_rental') or request('is_active')){
            $transactions = $this->searchTransactionDBFilter($transactions);
        }

        $transactions = $transactions
                        ->where('transactions.is_required_analog', 1)
                        ->where(function($query) {
                            $query->where('people.is_vending', 1)
                                    ->orWhere('people.is_dvm', 1);
                        });

        if(request('sortName')) {
            $transactions = $transactions->orderBy(request('sortName'), request('sortBy') ? 'asc' : 'desc');
        }else {
            $transactions = $transactions->orderBy('people.cust_id');
        }

        $transactions = $transactions->groupBy('people.id');

        return $transactions;
    }

    // calculate total when sql done the filter job
    private function calVendingGenerateInvoiceIndex($query)
    {
        $total_sales = 0;
        $total_sales_figure = 0;
        $total_profit_sharing = 0;
        $total_rental = 0;
        $total_utility = 0;
        $total_payout = 0;
        $total_gross_profit = 0;

        $query1 = clone $query;
        $people = $query1->get();
        foreach($people as $person) {
            $total_sales += $person->sales;
            $total_sales_figure += $person->subtotal_sales;
            $total_profit_sharing += $person->subtotal_profit_sharing;
            $total_rental += $person->vending_monthly_rental;
            $total_utility += $person->utility_subsidy;
            $total_payout += $person->subtotal_payout;
            $total_gross_profit += $person->subtotal_gross_profit;
        }

        $totals = [
        	'total_sales' => $total_sales,
            'total_sales_figure' => $total_sales_figure,
        	'total_profit_sharing' => $total_profit_sharing,
            'total_rental' => $total_rental,
        	'total_utility' => $total_utility,
        	'total_payout' => $total_payout,
            'total_gross_profit' => $total_gross_profit,
        ];

        return $totals;
    }

    // create deals for the vending transaciton(int transaction_id, Collection person)
    private function createVendingDeals($transaction_id, $person)
    {
        $transaction = Transaction::findOrFail($transaction_id);

        // 2 compulsory items 055, U01
        $sales_commission = Item::where('product_id', '055')->firstOrFail();
        $utility_subsidy = Item::where('product_id', 'U01')->firstOrFail();

        if($person->is_vending) {
            $deal_comm = new Deal();
            $deal_comm->item_id = $sales_commission->id;
            $deal_comm->transaction_id = $transaction_id;
            $deal_comm->dividend = $person->sales;
            $deal_comm->divisor = 1;
            $deal_comm->qty_status = 2;
            $deal_comm->qty = 0;
            $deal_comm->unit_price = -$person->profit_sharing;
            $deal_comm->amount = -$person->subtotal_profit_sharing;
            $deal_comm->save();
        }else if($person->is_dvm) {
            $deal_comm = new Deal();
            $deal_comm->item_id = $sales_commission->id;
            $deal_comm->transaction_id = $transaction_id;
            $deal_comm->dividend = $person->subtotal_sales;
            $deal_comm->divisor = 1;
            $deal_comm->qty_status = 2;
            $deal_comm->qty = 0;
            $deal_comm->unit_price = -$person->profit_sharing/100;
            $deal_comm->amount = $person->subtotal_sales * (-$person->profit_sharing/100);
            $deal_comm->save();
        }

        if($person->utility_subsidy != 0.00 and $person->utility_subsidy != null and $person->utility_subsidy != '') {
            $deal_util = new Deal();
            $deal_util->item_id = $utility_subsidy->id;
            $deal_util->transaction_id = $transaction_id;
            $deal_util->dividend = 1;
            $deal_util->divisor = 1;
            $deal_util->qty_status = 2;
            $deal_util->qty_status = 0;
            $deal_util->unit_price = -$person->utility_subsidy;
            $deal_util->amount = -$person->utility_subsidy;
            $deal_util->save();
        }
    }
}
